<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // SLA monitoring - check for overdue tasks every 15 minutes
        if (config('sla.enabled')) {
            $schedule->command('sla:check')
                ->cron(config('sla.check_interval', '*/15 * * * *'))
                ->name('Check Task SLAs')
                ->withoutOverlapping(
                    timeout: config('sla.command_timeout_minutes', 5) // 5 minute timeout
                )
                ->onFailure(function () {
                    \Illuminate\Support\Facades\Log::error('SLA check command failed');
                })
                ->onSuccess(function () {
                    \Illuminate\Support\Facades\Log::info('SLA check command completed successfully');
                });
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
