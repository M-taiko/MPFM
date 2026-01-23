<?php

namespace App\Http\Controllers;

use App\Models\PlanTask;
use App\Enums\TaskState;
use App\Services\TaskStateService;
use Illuminate\Http\Request;

class TaskStateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param TaskStateService $stateService
     */
    public function __construct(
        protected TaskStateService $stateService
    ) {
        $this->middleware('auth');
    }

    /**
     * Submit task to surveillance (Job Officer).
     *
     * @param Request $request
     * @param PlanTask $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitToSurveillance(Request $request, PlanTask $task)
    {
        $this->authorize('submitToSurveillance', $task);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->stateService->transition(
                $task,
                TaskState::SUBMITTED_TO_SURVEILLANCE,
                auth()->user(),
                $request->notes
            );

            return back()->with('success', 'Task submitted to Surveillance Supervisor successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit task: ' . $e->getMessage());
        }
    }

    /**
     * Assign task to MPFM (Surveillance Supervisor).
     *
     * @param Request $request
     * @param PlanTask $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignToMPFM(Request $request, PlanTask $task)
    {
        $this->authorize('assignToMPFM', $task);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'classification' => 'nullable|string|max:255',
        ]);

        try {
            $metadata = [];
            if ($request->classification) {
                $metadata['classification'] = $request->classification;
            }

            $this->stateService->transition(
                $task,
                TaskState::ASSIGNED_TO_MPFM,
                auth()->user(),
                $request->notes,
                $metadata
            );

            return back()->with('success', 'Task classified and assigned to MPFM department successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign task: ' . $e->getMessage());
        }
    }

    /**
     * Send task back to draft (Surveillance Supervisor).
     *
     * @param Request $request
     * @param PlanTask $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendBackToDraft(Request $request, PlanTask $task)
    {
        $this->authorize('sendBackToDraft', $task);

        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        try {
            $this->stateService->transition(
                $task,
                TaskState::DRAFT,
                auth()->user(),
                "RETURNED: " . $request->notes
            );

            return back()->with('info', 'Task sent back to draft for corrections.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send task back: ' . $e->getMessage());
        }
    }

    /**
     * Start task (Operator).
     *
     * @param PlanTask $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startTask(PlanTask $task)
    {
        $this->authorize('startTask', $task);

        try {
            $this->stateService->transition(
                $task,
                TaskState::IN_PROGRESS,
                auth()->user(),
                'Task started by operator'
            );

            // Redirect to upload page
            return redirect("/upload?task_id={$task->id}")
                ->with('success', 'Task started successfully. Please upload log file.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start task: ' . $e->getMessage());
        }
    }

    /**
     * Review task as MPFM Supervisor.
     *
     * @param Request $request
     * @param PlanTask $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reviewAsMPFM(Request $request, PlanTask $task)
    {
        $this->authorize('reviewAsMPFM', $task);

        $request->validate([
            'notes' => 'required|string|max:1000',
            'action' => 'required|in:approve,reject',
        ]);

        try {
            if ($request->action === 'approve') {
                // Approve and move forward
                $this->stateService->transition(
                    $task,
                    TaskState::MPFM_REVIEWED,
                    auth()->user(),
                    $request->notes
                );

                return back()->with('success', 'Task reviewed and sent to Surveillance Supervisor.');
            } else {
                // Reject back to operator
                $this->stateService->transition(
                    $task,
                    TaskState::IN_PROGRESS,
                    auth()->user(),
                    "MPFM REJECTED: " . $request->notes
                );

                return back()->with('info', 'Task rejected and sent back to operator for revision.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to review task: ' . $e->getMessage());
        }
    }

    /**
     * Review task as Surveillance Supervisor.
     *
     * @param Request $request
     * @param PlanTask $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reviewAsSurveillance(Request $request, PlanTask $task)
    {
        $this->authorize('reviewAsSurveillance', $task);

        $request->validate([
            'notes' => 'required|string|max:1000',
            'action' => 'required|in:approve,reject',
        ]);

        try {
            if ($request->action === 'approve') {
                // Approve and forward to Job Officer
                $this->stateService->transition(
                    $task,
                    TaskState::SURVEILLANCE_REVIEWED,
                    auth()->user(),
                    $request->notes
                );

                return back()->with('success', 'Task reviewed and sent to Job Officer for final approval.');
            } else {
                // Reject back to MPFM
                $this->stateService->transition(
                    $task,
                    TaskState::MPFM_REVIEWED,
                    auth()->user(),
                    "SURVEILLANCE REJECTED: " . $request->notes
                );

                return back()->with('info', 'Task rejected and sent back to MPFM Supervisor.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to review task: ' . $e->getMessage());
        }
    }

    /**
     * Final approval by Job Officer.
     *
     * @param Request $request
     * @param PlanTask $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finalApprove(Request $request, PlanTask $task)
    {
        $this->authorize('finalApprove', $task);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'action' => 'required|in:approve,reject',
        ]);

        try {
            if ($request->action === 'approve') {
                // Approve
                $this->stateService->transition(
                    $task,
                    TaskState::JOB_OFFICER_APPROVED,
                    auth()->user(),
                    $request->notes
                );

                // Automatically transition to completed
                $this->stateService->transition(
                    $task,
                    TaskState::COMPLETED,
                    auth()->user(),
                    'Auto-completed after Job Officer approval'
                );

                return back()->with('success', 'Task approved and completed successfully.');
            } else {
                // Reject back to surveillance
                $this->stateService->transition(
                    $task,
                    TaskState::SURVEILLANCE_REVIEWED,
                    auth()->user(),
                    "JOB OFFICER REJECTED: " . $request->notes
                );

                return back()->with('info', 'Task rejected and sent back to Surveillance Supervisor.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process approval: ' . $e->getMessage());
        }
    }

    /**
     * View state transition history.
     *
     * @param PlanTask $task
     * @return \Illuminate\View\View
     */
    public function showHistory(PlanTask $task)
    {
        $this->authorize('viewStateHistory', $task);

        $task->load([
            'stateHistories.changedBy',
            'monthlyPlan',
            'operators',
            'towerModel',
            'surveillanceSupervisor'
        ]);

        return view('tasks.state_history', compact('task'));
    }

    /**
     * Bulk transition multiple tasks.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkTransition(Request $request)
    {
        $request->validate([
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'exists:plan_tasks,id',
            'new_state' => 'required|string|in:' . implode(',', TaskState::values()),
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $newState = TaskState::from($request->new_state);

            $results = $this->stateService->bulkTransition(
                $request->task_ids,
                $newState,
                auth()->user(),
                $request->notes
            );

            $successCount = count($results['success']);
            $failedCount = count($results['failed']);

            $message = "$successCount task(s) transitioned successfully.";
            if ($failedCount > 0) {
                $message .= " $failedCount task(s) failed.";
            }

            return back()->with('success', $message)
                ->with('bulk_results', $results);
        } catch (\Exception $e) {
            return back()->with('error', 'Bulk transition failed: ' . $e->getMessage());
        }
    }

    /**
     * Get workflow dashboard with statistics.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = auth()->user();
        $statistics = $this->stateService->getWorkflowStatistics($user);

        // Get tasks pending review for current user's role
        $pendingTasks = collect();
        if ($user->hasRole('mpfm_supervisor')) {
            $pendingTasks = $this->stateService->getTasksPendingReview('mpfm_supervisor');
        } elseif ($user->hasRole('surveillance_supervisor')) {
            $pendingTasks = $this->stateService->getTasksPendingReview('surveillance_supervisor');
        } elseif ($user->hasRole('job_officer')) {
            $pendingTasks = $this->stateService->getTasksPendingReview('job_officer');
        }

        return view('tasks.workflow_dashboard', compact('statistics', 'pendingTasks'));
    }
}
