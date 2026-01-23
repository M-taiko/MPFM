<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PlanTask;
use App\Enums\TaskState;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration maps existing completion_status to new state field.
     * This is OPTIONAL and should only be run once after deploying the state machine.
     */
    public function up(): void
    {
        $this->log("Starting migration of existing tasks to new state machine...");

        // Process tasks in chunks to avoid memory issues
        $totalTasks = 0;
        $updatedTasks = 0;

        PlanTask::chunk(100, function ($tasks) use (&$totalTasks, &$updatedTasks) {
            foreach ($tasks as $task) {
                $totalTasks++;

                // Determine appropriate state based on completion_status
                $newState = $this->determineNewState($task);

                // Only update if state would change
                if ($task->state !== $newState) {
                    $task->update([
                        'state' => $newState,
                    ], ['timestamps' => false]); // Don't update timestamps

                    $updatedTasks++;
                }
            }
        });

        $this->log("Migration complete. Total tasks: {$totalTasks}, Updated: {$updatedTasks}");
    }

    /**
     * Determine new state based on existing completion_status
     */
    protected function determineNewState(PlanTask $task): TaskState
    {
        return match($task->completion_status) {
            // Pending tasks → Check if they're assigned
            'pending' => $task->assigned_operator_id
                ? TaskState::ASSIGNED_TO_MPFM
                : TaskState::DRAFT,

            // In progress tasks
            'in_progress' => TaskState::IN_PROGRESS,

            // Completed tasks
            'completed' => $this->determineCompletedState($task),

            // Failed tasks reset to draft
            'failed' => TaskState::DRAFT,

            // Default to draft
            default => TaskState::DRAFT,
        };
    }

    /**
     * Determine appropriate state for completed tasks
     */
    protected function determineCompletedState(PlanTask $task): TaskState
    {
        // If task has final_status = 'approved', mark as completed
        if ($task->final_status === 'approved') {
            return TaskState::COMPLETED;
        }

        // If task has mpfm_test_id (test uploaded), mark as MPFM reviewed
        if ($task->mpfm_test_id) {
            return TaskState::MPFM_REVIEWED;
        }

        // Otherwise, just in progress (technically completed but not reviewed)
        return TaskState::IN_PROGRESS;
    }

    /**
     * Reverse the migrations.
     *
     * Map state back to completion_status
     */
    public function down(): void
    {
        $this->log("Reverting tasks back to legacy completion_status...");

        $totalTasks = 0;
        $revertedTasks = 0;

        PlanTask::chunk(100, function ($tasks) use (&$totalTasks, &$revertedTasks) {
            foreach ($tasks as $task) {
                $totalTasks++;

                // Determine appropriate completion_status based on state
                $oldStatus = $this->determineOldStatus($task);

                // Only update if status would change
                if ($task->completion_status !== $oldStatus) {
                    $task->update([
                        'completion_status' => $oldStatus,
                    ], ['timestamps' => false]);

                    $revertedTasks++;
                }
            }
        });

        $this->log("Rollback complete. Total tasks: {$totalTasks}, Reverted: {$revertedTasks}");
    }

    /**
     * Determine old completion_status based on new state
     */
    protected function determineOldStatus(PlanTask $task): string
    {
        return match($task->state) {
            TaskState::DRAFT,
            TaskState::SUBMITTED_TO_SURVEILLANCE,
            TaskState::ASSIGNED_TO_MPFM => 'pending',

            TaskState::IN_PROGRESS,
            TaskState::MPFM_REVIEWED,
            TaskState::SURVEILLANCE_REVIEWED,
            TaskState::JOB_OFFICER_APPROVED => 'in_progress',

            TaskState::COMPLETED => 'completed',

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
