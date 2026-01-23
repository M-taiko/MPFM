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
            if (!Schema::hasColumn('mpfm_tests', 'well_test_id')) {
                $table->foreignId('well_test_id')
                      ->nullable()
                      ->after('plan_task_id')
                      ->constrained('well_tests')
                      ->onDelete('set null')
                      ->comment('Reference to the well test record from operator log uploads');

                // Add index for performance
                $table->index('well_test_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            if (Schema::hasColumn('mpfm_tests', 'well_test_id')) {
                $table->dropForeign(['well_test_id']);
                $table->dropIndex(['well_test_id']);
                $table->dropColumn('well_test_id');
            }
        });
    }
};
