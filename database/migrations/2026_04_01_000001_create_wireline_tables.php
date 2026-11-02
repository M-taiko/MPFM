<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wireline_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oil_string_id')->constrained('oil_strings')->cascadeOnDelete();
            $table->string('operation_type', 40);
            $table->unsignedSmallInteger('run_number')->default(1);
            $table->date('operation_date');
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('completed');
            $table->string('tool_string')->nullable();
            $table->decimal('depth_from', 9, 2)->nullable();   // m
            $table->decimal('depth_to', 9, 2)->nullable();     // m
            $table->string('crew_leader')->nullable();
            $table->text('objective')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['oil_string_id', 'operation_date']);
            $table->index('operation_type');
            $table->index('status');
        });

        Schema::create('wireline_gauge_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wireline_operation_id')->constrained('wireline_operations')->cascadeOnDelete();
            $table->string('survey_type', 30)->default('bhcip');
            $table->string('gauge_serial')->nullable();
            $table->string('gauge_model')->nullable();
            $table->string('gauge_manufacturer')->nullable();
            $table->date('calibration_date')->nullable();
            $table->decimal('max_range', 10, 1)->nullable();
            $table->string('pressure_unit', 10)->default('psig');
            $table->string('temperature_unit', 10)->default('degC');
            $table->unsignedInteger('sample_interval_sec')->default(15);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->unsignedInteger('sample_count')->default(0);
            $table->string('source_file')->nullable();
            $table->string('file_path')->nullable();
            $table->json('analysis')->nullable();   // stats, phases, events, stops, quality
            $table->timestamps();

            $table->index('started_at');
        });

        Schema::create('wireline_gauge_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wireline_gauge_run_id')->constrained('wireline_gauge_runs')->cascadeOnDelete();
            $table->unsignedInteger('t_sec');                 // seconds since run start
            $table->float('pressure');
            $table->float('temperature')->nullable();
            $table->float('internal_temp')->nullable();

            $table->index(['wireline_gauge_run_id', 't_sec']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wireline_gauge_readings');
        Schema::dropIfExists('wireline_gauge_runs');
        Schema::dropIfExists('wireline_operations');
    }
};
