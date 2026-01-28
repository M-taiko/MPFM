<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Domains\Task\Models\Task;
use Illuminate\Http\Request;

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
