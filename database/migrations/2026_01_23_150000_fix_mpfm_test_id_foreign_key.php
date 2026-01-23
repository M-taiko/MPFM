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
            // Drop the old foreign key constraint that references well_tests
            $table->dropForeign(['mpfm_test_id']);

            // Add the correct foreign key that references mpfm_tests
            $table->foreign('mpfm_test_id')
                  ->references('id')
                  ->on('mpfm_tests')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['mpfm_test_id']);

            // Restore the old foreign key if needed
            $table->foreign('mpfm_test_id')
                  ->references('id')
                  ->on('well_tests')
                  ->onDelete('set null');
        });
    }
};
