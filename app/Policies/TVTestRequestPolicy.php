<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TVTestRequest;

class TVTestRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'job_officer', 'surveillance_supervisor', 'mpfm_operator', 'operator']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TVTestRequest $tvTestRequest): bool
    {
        return $user->hasRole(['admin', 'job_officer', 'surveillance_supervisor', 'mpfm_operator', 'operator']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'job_officer', 'surveillance_supervisor']);
    }

    /**
     * Determine whether the user can start a test for this request.
     */
    public function startTest(User $user, TVTestRequest $tvTestRequest): bool
    {
        return $user->hasRole(['admin', 'mpfm_operator', 'operator']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TVTestRequest $tvTestRequest): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can force delete the model.
     */
    public function forceDelete(User $user, TVTestRequest $tvTestRequest): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TVTestRequest $tvTestRequest): bool
    {
        return $user->hasRole('admin');
    }
}
