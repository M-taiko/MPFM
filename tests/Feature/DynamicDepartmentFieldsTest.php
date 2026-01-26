<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use App\Models\MonthlyPlan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DynamicDepartmentFieldsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $departments;

    protected function setUp(): void
    {
        parent::setUp();

        // Create role first
        Role::create(['name' => 'job_officer', 'guard_name' => 'web']);

        // Create all departments
        $this->departments = [
            'MPFM' => Department::firstOrCreate(
                ['code' => 'MPFM'],
                ['name' => 'MPFM Operations', 'is_active' => true]
            ),
            'WIRELINE' => Department::firstOrCreate(
                ['code' => 'WIRELINE'],
                ['name' => 'Wire Line Services', 'is_active' => true]
            ),
            'PRODUCTION' => Department::firstOrCreate(
                ['code' => 'PRODUCTION'],
                ['name' => 'Production Operations', 'is_active' => true]
            ),
            'DRILLING' => Department::firstOrCreate(
                ['code' => 'DRILLING'],
                ['name' => 'Drilling Services', 'is_active' => true]
            ),
            'COMPLETION' => Department::firstOrCreate(
                ['code' => 'COMPLETION'],
                ['name' => 'Well Completion', 'is_active' => true]
            ),
            'WORKOVER' => Department::firstOrCreate(
                ['code' => 'WORKOVER'],
                ['name' => 'Workover Services', 'is_active' => true]
            ),
            'MAINTENANCE' => Department::firstOrCreate(
                ['code' => 'MAINTENANCE'],
                ['name' => 'Equipment Maintenance', 'is_active' => true]
            ),
        ];

        // Create test user with job_officer role
        $this->user = User::factory()->create();
        $this->user->assignRole('job_officer');
    }

    /**
     * Test that AJAX endpoint returns correct configuration for MPFM department
     */
    public function test_mpfm_department_fields_configuration()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/monthly-plans/api/department-fields/{$this->departments['MPFM']->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'department_name' => 'MPFM Operations',
                'department_code' => 'MPFM',
            ]);

        $config = $response->json('config');

        // Verify MPFM required fields
        $this->assertContains('tower', $config['required_fields']);
        $this->assertContains('test_location', $config['required_fields']);
        $this->assertContains('mpfm_type', $config['required_fields']);
        $this->assertContains('electrification_status', $config['required_fields']);

        // Verify MPFM has no hidden fields
        $this->assertEmpty($config['hidden_fields']);

        // Verify title and help text
        $this->assertEquals('MPFM Task Details', $config['title']);
        $this->assertStringContainsString('MPFM testing parameters', $config['help_text']);
    }

    /**
     * Test that AJAX endpoint returns correct configuration for Wireline department
     */
    public function test_wireline_department_fields_configuration()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/monthly-plans/api/department-fields/{$this->departments['WIRELINE']->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'department_name' => 'Wire Line Services',
                'department_code' => 'WIRELINE',
            ]);

        $config = $response->json('config');

        // Verify Wireline required fields
        $this->assertContains('tower', $config['required_fields']);
        $this->assertContains('reservoir_layer', $config['required_fields']);
        $this->assertContains('priority', $config['required_fields']);

        // Verify Wireline hidden fields
        $this->assertContains('mpfm_type', $config['hidden_fields']);
        $this->assertContains('electrification_status', $config['hidden_fields']);

        // Verify title and help text
        $this->assertEquals('Wireline Task Details', $config['title']);
        $this->assertStringContainsString('wireline operations', $config['help_text']);
    }

    /**
     * Test that AJAX endpoint returns correct configuration for Production department
     */
    public function test_production_department_fields_configuration()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/monthly-plans/api/department-fields/{$this->departments['PRODUCTION']->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'department_name' => 'Production Operations',
                'department_code' => 'PRODUCTION',
            ]);

        $config = $response->json('config');

        // Verify Production required fields
        $this->assertContains('production_rate', $config['required_fields']);
        $this->assertContains('scheduled_date', $config['required_fields']);

        // Verify Production hidden fields
        $this->assertContains('mpfm_type', $config['hidden_fields']);
        $this->assertContains('dhsv_type', $config['hidden_fields']);
        $this->assertContains('reservoir_layer', $config['hidden_fields']);

        // Verify title
        $this->assertEquals('Production Task Details', $config['title']);
    }

    /**
     * Test that invalid department ID returns error
     */
    public function test_invalid_department_id_returns_error()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/monthly-plans/api/department-fields/99999");

        $response->assertStatus(404);
    }

    /**
     * Test that unauthenticated user cannot access endpoint
     */
    public function test_unauthenticated_user_cannot_access_endpoint()
    {
        $response = $this->getJson(
            "/monthly-plans/api/department-fields/{$this->departments['MPFM']->id}"
        );

        $response->assertStatus(401);
    }

    /**
     * Test that all departments exist in database
     */
    public function test_all_departments_exist_in_database()
    {
        $departments = Department::all();

        $this->assertCount(7, $departments);
        $this->assertTrue($departments->contains('code', 'MPFM'));
        $this->assertTrue($departments->contains('code', 'WIRELINE'));
        $this->assertTrue($departments->contains('code', 'PRODUCTION'));
        $this->assertTrue($departments->contains('code', 'DRILLING'));
        $this->assertTrue($departments->contains('code', 'COMPLETION'));
        $this->assertTrue($departments->contains('code', 'WORKOVER'));
        $this->assertTrue($departments->contains('code', 'MAINTENANCE'));
    }

    /**
     * Test that endpoint works with all department codes
     */
    public function test_endpoint_works_with_all_department_codes()
    {
        $departmentCodes = ['MPFM', 'WIRELINE', 'PRODUCTION', 'DRILLING', 'COMPLETION', 'WORKOVER', 'MAINTENANCE'];

        foreach ($departmentCodes as $code) {
            $dept = Department::where('code', $code)->first();
            $this->assertNotNull($dept, "Department with code $code should exist");

            $response = $this->actingAs($this->user)
                ->getJson("/monthly-plans/api/department-fields/{$dept->id}");

            $response->assertStatus(200, "AJAX endpoint should return 200 for department code $code");
            $this->assertTrue($response->json('success'), "Response should have success: true for $code");
        }
    }

    /**
     * Test that all department configurations are complete
     */
    public function test_all_departments_have_complete_configuration()
    {
        $departments = ['MPFM', 'WIRELINE', 'PRODUCTION', 'DRILLING', 'COMPLETION', 'WORKOVER', 'MAINTENANCE'];

        foreach ($departments as $code) {
            $dept = Department::where('code', $code)->first();

            $response = $this->actingAs($this->user)
                ->getJson("/monthly-plans/api/department-fields/{$dept->id}");

            $response->assertStatus(200);
            $config = $response->json('config');

            // Verify structure
            $this->assertArrayHasKey('required_fields', $config);
            $this->assertArrayHasKey('optional_fields', $config);
            $this->assertArrayHasKey('hidden_fields', $config);
            $this->assertArrayHasKey('title', $config);
            $this->assertArrayHasKey('help_text', $config);

            // Verify all are arrays
            $this->assertIsArray($config['required_fields']);
            $this->assertIsArray($config['optional_fields']);
            $this->assertIsArray($config['hidden_fields']);

            // Verify title and help text are not empty
            $this->assertNotEmpty($config['title']);
            $this->assertNotEmpty($config['help_text']);
        }
    }

}
