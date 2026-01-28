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
        if (!Schema::hasTable('test_templates')) {
            Schema::create('test_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->string('name'); // Template name
                $table->text('description')->nullable();
                $table->string('template_type'); // mpfm_test, well_test, custom
                $table->json('required_fields'); // Fields that must be present
                $table->json('optional_fields')->nullable(); // Optional fields
                $table->json('validation_rules')->nullable(); // Field validation rules
                $table->json('calculations')->nullable(); // Auto-calculations to perform
                $table->integer('version')->default(1);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false); // Default template for department
                $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index('department_id');
                $table->index('template_type');
                $table->index('is_active');
                $table->index('is_default');
                $table->unique(['department_id', 'name', 'version']);
            });
        }

        if (!Schema::hasTable('test_template_versions')) {
            Schema::create('test_template_versions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('test_template_id')->constrained('test_templates')->cascadeOnDelete();
                $table->integer('version_number');
                $table->json('template_data');
                $table->text('change_notes')->nullable();
                $table->foreignId('changed_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->index('test_template_id');
                $table->unique(['test_template_id', 'version_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('test_template_versions')) {
            Schema::dropIfExists('test_template_versions');
        }

        if (Schema::hasTable('test_templates')) {
            Schema::dropIfExists('test_templates');
        }

        Schema::enableForeignKeyConstraints();
    }
};
