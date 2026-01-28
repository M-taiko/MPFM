<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Services\MPFMCalculationService;
use App\Services\MPFMParsingService;
use App\Services\MPFMChartService;
use App\Services\MPFMPlanService;
use App\Services\TaskStateService;
use App\Services\TaskSLAService;
use App\Events\TaskStateChanged;
use App\Events\SLATriggered;
use App\Listeners\UpdateMonthlyPlanStatistics;
use App\Listeners\NotifyStakeholders;
use App\Listeners\NotifySLAOverdueListener;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register MPFM services
        $this->app->singleton(MPFMCalculationService::class, function ($app) {
            return new MPFMCalculationService();
        });

        $this->app->singleton(MPFMParsingService::class, function ($app) {
            return new MPFMParsingService();
        });

        $this->app->singleton(MPFMChartService::class, function ($app) {
            return new MPFMChartService();
        });

        $this->app->singleton(MPFMPlanService::class, function ($app) {
            return new MPFMPlanService();
        });

        // Register Task State Service
        $this->app->singleton(TaskStateService::class, function ($app) {
            return new TaskStateService();
        });

        // Register Task SLA Service
        $this->app->singleton(TaskSLAService::class, function ($app) {
            return new TaskSLAService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // PlanTask observer removed in Phase 2 (model deleted, replaced with unified Task model)

        // Register Event Listeners for TaskStateChanged
        Event::listen(
            TaskStateChanged::class,
            [UpdateMonthlyPlanStatistics::class, NotifyStakeholders::class]
        );

        // Register Event Listener for SLATriggered
        Event::listen(
            SLATriggered::class,
            NotifySLAOverdueListener::class
        );
    }
}