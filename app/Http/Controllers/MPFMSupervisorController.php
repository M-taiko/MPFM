<?php

namespace App\Http\Controllers;

use App\Models\MonthlyPlan;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MPFMSupervisorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard for MPFM Supervisor
     */
    public function dashboard()
    {
        // Plans awaiting review
        $plansForReview = MonthlyPlan::where('status', 'under_review')
            ->with('creator', 'tasks')
            ->orderBy('created_at', 'desc')
            ->get();

        // Active plans
        $activePlans = MonthlyPlan::where('status', 'active')
            ->with('creator', 'tasks')
            ->orderBy('created_at', 'desc')
            ->get();

        // Unassigned tasks from all plans (not just active)
        $unassignedTasks = PlanTask::whereNull('assigned_operator_id')
            ->with('monthlyPlan', 'towerModel')
            ->orderBy('scheduled_date', 'asc')
            ->get();

        // All completed tests for the supervisor's team
        $recentCompletedTests = PlanTask::where('completion_status', 'completed')
            ->with('monthlyPlan', 'operator', 'mpfmTest', 'towerModel')
            ->orderBy('completed_at', 'desc')
            ->paginate(25);

        // Recent stats (last 30 days)
        $recentStats = [
            'total_plans' => MonthlyPlan::where('created_at', '>=', now()->subDays(30))->count(),
            'active_plans' => MonthlyPlan::where('status', 'active')->count(),
            'completed_today' => PlanTask::where('completion_status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
            'pending_tasks' => PlanTask::whereHas('monthlyPlan', function($q) {
                    $q->where('status', 'active');
                })
                ->where('completion_status', 'pending')
                ->count(),
        ];

        // Today's plans
        $todaysPlans = PlanTask::whereDate('scheduled_date', today())
            ->with('monthlyPlan', 'operator')
            ->get();

        // Upcoming daily plans (next 7 days)
        $upcomingPlans = DailyPlan::whereBetween('plan_date', [today(), today()->addDays(7)])
            ->with(['oilField', 'tower', 'creator'])
            ->orderBy('plan_date', 'asc')
            ->limit(10)
            ->get();

        // Get all operators for assignment dropdown
        $operators = User::role('mpfm_operator')->get();

        return view('supervisor.dashboard', compact(
            'plansForReview',
            'activePlans',
            'unassignedTasks',
            'recentCompletedTests',
            'recentStats',
            'todaysPlans',
            'upcomingPlans',
            'operators'
        ));
    }

    /**
     * Review plan (MPFM Supervisor reviews Job Officer's plan)
     */
    public function reviewPlan(MonthlyPlan $monthlyPlan)
    {
        if ($monthlyPlan->status !== 'under_review') {
            return back()->with('error', 'This plan is not under review.');
        }

        $monthlyPlan->load(['creator', 'tasks.towerModel', 'tasks.operator']);
        $operators = User::role('operator')->get();

        return view('supervisor.review_plan', compact('monthlyPlan', 'operators'));
    }

    /**
     * Approve plan and activate it
     */
    public function approvePlan(Request $request, MonthlyPlan $monthlyPlan)
    {
        if ($monthlyPlan->status !== 'under_review') {
            return back()->with('error', 'Only plans under review can be approved.');
        }

        try {
            $monthlyPlan->update([
                'status' => 'active',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // TODO: Send notification to Job Officer

            return redirect()
                ->route('supervisor.dashboard')
                ->with('success', 'Plan approved and activated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve plan: ' . $e->getMessage());
        }
    }

    /**
     * Reject plan
     */
    public function rejectPlan(Request $request, MonthlyPlan $monthlyPlan)
    {
        if ($monthlyPlan->status !== 'under_review') {
            return back()->with('error', 'Only plans under review can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        try {
            $monthlyPlan->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            // TODO: Send notification to Job Officer

            return redirect()
                ->route('supervisor.dashboard')
                ->with('success', 'Plan rejected. Job Officer has been notified.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject plan: ' . $e->getMessage());
        }
    }

    /**
     * Assign operator to a task
     */
    public function assignOperator(Request $request, PlanTask $planTask)
    {
        $request->validate([
            'operator_id' => 'required|exists:users,id',
        ]);

        try {
            $planTask->update([
                'assigned_operator_id' => $request->operator_id,
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
            ]);

            // TODO: Send notification to operator

            return back()->with('success', 'Operator assigned successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign operator: ' . $e->getMessage());
        }
    }

    /**
     * Bulk assign operator to multiple tasks
     */
    public function bulkAssignOperator(Request $request)
    {
        $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:plan_tasks,id',
            'operator_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            PlanTask::whereIn('id', $request->task_ids)->update([
                'assigned_operator_id' => $request->operator_id,
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
            ]);

            DB::commit();

            // TODO: Send notification to operator

            return back()->with('success', count($request->task_ids) . ' tasks assigned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to assign tasks: ' . $e->getMessage());
        }
    }

    /**
     * Update task details (change priority, date, etc.)
     */
    public function updateTask(Request $request, PlanTask $planTask)
    {
        $request->validate([
            'scheduled_date' => 'nullable|date',
            'priority' => 'nullable|in:normal,high,top',
            'supervisor_remarks' => 'nullable|string',
        ]);

        try {
            $planTask->update($request->only([
                'scheduled_date',
                'priority',
                'supervisor_remarks',
            ]));

            return back()->with('success', 'Task updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * View all tasks for assignment
     */
    public function assignmentView(MonthlyPlan $monthlyPlan)
    {
        if (!in_array($monthlyPlan->status, ['under_review', 'active'])) {
            return back()->with('error', 'Cannot assign tasks for this plan.');
        }

        $monthlyPlan->load(['tasks.operator', 'tasks.towerModel']);
        $operators = User::role('operator')->get();

        // Group tasks by tower for easier assignment
        $tasksByTower = $monthlyPlan->tasks->groupBy('tower');

        return view('supervisor.assign_tasks', compact('monthlyPlan', 'operators', 'tasksByTower'));
    }

    /**
     * Monitor progress of active plans
     */
    public function monitorProgress(MonthlyPlan $monthlyPlan)
    {
        $monthlyPlan->load(['tasks.operator', 'tasks.towerModel', 'tasks.mpfmTest']);

        // Statistics by operator
        $operatorStats = $monthlyPlan->tasks()
            ->select('assigned_operator_id', DB::raw('count(*) as total'))
            ->selectRaw('SUM(CASE WHEN completion_status = "completed" THEN 1 ELSE 0 END) as completed')
            ->selectRaw('SUM(CASE WHEN completion_status = "pending" THEN 1 ELSE 0 END) as pending')
            ->selectRaw('SUM(CASE WHEN completion_status = "in_progress" THEN 1 ELSE 0 END) as in_progress')
            ->whereNotNull('assigned_operator_id')
            ->groupBy('assigned_operator_id')
            ->with('operator')
            ->get();

        // Statistics by tower
        $towerStats = $monthlyPlan->tasks()
            ->select('tower', DB::raw('count(*) as total'))
            ->selectRaw('SUM(CASE WHEN completion_status = "completed" THEN 1 ELSE 0 END) as completed')
            ->groupBy('tower')
            ->get();

        return view('supervisor.monitor_progress', compact('monthlyPlan', 'operatorStats', 'towerStats'));
    }

    /**
     * Assign task to operator
     */
    public function assignTask(Request $request, PlanTask $task)
    {
        $request->validate([
            'operator_ids' => 'required|array|min:1',
            'operator_ids.*' => 'exists:users,id',
            'primary_operator_id' => 'required|exists:users,id',
        ]);

        try {
            DB::transaction(function() use ($request, $task) {
                // Detach all existing operators
                $task->operators()->detach();

                // Attach new operators
                $syncData = [];
                foreach ($request->operator_ids as $operatorId) {
                    $syncData[$operatorId] = [
                        'assigned_at' => now(),
                        'assigned_by' => auth()->id(),
                        'is_primary' => ($operatorId == $request->primary_operator_id),
                    ];
                }
                $task->operators()->attach($syncData);

                // Sync primary operator to main table for backward compatibility
                $task->update(['assigned_operator_id' => $request->primary_operator_id]);
            });

            return back()->with('success', 'Task assigned to ' . count($request->operator_ids) . ' operator(s) successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign task: ' . $e->getMessage());
        }
    }

    /**
     * Add a single operator to an existing task
     */
    public function addOperatorToTask(Request $request, PlanTask $task)
    {
        $request->validate([
            'operator_id' => 'required|exists:users,id',
        ]);

        try {
            if ($task->operators()->where('user_id', $request->operator_id)->exists()) {
                return back()->with('error', 'Operator is already assigned to this task.');
            }

            $task->operators()->attach($request->operator_id, [
                'assigned_at' => now(),
                'assigned_by' => auth()->id(),
                'is_primary' => false,
            ]);

            return back()->with('success', 'Operator added to task successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add operator: ' . $e->getMessage());
        }
    }

    /**
     * Remove an operator from a task
     */
    public function removeOperatorFromTask(Request $request, PlanTask $task)
    {
        $request->validate([
            'operator_id' => 'required|exists:users,id',
        ]);

        try {
            // Prevent removing primary operator
            $isPrimary = $task->operators()
                ->where('user_id', $request->operator_id)
                ->wherePivot('is_primary', true)
                ->exists();

            if ($isPrimary) {
                return back()->with('error', 'Cannot remove primary operator. Reassign first.');
            }

            $task->operators()->detach($request->operator_id);

            return back()->with('success', 'Operator removed from task successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove operator: ' . $e->getMessage());
        }
    }
}
