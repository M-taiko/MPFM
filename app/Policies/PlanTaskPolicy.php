<?php

namespace App\Policies;

use App\Models\PlanTask;
use App\Models\User;

class PlanTaskPolicy
{
    /**
     * Determine if the user can create a task
     *
     * Only Job Officers can create tasks
     */
    public function create(User $user): bool
    {
        return $user->hasRole('job_officer');
    }

    /**
     * Determine if the user can assign a task to a department
     *
     * Only Job Officers can assign tasks to departments
     */
    public function assignToDepartment(User $user, PlanTask $task): bool
    {
        return $user->hasRole('job_officer');
    }

    /**
     * Determine if the user can assign an operator to a task
     *
     * Department Managers can assign operators in their own department
     */
    public function assignToOperator(User $user, PlanTask $task): bool
    {
        // Admins can do anything
        if ($user->hasRole('admin')) {
            return true;
        }

        // Only department managers can assign operators
        if (!$user->hasRole('department_manager')) {
            return false;
        }

        // Must be in the same department
        return $task->department_id === $user->department_id;
    }

    /**
     * Determine if the user can view a task
     *
     * Job Officers, Surveillance Supervisors, and Admins can see all tasks
     * Department Managers can see tasks in their department
     * Operators can see only assigned tasks
     */
    public function view(User $user, PlanTask $task): bool
    {
        // Admin and Job Officer can view all tasks
        if ($user->hasAnyRole(['admin', 'job_officer'])) {
            return true;
        }

        // Surveillance Supervisor can view all tasks (read-only)
        if ($user->hasRole('surveillance_supervisor')) {
            return true;
        }

        // Department Manager can view tasks in their department
        if ($user->hasRole('department_manager')) {
            return $task->department_id === $user->department_id;
        }

        // Operators (mpfm_operator, operator) can only view assigned tasks
        if ($user->hasAnyRole(['mpfm_operator', 'operator'])) {
            return $task->isAssignedTo($user->id);
        }

        return false;
    }

    /**
     * Determine if the user can view all tasks (global view)
     *
     * Admin, Job Officer, and Surveillance Supervisor can view all
     */
    public function viewAll(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'job_officer', 'surveillance_supervisor']);
    }

    /**
     * Determine if the user can update a task
     *
     * Job Officer can edit tasks in draft state
     * Department Manager cannot edit the task directly (only assign operators)
     * Operator cannot edit task, only update status
     */
    public function update(User $user, PlanTask $task): bool
    {
        // Admins can update any task
        if ($user->hasRole('admin')) {
            return true;
        }

        // Job Officer can edit their own tasks in editable states
        if ($user->hasRole('job_officer')) {
            return $task->isEditableByJobOfficer();
        }

        return false;
    }

    /**
     * Determine if the user can delete a task
     *
     * Only Admin and Job Officer can delete (and only in draft state)
     */
    public function delete(User $user, PlanTask $task): bool
    {
        // Admins can delete any task
        if ($user->hasRole('admin')) {
            return true;
        }

        // Job Officer can delete tasks in draft state
        if ($user->hasRole('job_officer')) {
            return $task->isInState(\App\Enums\TaskState::DRAFT);
        }

        return false;
    }

    /**
     * Determine if the user can update the task status
     *
     * Operators can update status of their assigned tasks
     */
    public function updateStatus(User $user, PlanTask $task): bool
    {
        // Operators can update status of assigned tasks
        if ($user->hasAnyRole(['mpfm_operator', 'operator'])) {
            return $task->isAssignedTo($user->id);
        }

        // Admin can update any task status
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can view department for this task
     */
    public function viewDepartment(User $user, PlanTask $task): bool
    {
        // Anyone who can view the task can view its department
        return $this->view($user, $task);
    }
}
