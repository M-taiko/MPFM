<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Domains\MPFM\Models\DepartmentTestType;
use App\Domains\MPFM\Models\DepartmentWorkflow;

class DepartmentTestDataSeeder extends Seeder
{
    public function run()
    {
        $dept = Department::first();

        if (!$dept) {
            echo "No department found\n";
            return;
        }

        // Create test type
        DepartmentTestType::updateOrCreate(
            [
                'department_id' => $dept->id,
                'name' => 'MPFM Standard Test'
            ],
            [
                'description' => 'Standard multiphase flow meter testing procedure',
                'required_fields' => ['pressure', 'temperature', 'flow_rate'],
                'validation_rules' => [
                    'pressure' => 'numeric|min:0',
                    'temperature' => 'numeric|between:-40,150',
                    'flow_rate' => 'numeric|min:0'
                ],
                'estimated_duration_minutes' => 120,
                'is_active' => true,
                'created_by_user_id' => 1
            ]
        );

        echo "✅ Test Type created\n";

        // Create workflow
        DepartmentWorkflow::updateOrCreate(
            [
                'department_id' => $dept->id
            ],
            [
                'states' => ['draft', 'assigned', 'in_progress', 'completed'],
                'transitions' => [
                    'draft' => ['assigned'],
                    'assigned' => ['in_progress', 'draft'],
                    'in_progress' => ['completed'],
                    'completed' => []
                ],
                'sla_rules' => [],
                'notifications' => [],
                'custom_fields' => [
                    'bore_size' => [
                        'type' => 'select',
                        'label' => 'Bore Size (inches)',
                        'options' => ['2.5', '3.5', '4.5', '5.5'],
                        'placeholder' => 'Select bore size'
                    ],
                    'test_duration' => [
                        'type' => 'text',
                        'label' => 'Test Duration (hours)',
                        'placeholder' => 'Enter duration'
                    ],
                    'comments' => [
                        'type' => 'textarea',
                        'label' => 'Additional Comments',
                        'placeholder' => 'Enter special notes',
                        'rows' => 3
                    ]
                ],
                'is_active' => true,
                'created_by_user_id' => 1
            ]
        );

        echo "✅ Workflow created with custom fields\n";
    }
}
