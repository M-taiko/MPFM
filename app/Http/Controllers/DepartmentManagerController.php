<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\PlanTask;
use App\Models\User;
use App\Enums\TaskState;
use App\Services\TaskAssignmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentManagerController extends Controller
{
    protected TaskAssignmentService $assignmentService;

    public function __construct(TaskAssignmentService $assignmentService)
    {
        $this->middleware(['auth', 'role:department_manager']);
        $this->middleware('verified');

        $this->assignmentService = $assignmentService;
    }

    /**
     * Show the department dashboard
     */
    public function dashboard(): View
    {
        $user = auth()->user();
        $department = $user->department;

        if (!$department) {
            abort(403, 'You are not assigned to any department.');
        }

        // Get tasks for this department with pagination
        $tasks = PlanTask::where('department_id', $department->id)
            ->with(['monthlyPlan', 'operators', 'towerModel', 'department'])
            ->orderBy('scheduled_date')
            ->paginate(20);

        // Count unassigned tasks in this department
        $unassignedCount = PlanTask::where('department_id', $department->id)
            ->whereNull('assigned_operator_id')
            ->count();

        // Get available operators in this department
        $operators = $this->assignmentService->getAvailableOperators($department->id);

        // Calculate statistics
        $stats = [
            'total' => PlanTask::where('department_id', $department->id)->count(),
            'completed' => PlanTask::where('department_id', $department->id)->where('state', TaskState::COMPLETED->value)->count(),
            'in_progress' => PlanTask::where('department_id', $department->id)->where('state', TaskState::IN_PROGRESS->value)->count(),
            'pending' => PlanTask::where('department_id', $department->id)->where('completion_status', 'pending')->count(),
        ];

        $stats['completion_percentage'] = $stats['total'] > 0
            ? round(($stats['completed'] / $stats['total']) * 100, 2)
            : 0;

        return view('department_manager.dashboard', compact(
            'department',
            'tasks',
            'unassignedCount',
            'operators',
            'stats'
        ));
    }

    /**
     * Show task assignment form
     */
    public function showTask(PlanTask $task): View
    {
        $user = auth()->user();
        $this->authorize('view', $task);

        // Get available operators for this task's department
        $operators = $this->assignmentService->getAvailableOperators($task->department_id);

        // Get currently assigned operators
        $assignedOperators = $task->operators()->get();

        return view('department_manager.task_detail', compact(
            'task',
            'operators',
            'assignedOperators'
        ));
    }

    /**
     * Assign an operator to a task
     */
    public function assignOperator(Request $request, PlanTask $task)
    {
        $user = auth()->user();

        // Authorization check
        $this->authorize('assignToOperator', $task);

        $validated = $request->validate([
            'operator_id' => 'required|exists:users,id',
            'is_primary' => 'boolean',
        ]);

        try {
            $operator = User::findOrFail($validated['operator_id']);

            // Use the assignment service
            $this->assignmentService->assignOperatorToTask(
                $task,
                $operator,
                $user,
                $validated['is_primary'] ?? true
            );

            return back()->with('success', "Operator {$operator->name} assigned successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['operator_id' => $e->getMessage()]);
        }
    }

    /**
     * Unassign an operator from a task
     */
    public function unassignOperator(Request $request, PlanTask $task)
    {
        $user = auth()->user();
        $this->authorize('assignToOperator', $task);

        $validated = $request->validate([
            'operator_id' => 'required|exists:users,id',
        ]);

        try {
            $operator = User::findOrFail($validated['operator_id']);

            $this->assignmentService->unassignOperatorFromTask($task, $operator, $user);

            return back()->with('success', "Operator {$operator->name} unassigned successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['operator_id' => $e->getMessage()]);
        }
    }

    /**
     * Clear all operators from a task
     */
    public function clearOperators(PlanTask $task)
    {
        $user = auth()->user();
        $this->authorize('assignToOperator', $task);

        try {
            $this->assignmentService->clearTaskOperators($task, $user);
            return back()->with('success', 'All operators cleared from task.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show reports for this department
     */
    public function reports(): View
    {
        $user = auth()->user();
        $department = $user->department;

        if (!$department) {
            abort(403, 'You are not assigned to any department.');
        }

        // Get tasks grouped by state
        $tasksByState = [];
        foreach (TaskState::cases() as $state) {
            $count = PlanTask::where('department_id', $department->id)
                ->where('state', $state)
                ->count();

            if ($count > 0) {
                $tasksByState[$state->label()] = $count;
            }
        }

        // Get tasks by completion status
        $tasksByStatus = PlanTask::where('department_id', $department->id)
            ->selectRaw('completion_status, COUNT(*) as count')
            ->groupBy('completion_status')
            ->pluck('count', 'completion_status')
            ->toArray();

        // Get overdue tasks
        $overdueTasks = PlanTask::where('department_id', $department->id)
            ->where('overdue_flag', true)
            ->count();

        // Get recent activity
        $recentActivity = PlanTask::where('department_id', $department->id)
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return view('department_manager.reports', compact(
            'department',
            'tasksByState',
            'tasksByStatus',
            'overdueTasks',
            'recentActivity'
        ));
    }

    /**
     * Bulk assign operators to tasks
     */
    public function bulkAssign(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'exists:plan_tasks,id',
            'operator_ids' => 'required|array|min:1',
            'operator_ids.*' => 'exists:users,id',
        ]);

        try {
            $result = $this->assignmentService->bulkAssignOperators(
                $validated['task_ids'],
                $validated['operator_ids'],
                $user
            );

            $message = "Bulk assignment completed. Assigned: {$result['assigned']}, Failed: {$result['failed']}";
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get operators for department (AJAX endpoint)
     */
    public function getOperators()
    {
        $user = auth()->user();
        $department = $user->department;

        if (!$department) {
            return response()->json(['error' => 'No department assigned'], 403);
        }

        $operators = $this->assignmentService->getAvailableOperators($department->id);

        return response()->json([
            'operators' => $operators->map(fn($op) => [
                'id' => $op->id,
                'name' => $op->name,
                'email' => $op->email,
            ]),
        ]);
    }
}
