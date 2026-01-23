<?php

namespace App\Services;

use App\Models\PlanTask;
use App\Models\User;
use App\Models\TaskStateHistory;
use App\Enums\TaskState;
use App\Events\TaskStateChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskStateService
{
    /**
     * Transition a task to a new state with authorization and validation.
     *
     * @param PlanTask $task
     * @param TaskState $newState
     * @param User $user
     * @param string|null $notes
     * @param array $metadata
     * @return PlanTask
     * @throws \Exception
     */
    public function transition(
        PlanTask $task,
        TaskState $newState,
        User $user,
        ?string $notes = null,
        array $metadata = []
    ): PlanTask {
        // Authorization check
        if (!$this->canUserTransition($task, $newState, $user)) {
            throw new \Exception(
                "You are not authorized to transition this task to {$newState->label()}."
            );
        }

        // Validation check
        if (!$task->canTransitionTo($newState)) {
            $currentLabel = $task->getCurrentState()->label();
            $newLabel = $newState->label();
            throw new \Exception(
                "Invalid transition from '{$currentLabel}' to '{$newLabel}'."
            );
        }

        return DB::transaction(function () use ($task, $newState, $user, $notes, $metadata) {
            $oldState = $task->getCurrentState();

            // Update task state
            $task->state = $newState;

            // Update related fields based on state
            $this->updateRelatedFields($task, $newState, $user);

            $task->save();

            // Record history
            $this->recordHistory($task, $oldState, $newState, $user, $notes, $metadata);

            // Fire event for listeners (statistics update, notifications, etc.)
            event(new TaskStateChanged($task, $oldState, $newState, $user));

            // Log the transition
            Log::info("Task state transition", [
                'task_id' => $task->id,
                'from_state' => $oldState->value,
                'to_state' => $newState->value,
                'user_id' => $user->id,
                'user_role' => $user->roles->first()?->name,
            ]);

            return $task->fresh();
        });
    }

    /**
     * Check if user can transition task to new state.
     *
     * @param PlanTask $task
     * @param TaskState $newState
     * @param User $user
     * @return bool
     */
    public function canUserTransition(PlanTask $task, TaskState $newState, User $user): bool
    {
        // Admins can perform any transition
        if ($user->hasRole('admin')) {
            return true;
        }

        return match($newState) {
            // Job Officer submits tasks to surveillance
            TaskState::SUBMITTED_TO_SURVEILLANCE =>
                $user->hasRole('job_officer'),

            // Surveillance Supervisor assigns to MPFM department
            TaskState::ASSIGNED_TO_MPFM =>
                $user->hasRole('surveillance_supervisor'),

            // Surveillance can send back to draft for corrections
            TaskState::DRAFT =>
                $user->hasRole('surveillance_supervisor') &&
                $task->isInState(TaskState::SUBMITTED_TO_SURVEILLANCE),

            // MPFM Operator starts assigned tasks
            TaskState::IN_PROGRESS =>
                ($user->hasRole('mpfm_operator') || $user->hasRole('operator')) &&
                $task->isAssignedTo($user->id),

            // MPFM Supervisor reviews operator submissions
            TaskState::MPFM_REVIEWED =>
                $user->hasRole('mpfm_supervisor'),

            // Surveillance Supervisor pre-final review
            TaskState::SURVEILLANCE_REVIEWED =>
                $user->hasRole('surveillance_supervisor'),

            // Job Officer gives final approval
            TaskState::JOB_OFFICER_APPROVED =>
                $user->hasRole('job_officer'),

            // Job Officer marks as completed after approval
            TaskState::COMPLETED =>
                $user->hasRole('job_officer'),

            default => false
        };
    }

    /**
     * Update related fields when state changes.
     *
     * @param PlanTask $task
     * @param TaskState $newState
     * @param User $user
     * @return void
     */
    protected function updateRelatedFields(PlanTask $task, TaskState $newState, User $user): void
    {
        match($newState) {
            // When assigned to MPFM, record surveillance supervisor
            TaskState::ASSIGNED_TO_MPFM => function() use ($task, $user) {
                $task->surveillance_supervisor_id = $user->id;
                $task->surveillance_assigned_at = now();
            },

            // When operator starts, update completion_status
            TaskState::IN_PROGRESS => function() use ($task) {
                $task->completion_status = 'in_progress';
            },

            // When completed, mark all statuses as completed
            TaskState::COMPLETED => function() use ($task) {
                $task->completion_status = 'completed';
                if (!$task->completed_at) {
                    $task->completed_at = now();
                }
                $task->final_status = 'approved';
            },

            // For MPFM_REVIEWED going back to IN_PROGRESS (rejection)
            TaskState::IN_PROGRESS => function() use ($task) {
                if ($task->getCurrentState() === TaskState::MPFM_REVIEWED) {
                    // Operator needs to redo
                    $task->completion_status = 'in_progress';
                }
            },

            default => null
        };
    }

    /**
     * Record state transition in history table.
     *
     * @param PlanTask $task
     * @param TaskState $oldState
     * @param TaskState $newState
     * @param User $user
     * @param string|null $notes
     * @param array $metadata
     * @return TaskStateHistory
     */
    protected function recordHistory(
        PlanTask $task,
        TaskState $oldState,
        TaskState $newState,
        User $user,
        ?string $notes,
        array $metadata
    ): TaskStateHistory {
        return TaskStateHistory::create([
            'plan_task_id' => $task->id,
            'from_state' => $oldState,
            'to_state' => $newState,
            'changed_by' => $user->id,
            'changed_by_role' => $user->roles->first()?->name,
            'notes' => $notes,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    /**
     * Get tasks by state for a specific role/user.
     *
     * @param TaskState $state
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTasksByStateForRole(TaskState $state, User $user)
    {
        $query = PlanTask::where('state', $state)
            ->with(['monthlyPlan', 'operators', 'towerModel', 'surveillanceSupervisor']);

        // Filter based on role
        if ($user->hasRole('mpfm_operator') || $user->hasRole('operator')) {
            // Operators see only their assigned tasks
            $query->whereHas('operators', fn($q) => $q->where('user_id', $user->id));
        } elseif ($user->hasRole('job_officer')) {
            // Job Officers see tasks from their plans
            $query->whereHas('monthlyPlan', fn($q) => $q->where('created_by', $user->id));
        }
        // Supervisors see all tasks in their review states

        return $query->orderBy('scheduled_date')->get();
    }

    /**
     * Get workflow statistics.
     *
     * @param User|null $forUser
     * @return array
     */
    public function getWorkflowStatistics(?User $forUser = null): array
    {
        $query = PlanTask::query();

        // Filter by user role if specified
        if ($forUser) {
            if ($forUser->hasRole('mpfm_operator') || $forUser->hasRole('operator')) {
                $query->whereHas('operators', fn($q) => $q->where('user_id', $forUser->id));
            } elseif ($forUser->hasRole('job_officer')) {
                $query->whereHas('monthlyPlan', fn($q) => $q->where('created_by', $forUser->id));
            }
        }

        return [
            'draft' => (clone $query)->where('state', TaskState::DRAFT)->count(),
            'submitted_to_surveillance' => (clone $query)->where('state', TaskState::SUBMITTED_TO_SURVEILLANCE)->count(),
            'assigned_to_mpfm' => (clone $query)->where('state', TaskState::ASSIGNED_TO_MPFM)->count(),
            'in_progress' => (clone $query)->where('state', TaskState::IN_PROGRESS)->count(),
            'mpfm_reviewed' => (clone $query)->where('state', TaskState::MPFM_REVIEWED)->count(),
            'surveillance_reviewed' => (clone $query)->where('state', TaskState::SURVEILLANCE_REVIEWED)->count(),
            'job_officer_approved' => (clone $query)->where('state', TaskState::JOB_OFFICER_APPROVED)->count(),
            'completed' => (clone $query)->where('state', TaskState::COMPLETED)->count(),
            'total' => $query->count(),
        ];
    }

    /**
     * Bulk transition multiple tasks to the same state.
     *
     * @param array $taskIds
     * @param TaskState $newState
     * @param User $user
     * @param string|null $notes
     * @return array
     */
    public function bulkTransition(
        array $taskIds,
        TaskState $newState,
        User $user,
        ?string $notes = null
    ): array {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($taskIds as $taskId) {
            try {
                $task = PlanTask::findOrFail($taskId);
                $this->transition($task, $newState, $user, $notes);
                $results['success'][] = $taskId;
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'task_id' => $taskId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get allowed transitions for a task based on user role.
     *
     * @param PlanTask $task
     * @param User $user
     * @return array
     */
    public function getAllowedTransitionsForUser(PlanTask $task, User $user): array
    {
        $allowedTransitions = [];
        $currentState = $task->getCurrentState();
        $possibleTransitions = $currentState->allowedTransitions();

        foreach ($possibleTransitions as $transition) {
            if ($this->canUserTransition($task, $transition, $user)) {
                $allowedTransitions[] = $transition;
            }
        }

        return $allowedTransitions;
    }

    /**
     * Check if a task can be started by an operator.
     *
     * @param PlanTask $task
     * @param User $operator
     * @return bool
     */
    public function canOperatorStartTask(PlanTask $task, User $operator): bool
    {
        return $task->isInState(TaskState::ASSIGNED_TO_MPFM)
            && $task->isAssignedTo($operator->id)
            && $task->completion_status === 'pending';
    }

    /**
     * Get tasks pending review for a supervisor role.
     *
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTasksPendingReview(string $role)
    {
        $state = match($role) {
            'mpfm_supervisor' => TaskState::IN_PROGRESS,
            'surveillance_supervisor' => TaskState::MPFM_REVIEWED,
            'job_officer' => TaskState::SURVEILLANCE_REVIEWED,
            default => null
        };

        if (!$state) {
            return collect();
        }

        return PlanTask::where('state', $state)
            ->with(['monthlyPlan', 'operators', 'towerModel', 'mpfmTest'])
            ->orderBy('scheduled_date')
            ->get();
    }
}
