<?php

namespace App\Http\Controllers;

use App\Domains\Task\Models\Task;
use App\Domains\Task\Services\TaskStateMachine;
use App\Enums\TaskState;
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
     */
    public function create()
    {
        $monthlyPlans = MonthlyPlan::all();
        $departments = Department::active()->get();
        $users = User::all();
        $oilStrings = OilString::all();
        $towers = Tower::all();

        return view('tasks.form', compact('monthlyPlans', 'departments', 'users', 'oilStrings', 'towers'));
    }

    /**
     * Store a newly created task in storage
     */
    public function store(Request $request)
    {
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
        $validated['state'] = TaskState::DRAFT->value;

        $task = Task::create($validated);

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task created successfully');
    }

    /**
     * Display the specified task
     */
    public function show(Task $task)
    {
        $task->load(['department', 'assignedTo', 'createdBy', 'monthlyPlan', 'stateHistory', 'slAHistory', 'operators']);

        $availableTransitions = [];
        if (Auth::user()) {
            $availableTransitions = $this->getAvailableTransitions($task, Auth::user());
        }

        return view('tasks.show', compact('task', 'availableTransitions'));
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

        foreach ($task->canTransitionTo() as $state) {
            if ($this->stateMachine->canUserTransition($task, $state, $user)) {
                $available[] = $state;
            }
        }

        return $available;
    }
}
