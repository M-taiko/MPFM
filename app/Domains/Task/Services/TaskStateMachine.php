<?php

namespace App\Domains\Task\Services;

use App\Domains\Task\Models\Task;
use App\Domains\Task\Models\TaskStateHistory;
use App\Domains\Task\Models\TaskSLAHistory;
use App\Enums\TaskState;
use App\Models\User;
use App\Domains\Shared\Exceptions\InvalidStateTransition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskStateMachine
{
    /**
     * Attempt to transition task to a new state
     *
     * @throws InvalidStateTransition
     */
    public function transition(
        Task $task,
        TaskState $newState,
        User $user,
        ?string $notes = null
    ): ?TaskStateHistory {
        // 1. AUTHORIZATION CHECK
        if (!$this->canUserTransition($task, $newState, $user)) {
            throw new InvalidStateTransition(
                "User {$user->id} cannot transition task {$task->id} from {$task->state->value} to {$newState->value}"
            );
        }

        // 2. BUSINESS VALIDATION
        if (!$task->canTransitionTo($newState)) {
            throw new InvalidStateTransition(
                "Cannot transition task {$task->id} from {$task->state->value} to {$newState->value}"
            );
        }

        // 3. ATOMIC TRANSACTION
        return DB::transaction(function () use ($task, $newState, $user, $notes) {
            $oldState = $task->state;

            // Update task state
            $task->state = $newState;
            $task->save();

            // Record in audit trail (only for legacy plan_tasks, not new tasks table)
            // TODO: Create separate audit table for new Task model
            $history = null;
            try {
                $history = TaskStateHistory::create([
                    'plan_task_id' => $task->id,
                    'from_state' => $oldState->value,
                    'to_state' => $newState->value,
                    'changed_by' => $user->id,
                    'changed_by_role' => $user->roles()->first()?->name,
                    'notes' => $notes,
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                // If history table doesn't support this task model, continue without recording
                \Log::warning("Could not record task state history: " . $e->getMessage());
            }

            // Update SLA if needed
            $this->updateSLATracking($task, $newState);

            // Fire event for listeners
            event(new \App\Events\TaskStateChanged($task, $oldState, $newState, $user));

            return $history;
        });
    }

    /**
     * Check if user has authorization to transition task
     */
    public function canUserTransition(
        Task $task,
        TaskState $newState,
        User $user
    ): bool {
        // Admin can always transition
        if ($user->hasRole('admin')) {
            return true;
        }

        // Role-based transition rules (keyed by string value)
        $transitionRules = [
            TaskState::DRAFT->value => ['job_officer', 'admin'],
            TaskState::SUBMITTED_TO_SURVEILLANCE->value => ['surveillance_supervisor', 'job_officer', 'admin'],
            TaskState::ASSIGNED_TO_DEPARTMENT->value => ['surveillance_supervisor', 'department_supervisor', 'admin'],
            TaskState::IN_PROGRESS->value => ['operator', 'mpfm_operator', 'department_supervisor', 'admin'],
            TaskState::DEPARTMENT_REVIEWED->value => ['department_supervisor', 'surveillance_supervisor', 'admin'],
            TaskState::SURVEILLANCE_REVIEWED->value => ['surveillance_supervisor', 'admin'],
            TaskState::JOB_OFFICER_APPROVED->value => ['job_officer', 'admin'],
            TaskState::COMPLETED->value => ['job_officer', 'admin'],
        ];

        $allowedRoles = $transitionRules[$newState->value] ?? [];

        return $user->hasAnyRole($allowedRoles);
    }

    /**
     * Update SLA tracking on state change
     */
    private function updateSLATracking(Task $task, TaskState $newState): void
    {
        // Get SLA duration from department config or defaults
        $slaDuration = $this->getSLADurationForState($task->department, $newState);

        if ($slaDuration > 0) {
            $expectedCompletion = now()->addMinutes($slaDuration);

            $task->update([
                'expected_completion_at' => $expectedCompletion,
            ]);

            try {
                TaskSLAHistory::create([
                    'plan_task_id' => $task->id,
                    'expected_completion_at' => $expectedCompletion,
                    'escalation_level' => 0,
                    'notes' => 'State: ' . $newState->value,
                ]);
            } catch (\Exception $e) {
                // If SLA history table doesn't support this task model, continue without recording
                Log::warning("Could not record task SLA history: " . $e->getMessage());
            }
        }
    }

    /**
     * Get SLA duration for a state (in minutes)
     */
    private function getSLADurationForState($department, TaskState $state): int
    {
        // Default SLA durations (in hours)
        $defaults = [
            TaskState::DRAFT->value => 24,                    // 1 day
            TaskState::SUBMITTED_TO_SURVEILLANCE->value => 48,  // 2 days
            TaskState::ASSIGNED_TO_DEPARTMENT->value => 120,  // 5 days
            TaskState::IN_PROGRESS->value => 240,             // 10 days
            TaskState::DEPARTMENT_REVIEWED->value => 48,      // 2 days
            TaskState::SURVEILLANCE_REVIEWED->value => 48,    // 2 days
            TaskState::JOB_OFFICER_APPROVED->value => 24,     // 1 day
        ];

        // Try to get from department config
        if ($department && $department->configuration) {
            $config = $department->configuration->type_specific_data ?? [];
            $slaConfig = $config['sla_durations'] ?? [];

            if (isset($slaConfig[$state->value])) {
                return intval($slaConfig[$state->value]) * 60;  // Convert hours to minutes
            }
        }

        // Fall back to defaults (convert hours to minutes)
        return ($defaults[$state->value] ?? 0) * 60;
    }
}
