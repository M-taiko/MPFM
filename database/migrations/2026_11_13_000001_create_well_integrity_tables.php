<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One integrity profile (operating envelope) per well
        Schema::create('well_integrity_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oil_string_id')->unique()->constrained('oil_strings')->cascadeOnDelete();
            $table->string('well_type', 30)->default('producer');
            $table->string('lift_method', 30)->default('natural_flow');
            $table->string('tubing_size', 40)->nullable();
            $table->string('casing_size', 60)->nullable();
            $table->decimal('mawhp', 9, 1)->nullable();            // psi - max allowable wellhead pressure
            $table->decimal('max_oil_rate', 10, 1)->nullable();    // B/D - integrity-imposed rate limit
            $table->string('restriction_note')->nullable();
            $table->unsignedSmallInteger('review_interval_days')->default(365);
            $table->date('last_review_date')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('well_integrity_annuli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oil_string_id')->constrained('oil_strings')->cascadeOnDelete();
            $table->string('name', 10);                            // A, B, C, D
            $table->decimal('maasp', 9, 1)->nullable();            // psi
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['oil_string_id', 'name']);
        });

        Schema::create('well_integrity_annulus_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('well_integrity_annulus_id');
            $table->foreign('well_integrity_annulus_id', 'wi_ann_read_annulus_fk')->references('id')->on('well_integrity_annuli')->cascadeOnDelete();
            $table->foreignId('oil_string_id')->constrained('oil_strings')->cascadeOnDelete();
            $table->date('reading_date');
            $table->decimal('pressure', 9, 1);                     // psi
            $table->decimal('bleed_down_pressure', 9, 1)->nullable();
            $table->decimal('rebuild_pressure', 9, 1)->nullable();
            $table->decimal('rebuild_hours', 7, 1)->nullable();
            $table->string('fluid', 20)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['well_integrity_annulus_id', 'reading_date'], 'wi_ann_read_idx');
        });

        Schema::create('well_barrier_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oil_string_id')->constrained('oil_strings')->cascadeOnDelete();
            $table->string('envelope', 12);                        // primary | secondary
            $table->string('element_type', 30);
            $table->string('name');
            $table->string('status', 12)->default('untested');     // untested | healthy | degraded | failed
            $table->unsignedSmallInteger('test_interval_days')->default(180);
            $table->date('last_test_date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['oil_string_id', 'envelope']);
        });

        Schema::create('well_integrity_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('well_barrier_element_id');
            $table->foreign('well_barrier_element_id', 'wi_tests_element_fk')->references('id')->on('well_barrier_elements')->cascadeOnDelete();
            $table->foreignId('oil_string_id')->constrained('oil_strings')->cascadeOnDelete();
            $table->date('test_date');
            $table->string('test_type', 20)->default('pressure');  // function | pressure | leak_rate | inflow
            $table->string('result', 10);                          // pass | marginal | fail
            $table->decimal('test_pressure', 9, 1)->nullable();
            $table->decimal('leak_rate', 10, 2)->nullable();
            $table->unsignedSmallInteger('duration_min')->nullable();
            $table->string('tested_by')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['oil_string_id', 'test_date']);
        });

        Schema::create('well_integrity_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oil_string_id')->constrained('oil_strings')->cascadeOnDelete();
            $table->string('inspection_type', 20);
            $table->date('inspection_date');
            $table->decimal('depth_from', 9, 1)->nullable();
            $table->decimal('depth_to', 9, 1)->nullable();
            $table->decimal('max_wall_loss_pct', 5, 1)->nullable();
            $table->string('status', 20)->default('acceptable');   // acceptable | monitor | action_required
            $table->date('next_due_date')->nullable();
            $table->text('finding')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['oil_string_id', 'inspection_date']);
        });

        Schema::create('well_integrity_anomalies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oil_string_id')->constrained('oil_strings')->cascadeOnDelete();
            $table->string('title');
            $table->string('category', 30)->default('other');
            $table->unsignedTinyInteger('likelihood')->default(3);
            $table->unsignedTinyInteger('consequence')->default(3);
            $table->unsignedTinyInteger('risk_score')->default(9);
            $table->string('status', 12)->default('open');         // open | mitigated | closed
            $table->date('identified_date');
            $table->date('due_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->text('description')->nullable();
            $table->text('mitigation')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['oil_string_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('well_integrity_anomalies');
        Schema::dropIfExists('well_integrity_inspections');
        Schema::dropIfExists('well_integrity_tests');
        Schema::dropIfExists('well_barrier_elements');
        Schema::dropIfExists('well_integrity_annulus_readings');
        Schema::dropIfExists('well_integrity_annuli');
        Schema::dropIfExists('well_integrity_profiles');
    }
};
