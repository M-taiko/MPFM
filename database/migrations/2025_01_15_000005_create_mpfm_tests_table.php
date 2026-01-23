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
        Schema::create('mpfm_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('oil_field_id')->constrained('oil_fields')->onDelete('restrict');
            $table->foreignId('tower_id')->constrained('towers')->onDelete('restrict');
            $table->foreignId('oil_string_id')->constrained('oil_strings')->onDelete('restrict');
            $table->foreignId('mpfm_operational_plan_id')->nullable()->constrained('mpfm_operational_plans')->onDelete('set null');

            $table->date('test_date');
            $table->enum('shift', ['morning', 'afternoon', 'night']);

            // Production data
            $table->decimal('oil_rate', 10, 2)->nullable();
            $table->decimal('gas_rate', 10, 2)->nullable();
            $table->decimal('water_cut', 5, 2)->nullable(); // Percentage
            $table->decimal('pressure', 10, 2)->nullable();
            $table->decimal('temperature', 8, 2)->nullable();

            // Calculated KPIs (stored as JSON, calculated via Service)
            $table->json('calculated_kpis')->nullable();

            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');

            // Log file storage (for log-driven tests)

            $table->json('raw_log_data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_id');
            $table->index('oil_field_id');
            $table->index('tower_id');
            $table->index('oil_string_id');
            $table->index('mpfm_operational_plan_id');
            $table->index('test_date');
            $table->index('shift');
            $table->index('status');
            // Composite indexes for common queries
            $table->index(['user_id', 'test_date']);
            $table->index(['test_date', 'status']);
            $table->index(['oil_field_id', 'tower_id', 'oil_string_id']);
            $table->index(['oil_field_id', 'test_date']);
            $table->index(['tower_id', 'test_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpfm_tests');
    }
};

