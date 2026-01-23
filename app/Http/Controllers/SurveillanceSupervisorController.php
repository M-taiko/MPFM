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
