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
        if (!Schema::hasTable('department_sla_rules')) {
            Schema::create('department_sla_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
                $table->string('state'); // Task state name
                $table->integer('warning_minutes')->default(60); // Minutes before warning
                $table->integer('critical_minutes')->default(120); // Minutes before critical
                $table->integer('escalation_level')->default(1); // 1 = notify manager, 2 = notify supervisor, 3 = notify admin
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('department_id');
                $table->index('state');
                $table->unique(['department_id', 'state']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('department_sla_rules')) {
            Schema::dropIfExists('department_sla_rules');
        }
        Schema::enableForeignKeyConstraints();
    }
};
