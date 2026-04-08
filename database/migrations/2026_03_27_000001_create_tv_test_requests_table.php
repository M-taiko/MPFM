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
        Schema::create('tv_test_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_vessel_id')->constrained('test_vessels')->restrictOnDelete();
            $table->foreignId('tower_id')->constrained('towers')->restrictOnDelete();
            $table->foreignId('oil_field_id')->nullable()->constrained('oil_fields')->setNullOnDelete();
            $table->foreignId('requested_by_user_id')->constrained('users')->restrictOnDelete();
            $table->date('test_date');
            $table->enum('shift', ['morning', 'afternoon', 'night']);
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'in_progress', 'completed', 'cancelled'])->default('open');
            $table->timestamps();
            $table->softDeletes();

            $table->index('test_vessel_id');
            $table->index('oil_field_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_test_requests');
    }
};
