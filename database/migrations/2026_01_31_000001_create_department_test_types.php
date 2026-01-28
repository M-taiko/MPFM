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
        if (!Schema::hasTable('department_test_types')) {
            Schema::create('department_test_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->json('required_fields');
                $table->json('validation_rules');
                $table->integer('estimated_duration_minutes')->default(60);
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index('department_id');
                $table->index('is_active');
                $table->unique(['department_id', 'name']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('department_test_types')) {
            Schema::dropIfExists('department_test_types');
        }
        Schema::enableForeignKeyConstraints();
    }
};
