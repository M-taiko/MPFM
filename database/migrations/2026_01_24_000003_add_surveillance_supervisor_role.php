<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create surveillance_supervisor role
        $role = Role::firstOrCreate(['name' => 'surveillance_supervisor']);

        // Create all permissions (both new and existing ones for safety)
        $permissions = [
            // New surveillance-specific permissions
            'classify_task',
            'assign_to_mpfm',
            'view_surveillance_dashboard',
            'review_before_job_officer',
            'send_task_back_to_draft',
            // Existing permissions that surveillance needs
            'view_monthly_plans',
            'view_plan_tasks',
            'view_well_tests',
            'view_well_string_requirements',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Assign all permissions to surveillance_supervisor role
        $role->syncPermissions($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove role
        $role = Role::where('name', 'surveillance_supervisor')->first();
        if ($role) {
            $role->delete();
        }

        // Remove surveillance-specific permissions
        Permission::whereIn('name', [
            'classify_task',
            'assign_to_mpfm',
            'view_surveillance_dashboard',
            'review_before_job_officer',
            'send_task_back_to_draft',
        ])->delete();
    }
};
