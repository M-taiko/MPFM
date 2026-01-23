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
        // Only run this migration if we're using the old plan_tasks schema
        // (Skip if we're using the new monthly_plans system schema)
        if (Schema::hasColumn('plan_tasks', 'assigned_to')) {
            Schema::table('plan_tasks', function (Blueprint $table) {
                // Make assigned_to nullable since tasks don't need to be assigned immediately
                $table->bigInteger('assigned_to')->unsigned()->nullable()->change();

                // Make oil_string_id nullable since it might not exist yet
                if (Schema::hasColumn('plan_tasks', 'oil_string_id')) {
                    $table->bigInteger('oil_string_id')->unsigned()->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->bigInteger('assigned_to')->unsigned()->nullable(false)->change();
            $table->bigInteger('oil_string_id')->unsigned()->nullable(false)->change();
        });
    }
};
