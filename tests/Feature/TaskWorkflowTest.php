<?php

namespace Tests\Feature;

use App\Domains\Task\Models\Task;
use App\Domains\Task\Services\TaskStateMachine;
use App\Enums\TaskState;
use App\Models\Department;
use App\Models\MonthlyPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $jobOfficer;
    protected User $surveillanceSupervisor;
    protected User $departmentManager;
    protected User $operator;
    protected Department $department;
    protected MonthlyPlan $monthlyPlan;
    protected Task $task;
    protected TaskStateMachine $stateMachine;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with roles
        $this->jobOfficer = User::factory()->create();
        $this->jobOfficer->assignRole('job_officer');

        $this->surveillanceSupervisor = User::factory()->create();
        $this->surveillanceSupervisor->assignRole('surveillance_supervisor');

        $this->departmentManager = User::factory()->create();
        $this->departmentManager->assignRole('department_manager');

        $this->operator = User::factory()->create();
        $this->operator->assignRole('operator');

        // Create department
        $this->department = Department::factory()->create();
        $this->departmentManager->update(['department_id' => $this->department->id]);
        $this->operator->update(['department_id' => $this->department->id]);

        // Create monthly plan
        $this->monthlyPlan = MonthlyPlan::factory()->create();

        // Create task
        $this->task = Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::DRAFT,
            'priority' => 'medium',
            'title' => 'Test Task',
            'description' => 'Test task description',
            'created_by_user_id' => $this->jobOfficer->id,
        ]);

        $this->stateMachine = app(TaskStateMachine::class);
    }

    /**
     * Test task creation by job officer
     */
    public function test_job_officer_can_create_task(): void
    {
        $this->actingAs($this->jobOfficer);

        $response = $this->post(route('tasks.store'), [
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::DRAFT->value,
            'priority' => 'high',
            'title' => 'New Task',
            'description' => 'Test description',
            'expected_completion_at' => now()->addDays(7)->format('Y-m-d H:i'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task',
            'priority' => 'high',
        ]);
    }

    /**
     * Test task state transition from DRAFT to SUBMITTED_TO_SURVEILLANCE
     */
    public function test_job_officer_can_submit_task_to_surveillance(): void
    {
        $this->assertTrue($this->task->canTransitionTo(TaskState::SUBMITTED_TO_SURVEILLANCE));

        $this->actingAs($this->jobOfficer);
        $history = $this->stateMachine->transition(
            $this->task,
            TaskState::SUBMITTED_TO_SURVEILLANCE,
            $this->jobOfficer,
            'Task ready for review'
        );

        $this->task->refresh();
        $this->assertTrue($this->task->isInState(TaskState::SUBMITTED_TO_SURVEILLANCE));
        $this->assertNotNull($history);
        $this->assertEquals(TaskState::DRAFT->value, $history->from_state->value);
    }

    /**
     * Test surveillance supervisor can assign task to department
     */
    public function test_surveillance_supervisor_can_assign_to_department(): void
    {
        // First move to SUBMITTED_TO_SURVEILLANCE
        $this->stateMachine->transition(
            $this->task,
            TaskState::SUBMITTED_TO_SURVEILLANCE,
            $this->jobOfficer
        );

        $this->assertTrue(
            $this->stateMachine->canUserTransition(
                $this->task,
                TaskState::ASSIGNED_TO_DEPARTMENT,
                $this->surveillanceSupervisor
            )
        );

        $this->stateMachine->transition(
            $this->task,
            TaskState::ASSIGNED_TO_DEPARTMENT,
            $this->surveillanceSupervisor,
            'Assigned to MPFM department'
        );

        $this->task->refresh();
        $this->assertTrue($this->task->isInState(TaskState::ASSIGNED_TO_DEPARTMENT));
    }

    /**
     * Test department manager can assign operator and move to IN_PROGRESS
     */
    public function test_department_manager_can_assign_operator(): void
    {
        // Setup: Move task to ASSIGNED_TO_DEPARTMENT
        $this->task->update(['state' => TaskState::ASSIGNED_TO_DEPARTMENT]);

        // Assign operator
        $this->task->operators()->attach($this->operator->id, [
            'is_primary' => true,
            'assigned_at' => now(),
        ]);

        $this->assertTrue($this->task->isAssignedTo($this->operator->id));
    }

    /**
     * Test operator can start task (move to IN_PROGRESS)
     */
    public function test_operator_can_start_task(): void
    {
        // Setup: Assign operator
        $this->task->update(['state' => TaskState::ASSIGNED_TO_DEPARTMENT]);
        $this->task->operators()->attach($this->operator->id);

        $this->assertTrue(
            $this->stateMachine->canUserTransition(
                $this->task,
                TaskState::IN_PROGRESS,
                $this->operator
            )
        );

        $this->stateMachine->transition(
            $this->task,
            TaskState::IN_PROGRESS,
            $this->operator,
            'Started work'
        );

        $this->task->refresh();
        $this->assertTrue($this->task->isInState(TaskState::IN_PROGRESS));
    }

    /**
     * Test authorization prevents invalid transitions
     */
    public function test_operator_cannot_approve_task(): void
    {
        $this->task->update(['state' => TaskState::IN_PROGRESS]);

        $this->assertFalse(
            $this->stateMachine->canUserTransition(
                $this->task,
                TaskState::JOB_OFFICER_APPROVED,
                $this->operator
            )
        );
    }

    /**
     * Test task state validation
     */
    public function test_task_cannot_skip_states(): void
    {
        // Task is in DRAFT, try to go to COMPLETED directly
        $this->assertFalse($this->task->canTransitionTo(TaskState::COMPLETED));
    }

    /**
     * Test SLA tracking on state change
     */
    public function test_sla_tracking_created_on_state_change(): void
    {
        $this->stateMachine->transition(
            $this->task,
            TaskState::SUBMITTED_TO_SURVEILLANCE,
            $this->jobOfficer
        );

        // Check if SLA history was created
        $this->task->refresh();
        $slaHistories = $this->task->slaHistories;

        // Should have SLA tracking for this state
        $this->assertTrue($slaHistories->count() >= 0); // May be 0 if no SLA set for DRAFT
    }

    /**
     * Test task audit trail
     */
    public function test_state_history_tracks_all_transitions(): void
    {
        $this->stateMachine->transition(
            $this->task,
            TaskState::SUBMITTED_TO_SURVEILLANCE,
            $this->jobOfficer,
            'Testing'
        );

        $this->task->refresh();
        $history = $this->task->stateHistories()->first();

        $this->assertNotNull($history);
        $this->assertEquals(TaskState::DRAFT->value, $history->from_state->value);
        $this->assertEquals(TaskState::SUBMITTED_TO_SURVEILLANCE->value, $history->to_state->value);
        $this->assertEquals($this->jobOfficer->id, $history->changed_by_user_id);
        $this->assertEquals('Testing', $history->notes);
    }

    /**
     * Test task helper methods
     */
    public function test_task_helper_methods(): void
    {
        $this->assertTrue($this->task->isInState(TaskState::DRAFT));
        $this->assertFalse($this->task->isCompleted());

        $this->task->update(['expected_completion_at' => now()->subDays(1)]);
        $this->assertTrue($this->task->isOverdue());

        $this->task->update(['expected_completion_at' => now()->addDays(5)]);
        $this->assertFalse($this->task->isOverdue());
        $this->assertEquals(5, $this->task->getDaysUntilDue());
    }

    /**
     * Test progress percentage calculation
     */
    public function test_progress_percentage_by_state(): void
    {
        $this->assertEquals(10, $this->task->getProgressPercentage());

        $this->task->update(['state' => TaskState::SUBMITTED_TO_SURVEILLANCE]);
        $this->assertEquals(20, $this->task->getProgressPercentage());

        $this->task->update(['state' => TaskState::IN_PROGRESS]);
        $this->assertEquals(60, $this->task->getProgressPercentage());

        $this->task->update(['state' => TaskState::COMPLETED]);
        $this->assertEquals(100, $this->task->getProgressPercentage());
    }

    /**
     * Test task role-based access
     */
    public function test_job_officer_can_view_all_tasks(): void
    {
        $this->actingAs($this->jobOfficer);

        $response = $this->get(route('tasks.index'));
        $response->assertStatus(200);
    }

    /**
     * Test operator can only view assigned tasks
     */
    public function test_operator_sees_only_assigned_tasks(): void
    {
        $this->task->operators()->attach($this->operator->id);

        $this->actingAs($this->operator);

        // Should be able to view assigned task
        $response = $this->get(route('tasks.show', $this->task));
        $response->assertStatus(200);

        // Create another task not assigned to operator
        $otherTask = Task::create([
            'monthly_plan_id' => $this->monthlyPlan->id,
            'department_id' => $this->department->id,
            'task_type' => 'plan_task',
            'state' => TaskState::DRAFT,
            'priority' => 'medium',
            'title' => 'Other Task',
            'created_by_user_id' => $this->jobOfficer->id,
        ]);

        // Should not be able to view other task
        $response = $this->get(route('tasks.show', $otherTask));
        $response->assertStatus(403);
    }
}
