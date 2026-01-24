<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpfm_test_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mpfm_test_id')->constrained()->onDelete('cascade');
            $table->timestamp('validated_at');

            // Overall validation results
            $table->enum('overall_status', ['pass', 'warning', 'fail']);
            $table->unsignedTinyInteger('confidence_score')->comment('0-100');
            $table->enum('auto_suggestion', ['approve', 'review', 'reject']);

            // Detailed results (JSON arrays)
            $table->json('rule_results')->comment('Array of individual rule execution results');
            $table->json('historical_comparison')->nullable()->comment('Historical statistics and deviations');
            $table->json('anomalies')->nullable()->comment('Detected anomalies');

            $table->timestamps();

            // Indexes
            $table->index('mpfm_test_id');
            $table->index('overall_status');
            $table->index('auto_suggestion');
            $table->index('validated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpfm_test_validations');
    }
};
