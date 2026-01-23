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
            if (!Schema::hasColumn('mpfm_tests', 'test_duration_minutes')) {
                $table->integer('test_duration_minutes')->nullable()->after('log_file_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            if (Schema::hasColumn('mpfm_tests', 'test_duration_minutes')) {
                $table->dropColumn('test_duration_minutes');
            }
        });
    }
};
