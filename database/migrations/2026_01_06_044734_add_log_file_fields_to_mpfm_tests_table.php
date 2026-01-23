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
        Schema::table('mpfm_tests', function (Blueprint $table) {
            // Log file storage (for log-driven tests)
            // Only add log_file_path - raw_log_data already exists in original create migration
            if (!Schema::hasColumn('mpfm_tests', 'log_file_path')) {
                $table->string('log_file_path')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            // Only drop log_file_path - raw_log_data should remain as it's part of original schema
            if (Schema::hasColumn('mpfm_tests', 'log_file_path')) {
                $table->dropColumn('log_file_path');
            }
        });
    }
};
