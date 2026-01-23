<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create task_sla_histories table to maintain complete audit trail of SLA events:
     * - When SLA was set
     * - When task became overdue
     * - When notifications were sent
     * - Escalation levels
     * - Resolution status
     */
    public function up(): void
    {
        Schema::create('task_sla_histories', function (Blueprint $table) {
            $table->id();

            // Reference to the task
            $table->unsignedBigInteger('plan_task_id');
            $table->foreign('plan_task_id')
                ->references('id')
                ->on('plan_tasks')
                ->onDelete('cascade');

            // SLA details
            $table->integer('sla_duration_hours')
                ->nullable()
                ->comment('Number of hours for this SLA (at time of creation)');

            $table->dateTime('expected_completion_at')
                ->nullable()
                ->comment('Expected completion datetime');

            // When did task become overdue?
            $table->dateTime('overdue_detected_at')
                ->nullable()
                ->comment('When did the system detect the task was overdue?');

            // Notification details
            $table->dateTime('notification_sent_at')
                ->nullable()
                ->comment('When was notification sent?');

            $table->string('notified_to_role')
                ->nullable()
                ->comment('Which role received the notification?');

            // Escalation tracking
            $table->integer('escalation_level')
                ->default(0)
                ->comment('0=no escalation, 1=first reminder, 2=supervisor, 3=management');

            // Was the issue resolved?
            $table->dateTime('resolution_at')
                ->nullable()
                ->comment('When was the overdue task brought back on track?');

            // Additional info
            $table->text('notes')
                ->nullable()
                ->comment('Notes about the SLA event');

            $table->json('metadata')
                ->nullable()
                ->comment('Additional context (state at detection, hours overdue, etc)');

            $table->timestamps();

            // Indexes for efficient queries
            $table->index('plan_task_id');
            $table->index('overdue_detected_at');
            $table->index(['plan_task_id', 'created_at']);
            $table->index('notified_to_role');
            $table->index('escalation_level');
            $table->index(['overdue_detected_at', 'resolution_at']); // For analytics queries
        });

        \Log::info('Created task_sla_histories table');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_sla_histories');

        \Log::info('Dropped task_sla_histories table');
    }
};
