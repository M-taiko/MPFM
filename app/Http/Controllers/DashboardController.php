<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WellTest;
use App\Models\User;
use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use App\Models\Department;
use App\Services\MPFMCalculationService;

class DashboardController extends Controller
{
    protected $calculationService;

    public function __construct(MPFMCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    public function index()
    {
        // Legacy MPFM test data
        $wellTests = WellTest::latest()->limit(5)->get();
        $totalTests = WellTest::count();
        $totalUsers = User::count();
        $recentTests = WellTest::where('created_at', '>=', now()->subDays(7))->count();

        // Get tests grouped by client for chart
        $testsByClient = WellTest::selectRaw('client, COUNT(*) as count')
            ->groupBy('client')
            ->whereNotNull('client')
            ->get();

        // Get tests over time for chart (last 30 days)
        $testsOverTime = WellTest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate summary metrics from recent tests
        $summaryMetrics = $this->calculateSummaryMetrics($wellTests);

        // Multi-Department Workflow Statistics
        $workflowStats = $this->getWorkflowStatistics();

        // Department breakdown
        $departmentStats = $this->getDepartmentBreakdown();

        // Task state distribution
        $taskStateDistribution = $this->getTaskStateDistribution();

        // User statistics
        $userStats = $this->getUserStatistics();

        // Recent activity
        $recentActivity = $this->getRecentActivity();

        // Plan progress data
        $planProgress = $this->getPlanProgressData();

        return view('dashboard', compact(
            'wellTests', 'totalTests', 'totalUsers', 'recentTests',
            'testsByClient', 'testsOverTime', 'summaryMetrics',
            'workflowStats', 'departmentStats', 'taskStateDistribution',
            'userStats', 'recentActivity', 'planProgress'
        ));
    }

    /**
     * Get comprehensive workflow statistics
     */
    private function getWorkflowStatistics()
    {
        $totalTasks = PlanTask::count();
        $completedTasks = PlanTask::where('state', 'completed')->count();
        $inProgressTasks = PlanTask::where('state', 'in_progress')->count();
        $pendingTasks = $totalTasks - $completedTasks - $inProgressTasks;

        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'in_progress_tasks' => $inProgressTasks,
            'pending_tasks' => $pendingTasks,
            'completion_rate' => $completionRate,
            'total_plans' => MonthlyPlan::count(),
            'active_plans' => MonthlyPlan::where('status', 'active')->orWhere('status', 'under_review')->count(),
        ];
    }

    /**
     * Get department-wise task breakdown
     */
    private function getDepartmentBreakdown()
    {
        $departments = Department::where('is_active', true)
            ->withCount('tasks')
            ->get()
            ->map(function ($dept) {
                $completed = $dept->tasks()->where('state', 'completed')->count();
                $total = $dept->tasks_count;
                $progress = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

                return [
                    'name' => $dept->name,
                    'code' => $dept->code,
                    'total_tasks' => $total,
                    'completed_tasks' => $completed,
                    'progress' => $progress,
                    'users_count' => $dept->users()->count(),
                ];
            });

        return $departments;
    }

    /**
     * Get task state distribution for pie chart
     */
    private function getTaskStateDistribution()
    {
        return PlanTask::selectRaw('state, COUNT(*) as count')
            ->groupBy('state')
            ->pluck('count', 'state')
            ->toArray();
    }

    /**
     * Get user statistics by role
     */
    private function getUserStatistics()
    {
        $usersByRole = [];

        $roles = ['admin', 'job_officer', 'surveillance_supervisor', 'department_manager', 'mpfm_operator', 'operator'];

        foreach ($roles as $role) {
            $count = User::role($role)->count();
            if ($count > 0) {
                $usersByRole[$role] = $count;
            }
        }

        return [
            'total_users' => User::count(),
            'users_by_role' => $usersByRole,
            'departments_count' => Department::where('is_active', true)->count(),
            'active_users' => User::whereHas('roles')->count(),
        ];
    }

    /**
     * Get recent activity for dashboard
     */
    private function getRecentActivity()
    {
        return [
            'recent_tasks' => PlanTask::with('plan', 'department')
                ->latest()
                ->limit(5)
                ->get(),
            'recent_plans' => MonthlyPlan::latest()
                ->limit(5)
                ->get(),
            'recent_users' => User::latest()
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Get plan progress data for chart
     */
    private function getPlanProgressData()
    {
        $plans = MonthlyPlan::with('tasks')
            ->get()
            ->map(function ($plan) {
                $total = $plan->tasks()->count();
                $completed = $plan->tasks()->where('state', 'completed')->count();
                $progress = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

                return [
                    'name' => $plan->plan_name,
                    'month' => $plan->plan_month,
                    'progress' => $progress,
                    'total_tasks' => $total,
                    'completed_tasks' => $completed,
                ];
            })
            ->sortByDesc('progress')
            ->take(10);

        return $plans;
    }

    public function jobOfficerReport()
    {
        // Job officer specific report logic
        return view('reports.job_officer');
    }

    public function reservoirEngineerReport()
    {
        // Reservoir engineer specific report logic
        return view('reports.reservoir_engineer');
    }

    public function productionSupervisorReport()
    {
        // Production supervisor specific report logic
        return view('reports.production_supervisor');
    }

    public function mpfmSupervisorReport()
    {
        // MPFM supervisor specific report logic
        return view('reports.mpfm_supervisor');
    }

    public function mpfmOperatorReport()
    {
        // MPFM operator specific report logic
        return view('reports.mpfm_operator');
    }

    private function calculateSummaryMetrics($wellTests)
    {
        $summary = [
            'total_oil_rate' => 0,
            'total_gas_rate' => 0,
            'total_water_rate' => 0,
            'avg_gor' => 0,
            'avg_bsw' => 0,
            'test_count' => 0,
        ];

        $gorSum = 0;
        $bswSum = 0;
        $validTestsCount = 0;

        foreach ($wellTests as $test) {
            $calculatedMetrics = $test->calculated_metrics ?? [];

            $oilRate = $calculatedMetrics['oil_rate'] ?? 0;
            $gasRate = $calculatedMetrics['gas_rate'] ?? 0;
            $waterRate = $calculatedMetrics['water_rate'] ?? 0;

            $summary['total_oil_rate'] += is_numeric($oilRate) ? $oilRate : 0;
            $summary['total_gas_rate'] += is_numeric($gasRate) ? $gasRate : 0;
            $summary['total_water_rate'] += is_numeric($waterRate) ? $waterRate : 0;

            // Calculate GOR and BSW if possible
            if (is_numeric($oilRate) && $oilRate > 0) {
                $gor = is_numeric($gasRate) ? $gasRate / $oilRate : 0;
                $gorSum += $gor;
            }

            if (is_numeric($oilRate) && is_numeric($waterRate) && ($oilRate + $waterRate) > 0) {
                $bsw = ($waterRate / ($oilRate + $waterRate)) * 100;
                $bswSum += $bsw;
            }

            $validTestsCount++;
        }

        if ($validTestsCount > 0) {
            $summary['avg_gor'] = $gorSum / $validTestsCount;
            $summary['avg_bsw'] = $bswSum / $validTestsCount;
        }

        $summary['test_count'] = $validTestsCount;

        return $summary;
    }
}
