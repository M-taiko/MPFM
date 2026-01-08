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
        Schema::create('plan_task_operators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_task_id')->constrained('plan_tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('assigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['plan_task_id', 'user_id']);

            // Index for queries
            $table->index('user_id');
            $table->index('plan_task_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_task_operators');
    }
};
