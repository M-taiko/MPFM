<?php

namespace App\Listeners;

use App\Events\SLATriggered;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotifySLAOverdueListener
{
    /**
     * Handle the SLATriggered event
     *
     * This listener sends notifications to responsible actors when a task becomes overdue
     *
     * @param SLATriggered $event
     * @return void
     */
    public function handle(SLATriggered $event): void
    {
        try {
            $task = $event->task;
            $slaHistory = $event->slaHistory;

            Log::info("Processing SLA notification for task {$task->id}", [
                'reason' => $event->reason,
                'escalation_level' => $slaHistory->escalation_level,
            ]);

            // Get the responsible actor based on task state
            $actor = $this->getResponsibleActor($task);

            if ($actor === null) {
                Log::warning("No responsible actor found for task {$task->id}");
                return;
            }

            // Determine notification type based on escalation level
            $notificationClass = $this->getNotificationClass($slaHistory->escalation_level);

            // Send notification
            if (config('sla.notifications.enabled')) {
                $this->sendNotification($actor, $task, $slaHistory, $notificationClass);
            }

            // Log the notification
            Log::info("SLA notification processed for task {$task->id}", [
                'notified_to' => $actor->email,
                'notification_type' => $notificationClass,
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to process SLA notification: {$e->getMessage()}", [
                'task_id' => $event->task->id,
                'exception' => $e,
            ]);
        }
    }

    /**
     * Get the responsible actor for the task
     *
     * @param \App\Models\PlanTask $task
     * @return User|null
     */
    protected function getResponsibleActor($task): ?User
    {
        return match($task->state) {
            // Draft: Job Officer who created the plan
            \App\Enums\TaskState::DRAFT => $task->monthlyPlan?->createdBy,

            // Submitted: Surveillance supervisor
            \App\Enums\TaskState::SUBMITTED_TO_SURVEILLANCE => User::role('surveillance_supervisor')->first(),

            // Assigned: MPFM supervisor
            \App\Enums\TaskState::ASSIGNED_TO_MPFM => User::role('mpfm_supervisor')->first(),

            // In Progress: The assigned operator(s)
            \App\Enums\TaskState::IN_PROGRESS => $task->primaryOperator()->first() ?? $task->operators()->first(),

            // MPFM Reviewed: Surveillance supervisor
            \App\Enums\TaskState::MPFM_REVIEWED => User::role('surveillance_supervisor')->first(),

            // Surveillance Reviewed: Job Officer
            \App\Enums\TaskState::SURVEILLANCE_REVIEWED => $task->monthlyPlan?->createdBy,

            // Job Officer Approved: Job Officer
            \App\Enums\TaskState::JOB_OFFICER_APPROVED => $task->monthlyPlan?->createdBy,

            default => null,
        };
    }

    /**
     * Get the notification class based on escalation level
     *
     * @param int $escalationLevel
     * @return string
     */
    protected function getNotificationClass(int $escalationLevel): string
    {
        return match($escalationLevel) {
            0 => \App\Notifications\TaskOverdueNotification::class,
            1 => \App\Notifications\TaskOverdueFirstEscalationNotification::class,
            2 => \App\Notifications\TaskOverdueSupervisorNotification::class,
            3 => \App\Notifications\TaskOverdueDirectorNotification::class,
            default => \App\Notifications\TaskOverdueNotification::class,
        };
    }

    /**
     * Send the notification to the actor
     *
     * @param User $actor
     * @param \App\Models\PlanTask $task
     * @param \App\Models\TaskSLAHistory $slaHistory
     * @param string $notificationClass
     * @return void
     */
    protected function sendNotification(User $actor, $task, $slaHistory, string $notificationClass): void
    {
        // Get configured channels
        $channels = config('sla.notifications.channels', ['database', 'mail']);

        // Create notification instance
        if (class_exists($notificationClass)) {
            $notification = new $notificationClass($task, $slaHistory);
            Notification::send($actor, $notification);
        } else {
            // Fallback: Use simple database notification
            $actor->notify(new \App\Notifications\TaskOverdueNotification($task, $slaHistory));
        }

        // Record in SLA history
        $slaHistory->recordNotification($actor->roles->first()?->name ?? 'user');
    }
}
