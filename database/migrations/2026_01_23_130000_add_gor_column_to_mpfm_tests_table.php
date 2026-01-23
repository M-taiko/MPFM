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
            if (!Schema::hasColumn('mpfm_tests', 'gor')) {
                $table->decimal('gor', 10, 2)->nullable()->after('temperature');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            if (Schema::hasColumn('mpfm_tests', 'gor')) {
                $table->dropColumn('gor');
            }
        });
    }
};
