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
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $jobOfficerRole = Role::firstOrCreate(['name' => 'job_officer']);
        $reservoirEngineerRole = Role::firstOrCreate(['name' => 'reservoir_engineer']);
        $productionSupervisorRole = Role::firstOrCreate(['name' => 'production_supervisor']);
        $mpfmSupervisorRole = Role::firstOrCreate(['name' => 'mpfm_supervisor']);
        $mpfmOperatorRole = Role::firstOrCreate(['name' => 'mpfm_operator']);

        // Admin gets all permissions
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
        ]);

        // MPFM Operator permissions
        $mpfmOperatorRole->syncPermissions([
            'view_well_tests',
            'view_mpfm_operator_reports',
        ]);
    }
}
