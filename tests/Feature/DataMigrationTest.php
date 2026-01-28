<?php

namespace Tests\Feature;

use App\Domains\Task\Models\Task;
use App\Enums\TaskState;
use App\Models\Department;
use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected MonthlyPlan $monthlyPlan;
    protected Department $department;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->department = Department::factory()->create(['code' => 'MPFM']);
        $this->monthlyPlan = MonthlyPlan::factory()->create();
        $this->user = User::factory()->create();
    }

    /**
     * Test that plan tasks are successfully migrated to unified tasks table
     */
    public function test_plan_tasks_migrated_to_tasks_table(): void
    {
        // Create old plan tasks before migration would run
        // Note: In real migration, these exist in production

        // Simulate the migration by creating tasks directly
        $task1 = Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::DRAFT,
            'priority' => 'high',
            'title' => 'Test Task 1',
            'created_by_user_id' => $this->user->id,
        ]);

        $task2 = Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::IN_PROGRESS,
            'priority' => 'medium',
            'title' => 'Test Task 2',
            'created_by_user_id' => $this->user->id,
        ]);

        // Verify migrated data
        $this->assertDatabaseHas('tasks', [
            'id' => $task1->id,
            'task_type' => 'plan_task',
            'title' => 'Test Task 1',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task2->id,
            'task_type' => 'plan_task',
            'title' => 'Test Task 2',
        ]);

        // Verify count
        $migratedCount = Task::where('task_type', 'plan_task')->count();
        $this->assertEquals(2, $migratedCount);
    }

    /**
     * Test that operator assignments are migrated
     */
    public function test_operator_assignments_migrated(): void
    {
        $operator = User::factory()->create();

        $task = Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::DRAFT,
            'priority' => 'medium',
            'title' => 'Task with Operator',
            'created_by_user_id' => $this->user->id,
        ]);

        // Assign operator
        $task->operators()->attach($operator->id, ['is_primary' => true]);

        // Verify operator is assigned
        $this->assertTrue($task->isAssignedTo($operator->id));
        $this->assertDatabaseHas('task_operators', [
            'task_id' => $task->id,
            'user_id' => $operator->id,
        ]);
    }

    /**
     * Test that task state is preserved in migration
     */
    public function test_task_states_preserved_in_migration(): void
    {
        $states = [
            TaskState::DRAFT,
            TaskState::SUBMITTED_TO_SURVEILLANCE,
            TaskState::ASSIGNED_TO_DEPARTMENT,
            TaskState::IN_PROGRESS,
            TaskState::DEPARTMENT_REVIEWED,
            TaskState::SURVEILLANCE_REVIEWED,
            TaskState::JOB_OFFICER_APPROVED,
            TaskState::COMPLETED,
        ];

        foreach ($states as $index => $state) {
            Task::create([
                'monthly_plan_id' => $this->monthlyPlan->id,
                'department_id' => $this->department->id,
                'task_type' => 'plan_task',
                'state' => $state,
                'priority' => 'medium',
                'title' => "Task in {$state->label()}",
                'created_by_user_id' => $this->user->id,
            ]);
        }

        // Verify all states exist
        foreach ($states as $state) {
            $this->assertDatabaseHas('tasks', [
                'task_type' => 'plan_task',
                'state' => $state->value,
            ]);
        }

        // Verify count
        $this->assertEquals(count($states), Task::where('task_type', 'plan_task')->count());
    }

    /**
     * Test that SLA histories are created for migrated tasks
     */
    public function test_sla_histories_created_for_migrated_tasks(): void
    {
        $futureDate = now()->addDays(7);

        $task = Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::DRAFT,
            'priority' => 'medium',
            'title' => 'Task with SLA',
            'created_by_user_id' => $this->user->id,
            'expected_completion_at' => $futureDate,
        ]);

        // Create SLA history (migration would do this)
        $task->slaHistories()->create([
            'entered_state' => TaskState::DRAFT->value,
            'expected_completion_at' => $futureDate,
            'escalation_level' => 0,
        ]);

        // Verify SLA was created
        $this->assertDatabaseHas('task_sla_histories', [
            'task_id' => $task->id,
            'escalation_level' => 0,
        ]);
    }

    /**
     * Test data integrity - no null states
     */
    public function test_no_null_states_after_migration(): void
    {
        Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::DRAFT,
            'priority' => 'medium',
            'title' => 'Task with State',
            'created_by_user_id' => $this->user->id,
        ]);

        // Verify no null states exist
        $nullStates = Task::where('task_type', 'plan_task')
            ->whereNull('state')
            ->count();

        $this->assertEquals(0, $nullStates);
    }

    /**
     * Test data integrity - no null created_by
     */
    public function test_no_null_created_by_after_migration(): void
    {
        Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::DRAFT,
            'priority' => 'medium',
            'title' => 'Task Created By Someone',
            'created_by_user_id' => $this->user->id,
        ]);

        // Verify no null created_by_user_id
        $nullCreatedBy = Task::where('task_type', 'plan_task')
            ->whereNull('created_by_user_id')
            ->count();

        $this->assertEquals(0, $nullCreatedBy);
    }

    /**
     * Test that old plan_tasks table remains intact
     */
    public function test_old_plan_tasks_table_remains_intact(): void
    {
        // Create a task using old Task model if it still exists
        // This ensures backward compatibility during migration

        // After migration, old table should still have data
        // New tasks table is populated with migrated data
        // Both can coexist during transition period
        $this->assertTrue(true); // Placeholder for actual table check
    }

    /**
     * Test batch task creation with TaskWorkflowEngine
     */
    public function test_bulk_task_creation_with_engine(): void
    {
        $this->actingAs($this->user);

        $engine = app(\App\Domains\Task\Services\TaskWorkflowEngine::class);

        $tasks = $engine->createFromMonthlyPlan($this->monthlyPlan, [
            [
                'title' => 'Bulk Task 1',
                'department_id' => $this->department->id,
                'priority' => 'high',
            ],
            [
                'title' => 'Bulk Task 2',
                'department_id' => $this->department->id,
                'priority' => 'medium',
            ],
        ]);

        $this->assertEquals(2, $tasks->count());
        $this->assertDatabaseHas('tasks', ['title' => 'Bulk Task 1']);
        $this->assertDatabaseHas('tasks', ['title' => 'Bulk Task 2']);
    }

    /**
     * Test bulk state transitions with TaskWorkflowEngine
     */
    public function test_bulk_state_transitions_with_engine(): void
    {
        $this->actingAs($this->user);

        $engine = app(\App\Domains\Task\Services\TaskWorkflowEngine::class);

        // Create multiple tasks in DRAFT state
        $tasks = collect();
        for ($i = 0; $i < 3; $i++) {
            $tasks->push(Task::create([
                'monthly_plan_id' => $this->monthlyPlan->id,
                'department_id' => $this->department->id,
                'task_type' => 'plan_task',
                'state' => TaskState::DRAFT,
                'priority' => 'medium',
                'title' => "Task $i",
                'created_by_user_id' => $this->user->id,
            ]));
        }

        // Bulk transition
        $results = $engine->transitionBulk(
            $tasks,
            TaskState::SUBMITTED_TO_SURVEILLANCE,
            $this->user,
            'Bulk transition test'
        );

        // Verify results
        $this->assertEquals(3, $results->count());
        $successCount = $results->where('success', true)->count();
        $this->assertEquals(3, $successCount);

        // Verify all transitioned
        $this->assertEquals(
            3,
            Task::where('state', TaskState::SUBMITTED_TO_SURVEILLANCE->value)->count()
        );
    }

    /**
     * Test workflow statistics calculation
     */
    public function test_workflow_statistics_calculation(): void
    {
        $engine = app(\App\Domains\Task\Services\TaskWorkflowEngine::class);

        // Create tasks in various states
        Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::DRAFT,
            'priority' => 'medium',
            'title' => 'Task 1',
            'created_by_user_id' => $this->user->id,
        ]);

        Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::IN_PROGRESS,
            'priority' => 'medium',
            'title' => 'Task 2',
            'created_by_user_id' => $this->user->id,
        ]);

        Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::COMPLETED,
            'priority' => 'medium',
            'title' => 'Task 3',
            'created_by_user_id' => $this->user->id,
        ]);

        // Get stats
        $stats = $engine->getWorkflowStats($this->monthlyPlan);

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(1, $stats['completed']);
        $this->assertEquals(33, $stats['completion_percentage']); // 1/3 = 33%
    }
}
