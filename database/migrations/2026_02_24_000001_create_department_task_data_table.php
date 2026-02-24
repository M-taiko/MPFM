<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_task_data', function (Blueprint $table) {
            $table->id();

            // Link to both task systems
            $table->foreignId('plan_task_id')->nullable()->constrained('plan_tasks')->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();

            // Department identifier
            $table->string('department_code', 50); // MPFM, WIRELINE, PRODUCTION, etc.

            // All department-specific data stored as JSON
            $table->json('data')->nullable();

            // Completion tracking
            $table->foreignId('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('completion_notes')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('department_code');
            $table->index('plan_task_id');
            $table->index('task_id');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_task_data');
    }
};
