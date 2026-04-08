<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TestVessel;

class TestVesselPolicy
{
    /**
     * Determine whether the user can view any test vessels.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'job_officer', 'surveillance_supervisor', 'mpfm_operator', 'department_manager']);
    }

    /**
     * Determine whether the user can view the test vessel.
     */
    public function view(User $user, TestVessel $testVessel): bool
    {
        return $user->hasAnyRole(['admin', 'job_officer', 'surveillance_supervisor', 'mpfm_operator', 'department_manager']);
    }

    /**
     * Determine whether the user can create test vessels.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'job_officer', 'surveillance_supervisor']);
    }

    /**
     * Determine whether the user can update the test vessel.
     */
    public function update(User $user, TestVessel $testVessel): bool
    {
        return $user->hasAnyRole(['admin', 'job_officer', 'surveillance_supervisor']);
    }

    /**
     * Determine whether the user can delete the test vessel.
     */
    public function delete(User $user, TestVessel $testVessel): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the test vessel.
     */
    public function forceDelete(User $user, TestVessel $testVessel): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the test vessel.
     */
    public function restore(User $user, TestVessel $testVessel): bool
    {
        return $user->hasRole('admin');
    }
}
