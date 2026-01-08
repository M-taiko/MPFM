<?php

namespace App\Http\Controllers;

use App\Models\WellTest;
use App\Models\OperatorPlan;
use App\Services\OperatorPlanService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OperatorPlanController extends Controller
{
    protected $operatorPlanService;

    public function __construct(OperatorPlanService $operatorPlanService)
    {
        $this->operatorPlanService = $operatorPlanService;
    }

    /**
     * Display a listing of operator plans for a specific well test
     */
    public function index(Request $request, $wellTestId = null)
    {
        if ($wellTestId) {
            $wellTest = WellTest::findOrFail($wellTestId);
            $operatorPlans = $this->operatorPlanService->getOperatorPlansForWellTest($wellTestId);
            $planStats = $this->operatorPlanService->getPlanStatistics($wellTestId);

            return view('operator_plans.index', compact('wellTest', 'operatorPlans', 'planStats'));
        } else {
            // If no well test ID provided, show all plans or redirect
            return redirect()->route('well_tests.index');
        }
    }

    /**
     * Show the form for creating a new operator plan
     */
    public function create($wellTestId)
    {
        $wellTest = WellTest::findOrFail($wellTestId);
        return view('operator_plans.create', compact('wellTest'));
    }

    /**
     * Store a newly created operator plan in storage
     */
    public function store(Request $request, $wellTestId)
    {
        $request->validate([
            'step_number' => 'required|integer|min:1',
            'step_type' => 'required|in:instruction,measurement,action,preparation,cleanup',
            'step_description' => 'required|string|max:1000',
            'step_parameters' => 'nullable|array',
            'order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['well_test_id'] = $wellTestId;

        $operatorPlan = $this->operatorPlanService->createOperatorPlan($data);

        return redirect()->route('operator_plans.index', ['wellTestId' => $wellTestId])
            ->with('success', 'Operator plan step created successfully!');
    }

    /**
     * Display the specified operator plan
     */
    public function show($id)
    {
        $operatorPlan = $this->operatorPlanService->getOperatorPlan($id);

        if (!$operatorPlan) {
            abort(404, 'Operator plan not found');
        }

        return view('operator_plans.show', compact('operatorPlan'));
    }

    /**
     * Show the form for editing the specified operator plan
     */
    public function edit($id)
    {
        $operatorPlan = $this->operatorPlanService->getOperatorPlan($id);

        if (!$operatorPlan) {
            abort(404, 'Operator plan not found');
        }

        return view('operator_plans.edit', compact('operatorPlan'));
    }

    /**
     * Update the specified operator plan in storage
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'step_number' => 'required|integer|min:1',
            'step_type' => 'required|in:instruction,measurement,action,preparation,cleanup',
            'step_description' => 'required|string|max:1000',
            'step_parameters' => 'nullable|array',
            'order' => 'nullable|integer',
        ]);

        $operatorPlan = $this->operatorPlanService->updateOperatorPlan($id, $request->all());

        if (!$operatorPlan) {
            abort(404, 'Operator plan not found');
        }

        return redirect()->route('operator_plans.index', ['wellTestId' => $operatorPlan->well_test_id])
            ->with('success', 'Operator plan step updated successfully!');
    }

    /**
     * Remove the specified operator plan from storage
     */
    public function destroy($id)
    {
        $operatorPlan = $this->operatorPlanService->getOperatorPlan($id);

        if (!$operatorPlan) {
            abort(404, 'Operator plan not found');
        }

        $wellTestId = $operatorPlan->well_test_id;
        $this->operatorPlanService->deleteOperatorPlan($id);

        return redirect()->route('operator_plans.index', ['wellTestId' => $wellTestId])
            ->with('success', 'Operator plan step deleted successfully!');
    }

    /**
     * Mark the specified operator plan step as completed
     */
    public function markCompleted(Request $request, $id)
    {
        $request->validate([
            'completed_by' => 'nullable|string|max:255',
            'results' => 'nullable|array',
        ]);

        $operatorPlan = $this->operatorPlanService->markStepAsCompleted(
            $id,
            $request->completed_by,
            $request->results ?? []
        );

        if (!$operatorPlan) {
            return response()->json(['error' => 'Operator plan not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Step marked as completed',
            'operator_plan' => $operatorPlan
        ]);
    }

    /**
     * Mark the specified operator plan step as pending
     */
    public function markPending($id)
    {
        $operatorPlan = $this->operatorPlanService->markStepAsPending($id);

        if (!$operatorPlan) {
            return response()->json(['error' => 'Operator plan not found'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Step marked as pending',
            'operator_plan' => $operatorPlan
        ]);
    }

    /**
     * Reorder operator plan steps
     */
    public function reorder(Request $request, $wellTestId)
    {
        $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'integer|exists:operator_plans,id,well_test_id,' . $wellTestId,
        ]);

        $success = $this->operatorPlanService->reorderSteps($wellTestId, $request->ordered_ids);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Steps reordered successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to reorder steps'
        ]);
    }

    /**
     * Get next step for the specified well test
     */
    public function getNextStep($wellTestId)
    {
        $nextStep = $this->operatorPlanService->getNextStep($wellTestId);

        return response()->json([
            'success' => true,
            'next_step' => $nextStep
        ]);
    }

    /**
     * Get operator plan statistics for a well test
     */
    public function getStatistics($wellTestId)
    {
        $stats = $this->operatorPlanService->getPlanStatistics($wellTestId);

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Get completed steps for a well test
     */
    public function getCompletedSteps($wellTestId)
    {
        $completedSteps = $this->operatorPlanService->getCompletedSteps($wellTestId);

        return response()->json([
            'success' => true,
            'completed_steps' => $completedSteps
        ]);
    }

    /**
     * Get pending steps for a well test
     */
    public function getPendingSteps($wellTestId)
    {
        $pendingSteps = $this->operatorPlanService->getPendingSteps($wellTestId);

        return response()->json([
            'success' => true,
            'pending_steps' => $pendingSteps
        ]);
    }
}
