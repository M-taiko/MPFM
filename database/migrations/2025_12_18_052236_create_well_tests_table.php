<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('well_tests', function (Blueprint $table) {
        $table->id();
        $table->string('client');
        $table->string('field');
        $table->string('well');
        $table->string('wellhead_tower')->nullable();
        $table->string('wellhead_string')->nullable();
        $table->string('choke_size')->nullable();
        $table->string('upstream_choke')->nullable();
        $table->string('downstream_choke')->nullable();
        $table->dateTime('start_time')->nullable();
        $table->dateTime('end_time')->nullable();
        $table->json('metadata')->nullable();
        $table->json('test_data')->nullable();
        $table->json('calculated_metrics')->nullable();
        $table->string('filename');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('well_tests');
    }
};
