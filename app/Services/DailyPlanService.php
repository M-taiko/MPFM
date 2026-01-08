<?php

namespace App\Services;

use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class DailyPlanService
{
    /**
     * Create a new daily plan with tasks
     */
    public function createPlan(array $planData, array $tasks = []): DailyPlan
    {
        return DB::transaction(function () use ($planData, $tasks) {
            // Create the daily plan
            $plan = DailyPlan::create([
                'created_by' => $planData['created_by'],
                'plan_name' => $planData['plan_name'],
                'plan_date' => $planData['plan_date'],
                'oil_field_id' => $planData['oil_field_id'],
                'tower_id' => $planData['tower_id'] ?? null,
                'plan_notes' => $planData['plan_notes'] ?? null,
                'status' => 'draft',
            ]);

            // Add tasks if provided
            if (!empty($tasks)) {
                $this->addTasksToPlan($plan, $tasks);
            }

            return $plan->fresh();
        });
    }

    /**
     * Add tasks to an existing plan
     */
    public function addTasksToPlan(DailyPlan $plan, array $tasks): void
    {
        $taskOrder = $plan->tasks()->max('task_order') ?? 0;

        foreach ($tasks as $taskData) {
            $taskOrder++;

            PlanTask::create([
                'daily_plan_id' => $plan->id,
                'assigned_to' => $taskData['assigned_to'],
                'task_order' => $taskOrder,
                'oil_string_id' => $taskData['oil_string_id'],
                'mpfm_device' => $taskData['mpfm_device'] ?? null,
                'device_type' => $taskData['device_type'] ?? null,
                'expected_oil_rate' => $taskData['expected_oil_rate'] ?? null,
                'expected_gas_rate' => $taskData['expected_gas_rate'] ?? null,
                'expected_water_cut' => $taskData['expected_water_cut'] ?? null,
                'expected_pressure' => $taskData['expected_pressure'] ?? null,
                'expected_temperature' => $taskData['expected_temperature'] ?? null,
                'task_instructions' => $taskData['task_instructions'] ?? null,
                'priority' => $taskData['priority'] ?? 'normal',
                'status' => 'pending',
            ]);
        }

        $plan->updateTaskCounts();
    }

    /**
     * Update a plan
     */
    public function updatePlan(DailyPlan $plan, array $data): DailyPlan
    {
        $plan->update($data);
        return $plan->fresh();
    }

    /**
     * Activate a plan (make it ready for operators)
     */
    public function activatePlan(DailyPlan $plan): DailyPlan
    {
        if (!$plan->canBeActivated()) {
            throw new \Exception('Plan cannot be activated. Ensure it has tasks and is in draft status.');
        }

        $plan->update(['status' => 'active']);
        return $plan->fresh();
    }

    /**
     * Cancel a plan
     */
    public function cancelPlan(DailyPlan $plan, string $reason = null): DailyPlan
    {
        return DB::transaction(function () use ($plan, $reason) {
            // Cancel all pending tasks
            $plan->tasks()->whereIn('status', ['pending', 'in_progress'])->update([
                'status' => 'cancelled',
                'completion_notes' => $reason ?? 'Plan cancelled by supervisor',
            ]);

            $plan->update([
                'status' => 'cancelled',
                'plan_notes' => ($plan->plan_notes ?? '') . "\n\nCancelled: " . ($reason ?? 'No reason provided'),
            ]);

            $plan->updateTaskCounts();
            return $plan->fresh();
        });
    }

    /**
     * Get operator's tasks for a specific date
     */
    public function getOperatorTasksForDate(User $operator, Carbon $date): Collection
    {
        return PlanTask::with(['dailyPlan', 'oilString.tower.oilField'])
            ->assignedTo($operator->id)
            ->whereHas('dailyPlan', function ($query) use ($date) {
                $query->forDate($date)
                      ->whereIn('status', ['active', 'in_progress']);
            })
            ->orderedBySequence()
            ->get();
    }

    /**
     * Get all operator's pending tasks
     */
    public function getOperatorPendingTasks(User $operator): Collection
    {
        return PlanTask::with(['dailyPlan', 'oilString.tower.oilField'])
            ->assignedTo($operator->id)
            ->pending()
            ->whereHas('dailyPlan', function ($query) {
                $query->active();
            })
            ->orderedBySequence()
            ->get();
    }

    /**
     * Get operator's tasks grouped by plan
     */
    public function getOperatorTasksGroupedByPlan(User $operator, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = DailyPlan::with(['tasks' => function ($query) use ($operator) {
                $query->assignedTo($operator->id)
                      ->orderedBySequence();
            }])
            ->whereHas('tasks', function ($query) use ($operator) {
                $query->assignedTo($operator->id);
            });

        if ($startDate && $endDate) {
            $query->whereBetween('plan_date', [$startDate, $endDate]);
        }

        return $query->orderBy('plan_date', 'desc')->get();
    }

    /**
     * Start a task (operator action)
     */
    public function startTask(PlanTask $task, User $operator): PlanTask
    {
        if ($task->assigned_to !== $operator->id) {
            throw new \Exception('This task is not assigned to you.');
        }

        if (!$task->canBeStarted()) {
            throw new \Exception('This task cannot be started.');
        }

        $task->start();
        return $task->fresh();
    }

    /**
     * Complete a task (operator action)
     */
    public function completeTask(PlanTask $task, array $completionData): PlanTask
    {
        $task->complete($completionData);
        return $task->fresh();
    }

    /**
     * Get plan statistics
     */
    public function getPlanStatistics(DailyPlan $plan): array
    {
        return [
            'total_tasks' => $plan->total_tasks,
            'completed_tasks' => $plan->completed_tasks,
            'pending_tasks' => $plan->pending_tasks,
            'in_progress_tasks' => $plan->in_progress_tasks,
            'completion_percentage' => $plan->completion_percentage,
            'is_completed' => $plan->isCompleted(),
            'operators_count' => $plan->tasks()->distinct('assigned_to')->count('assigned_to'),
            'average_task_duration' => $plan->tasks()->completed()->avg('duration_minutes'),
            'tasks_with_issues' => $plan->tasks()->where('has_issues', true)->count(),
        ];
    }

    /**
     * Get supervisor's plan overview for a date range
     */
    public function getSupervisorPlanOverview(User $supervisor, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = DailyPlan::with(['tasks.operator', 'oilField', 'tower'])
            ->createdBy($supervisor->id);

        if ($startDate && $endDate) {
            $query->whereBetween('plan_date', [$startDate, $endDate]);
        }

        return $query->orderBy('plan_date', 'desc')->get();
    }

    /**
     * Get operator performance metrics
     */
    public function getOperatorPerformance(User $operator, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = PlanTask::assignedTo($operator->id);

        if ($startDate && $endDate) {
            $query->whereHas('dailyPlan', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('plan_date', [$startDate, $endDate]);
            });
        }

        $tasks = $query->get();

        return [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'completed')->count(),
            'pending_tasks' => $tasks->where('status', 'pending')->count(),
            'in_progress_tasks' => $tasks->where('status', 'in_progress')->count(),
            'failed_tasks' => $tasks->where('status', 'failed')->count(),
            'completion_rate' => $tasks->count() > 0
                ? round(($tasks->where('status', 'completed')->count() / $tasks->count()) * 100, 2)
                : 0,
            'average_duration' => $tasks->where('status', 'completed')->avg('duration_minutes'),
            'tasks_with_issues' => $tasks->where('has_issues', true)->count(),
        ];
    }

    /**
     * Reorder tasks in a plan
     */
    public function reorderTasks(DailyPlan $plan, array $taskIdOrder): void
    {
        DB::transaction(function () use ($plan, $taskIdOrder) {
            foreach ($taskIdOrder as $order => $taskId) {
                PlanTask::where('id', $taskId)
                    ->where('daily_plan_id', $plan->id)
                    ->update(['task_order' => $order + 1]);
            }
        });
    }

    /**
     * Clone a plan to a new date
     */
    public function clonePlan(DailyPlan $originalPlan, Carbon $newDate, ?string $newName = null): DailyPlan
    {
        return DB::transaction(function () use ($originalPlan, $newDate, $newName) {
            // Create new plan
            $newPlan = DailyPlan::create([
                'created_by' => $originalPlan->created_by,
                'plan_name' => $newName ?? ($originalPlan->plan_name . ' (Copy)'),
                'plan_date' => $newDate,
                'oil_field_id' => $originalPlan->oil_field_id,
                'tower_id' => $originalPlan->tower_id,
                'plan_notes' => $originalPlan->plan_notes,
                'status' => 'draft',
            ]);

            // Clone tasks
            foreach ($originalPlan->tasks as $originalTask) {
                PlanTask::create([
                    'daily_plan_id' => $newPlan->id,
                    'assigned_to' => $originalTask->assigned_to,
                    'task_order' => $originalTask->task_order,
                    'oil_string_id' => $originalTask->oil_string_id,
                    'mpfm_device' => $originalTask->mpfm_device,
                    'device_type' => $originalTask->device_type,
                    'expected_oil_rate' => $originalTask->expected_oil_rate,
                    'expected_gas_rate' => $originalTask->expected_gas_rate,
                    'expected_water_cut' => $originalTask->expected_water_cut,
                    'expected_pressure' => $originalTask->expected_pressure,
                    'expected_temperature' => $originalTask->expected_temperature,
                    'task_instructions' => $originalTask->task_instructions,
                    'priority' => $originalTask->priority,
                    'status' => 'pending',
                ]);
            }

            $newPlan->updateTaskCounts();
            return $newPlan->fresh();
        });
    }
}
