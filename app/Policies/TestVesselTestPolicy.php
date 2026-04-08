<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TestVesselTest;

class TestVesselTestPolicy
{
    /**
     * Determine whether the user can view any test vessel tests.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'job_officer', 'surveillance_supervisor', 'mpfm_operator', 'department_manager']);
    }

    /**
     * Determine whether the user can view the test vessel test.
     */
    public function view(User $user, TestVesselTest $tvTest): bool
    {
        return $user->hasAnyRole(['admin', 'job_officer', 'surveillance_supervisor', 'mpfm_operator', 'department_manager']);
    }

    /**
     * Determine whether the user can create test vessel tests.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'surveillance_supervisor', 'mpfm_operator']);
    }

    /**
     * Determine whether the user can update the test vessel test.
     */
    public function update(User $user, TestVesselTest $tvTest): bool
    {
        return $user->hasAnyRole(['admin', 'surveillance_supervisor', 'mpfm_operator']) || $user->id === $tvTest->user_id;
    }

    /**
     * Determine whether the user can delete the test vessel test.
     */
    public function delete(User $user, TestVesselTest $tvTest): bool
    {
        return $user->hasAnyRole(['admin', 'surveillance_supervisor']) || $user->id === $tvTest->user_id;
    }

    /**
     * Determine whether the user can permanently delete the test vessel test.
     */
    public function forceDelete(User $user, TestVesselTest $tvTest): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the test vessel test.
     */
    public function restore(User $user, TestVesselTest $tvTest): bool
    {
        return $user->hasRole('admin');
    }
}
