<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DepartmentTaskData;
use App\Models\PlanTask;
use App\Models\User;
use App\Domains\Task\Models\Task;
use App\Enums\TaskState;
use App\Services\DepartmentConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show tasks for a specific department (for department managers)
     */
    public function index($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        // Check if user is admin or department manager of this department
        $this->authorizeDepartmentAccess($department);

        // Get unassigned and assigned tasks for this department
        $tasks = Task::where('department_id', $department->id)
            ->with(['department', 'oilString', 'tower', 'createdBy', 'operators'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('departments.tasks', compact('department', 'tasks'));
    }

    /**
     * Show assignment modal data (AJAX)
     */
    public function getAssignmentModal(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
        ]);

        $task = Task::findOrFail($request->task_id);

        // Verify user has access to this task's department
        $this->authorizeDepartmentAccess($task->department);

        // Get available operators in this department
        $operators = User::where('department_id', $task->department_id)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['operator', 'mpfm_operator']))
            ->select('id', 'name', 'email')
            ->get();

        // Get already assigned operators
        $assignedOperators = $task->operators()->pluck('user_id')->toArray();

        return response()->json([
            'success' => true,
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'department_id' => $task->department_id,
            ],
            'operators' => $operators,
            'assignedOperators' => $assignedOperators,
        ]);
    }

    /**
     * Assign operator to task (AJAX)
     */
    public function assignOperator(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'operator_id' => 'required|exists:users,id',
            'is_primary' => 'nullable|boolean',
        ]);

        $task = Task::findOrFail($request->task_id);
        $operator = User::findOrFail($request->operator_id);

        // Verify task belongs to department manager's department
        $this->authorizeDepartmentAccess($task->department);

        // Verify operator is in the same department
        if ($operator->department_id !== $task->department_id) {
            return response()->json([
                'success' => false,
                'message' => 'Operator is not in the same department as the task.',
            ], 403);
        }

        // Check if operator already assigned
        if ($task->operators()->where('user_id', $operator->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Operator is already assigned to this task.',
            ], 422);
        }

        // Assign operator
        $task->operators()->attach($operator->id, [
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
            'is_primary' => $request->boolean('is_primary', false),
        ]);

        // Update task state if it was in DRAFT
        if ($task->state->value === 'draft') {
            $task->update(['state' => 'assigned_to_department']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Operator assigned successfully.',
            'operator' => [
                'id' => $operator->id,
                'name' => $operator->name,
            ],
        ]);
    }

    /**
     * Remove operator from task (AJAX)
     */
    public function removeOperator(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'operator_id' => 'required|exists:users,id',
        ]);

        $task = Task::findOrFail($request->task_id);

        // Verify authorization
        $this->authorizeDepartmentAccess($task->department);

        // Remove operator
        $task->operators()->detach($request->operator_id);

        return response()->json([
            'success' => true,
            'message' => 'Operator removed successfully.',
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // AJAX: return department-specific fields as JSON
    // GET /tasks/api/department-fields/{code}
    // ─────────────────────────────────────────────────────────
    public function getFields(string $code)
    {
        $config = DepartmentConfig::get($code);

        if (!$config) {
            return response()->json(['fields' => [], 'label' => '', 'color' => '#64748b', 'icon' => 'fa-building']);
        }

        return response()->json([
            'fields'      => $config['fields'],
            'label'       => $config['label'],
            'color'       => $config['color'],
            'bg'          => $config['bg'] ?? '#f8fafc',
            'icon'        => $config['icon'],
            'description' => $config['description'],
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // Dashboard for a specific department
    // GET /dept/{code}/dashboard
    // ─────────────────────────────────────────────────────────
    public function dashboard(string $code)
    {
        $config = DepartmentConfig::get($code);
        if (!$config) abort(404, 'Department not configured.');

        $department = Department::whereRaw('UPPER(code) = ?', [strtoupper($code)])->firstOrFail();

        // ── New Tasks (Task model) assigned to this department ──
        $newTasksQuery = Task::where('department_id', $department->id);

        // ── Legacy PlanTasks still in use ──
        $legacyQuery = PlanTask::where('department_id', $department->id);

        // ── Stats combining both ──
        $newTotal     = (clone $newTasksQuery)->count();
        $legacyTotal  = (clone $legacyQuery)->count();

        $stats = [
            'total' => $newTotal + $legacyTotal,

            'pending' => (clone $newTasksQuery)->whereIn('state', [
                                TaskState::ASSIGNED_TO_DEPARTMENT->value,
                                TaskState::SUBMITTED_TO_SURVEILLANCE->value,
                            ])->count()
                       + (clone $legacyQuery)->where('completion_status', 'pending')->count(),

            'in_progress' => (clone $newTasksQuery)->where('state', TaskState::IN_PROGRESS->value)->count()
                           + (clone $legacyQuery)->where('completion_status', 'in_progress')->count(),

            'completed' => (clone $newTasksQuery)->where('state', TaskState::COMPLETED->value)->count()
                         + (clone $legacyQuery)->where('completion_status', 'completed')->count(),

            'overdue' => (clone $newTasksQuery)
                            ->whereNotIn('state', [TaskState::COMPLETED->value])
                            ->whereNotNull('expected_completion_at')
                            ->where('expected_completion_at', '<', now())->count()
                       + (clone $legacyQuery)
                            ->where('completion_status', '!=', 'completed')
                            ->where('scheduled_date', '<', today())->count(),
        ];
        $stats['completion_rate'] = $stats['total'] > 0
            ? round(($stats['completed'] / $stats['total']) * 100)
            : 0;

        // ── Recent Tasks: new Tasks first, then legacy ──
        $recentNewTasks    = (clone $newTasksQuery)
            ->with(['createdBy', 'tower', 'oilString', 'department'])
            ->whereIn('state', [
                TaskState::ASSIGNED_TO_DEPARTMENT->value,
                TaskState::IN_PROGRESS->value,
                TaskState::DEPARTMENT_REVIEWED->value,
            ])
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get()
            ->map(fn($t) => (object)[
                'id'               => $t->id,
                'is_new_task'      => true,
                'well_full_name'   => $t->title,
                'tower'            => null,
                'towerModel'       => $t->tower,
                'oilString'        => $t->oilString,
                'scheduled_date'   => $t->expected_completion_at,
                'priority'         => $t->priority ?? 'medium',
                'completion_status'=> match($t->state->value) {
                    TaskState::ASSIGNED_TO_DEPARTMENT->value => 'pending',
                    TaskState::IN_PROGRESS->value            => 'in_progress',
                    TaskState::COMPLETED->value              => 'completed',
                    default                                  => 'pending',
                },
            ]);

        $recentLegacyTasks = (clone $legacyQuery)
            ->with(['assignedOperator', 'towerModel', 'oilString'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn($t) => (object)[
                'id'               => $t->id,
                'is_new_task'      => false,
                'well_full_name'   => $t->well_full_name ?? ('Task #' . $t->id),
                'tower'            => $t->tower,
                'towerModel'       => $t->towerModel,
                'oilString'        => $t->oilString,
                'scheduled_date'   => $t->scheduled_date,
                'priority'         => $t->priority ?? 'medium',
                'completion_status'=> $t->completion_status ?? 'pending',
            ]);

        $recentTasks = $recentNewTasks->concat($recentLegacyTasks)->sortByDesc('completion_status');

        // ── Priority breakdown (new tasks) ──
        $byPriority = (clone $newTasksQuery)
            ->select('priority', DB::raw('COUNT(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        // ── 6-month completion trend ──
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trend[] = [
                'month' => $month->format('M Y'),
                'count' => (clone $newTasksQuery)
                               ->where('state', TaskState::COMPLETED->value)
                               ->whereYear('updated_at', $month->year)
                               ->whereMonth('updated_at', $month->month)
                               ->count()
                         + (clone $legacyQuery)
                               ->where('completion_status', 'completed')
                               ->whereYear('completed_at', $month->year)
                               ->whereMonth('completed_at', $month->month)
                               ->count(),
            ];
        }

        return view('departments.dept_dashboard', compact(
            'code', 'config', 'department', 'stats', 'recentTasks', 'byPriority', 'trend'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // Task list for a specific department
    // GET /dept/{code}/tasks
    // ─────────────────────────────────────────────────────────
    public function deptTasks(string $code, Request $request)
    {
        $config = DepartmentConfig::get($code);
        if (!$config) abort(404);

        $department = Department::whereRaw('UPPER(code) = ?', [strtoupper($code)])->firstOrFail();

        // New Tasks (Task model) assigned/in-progress for this department
        $newTasksQuery = Task::where('department_id', $department->id)
            ->whereIn('state', [
                TaskState::ASSIGNED_TO_DEPARTMENT->value,
                TaskState::IN_PROGRESS->value,
                TaskState::DEPARTMENT_REVIEWED->value,
                TaskState::SURVEILLANCE_REVIEWED->value,
                TaskState::JOB_OFFICER_APPROVED->value,
                TaskState::COMPLETED->value,
            ])
            ->with(['createdBy', 'tower', 'oilString', 'assignedTo']);

        if ($request->filled('priority')) {
            $newTasksQuery->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $newTasksQuery->where('title', 'like', "%{$s}%");
        }

        // Operators in this department for the assign modal
        $deptOperators = User::where('department_id', $department->id)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['operator', 'mpfm_operator', 'department_supervisor']))
            ->orderBy('name')
            ->get(['id', 'name']);

        $newTasks = $newTasksQuery->orderByDesc('updated_at')->get()
            ->map(fn($t) => (object)[
                'id'               => $t->id,
                'is_new_task'      => true,
                'task_state'       => $t->state->value,
                'well_full_name'   => $t->title,
                'towerModel'       => $t->tower,
                'oilString'        => $t->oilString,
                'assignedOperator' => $t->assignedTo,
                'assigned_to_user_id' => $t->assigned_to_user_id,
                'department_code'  => $code,
                'scheduled_date'   => $t->expected_completion_at,
                'priority'         => $t->priority ?? 'medium',
                'completion_status'=> match($t->state->value) {
                    TaskState::ASSIGNED_TO_DEPARTMENT->value => 'pending',
                    TaskState::IN_PROGRESS->value            => 'in_progress',
                    TaskState::COMPLETED->value              => 'completed',
                    default                                  => 'pending',
                },
                'updated_at'       => $t->updated_at,
            ]);

        // Legacy PlanTasks
        $legacyQuery = PlanTask::where('department_id', $department->id)
            ->with(['assignedOperator', 'towerModel', 'oilString']);

        if ($request->filled('status')) {
            $legacyQuery->where('completion_status', $request->status);
        }
        if ($request->filled('priority')) {
            $legacyQuery->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $legacyQuery->where(function ($q) use ($s) {
                $q->where('well_full_name', 'like', "%{$s}%")
                  ->orWhere('well_number', 'like', "%{$s}%");
            });
        }

        $legacyTasks = $legacyQuery->orderByDesc('scheduled_date')->get()
            ->map(fn($t) => (object)[
                'id'               => $t->id,
                'is_new_task'      => false,
                'well_full_name'   => $t->well_full_name ?? ('Task #' . $t->id),
                'towerModel'       => $t->towerModel,
                'oilString'        => $t->oilString,
                'assignedOperator' => $t->assignedOperator,
                'scheduled_date'   => $t->scheduled_date,
                'priority'         => $t->priority ?? 'medium',
                'completion_status'=> $t->completion_status ?? 'pending',
                'updated_at'       => $t->updated_at,
            ]);

        // Merge and paginate manually
        $allTasks = $newTasks->concat($legacyTasks)->sortByDesc('updated_at');

        // Simple pagination
        $page     = $request->get('page', 1);
        $perPage  = 20;
        $tasks    = new \Illuminate\Pagination\LengthAwarePaginator(
            $allTasks->forPage($page, $perPage),
            $allTasks->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('departments.dept_tasks', compact('code', 'config', 'department', 'tasks', 'deptOperators'));
    }

    // ─────────────────────────────────────────────────────────
    // Show completion form
    // GET /dept/complete/{planTask}
    // ─────────────────────────────────────────────────────────
    public function showCompletion(PlanTask $planTask)
    {
        if (!$planTask->isAssignedTo(Auth::id()) &&
            !Auth::user()->hasRole(['admin', 'department_manager', 'department_supervisor'])) {
            abort(403, 'You are not assigned to this task.');
        }

        if ($planTask->completion_status === 'completed') {
            return redirect()->route('dept.result', $planTask)
                ->with('info', 'This task is already completed.');
        }

        $planTask->load(['monthlyPlan', 'towerModel', 'oilString', 'department', 'operators']);

        $code   = strtoupper($planTask->department->code ?? 'MPFM');
        $config = DepartmentConfig::get($code);

        if (!$config) abort(404, 'Department configuration not found.');

        // MPFM → redirect to existing upload flow
        if (($config['completion_type'] ?? 'form') === 'mpfm_upload') {
            return redirect()->route('operator.tasks.upload', $planTask);
        }

        $completionFields = DepartmentConfig::getCompletionFields($code);
        $prefill = is_array($planTask->type_specific_data) ? $planTask->type_specific_data : [];

        return view('departments.completion.form', compact(
            'planTask', 'code', 'config', 'completionFields', 'prefill'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // Store completion
    // POST /dept/complete/{planTask}
    // ─────────────────────────────────────────────────────────
    public function storeCompletion(Request $request, PlanTask $planTask)
    {
        if (!$planTask->isAssignedTo(Auth::id()) &&
            !Auth::user()->hasRole(['admin', 'department_manager', 'department_supervisor'])) {
            abort(403);
        }

        $planTask->load('department');
        $code = strtoupper($planTask->department->code ?? 'MPFM');

        $request->validate([
            'completion_notes' => 'nullable|string|max:2000',
            'data'             => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $planTask, $code) {
            DepartmentTaskData::updateOrCreate(
                ['plan_task_id' => $planTask->id],
                [
                    'department_code'      => $code,
                    'data'                 => $request->input('data', []),
                    'completed_by_user_id' => Auth::id(),
                    'completion_notes'     => $request->completion_notes,
                    'completed_at'         => now(),
                ]
            );

            $planTask->update([
                'completion_status' => 'completed',
                'completed_at'      => now(),
                'operator_notes'    => $request->completion_notes,
            ]);
        });

        return redirect()->route('dept.result', $planTask)
            ->with('success', 'Task completed successfully!');
    }

    // ─────────────────────────────────────────────────────────
    // Completion result page
    // GET /dept/result/{planTask}
    // ─────────────────────────────────────────────────────────
    public function completionResult(PlanTask $planTask)
    {
        $planTask->load(['monthlyPlan', 'towerModel', 'oilString', 'department', 'operators']);

        $code   = strtoupper($planTask->department->code ?? 'MPFM');
        $config = DepartmentConfig::get($code) ?? [];

        $taskData         = DepartmentTaskData::where('plan_task_id', $planTask->id)->first();
        $completionFields = DepartmentConfig::getCompletionFields($code);

        return view('departments.completion.result', compact(
            'planTask', 'code', 'config', 'taskData', 'completionFields'
        ));
    }

    /**
     * Check if user has access to department
     */
    private function authorizeDepartmentAccess(Department $department)
    {
        $user = auth()->user();

        // Allow admin access
        if ($user->hasRole('admin')) {
            return true;
        }

        // Allow department manager for their own department
        if ($user->hasRole('department_manager') && $user->department_id === $department->id) {
            return true;
        }

        // Deny access
        abort(403, 'Unauthorized access to this department.');
    }
}
