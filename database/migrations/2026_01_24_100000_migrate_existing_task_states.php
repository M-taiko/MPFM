<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Enums\TaskState;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration maps existing completion_status to new state field.
     * This is OPTIONAL and should only be run once after deploying the state machine.
     *
     * NOTE: Updated in Phase 2 to use raw SQL instead of PlanTask model (model deleted)
     */
    public function up(): void
    {
        $this->log("Starting migration of existing tasks to new state machine...");

        // Process tasks using raw SQL (PlanTask model was deleted in Phase 2)
        $tasks = DB::table('plan_tasks')->get();
        $totalTasks = $tasks->count();
        $updatedTasks = 0;

        foreach ($tasks as $task) {
            // Determine appropriate state based on completion_status
            $newState = $this->determineNewState($task);

            // Only update if state would change
            if ($task->state !== $newState) {
                DB::table('plan_tasks')
                    ->where('id', $task->id)
                    ->update(['state' => $newState]);

                $updatedTasks++;
            }
        }

        $this->log("Migration complete. Total tasks: {$totalTasks}, Updated: {$updatedTasks}");
    }

    /**
     * Determine new state based on existing completion_status
     */
    protected function determineNewState($task): string
    {
        return match($task->completion_status) {
            // Pending tasks → Check if they're assigned
            'pending' => $task->assigned_operator_id
                ? 'assigned_to_mpfm'
                : 'draft',

            // In progress tasks
            'in_progress' => 'in_progress',

            // Completed tasks
            'completed' => $this->determineCompletedState($task),

            // Failed tasks reset to draft
            'failed' => 'draft',

            // Default to draft
            default => 'draft',
        };
    }

    /**
     * Determine appropriate state for completed tasks
     */
    protected function determineCompletedState($task): string
    {
        // If task has final_status = 'approved', mark as completed
        if ($task->final_status === 'approved') {
            return 'completed';
        }

        // If task has mpfm_test_id (test uploaded), mark as MPFM reviewed
        if ($task->mpfm_test_id) {
            return 'mpfm_reviewed';
        }

        // Otherwise, just in progress (technically completed but not reviewed)
        return 'in_progress';
    }

    /**
     * Reverse the migrations.
     *
     * Map state back to completion_status
     */
    public function down(): void
    {
        $this->log("Reverting tasks back to legacy completion_status...");

        $tasks = DB::table('plan_tasks')->get();
        $totalTasks = $tasks->count();
        $revertedTasks = 0;

        foreach ($tasks as $task) {
            // Determine appropriate completion_status based on state
            $oldStatus = $this->determineOldStatus($task);

            // Only update if status would change
            if ($task->completion_status !== $oldStatus) {
                DB::table('plan_tasks')
                    ->where('id', $task->id)
                    ->update(['completion_status' => $oldStatus]);

                $revertedTasks++;
            }
        }

        $this->log("Rollback complete. Total tasks: {$totalTasks}, Reverted: {$revertedTasks}");
    }

    /**
     * Determine old completion_status based on new state
     */
    protected function determineOldStatus($task): string
    {
        return match($task->state) {
            'draft',
            'submitted_to_surveillance',
            'assigned_to_mpfm' => 'pending',

            'in_progress',
            'mpfm_reviewed',
            'surveillance_reviewed',
            'job_officer_approved' => 'in_progress',

            'completed' => 'completed',

            default => 'pending',
        };
    }

    /**
     * Log migration messages
     */
    protected function log(string $message): void
    {
        if (app()->runningInConsole()) {
            echo "\n{$message}\n";
        }
        \Log::info("TaskState Migration: {$message}");
    }
};
