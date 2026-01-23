<?php

namespace App\Listeners;

use App\Events\TaskStateChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UpdateMonthlyPlanStatistics
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param TaskStateChanged $event
     * @return void
     */
    public function handle(TaskStateChanged $event): void
    {
        // Update monthly plan statistics when task state changes
        if ($event->task->monthlyPlan) {
            try {
                $event->task->monthlyPlan->updateStatistics();

                Log::info("Monthly plan statistics updated", [
                    'plan_id' => $event->task->monthlyPlan->id,
                    'task_id' => $event->task->id,
                    'from_state' => $event->oldState->value,
                    'to_state' => $event->newState->value,
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to update monthly plan statistics", [
                    'plan_id' => $event->task->monthlyPlan->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
