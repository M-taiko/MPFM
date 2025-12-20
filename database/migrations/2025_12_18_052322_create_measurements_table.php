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
    Schema::create('measurements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('well_test_id')->constrained()->onDelete('cascade');
        $table->timestamp('datetime');

        // Rates
        $table->double('oil_rate_sc')->nullable();      // qo_sc[STB/d]
        $table->double('water_rate_sc')->nullable();   // qw_sc[STB/d]
        $table->double('gas_rate_sc')->nullable();     // qg_sc[MMScf/d]

        // Ratios
        $table->double('bsw')->nullable();             // BSW[%]
        $table->double('gor')->nullable();             // GOR[Scf/STB]

        // Counts
        $table->integer('n32')->nullable();
        $table->integer('n81')->nullable();
        $table->integer('n356')->nullable();
        $table->integer('ntotal')->nullable();

        // Pressures & Temps (إن وُجدت لاحقًا)
        // $table->double('wh_pressure')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};
