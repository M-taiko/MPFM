<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use App\Models\Department;
use App\Enums\TaskState;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $jobOfficer;
    protected $departmentManager;
    protected $operator;
    protected $mpfmDept;
    protected $plan;
    protected $task;

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
        // Create departments
        $this->mpfmDept = Department::firstOrCreate(
            ['code' => 'MPFM'],
            [
                'name' => 'MPFM Department',
                'description' => 'Multi-Phase Flow Meter Department',
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        // Create roles
        $this->createRolesAndPermissions();

        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'department_id' => null,
        ])->assignRole('admin');

        // Create job officer
        $this->jobOfficer = User::create([
            'name' => 'Job Officer',
            'email' => 'job.officer@test.com',
            'password' => bcrypt('password'),
            'department_id' => null,
        ])->assignRole('job_officer');

        // Create department manager
        $this->departmentManager = User::create([
            'name' => 'Department Manager',
            'email' => 'dept.manager@test.com',
            'password' => bcrypt('password'),
            'department_id' => $this->mpfmDept->id,
        ])->assignRole('department_manager');

        // Create operator
        $this->operator = User::create([
            'name' => 'Operator',
            'email' => 'operator@test.com',
            'password' => bcrypt('password'),
            'department_id' => $this->mpfmDept->id,
        ])->assignRole('mpfm_operator');

        // Create sample plan
        $this->plan = MonthlyPlan::create([
            'plan_name' => 'Test Plan',
            'plan_month' => 'February 2026',
            'monthly_target_kpi' => 10,
            'created_by' => $this->jobOfficer->id,
            'status' => 'draft',
        ]);

        // Create sample task
        $this->task = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 1,
            'well_number' => 'TEST-01',
            'string_number' => '1',
            'well_full_name' => 'TEST-01/1',
            'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
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
            'manage_users',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $jobOfficerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'job_officer']);
        $departmentManagerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'department_manager']);
        $mpfmOperatorRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'mpfm_operator']);

        // Admin gets all permissions
        $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::all());

        // Job Officer permissions
        $jobOfficerRole->syncPermissions(['view_well_tests']);

        // Department Manager permissions
        $departmentManagerRole->syncPermissions(['view_department_tasks', 'assign_operators', 'review_department_work']);

        // MPFM Operator permissions
        $mpfmOperatorRole->syncPermissions(['view_well_tests']);
    }

    /**
     * Test: Admin is recognized as admin
     */
    public function test_admin_is_identified_as_admin(): void
    {
        $this->assertTrue($this->admin->isAdmin());
        $this->assertTrue($this->admin->hasAdminAccess());
        $this->assertTrue($this->admin->hasRole('admin'));
    }

    /**
     * Test: Admin has all permissions
     */
    public function test_admin_has_all_permissions(): void
    {
        $this->assertTrue($this->admin->hasPermissionTo('view_well_tests'));
        $this->assertTrue($this->admin->hasPermissionTo('assign_operators'));
        $this->assertTrue($this->admin->hasPermissionTo('manage_users'));
        $this->assertTrue($this->admin->hasPermissionTo('view_users'));
        $this->assertTrue($this->admin->hasPermissionTo('create_users'));
        $this->assertTrue($this->admin->hasPermissionTo('edit_users'));
        $this->assertTrue($this->admin->hasPermissionTo('delete_users'));
        $this->assertTrue($this->admin->hasPermissionTo('view_all_departments'));
        $this->assertTrue($this->admin->hasPermissionTo('review_department_work'));
    }

    /**
     * Test: Admin can view all tasks
     */
    public function test_admin_can_view_all_tasks(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue($this->admin->can('view', $this->task));
    }

    /**
     * Test: Admin can assign operators across departments
     */
    public function test_admin_can_assign_operator_cross_department(): void
    {
        // Create another department
        $wirelineDept = Department::firstOrCreate(
            ['code' => 'WIRELINE'],
            ['name' => 'Wireline Department', 'description' => 'Wireline', 'is_active' => true]
        );

        // Create operator in different department
        $wirelineOperator = User::create([
            'name' => 'Wireline Operator',
            'email' => 'wireline.op@test.com',
            'password' => bcrypt('password'),
            'department_id' => $wirelineDept->id,
        ])->assignRole('mpfm_operator');

        // Create task in MPFM department
        $mpfmTask = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $this->mpfmDept->id,
            'sno' => 2,
            'well_number' => 'TEST-02',
            'string_number' => '1',
            'well_full_name' => 'TEST-02/1',
            'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
        ]);

        $this->actingAs($this->admin);

        // Admin should be able to assign operator from different department
        $this->assertTrue($this->admin->can('assignToOperator', $mpfmTask));

        // Verify admin can assign the operator
        $mpfmTask->assignedOperators()->attach($wirelineOperator->id);
        $this->assertTrue($mpfmTask->assignedOperators()->where('user_id', $wirelineOperator->id)->exists());
    }

    /**
     * Test: Admin can override task state
     */
    public function test_admin_can_override_task_state(): void
    {
        $this->actingAs($this->admin);

        $currentState = $this->task->state;
        $this->task->update(['state' => TaskState::COMPLETED]);

        $this->assertNotEquals($currentState, $this->task->fresh()->state);
        $this->assertEquals(TaskState::COMPLETED, $this->task->fresh()->state);
    }

    /**
     * Test: Admin can assign users to any department
     */
    public function test_admin_can_assign_user_to_department(): void
    {
        // Create another department
        $wirelineDept = Department::firstOrCreate(
            ['code' => 'WIRELINE'],
            ['name' => 'Wireline Department', 'description' => 'Wireline', 'is_active' => true]
        );

        $this->actingAs($this->admin);

        // Admin can change user's department
        $this->operator->update(['department_id' => $wirelineDept->id]);

        $this->assertEquals($wirelineDept->id, $this->operator->fresh()->department_id);
    }

    /**
     * Test: Admin can assign roles to users
     */
    public function test_admin_can_assign_roles(): void
    {
        $this->actingAs($this->admin);

        // Create a new user
        $newUser = User::create([
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'password' => bcrypt('password'),
            'department_id' => null,
        ]);

        // Admin assigns role
        $newUser->assignRole('job_officer');

        $this->assertTrue($newUser->hasRole('job_officer'));
    }

    /**
     * Test: Admin bypasses authorization policy
     */
    public function test_admin_bypasses_authorization_policy(): void
    {
        // Create a task in another department
        $wirelineDept = Department::firstOrCreate(
            ['code' => 'WIRELINE'],
            ['name' => 'Wireline Department', 'description' => 'Wireline', 'is_active' => true]
        );

        $wirelineTask = PlanTask::create([
            'monthly_plan_id' => $this->plan->id,
            'department_id' => $wirelineDept->id,
            'sno' => 3,
            'well_number' => 'WL-01',
            'string_number' => '1',
            'well_full_name' => 'WL-01/1',
            'state' => TaskState::ASSIGNED_TO_DEPARTMENT,
        ]);

        $this->actingAs($this->admin);

        // Department manager would be blocked, but admin should pass
        $this->assertTrue($this->admin->can('view', $wirelineTask));
        $this->assertTrue($this->admin->can('assignToOperator', $wirelineTask));
        $this->assertTrue($this->admin->can('update', $wirelineTask));
    }

    /**
     * Test: Admin can view all departments
     */
    public function test_admin_can_view_all_departments(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue($this->admin->hasPermissionTo('view_all_departments'));

        // Admin should be able to view all department data
        $departments = Department::all();
        $this->assertGreaterThanOrEqual(1, $departments->count());
    }

    /**
     * Test: Admin can create users
     */
    public function test_admin_can_create_users(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue($this->admin->hasPermissionTo('create_users'));

        $newUser = User::create([
            'name' => 'New Admin User',
            'email' => 'newadmin@test.com',
            'password' => bcrypt('password'),
            'department_id' => null,
        ]);

        $this->assertDatabaseHas('users', ['email' => 'newadmin@test.com']);
    }

    /**
     * Test: Admin can delete users
     */
    public function test_admin_can_delete_users(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue($this->admin->hasPermissionTo('delete_users'));

        $userToDelete = User::create([
            'name' => 'User to Delete',
            'email' => 'delete@test.com',
            'password' => bcrypt('password'),
            'department_id' => null,
        ]);

        $userToDelete->delete();

        $this->assertDatabaseMissing('users', ['email' => 'delete@test.com']);
    }

    /**
     * Test: Non-admin users don't have admin access
     */
    public function test_non_admin_users_not_have_admin_access(): void
    {
        $this->assertFalse($this->jobOfficer->isAdmin());
        $this->assertFalse($this->jobOfficer->hasAdminAccess());
        $this->assertFalse($this->departmentManager->isAdmin());
        $this->assertFalse($this->operator->isAdmin());
    }

    /**
     * Test: Admin access gate works
     */
    public function test_admin_access_gate_works(): void
    {
        $this->actingAs($this->admin);

        $this->assertTrue(\Illuminate\Support\Facades\Gate::allows('admin-access'));
    }

    /**
     * Test: Non-admin denied by admin access gate
     */
    public function test_non_admin_denied_by_admin_access_gate(): void
    {
        $this->actingAs($this->jobOfficer);

        $this->assertFalse(\Illuminate\Support\Facades\Gate::allows('admin-access'));
    }
}
