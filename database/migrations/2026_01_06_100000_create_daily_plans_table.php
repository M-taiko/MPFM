<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Daily Plan represents a supervisor's plan for a specific date.
     * Contains multiple tasks that can be assigned to different operators.
     */
    public function up(): void
    {
        Schema::create('daily_plans', function (Blueprint $table) {
            $table->id();

            // Plan ownership and metadata
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict')->comment('Supervisor who created the plan');
            $table->string('plan_name')->comment('Descriptive name for the plan');
            $table->date('plan_date')->comment('Date for which this plan is scheduled');

            // Location information
            $table->foreignId('oil_field_id')->constrained('oil_fields')->onDelete('cascade');
            $table->foreignId('tower_id')->nullable()->constrained('towers')->onDelete('cascade');

            // Plan status and notes
            $table->enum('status', ['draft', 'active', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->text('plan_notes')->nullable()->comment('General notes/instructions for the entire plan');

            // Completion tracking
            $table->integer('total_tasks')->default(0)->comment('Total number of tasks in this plan');
            $table->integer('completed_tasks')->default(0)->comment('Number of completed tasks');
            $table->integer('pending_tasks')->default(0)->comment('Number of pending tasks');
            $table->integer('in_progress_tasks')->default(0)->comment('Number of tasks currently in progress');

            // Timestamps
            $table->timestamp('started_at')->nullable()->comment('When first task was started');
            $table->timestamp('completed_at')->nullable()->comment('When all tasks were completed');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('created_by');
            $table->index('plan_date');
            $table->index('status');
            $table->index('oil_field_id');
            $table->index('tower_id');

            // Composite indexes for common queries
            $table->index(['plan_date', 'status']);
            $table->index(['created_by', 'plan_date']);
            $table->index(['oil_field_id', 'plan_date']);
            $table->index(['plan_date', 'status', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_plans');
    }
};
