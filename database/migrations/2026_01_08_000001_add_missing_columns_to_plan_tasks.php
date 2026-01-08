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
        Schema::table('plan_tasks', function (Blueprint $table) {
            // Add missing columns from plan.txt structure
            if (!Schema::hasColumn('plan_tasks', 'well_number')) {
                $table->string('well_number')->nullable()->after('sno');
            }
            if (!Schema::hasColumn('plan_tasks', 'string_number')) {
                $table->string('string_number')->nullable()->after('well_number');
            }
            if (!Schema::hasColumn('plan_tasks', 'well_full_name')) {
                $table->string('well_full_name')->nullable()->after('string_number');
            }
            if (!Schema::hasColumn('plan_tasks', 'tower')) {
                $table->string('tower')->nullable()->after('well_full_name');
            }
            if (!Schema::hasColumn('plan_tasks', 'tower_id')) {
                $table->unsignedBigInteger('tower_id')->nullable()->after('tower');
            }
            if (!Schema::hasColumn('plan_tasks', 'electrification_status')) {
                $table->string('electrification_status', 1)->nullable()->after('test_location');
            }
            if (!Schema::hasColumn('plan_tasks', 'production_rate')) {
                $table->string('production_rate')->nullable()->after('tested_whfp');
            }
            if (!Schema::hasColumn('plan_tasks', 'scheduled_date')) {
                $table->date('scheduled_date')->nullable()->after('production_rate');
            }
            if (!Schema::hasColumn('plan_tasks', 'assigned_operator_id')) {
                $table->unsignedBigInteger('assigned_operator_id')->nullable()->after('scheduled_date');
            }
            if (!Schema::hasColumn('plan_tasks', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('assigned_operator_id');
            }
            if (!Schema::hasColumn('plan_tasks', 'assigned_by')) {
                $table->unsignedBigInteger('assigned_by')->nullable()->after('assigned_at');
            }
            if (!Schema::hasColumn('plan_tasks', 'completion_status')) {
                $table->enum('completion_status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending')->after('assigned_by');
            }
            if (!Schema::hasColumn('plan_tasks', 'completion_result')) {
                $table->string('completion_result')->nullable()->after('completion_status');
            }
            if (!Schema::hasColumn('plan_tasks', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('completion_result');
            }
            if (!Schema::hasColumn('plan_tasks', 'parent_task_id')) {
                $table->unsignedBigInteger('parent_task_id')->nullable()->after('is_recurring');
            }
            if (!Schema::hasColumn('plan_tasks', 'supervisor_remarks')) {
                $table->text('supervisor_remarks')->nullable()->after('parent_task_id');
            }
            if (!Schema::hasColumn('plan_tasks', 'hq_notes')) {
                $table->text('hq_notes')->nullable()->after('supervisor_remarks');
            }
            if (!Schema::hasColumn('plan_tasks', 'dhsv_type')) {
                $table->string('dhsv_type')->nullable()->after('mpfm_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropColumn([
                'well_number', 'string_number', 'well_full_name', 'tower', 'tower_id',
                'electrification_status', 'production_rate', 'scheduled_date',
                'assigned_operator_id', 'assigned_at', 'assigned_by',
                'completion_status', 'completion_result', 'is_recurring',
                'parent_task_id', 'supervisor_remarks', 'hq_notes', 'dhsv_type'
            ]);
        });
    }
};
