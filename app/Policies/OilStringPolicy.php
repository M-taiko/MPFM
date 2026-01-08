<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OilString;

class OilStringPolicy
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
    public function view(User $user, OilString $oilString): bool
    {
        return $user->hasAnyRole(['admin', 'mpfm_supervisor', 'mpfm_operator']);
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
    public function update(User $user, OilString $oilString): bool
    {
        return $user->hasAnyRole(['admin', 'mpfm_supervisor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OilString $oilString): bool
    {
        return $user->hasRole('admin');
    }
}

