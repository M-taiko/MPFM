<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Domains\MPFM\Models\DepartmentTestType;
use App\Domains\MPFM\Models\DepartmentWorkflow;

class ComprehensiveDepartmentJobsSeeder extends Seeder
{
    public function run()
    {
        $jobs = $this->departmentsJobs();

        foreach ($jobs as $deptName => $jobsList) {
            $dept = Department::where('name', $deptName)->first();

            if (!$dept) {
                echo "Department not found: $deptName\n";
                continue;
            }

            echo "\n📋 Seeding $deptName...\n";

            // Create test types (jobs) for this department
            foreach ($jobsList as $job) {
                DepartmentTestType::updateOrCreate(
                    [
                        'department_id' => $dept->id,
                        'name' => $job['name']
                    ],
                    [
                        'description' => $job['description'] ?? '',
                        'required_fields' => $job['required_fields'] ?? [],
                        'validation_rules' => $job['validation_rules'] ?? [],
                        'estimated_duration_minutes' => $job['duration'] ?? 60,
                        'is_active' => true,
                        'created_by_user_id' => 1
                    ]
                );

                echo "  ✅ " . $job['name'] . "\n";
            }

            // Create workflow for department
            if (!empty($jobsList[0]['workflow'])) {
                $workflow = $jobsList[0]['workflow'];

                DepartmentWorkflow::updateOrCreate(
                    ['department_id' => $dept->id],
                    [
                        'states' => $workflow['states'] ?? [],
                        'transitions' => $workflow['transitions'] ?? [],
                        'sla_rules' => [],
                        'notifications' => [],
                        'custom_fields' => $workflow['custom_fields'] ?? [],
                        'is_active' => true,
                        'created_by_user_id' => 1
                    ]
                );

                echo "  ⚙️  Workflow created\n";
            }
        }

        echo "\n✅ All departments seeded successfully!\n";
    }

    private function departmentsJobs(): array
    {
        return [

            /** ======================
             *  MPFM Operations
             *  ====================== */
            'MPFM Operations' => [
                [
                    'name' => 'MPFM Test',
                    'category' => 'Test',
                    'description' => 'Multiphase flow meter test - Measure oil, gas, and water flow rates',
                    'required_fields' => ['pressure', 'temperature', 'flow_rate', 'bore_size'],
                    'validation_rules' => [
                        'pressure' => 'numeric|min:0',
                        'temperature' => 'numeric|between:-40,150',
                        'flow_rate' => 'numeric|min:0',
                        'bore_size' => 'required|in:2.5,3.5,4.5,5.5'
                    ],
                    'duration' => 120,
                    'workflow' => [
                        'states' => ['draft', 'assigned', 'in_progress', 'completed'],
                        'transitions' => [
                            'draft' => ['assigned'],
                            'assigned' => ['in_progress', 'draft'],
                            'in_progress' => ['completed'],
                            'completed' => []
                        ],
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
                                'placeholder' => 'Enter duration in hours'
                            ],
                            'comments' => [
                                'type' => 'textarea',
                                'label' => 'Technical Comments',
                                'placeholder' => 'Enter observations during test',
                                'rows' => 3
                            ]
                        ]
                    ]
                ]
            ],

