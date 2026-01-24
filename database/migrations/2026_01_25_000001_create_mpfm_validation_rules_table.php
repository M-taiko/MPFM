<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpfm_validation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');
            $table->enum('rule_type', ['range', 'percentage_change', 'trend', 'anomaly']);
            $table->string('parameter')->comment('oil_rate, water_cut, GOR, bhp, bht, etc.');

            // Hierarchical scoping (most specific wins)
            $table->foreignId('oil_field_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tower_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('oil_string_id')->nullable()->constrained()->onDelete('cascade');

            // Rule configuration (JSON)
            $table->json('thresholds')->comment('Flexible threshold config based on rule_type');

            // Metadata
            $table->enum('severity', ['critical', 'major', 'minor'])->default('major');
            $table->enum('auto_action', ['block', 'warn', 'info'])->default('warn');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index('oil_field_id');
            $table->index('tower_id');
            $table->index('oil_string_id');
            $table->index('parameter');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpfm_validation_rules');
    }
};
