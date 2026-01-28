<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\WellStringRequirement;
use App\Domains\Task\Models\Task;
use App\Policies\WellStringRequirementPolicy;
use App\Policies\TaskStatePolicy;
use App\Policies\TaskPolicy;
use App\Policies\AdminPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        WellStringRequirement::class => WellStringRequirementPolicy::class,
        Task::class => TaskPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin access gate - grants full system access
        Gate::define('admin-access', function ($user) {
            return $user && $user->isAdmin();
        });

        // Gate for reviewing tests
        Gate::define('review-test', function ($user) {
            return $user && $user->hasPermissionTo('review-test');
        });

        // Before gate for admin bypass on all policies
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });
    }
}
