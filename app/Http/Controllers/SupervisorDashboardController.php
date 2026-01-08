<?php

namespace App\Http\Controllers;

use App\Models\DailyPlan;
use App\Services\DailyPlanService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SupervisorDashboardController extends Controller
{
    protected $dailyPlanService;

    public function __construct(DailyPlanService $dailyPlanService)
    {
        $this->dailyPlanService = $dailyPlanService;
        $this->middleware('auth');
    }

    /**
     * Supervisor main dashboard
     */
    public function index()
    {
        $supervisor = auth()->user();

        // Today's plans
        $todaysPlans = DailyPlan::with(['tasks', 'oilField'])
            ->createdBy($supervisor->id)
            ->forDate(Carbon::today())
            ->get();

        // Upcoming plans (next 7 days)
        $upcomingPlans = DailyPlan::createdBy($supervisor->id)
            ->whereBetween('plan_date', [Carbon::tomorrow(), Carbon::now()->addDays(7)])
            ->orderBy('plan_date')
            ->get();

        // Recent plans stats
        $recentStats = [
            'total_plans' => DailyPlan::createdBy($supervisor->id)
                ->whereBetween('plan_date', [Carbon::now()->subDays(30), Carbon::now()])
                ->count(),
            'active_plans' => DailyPlan::createdBy($supervisor->id)
                ->active()
                ->count(),
            'completed_today' => DailyPlan::createdBy($supervisor->id)
                ->forDate(Carbon::today())
                ->where('status', 'completed')
                ->count(),
            'pending_tasks' => DailyPlan::createdBy($supervisor->id)
                ->active()
                ->sum('pending_tasks'),
        ];

        return view('supervisor.dashboard', compact('todaysPlans', 'upcomingPlans', 'recentStats'));
    }

    /**
     * Plan calendar view
     */
    public function calendar(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $plans = DailyPlan::createdBy(auth()->id())
            ->whereBetween('plan_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($plan) {
                return $plan->plan_date->format('Y-m-d');
            });

        return view('supervisor.calendar', compact('plans', 'month', 'year'));
    }
}
