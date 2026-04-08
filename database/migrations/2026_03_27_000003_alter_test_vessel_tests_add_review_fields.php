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
        Schema::table('test_vessel_tests', function (Blueprint $table) {
            $table->foreignId('tv_test_request_well_id')->nullable()->constrained('tv_test_request_wells')->setNullOnDelete();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->setNullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_vessel_tests', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['tv_test_request_well_id']);
            $table->dropForeignKeyIfExists(['reviewed_by_user_id']);
            $table->dropColumn(['tv_test_request_well_id', 'reviewed_by_user_id', 'review_notes', 'reviewed_at']);
        });
    }
};
