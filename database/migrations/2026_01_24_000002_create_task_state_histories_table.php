<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_state_histories', function (Blueprint $table) {
            $table->id();

            // Link to task
            $table->foreignId('plan_task_id')
                ->constrained('plan_tasks')
                ->onDelete('cascade');

            // State transition
            $table->string('from_state')->nullable(); // Nullable for initial state
            $table->string('to_state');

            // Who made the change
            $table->foreignId('changed_by')
                ->constrained('users')
                ->onDelete('cascade');

            // Role at time of change (for audit trail)
            $table->string('changed_by_role')->nullable();

            // Context and notes
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            // Timestamp
            $table->timestamp('created_at');

            // Indexes for efficient querying
            $table->index('plan_task_id');
            $table->index(['plan_task_id', 'created_at']);
            $table->index('to_state');
            $table->index('changed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_state_histories');
    }
};
