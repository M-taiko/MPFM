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
            $table->string('log_file_path')->nullable()->after('status');
            $table->json('raw_log_data')->nullable()->after('log_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            $table->dropColumn(['log_file_path', 'raw_log_data']);
        });
    }
};
