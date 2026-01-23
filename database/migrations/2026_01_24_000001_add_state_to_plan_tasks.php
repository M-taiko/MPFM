<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TaskState;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            // Add state column for workflow management
            // Keep existing completion_status for backward compatibility
            $table->string('state')->default(TaskState::DRAFT->value)->after('completion_status');

            // Add surveillance supervisor tracking
            $table->unsignedBigInteger('surveillance_supervisor_id')->nullable()->after('assigned_by');
            $table->timestamp('surveillance_assigned_at')->nullable()->after('surveillance_supervisor_id');

            // Add indexes for performance on state-based queries
            $table->index('state');
            $table->index(['state', 'scheduled_date']);
            $table->index(['monthly_plan_id', 'state']);

            // Foreign key for surveillance supervisor
            $table->foreign('surveillance_supervisor_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_tasks', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['surveillance_supervisor_id']);

            // Drop indexes
            $table->dropIndex(['monthly_plan_id', 'state']);
            $table->dropIndex(['state', 'scheduled_date']);
            $table->dropIndex(['state']);

            // Drop columns
            $table->dropColumn([
                'state',
                'surveillance_supervisor_id',
                'surveillance_assigned_at'
            ]);
        });
    }
};
