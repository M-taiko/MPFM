<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create permissions if they don't exist
        $permissions = [
            // User Management Permissions
            'manage_users',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Role Management Permissions
            'manage_roles',
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',

            // Permission Management Permissions
            'manage_permissions',
            'view_permissions',
            'create_permissions',
            'edit_permissions',
            'delete_permissions',

            // Well Test Permissions
            'view_well_tests',
            'create_well_tests',
            'edit_well_tests',
            'delete_well_tests',

            // Report Permissions
            'view_job_officer_reports',
            'view_reservoir_engineer_reports',
            'view_production_supervisor_reports',
            'view_mpfm_supervisor_reports',
            'view_mpfm_operator_reports',

            // Well String Requirements Permissions
            'view_well_string_requirements',

            // Multi-Department Workflow Permissions
            'view_department_tasks',
            'assign_operators',
            'review_department_work',
            'view_all_departments',

            // Test Vessel Permissions
            'manage_test_vessels',
            'view_test_vessels',
            'create_tv_tests',
            'compare_tv_tests',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $jobOfficerRole = Role::firstOrCreate(['name' => 'job_officer']);
        $reservoirEngineerRole = Role::firstOrCreate(['name' => 'reservoir_engineer']);
        $productionSupervisorRole = Role::firstOrCreate(['name' => 'production_supervisor']);
        $surveillanceSupervisorRole = Role::firstOrCreate(['name' => 'surveillance_supervisor']);
        $mpfmSupervisorRole = Role::firstOrCreate(['name' => 'mpfm_supervisor']);
        $mpfmOperatorRole = Role::firstOrCreate(['name' => 'mpfm_operator']);
        $operatorRole = Role::firstOrCreate(['name' => 'operator']); // Backward compatibility fallback
        $departmentManagerRole = Role::firstOrCreate(['name' => 'department_manager']); // NEW: Multi-department manager
        $departmentSupervisorRole = Role::firstOrCreate(['name' => 'department_supervisor']); // NEW: Department supervisor for user management

        // Admin gets ALL permissions - full system access
        // Admins have unrestricted access to:
        // - All user management operations
        // - All role and permission management
        // - All MPFM tests and operational plans
        // - All department and workflow management
        // - All reports and analytics
        // - All system configuration
        $adminRole->givePermissionTo(Permission::all());

        // Job Officer permissions
        $jobOfficerRole->syncPermissions([
            'view_well_tests',
            'view_job_officer_reports',
        ]);

        // Reservoir Engineer permissions
        $reservoirEngineerRole->syncPermissions([
            'view_well_tests',
            'view_reservoir_engineer_reports',
            'view_well_string_requirements',
        ]);

        // Production Supervisor permissions
        $productionSupervisorRole->syncPermissions([
            'view_well_tests',
            'view_production_supervisor_reports',
            'view_well_string_requirements',
        ]);

        // MPFM Supervisor permissions
        $mpfmSupervisorRole->syncPermissions([
            'view_well_tests',
            'view_mpfm_supervisor_reports',
            'view_well_string_requirements',
        ]);

        // Job Officer permissions
        $jobOfficerRole->syncPermissions([
            'view_well_tests',
            'view_job_officer_reports',
            'view_well_string_requirements',
            'view_test_vessels',
            'compare_tv_tests',
        ]);

        // Surveillance Supervisor permissions
        $surveillanceSupervisorRole->syncPermissions([
            'view_well_tests',
            'view_well_string_requirements',
        ]);

        // MPFM Operator permissions
        $mpfmOperatorRole->syncPermissions([
            'view_well_tests',
            'view_mpfm_operator_reports',
            'view_test_vessels',
            'create_tv_tests',
        ]);

        // Operator permissions (backward compatibility)
        $operatorRole->syncPermissions([
            'view_well_tests',
            'view_mpfm_operator_reports',
        ]);

        // Department Manager permissions (NEW: Multi-department support)
        $departmentManagerRole->syncPermissions([
            'view_department_tasks',
            'assign_operators',
            'review_department_work',
            'view_well_tests',
            'view_test_vessels',
            'create_tv_tests',
        ]);

        // Department Supervisor permissions (NEW: Manage users within department)
        $departmentSupervisorRole->syncPermissions([
            'manage_users',           // Can create, edit, delete users
            'view_users',             // Can view users
            'view_department_tasks',  // Can see department tasks
            'view_well_tests',        // Can view MPFM tests
        ]);

        // Update Surveillance Supervisor - ADD read-only permission, REMOVE assignment
        $surveillanceSupervisorRole->syncPermissions([
            'view_well_tests',
            'view_well_string_requirements',
            'view_all_departments', // Can see all departments (read-only)
            'view_test_vessels',
            'create_tv_tests',
            'compare_tv_tests',
        ]);
    }
}
