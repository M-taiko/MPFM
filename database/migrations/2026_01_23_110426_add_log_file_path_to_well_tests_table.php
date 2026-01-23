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
        Schema::table('well_tests', function (Blueprint $table) {
            if (!Schema::hasColumn('well_tests', 'log_file_path')) {
                $table->string('log_file_path')->nullable()->after('filename');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('well_tests', function (Blueprint $table) {
            if (Schema::hasColumn('well_tests', 'log_file_path')) {
                $table->dropColumn('log_file_path');
            }
        });
    }
};
