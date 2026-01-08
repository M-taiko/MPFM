<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MPFMTest;

class MPFMTestPolicy
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
    public function view(User $user, MPFMTest $test): bool
    {
        // Admins and supervisors can view all
        if ($user->hasAnyRole(['admin', 'mpfm_supervisor'])) {
            return true;
        }

        // Operators can only view tests they created or tests linked to their assigned plans
        if ($user->hasRole('mpfm_operator')) {
            if ($test->user_id === $user->id) {
                return true;
            }

            // Check if test is linked to a plan assigned to this operator
            if ($test->mpfm_operational_plan_id) {
                $plan = $test->mpfmOperationalPlan;
                if ($plan && $plan->assigned_user_id === $user->id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'mpfm_operator']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MPFMTest $test): bool
    {
        // Admins and supervisors can update all
        if ($user->hasAnyRole(['admin', 'mpfm_supervisor'])) {
            return true;
        }

        // Operators can only update their own tests if status is draft
        if ($user->hasRole('mpfm_operator') && $test->user_id === $user->id && $test->status === 'draft') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MPFMTest $test): bool
    {
        // Only admins and supervisors can delete
        return $user->hasAnyRole(['admin', 'mpfm_supervisor']);
    }
}

