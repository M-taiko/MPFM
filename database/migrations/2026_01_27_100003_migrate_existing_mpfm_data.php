<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create MPFM department
        $mpfmDept = DB::table('departments')->insertGetId([
            'code' => 'MPFM',
            'name' => 'MPFM Operations',
            'description' => 'Multi-Phase Flow Meter Testing Department',
            'is_active' => true,
            'display_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign all mpfm_supervisor and mpfm_operator users to MPFM department
        DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['mpfm_supervisor', 'mpfm_operator'])
            ->update(['users.department_id' => $mpfmDept]);

        // Assign all existing plan_tasks to MPFM department
        DB::table('plan_tasks')->update(['department_id' => $mpfmDept]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete all department assignments
        DB::table('plan_tasks')->update(['department_id' => null]);
        DB::table('users')->update(['department_id' => null]);

        // Delete MPFM department
        DB::table('departments')->where('code', 'MPFM')->delete();
    }
};
