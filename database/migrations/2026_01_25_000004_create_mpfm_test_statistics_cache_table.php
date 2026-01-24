<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpfm_test_statistics_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oil_string_id')->constrained()->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');

            $table->json('statistics')->comment('Cached avg, median, std_dev for all parameters');

            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['oil_string_id', 'period_start', 'period_end'], 'cache_string_period_unique');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpfm_test_statistics_cache');
    }
};
