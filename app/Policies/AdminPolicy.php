<?php

namespace App\Policies;

use App\Models\User;

/**
 * Admin Policy
 *
 * Grants admin users unrestricted access to all system resources.
 * Admin users bypass all authorization checks and have full control
 * over every aspect of the system including:
 * - All user management operations
 * - All role and permission management
 * - All MPFM test and operational data
 * - All department and workflow management
 * - All reports and analytics
 * - System configuration and settings
 */
class AdminPolicy
{
    /**
     * Check if user is admin
     *
     * Used as a before hook to grant blanket access
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Admins can do everything - catch-all method
     */
    public function __call($name, $arguments)
    {
        return true;
    }
}
