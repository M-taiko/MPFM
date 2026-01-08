<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WellTest;
use App\Models\MPFMPlan;
use App\Models\MPFMStep;
use App\Models\MPFMReading;
use App\Services\MPFMPlanService;

class MPFMPlanController extends Controller
{
    protected $planService;

    public function __construct(MPFMPlanService $planService)
    {
        $this->planService = $planService;
    }

    public function index()
    {
        $plans = MPFMPlan::with('wellTest')->latest()->paginate(10);
        return view('mpfm_plans.index', compact('plans'));
    }

    public function create($wellTestId)
    {
        $wellTest = WellTest::findOrFail($wellTestId);
        return view('mpfm_plans.create', compact('wellTest'));
    }

    public function store(Request $request, $wellTestId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'planned_oil_rate' => 'nullable|numeric',
            'planned_gas_rate' => 'nullable|numeric',
            'planned_water_rate' => 'nullable|numeric',
            'expected_gor' => 'nullable|numeric',
            'expected_bsw' => 'nullable|numeric',
            'expected_choke' => 'nullable|string',
            'expected_duration' => 'nullable|integer',
            'test_objective' => 'nullable|string',
            'steps' => 'nullable|array',
            'steps.*.step_number' => 'required|integer',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.choke' => 'nullable|string',
            'steps.*.time_window' => 'nullable|string',
            'steps.*.planned_rates' => 'nullable|array',
            'steps.*.description' => 'nullable|string',
        ]);

        $planData = $request->all();
        $plan = $this->planService->createPlan($wellTestId, $planData);

        return redirect()->route('well_tests.show', $wellTestId)
            ->with('success', 'MPFM Plan created successfully!');
    }

    public function show($id)
    {
        $plan = MPFMPlan::with(['wellTest', 'steps.readings'])->findOrFail($id);
        $report = $this->planService->generateReport($id);
        
        return view('mpfm_plans.show', compact('plan', 'report'));
    }

    public function edit($id)
    {
        $plan = MPFMPlan::with(['steps'])->findOrFail($id);
        return view('mpfm_plans.edit', compact('plan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'planned_oil_rate' => 'nullable|numeric',
            'planned_gas_rate' => 'nullable|numeric',
            'planned_water_rate' => 'nullable|numeric',
            'expected_gor' => 'nullable|numeric',
            'expected_bsw' => 'nullable|numeric',
            'expected_choke' => 'nullable|string',
            'expected_duration' => 'nullable|integer',
            'test_objective' => 'nullable|string',
        ]);

        $plan = MPFMPlan::findOrFail($id);
        $plan->update($request->all());

        return redirect()->route('mpfm_plans.show', $id)
            ->with('success', 'MPFM Plan updated successfully!');
    }

    public function addStep(Request $request, $planId)
    {
        $request->validate([
            'step_number' => 'required|integer',
            'name' => 'required|string|max:255',
            'choke' => 'nullable|string',
            'time_window' => 'nullable|string',
            'planned_rates' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        $stepData = $request->all();
        $step = $this->planService->createStep($planId, $stepData);

        return redirect()->back()
            ->with('success', 'Step added successfully!');
    }

    public function addReading(Request $request, $stepId)
    {
        $request->validate([
            'oil_rate' => 'nullable|numeric',
            'gas_rate' => 'nullable|numeric',
            'water_rate' => 'nullable|numeric',
            'wlr' => 'nullable|numeric',
            'bsw' => 'nullable|numeric',
            'pressure' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'reading_time' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $readingData = $request->all();
        $reading = $this->planService->addReading($stepId, $readingData);

        return redirect()->back()
            ->with('success', 'Reading added successfully!');
    }

    public function generateReport($id)
    {
        $plan = MPFMPlan::with(['wellTest', 'steps.readings'])->findOrFail($id);
        $report = $this->planService->generateReport($id);
        
        return view('mpfm_plans.report', compact('plan', 'report'));
    }

    public function destroy($id)
    {
        $plan = MPFMPlan::findOrFail($id);
        $wellTestId = $plan->well_test_id;
        $plan->delete();

        return redirect()->route('well_tests.show', $wellTestId)
            ->with('success', 'MPFM Plan deleted successfully!');
    }
}