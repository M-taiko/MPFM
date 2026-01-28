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
        if (!Schema::hasTable('department_workflows')) {
            Schema::create('department_workflows', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->json('states'); // Custom states per department
                $table->json('transitions'); // Custom transitions
                $table->json('sla_rules'); // SLA config per state
                $table->json('notifications')->nullable(); // Notification settings
                $table->json('custom_fields')->nullable(); // Custom fields
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index('department_id');
                $table->index('is_active');
                $table->unique('department_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('department_workflows')) {
            Schema::dropIfExists('department_workflows');
        }
        Schema::enableForeignKeyConstraints();
    }
};
