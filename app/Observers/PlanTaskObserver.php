<?php

namespace App\Observers;

use App\Models\PlanTask;
use Illuminate\Support\Facades\Log;

class PlanTaskObserver
{
    /**
     * Handle the PlanTask "updated" event.
     *
     * @param PlanTask $planTask
     * @return void
     */
    public function updated(PlanTask $planTask): void
    {
        // If state was changed, update monthly plan statistics
        if ($planTask->wasChanged('state')) {
            if ($planTask->monthlyPlan) {
                try {
                    $planTask->monthlyPlan->updateStatistics();

                    Log::debug("Observer: Monthly plan statistics updated after task state change", [
                        'plan_id' => $planTask->monthlyPlan->id,
                        'task_id' => $planTask->id,
                        'old_state' => $planTask->getOriginal('state'),
                        'new_state' => $planTask->state,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Observer: Failed to update monthly plan statistics", [
                        'plan_id' => $planTask->monthlyPlan->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Also update if completion_status changed (for backward compatibility)
        if ($planTask->wasChanged('completion_status')) {
            if ($planTask->monthlyPlan) {
                try {
                    $planTask->monthlyPlan->updateStatistics();
                } catch (\Exception $e) {
                    Log::error("Observer: Failed to update monthly plan statistics on completion_status change", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Handle the PlanTask "deleted" event.
     *
     * @param PlanTask $planTask
     * @return void
     */
    public function deleted(PlanTask $planTask): void
    {
        if ($planTask->monthlyPlan) {
            try {
                $planTask->monthlyPlan->updateStatistics();

                Log::debug("Observer: Monthly plan statistics updated after task deletion", [
                    'plan_id' => $planTask->monthlyPlan->id,
                    'task_id' => $planTask->id,
                ]);
            } catch (\Exception $e) {
                Log::error("Observer: Failed to update monthly plan statistics after deletion", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
