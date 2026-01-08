<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates ADNOC-grade users with proper roles
     */
    public function run(): void
    {
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'donia.a5ra2019@gmail.com'],
            [
                'name' => 'Mohamed Tarek',
                'password' => Hash::make('123456789'),
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // MPFM Supervisor
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@adnoc.ae'],
            [
                'name' => 'MPFM Supervisor',
                'password' => Hash::make('password'),
            ]
        );
        if (!$supervisor->hasRole('mpfm_supervisor')) {
            $supervisor->assignRole('mpfm_supervisor');
        }

        // MPFM Operators
        $operators = [
            ['name' => 'Operator 1', 'email' => 'operator1@adnoc.ae'],
            ['name' => 'Operator 2', 'email' => 'operator2@adnoc.ae'],
            ['name' => 'Operator 3', 'email' => 'operator3@adnoc.ae'],
        ];

        foreach ($operators as $operatorData) {
            $operator = User::firstOrCreate(
                ['email' => $operatorData['email']],
                [
                    'name' => $operatorData['name'],
                    'password' => Hash::make('password'),
                ]
            );
            if (!$operator->hasRole('mpfm_operator')) {
                $operator->assignRole('mpfm_operator');
            }
        }

        // Production Supervisor
        $prodSupervisor = User::firstOrCreate(
            ['email' => 'prod.supervisor@adnoc.ae'],
            [
                'name' => 'Production Supervisor',
                'password' => Hash::make('password'),
            ]
        );
        if (!$prodSupervisor->hasRole('production_supervisor')) {
            $prodSupervisor->assignRole('production_supervisor');
        }

        // Reservoir Engineer
        $reservoirEngineer = User::firstOrCreate(
            ['email' => 'reservoir.engineer@adnoc.ae'],
            [
                'name' => 'Reservoir Engineer',
                'password' => Hash::make('password'),
            ]
        );
        if (!$reservoirEngineer->hasRole('reservoir_engineer')) {
            $reservoirEngineer->assignRole('reservoir_engineer');
        }

        // Job Officer
        $jobOfficer = User::firstOrCreate(
            ['email' => 'job.officer@adnoc.ae'],
            [
                'name' => 'Job Officer',
                'password' => Hash::make('password'),
            ]
        );
        if (!$jobOfficer->hasRole('job_officer')) {
            $jobOfficer->assignRole('job_officer');
        }

        $this->command->info('Users seeded successfully!');
    }
}

