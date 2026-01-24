<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpfm_test_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('current_test_id')->constrained('mpfm_tests')->onDelete('cascade');
            $table->foreignId('oil_string_id')->constrained()->onDelete('cascade');

            $table->json('compared_test_ids')->comment('Array of historical test IDs used');
            $table->json('comparison_data')->comment('Detailed comparison results');
            $table->json('alerts')->nullable()->comment('Any alerts generated');

            $table->timestamps();

            $table->index('current_test_id');
            $table->index('oil_string_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpfm_test_comparisons');
    }
};
