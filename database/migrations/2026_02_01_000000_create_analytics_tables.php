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
        // Department metrics tracking
        if (!Schema::hasTable('department_metrics')) {
            Schema::create('department_metrics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->date('metric_date');
                $table->integer('total_tasks')->default(0);
                $table->integer('completed_tasks')->default(0);
                $table->integer('pending_tasks')->default(0);
                $table->integer('overdue_tasks')->default(0);
                $table->decimal('completion_rate', 5, 2)->default(0);
                $table->decimal('average_completion_time_minutes', 8, 2)->default(0);
                $table->decimal('sla_compliance_rate', 5, 2)->default(0);
                $table->timestamps();

                $table->index('department_id');
                $table->index('metric_date');
                $table->index(['department_id', 'metric_date']);
                $table->unique(['department_id', 'metric_date'], 'dept_metric_unique');
            });
        }

        // Test statistics and trending
        if (!Schema::hasTable('test_statistics')) {
            Schema::create('test_statistics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->string('parameter_name'); // e.g., 'GOR', 'Temperature'
                $table->date('statistic_date');
                $table->decimal('average_value', 12, 4);
                $table->decimal('min_value', 12, 4);
                $table->decimal('max_value', 12, 4);
                $table->decimal('std_deviation', 12, 4)->default(0);
                $table->integer('sample_count')->default(0);
                $table->timestamps();

                $table->index('department_id');
                $table->index('parameter_name');
                $table->index('statistic_date');
                $table->index(['department_id', 'parameter_name']);
                $table->unique(['department_id', 'parameter_name', 'statistic_date'], 'test_stat_unique');
            });
        }

        // User performance metrics
        if (!Schema::hasTable('user_performance_metrics')) {
            Schema::create('user_performance_metrics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->date('metric_date');
                $table->integer('tasks_assigned')->default(0);
                $table->integer('tasks_completed')->default(0);
                $table->integer('tasks_overdue')->default(0);
                $table->decimal('completion_rate', 5, 2)->default(0);
                $table->decimal('average_completion_time_minutes', 8, 2)->default(0);
                $table->decimal('productivity_score', 5, 2)->default(0);
                $table->timestamps();

                $table->index('user_id');
                $table->index('department_id');
                $table->index('metric_date');
                $table->index(['user_id', 'metric_date']);
                $table->unique(['user_id', 'department_id', 'metric_date'], 'user_perf_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('user_performance_metrics')) {
            Schema::dropIfExists('user_performance_metrics');
        }

        if (Schema::hasTable('test_statistics')) {
            Schema::dropIfExists('test_statistics');
        }

        if (Schema::hasTable('department_metrics')) {
            Schema::dropIfExists('department_metrics');
        }

        Schema::enableForeignKeyConstraints();
    }
};
