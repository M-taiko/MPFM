<?php

namespace App\Listeners;

use App\Events\TaskStateChanged;
use App\Enums\TaskState;
use App\Models\User;
use App\Notifications\TaskStatusNotification;
use Illuminate\Support\Facades\Notification;

class NotifyStakeholders
{
    public function handle(TaskStateChanged $event): void
    {
        $task     = $event->task;
        $newState = $event->newState;
        $deptName = $task->department->name ?? 'a department';
        $title    = $task->title;

        if ($newState === TaskState::SUBMITTED_TO_SURVEILLANCE) {
            $this->notifyRole('surveillance_supervisor', $task,
                'New Task Awaiting Review',
                "Task \"{$title}\" from {$deptName} needs your review.",
                'warning');

        } elseif ($newState === TaskState::ASSIGNED_TO_DEPARTMENT
               || $newState === TaskState::ASSIGNED_TO_MPFM) {
            $this->notifyDepartment($task,
                'New Task Assigned to Your Department',
                "Task \"{$title}\" has been approved and assigned to your department.",
                'info');

        } elseif ($newState === TaskState::IN_PROGRESS) {
            $this->notifySurveillanceSupervisorOfTask($task,
                'Task In Progress',
                "Task \"{$title}\" is now in progress.",
                'info');

        } elseif ($newState === TaskState::DEPARTMENT_REVIEWED
               || $newState === TaskState::MPFM_REVIEWED) {
            $this->notifyRole('surveillance_supervisor', $task,
                'Task Ready for Surveillance Review',
                "Task \"{$title}\" has been completed by the department and needs your review.",
                'success');

        } elseif ($newState === TaskState::SURVEILLANCE_REVIEWED) {
            $this->notifyTaskCreator($task,
                'Task Ready for Your Approval',
                "Task \"{$title}\" has been reviewed by surveillance and is ready for your final approval.",
                'success');

        } elseif ($newState === TaskState::JOB_OFFICER_APPROVED) {
            $this->notifyAllStakeholders($task,
                'Task Approved',
                "Task \"{$title}\" has been approved by the Job Officer.",
                'success');

        } elseif ($newState === TaskState::COMPLETED) {
            $this->notifyAllStakeholders($task,
                'Task Completed',
                "Task \"{$title}\" has been completed successfully.",
                'success');

        } elseif ($newState === TaskState::DRAFT) {
            $remarks = $task->supervisor_remarks ?? '—';
            $this->notifyTaskCreator($task,
                'Task Sent Back for Revision',
                "Task \"{$title}\" was rejected and needs revision. Remarks: {$remarks}",
                'danger');
        }
    }

    // ── Helpers ──────────────────────────────────────────────

    private function notifyRole(string $role, $task, string $title, string $message, string $type): void
    {
        $users = User::role($role)->get();
        if ($users->isEmpty()) return;

        Notification::send($users, new TaskStatusNotification($task, $title, $message, $type));
    }

    private function notifyDepartment($task, string $title, string $message, string $type): void
    {
        if (!$task->department_id) return;

        $users = User::where('department_id', $task->department_id)
            ->whereHas('roles', fn($q) => $q->whereIn('name', [
                'department_manager', 'operator', 'mpfm_operator',
            ]))
            ->get();

        if ($users->isEmpty()) return;

        Notification::send($users, new TaskStatusNotification($task, $title, $message, $type));
    }

    private function notifySurveillanceSupervisorOfTask($task, string $title, string $message, string $type): void
    {
        if ($task->surveillance_supervisor_id) {
            $supervisor = User::find($task->surveillance_supervisor_id);
            if ($supervisor) {
                $supervisor->notify(new TaskStatusNotification($task, $title, $message, $type));
                return;
            }
        }

        $this->notifyRole('surveillance_supervisor', $task, $title, $message, $type);
    }

    private function notifyTaskCreator($task, string $title, string $message, string $type): void
    {
        $creator = User::find($task->created_by_user_id);
        if ($creator) {
            $creator->notify(new TaskStatusNotification($task, $title, $message, $type));
        }
    }

    private function notifyAllStakeholders($task, string $title, string $message, string $type): void
    {
        $userIds = collect();

        if ($task->created_by_user_id)      $userIds->push($task->created_by_user_id);
        if ($task->surveillance_supervisor_id) $userIds->push($task->surveillance_supervisor_id);

        if ($task->department_id) {
            User::where('department_id', $task->department_id)
                ->whereHas('roles', fn($q) => $q->whereIn('name', [
                    'department_manager', 'operator', 'mpfm_operator',
                ]))
                ->pluck('id')
                ->each(fn($id) => $userIds->push($id));
        }

        $users = User::whereIn('id', $userIds->unique()->values())->get();
        if ($users->isEmpty()) return;

        Notification::send($users, new TaskStatusNotification($task, $title, $message, $type));
    }
}