            /** ======================
             *  Wire Line Services
             *  ====================== */
            'Wire Line Services' => [
                [
                    'name' => 'Fishing Operation',
                    'category' => 'Intervention',
                    'description' => 'Recover stuck tools or debris from well',
                    'required_fields' => ['fish_type', 'fish_depth', 'tool_string'],
                    'validation_rules' => [
                        'fish_type' => 'required|string',
                        'fish_depth' => 'numeric|min:0',
                        'tool_string' => 'required|string'
                    ],
                    'duration' => 360,
                    'workflow' => [
                        'states' => ['planned', 'rig_up', 'fishing', 'recovered', 'closed'],
                        'transitions' => [
                            'planned' => ['rig_up'],
                            'rig_up' => ['fishing', 'planned'],
                            'fishing' => ['recovered', 'closed'],
                            'recovered' => ['closed'],
                            'closed' => []
                        ],
                        'custom_fields' => [
                            'fish_type' => [
                                'type' => 'select',
                                'label' => 'Fish Type',
                                'options' => ['Tubing', 'Drill Pipe', 'Fish Joint', 'Debris'],
                                'placeholder' => 'Select fish type'
                            ],
                            'tool_string' => [
                                'type' => 'textarea',
                                'label' => 'Tool String Description',
                                'placeholder' => 'Describe the tools being used',
                                'rows' => 3
                            ],
                            'recovery_status' => [
                                'type' => 'select',
                                'label' => 'Recovery Status',
                                'options' => ['In Progress', 'Partial', 'Complete', 'Failed'],
                                'placeholder' => 'Current recovery status'
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Open Hole Logging',
                    'category' => 'Logging',
                    'description' => 'Conduct open hole logging to measure formation properties',
                    'required_fields' => ['log_type', 'max_depth', 'logging_speed'],
                    'validation_rules' => [
                        'log_type' => 'required|string',
                        'max_depth' => 'numeric|min:0',
                        'logging_speed' => 'numeric|min:0'
                    ],
                    'duration' => 240,
                    'workflow' => [
                        'states' => ['planned', 'logging', 'quality_check', 'approved'],
                        'transitions' => [
                            'planned' => ['logging'],
                            'logging' => ['quality_check'],
                            'quality_check' => ['approved', 'logging'],
                            'approved' => []
                        ],
                        'custom_fields' => [
                            'log_type' => [
                                'type' => 'select',
                                'label' => 'Log Type',
                                'options' => ['Gamma Ray', 'Resistivity', 'Sonic', 'Density', 'Porosity'],
                                'placeholder' => 'Select log type'
                            ],
                            'depth_interval' => [
                                'type' => 'text',
                                'label' => 'Depth Interval (m)',
                                'placeholder' => 'e.g., 1000-2500'
                            ]
                        ]
                    ]
                ]
            ],

            /** ======================
             *  Production Operations
             *  ====================== */
            'Production Operations' => [
                [
                    'name' => 'Daily Production Report',
                    'category' => 'Production',
                    'description' => 'Daily well production data collection and reporting',
                    'required_fields' => ['oil_rate', 'water_cut', 'gas_rate', 'choke_size'],
                    'validation_rules' => [
                        'oil_rate' => 'numeric|min:0',
                        'water_cut' => 'numeric|between:0,100',
                        'gas_rate' => 'numeric|min:0',
                        'choke_size' => 'required|string'
                    ],
                    'duration' => 60,
                    'workflow' => [
                        'states' => ['reported', 'reviewed', 'approved'],
                        'transitions' => [
                            'reported' => ['reviewed', 'reported'],
                            'reviewed' => ['approved', 'reported'],
                            'approved' => []
                        ],
                        'custom_fields' => [
                            'choke_size' => [
                                'type' => 'select',
                                'label' => 'Choke Size',
                                'options' => ['16/64', '24/64', '32/64', '40/64'],
                                'placeholder' => 'Select choke size'
                            ],
                            'well_status' => [
                                'type' => 'select',
                                'label' => 'Well Status',
                                'options' => ['Producing', 'Idle', 'Maintenance', 'Testing'],
                                'placeholder' => 'Current well status'
                            ],
                            'remarks' => [
                                'type' => 'textarea',
                                'label' => 'Daily Remarks',
                                'placeholder' => 'Any issues or observations',
                                'rows' => 2
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'Well Maintenance',
                    'category' => 'Maintenance',
                    'description' => 'Perform routine well maintenance operations',
                    'required_fields' => ['maintenance_type', 'duration_days'],
                    'validation_rules' => [
                        'maintenance_type' => 'required|string',
                        'duration_days' => 'numeric|min:1'
                    ],
                    'duration' => 480,
                    'workflow' => [
                        'states' => ['planned', 'in_progress', 'inspection', 'completed'],
                        'transitions' => [
                            'planned' => ['in_progress'],
                            'in_progress' => ['inspection'],
                            'inspection' => ['completed', 'in_progress'],
                            'completed' => []
                        ],
                        'custom_fields' => [
                            'maintenance_type' => [
                                'type' => 'select',
                                'label' => 'Maintenance Type',
                                'options' => ['Pipeline Cleaning', 'Valve Service', 'Corrosion Inspection', 'Seal Replacement'],
                                'placeholder' => 'Select maintenance type'
                            ],
                            'work_description' => [
                                'type' => 'textarea',
                                'label' => 'Work Description',
                                'placeholder' => 'Detailed description of work performed',
                                'rows' => 3
                            ]
                        ]
                    ]
                ]
            ],

            /** ======================
             *  Drilling Operations
             *  ====================== */
            'Drilling Operations' => [
                [
                    'name' => 'Drilling Progress Report',
                    'category' => 'Drilling',
                    'description' => 'Daily drilling progress and operations report',
                    'required_fields' => ['depth_drilled', 'drilling_rate', 'mud_weight'],
                    'validation_rules' => [
                        'depth_drilled' => 'numeric|min:0',
                        'drilling_rate' => 'numeric|min:0',
                        'mud_weight' => 'numeric|min:0'
                    ],
                    'duration' => 120,
                    'workflow' => [
                        'states' => ['daily_report', 'supervisor_review', 'approved'],
                        'transitions' => [
                            'daily_report' => ['supervisor_review'],
                            'supervisor_review' => ['approved', 'daily_report'],
                            'approved' => []
                        ],
                        'custom_fields' => [
                            'well_section' => [
                                'type' => 'select',
                                'label' => 'Well Section',
                                'options' => ['Surface', 'Intermediate', 'Production'],
                                'placeholder' => 'Current well section'
                            ],
                            'drilling_problems' => [
                                'type' => 'textarea',
                                'label' => 'Drilling Problems/Issues',
                                'placeholder' => 'Any issues encountered',
                                'rows' => 2
                            ]
                        ]
                    ]
                ]
            ],

            /** ======================
             *  Completion Operations
             *  ====================== */
            'Completion Operations' => [
                [
                    'name' => 'Well Completion',
                    'category' => 'Completion',
                    'description' => 'Well completion activities and equipment installation',
                    'required_fields' => ['completion_type', 'tubing_size'],
                    'validation_rules' => [
                        'completion_type' => 'required|string',
                        'tubing_size' => 'required|string'
                    ],
                    'duration' => 360,
                    'workflow' => [
                        'states' => ['design', 'procurement', 'installation', 'testing', 'completed'],
                        'transitions' => [
                            'design' => ['procurement'],
                            'procurement' => ['installation'],
                            'installation' => ['testing'],
                            'testing' => ['completed'],
                            'completed' => []
                        ],
                        'custom_fields' => [
                            'completion_type' => [
                                'type' => 'select',
                                'label' => 'Completion Type',
                                'options' => ['Open Hole', 'Cased Hole', 'Monobore', 'Smart Completion'],
                                'placeholder' => 'Select completion type'
                            ],
                            'equipment_list' => [
                                'type' => 'textarea',
                                'label' => 'Equipment List',
                                'placeholder' => 'List all equipment to be installed',
                                'rows' => 3
                            ]
                        ]
                    ]
                ]
            ],

            /** ======================
             *  Workover Operations
             *  ====================== */
            'Workover Operations' => [
                [
                    'name' => 'Well Workover',
                    'category' => 'Workover',
                    'description' => 'Well workover operations for remedial work',
                    'required_fields' => ['workover_type', 'objective'],
                    'validation_rules' => [
                        'workover_type' => 'required|string',
                        'objective' => 'required|string'
                    ],
                    'duration' => 480,
                    'workflow' => [
                        'states' => ['planning', 'rig_setup', 'workover', 'testing', 'cleanup'],
                        'transitions' => [
                            'planning' => ['rig_setup'],
                            'rig_setup' => ['workover'],
                            'workover' => ['testing'],
                            'testing' => ['cleanup'],
                            'cleanup' => []
                        ],
                        'custom_fields' => [
                            'workover_type' => [
                                'type' => 'select',
                                'label' => 'Workover Type',
                                'options' => ['Intervention', 'Remedial', 'Recompletion', 'Abandonment'],
                                'placeholder' => 'Select workover type'
                            ],
                            'expected_outcome' => [
                                'type' => 'textarea',
                                'label' => 'Expected Outcome',
                                'placeholder' => 'Describe expected results',
                                'rows' => 2
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
