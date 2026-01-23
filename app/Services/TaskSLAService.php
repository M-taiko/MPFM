<?php

namespace App\Services;

use App\Models\PlanTask;
use App\Models\TaskSLAHistory;
use App\Models\User;
use App\Enums\TaskState;
use App\Events\SLATriggered;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TaskSLAService
{
    /**
     * Check if a task is currently overdue
     *
     * @param PlanTask $task
     * @return bool
     */
    public function isTaskOverdue(PlanTask $task): bool
    {
        if ($task->expected_completion_at === null) {
            return false;
        }

        if ($this->isTaskInFinalState($task)) {
            return false;
        }

        return now()->isAfter($task->expected_completion_at);
    }

    /**
     * Get hours until SLA expiry (negative if already overdue)
     *
     * @param PlanTask $task
     * @return float|null
     */
    public function getHoursUntilSLAExpiry(PlanTask $task): ?float
    {
        if ($task->expected_completion_at === null) {
            return null;
        }

        return now()->diffInMinutes($task->expected_completion_at) / 60;
    }

    /**
     * Check if task is in a final state (completed/approved)
     *
     * @param PlanTask $task
     * @return bool
     */
    public function isTaskInFinalState(PlanTask $task): bool
    {
        return $task->state?->in([
            TaskState::COMPLETED,
            TaskState::JOB_OFFICER_APPROVED,
        ]) ?? false;
    }

    /**
     * Set expected completion time for a task
     *
     * @param PlanTask $task
     * @param Carbon|null $datetime
     * @return void
     */
    public function setExpectedCompletion(PlanTask $task, ?Carbon $datetime): void
    {
        $task->update([
            'expected_completion_at' => $datetime,
            'overdue_flag' => false,
            'sla_notified_at' => null,
        ]);

        Log::info("SLA set for task {$task->id}", [
            'expected_at' => $datetime,
            'state' => $task->state?->value,
        ]);
    }

    /**
     * Calculate SLA deadline based on task state and config
     *
     * @param PlanTask $task
     * @param TaskState|null $forState
     * @return Carbon
     */
    public function calculateSLAFromState(PlanTask $task, ?TaskState $forState = null): Carbon
    {
        $state = $forState ?? $task->state ?? TaskState::DRAFT;
        $slaHours = $this->getSLADurationForState($state, $task);

        return now()->addHours($slaHours);
    }

    /**
     * Get SLA duration for a specific state (in hours)
     * Considers state, priority, and role overrides
     *
     * @param TaskState $state
     * @param PlanTask|null $task
     * @return int
     */
    public function getSLADurationForState(TaskState $state, ?PlanTask $task = null): int
    {
        $config = config('sla.state_durations');
        $baseDuration = $config[$state->value] ?? 24;

        // Apply priority multiplier if task provided
        if ($task && $task->priority) {
            $multipliers = config('sla.priority_multipliers', []);
            $multiplier = $multipliers[$task->priority] ?? 1.0;
            $baseDuration = (int) ($baseDuration * $multiplier);
        }

        return $baseDuration;
    }

    /**
     * Get all overdue tasks that need processing
     * Safe for concurrent execution using database locks
     *
     * @return Collection
     */
    public function checkAndProcessOverdueTasks(): Collection
    {
        return DB::transaction(function () {
            $overdueTasks = PlanTask::query()
                ->where('overdue_flag', false)
                ->whereNotNull('expected_completion_at')
                ->where('expected_completion_at', '<', now())
                ->whereNotIn('state', [
                    TaskState::COMPLETED->value,
                    TaskState::JOB_OFFICER_APPROVED->value,
                ])
                ->lockForUpdate() // Prevent concurrent processing
                ->get();

            foreach ($overdueTasks as $task) {
                $this->markTaskAsOverdue($task);
            }

            return $overdueTasks;
        });
    }

    /**
     * Mark a task as overdue and record in history
     *
     * @param PlanTask $task
     * @return TaskSLAHistory
     */
    public function markTaskAsOverdue(PlanTask $task): TaskSLAHistory
    {
        $slaHistory = $task->slaHistory ?? $this->recordSLAHistory(
            $task,
            now(),
            null,
            'Task detected as overdue by scheduled task'
        );

        $task->update([
            'overdue_flag' => true,
        ]);

        Log::warning("Task {$task->id} marked as overdue", [
            'expected_at' => $task->expected_completion_at,
            'state' => $task->state?->value,
        ]);

        return $slaHistory;
    }

    /**
     * Get the responsible actor for a task based on its current state
     *
     * @param PlanTask $task
     * @return User|null
     */
    public function getResponsibleActorForState(PlanTask $task): ?User
    {
        return match($task->state) {
            TaskState::DRAFT => $task->monthlyPlan->createdBy,
            TaskState::SUBMITTED_TO_SURVEILLANCE => $this->getSurveillanceSupervisor(),
            TaskState::ASSIGNED_TO_MPFM => $this->getMPFMSupervisor($task),
            TaskState::IN_PROGRESS => $task->primaryOperator()->first(),
            TaskState::MPFM_REVIEWED => $this->getSurveillanceSupervisor(),
            TaskState::SURVEILLANCE_REVIEWED => $task->monthlyPlan->createdBy,
            TaskState::JOB_OFFICER_APPROVED => $task->monthlyPlan->createdBy,
            default => null,
        };
    }

    /**
     * Send SLA notification to responsible actor
     *
     * @param PlanTask $task
     * @param string $reason
     * @return bool
     */
    public function sendSLANotification(PlanTask $task, string $reason = 'Task overdue'): bool
    {
        // Check if notifications are enabled
        if (!config('sla.notifications.enabled')) {
            return false;
        }

        // Check if we should send (prevent notification flooding)
        if ($this->shouldThrottleNotification($task)) {
            Log::debug("SLA notification throttled for task {$task->id}");
            return false;
        }

        $actor = $this->getResponsibleActorForState($task);
        if ($actor === null) {
            Log::warning("No responsible actor found for task {$task->id}");
            return false;
        }

        // Get SLA history or create one
        $slaHistory = $task->slaHistory ?? $this->recordSLAHistory(
            $task,
            now(),
            $actor->roles->first()?->name,
            $reason
        );

        try {
            // Send notification
            $notifiable = new \Illuminate\Notifications\Notifiable();
            $notifiable->route('mail', $actor->email);
            $notifiable->route('database', $actor->id);

            // Here you would use your actual notification class
            // For now, we'll just record it
            $slaHistory->recordNotification($actor->roles->first()?->name ?? 'user');

            Log::info("SLA notification sent for task {$task->id}", [
                'to_user' => $actor->id,
                'reason' => $reason,
            ]);

            // Mark task as notified
            $task->update([
                'sla_notified_at' => now(),
            ]);

            // Fire event for listeners to handle
            event(new SLATriggered($task, $slaHistory, auth()->user() ?? $actor, $reason));

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send SLA notification for task {$task->id}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Check if we should throttle SLA notification (prevent spamming)
     *
     * @param PlanTask $task
     * @return bool
     */
    protected function shouldThrottleNotification(PlanTask $task): bool
    {
        if ($task->sla_notified_at === null) {
            return false; // Never notified, should send
        }

        $minutesSinceLastNotification = $task->sla_notified_at->diffInMinutes(now());
        $throttleMinutes = config('sla.notifications.on_overdue_minutes', 15);

        return $minutesSinceLastNotification < $throttleMinutes;
    }

    /**
     * Record SLA history entry
     *
     * @param PlanTask $task
     * @param Carbon|null $overdueAt
     * @param string|null $role
     * @param string|null $notes
     * @return TaskSLAHistory
     */
    public function recordSLAHistory(
        PlanTask $task,
        ?Carbon $overdueAt = null,
        ?string $role = null,
        ?string $notes = null
    ): TaskSLAHistory
    {
        return TaskSLAHistory::updateOrCreate(
            ['plan_task_id' => $task->id],
            [
                'sla_duration_hours' => $this->getSLADurationForState($task->state ?? TaskState::DRAFT, $task),
                'expected_completion_at' => $task->expected_completion_at,
                'overdue_detected_at' => $overdueAt,
                'notified_to_role' => $role,
                'escalation_level' => 0,
                'notes' => $notes,
                'metadata' => [
                    'state_at_detection' => $task->state?->value,
                    'hours_overdue' => $overdueAt ? $overdueAt->diffInHours(now()) : null,
                ],
            ]
        );
    }

    /**
     * Escalate an overdue task
     *
     * @param PlanTask $task
     * @param int $level
     * @return void
     */
    public function escalateTask(PlanTask $task, int $level): void
    {
        $slaHistory = $task->slaHistory;
        if ($slaHistory) {
            $slaHistory->escalateTo($level);
        }

        Log::warning("Task {$task->id} escalated to level {$level}");
    }

    /**
     * Resolve a task's overdue status
     *
     * @param PlanTask $task
     * @return void
     */
    public function resolveOverdueTask(PlanTask $task): void
    {
        $slaHistory = $task->slaHistory;
        if ($slaHistory) {
            $slaHistory->markResolved();
        }

        $task->update([
            'overdue_flag' => false,
        ]);

        Log::info("Task {$task->id} overdue status resolved");
    }

    /**
     * Get all overdue tasks for a specific role
     *
     * @param string $role
     * @return Collection
     */
    public function getOverdueTasksForRole(string $role): Collection
    {
        return PlanTask::query()
            ->where('overdue_flag', true)
            ->whereNotIn('state', [
                TaskState::COMPLETED->value,
                TaskState::JOB_OFFICER_APPROVED->value,
            ])
            ->with(['monthlyPlan', 'stateHistories'])
            ->get()
            ->filter(function ($task) use ($role) {
                $actor = $this->getResponsibleActorForState($task);
                return $actor && $actor->hasRole($role);
            });
    }

    /**
     * Get SLA summary statistics
     *
     * @return array
     */
    public function getSLASummary(): array
    {
        $onTime = PlanTask::where('overdue_flag', false)->count();
        $overdue = PlanTask::where('overdue_flag', true)->count();
        $total = $onTime + $overdue;

        return [
            'total_tasks' => $total,
            'on_time' => $onTime,
            'overdue' => $overdue,
            'compliance_percentage' => $total > 0 ? ($onTime / $total) * 100 : 0,
            'overdue_by_state' => PlanTask::where('overdue_flag', true)
                ->groupBy('state')
                ->selectRaw('state, count(*) as count')
                ->pluck('count', 'state')
                ->toArray(),
        ];
    }

    /**
     * Helper: Get or create surveillance supervisor
     *
     * @return User|null
     */
    protected function getSurveillanceSupervisor(): ?User
    {
        return User::role('surveillance_supervisor')->first();
    }

    /**
     * Helper: Get MPFM supervisor for a task
     *
     * @param PlanTask $task
     * @return User|null
     */
    protected function getMPFMSupervisor(PlanTask $task): ?User
    {
        return User::role('mpfm_supervisor')->first();
    }
}
