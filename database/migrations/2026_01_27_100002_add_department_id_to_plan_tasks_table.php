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
        // Only add department_id column if it doesn't already exist (idempotent)
        if (!Schema::hasColumn('plan_tasks', 'department_id')) {
            Schema::table('plan_tasks', function (Blueprint $table) {
                $table->foreignId('department_id')
                    ->nullable()
                    ->after('monthly_plan_id')
                    ->constrained('departments')
                    ->nullOnDelete()
                    ->comment('Which department this task is assigned to');

                $table->index('department_id');
                $table->index(['department_id', 'completion_status']);
                $table->index(['department_id', 'state']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
