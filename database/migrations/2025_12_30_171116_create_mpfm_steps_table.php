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
        Schema::create('mpfm_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('mpfm_plans')->onDelete('cascade');
            $table->integer('step_number');
            $table->string('name');
            $table->string('choke')->nullable();
            $table->string('time_window')->nullable();
            $table->json('planned_rates')->nullable(); // e.g., oil_rate, gas_rate, water_rate
            $table->text('description')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpfm_steps');
    }
};