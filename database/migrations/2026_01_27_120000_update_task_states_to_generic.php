<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: This migration converts MPFM-specific state names to generic department-aware names
     * for backward compatibility during refactoring.
     */
    public function up(): void
    {
        // Update plan_tasks.state column
        DB::table('plan_tasks')
            ->where('state', 'assigned_to_mpfm')
            ->update(['state' => 'assigned_to_department']);

        DB::table('plan_tasks')
            ->where('state', 'mpfm_reviewed')
            ->update(['state' => 'department_reviewed']);

        // Update task_state_histories.from_state column
        DB::table('task_state_histories')
            ->where('from_state', 'assigned_to_mpfm')
            ->update(['from_state' => 'assigned_to_department']);

        DB::table('task_state_histories')
            ->where('from_state', 'mpfm_reviewed')
            ->update(['from_state' => 'department_reviewed']);

        // Update task_state_histories.to_state column
        DB::table('task_state_histories')
            ->where('to_state', 'assigned_to_mpfm')
            ->update(['to_state' => 'assigned_to_department']);

        DB::table('task_state_histories')
            ->where('to_state', 'mpfm_reviewed')
            ->update(['to_state' => 'department_reviewed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert plan_tasks.state column
        DB::table('plan_tasks')
            ->where('state', 'assigned_to_department')
            ->update(['state' => 'assigned_to_mpfm']);

        DB::table('plan_tasks')
            ->where('state', 'department_reviewed')
            ->update(['state' => 'mpfm_reviewed']);

        // Revert task_state_histories.from_state column
        DB::table('task_state_histories')
            ->where('from_state', 'assigned_to_department')
            ->update(['from_state' => 'assigned_to_mpfm']);

        DB::table('task_state_histories')
            ->where('from_state', 'department_reviewed')
            ->update(['from_state' => 'mpfm_reviewed']);

        // Revert task_state_histories.to_state column
        DB::table('task_state_histories')
            ->where('to_state', 'assigned_to_department')
            ->update(['to_state' => 'assigned_to_mpfm']);

        DB::table('task_state_histories')
            ->where('to_state', 'department_reviewed')
            ->update(['to_state' => 'mpfm_reviewed']);
    }
};
