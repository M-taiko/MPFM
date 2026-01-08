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
            $table->string('operator')->nullable();
            $table->date('test_date')->nullable();
            $table->string('test_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('well_tests', function (Blueprint $table) {
            //
        });
    }
};
