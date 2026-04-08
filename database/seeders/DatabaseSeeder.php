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
        $this->command->info('Step 1/3: Seeding departments...');
        $this->call(DepartmentSeeder::class);

        // Step 2: Roles & Permissions
        $this->command->info('Step 2/3: Seeding roles and permissions...');
        $this->call(RolePermissionSeeder::class);

        // Step 3: Users (depends on departments and roles)
        $this->command->info('Step 3/3: Seeding users...');
        $this->call(UserSeeder::class);

        $this->command->info('Database seeding completed successfully!');
    }
}
