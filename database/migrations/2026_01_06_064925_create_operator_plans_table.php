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
        Schema::create('operator_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('well_test_id')->constrained('well_tests')->onDelete('cascade');
            $table->integer('step_number');
            $table->string('step_type')->default('instruction'); // instruction, measurement, action, etc.
            $table->text('step_description');
            $table->json('step_parameters')->nullable(); // Additional parameters for the step
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->string('completed_by')->nullable(); // User who completed the step
            $table->json('step_results')->nullable(); // Results of the step execution
            $table->integer('order')->default(0); // Order for reordering steps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operator_plans');
    }
};
