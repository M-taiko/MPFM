<?php

namespace App\Http\Controllers;

use App\Models\PlanTask;
use App\Models\MonthlyPlan;
use App\Models\WellTest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperatorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Operator main dashboard
     */
    public function index()
    {
        $operator = auth()->user();

        // Today's tasks - using many-to-many relationship
        $todaysTasks = PlanTask::whereHas('operators', function($q) use ($operator) {
                $q->where('user_id', $operator->id);
            })
            ->today()
            ->with(['monthlyPlan', 'towerModel', 'oilString', 'operators'])
            ->orderBy('priority', 'desc')
            ->orderBy('scheduled_date', 'asc')
            ->get();

        // This week's tasks
        $weekTasks = PlanTask::whereHas('operators', function($q) use ($operator) {
                $q->where('user_id', $operator->id);
            })
            ->thisWeek()
            ->with(['monthlyPlan', 'towerModel', 'operators'])
            ->orderBy('scheduled_date', 'asc')
            ->get();

        // Pending tasks
        $pendingTasks = PlanTask::whereHas('operators', function($q) use ($operator) {
                $q->where('user_id', $operator->id);
            })
            ->pending()
            ->with(['monthlyPlan', 'towerModel', 'operators'])
            ->orderBy('scheduled_date', 'asc')
            ->limit(10)
            ->get();

        // Overdue tasks
        $overdueTasks = PlanTask::whereHas('operators', function($q) use ($operator) {
                $q->where('user_id', $operator->id);
            })
            ->where('scheduled_date', '<', today())
            ->where('completion_status', '!=', 'completed')
            ->with(['monthlyPlan', 'towerModel', 'operators'])
            ->orderBy('scheduled_date', 'asc')
            ->get();

        // Recent completed tasks (last 7 days)
        $recentCompleted = PlanTask::whereHas('operators', function($q) use ($operator) {
                $q->where('user_id', $operator->id);
            })
            ->completed()
            ->where('completed_at', '>=', now()->subDays(7))
            ->with(['monthlyPlan', 'towerModel', 'mpfmTest', 'operators'])
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        // Performance metrics
        $stats = [
            'today_total' => $todaysTasks->count(),
            'today_completed' => $todaysTasks->where('completion_status', 'completed')->count(),
            'week_total' => $weekTasks->count(),
            'week_completed' => $weekTasks->where('completion_status', 'completed')->count(),
            'pending_total' => PlanTask::where('assigned_operator_id', $operator->id)->pending()->count(),
            'overdue_total' => $overdueTasks->count(),
            'month_completed' => PlanTask::where('assigned_operator_id', $operator->id)
                ->completed()
                ->whereMonth('completed_at', now()->month)
                ->count(),
        ];

        // Performance metrics (last 30 days)
        $totalTasks = PlanTask::where('assigned_operator_id', $operator->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $completedTasks = PlanTask::where('assigned_operator_id', $operator->id)
            ->completed()
            ->where('completed_at', '>=', now()->subDays(30))
            ->count();
        $pendingTasksCount = PlanTask::where('assigned_operator_id', $operator->id)->pending()->count();
        $inProgressTasks = PlanTask::where('assigned_operator_id', $operator->id)
            ->where('completion_status', 'in_progress')
            ->count();
        $failedTasks = PlanTask::where('assigned_operator_id', $operator->id)
            ->where('completion_status', 'failed')
            ->count();
        $avgDuration = PlanTask::where('assigned_operator_id', $operator->id)
            ->completed()
            ->whereNotNull('duration_minutes')
            ->avg('duration_minutes');

        $performance = [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasksCount,
            'in_progress_tasks' => $inProgressTasks,
            'failed_tasks' => $failedTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
            'average_duration' => $avgDuration,
        ];

        return view('operator.dashboard', compact(
            'todaysTasks',
            'weekTasks',
            'pendingTasks',
            'overdueTasks',
            'recentCompleted',
            'stats',
            'performance'
        ));
    }

    /**
     * View all operator tasks with filters
     */
    public function tasks(Request $request)
    {
        $operator = auth()->user();

        $query = PlanTask::whereHas('operators', function($q) use ($operator) {
                $q->where('user_id', $operator->id);
            })
            ->with(['monthlyPlan', 'towerModel', 'oilString', 'operators']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('completion_status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by tower
        if ($request->filled('tower')) {
            $query->where('tower', $request->tower);
        }

        $tasks = $query->orderBy('scheduled_date', 'desc')->paginate(20);

        // Get filter options
        $towers = PlanTask::where('assigned_operator_id', $operator->id)
            ->distinct()
            ->pluck('tower')
            ->filter();

        return view('operator.tasks', compact('tasks', 'towers'));
    }

    /**
     * View task details
     */
    public function showTask(PlanTask $planTask)
    {
        // Ensure operator can only view their assigned tasks (multi-operator support)
        if (!$planTask->isAssignedTo(auth()->id())) {
            abort(403, 'You are not authorized to view this task.');
        }

        $planTask->load(['monthlyPlan', 'towerModel', 'oilString', 'mpfmTest', 'assigner']);

        return view('operator.task_detail', compact('planTask'));
    }

    /**
     * Start a task (marks as in_progress and redirects to unified upload page)
     */
    public function startTask(PlanTask $planTask)
    {
        // Check if operator is assigned via pivot table
        if (!$planTask->isAssignedTo(auth()->id())) {
            abort(403, 'You are not assigned to this task.');
        }

        if ($planTask->completion_status !== 'pending') {
            return back()->with('error', 'Task has already been started or completed.');
        }

        try {
            $planTask->update(['completion_status' => 'in_progress']);

            // Redirect to centralized upload page at /upload with task_id parameter
            return redirect()->route('upload.form', ['task_id' => $planTask->id])
                ->with('success', 'Task started successfully. Please upload the log file.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start task: ' . $e->getMessage());
        }
    }

    /**
     * Complete a task
     */
    public function completeTask(PlanTask $planTask)
    {
        // Check if operator is assigned via pivot table
        if (!$planTask->isAssignedTo(auth()->id())) {
            abort(403, 'You are not assigned to this task.');
        }

        if ($planTask->completion_status === 'completed') {
            return back()->with('error', 'Task has already been completed.');
        }

        try {
            $planTask->update([
                'completion_status' => 'completed',
                'completed_at' => now(),
            ]);

            return back()->with('success', 'Task completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to complete task: ' . $e->getMessage());
        }
    }

    /**
     * Upload log and start test (with auto-fill from plan)
     */
    public function uploadLog(PlanTask $planTask)
    {
        // Check if operator is assigned via pivot table (multi-operator support)
        if (!$planTask->isAssignedTo(auth()->id())) {
            abort(403);
        }

        // Auto-fill data from plan task
        $autoFillData = [
            'well' => $planTask->well_full_name,
            'tower' => $planTask->tower,
            'string' => $planTask->string_number,
            'flow_route' => $planTask->flow_route,
            'reservoir_layer' => $planTask->reservoir_layer,
            'test_location' => $planTask->test_location,
            'mpfm_type' => $planTask->mpfm_type,
            'dhsv_type' => $planTask->dhsv_type,
            'category' => $planTask->category,
            'required_whfp' => $planTask->required_whfp,
            'scheduled_date' => $planTask->scheduled_date,
            'operator' => auth()->user()->name,
            'plan_task_id' => $planTask->id,
        ];

        return view('operator.upload_log', compact('planTask', 'autoFillData'));
    }

    /**
     * Calendar view of tasks
     */
    public function calendar(Request $request)
    {
        $operator = auth()->user();

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $tasks = PlanTask::whereHas('operators', function($q) use ($operator) {
                $q->where('user_id', $operator->id);
            })
            ->whereYear('scheduled_date', $year)
            ->whereMonth('scheduled_date', $month)
            ->with(['monthlyPlan', 'towerModel', 'operators'])
            ->get();

        return view('operator.calendar', compact('tasks', 'month', 'year'));
    }

    /**
     * Task history
     */
    public function history(Request $request)
    {
        $operator = auth()->user();

        $query = PlanTask::whereHas('operators', function($q) use ($operator) {
                $q->where('user_id', $operator->id);
            })
            ->completed()
            ->with(['monthlyPlan', 'towerModel', 'mpfmTest', 'operators']);

        if ($request->filled('month')) {
            $query->whereMonth('completed_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('completed_at', $request->year);
        }

        $completedTasks = $query->orderBy('completed_at', 'desc')->paginate(20);

        return view('operator.history', compact('completedTasks'));
    }

    /**
     * Store uploaded log file and complete the task
     */
    public function storeUploadLog(\Illuminate\Http\Request $request, PlanTask $planTask)
    {
        // Check if operator is assigned
        if (!$planTask->isAssignedTo(auth()->id())) {
            abort(403, 'You are not assigned to this task.');
        }

        // Validate the uploaded file
        $request->validate([
            'log_file' => 'required|file|mimes:txt,csv,pdf,jpg,jpeg,png|max:5120', // 5MB max
            'remarks' => 'nullable|string|max:1000',
        ]);

        try {
            // Store the file
            if ($request->hasFile('log_file')) {
                $file = $request->file('log_file');
                $filename = $planTask->id . '_' . auth()->id() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/task_logs', $filename, 'public');

                // Update task with file info
                $planTask->update([
                    'completion_status' => 'completed',
                    'completed_at' => now(),
                    'operator_notes' => $request->remarks,
                ]);

                return redirect()->route('operator.dashboard')
                    ->with('success', 'Task completed successfully! Log file uploaded.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * View all tasks of the month with filters
     */
    public function monthlyTasks(\Illuminate\Http\Request $request)
    {
        $operator = auth()->user();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $status = $request->get('status', ''); // all, pending, in_progress, completed

        $query = PlanTask::whereHas('operators', function($q) use ($operator) {
                $q->where('user_id', $operator->id);
            })
            ->whereYear('scheduled_date', $year)
            ->whereMonth('scheduled_date', $month)
            ->with(['monthlyPlan', 'towerModel', 'oilString', 'operators']);

        // Filter by status
        if ($status && $status !== 'all') {
            $query->where('completion_status', $status);
        }

        $tasks = $query->orderBy('scheduled_date', 'asc')
            ->orderBy('priority', 'desc')
            ->paginate(20);

        // Get available months for dropdown
        $availableMonths = collect(range(1, 12))->map(function ($m) {
            return ['value' => $m, 'label' => \Carbon\Carbon::createFromDate(2026, $m, 1)->format('F')];
        });

        // Get available years
        $availableYears = collect(range(2024, 2027))->reverse();

        // Count by status
        $statusCounts = PlanTask::whereHas('operators', function($q) use ($operator) {
                $q->where('user_id', $operator->id);
            })
            ->whereYear('scheduled_date', $year)
            ->whereMonth('scheduled_date', $month)
            ->selectRaw('completion_status, COUNT(*) as count')
            ->groupBy('completion_status')
            ->pluck('count', 'completion_status');

        return view('operator.monthly_tasks', compact(
            'tasks',
            'month',
            'year',
            'status',
            'availableMonths',
            'availableYears',
            'statusCounts'
        ));
    }
}
