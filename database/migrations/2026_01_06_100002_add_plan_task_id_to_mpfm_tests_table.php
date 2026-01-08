<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Links mpfm_tests to plan_tasks.
     * When an operator completes a task, an mpfm_test is created and linked.
     */
    public function up(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            // Add foreign key to plan_tasks
            $table->foreignId('plan_task_id')
                  ->nullable()
                  ->after('mpfm_operational_plan_id')
                  ->constrained('plan_tasks')
                  ->onDelete('set null')
                  ->comment('Link to the specific task that generated this test');

            // Add index for performance
            $table->index('plan_task_id');

            // Composite index for common queries
            $table->index(['plan_task_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            $table->dropForeign(['plan_task_id']);
            $table->dropIndex(['plan_task_id']);
            $table->dropIndex(['plan_task_id', 'status']);
            $table->dropColumn('plan_task_id');
        });
    }
};
