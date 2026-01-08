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
        Schema::create('mpfm_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step_id')->constrained('mpfm_steps')->onDelete('cascade');
            $table->decimal('oil_rate', 10, 2)->nullable();
            $table->decimal('gas_rate', 10, 2)->nullable();
            $table->decimal('water_rate', 10, 2)->nullable();
            $table->decimal('wlr', 10, 2)->nullable(); // Water Liquid Ratio
            $table->decimal('bsw', 10, 2)->nullable(); // Basic Sediment and Water
            $table->decimal('pressure', 10, 2)->nullable();
            $table->decimal('temperature', 10, 2)->nullable();
            $table->timestamp('reading_time')->nullable();
            $table->text('notes')->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpfm_readings');
    }
};