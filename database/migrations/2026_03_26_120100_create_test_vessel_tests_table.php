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
        Schema::create('test_vessel_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_vessel_id')->constrained('test_vessels')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('oil_field_id')->constrained('oil_fields')->restrictOnDelete();
            $table->foreignId('tower_id')->constrained('towers')->restrictOnDelete();
            $table->foreignId('oil_string_id')->constrained('oil_strings')->restrictOnDelete();
            $table->foreignId('mpfm_test_id')->nullable()->constrained('mpfm_tests')->onDelete('set null');

            $table->date('test_date');
            $table->enum('shift', ['morning', 'afternoon', 'night']);

            // Measurements
            $table->decimal('oil_rate', 10, 2);
            $table->decimal('gas_rate', 10, 2);
            $table->decimal('water_cut', 5, 2);
            $table->decimal('pressure', 8, 2);
            $table->decimal('temperature', 6, 2);
            $table->decimal('gor', 10, 2)->nullable();

            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('test_vessel_id');
            $table->index('oil_field_id');
            $table->index('tower_id');
            $table->index('oil_string_id');
            $table->index('mpfm_test_id');
            $table->index('user_id');
            $table->index('test_date');
            $table->index(['test_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_vessel_tests');
    }
};
