<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hierarchy: Location (onshore / offshore / ...) > Oil field > Tower (wellhead tower, platform, ...) > Well string.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropIndex(['environment', 'site_type']);
            $table->dropConstrainedForeignId('oil_field_id');
            $table->dropColumn('site_type');
            $table->index('environment');
        });

        Schema::table('oil_fields', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('code')->constrained('locations')->nullOnDelete();
        });

        Schema::table('towers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->string('tower_type', 30)->default('wellhead_tower')->after('code');
        });

        Schema::table('oil_strings', function (Blueprint $table) {
            $table->unsignedSmallInteger('string_number')->nullable()->after('code');
        });
    }

    public function down(): void
    {
        Schema::table('oil_strings', function (Blueprint $table) {
            $table->dropColumn('string_number');
        });
        Schema::table('towers', function (Blueprint $table) {
            $table->dropColumn('tower_type');
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
        });
        Schema::table('oil_fields', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });
        Schema::table('locations', function (Blueprint $table) {
            $table->dropIndex(['environment']);
            $table->string('site_type', 30)->default('wellhead_tower');
            $table->foreignId('oil_field_id')->nullable()->constrained('oil_fields')->nullOnDelete();
        });
    }
};
