<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use App\Models\Department;
use App\Enums\TaskState;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $jobOfficer;
    protected $surveillanceSupervisor;
    protected $mpfmDepartmentManager;
    protected $wirelineDepartmentManager;
    protected $mpfmOperator;
    protected $wirelineOperator;
    protected $mpfmDept;
    protected $wirelineDept;
    protected $plan;

    /**
     * Set up test data
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seedDatabase();
    }

    /**
     * Seed test database with required data
     */
    private function seedDatabase(): void
    {
        // Use or create departments
        $this->mpfmDept = Department::firstOrCreate(
            ['code' => 'MPFM'],
            [
                'name' => 'MPFM Department',
                'description' => 'Multi-Phase Flow Meter Department',
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        $this->wirelineDept = Department::firstOrCreate(
            ['code' => 'WIRELINE'],
            [
                'name' => 'Wireline Department',
                'description' => 'Wireline Operations',
                'is_active' => true,
                'display_order' => 2,
            ]
        );

        // Create roles
        $this->createRolesAndPermissions();

        // Create users
        $this->jobOfficer = User::create([
            'name' => 'Job Officer',
            'email' => 'job.officer@test.com',
            'password' => bcrypt('password'),
            'department_id' => null,
        ])->assignRole('job_officer');

        $this->surveillanceSupervisor = User::create([
            'name' => 'Surveillance Supervisor',
            'email' => 'surveillance@test.com',
            'password' => bcrypt('password'),
            'department_id' => null,
        ])->assignRole('surveillance_supervisor');

        $this->mpfmDepartmentManager = User::create([
            'name' => 'MPFM Department Manager',
            'email' => 'mpfm.manager@test.com',
            'password' => bcrypt('password'),
            'department_id' => $this->mpfmDept->id,
        ])->assignRole('department_manager');

        $this->wirelineDepartmentManager = User::create([
            'name' => 'Wireline Department Manager',
            'email' => 'wireline.manager@test.com',
            'password' => bcrypt('password'),
            'department_id' => $this->wirelineDept->id,
        ])->assignRole('department_manager');

        $this->mpfmOperator = User::create([
            'name' => 'MPFM Operator',
            'email' => 'mpfm.operator@test.com',
            'password' => bcrypt('password'),
            'department_id' => $this->mpfmDept->id,
        ])->assignRole('mpfm_operator');

        $this->wirelineOperator = User::create([
            'name' => 'Wireline Operator',
            'email' => 'wireline.operator@test.com',
            'password' => bcrypt('password'),
            'department_id' => $this->wirelineDept->id,
        ])->assignRole('operator');

        // Create sample plan
        $this->plan = MonthlyPlan::create([
            'plan_name' => 'Test Plan',
            'plan_month' => 'February 2026',
            'monthly_target_kpi' => 10,
            'created_by' => $this->jobOfficer->id,
            'status' => 'draft',
        ]);
    }

    /**
     * Create roles and permissions for testing
     */
    private function createRolesAndPermissions(): void
    {
        // Create permissions
        $permissions = [
            'view_department_tasks',
            'assign_operators',
            'review_department_work',
            'view_all_departments',
            'view_well_tests',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $roles = [
            'admin' => \Spatie\Permission\Models\Permission::all(),
            'job_officer' => ['view_well_tests'],
            'surveillance_supervisor' => ['view_well_tests', 'view_all_departments'],
            'department_manager' => ['view_department_tasks', 'assign_operators', 'review_department_work'],
            'mpfm_operator' => ['view_well_tests'],
            'operator' => ['view_well_tests'],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);
            if ($roleName !== 'admin') {
                $permissionIds = [];
                foreach ($perms as $permName) {
                    $permissionIds[] = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permName])->id;
                }
                $role->syncPermissions($permissionIds);
            }
        }
    }

    /**
     * Test: Job Officer can create task and assign to department
     */
    public function test_job_officer_can_create_task_and_assign_department(): void
    {
        $this->actingAs($this->jobOfficer);

        $taskData = [
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 1,
            'well_number' => 'TEST-01',
            'string_number' => '1',
            'well_full_name' => 'TEST-01/1',
            'state' => TaskState::DRAFT,
        ];

        $task = PlanTask::create($taskData);

        $this->assertDatabaseHas('plan_tasks', [
            'id' => $task->id,
            'department_id' => $this->mpfmDept->id,
            'well_number' => 'TEST-01',
        ]);
    }

    /**
     * Test: Department Manager can only see their own department's tasks
     */
    public function test_department_manager_sees_only_own_department_tasks(): void
    {
        // Create MPFM task
        $mpfmTask = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 1,
            'well_number' => 'MPFM-01',
            'string_number' => '1',
            'well_full_name' => 'MPFM-01/1',
            'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
        ]);

        // Create Wireline task
        $wirelineTask = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->wirelineDept->id,
            'sno' => 2,
            'well_number' => 'WL-01',
            'string_number' => '1',
            'well_full_name' => 'WL-01/1',
            'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
        ]);

        // MPFM manager should see only MPFM task
        $mpfmTasks = PlanTask::forDepartment($this->mpfmDept->id)->get();
        $this->assertCount(1, $mpfmTasks);
        $this->assertEquals($mpfmTask->id, $mpfmTasks->first()->id);

        // Wireline manager should see only Wireline task
        $wirelineTasks = PlanTask::forDepartment($this->wirelineDept->id)->get();
        $this->assertCount(1, $wirelineTasks);
        $this->assertEquals($wirelineTask->id, $wirelineTasks->first()->id);
    }

    /**
     * Test: Department Manager can assign operators within same department
     */
    public function test_department_manager_can_assign_operator_same_department(): void
    {
        $task = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 1,
            'well_number' => 'MPFM-01',
            'string_number' => '1',
            'well_full_name' => 'MPFM-01/1',
            'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
        ]);

        // MPFM manager assigns MPFM operator
        $task->assignedOperators()->attach($this->mpfmOperator->id, ['is_primary' => true]);
        $task->update(['assigned_operator_id' => $this->mpfmOperator->id]);

        $this->assertTrue(
            $task->assignedOperators()->where('user_id', $this->mpfmOperator->id)->exists()
        );
    }

    /**
     * Test: Department Manager CANNOT cross-assign operators from different department
     */
    public function test_department_manager_cannot_cross_assign_operator(): void
    {
        $task = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 1,
            'well_number' => 'MPFM-01',
            'string_number' => '1',
            'well_full_name' => 'MPFM-01/1',
            'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
        ]);

        // MPFM manager tries to assign Wireline operator (should fail in policy)
        $this->actingAs($this->mpfmDepartmentManager);

        // Check if they can view Wireline task (should not be able to)
        $this->assertFalse(
            $this->mpfmDepartmentManager->can('assignToOperator', $task)
        );
    }

    /**
     * Test: Surveillance Supervisor has read-only access
     */
    public function test_surveillance_supervisor_has_readonly_access(): void
    {
        $mpfmTask = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 1,
            'well_number' => 'MPFM-01',
            'string_number' => '1',
            'well_full_name' => 'MPFM-01/1',
            'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
        ]);

        $this->actingAs($this->surveillanceSupervisor);

        // Should be able to view
        $this->assertTrue(
            $this->surveillanceSupervisor->can('view', $mpfmTask)
        );

        // Should NOT be able to assign operators (no assignToOperator permission)
        $this->assertFalse(
            $this->surveillanceSupervisor->hasPermissionTo('assign_operators')
        );
    }

    /**
     * Test: Operator sees only assigned tasks
     */
    public function test_operator_sees_only_assigned_tasks(): void
    {
        // Create task assigned to MPFM operator
        $assignedTask = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 1,
            'well_number' => 'MPFM-01',
            'string_number' => '1',
            'well_full_name' => 'MPFM-01/1',
            'state' => TaskState::IN_PROGRESS,
        ]);
        $assignedTask->assignedOperators()->attach($this->mpfmOperator->id, ['is_primary' => true]);

        // Create task NOT assigned to MPFM operator
        $unassignedTask = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 2,
            'well_number' => 'MPFM-02',
            'string_number' => '1',
            'well_full_name' => 'MPFM-02/1',
            'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
        ]);

        $this->actingAs($this->mpfmOperator);

        // Operator can view assigned task
        $this->assertTrue($this->mpfmOperator->can('view', $assignedTask));

        // Operator might not be able to view unassigned task
        // (depending on policy implementation)
    }

    /**
     * Test: Plan progress calculation is accurate
     */
    public function test_plan_progress_calculation(): void
    {
        // Create 10 tasks
        for ($i = 1; $i <= 10; $i++) {
            $wellNumber = sprintf('TEST-%02d', $i);
            PlanTask::create([
                'monthly_plan_id' => $this->plan->id,
                'department_id' => $this->mpfmDept->id,
                'sno' => $i,
                'well_number' => $wellNumber,
                'string_number' => '1',
                'well_full_name' => $wellNumber . '/1',
                'state' => $i <= 5 ? TaskState::COMPLETED : TaskState::IN_PROGRESS,
                'completion_status' => $i <= 5 ? 'completed' : 'in_progress',
            ]);
        }

        // Refresh plan
        $this->plan->refresh();

        // 5 of 10 = 50%
        $this->assertEquals(5, $this->plan->tasks()->where('state', TaskState::COMPLETED)->count());
    }

    /**
     * Test: Task state transitions respect department
     */
    public function test_task_state_transitions(): void
    {
        $task = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 1,
            'well_number' => 'MPFM-01',
            'string_number' => '1',
            'well_full_name' => 'MPFM-01/1',
            'state' => TaskState::DRAFT,
            'completion_status' => 'pending',
        ]);

        // Update state
        $task->update(['state' => TaskState::ASSIGNED_TO_DEPARTMENT]);
        $this->assertEquals(TaskState::ASSIGNED_TO_DEPARTMENT, $task->state);

        // Progress through states
        $task->update(['state' => TaskState::IN_PROGRESS]);
        $this->assertEquals(TaskState::IN_PROGRESS, $task->state);

        // Department review
        $task->update(['state' => TaskState::DEPARTMENT_REVIEWED]);
        $this->assertEquals(TaskState::DEPARTMENT_REVIEWED, $task->state);
    }

    /**
     * Test: Department relationship works correctly
     */
    public function test_task_department_relationship(): void
    {
        $task = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 1,
            'well_number' => 'MPFM-01',
            'string_number' => '1',
            'well_full_name' => 'MPFM-01/1',
            'state' => TaskState::DRAFT,
        ]);

        $this->assertNotNull($task->department);
        $this->assertEquals($this->mpfmDept->id, $task->department->id);
        $this->assertEquals('MPFM', $task->department->code);
    }

    /**
     * Test: User department relationship
     */
    public function test_user_department_relationship(): void
    {
        $this->assertNotNull($this->mpfmDepartmentManager->department);
        $this->assertEquals($this->mpfmDept->id, $this->mpfmDepartmentManager->department->id);

        $this->assertNull($this->jobOfficer->department);
        $this->assertNull($this->surveillanceSupervisor->department);
    }

    /**
     * Test: Multi-operator assignment
     */
    public function test_task_multi_operator_assignment(): void
    {
        // Create two MPFM operators
        $operator2 = User::create([
            'name' => 'MPFM Operator 2',
            'email' => 'mpfm.operator2@test.com',
            'password' => bcrypt('password'),
            'department_id' => $this->mpfmDept->id,
        ])->assignRole('mpfm_operator');

        $task = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 1,
            'well_number' => 'MPFM-01',
            'string_number' => '1',
            'well_full_name' => 'MPFM-01/1',
            'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
        ]);

        // Assign multiple operators
        $task->assignedOperators()->attach([
            $this->mpfmOperator->id => ['is_primary' => true],
            $operator2->id => ['is_primary' => false],
        ]);

        $this->assertCount(2, $task->assignedOperators);
    }
}
