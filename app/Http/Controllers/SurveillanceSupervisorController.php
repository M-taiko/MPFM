<?php

namespace App\Http\Controllers;

use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use App\Enums\TaskState;
use Illuminate\Http\Request;

class SurveillanceSupervisorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Check if user has surveillance_supervisor or production_supervisor role
            if (!auth()->user()->hasRole(['surveillance_supervisor', 'production_supervisor'])) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    /**
     * Show comprehensive surveillance supervisor dashboard
     */
    public function dashboard(Request $request)
    {
        // Active monthly plans
        $activePlans = MonthlyPlan::where('status', 'active')
            ->with(['tasks'])
            ->get()
            ->map(function ($plan) {
                $tasks = $plan->tasks;
                return [
                    'id' => $plan->id,
                    'plan_name' => $plan->plan_name,
                    'plan_month' => $plan->plan_month,
                    'total_tasks' => $tasks->count(),
                    'completed_tasks' => $tasks->where('completion_status', 'completed')->count(),
                    'pending_tasks' => $tasks->where('completion_status', 'pending')->count(),
                    'progress' => $tasks->count() > 0 ? round(($tasks->where('completion_status', 'completed')->count() / $tasks->count()) * 100, 1) : 0
                ];
            });

        // KPI Counters - All tasks from all plans
        $totalTasks = PlanTask::count();
        $completedTasks = PlanTask::where('completion_status', 'completed')->orWhere('state', 'completed')->count();
        $pendingTasks = PlanTask::where('completion_status', 'pending')->count();
        $overdueTasks = PlanTask::where('completion_status', 'pending')
            ->where('scheduled_date', '<', now()->startOfDay())
            ->count();

        // DataTable data will be loaded via AJAX
        if ($request->ajax()) {
            return $this->getDashboardTasksData($request);
        }

        return view('surveillance.dashboard', compact(
            'activePlans',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'overdueTasks'
        ));
    }

    /**
     * Get tasks data for DataTable (AJAX)
     */
    private function getDashboardTasksData(Request $request)
    {
        $query = PlanTask::with([
            'monthlyPlan',
            'towerModel',
            'oilString',
            'operator',
            'mpfmTest'
        ]);

        // Search functionality
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('sno', 'like', "%{$searchValue}%")
                    ->orWhere('well_full_name', 'like', "%{$searchValue}%")
                    ->orWhereHas('operator', function ($q) use ($searchValue) {
                        $q->where('name', 'like', "%{$searchValue}%");
                    });
            });
        }

        // Sorting
        $columns = ['id', 'sno', 'well_full_name', 'category', 'sub_category', 'assigned_operator_id', 'completion_status', 'updated_at'];
        if ($request->has('order') && isset($request->order[0])) {
            $orderColumn = $columns[$request->order[0]['column']] ?? 'id';
            $orderDir = $request->order[0]['dir'] === 'desc' ? 'desc' : 'asc';
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->latest('updated_at');
        }

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $total = $query->count();

        $tasks = $query->offset($start)
            ->limit($length)
            ->get();

        $data = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'sno' => $task->sno ?? '-',
                'plan' => $task->monthlyPlan?->plan_month ?? '-',
                'well_name' => $task->well_full_name ?? '-',
                'department' => ucfirst($task->category ?? '-'),
                'operation_type' => ucfirst($task->sub_category ?? '-'),
                'operator' => $task->operator?->name ?? 'Unassigned',
                'status' => $this->getStatusBadge($task->completion_status),
                'updated_at' => $task->updated_at?->format('d M Y H:i') ?? '-',
                'action' => view('surveillance.task-detail-button', compact('task'))->render()
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    /**
     * Get status badge HTML
     */
    private function getStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'in_progress' => '<span class="badge badge-info">In Progress</span>',
            'completed' => '<span class="badge badge-success">Completed</span>',
            'failed' => '<span class="badge badge-danger">Failed</span>',
            'reviewed' => '<span class="badge badge-primary">Reviewed</span>',
        ];
        return $badges[$status] ?? '<span class="badge badge-secondary">Unknown</span>';
    }

    /**
     * Show task details for surveillance supervisor
     */
    public function viewTask(PlanTask $task)
    {
        // Load task with all relationships
        $task->load([
            'monthlyPlan',
            'towerModel',
            'oilString',
            'operator',
            'mpfmTest',
            'mpfmTest.wellTest',
            'stateHistories',
            'stateHistories.changedBy'
        ]);

        return view('surveillance.task-details', compact('task'));
    }

    /**
     * Show list of monthly plans awaiting surveillance review
     */
    public function index()
    {
        $plans = MonthlyPlan::whereIn('status', ['submitted_to_surveillance', 'under_surveillance_review'])
            ->with(['creator', 'tasks' => function ($query) {
                $query->where('state', TaskState::SUBMITTED_TO_SURVEILLANCE);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('surveillance.index', compact('plans'));
    }

    /**
     * Show plan details for surveillance review
     */
    public function reviewPlan(MonthlyPlan $plan)
    {
        $plan->load([
            'creator',
            'tasks' => function ($query) {
                $query->orderBy('created_at');
            },
            'tasks.operator',
            'tasks.towerModel',
            'tasks.oilString',
            'tasks.stateHistories'
        ]);

        $pendingTasks = $plan->tasks()
            ->where('state', TaskState::SUBMITTED_TO_SURVEILLANCE)
            ->count();

        return view('surveillance.review-plan', compact('plan', 'pendingTasks'));
    }

    /**
     * Get task details for modal
     */
    public function getTaskDetails(MonthlyPlan $plan, PlanTask $task)
    {
        // Verify task belongs to this plan
        if ($task->monthly_plan_id != $plan->id) {
            abort(403, 'Task does not belong to this plan');
        }

        // Load task details
        $task->load([
            'stateHistories' => function ($query) {
                $query->latest('created_at');
            },
            'stateHistories.changedBy',
            'monthlyPlan',
            'operator',
            'mpfmTest',
            'towerModel',
            'oilString'
        ]);

        return view('surveillance.task-modal-content', compact('task', 'plan'));
    }

    /**
     * Approve task and assign to MPFM
     */
    public function approveTask(MonthlyPlan $monthlyPlan, PlanTask $planTask, Request $request)
    {
        if ($planTask->monthly_plan_id != $monthlyPlan->id) {
            abort(403, 'Task does not belong to this plan');
        }

        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        try {
            // Transition task to ASSIGNED_TO_MPFM
            $planTask->update([
                'state' => TaskState::ASSIGNED_TO_MPFM,
                'surveillance_supervisor_id' => auth()->id(),
                'surveillance_assigned_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Task approved and assigned to MPFM for execution.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve task: ' . $e->getMessage());
        }
    }

    /**
     * Reject task and send back to draft
     */
    public function rejectTask(MonthlyPlan $monthlyPlan, PlanTask $planTask, Request $request)
    {
        if ($planTask->monthly_plan_id != $monthlyPlan->id) {
            abort(403, 'Task does not belong to this plan');
        }

        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        try {
            $remarks = $request->input('remarks');

            // Transition task back to DRAFT
            $planTask->update([
                'state' => TaskState::DRAFT,
                'completion_status' => 'pending',
            ]);

            // Notify job officer about rejection
            if ($planTask->monthlyPlan && $planTask->monthlyPlan->createdBy) {
                // TODO: Send notification to Job Officer about task rejection
            }

            return redirect()->back()->with('success', 'Task rejected and sent back to draft. Job Officer will be notified.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject task: ' . $e->getMessage());
        }
    }

    /**
     * Approve entire plan
     */
    public function approvePlan(MonthlyPlan $monthlyPlan)
    {
        try {
            $monthlyPlan->update([
                'status' => 'surveillance_approved',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'Plan approved by surveillance. Tasks ready for MPFM assignment.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve plan: ' . $e->getMessage());
        }
    }

    /**
     * Reject entire plan
     */
    public function rejectPlan(MonthlyPlan $monthlyPlan, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        try {
            $monthlyPlan->update([
                'status' => 'surveillance_rejected',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
            ]);

            // TODO: Notify job officer about plan rejection

            return redirect()->back()->with('success', 'Plan rejected. Job Officer will be notified for corrections.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject plan: ' . $e->getMessage());
        }
    }
}
