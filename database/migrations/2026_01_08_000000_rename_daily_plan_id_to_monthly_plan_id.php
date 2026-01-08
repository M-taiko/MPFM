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
        // First, drop the foreign key constraint if it exists
        Schema::table('plan_tasks', function (Blueprint $table) {
            // Drop foreign key if it exists (check common naming conventions)
            try {
                $table->dropForeign(['daily_plan_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist or have different name
            }
        });

        // Rename the column
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->renameColumn('daily_plan_id', 'monthly_plan_id');
        });

        // Add the foreign key back with the new column name
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->foreign('monthly_plan_id')
                  ->references('id')
                  ->on('monthly_plans')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropForeign(['monthly_plan_id']);
        });

        // Rename back to daily_plan_id
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->renameColumn('monthly_plan_id', 'daily_plan_id');
        });

        // Restore the old foreign key
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->foreign('daily_plan_id')
                  ->references('id')
                  ->on('daily_plans')
                  ->onDelete('cascade');
        });
    }
};
