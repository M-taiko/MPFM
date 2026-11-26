<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 60);
            $table->string('environment', 10);                  // onshore | offshore
            $table->string('site_type', 30);                    // wellhead_tower, desert, platform, ...
            $table->foreignId('oil_field_id')->nullable()->constrained('oil_fields')->nullOnDelete();
            $table->string('status', 12)->default('active');    // active | maintenance | inactive
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->decimal('water_depth_m', 7, 1)->nullable(); // offshore only
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['environment', 'site_type']);
            $table->index('status');
            $table->index('code');
        });

        Schema::table('towers', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('oil_field_id')->constrained('locations')->nullOnDelete();
        });
        Schema::table('test_vessels', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('oil_field_id')->constrained('locations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('test_vessels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });
        Schema::table('towers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
        });
        Schema::dropIfExists('locations');
    }
};
