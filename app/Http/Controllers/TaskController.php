<?php

namespace App\Http\Controllers;

use App\Domains\Task\Models\Task;
use App\Domains\Task\Services\TaskStateMachine;
use App\Enums\TaskState;
use App\Events\TaskStateChanged;
use App\Models\Department;
use App\Models\MonthlyPlan;
use App\Models\OilString;
use App\Models\Tower;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private TaskStateMachine $stateMachine;

    public function __construct(TaskStateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
        $this->middleware('auth');
    }

    /**
     * Display a listing of tasks
     */
    public function index(Request $request)
    {
        $query = Task::with(['department', 'assignedTo', 'createdBy', 'monthlyPlan']);

        // Filter by department (if user is department manager)
        $user = Auth::user();
        if ($user->hasRole('department_manager') && $user->department_id) {
            $query->where('department_id', $user->department_id);
        } elseif ($user->hasRole('operator') && $user->department_id) {
            // Operators see only their assigned tasks
            $query->whereHas('operators', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }
        // Admins and surveillance supervisors see all tasks

        // Apply filters from request
        if ($request->has('state') && $request->state) {
            $query->where('state', $request->state);
        }

        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->paginate(15);
        $departments = Department::active()->get();

        return view('tasks.index', compact('tasks', 'departments'));
    }

    /**
     * Show the form for creating a new task
     * Accepts ?monthly_plan_id=X to pre-select a monthly plan (used by monthly-plans flow)
     */
    public function create(Request $request)
    {
        $monthlyPlans  = MonthlyPlan::orderBy('created_at', 'desc')->get();
        $departments   = Department::active()->get();
        $users         = User::all();
        $oilStrings    = OilString::all();
        $towers        = Tower::all();
        $selectedPlanId = $request->query('monthly_plan_id');

        return view('tasks.form', compact(
            'monthlyPlans', 'departments', 'users', 'oilStrings', 'towers', 'selectedPlanId'
        ));
    }

    /**
     * Store a newly created task in storage
     */
    public function store(Request $request)
    {
        if ($request->filled('expected_completion_at')) {
            $request->merge([
                'expected_completion_at' => str_replace('T', ' ', $request->expected_completion_at),
            ]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'monthly_plan_id' => 'required|exists:monthly_plans,id',
            'department_id' => 'required|exists:departments,id',
            'task_type' => 'required|string|in:plan_task,daily_plan,operator_plan',
            'priority' => 'required|string|in:low,medium,high,critical',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'expected_completion_at' => 'nullable|date_format:Y-m-d H:i|after:now',
            'oil_string_id' => 'nullable|exists:oil_strings,id',
            'tower_id' => 'nullable|exists:towers,id',
        ]);

        $validated['created_by_user_id'] = Auth::id();
        // Start directly in submitted_to_surveillance so it appears in surveillance dashboard
        $validated['state'] = TaskState::SUBMITTED_TO_SURVEILLANCE->value;

        // Save department-specific fields in type_specific_data JSON
        if ($request->filled('dept_data')) {
            $validated['type_specific_data'] = array_filter(
                $request->input('dept_data', []),
                fn($v) => $v !== '' && $v !== null
            );
        }

        $task = Task::create($validated);

        // Fire event so NotifyStakeholders listener sends notifications to surveillance supervisors
        event(new TaskStateChanged(
            $task,
            TaskState::DRAFT,                      // conceptual "before" state (task didn't exist)
            TaskState::SUBMITTED_TO_SURVEILLANCE,  // actual initial state
            Auth::user()
        ));

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task created and submitted to Surveillance Supervisor for review.');
    }

    /**
     * Display the specified task
     */
    public function show(Task $task)
    {
        $task->load(['department', 'assignedTo', 'createdBy', 'monthlyPlan', 'stateHistory', 'slAHistory', 'operators', 'oilString', 'tower']);

        $availableTransitions = [];
        if (Auth::user()) {
            $availableTransitions = $this->getAvailableTransitions($task, Auth::user());
        }

        // Operators in the task's department (for assign form shown to surveillance supervisor)
        $departmentOperators = collect();
        if ($task->department_id && Auth::user()->hasAnyRole(['surveillance_supervisor', 'department_supervisor', 'admin'])) {
            $departmentOperators = User::where('department_id', $task->department_id)
                ->whereHas('roles', fn($q) => $q->whereIn('name', ['operator', 'mpfm_operator', 'department_supervisor']))
                ->orderBy('name')
                ->get();
        }

        return view('tasks.show', compact('task', 'availableTransitions', 'departmentOperators'));
    }

    /**
     * Show the form for editing the specified task
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $monthlyPlans = MonthlyPlan::all();
        $departments = Department::active()->get();
        $users = User::all();
        $oilStrings = OilString::all();
        $towers = Tower::all();

        return view('tasks.form', compact('task', 'monthlyPlans', 'departments', 'users', 'oilStrings', 'towers'));
    }

    /**
     * Update the specified task in storage
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        if ($request->filled('expected_completion_at')) {
            $request->merge([
                'expected_completion_at' => str_replace('T', ' ', $request->expected_completion_at),
            ]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'monthly_plan_id' => 'required|exists:monthly_plans,id',
            'department_id' => 'required|exists:departments,id',
            'task_type' => 'required|string|in:plan_task,daily_plan,operator_plan',
            'priority' => 'required|string|in:low,medium,high,critical',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'expected_completion_at' => 'nullable|date_format:Y-m-d H:i|after:now',
            'oil_string_id' => 'nullable|exists:oil_strings,id',
            'tower_id' => 'nullable|exists:towers,id',
        ]);

        $task->update($validated);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified task from storage
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully');
    }

    /**
     * Assign operators to a task and move it to ASSIGNED_TO_DEPARTMENT.
     * Accepts multiple operators with one designated as primary.
     * Also works as re-assignment when task is already ASSIGNED_TO_DEPARTMENT.
     */
    public function assign(Request $request, Task $task)
    {
        $validated = $request->validate([
            'operator_ids'        => 'required|array|min:1',
            'operator_ids.*'      => 'exists:users,id',
            'primary_operator_id' => 'required|exists:users,id',
            'notes'               => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        if (!$user->hasAnyRole(['surveillance_supervisor', 'department_supervisor', 'admin'])) {
            return redirect()->back()->with('error', 'You do not have permission to assign tasks.');
        }

        $allowedStates = [
            TaskState::SUBMITTED_TO_SURVEILLANCE,
            TaskState::ASSIGNED_TO_DEPARTMENT,
        ];

        if (!in_array($task->state, $allowedStates)) {
            return redirect()->back()->with('error', 'Cannot assign task in its current state.');
        }

        try {
            // Transition to ASSIGNED_TO_DEPARTMENT only if not already there
            if ($task->state === TaskState::SUBMITTED_TO_SURVEILLANCE) {
                $this->stateMachine->transition(
                    $task,
                    TaskState::ASSIGNED_TO_DEPARTMENT,
                    $user,
                    $validated['notes'] ?? null
                );
            }

            // Set primary operator in the tasks table
            $task->update(['assigned_to_user_id' => $validated['primary_operator_id']]);

            // Sync operators in the pivot table (replace existing)
            $syncData = [];
            foreach ($validated['operator_ids'] as $opId) {
                $syncData[$opId] = [
                    'assigned_at' => now(),
                    'is_primary'  => ($opId == $validated['primary_operator_id']),
                ];
            }
            $task->operators()->sync($syncData);

            return redirect()->back()->with('success', 'Task assigned to operators successfully.');
        } catch (\App\Domains\Shared\Exceptions\InvalidStateTransition $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Transition task to a new state
     */
    public function transition(Request $request, Task $task)
    {
        $validated = $request->validate([
            'new_state' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $newState = TaskState::from($validated['new_state']);

        try {
            $this->stateMachine->transition(
                $task,
                $newState,
                Auth::user(),
                $validated['notes'] ?? null
            );

            return redirect()
                ->route('tasks.show', $task)
                ->with('success', "Task transitioned to {$newState->label()}");
        } catch (\App\Domains\Shared\Exceptions\InvalidStateTransition $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get available state transitions for a task and user
     */
    private function getAvailableTransitions(Task $task, User $user): array
    {
        $available = [];

        // Get available transitions from the task model
        $transitions = $task->getAvailableTransitions();

        foreach ($transitions as $state) {
            if ($this->stateMachine->canUserTransition($task, $state, $user)) {
                $available[] = $state;
            }
        }

        return $available;
    }

    /**
     * Display task details for supervisors/managers/admins
     * Shows complete workflow and task information
     */
    public function supervisorView(Task $task)
    {
        // Check authorization
        $user = Auth::user();
        if (!$user->hasRole(['department_supervisor', 'surveillance_supervisor', 'job_officer', 'hq_supervisor', 'admin'])) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // Load relationships
        $task->load([
            'department',
            'operators',
            'createdBy',
            'monthlyPlan',
            'mpfmTest'
        ]);

        return view('tasks.supervisor-view', compact('task'));
    }
}
