<?php

namespace App\Listeners;

use App\Events\TaskStateChanged;
use App\Enums\TaskState;
use App\Models\User;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Notification;

class NotifyStakeholders
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TaskStateChanged $event
     * @return void
     */
    public function handle(TaskStateChanged $event): void
    {
        // Send notifications based on state transition
        match($event->newState) {
            TaskState::SUBMITTED_TO_SURVEILLANCE =>
                $this->notifySurveillanceSupervisors($event->task),

            TaskState::ASSIGNED_TO_MPFM =>
                $this->notifyMPFMSupervisors($event->task),

            TaskState::IN_PROGRESS =>
                $this->notifyTaskOperators($event->task),

            TaskState::MPFM_REVIEWED =>
                $this->notifySurveillanceSupervisor($event->task),

            TaskState::SURVEILLANCE_REVIEWED =>
                $this->notifyJobOfficer($event->task),

            TaskState::JOB_OFFICER_APPROVED =>
                $this->notifyAllStakeholders($event->task, 'Task approved by Job Officer'),

            TaskState::COMPLETED =>
                $this->notifyAllStakeholders($event->task, 'Task completed successfully'),

            default => null
        };
    }

    /**
     * Notify surveillance supervisors when task is submitted.
     *
     * @param \App\Models\PlanTask $task
     * @return void
     */
    protected function notifySurveillanceSupervisors($task): void
    {
        // TODO: Implement notification
        // Example:
        // $supervisors = User::role('surveillance_supervisor')->get();
        // Notification::send($supervisors, new TaskSubmittedNotification($task));

        Log::info("Notification: Task submitted to surveillance", [
            'task_id' => $task->id,
            'plan_id' => $task->monthly_plan_id,
        ]);
    }

    /**
     * Notify MPFM supervisors when task is assigned to department.
     *
     * @param \App\Models\PlanTask $task
     * @return void
     */
    protected function notifyMPFMSupervisors($task): void
    {
        // TODO: Implement notification
        Log::info("Notification: Task assigned to MPFM department", [
            'task_id' => $task->id,
        ]);
    }

    /**
     * Notify task operators when they can start work.
     *
     * @param \App\Models\PlanTask $task
     * @return void
     */
    protected function notifyTaskOperators($task): void
    {
        // TODO: Implement notification to assigned operators
        // $task->operators->each(function($operator) use ($task) {
        //     $operator->notify(new TaskStartedNotification($task));
        // });

        Log::info("Notification: Task started by operator", [
            'task_id' => $task->id,
            'operators' => $task->operators->pluck('id')->toArray(),
        ]);
    }

    /**
     * Notify surveillance supervisor for review.
     *
     * @param \App\Models\PlanTask $task
     * @return void
     */
    protected function notifySurveillanceSupervisor($task): void
    {
        // TODO: Implement notification
        if ($task->surveillanceSupervisor) {
            // $task->surveillanceSupervisor->notify(new TaskReadyForReviewNotification($task));

            Log::info("Notification: Task ready for surveillance review", [
                'task_id' => $task->id,
                'supervisor_id' => $task->surveillance_supervisor_id,
            ]);
        }
    }

    /**
     * Notify job officer for final approval.
     *
     * @param \App\Models\PlanTask $task
     * @return void
     */
    protected function notifyJobOfficer($task): void
    {
        // TODO: Implement notification
        if ($task->monthlyPlan && $task->monthlyPlan->creator) {
            // $task->monthlyPlan->creator->notify(new TaskReadyForApprovalNotification($task));

            Log::info("Notification: Task ready for job officer approval", [
                'task_id' => $task->id,
                'job_officer_id' => $task->monthlyPlan->created_by,
            ]);
        }
    }

    /**
     * Notify all stakeholders involved in the task.
     *
     * @param \App\Models\PlanTask $task
     * @param string $message
     * @return void
     */
    protected function notifyAllStakeholders($task, string $message): void
    {
        // TODO: Implement notification to all stakeholders
        // Notify: Job Officer, Surveillance Supervisor, MPFM Supervisor, Operators

        Log::info("Notification: " . $message, [
            'task_id' => $task->id,
            'plan_id' => $task->monthly_plan_id,
        ]);
    }
}
