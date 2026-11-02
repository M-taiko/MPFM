<?php

namespace App\Http\Controllers;

use App\Models\TestVessel;
use App\Models\OilField;
use App\Http\Requests\StoreTestVesselRequest;
use App\Http\Requests\UpdateTestVesselRequest;
use App\Services\DataTable\TestVesselDataTableService;
use Illuminate\Http\Request;

class TestVesselController extends Controller
{
    protected $dataTableService;

    public function __construct(TestVesselDataTableService $dataTableService)
    {
        $this->dataTableService = $dataTableService;
        $this->authorizeResource(TestVessel::class, 'test_vessel');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->dataTableService->getDataTable($request);
        }

        $oilFields = OilField::active()->get();
        $vessels = TestVessel::with('oilField')
            ->withCount('tests')
            ->withAvg('tests as avg_oil', 'oil_rate')
            ->withMax('tests as last_test_at', 'test_date')
            ->orderBy('name')
            ->get();

        return view('test_vessels.index', compact('oilFields', 'vessels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $oilFields = OilField::active()->get();
        return view('test_vessels.create', compact('oilFields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTestVesselRequest $request)
    {
        TestVessel::create($request->validated());

        return redirect()->route('test_vessels.index')
            ->with('success', 'Test Vessel created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TestVessel $testVessel)
    {
        $testVessel->load('oilField', 'tests.user');
        return view('test_vessels.show', compact('testVessel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TestVessel $testVessel)
    {
        $oilFields = OilField::active()->get();
        return view('test_vessels.edit', compact('testVessel', 'oilFields'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestVesselRequest $request, TestVessel $testVessel)
    {
        $testVessel->update($request->validated());

        return redirect()->route('test_vessels.index')
            ->with('success', 'Test Vessel updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TestVessel $testVessel)
    {
        $testVessel->delete();

        return redirect()->route('test_vessels.index')
            ->with('success', 'Test Vessel deleted successfully.');
    }

    /**
     * Get tests for a test vessel (AJAX endpoint)
     */
    public function getTests(TestVessel $testVessel)
    {
        return response()->json([
            'tests' => $testVessel->tests()
                ->with('user', 'oilField', 'tower', 'oilString')
                ->latest('test_date')
                ->get(),
        ]);
    }

    /**
     * Get vessel info (oil field) for AJAX
     */
    public function getInfo(TestVessel $testVessel)
    {
        return response()->json([
            'oil_field_id' => $testVessel->oil_field_id,
            'oil_field_name' => $testVessel->oilField?->name ?? '-',
        ]);
    }
}
