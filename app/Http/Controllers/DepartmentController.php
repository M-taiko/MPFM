<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('department-access')->except(['dashboard', 'tasks', 'operators', 'reports']);
    }

    /**
     * Display department dashboard with statistics
     */
    public function dashboard(Department $department)
    {
        // Check access
        $this->checkDepartmentAccess($department);

        // Get department statistics
        $totalTasks = $department->planTasks()->count();
        $completedTasks = $department->planTasks()->where('completion_status', 'completed')->count();
        $inProgressTasks = $department->planTasks()->where('completion_status', 'in_progress')->count();
        $pendingTasks = $department->planTasks()->where('completion_status', 'pending')->count();
        $failedTasks = $department->planTasks()->where('completion_status', 'failed')->count();

        // Calculate completion percentage
        $completionPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        // Get recent tasks
        $recentTasks = $department->planTasks()
            ->with(['monthlyPlan', 'operators', 'slaHistory'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        // Get task by state breakdown
        $tasksByState = $department->planTasks()
            ->select('state')
            ->selectRaw('count(*) as count')
            ->groupBy('state')
            ->get()
            ->pluck('count', 'state')
            ->toArray();

        // Get team members (operators in this department)
        $teamMembers = User::where('department_id', $department->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'operator');
            })
            ->withCount('planTaskOperators')
            ->get();

        return view('departments.dashboard', compact(
            'department',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'pendingTasks',
            'failedTasks',
            'completionPercentage',
            'recentTasks',
            'tasksByState',
            'teamMembers'
        ));
    }

    /**
     * Display all tasks for the department
     */
    public function tasks(Department $department, Request $request)
    {
        // Check access
        $this->checkDepartmentAccess($department);

        $query = $department->planTasks()
            ->with(['monthlyPlan', 'operators', 'primaryOperator', 'slaHistory', 'department']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('completion_status', $request->status);
        }

        // Filter by state
        if ($request->has('state') && $request->state !== '') {
            $query->where('state', $request->state);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('well_full_name', 'like', $search)
                    ->orWhere('well_number', 'like', $search)
                    ->orWhere('sno', 'like', $search);
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tasks = $query->paginate(20);

        // Get filter options for view
        $statuses = ['pending', 'in_progress', 'completed', 'failed'];
        $priorities = ['top', 'high', 'normal', 'low'];

        return view('departments.tasks', compact(
            'department',
            'tasks',
            'statuses',
            'priorities'
        ));
    }

    /**
     * Display team members and operators
     */
    public function operators(Department $department, Request $request)
    {
        // Check access - only admin and department managers can view/manage operators
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('department_manager')) {
            abort(403, 'Unauthorized access to department operators management.');
        }

        $this->checkDepartmentAccess($department);

        // Get all operators in this department
        $operators = User::where('department_id', $department->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'operator');
            })
            ->with(['planTaskOperators' => function ($query) {
                $query->where('completion_status', '!=', 'completed');
            }])
            ->withCount([
                'planTaskOperators as total_assigned',
                'planTaskOperators as completed_tasks' => function ($query) {
                    $query->where('completion_status', 'completed');
                }
            ])
            ->paginate(15);

        // Get available operators not yet in this department
        $availableOperators = User::where('department_id', '!=', $department->id)
            ->orWhereNull('department_id')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'operator');
            })
            ->get();

        return view('departments.operators', compact(
            'department',
            'operators',
            'availableOperators'
        ));
    }

    /**
     * Display department reports and analytics
     */
    public function reports(Department $department, Request $request)
    {
        // Check access
        $this->checkDepartmentAccess($department);

        $period = $request->get('period', '30'); // days

        // Task statistics
        $totalTasks = $department->planTasks()->count();
        $completedTasks = $department->planTasks()->where('completion_status', 'completed')->count();
        $failedTasks = $department->planTasks()->where('completion_status', 'failed')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

        // SLA performance
        $overdueTasks = $department->planTasks()
            ->where('overdue_flag', true)
            ->count();

        // Tasks by priority
        $tasksByPriority = $department->planTasks()
            ->select('priority')
            ->selectRaw('count(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        // Operator performance
        $operatorPerformance = User::where('department_id', $department->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'operator');
            })
            ->with(['planTaskOperators' => function ($query) {
                $query->where('completion_status', 'completed');
            }])
            ->get()
            ->map(function ($operator) {
                return [
                    'name' => $operator->name,
                    'email' => $operator->email,
                    'completed_tasks' => $operator->planTaskOperators->count(),
                    'total_assigned' => $operator->planTaskOperators()->count(),
                ];
            });

        // Recent activities
        $recentActivities = $department->planTasks()
            ->with(['stateHistories' => function ($query) {
                $query->latest('created_at')->limit(5);
            }])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return view('departments.reports', compact(
            'department',
            'totalTasks',
            'completedTasks',
            'failedTasks',
            'completionRate',
            'overdueTasks',
            'tasksByPriority',
            'operatorPerformance',
            'recentActivities',
            'period'
        ));
    }

    /**
     * Check if user has access to the department
     */
    private function checkDepartmentAccess(Department $department)
    {
        $user = auth()->user();

        // Admin can access all departments
        if ($user->hasRole('admin')) {
            return true;
        }

        // Department manager can only access their own department
        if ($user->hasRole('department_manager')) {
            if ($user->department_id !== $department->id) {
                abort(403, 'You do not have access to this department.');
            }
            return true;
        }

        // Operator can only access their own department
        if ($user->hasRole('operator')) {
            if ($user->department_id !== $department->id) {
                abort(403, 'You do not have access to this department.');
            }
            return true;
        }

        // Other roles cannot access department pages
        abort(403, 'Unauthorized access to department management.');
    }

    /**
     * Assign operator to department (admin/manager only)
     */
    public function assignOperator(Department $department, Request $request)
    {
        // Check access - only admin and department managers
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('department_manager')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->checkDepartmentAccess($department);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($validated['user_id']);

        // Verify user is an operator
        if (!$user->hasRole('operator')) {
            return response()->json(['error' => 'User must be an operator'], 400);
        }

        // Update user's department
        $user->update(['department_id' => $department->id]);

        return response()->json(['message' => 'Operator assigned successfully']);
    }
}
