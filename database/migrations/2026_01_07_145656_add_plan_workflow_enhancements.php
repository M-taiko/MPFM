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
        // Add plan_task_id to well_tests table
        if (Schema::hasTable('well_tests') && !Schema::hasColumn('well_tests', 'plan_task_id')) {
            Schema::table('well_tests', function (Blueprint $table) {
                $table->unsignedBigInteger('plan_task_id')->nullable()->after('id');
                $table->foreign('plan_task_id')->references('id')->on('plan_tasks')->onDelete('set null');
            });
        }

        // Enhance plan_tasks table
        if (Schema::hasTable('plan_tasks')) {
            Schema::table('plan_tasks', function (Blueprint $table) {
                // Add fields from plan.txt if they don't exist
                if (!Schema::hasColumn('plan_tasks', 'sno')) {
                    $table->string('sno')->nullable()->after('id');
                }
                if (!Schema::hasColumn('plan_tasks', 'flow_route')) {
                    $table->string('flow_route')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'reservoir_layer')) {
                    $table->string('reservoir_layer')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'test_location')) {
                    $table->string('test_location')->nullable()->comment('Locally or SCADA');
                }
                if (!Schema::hasColumn('plan_tasks', 'mpfm_type')) {
                    $table->string('mpfm_type')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'dhsv')) {
                    $table->string('dhsv')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'sub_category')) {
                    $table->string('sub_category')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'category')) {
                    $table->string('category')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'required_whfp')) {
                    $table->float('required_whfp')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'tested_whfp')) {
                    $table->float('tested_whfp')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'expected_oil_rate')) {
                    $table->float('expected_oil_rate')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'expected_gas_rate')) {
                    $table->float('expected_gas_rate')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'expected_water_cut')) {
                    $table->float('expected_water_cut')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'expected_pressure')) {
                    $table->float('expected_pressure')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'expected_temperature')) {
                    $table->float('expected_temperature')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'final_status')) {
                    $table->string('final_status')->nullable();
                }
                if (!Schema::hasColumn('plan_tasks', 'mpfm_test_id')) {
                    $table->unsignedBigInteger('mpfm_test_id')->nullable();
                }
            });
        }

        // Enhance daily_plans table
        if (Schema::hasTable('daily_plans')) {
            Schema::table('daily_plans', function (Blueprint $table) {
                if (!Schema::hasColumn('daily_plans', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable();
                }
                if (!Schema::hasColumn('daily_plans', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable();
                }
                if (!Schema::hasColumn('daily_plans', 'monthly_target_kpi')) {
                    $table->integer('monthly_target_kpi')->nullable();
                }
                if (!Schema::hasColumn('daily_plans', 'total_tests')) {
                    $table->integer('total_tests')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove plan_task_id from well_tests
        if (Schema::hasTable('well_tests') && Schema::hasColumn('well_tests', 'plan_task_id')) {
            Schema::table('well_tests', function (Blueprint $table) {
                $table->dropForeign(['plan_task_id']);
                $table->dropColumn('plan_task_id');
            });
        }

        // Remove enhanced fields from plan_tasks
        if (Schema::hasTable('plan_tasks')) {
            Schema::table('plan_tasks', function (Blueprint $table) {
                $columnsToRemove = [
                    'sno', 'flow_route', 'reservoir_layer', 'test_location', 'mpfm_type',
                    'dhsv', 'sub_category', 'category', 'required_whfp', 'tested_whfp',
                    'expected_oil_rate', 'expected_gas_rate', 'expected_water_cut',
                    'expected_pressure', 'expected_temperature', 'final_status', 'mpfm_test_id'
                ];

                foreach ($columnsToRemove as $column) {
                    if (Schema::hasColumn('plan_tasks', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // Remove enhanced fields from daily_plans
        if (Schema::hasTable('daily_plans')) {
            Schema::table('daily_plans', function (Blueprint $table) {
                $columnsToRemove = ['approved_by', 'approved_at', 'monthly_target_kpi', 'total_tests'];

                foreach ($columnsToRemove as $column) {
                    if (Schema::hasColumn('daily_plans', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
