<?php

namespace App\Domains\Task\Services;

use App\Domains\Task\Models\Task;
use App\Domains\Task\Models\TaskStateHistory;
use App\Domains\Task\Models\TaskSLAHistory;
use App\Enums\TaskState;
use App\Models\User;
use App\Domains\Shared\Exceptions\InvalidStateTransition;
use Illuminate\Support\Facades\DB;

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
    ): TaskStateHistory {
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

            // Record in audit trail
            $history = TaskStateHistory::create([
                'task_id' => $task->id,
                'from_state' => $oldState,
                'to_state' => $newState,
                'changed_by_user_id' => $user->id,
                'changed_by_role' => $user->roles()->first()?->name,
                'notes' => $notes,
                'changed_at' => now(),
            ]);

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

        // Role-based transition rules
        $transitionRules = [
            TaskState::DRAFT => ['job_officer', 'admin'],
            TaskState::SUBMITTED_TO_SURVEILLANCE => ['surveillance_supervisor', 'job_officer', 'admin'],
            TaskState::ASSIGNED_TO_DEPARTMENT => ['surveillance_supervisor', 'department_supervisor', 'admin'],
            TaskState::IN_PROGRESS => ['operator', 'mpfm_operator', 'department_supervisor', 'admin'],
            TaskState::DEPARTMENT_REVIEWED => ['department_supervisor', 'surveillance_supervisor', 'admin'],
            TaskState::SURVEILLANCE_REVIEWED => ['surveillance_supervisor', 'admin'],
            TaskState::JOB_OFFICER_APPROVED => ['job_officer', 'admin'],
            TaskState::COMPLETED => ['job_officer', 'admin'],
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

            TaskSLAHistory::create([
                'task_id' => $task->id,
                'entered_state' => $newState->value,
                'expected_completion_at' => $expectedCompletion,
                'escalation_level' => 0,
            ]);
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
