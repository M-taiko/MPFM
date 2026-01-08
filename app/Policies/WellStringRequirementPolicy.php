<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WellStringRequirement;
use Illuminate\Auth\Access\Response;

class WellStringRequirementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_well_string_requirements');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WellStringRequirement $wellStringRequirement): bool
    {
        // Check if user has permission to view based on their role and the requirement status
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('reservoir_engineer') && $wellStringRequirement->created_by === $user->id) {
            return true;
        }
        
        if ($user->hasRole('job_officer') && $wellStringRequirement->status === 'pending_job_officer') {
            return true;
        }
        
        if ($user->hasRole('production_supervisor') && $wellStringRequirement->status === 'pending_production_supervisor') {
            return true;
        }
        
        if ($user->hasRole('mpfm_supervisor') && $wellStringRequirement->status === 'pending_mpfm_supervisor') {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['reservoir_engineer', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WellStringRequirement $wellStringRequirement): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('job_officer') && $wellStringRequirement->status === 'pending_job_officer') {
            return true;
        }
        
        if ($user->hasRole('production_supervisor') && $wellStringRequirement->status === 'pending_production_supervisor') {
            return true;
        }
        
        if ($user->hasRole('mpfm_supervisor') && $wellStringRequirement->status === 'pending_mpfm_supervisor') {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WellStringRequirement $wellStringRequirement): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WellStringRequirement $wellStringRequirement): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WellStringRequirement $wellStringRequirement): bool
    {
        return $user->hasRole('admin');
    }
}