<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

/**
 * AdminDashboardController
 *
 * Provides comprehensive admin dashboard with system-wide visibility
 * and management capabilities. Admins can:
 * - View all users, departments, plans, and tasks
 * - Manage all system roles and permissions
 * - Override any workflow or state transitions
 * - Access all reports and analytics
 * - Monitor system health and usage statistics
 */
class AdminDashboardController extends Controller
{
    /**
     * Show admin dashboard with system overview
     */
    public function dashboard()
    {
        $this->authorize('admin-access');

        $stats = [
            'total_users' => User::count(),
            'total_departments' => Department::count(),
            'active_departments' => Department::where('is_active', true)->count(),
            'total_plans' => MonthlyPlan::count(),
            'active_plans' => MonthlyPlan::where('status', 'active')->count(),
            'total_tasks' => PlanTask::count(),
            'completed_tasks' => PlanTask::where('state', 'completed')->count(),
            'in_progress_tasks' => PlanTask::where('state', 'in_progress')->count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
        ];

        $recentUsers = User::latest()->take(10)->get();
        $recentPlans = MonthlyPlan::latest()->take(10)->get();
        $recentTasks = PlanTask::latest()->take(10)->get();

        $departmentBreakdown = Department::withCount(['tasks', 'users'])
            ->get()
            ->map(function ($dept) {
                return [
                    'name' => $dept->name,
                    'code' => $dept->code,
                    'tasks' => $dept->tasks_count,
                    'users' => $dept->users_count,
                    'completed' => $dept->tasks()->where('state', 'completed')->count(),
                ];
            });

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentPlans', 'recentTasks', 'departmentBreakdown'));
    }

    /**
     * Show all users with management interface
     */
    public function users()
    {
        $this->authorize('admin-access');

        $users = User::with('roles', 'department')->paginate(50);

        return view('admin.users', compact('users'));
    }

    /**
     * Show all departments with management interface
     */
    public function departments()
    {
        $this->authorize('admin-access');

        $departments = Department::with('users')
            ->withCount('tasks', 'users')
            ->paginate(25);

        return view('admin.departments', compact('departments'));
    }

    /**
     * Show all plans with management interface
     */
    public function plans()
    {
        $this->authorize('admin-access');

        $plans = MonthlyPlan::with('tasks', 'createdBy')
            ->withCount('tasks')
            ->paginate(25);

        return view('admin.plans', compact('plans'));
    }

    /**
     * Show all tasks with management interface
     */
    public function tasks()
    {
        $this->authorize('admin-access');

        $tasks = PlanTask::with('plan', 'department', 'assignedOperators')
            ->paginate(50);

        return view('admin.tasks', compact('tasks'));
    }

    /**
     * Show roles and permissions management
     */
    public function roles()
    {
        $this->authorize('admin-access');

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('admin.roles', compact('roles', 'permissions'));
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user)
    {
        $this->authorize('admin-access');

        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->assignRole($validated['role']);

        return back()->with('success', "Role '{$validated['role']}' assigned to {$user->name}");
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request, User $user)
    {
        $this->authorize('admin-access');

        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->removeRole($validated['role']);

        return back()->with('success', "Role '{$validated['role']}' removed from {$user->name}");
    }

    /**
     * Override task state transition (admin only)
     */
    public function overrideTaskState(Request $request, PlanTask $task)
    {
        $this->authorize('admin-access');

        $validated = $request->validate([
            'state' => 'required|string',
        ]);

        $task->update(['state' => $validated['state']]);

        return back()->with('success', "Task state updated to {$validated['state']}");
    }

    /**
     * Get system statistics
     */
    public function statistics()
    {
        $this->authorize('admin-access');

        $stats = [
            'users' => User::count(),
            'departments' => Department::count(),
            'plans' => MonthlyPlan::count(),
            'tasks' => PlanTask::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'task_completion_rate' => $this->calculateCompletionRate(),
            'user_by_role' => $this->getUsersByRole(),
            'tasks_by_state' => $this->getTasksByState(),
            'tasks_by_department' => $this->getTasksByDepartment(),
        ];

        return view('admin.statistics', compact('stats'));
    }

    /**
     * Calculate overall task completion rate
     */
    private function calculateCompletionRate(): float
    {
        $total = PlanTask::count();
        if ($total === 0) {
            return 0;
        }

        $completed = PlanTask::where('state', 'completed')->count();

        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get user distribution by role
     */
    private function getUsersByRole(): array
    {
        $roles = Role::all();
        $distribution = [];

        foreach ($roles as $role) {
            $distribution[$role->name] = $role->users()->count();
        }

        return $distribution;
    }

    /**
     * Get task distribution by state
     */
    private function getTasksByState(): array
    {
        return PlanTask::select('state')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('state')
            ->pluck('count', 'state')
            ->toArray();
    }

    /**
     * Get task distribution by department
     */
    private function getTasksByDepartment(): array
    {
        return PlanTask::select('department_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('department_id')
            ->with('department')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->department?->name ?? 'Unassigned' => $item->count];
            })
            ->toArray();
    }
}
