<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Execution order:
     * 1. Departments (must be before users)
     * 2. Roles & Permissions (must be early)
     * 3. Users (depends on departments and roles)
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        // Step 1: Departments (must be before users)
        $this->command->info('Step 1/5: Seeding departments...');
        $this->call(DepartmentSeeder::class);

        // Step 2: Roles & Permissions
        $this->command->info('Step 2/5: Seeding roles and permissions...');
        $this->call(RolePermissionSeeder::class);

        // Step 3: Users (depends on departments and roles)
        $this->command->info('Step 3/5: Seeding users...');
        $this->call(UserSeeder::class);

        // Step 4: demo data for presentations (fields, wells, tests, archive, Wire Line, integrity) + the admin demo login
        $this->command->info('Step 4/5: Seeding demo data...');
        $this->call(DemoDataSeeder::class);

        // Step 5: full-access user without user management
        $this->command->info('Step 5/5: Seeding full-access user...');
        $this->call(FullAccessUserSeeder::class);

        $this->command->info('Database seeding completed successfully!');
    }
}
