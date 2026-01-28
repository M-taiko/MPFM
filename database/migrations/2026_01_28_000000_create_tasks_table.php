<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TaskState;
use App\Enums\TaskType;
use App\Enums\TaskPriority;

return new class extends Migration
{
    public function up(): void
    {
        // Only create if table doesn't exist (idempotent)
        if (Schema::hasTable('tasks')) {
            // Table already exists from previous migration run
            return;
        }

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->foreignId('monthly_plan_id')->constrained('monthly_plans');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('oil_string_id')->nullable()->constrained('oil_strings');
            $table->foreignId('tower_id')->nullable()->constrained('towers');

            // Task identity
            $table->string('task_type'); // Enum: plan_task, daily_plan, operator_plan
            $table->enum('state', array_map(fn($case) => $case->value, TaskState::cases()))
                ->default(TaskState::DRAFT->value);
            $table->string('priority')->default('medium'); // low, medium, high, critical
            $table->string('title');
            $table->text('description')->nullable();

            // User assignments
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users');

            // Timestamps
            $table->dateTime('expected_completion_at')->nullable();
            $table->dateTime('actual_completion_at')->nullable();
            $table->text('notes')->nullable();

            // Type-specific data (JSON)
            $table->json('type_specific_data')->nullable();

            // Related entities
            $table->foreignId('mpfm_test_id')->nullable()->constrained('mpfm_tests')->nullOnDelete();

            // Standard timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('monthly_plan_id');
            $table->index('department_id');
            $table->index('state');
            $table->index('task_type');
            $table->index('assigned_to_user_id');
            $table->index('created_by_user_id');
            $table->index('expected_completion_at');
            $table->index(['department_id', 'state']);
            $table->index(['department_id', 'task_type']);
            $table->index(['monthly_plan_id', 'state']);
            $table->unique(['monthly_plan_id', 'id']); // For scoping
        });

        // Task state history - audit trail (only if doesn't exist)
        if (!Schema::hasTable('task_state_histories')) {
            Schema::create('task_state_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
                $table->string('from_state');
                $table->string('to_state');
                $table->foreignId('changed_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->string('changed_by_role')->nullable();
                $table->text('notes')->nullable();
                $table->dateTime('changed_at')->useCurrent();
                $table->index('task_id');
                $table->index('changed_at');
                $table->index(['task_id', 'changed_at']);
            });
        }

        // Task SLA history - SLA tracking (only if doesn't exist)
        if (!Schema::hasTable('task_sla_histories')) {
            Schema::create('task_sla_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
                $table->string('entered_state');
                $table->dateTime('expected_completion_at');
                $table->dateTime('actual_completion_at')->nullable();
                $table->integer('escalation_level')->default(0);
                $table->foreignId('escalated_to_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('resolved_at')->nullable();
                $table->timestamps();
                $table->index('task_id');
                $table->index('expected_completion_at');
                $table->index('escalation_level');
            });
        }

        // Many-to-many: tasks to operators (only if doesn't exist)
        if (!Schema::hasTable('task_operators')) {
            Schema::create('task_operators', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->boolean('is_primary')->default(false);
                $table->dateTime('assigned_at')->useCurrent();
                $table->timestamps();
                $table->unique(['task_id', 'user_id']);
                $table->index('task_id');
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('task_operators');
        Schema::dropIfExists('task_sla_histories');
        Schema::dropIfExists('task_state_histories');
        Schema::dropIfExists('tasks');
    }
};
