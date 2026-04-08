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
     * Creates system users with proper roles and department assignments.
     */
    public function run(): void
    {
        // Get the MPFM department (should exist from DepartmentSeeder)
        $mpfmDept = Department::where('code', 'MPFM')->first();

        // Admin User (no department - global admin)
        $admin = User::firstOrCreate(
            ['email' => 'admin@oilfield.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'department_id' => null,
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Department Manager — assigned to MPFM dept
        $supervisor = User::firstOrCreate(
            ['email' => 'manager@oilfield.com'],
            [
                'name' => 'MPFM Manager',
                'password' => Hash::make('password'),
                'department_id' => $mpfmDept?->id,
            ]
        );
        if ($supervisor->hasRole('mpfm_supervisor')) {
            $supervisor->removeRole('mpfm_supervisor');
        }
        if (!$supervisor->hasRole('department_manager')) {
            $supervisor->assignRole('department_manager');
        }

        // MPFM Operators (assigned to MPFM department)
        $operators = [
            ['name' => 'Operator One',   'email' => 'operator1@oilfield.com'],
            ['name' => 'Operator Two',   'email' => 'operator2@oilfield.com'],
            ['name' => 'Operator Three', 'email' => 'operator3@oilfield.com'],
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

        // Production Supervisor (global role)
        $prodSupervisor = User::firstOrCreate(
            ['email' => 'production.supervisor@oilfield.com'],
            [
                'name' => 'Production Supervisor',
                'password' => Hash::make('password'),
                'department_id' => null,
            ]
        );
        if (!$prodSupervisor->hasRole('production_supervisor')) {
            $prodSupervisor->assignRole('production_supervisor');
        }

        // Reservoir Engineer (global role)
        $reservoirEngineer = User::firstOrCreate(
            ['email' => 'reservoir.engineer@oilfield.com'],
            [
                'name' => 'Reservoir Engineer',
                'password' => Hash::make('password'),
                'department_id' => null,
            ]
        );
        if (!$reservoirEngineer->hasRole('reservoir_engineer')) {
            $reservoirEngineer->assignRole('reservoir_engineer');
        }

        // Job Officer (global role)
        $jobOfficer = User::firstOrCreate(
            ['email' => 'job.officer@oilfield.com'],
            [
                'name' => 'Job Officer',
                'password' => Hash::make('password'),
                'department_id' => null,
            ]
        );
        if (!$jobOfficer->hasRole('job_officer')) {
            $jobOfficer->assignRole('job_officer');
        }

        // Surveillance Supervisor (global monitoring)
        $surveillanceSupervisor = User::firstOrCreate(
            ['email' => 'surveillance@oilfield.com'],
            [
                'name' => 'Surveillance Supervisor',
                'password' => Hash::make('password'),
                'department_id' => null,
            ]
        );
        if (!$surveillanceSupervisor->hasRole('surveillance_supervisor')) {
            $surveillanceSupervisor->assignRole('surveillance_supervisor');
        }

        $this->command->info('Users seeded successfully!');
    }
}
