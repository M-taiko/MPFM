<?php

namespace App\Http\Controllers;

use App\Models\OilString;
use App\Models\Tower;
use App\Models\OilField;
use App\Models\PlanTask;
use App\Domains\Task\Models\Task;
use App\Http\Requests\StoreOilStringRequest;
use App\Http\Requests\UpdateOilStringRequest;
use App\Services\DataTable\OilStringDataTableService;
use Illuminate\Http\Request;

class OilStringController extends Controller
{
    protected $dataTableService;

    public function __construct(OilStringDataTableService $dataTableService)
    {
        $this->dataTableService = $dataTableService;
        $this->authorizeResource(OilString::class, 'oil_string');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTableService->getDataTable($request);
        }

        $towers = Tower::with('oilField')->active()->get();
        $oilFields = OilField::active()->get();
        return view('oil_strings.index', compact('towers', 'oilFields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $towers = Tower::with('oilField')->active()->get();
        return view('oil_strings.create', compact('towers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOilStringRequest $request)
    {
        OilString::create($request->validated());

        return redirect()->route('oil_strings.index')
            ->with('success', 'Oil String created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OilString $oilString)
    {
        $oilString->load(['tower.oilField']);

        // MPFM Tests — ordered newest first, load operator & plan task
        $mpfmTests = $oilString->mpfmTests()
            ->with(['user', 'planTask.department'])
            ->orderByDesc('test_date')
            ->get();

        // Approved tests only (for stats & charts)
        $approvedTests = $mpfmTests->where('status', 'approved');

        // Production statistics from approved tests
        $stats = [
            'total_tests'        => $mpfmTests->count(),
            'approved_tests'     => $approvedTests->count(),
            'latest_oil_rate'    => $approvedTests->first()?->oil_rate,
            'avg_oil_rate'       => $approvedTests->avg('oil_rate'),
            'max_oil_rate'       => $approvedTests->max('oil_rate'),
            'avg_water_cut'      => $approvedTests->avg('water_cut'),
            'avg_gor'            => $approvedTests->avg('gor'),
            'avg_pressure'       => $approvedTests->avg('pressure'),
            'avg_temperature'    => $approvedTests->avg('temperature'),
            'avg_gas_rate'       => $approvedTests->avg('gas_rate'),
            'latest_test_date'   => $approvedTests->first()?->test_date,
        ];

        // Chart data — for approved tests sorted ascending by date
        $chartTests = $approvedTests->sortBy('test_date')->values();
        $chartDates    = $chartTests->pluck('test_date')->map(fn($d) => $d?->format('Y-m-d'))->toArray();
        $chartOilRate  = $chartTests->pluck('oil_rate')->map(fn($v) => round((float)$v, 2))->toArray();
        $chartGasRate  = $chartTests->pluck('gas_rate')->map(fn($v) => round((float)$v, 2))->toArray();
        $chartWaterCut = $chartTests->pluck('water_cut')->map(fn($v) => round((float)$v, 2))->toArray();
        $chartPressure = $chartTests->pluck('pressure')->map(fn($v) => round((float)$v, 2))->toArray();

        // Lift curve data: Qo vs Wellhead Flowing Pressure
        $liftCurveData = $chartTests
            ->filter(fn($t) => $t->oil_rate > 0 && $t->pressure > 0)
            ->map(fn($t) => ['x' => round((float)$t->pressure, 2), 'y' => round((float)$t->oil_rate, 2)])
            ->values()
            ->toArray();

        // Plan tasks linked to this oil string (legacy)
        $planTasks = PlanTask::where('oil_string_id', $oilString->id)
            ->with(['department', 'assignedOperator', 'monthlyPlan'])
            ->orderByDesc('scheduled_date')
            ->get();

        // New-style tasks linked to this oil string
        $tasks = Task::where('oil_string_id', $oilString->id)
            ->with(['department', 'assignedTo'])
            ->orderByDesc('created_at')
            ->get();

        return view('oil_strings.show', compact(
            'oilString', 'mpfmTests', 'approvedTests', 'stats',
            'chartDates', 'chartOilRate', 'chartGasRate', 'chartWaterCut', 'chartPressure',
            'liftCurveData', 'planTasks', 'tasks'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OilString $oilString)
    {
        $towers = Tower::with('oilField')->active()->get();
        return view('oil_strings.edit', compact('oilString', 'towers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOilStringRequest $request, OilString $oilString)
    {
        $oilString->update($request->validated());

        return redirect()->route('oil_strings.index')
            ->with('success', 'Oil String updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OilString $oilString)
    {
        $oilString->delete();

        return redirect()->route('oil_strings.index')
            ->with('success', 'Oil String deleted successfully.');
    }
}

