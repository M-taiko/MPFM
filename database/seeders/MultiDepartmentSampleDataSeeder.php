<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use App\Models\Department;
use App\Models\User;
use App\Enums\TaskState;

class MultiDepartmentSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates sample data demonstrating multi-department workflow
     */
    public function run(): void
    {
        $this->command->info('Creating multi-department sample data...');

        // Get departments
        $mpfmDept = Department::where('code', 'MPFM')->first();
        $wirelineDept = Department::where('code', 'WIRELINE')->first();
        $productionDept = Department::where('code', 'PRODUCTION')->first();

        if (!$mpfmDept || !$wirelineDept || !$productionDept) {
            $this->command->error('Required departments not found. Run DepartmentSeeder first.');
            return;
        }

        // Get Job Officer
        $jobOfficer = User::whereHas('roles', function ($query) {
            $query->where('name', 'job_officer');
        })->first();

        if (!$jobOfficer) {
            $this->command->error('Job Officer user not found. Run UserSeeder first.');
            return;
        }

        // Create sample multi-department plan
        $plan = MonthlyPlan::create([
            'plan_name' => 'February 2026 - Multi-Department Operations',
            'plan_month' => 'February 2026',
            'monthly_target_kpi' => 15,
            'notes' => 'Sample plan demonstrating multi-department task assignment and workflow',
            'created_by' => $jobOfficer->id,
            'status' => 'under_review',
        ]);

        $this->command->info("Created plan: {$plan->plan_name}");

        // MPFM Department Tasks (5 tasks)
        $mpfmTasks = [
            [
                'sno' => 1,
                'well_number' => 'MP-01',
                'string_number' => '1',
                'tower' => 'Tower-A',
                'test_location' => 'Locally',
                'electrification_status' => 'Y',
                'mpfm_type' => 'Type A',
                'priority' => 'high',
            ],
            [
                'sno' => 2,
                'well_number' => 'MP-02',
                'string_number' => '1',
                'tower' => 'Tower-A',
                'test_location' => 'Scada',
                'electrification_status' => 'N',
                'mpfm_type' => 'Type B',
                'priority' => 'normal',
            ],
            [
                'sno' => 3,
                'well_number' => 'MP-03',
                'string_number' => '2',
                'tower' => 'Tower-B',
                'test_location' => 'Locally',
                'electrification_status' => 'Y',
                'mpfm_type' => 'Type A',
                'priority' => 'normal',
            ],
            [
                'sno' => 4,
                'well_number' => 'MP-04',
                'string_number' => '1',
                'tower' => 'Tower-B',
                'test_location' => 'Scada',
                'electrification_status' => 'Y',
                'mpfm_type' => 'Type C',
                'priority' => 'normal',
            ],
            [
                'sno' => 5,
                'well_number' => 'MP-05',
                'string_number' => '3',
                'tower' => 'Tower-C',
                'test_location' => 'Locally',
                'electrification_status' => 'N',
                'mpfm_type' => 'Type B',
                'priority' => 'high',
            ],
        ];

        foreach ($mpfmTasks as $taskData) {
            $task = $plan->tasks()->create(array_merge($taskData, [
                'department_id' => $mpfmDept->id,
                'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
                'completion_status' => 'pending',
                'well_full_name' => $taskData['well_number'] . '/' . $taskData['string_number'],
            ]));
            $this->command->info("  Created MPFM task: {$task->well_number}");
        }

        // Wireline Department Tasks (3 tasks)
        $wirelineTasks = [
            [
                'sno' => 6,
                'well_number' => 'WL-01',
                'string_number' => '1',
                'tower' => 'Tower-D',
                'test_location' => 'Locally',
                'electrification_status' => 'Y',
                'mpfm_type' => 'Type A',
                'priority' => 'high',
            ],
            [
                'sno' => 7,
                'well_number' => 'WL-02',
                'string_number' => '2',
                'tower' => 'Tower-D',
                'test_location' => 'Scada',
                'electrification_status' => 'N',
                'mpfm_type' => 'Type B',
                'priority' => 'normal',
            ],
            [
                'sno' => 8,
                'well_number' => 'WL-03',
                'string_number' => '1',
                'tower' => 'Tower-E',
                'test_location' => 'Locally',
                'electrification_status' => 'Y',
                'mpfm_type' => 'Type A',
                'priority' => 'normal',
            ],
        ];

        foreach ($wirelineTasks as $taskData) {
            $task = $plan->tasks()->create(array_merge($taskData, [
                'department_id' => $wirelineDept->id,
                'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
                'completion_status' => 'pending',
                'well_full_name' => $taskData['well_number'] . '/' . $taskData['string_number'],
            ]));
            $this->command->info("  Created Wireline task: {$task->well_number}");
        }

        // Production Department Tasks (2 tasks)
        $productionTasks = [
            [
                'sno' => 9,
                'well_number' => 'PD-01',
                'string_number' => '1',
                'tower' => 'Tower-F',
                'test_location' => 'Scada',
                'electrification_status' => 'Y',
                'mpfm_type' => 'Type C',
                'priority' => 'high',
            ],
            [
                'sno' => 10,
                'well_number' => 'PD-02',
                'string_number' => '2',
                'tower' => 'Tower-F',
                'test_location' => 'Locally',
                'electrification_status' => 'N',
                'mpfm_type' => 'Type B',
                'priority' => 'normal',
            ],
        ];

        foreach ($productionTasks as $taskData) {
            $task = $plan->tasks()->create(array_merge($taskData, [
                'department_id' => $productionDept->id,
                'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
                'completion_status' => 'pending',
                'well_full_name' => $taskData['well_number'] . '/' . $taskData['string_number'],
            ]));
            $this->command->info("  Created Production task: {$task->well_number}");
        }

        // Update plan statistics
        $plan->updateStatistics();

        $this->command->info('Multi-department sample data created successfully!');
        $this->command->info("Total tasks created: {$plan->tasks()->count()}");
        $this->command->info('  - MPFM: 5 tasks');
        $this->command->info('  - Wireline: 3 tasks');
        $this->command->info('  - Production: 2 tasks');
        $this->command->info("\nYou can now:");
        $this->command->info('1. Login as Job Officer to view the plan');
        $this->command->info('2. Login as Surveillance Supervisor to review (read-only)');
        $this->command->info('3. Login as Department Managers to assign operators');
        $this->command->info('4. Login as Operators to execute tasks');
    }
}
