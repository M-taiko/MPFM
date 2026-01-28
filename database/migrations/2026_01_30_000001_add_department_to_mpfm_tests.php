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
        if (Schema::hasTable('mpfm_tests') && !Schema::hasColumn('mpfm_tests', 'department_id')) {
            Schema::table('mpfm_tests', function (Blueprint $table) {
                // Add department_id after well_test_id
                $table->foreignId('department_id')
                    ->nullable()
                    ->after('well_test_id')
                    ->constrained('departments')
                    ->nullOnDelete();

                $table->index('department_id');
                $table->index(['department_id', 'created_at']);
            });

            // Assign all existing MPFM tests to MPFM department (id = 1)
            // Assuming MPFM is the first department created in Phase 2
            \DB::table('mpfm_tests')
                ->whereNull('department_id')
                ->update(['department_id' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mpfm_tests') && Schema::hasColumn('mpfm_tests', 'department_id')) {
            Schema::table('mpfm_tests', function (Blueprint $table) {
                $table->dropForeignIdFor('departments');
                $table->dropIndex(['department_id']);
                $table->dropIndex(['department_id', 'created_at']);
                $table->dropColumn('department_id');
            });
        }
    }
};
