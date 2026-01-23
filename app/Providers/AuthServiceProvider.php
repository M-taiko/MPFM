<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\WellStringRequirement;
use App\Models\PlanTask;
use App\Policies\WellStringRequirementPolicy;
use App\Policies\TaskStatePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        WellStringRequirement::class => WellStringRequirementPolicy::class,
        PlanTask::class => TaskStatePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate for reviewing tests
        Gate::define('review-test', function ($user) {
            return $user && $user->hasPermissionTo('review-test');
        });
    }
}
