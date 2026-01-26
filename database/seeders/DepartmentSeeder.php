<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'code' => 'MPFM',
                'name' => 'MPFM Operations',
                'description' => 'Multi-Phase Flow Meter Testing Department',
                'display_order' => 1,
            ],
            [
                'code' => 'WIRELINE',
                'name' => 'Wire Line Services',
                'description' => 'Wire Line Testing and Logging Services',
                'display_order' => 2,
            ],
            [
                'code' => 'PRODUCTION',
                'name' => 'Production Operations',
                'description' => 'Production Monitoring and Management',
                'display_order' => 3,
            ],
            [
                'code' => 'DRILLING',
                'name' => 'Drilling Services',
                'description' => 'Drilling Operations and Management',
                'display_order' => 4,
            ],
            [
                'code' => 'COMPLETION',
                'name' => 'Well Completion',
                'description' => 'Well Completion and Perforation Services',
                'display_order' => 5,
            ],
            [
                'code' => 'WORKOVER',
                'name' => 'Workover Services',
                'description' => 'Workover and Intervention Services',
                'display_order' => 6,
            ],
            [
                'code' => 'MAINTENANCE',
                'name' => 'Equipment Maintenance',
                'description' => 'Equipment Maintenance and Support',
                'display_order' => 7,
            ],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['code' => $department['code']],
                $department
            );
        }
    }
}
