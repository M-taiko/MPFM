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
        Schema::table('mpfm_tests', function (Blueprint $table) {
            // Approval status fields
            if (!Schema::hasColumn('mpfm_tests', 'department_supervisor_id')) {
                $table->unsignedBigInteger('department_supervisor_id')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('mpfm_tests', 'surveillance_supervisor_id')) {
                $table->unsignedBigInteger('surveillance_supervisor_id')->nullable()->after('department_supervisor_id');
            }

            if (!Schema::hasColumn('mpfm_tests', 'approved_by_department_at')) {
                $table->timestamp('approved_by_department_at')->nullable()->after('test_date');
            }

            if (!Schema::hasColumn('mpfm_tests', 'approved_by_surveillance_at')) {
                $table->timestamp('approved_by_surveillance_at')->nullable()->after('approved_by_department_at');
            }

            // Rejection fields
            if (!Schema::hasColumn('mpfm_tests', 'rejected_by_user_id')) {
                $table->unsignedBigInteger('rejected_by_user_id')->nullable()->after('surveillance_supervisor_id');
            }

            if (!Schema::hasColumn('mpfm_tests', 'rejected_by_role')) {
                $table->string('rejected_by_role')->nullable()->comment('department_supervisor, surveillance_supervisor')->after('rejected_by_user_id');
            }

            if (!Schema::hasColumn('mpfm_tests', 'rejected_reason')) {
                $table->string('rejected_reason')->nullable()->comment('invalid_data, equipment_failure, test_interrupted, etc.')->after('rejected_by_role');
            }

            if (!Schema::hasColumn('mpfm_tests', 'rejected_details')) {
                $table->longText('rejected_details')->nullable()->after('rejected_reason');
            }

            if (!Schema::hasColumn('mpfm_tests', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_details');
            }

            // Re-test request fields
            if (!Schema::hasColumn('mpfm_tests', 'retest_requested_by_user_id')) {
                $table->unsignedBigInteger('retest_requested_by_user_id')->nullable()->after('rejected_by_user_id');
            }

            if (!Schema::hasColumn('mpfm_tests', 'retest_reason')) {
                $table->string('retest_reason')->nullable()->comment('data_inconsistency, incomplete_test, equipment_suspect, etc.')->after('retest_requested_by_user_id');
            }

            if (!Schema::hasColumn('mpfm_tests', 'retest_details')) {
                $table->longText('retest_details')->nullable()->after('retest_reason');
            }

            if (!Schema::hasColumn('mpfm_tests', 'retest_requested_at')) {
                $table->timestamp('retest_requested_at')->nullable()->after('retest_details');
            }

            // Supervisor remarks field
            if (!Schema::hasColumn('mpfm_tests', 'supervisor_remarks')) {
                $table->longText('supervisor_remarks')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            $columns = [
                'department_supervisor_id',
                'surveillance_supervisor_id',
                'approved_by_department_at',
                'approved_by_surveillance_at',
                'rejected_by_user_id',
                'rejected_by_role',
                'rejected_reason',
                'rejected_details',
                'rejected_at',
                'retest_requested_by_user_id',
                'retest_reason',
                'retest_details',
                'retest_requested_at',
                'supervisor_remarks',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('mpfm_tests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
