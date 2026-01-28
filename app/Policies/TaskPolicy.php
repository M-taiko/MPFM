<?php

namespace App\Policies;

use App\Domains\Task\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if the user can view any tasks
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view tasks
        return true;
    }

    /**
     * Determine if the user can view the task
     */
    public function view(User $user, Task $task): bool
    {
        // Admin can view any task
        if ($user->hasRole('admin')) {
            return true;
        }

        // Surveillance supervisor can view all tasks
        if ($user->hasRole('surveillance_supervisor')) {
            return true;
        }

        // Job officer can view all tasks
        if ($user->hasRole('job_officer')) {
            return true;
        }

        // Department manager can view their department's tasks
        if ($user->hasRole('department_manager') && $user->department_id) {
            return $task->department_id === $user->department_id;
        }

        // Operator can only view assigned tasks
        if ($user->hasRole('operator') || $user->hasRole('mpfm_operator')) {
            return $task->isAssignedTo($user->id);
        }

        return false;
    }

    /**
     * Determine if the user can create tasks
     */
    public function create(User $user): bool
    {
        // Only job officers and admins can create tasks
        return $user->hasAnyRole(['admin', 'job_officer']);
    }

    /**
     * Determine if the user can update the task
     */
    public function update(User $user, Task $task): bool
    {
        // Admin can always update
        if ($user->hasRole('admin')) {
            return true;
        }

        // Job officer can update their own tasks
        if ($user->hasRole('job_officer') && $task->created_by_user_id === $user->id) {
            return true;
        }

        // Department manager can update tasks in their department
        if ($user->hasRole('department_manager') && $user->department_id) {
            return $task->department_id === $user->department_id;
        }

        // Operators cannot update (only view and transition)
        return false;
    }

    /**
     * Determine if the user can delete the task
     */
    public function delete(User $user, Task $task): bool
    {
        // Only admin and job officer can delete
        if ($user->hasRole('admin')) {
            return true;
        }

        // Job officer can only delete their own tasks, and only if in DRAFT state
        if ($user->hasRole('job_officer')) {
            return $task->created_by_user_id === $user->id &&
                   $task->isInState(\App\Enums\TaskState::DRAFT);
        }

        return false;
    }

    /**
     * Determine if the user can transition task to a new state
     */
    public function transition(User $user, Task $task): bool
    {
        // All authenticated users can attempt transitions
        // Actual authorization is handled by TaskStateMachine service
        return true;
    }

    /**
     * Determine if the user can assign operators to the task
     */
    public function assignOperators(User $user, Task $task): bool
    {
        // Admin can always assign
        if ($user->hasRole('admin')) {
            return true;
        }

        // Department manager can assign operators within their department
        if ($user->hasRole('department_manager') && $user->department_id) {
            return $task->department_id === $user->department_id;
        }

        // Surveillance supervisor can assign to any department
        if ($user->hasRole('surveillance_supervisor')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can view the state history
     */
    public function viewStateHistory(User $user, Task $task): bool
    {
        // Same as view permission
        return $this->view($user, $task);
    }

    /**
     * Determine if the user can view SLA information
     */
    public function viewSLAHistory(User $user, Task $task): bool
    {
        // Same rules as viewing task
        return $this->view($user, $task);
    }
}
