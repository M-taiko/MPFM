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
        // Monthly Plans Table (Job Officer creates this)
        Schema::create('monthly_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name'); // e.g., "MPFM Flow Testing Dec. 2025"
            $table->string('plan_month'); // e.g., "December 2025"
            $table->integer('total_tests')->default(0); // Total number of tests in plan
            $table->integer('monthly_target_kpi')->default(0); // KPI target
            $table->integer('total_approved')->default(0);
            $table->integer('total_rejected')->default(0);
            $table->integer('total_pending')->default(0);
            $table->integer('total_completed')->default(0); // Tests marked "YES"
            $table->integer('total_issues')->default(0); // Tests marked "NO"
            $table->integer('kpi_progress')->default(0); // Progress vs KPI

            // Workflow fields
            $table->unsignedBigInteger('created_by'); // Job Officer who created plan
            $table->unsignedBigInteger('reviewed_by')->nullable(); // MPFM Supervisor who reviewed
            $table->timestamp('reviewed_at')->nullable();
            $table->enum('status', ['draft', 'under_review', 'active', 'completed', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });

        // Plan Tasks Table (Individual test tasks in the plan)
        Schema::create('plan_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monthly_plan_id');

            // Task identification
            $table->integer('sno'); // Serial number in plan
            $table->string('well_number'); // e.g., "388"
            $table->string('string_number'); // e.g., "2"
            $table->string('well_full_name'); // e.g., "388/2"

            // Location & Equipment
            $table->string('tower')->nullable(); // e.g., "3", "15", "26/26A"
            $table->unsignedBigInteger('tower_id')->nullable(); // FK to towers table
            $table->unsignedBigInteger('oil_string_id')->nullable(); // FK to oil_strings table
            $table->string('flow_route')->nullable(); // e.g., "ZC", "ZW"
            $table->string('reservoir_layer')->nullable(); // e.g., "IVB-6", "VU-6"

            // Test specifications
            $table->string('test_location')->nullable(); // "Locally", "Scada", "0"
            $table->string('electrification_status', 1)->nullable(); // Y/N
            $table->string('mpfm_type')->nullable(); // "Vx-Spectra", "Vx-PW"
            $table->string('dhsv_type')->nullable(); // "DKS", "FXE", "FRE", "VE"
            $table->string('sub_category')->nullable();
            $table->string('category')->nullable(); // "Healthy", "Active", "HGOR-OPEN"

            // Test parameters
            $table->decimal('required_whfp', 10, 2)->nullable(); // Required Wellhead Flowing Pressure
            $table->decimal('tested_whfp', 10, 2)->nullable(); // Actual tested WHFP
            $table->string('production_rate')->nullable(); // e.g., "3 MBD", "2.5 MBD"

            // Scheduling & Assignment
            $table->date('scheduled_date')->nullable();
            $table->unsignedBigInteger('assigned_operator_id')->nullable(); // MPFM Supervisor assigns operator
            $table->timestamp('assigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable(); // Who assigned the operator

            // Completion tracking
            $table->enum('completion_status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->string('completion_result')->nullable(); // "YES", "NO" from plan.txt
            $table->timestamp('completed_at')->nullable();
            $table->enum('final_status', ['approved', 'rejected', 'pending'])->default('pending');

            // Priority & Recurrence
            $table->enum('priority', ['normal', 'high', 'top'])->default('normal');
            $table->boolean('is_recurring')->default(false);
            $table->unsignedBigInteger('parent_task_id')->nullable(); // For recurring tests

            // Linked test result
            $table->unsignedBigInteger('mpfm_test_id')->nullable(); // FK to well_tests

            // Notes & Remarks
            $table->text('supervisor_remarks')->nullable();
            $table->text('hq_notes')->nullable();
            $table->text('operator_notes')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('monthly_plan_id')->references('id')->on('monthly_plans')->onDelete('cascade');
            $table->foreign('tower_id')->references('id')->on('towers')->onDelete('set null');
            $table->foreign('oil_string_id')->references('id')->on('oil_strings')->onDelete('set null');
            $table->foreign('assigned_operator_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('parent_task_id')->references('id')->on('plan_tasks')->onDelete('set null');
            $table->foreign('mpfm_test_id')->references('id')->on('well_tests')->onDelete('set null');

            // Indexes
            $table->index('scheduled_date');
            $table->index('assigned_operator_id');
            $table->index('completion_status');
        });

        // Add plan_task_id to well_tests table if not exists
        if (Schema::hasTable('well_tests') && !Schema::hasColumn('well_tests', 'plan_task_id')) {
            Schema::table('well_tests', function (Blueprint $table) {
                $table->unsignedBigInteger('plan_task_id')->nullable()->after('id');
                $table->foreign('plan_task_id')->references('id')->on('plan_tasks')->onDelete('set null');
                $table->index('plan_task_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key from well_tests
        if (Schema::hasTable('well_tests') && Schema::hasColumn('well_tests', 'plan_task_id')) {
            Schema::table('well_tests', function (Blueprint $table) {
                $table->dropForeign(['plan_task_id']);
                $table->dropColumn('plan_task_id');
            });
        }

        Schema::dropIfExists('plan_tasks');
        Schema::dropIfExists('monthly_plans');
    }
};
