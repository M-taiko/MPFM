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
        Schema::create('mpfm_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('well_test_id')->constrained('well_tests')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('planned_oil_rate', 10, 2)->nullable();
            $table->decimal('planned_gas_rate', 10, 2)->nullable();
            $table->decimal('planned_water_rate', 10, 2)->nullable();
            $table->decimal('expected_gor', 10, 2)->nullable();
            $table->decimal('expected_bsw', 10, 2)->nullable();
            $table->string('expected_choke')->nullable();
            $table->integer('expected_duration')->nullable(); // in minutes
            $table->text('test_objective')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpfm_plans');
    }
};