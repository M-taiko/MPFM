<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create department_manager role
        $role = Role::firstOrCreate(['name' => 'department_manager']);

        // Create permissions
        $permissions = [
            'view_department_tasks',
            'assign_operators',
            'view_department_reports',
            'review_department_work',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to department_manager role
        $role->syncPermissions($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $role = Role::where('name', 'department_manager')->first();
        if ($role) {
            $role->syncPermissions([]);
            $role->delete();
        }

        // Optionally delete permissions if not used elsewhere
        $permissions = [
            'view_department_tasks',
            'assign_operators',
            'view_department_reports',
            'review_department_work',
        ];

        foreach ($permissions as $permission) {
            $perm = Permission::where('name', $permission)->first();
            if ($perm && $perm->roles()->count() === 0) {
                $perm->delete();
            }
        }
    }
};
