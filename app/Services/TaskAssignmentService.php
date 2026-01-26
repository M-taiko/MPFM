<?php

namespace App\Services;

use App\Models\Department;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TaskAssignmentService
{
    /**
     * Assign a task to a department (Job Officer action)
     *
     * @param PlanTask $task
     * @param int $departmentId
     * @param User $assignedBy
     * @return PlanTask
     * @throws \Exception
     */
    public function assignToDepartment(
        PlanTask $task,
        int $departmentId,
        User $assignedBy
    ): PlanTask {
        // Validate Job Officer
        if (!$assignedBy->hasRole('job_officer')) {
            throw new \Exception('Only Job Officers can assign tasks to departments.');
        }

        $department = Department::findOrFail($departmentId);

        $task->update([
            'department_id' => $departmentId,
            'assigned_by' => $assignedBy->id,
            'assigned_at' => now(),
        ]);

        Log::info("Task {$task->id} assigned to department {$department->name} by Job Officer {$assignedBy->id}");

        return $task->fresh();
    }

    /**
     * Assign an operator to a task (Department Manager action)
     *
     * Only department managers can assign operators, and they can only assign
     * operators from their own department to tasks in their department.
     *
     * @param PlanTask $task
     * @param User $operator
     * @param User $assignedBy
     * @param bool $isPrimary
     * @return void
     * @throws \Exception
     */
    public function assignOperatorToTask(
        PlanTask $task,
        User $operator,
        User $assignedBy,
        bool $isPrimary = true
    ): void {
        // Validate Department Manager
        if (!$assignedBy->hasRole('department_manager')) {
            throw new \Exception('Only Department Managers can assign operators.');
        }

        // Validate same department for task
        if ($task->department_id !== $assignedBy->department_id) {
            throw new \Exception('Cannot assign operators to tasks outside your department.');
        }

        // Validate same department for operator
        if ($operator->department_id !== $task->department_id) {
            throw new \Exception('Operator must be in the same department as the task.');
        }

        // Validate operator has operator role
        if (!$operator->hasAnyRole(['mpfm_operator', 'operator'])) {
            throw new \Exception('Selected user is not an operator.');
        }

        // Use existing many-to-many relationship
        $task->operators()->attach($operator->id, [
            'assigned_at' => now(),
            'assigned_by' => $assignedBy->id,
            'is_primary' => $isPrimary,
        ]);

        // Update primary operator field if this is primary assignment
        if ($isPrimary) {
            $task->update(['assigned_operator_id' => $operator->id]);
        }

        Log::info(
            "Operator {$operator->id} assigned to task {$task->id} by Department Manager {$assignedBy->id}"
        );
    }

    /**
     * Unassign an operator from a task (Department Manager action)
     *
     * @param PlanTask $task
     * @param User $operator
     * @param User $unassignedBy
     * @return void
     * @throws \Exception
     */
    public function unassignOperatorFromTask(
        PlanTask $task,
        User $operator,
        User $unassignedBy
    ): void {
        // Validate Department Manager
        if (!$unassignedBy->hasRole('department_manager')) {
            throw new \Exception('Only Department Managers can unassign operators.');
        }

        // Validate same department
        if ($task->department_id !== $unassignedBy->department_id) {
            throw new \Exception('Cannot unassign operators from tasks outside your department.');
        }

        // Remove from pivot table
        $task->operators()->detach($operator->id);

        // Clear primary operator if this was the primary
        if ($task->assigned_operator_id === $operator->id) {
            // Try to set another operator as primary
            $primaryOperator = $task->primaryOperator()->first();
            if ($primaryOperator) {
                $task->update(['assigned_operator_id' => $primaryOperator->id]);
            } else {
                $task->update(['assigned_operator_id' => null]);
            }
        }

        Log::info(
            "Operator {$operator->id} unassigned from task {$task->id} by Department Manager {$unassignedBy->id}"
        );
    }

    /**
     * Get available operators for a department
     *
     * Returns all users in the department with operator roles
     *
     * @param int $departmentId
     * @return Collection
     */
    public function getAvailableOperators(int $departmentId): Collection
    {
        return User::where('department_id', $departmentId)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['mpfm_operator', 'operator']))
            ->get();
    }

    /**
     * Get available operators for bulk assignment
     *
     * Returns operators grouped by availability/assigned task count
     *
     * @param int $departmentId
     * @return array
     */
    public function getAvailableOperatorsGrouped(int $departmentId): array
    {
        $operators = $this->getAvailableOperators($departmentId);

        return [
            'all' => $operators,
            'count' => $operators->count(),
        ];
    }

    /**
     * Bulk assign multiple operators to multiple tasks
     *
     * @param array $taskIds array of task IDs
     * @param array $operatorIds array of operator IDs
     * @param User $assignedBy
     * @return array result summary
     * @throws \Exception
     */
    public function bulkAssignOperators(
        array $taskIds,
        array $operatorIds,
        User $assignedBy
    ): array {
        if (!$assignedBy->hasRole('department_manager')) {
            throw new \Exception('Only Department Managers can bulk assign operators.');
        }

        $results = [
            'assigned' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($taskIds as $taskId) {
            try {
                $task = PlanTask::findOrFail($taskId);

                // Validate department match
                if ($task->department_id !== $assignedBy->department_id) {
                    $results['failed']++;
                    $results['errors'][] = "Task {$taskId} is in different department";
                    continue;
                }

                // Assign first operator as primary
                foreach ($operatorIds as $index => $operatorId) {
                    $operator = User::findOrFail($operatorId);
                    $this->assignOperatorToTask($task, $operator, $assignedBy, $index === 0);
                    $results['assigned']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Task {$taskId}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Remove all operators from a task
     *
     * @param PlanTask $task
     * @param User $removedBy
     * @return void
     * @throws \Exception
     */
    public function clearTaskOperators(
        PlanTask $task,
        User $removedBy
    ): void {
        if (!$removedBy->hasRole('department_manager')) {
            throw new \Exception('Only Department Managers can clear task operators.');
        }

        if ($task->department_id !== $removedBy->department_id) {
            throw new \Exception('Cannot modify operators for tasks outside your department.');
        }

        $task->operators()->detach();
        $task->update(['assigned_operator_id' => null]);

        Log::info("All operators cleared from task {$task->id} by Department Manager {$removedBy->id}");
    }
}
