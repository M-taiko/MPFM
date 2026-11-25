<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellhead_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oil_string_id')->constrained('oil_strings')->cascadeOnDelete();
            $table->date('check_date');
            $table->string('wht', 60)->nullable();            // wellhead tag
            $table->string('well_no', 80)->nullable();
            $table->string('oem', 60)->nullable();
            $table->string('string_no', 10)->default('1');
            $table->string('well_status', 20)->default('online');
            $table->decimal('whcip', 9, 1)->nullable();        // wellhead closed-in pressure, psi
            $table->string('tree_cap', 10)->default('ok');     // ok | not_ok | na
            $table->text('remarks')->nullable();
            $table->string('checked_by')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['oil_string_id', 'check_date']);
        });

        Schema::create('wellhead_check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wellhead_check_id');
            $table->foreign('wellhead_check_id', 'wh_items_check_fk')->references('id')->on('wellhead_checks')->cascadeOnDelete();
            $table->string('item_type', 10);                   // valve | annulus
            $table->string('code', 20);                        // SV, UMV, LMV, WV, A, B, C
            $table->string('condition', 10)->nullable();       // ok | stiff | leaking | seized
            $table->decimal('turns', 6, 1)->nullable();
            $table->unsignedSmallInteger('time_sec')->nullable();
            $table->boolean('greased')->default(false);
            $table->decimal('pressure', 9, 1)->nullable();     // annulus pressure, psi
            $table->string('valve_ref', 20)->nullable();       // e.g. ABCD, BD
            $table->string('dbb_side', 10)->nullable();        // RHS | LHS
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellhead_check_items');
        Schema::dropIfExists('wellhead_checks');
    }
};
