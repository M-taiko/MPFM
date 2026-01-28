<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\TaskState;

return new class extends Migration
{
    public function up(): void
    {
        // STEP 0: Check if migration already ran (idempotent)
        $alreadyMigrated = DB::table('tasks')
            ->where('task_type', 'plan_task')
            ->exists();

        if ($alreadyMigrated) {
            \Log::info('Phase 2 migration: Data already migrated, skipping');
            return;
        }

        // STEP 1: Check if plan_tasks table exists
        if (!Schema::hasTable('plan_tasks')) {
            \Log::info('Phase 2 migration: plan_tasks table not found, skipping migration');
            return;
        }

        // STEP 2: Create backup view for reference
        try {
            DB::statement('CREATE OR REPLACE VIEW plan_tasks_backup AS SELECT * FROM plan_tasks');
        } catch (\Exception $e) {
            \Log::warning('Backup view creation skipped: ' . $e->getMessage());
        }

        // STEP 3: Migrate data from plan_tasks to tasks
        // Get MPFM department (or create if doesn't exist)
        $mpfmDept = DB::table('departments')->where('code', 'MPFM')->first();
        $deptId = $mpfmDept?->id ?? 1; // Default to 1 if no MPFM dept

        // Get count of plan_tasks to migrate (before migration)
        $planTaskCount = DB::table('plan_tasks')->count();

        if ($planTaskCount > 0) {
            // Migrate data from plan_tasks to tasks
            // plan_tasks is MPFM-specific with well/test location data
            try {
                DB::statement("
                    INSERT INTO tasks (
                        monthly_plan_id, department_id, oil_string_id, tower_id, mpfm_test_id,
                        task_type, state, priority, title, description,
                        assigned_to_user_id, created_by_user_id, expected_completion_at,
                        type_specific_data, created_at, updated_at
                    )
                    SELECT
                        pt.monthly_plan_id,
                        $deptId as department_id,
                        pt.oil_string_id,
                        pt.tower_id,
                        pt.mpfm_test_id,
                        'plan_task' as task_type,
                        COALESCE(pt.state, 'draft') as state,
                        COALESCE(pt.priority, 'normal') as priority,
                        COALESCE(
                            pt.well_full_name,
                            CONCAT_WS(' - ', pt.well_number, pt.test_location),
                            CONCAT('MPFM Test ', pt.id)
                        ) as title,
                        COALESCE(pt.supervisor_remarks, pt.operator_notes) as description,
                        pt.assigned_operator_id as assigned_to_user_id,
                        COALESCE(pt.assigned_by, 1) as created_by_user_id,
                        pt.expected_completion_at,
                        JSON_OBJECT(
                            'well_number', pt.well_number,
                            'test_location', pt.test_location,
                            'flow_route', pt.flow_route,
                            'mpfm_type', pt.mpfm_type,
                            'dhsv_type', pt.dhsv_type,
                            'category', pt.category,
                            'sub_category', pt.sub_category,
                            'completion_status', pt.completion_status,
                            'completion_result', pt.completion_result,
                            'final_status', pt.final_status
                        ) as type_specific_data,
                        pt.created_at,
                        pt.updated_at
                    FROM plan_tasks pt
                ");
            } catch (\Exception $e) {
                \Log::error('Phase 2 migration: Data migration failed: ' . $e->getMessage());
                throw new \Exception('Phase 2 migration failed: ' . $e->getMessage());
            }
        }

        // STEP 4: Migrate operator assignments
        // Operator already assigned via assigned_to_user_id in main migration
        // This step is for additional operators if needed in future

        // STEP 5: Create initial SLA records for migrated tasks
        $migratedTaskCount = DB::table('tasks')->where('task_type', 'plan_task')->count();

        if ($migratedTaskCount > 0) {
            DB::statement("
                INSERT INTO task_sla_histories (task_id, entered_state, expected_completion_at, escalation_level, created_at, updated_at)
                SELECT
                    t.id,
                    t.state,
                    t.expected_completion_at,
                    0,
                    NOW(),
                    NOW()
                FROM tasks t
                WHERE t.task_type = 'plan_task'
                AND t.expected_completion_at IS NOT NULL
            ");
        }

        // STEP 6: Verify data integrity
        $oldCount = DB::table('plan_tasks')->count();
        $newCount = DB::table('tasks')->where('task_type', 'plan_task')->count();

        if ($oldCount !== $newCount) {
            throw new \Exception(
                "Data migration verification failed: plan_tasks=$oldCount, tasks=$newCount. " .
                "Migration rolled back. Please check the database and try again."
            );
        }

        if ($oldCount === 0) {
            \Log::info("Phase 2 Migration: No plan_tasks to migrate (table is empty). Migration completed successfully.");
        } else {
            \Log::info("Phase 2 Migration: Successfully migrated $oldCount plan tasks to unified Task model");
        }
    }

    public function down(): void
    {
        // STEP 1: Delete migrated tasks
        $taskIds = DB::table('tasks')
            ->where('task_type', 'plan_task')
            ->pluck('id');

        // STEP 2: Delete related SLA histories
        DB::table('task_sla_histories')
            ->whereIn('task_id', $taskIds)
            ->delete();

        // STEP 3: Delete state histories
        DB::table('task_state_histories')
            ->whereIn('task_id', $taskIds)
            ->delete();

        // STEP 4: Delete operator assignments
        DB::table('task_operators')
            ->whereIn('task_id', $taskIds)
            ->delete();

        // STEP 5: Delete tasks
        DB::table('tasks')
            ->where('task_type', 'plan_task')
            ->delete();

        // STEP 6: Drop backup view
        try {
            DB::statement('DROP VIEW IF EXISTS plan_tasks_backup');
        } catch (\Exception $e) {
            \Log::warning('Backup view drop skipped: ' . $e->getMessage());
        }

        \Log::info("Phase 2 Migration: Rollback complete. plan_tasks table remains intact.");
    }
};
