<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            $table->enum('validation_status', ['pass', 'warning', 'fail'])->nullable()->after('status');
            $table->unsignedTinyInteger('validation_score')->nullable();
            $table->enum('validation_suggestion', ['approve', 'review', 'reject'])->nullable();
            $table->text('validation_summary')->nullable();
            $table->timestamp('last_validated_at')->nullable();

            $table->index('validation_status');
            $table->index('validation_suggestion');
        });
    }

    public function down(): void
    {
        Schema::table('mpfm_tests', function (Blueprint $table) {
            $table->dropIndex(['validation_status']);
            $table->dropIndex(['validation_suggestion']);

            $table->dropColumn([
                'validation_status',
                'validation_score',
                'validation_suggestion',
                'validation_summary',
                'last_validated_at'
            ]);
        });
    }
};
