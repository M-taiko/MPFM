<?php

namespace App\Http\Controllers;

use App\Models\MPFMOperationalPlan;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\OilString;
use App\Models\User;
use App\Http\Requests\StoreMPFMOperationalPlanRequest;
use App\Http\Requests\UpdateMPFMOperationalPlanRequest;
use App\Services\MPFMOperationalPlanService;
use App\Services\DataTable\MPFMOperationalPlanDataTableService;
use Illuminate\Http\Request;

class MPFMOperationalPlanController extends Controller
{
    protected $planService;
    protected $dataTableService;

    public function __construct(
        MPFMOperationalPlanService $planService,
        MPFMOperationalPlanDataTableService $dataTableService
    ) {
        $this->planService = $planService;
        $this->dataTableService = $dataTableService;
        $this->authorizeResource(MPFMOperationalPlan::class, 'mpfm_operational_plan');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTableService->getDataTable($request, auth()->user());
        }

        $oilFields = OilField::active()->get();
        $towers = Tower::active()->get();
        return view('mpfm_operational_plans.index', compact('oilFields', 'towers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $oilFields = OilField::active()->get();
        $operators = User::role('mpfm_operator')->get();
        return view('mpfm_operational_plans.create', compact('oilFields', 'operators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMPFMOperationalPlanRequest $request)
    {
        $plan = $this->planService->create($request->validated(), auth()->user());

        return redirect()->route('mpfm_operational_plans.index')
            ->with('success', 'MPFM Operational Plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MPFMOperationalPlan $mpfmOperationalPlan)
    {
        $mpfmOperationalPlan->load([
            'creator',
            'assignedUser',
            'oilField',
            'tower',
            'mpfmTests'
        ]);

        $assignedStrings = $mpfmOperationalPlan->assignedOilStrings();
        
        return view('mpfm_operational_plans.show', compact('mpfmOperationalPlan', 'assignedStrings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MPFMOperationalPlan $mpfmOperationalPlan)
    {
        $oilFields = OilField::active()->get();
        $towers = Tower::where('oil_field_id', $mpfmOperationalPlan->oil_field_id)->active()->get();
        $oilStrings = OilString::where('tower_id', $mpfmOperationalPlan->tower_id)->active()->get();
        $operators = User::role('mpfm_operator')->get();

        return view('mpfm_operational_plans.edit', compact(
            'mpfmOperationalPlan',
            'oilFields',
            'towers',
            'oilStrings',
            'operators'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMPFMOperationalPlanRequest $request, MPFMOperationalPlan $mpfmOperationalPlan)
    {
        $this->planService->update($mpfmOperationalPlan, $request->validated());

        return redirect()->route('mpfm_operational_plans.index')
            ->with('success', 'MPFM Operational Plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MPFMOperationalPlan $mpfmOperationalPlan)
    {
        $mpfmOperationalPlan->delete();

        return redirect()->route('mpfm_operational_plans.index')
            ->with('success', 'MPFM Operational Plan deleted successfully.');
    }

    /**
     * Get towers for a specific oil field (AJAX)
     */
    public function getTowers(Request $request, $oilFieldId)
    {
        $towers = Tower::where('oil_field_id', $oilFieldId)->active()->get();
        return response()->json($towers);
    }

    /**
     * Get oil strings for a specific tower (AJAX)
     */
    public function getOilStrings(Request $request, $towerId)
    {
        $oilStrings = OilString::where('tower_id', $towerId)->active()->get();
        return response()->json($oilStrings);
    }
}

