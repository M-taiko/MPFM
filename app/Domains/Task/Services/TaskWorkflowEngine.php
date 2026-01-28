<?php

namespace App\Domains\Task\Services;

use App\Domains\Task\Models\Task;
use App\Enums\TaskState;
use App\Models\MonthlyPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TaskWorkflowEngine
{
    private TaskStateMachine $stateMachine;

    public function __construct(TaskStateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * Create multiple tasks from monthly plan specification
     *
     * Usage:
     * $tasks = $engine->createFromMonthlyPlan($plan, [
     *     ['title' => 'Task 1', 'department_id' => 1, 'priority' => 'high'],
     *     ['title' => 'Task 2', 'department_id' => 2, 'priority' => 'medium'],
     * ]);
     */
    public function createFromMonthlyPlan(MonthlyPlan $plan, array $tasksData): Collection
    {
        return DB::transaction(function () use ($plan, $tasksData) {
            $created = collect();

            foreach ($tasksData as $data) {
                $task = Task::create([
                    'monthly_plan_id' => $plan->id,
                    'department_id' => $data['department_id'] ?? 1,
                    'task_type' => $data['task_type'] ?? 'plan_task',
                    'state' => TaskState::DRAFT,
                    'priority' => $data['priority'] ?? 'medium',
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'created_by_user_id' => auth()->id(),
                    'expected_completion_at' => $data['expected_completion_at'] ?? null,
                    'type_specific_data' => $data['type_specific_data'] ?? [],
                ]);

                // Assign operators if provided
                if (isset($data['operators']) && is_array($data['operators'])) {
                    foreach ($data['operators'] as $operatorId => $isPrimary) {
                        $task->operators()->attach($operatorId, [
                            'is_primary' => $isPrimary,
                            'assigned_at' => now(),
                        ]);
                    }
                }

                $created->push($task);
            }

            return $created;
        });
    }

    /**
     * Bulk transition tasks to new state
     * Returns collection with success/failure per task
     */
    public function transitionBulk(
        Collection $tasks,
        TaskState $newState,
        User $user,
        ?string $notes = null
    ): Collection {
        $results = collect();

        foreach ($tasks as $task) {
            try {
                if (!$task->canTransitionTo($newState)) {
                    $results->push([
                        'task_id' => $task->id,
                        'title' => $task->title,
                        'success' => false,
                        'error' => "Cannot transition from {$task->state->label()} to {$newState->label()}",
                    ]);
                    continue;
                }

                $history = $this->stateMachine->transition(
                    $task,
                    $newState,
                    $user,
                    $notes
                );

                $results->push([
                    'task_id' => $task->id,
                    'title' => $task->title,
                    'success' => true,
                    'history' => $history,
                ]);
            } catch (\Exception $e) {
                $results->push([
                    'task_id' => $task->id,
                    'title' => $task->title,
                    'success' => false,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Check SLA violations and escalate if needed
     * Recommended to run via scheduled command
     */
    public function checkAndEscalateSLA(): Collection
    {
        $escalated = collect();

        $overdue = Task::overdue()
            ->where('state', '!=', TaskState::COMPLETED->value)
            ->get();

        foreach ($overdue as $task) {
            $latestSLA = $task->slaHistories()->latest()->first();
            $currentLevel = $latestSLA?->escalation_level ?? 0;

            // Only escalate up to level 3
            if ($currentLevel < 3) {
                $newLevel = $currentLevel + 1;
                $escalationUser = $this->getEscalationUser($task, $newLevel);

                $newSLA = $task->slaHistories()->create([
                    'entered_state' => $task->state->value,
                    'expected_completion_at' => $task->expected_completion_at,
                    'actual_completion_at' => null,
                    'escalation_level' => $newLevel,
                    'escalated_to_user_id' => $escalationUser?->id,
                ]);

                $escalated->push([
                    'task_id' => $task->id,
                    'title' => $task->title,
                    'escalation_level' => $newLevel,
                    'escalated_to' => $escalationUser?->name ?? 'System',
                    'days_overdue' => $task->getDaysOverdue(),
                ]);
            }
        }

        return $escalated;
    }

    /**
     * Complete a task with validation
     * Handles final transition and timestamps
     */
    public function completeTask(Task $task, User $user, ?string $notes = null): bool
    {
        if ($task->isCompleted()) {
            return false;
        }

        return DB::transaction(function () use ($task, $user, $notes) {
            // Mark actual completion
            $task->update([
                'actual_completion_at' => now(),
            ]);

            // Transition to COMPLETED state
            try {
                $this->stateMachine->transition(
                    $task,
                    TaskState::COMPLETED,
                    $user,
                    $notes ?? 'Task completed'
                );
            } catch (\Exception $e) {
                // If transition fails, still mark as completed
                \Log::warning("Task transition failed during completion: {$e->getMessage()}");
            }

            return true;
        });
    }

    /**
     * Get workflow statistics for a monthly plan
     * Useful for dashboards and reporting
     */
    public function getWorkflowStats(MonthlyPlan $plan): array
    {
        $tasks = $plan->tasks()->get();
        $completedTasks = $tasks->where('state', TaskState::COMPLETED->value);
        $overdueTasks = $tasks->filter(fn($t) => $t->isOverdue());

        return [
            'total' => $tasks->count(),
            'completed' => $completedTasks->count(),
            'overdue' => $overdueTasks->count(),
            'completion_percentage' => $tasks->count() > 0
                ? (int) ($completedTasks->count() / $tasks->count() * 100)
                : 0,
            'on_time_percentage' => $completedTasks->count() > 0
                ? (int) ($completedTasks->filter(fn($t) =>
                    $t->actual_completion_at <= $t->expected_completion_at
                )->count() / $completedTasks->count() * 100)
                : 0,
            'by_state' => $tasks->groupBy('state')->map(fn($group) => $group->count()),
            'by_priority' => $tasks->groupBy('priority')->map(fn($group) => $group->count()),
            'by_department' => $tasks->groupBy('department_id')->map(fn($group) => $group->count()),
        ];
    }

    /**
     * Get department manager for task
     */
    public function getDepartmentManager(Task $task): ?User
    {
        return $task->department?->users()
            ->whereHas('roles', fn($q) => $q->where('name', 'department_manager'))
            ->first();
    }

    /**
     * Determine escalation user based on level and task
     */
    private function getEscalationUser(Task $task, int $level): ?User
    {
        return match ($level) {
            1 => $this->getDepartmentManager($task),
            2 => User::whereHas('roles', fn($q) =>
                $q->where('name', 'surveillance_supervisor')
            )->first(),
            default => User::whereHas('roles', fn($q) =>
                $q->where('name', 'admin')
            )->first() ?? User::whereHas('roles', fn($q) =>
                $q->where('name', 'job_officer')
            )->first(),
        };
    }
}
