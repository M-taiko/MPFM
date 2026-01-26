<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Execution order is critical:
     * 1. Departments (must be early - before users)
     * 2. Roles & Permissions (must be early)
     * 3. Users (depends on departments and roles)
     * 4. Core Assets (oil fields, towers, oil strings)
     * 5. Operational Plans (depends on users and assets)
     * 6. MPFM Tests (depends on all above)
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        // Step 1: Departments (must be before users)
        $this->command->info('Step 1/5: Seeding departments...');
        $this->call(DepartmentSeeder::class);

        // Step 2: Roles & Permissions (must be early)
        $this->command->info('Step 2/5: Seeding roles and permissions...');
        $this->call(RolePermissionSeeder::class);

        // Step 3: Users (depends on departments and roles)
        $this->command->info('Step 3/5: Seeding users...');
        $this->call(UserSeeder::class);

        // Step 4: Core Assets (oil fields, towers, oil strings)
        $this->command->info('Step 4/5: Seeding oil fields, towers, and oil strings...');
        $this->call(OilFieldSeeder::class);

        // Step 5: Operational Plans & MPFM Tests (depends on all above)
        $this->command->info('Step 5/5: Seeding operational plans and MPFM tests...');
        $this->call(MPFMSampleDataSeeder::class);

        $this->command->info('Database seeding completed successfully!');
    }
}
