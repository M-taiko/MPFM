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
        Schema::create('tv_test_request_wells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_test_request_id')->constrained('tv_test_requests')->cascadeOnDelete();
            $table->foreignId('oil_string_id')->constrained('oil_strings')->restrictOnDelete();
            $table->foreignId('test_vessel_test_id')->nullable()->constrained('test_vessel_tests')->setNullOnDelete();
            $table->foreignId('started_by_user_id')->nullable()->constrained('users')->setNullOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamps();

            $table->index('tv_test_request_id');
            $table->index('oil_string_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_test_request_wells');
    }
};
