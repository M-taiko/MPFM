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
        Schema::create('mpfm_operational_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('assigned_user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('oil_field_id')->constrained('oil_fields')->onDelete('cascade');
            $table->foreignId('tower_id')->constrained('towers')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('plan_date');
            $table->enum('shift', ['morning', 'afternoon', 'night'])->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->json('assigned_oil_string_ids')->nullable(); // Array of oil_string IDs
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('created_by');
            $table->index('assigned_user_id');
            $table->index('oil_field_id');
            $table->index('tower_id');
            $table->index('plan_date');
            $table->index('status');
            $table->index('shift');
            // Composite indexes for common queries
            $table->index(['assigned_user_id', 'status']);
            $table->index(['plan_date', 'status']);
            $table->index(['oil_field_id', 'tower_id', 'plan_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpfm_operational_plans');
    }
};

