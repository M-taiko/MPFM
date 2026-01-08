<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MPFMCalculationService;
use App\Services\MPFMParsingService;
use App\Services\MPFMChartService;
use App\Services\MPFMPlanService;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}