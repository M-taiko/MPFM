<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Execution order is critical:
     * 1. Roles & Permissions (must be first)
     * 2. Users (depends on roles)
     * 3. Core Assets (oil fields, towers, oil strings)
     * 4. Operational Plans (depends on users and assets)
     * 5. MPFM Tests (depends on all above)
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        // Step 1: Roles & Permissions (must be first)
        $this->command->info('Step 1/4: Seeding roles and permissions...');
        $this->call(RolePermissionSeeder::class);

        // Step 2: Users (depends on roles)
        $this->command->info('Step 2/4: Seeding users...');
        $this->call(UserSeeder::class);

        // Step 3: Core Assets (oil fields, towers, oil strings)
        $this->command->info('Step 3/4: Seeding oil fields, towers, and oil strings...');
        $this->call(OilFieldSeeder::class);

        // Step 4: Operational Plans & MPFM Tests (depends on all above)
        $this->command->info('Step 4/4: Seeding operational plans and MPFM tests...');
        $this->call(MPFMSampleDataSeeder::class);

        $this->command->info('Database seeding completed successfully!');
    }
}
