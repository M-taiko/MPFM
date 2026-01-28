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
        // Create normalized test results table
        if (!Schema::hasTable('test_results')) {
            Schema::create('test_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mpfm_test_id')->constrained('mpfm_tests')->cascadeOnDelete();
                $table->string('parameter_name'); // e.g., 'GOR', 'Temperature'
                $table->decimal('value', 12, 4); // The measured value
                $table->string('unit')->nullable(); // Unit of measurement
                $table->string('status')->default('recorded'); // recorded, validated, approved
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('mpfm_test_id');
                $table->index('parameter_name');
                $table->index('status');
                $table->index(['mpfm_test_id', 'parameter_name']);
            });
        }

        // Update mpfm_tests table structure if not already done
        if (Schema::hasTable('mpfm_tests') && !Schema::hasColumn('mpfm_tests', 'workflow_config')) {
            Schema::table('mpfm_tests', function (Blueprint $table) {
                $table->json('workflow_config')->nullable()->after('test_duration_minutes');
                $table->boolean('is_active')->default(true)->after('workflow_config');
                $table->string('processing_status')->default('pending')->after('is_active'); // pending, processing, completed, failed
                $table->text('processing_errors')->nullable()->after('processing_status');

                $table->index('processing_status');
                $table->index('is_active');
            });
        }

        // Create test logs table for bulk uploads
        if (!Schema::hasTable('mpfm_test_logs')) {
            Schema::create('mpfm_test_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('well_test_id')->nullable()->constrained('well_tests')->nullOnDelete();
                $table->foreignId('mpfm_test_id')->nullable()->constrained('mpfm_tests')->nullOnDelete();
                $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->string('file_name');
                $table->string('file_path');
                $table->integer('file_size_bytes');
                $table->string('file_hash')->unique(); // For duplicate detection
                $table->json('parsed_data')->nullable();
                $table->string('parsing_status')->default('pending'); // pending, success, failed
                $table->text('parsing_errors')->nullable();
                $table->dateTime('parsed_at')->nullable();
                $table->dateTime('imported_at')->nullable();
                $table->integer('records_count')->default(0);
                $table->integer('records_imported')->default(0);
                $table->integer('records_failed')->default(0);
                $table->timestamps();

                $table->index('file_hash');
                $table->index('parsing_status');
                $table->index('uploaded_by_user_id');
                $table->index('created_at');
                $table->index(['mpfm_test_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('mpfm_test_logs')) {
            Schema::dropIfExists('mpfm_test_logs');
        }

        if (Schema::hasTable('test_results')) {
            Schema::dropIfExists('test_results');
        }

        if (Schema::hasTable('mpfm_tests') && Schema::hasColumn('mpfm_tests', 'workflow_config')) {
            Schema::table('mpfm_tests', function (Blueprint $table) {
                $table->dropColumn([
                    'workflow_config',
                    'is_active',
                    'processing_status',
                    'processing_errors'
                ]);
            });
        }

        Schema::enableForeignKeyConstraints();
    }
};
