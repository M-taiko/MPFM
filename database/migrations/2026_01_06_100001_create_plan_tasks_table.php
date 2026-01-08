<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Plan Task represents a single MPFM test task assigned to ONE operator.
     * Multiple tasks can belong to the same daily plan.
     */
    public function up(): void
    {
        Schema::create('plan_tasks', function (Blueprint $table) {
            $table->id();

            // Relationship to daily plan
            $table->foreignId('daily_plan_id')->constrained('daily_plans')->onDelete('cascade');

            // Task assignment
            $table->foreignId('assigned_to')->constrained('users')->onDelete('restrict')->comment('Operator assigned to this task');

            // Task execution order
            $table->integer('task_order')->default(0)->comment('Sequence number for task execution');

            // Well/String information (what needs to be tested)
            $table->foreignId('oil_string_id')->constrained('oil_strings')->onDelete('restrict')->comment('Specific string to test');

            // MPFM Device information
            $table->string('mpfm_device')->nullable()->comment('MPFM/DAFC device serial or ID');
            $table->string('device_type')->nullable()->comment('Device type: MPFM, DAFC, etc.');

            // Expected conditions (for operator guidance)
            $table->decimal('expected_oil_rate', 10, 2)->nullable()->comment('Expected oil rate (STB/d)');
            $table->decimal('expected_gas_rate', 10, 2)->nullable()->comment('Expected gas rate (MMScf/d)');
            $table->decimal('expected_water_cut', 5, 2)->nullable()->comment('Expected water cut (%)');
            $table->decimal('expected_pressure', 10, 2)->nullable()->comment('Expected pressure (psi)');
            $table->decimal('expected_temperature', 8, 2)->nullable()->comment('Expected temperature (°F)');

            // Task-specific instructions
            $table->text('task_instructions')->nullable()->comment('Specific instructions for this task');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // Task status
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed', 'cancelled'])->default('pending');

            // Execution tracking
            $table->timestamp('started_at')->nullable()->comment('When operator started this task');
            $table->timestamp('completed_at')->nullable()->comment('When task was completed');
            $table->integer('duration_minutes')->nullable()->comment('Task duration in minutes');

            // Notes and feedback
            $table->text('operator_notes')->nullable()->comment('Notes from operator during execution');
            $table->text('completion_notes')->nullable()->comment('Notes upon task completion');

            // Issue tracking
            $table->boolean('has_issues')->default(false);
            $table->text('issues_description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('daily_plan_id');
            $table->index('assigned_to');
            $table->index('oil_string_id');
            $table->index('status');
            $table->index('task_order');
            $table->index('priority');

            // Composite indexes for common queries
            $table->index(['daily_plan_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['daily_plan_id', 'task_order']);
            $table->index(['assigned_to', 'status', 'started_at']);
            $table->index(['daily_plan_id', 'assigned_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_tasks');
    }
};
