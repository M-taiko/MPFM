<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MPFMOperationalPlan;

class MPFMOperationalPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'mpfm_supervisor', 'mpfm_operator']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MPFMOperationalPlan $plan): bool
    {
        // Supervisors can view plans they created
        if ($user->hasAnyRole(['admin', 'mpfm_supervisor']) && $plan->created_by === $user->id) {
            return true;
        }

        // Operators can only view plans assigned to them
        if ($user->hasRole('mpfm_operator') && $plan->assigned_user_id === $user->id) {
            return true;
        }

        // Admins can view all
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'mpfm_supervisor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MPFMOperationalPlan $plan): bool
    {
        // Only supervisors who created the plan or admins can update
        return ($user->hasRole('mpfm_supervisor') && $plan->created_by === $user->id) || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MPFMOperationalPlan $plan): bool
    {
        // Only supervisors who created the plan or admins can delete
        return ($user->hasRole('mpfm_supervisor') && $plan->created_by === $user->id) || $user->hasRole('admin');
    }
}

