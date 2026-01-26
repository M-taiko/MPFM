<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates ADNOC-grade users with proper roles and department assignments
     */
    public function run(): void
    {
        // Get the MPFM department (should exist from DepartmentSeeder)
        $mpfmDept = Department::where('code', 'MPFM')->first();

        // Admin User (no department - global admin)
        $admin = User::firstOrCreate(
            ['email' => 'donia.a5ra2019@gmail.com'],
            [
                'name' => 'Mohamed Tarek',
                'password' => Hash::make('123456789'),
                'department_id' => null, // Admin has no specific department
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // MPFM Supervisor (migrate to Department Manager role, assigned to MPFM dept)
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@adnoc.ae'],
            [
                'name' => 'MPFM Supervisor',
                'password' => Hash::make('password'),
                'department_id' => $mpfmDept?->id,
            ]
        );
        // Update to new department_manager role if was mpfm_supervisor
        if ($supervisor->hasRole('mpfm_supervisor')) {
            $supervisor->removeRole('mpfm_supervisor');
        }
        if (!$supervisor->hasRole('department_manager')) {
            $supervisor->assignRole('department_manager');
        }

        // MPFM Operators (assigned to MPFM department)
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
                    'department_id' => $mpfmDept?->id,
                ]
            );
            if (!$operator->hasRole('mpfm_operator') && !$operator->hasRole('operator')) {
                $operator->assignRole('mpfm_operator');
            }
        }

        // Production Supervisor (no specific department - global role)
        $prodSupervisor = User::firstOrCreate(
            ['email' => 'prod.supervisor@adnoc.ae'],
            [
                'name' => 'Production Supervisor',
                'password' => Hash::make('password'),
                'department_id' => null,
            ]
        );
        if (!$prodSupervisor->hasRole('production_supervisor')) {
            $prodSupervisor->assignRole('production_supervisor');
        }

        // Reservoir Engineer (no specific department - global role)
        $reservoirEngineer = User::firstOrCreate(
            ['email' => 'reservoir.engineer@adnoc.ae'],
            [
                'name' => 'Reservoir Engineer',
                'password' => Hash::make('password'),
                'department_id' => null,
            ]
        );
        if (!$reservoirEngineer->hasRole('reservoir_engineer')) {
            $reservoirEngineer->assignRole('reservoir_engineer');
        }

        // Job Officer (no specific department - global role)
        $jobOfficer = User::firstOrCreate(
            ['email' => 'job.officer@adnoc.ae'],
            [
                'name' => 'Job Officer',
                'password' => Hash::make('password'),
                'department_id' => null,
            ]
        );
        if (!$jobOfficer->hasRole('job_officer')) {
            $jobOfficer->assignRole('job_officer');
        }

        // Surveillance Supervisor (no specific department - global monitoring)
        $surveillanceSupervisor = User::firstOrCreate(
            ['email' => 'surveillance@adnoc.ae'],
            [
                'name' => 'Surveillance Supervisor',
                'password' => Hash::make('password'),
                'department_id' => null, // Global monitoring role
            ]
        );
        if (!$surveillanceSupervisor->hasRole('surveillance_supervisor')) {
            $surveillanceSupervisor->assignRole('surveillance_supervisor');
        }

        $this->command->info('Users seeded successfully with department assignments!');
    }
}

