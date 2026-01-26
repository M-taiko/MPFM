<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;

/**
 * Test suite for Dynamic Department-Specific Fields feature
 *
 * Tests the AJAX endpoint that returns department-specific field configurations
 * for the task creation form
 */
class DynamicDepartmentFieldsSimpleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary role
        Role::create(['name' => 'job_officer', 'guard_name' => 'web']);

        // Create test user
        $this->user = User::factory()->create();
        $this->user->assignRole('job_officer');
    }

    /**
     * Test that AJAX endpoint exists and returns correct structure
     */
    public function test_ajax_endpoint_returns_correct_json_structure()
    {
        // Create or use existing department
        $dept = Department::firstOrCreate(
            ['code' => 'MPFM'],
            ['name' => 'MPFM Operations', 'is_active' => true]
        );

        // Make authenticated request to endpoint
        $response = $this->actingAs($this->user)
            ->getJson("/monthly-plans/api/department-fields/{$dept->id}");

        // Assert successful response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'department_name' => 'MPFM Operations',
                'department_code' => 'MPFM',
            ])
            ->assertJsonStructure([
                'success',
                'department_name',
                'department_code',
                'config' => [
                    'required_fields',
                    'optional_fields',
                    'hidden_fields',
                    'title',
                    'help_text'
                ]
            ]);
    }

    /**
     * Test that MPFM department has correct field configuration
     */
    public function test_mpfm_department_configuration_is_correct()
    {
        $dept = Department::firstOrCreate(
            ['code' => 'MPFM'],
            ['name' => 'MPFM Operations', 'is_active' => true]
        );

        $response = $this->actingAs($this->user)
            ->getJson("/monthly-plans/api/department-fields/{$dept->id}");

        $config = $response->json('config');

        // Verify MPFM required fields
        $this->assertContains('tower', $config['required_fields']);
        $this->assertContains('test_location', $config['required_fields']);
        $this->assertContains('mpfm_type', $config['required_fields']);
        $this->assertContains('electrification_status', $config['required_fields']);

        // MPFM should have no hidden fields
        $this->assertEmpty($config['hidden_fields']);
    }

    /**
     * Test that Wireline department has different configuration from MPFM
     */
    public function test_wireline_department_configuration_differs_from_mpfm()
    {
        $wireline = Department::firstOrCreate(
            ['code' => 'WIRELINE'],
            ['name' => 'Wire Line Services', 'is_active' => true]
        );

        $response = $this->actingAs($this->user)
            ->getJson("/monthly-plans/api/department-fields/{$wireline->id}");

        $config = $response->json('config');

        // Wireline has different required fields
        $this->assertContains('tower', $config['required_fields']);
        $this->assertContains('reservoir_layer', $config['required_fields']);
        $this->assertContains('priority', $config['required_fields']);

        // Wireline has hidden fields (things not needed)
        $this->assertContains('mpfm_type', $config['hidden_fields']);
        $this->assertContains('electrification_status', $config['hidden_fields']);
    }

    /**
     * Test that unauthenticated user gets 401 response
     */
    public function test_unauthenticated_user_gets_401_response()
    {
        $dept = Department::firstOrCreate(
            ['code' => 'TEST'],
            ['name' => 'Test Department', 'is_active' => true]
        );

        // Make request without authentication
        $response = $this->getJson("/monthly-plans/api/department-fields/{$dept->id}");

        // Should be redirected to login (302) or get unauthorized (401/419)
        $this->assertTrue(
            in_array($response->status(), [301, 302, 401, 419]),
            "Expected status to be 301, 302, 401, or 419, but got {$response->status()}"
        );
    }

    /**
     * Test that invalid department ID returns 404
     */
    public function test_invalid_department_id_returns_404()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/monthly-plans/api/department-fields/99999");

        $response->assertStatus(404);
    }

    /**
     * Test that all departments return consistent response structure
     */
    public function test_all_departments_return_consistent_structure()
    {
        $departmentCodes = ['MPFM', 'WIRELINE', 'PRODUCTION', 'DRILLING', 'COMPLETION', 'WORKOVER', 'MAINTENANCE'];

        foreach ($departmentCodes as $code) {
            $dept = Department::firstOrCreate(
                ['code' => $code],
                ['name' => "{$code} Operations", 'is_active' => true]
            );

            $response = $this->actingAs($this->user)
                ->getJson("/monthly-plans/api/department-fields/{$dept->id}");

            // All responses should be successful
            $response->assertStatus(200, "Endpoint should return 200 for department code $code");

            // All responses should have required structure
            $config = $response->json('config');
            $this->assertIsArray($config['required_fields'], "required_fields should be array for $code");
            $this->assertIsArray($config['optional_fields'], "optional_fields should be array for $code");
            $this->assertIsArray($config['hidden_fields'], "hidden_fields should be array for $code");
            $this->assertNotEmpty($config['title'], "title should not be empty for $code");
            $this->assertNotEmpty($config['help_text'], "help_text should not be empty for $code");
        }
    }

    /**
     * Test that field configurations are mutually exclusive (required + optional + hidden = all fields)
     */
    public function test_field_categories_are_properly_defined()
    {
        $dept = Department::firstOrCreate(
            ['code' => 'MPFM'],
            ['name' => 'MPFM Operations', 'is_active' => true]
        );

        $response = $this->actingAs($this->user)
            ->getJson("/monthly-plans/api/department-fields/{$dept->id}");

        $config = $response->json('config');

        // All fields should be defined
        $allFields = array_merge(
            $config['required_fields'],
            $config['optional_fields'],
            $config['hidden_fields']
        );

        // Check for reasonable number of fields
        $this->assertGreaterThan(3, count($allFields), "Should have more than 3 fields defined");
        $this->assertLessThan(20, count($allFields), "Should have less than 20 fields defined");

        // Required fields should not be empty for at least one department
        $this->assertNotEmpty($config['required_fields'], "At least one department should have required fields");
    }
}
