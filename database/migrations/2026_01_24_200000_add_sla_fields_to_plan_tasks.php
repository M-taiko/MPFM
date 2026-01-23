<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add SLA monitoring fields to plan_tasks table:
     * - expected_completion_at: When the task should be completed
     * - overdue_flag: Boolean for fast queries on overdue tasks
     * - sla_notified_at: Timestamp of last SLA notification (prevents duplicates)
     */
    public function up(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            // SLA deadline
            $table->dateTime('expected_completion_at')
                ->nullable()
                ->after('surveillance_assigned_at')
                ->comment('Expected task completion datetime based on SLA');

            // Flag for fast queries
            $table->boolean('overdue_flag')
                ->default(false)
                ->after('expected_completion_at')
                ->comment('Indicates task is overdue (current_time > expected_completion_at)');

            // Track last notification to prevent duplicates
            $table->dateTime('sla_notified_at')
                ->nullable()
                ->after('overdue_flag')
                ->comment('Timestamp when last SLA notification was sent');

            // Indexes for SLA queries (critical for performance)
            $table->index('expected_completion_at');
            $table->index(['overdue_flag', 'state']);
            $table->index(['state', 'expected_completion_at']);
        });

        \Log::info('SLA fields added to plan_tasks table');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropIndex(['overdue_flag', 'state']);
            $table->dropIndex(['state', 'expected_completion_at']);
            $table->dropIndex(['expected_completion_at']);

            $table->dropColumn(['expected_completion_at', 'overdue_flag', 'sla_notified_at']);
        });

        \Log::info('SLA fields dropped from plan_tasks table');
    }
};
