<?php

namespace App\Http\Controllers;

use App\Models\TestVesselTest;
use App\Models\TestVessel;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\OilString;
use App\Models\MPFMTest;
use App\Http\Requests\StoreTestVesselTestRequest;
use App\Http\Requests\UpdateTestVesselTestRequest;
use App\Services\DataTable\TestVesselTestDataTableService;
use Illuminate\Http\Request;

class TestVesselTestController extends Controller
{
    protected $dataTableService;

    public function __construct(TestVesselTestDataTableService $dataTableService)
    {
        $this->dataTableService = $dataTableService;
        $this->authorizeResource(TestVesselTest::class, 'tv_test');
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
        $testVessels = TestVessel::active()->get();
        return view('tv_tests.index', compact('oilFields', 'testVessels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $oilFields = OilField::active()->get();
        $testVessels = TestVessel::active()->get();
        $tvTest = new TestVesselTest();

        // Pre-fill from request params (when coming from a TV test request)
        if ($request->has('test_vessel_id')) {
            $tvTest->test_vessel_id = $request->test_vessel_id;
            $tvTest->tower_id = $request->tower_id;
            $tvTest->oil_field_id = $request->oil_field_id;
            $tvTest->oil_string_id = $request->oil_string_id;
            $tvTest->tv_test_request_well_id = $request->tv_test_request_well_id;
        }

        // Load towers/strings if available
        $towers = $tvTest->tower_id ? Tower::where('oil_field_id', $tvTest->oil_field_id)->get() : collect();
        $oilStrings = $tvTest->tower_id ? OilString::where('tower_id', $tvTest->tower_id)->get() : collect();
        $mpfmTests = $tvTest->oil_field_id ? MPFMTest::where('oil_field_id', $tvTest->oil_field_id)->get() : collect();

        return view('tv_tests.create', compact('oilFields', 'testVessels', 'tvTest', 'towers', 'oilStrings', 'mpfmTests'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTestVesselTestRequest $request)
    {
        // Ensure user_id is set to current user if not provided
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $tvTest = TestVesselTest::create($data);

        // Update the linked request well on submit
        if (!empty($data['tv_test_request_well_id'])) {
            \App\Models\TVTestRequestWell::where('id', $data['tv_test_request_well_id'])
                ->update([
                    'test_vessel_test_id' => $tvTest->id,
                    'status' => 'in_progress',
                    'started_by_user_id' => auth()->id(),
                    'started_at' => now(),
                ]);
        }

        return redirect()->route('tv_tests.show', $tvTest->id)
            ->with('success', 'Test Vessel Test created successfully. You can submit it for review now.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TestVesselTest $tvTest)
    {
        $tvTest->load('testVessel', 'user', 'oilField', 'tower', 'oilString', 'mpfmTest');
        return view('tv_tests.show', compact('tvTest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TestVesselTest $tvTest)
    {
        $tvTest->load('testVessel', 'oilField', 'tower', 'oilString', 'mpfmTest');
        $oilFields = OilField::active()->get();
        $testVessels = TestVessel::active()->get();
        $towers = $tvTest->oilField ? Tower::where('oil_field_id', $tvTest->oilField->id)->get() : [];
        $oilStrings = $tvTest->tower ? OilString::where('tower_id', $tvTest->tower->id)->get() : [];
        $mpfmTests = MPFMTest::where('oil_field_id', $tvTest->oil_field_id)->get();

        return view('tv_tests.edit', compact(
            'tvTest',
            'oilFields',
            'testVessels',
            'towers',
            'oilStrings',
            'mpfmTests'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestVesselTestRequest $request, TestVesselTest $tvTest)
    {
        $tvTest->update($request->validated());

        return redirect()->route('tv_tests.index')
            ->with('success', 'Test Vessel Test updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TestVesselTest $tvTest)
    {
        $tvTest->delete();

        return redirect()->route('tv_tests.index')
            ->with('success', 'Test Vessel Test deleted successfully.');
    }

    /**
     * Show comparison form
     */
    public function compare(Request $request)
    {
        // Get TV tests - both submitted and approved
        $tvTests = TestVesselTest::with('testVessel', 'oilField', 'tower', 'oilString')
            ->whereIn('status', ['submitted', 'approved'])
            ->latest('test_date')
            ->get();

        // Get MPFM tests - all statuses
        $mpfmTests = MPFMTest::with('oilField', 'tower', 'oilString')
            ->latest('test_date')
            ->get();

        return view('tv_tests.compare', compact('tvTests', 'mpfmTests'));
    }

    /**
     * Submit a test for review
     */
    public function submit(TestVesselTest $tvTest)
    {
        $this->authorize('update', $tvTest);

        $tvTest->update(['status' => 'submitted']);

        if ($tvTest->requestWell) {
            $tvTest->requestWell->update(['status' => 'submitted']);
        }

        return redirect()->back()->with('success', 'Test submitted for review.');
    }

    /**
     * Show comparison result
     */
    public function compareResult(Request $request)
    {
        $tvTestId = $request->get('tv_test_id');
        $mpfmTestId = $request->get('mpfm_test_id');

        if (!$tvTestId || !$mpfmTestId) {
            return redirect()->route('tv_tests.compare')
                ->with('error', 'Please select both tests to compare.');
        }

        $tvTest = TestVesselTest::findOrFail($tvTestId);
        $mpfmTest = MPFMTest::findOrFail($mpfmTestId);

        // Calculate differences for key metrics
        $metrics = [
            'oil_rate' => [
                'tv_value' => $tvTest->oil_rate,
                'mpfm_value' => $mpfmTest->oil_rate,
            ],
            'gas_rate' => [
                'tv_value' => $tvTest->gas_rate,
                'mpfm_value' => $mpfmTest->gas_rate,
            ],
            'water_cut' => [
                'tv_value' => $tvTest->water_cut,
                'mpfm_value' => $mpfmTest->water_cut,
            ],
            'pressure' => [
                'tv_value' => $tvTest->pressure,
                'mpfm_value' => $mpfmTest->pressure,
            ],
            'temperature' => [
                'tv_value' => $tvTest->temperature,
                'mpfm_value' => $mpfmTest->temperature,
            ],
            'gor' => [
                'tv_value' => $tvTest->gor ?? 0,
                'mpfm_value' => $mpfmTest->gor ?? 0,
            ],
        ];

        // Calculate diffs
        foreach ($metrics as &$metric) {
            $metric['abs_diff'] = abs($metric['tv_value'] - $metric['mpfm_value']);

            // Calculate percentage difference
            $base = max(abs($metric['mpfm_value']), 0.0001); // Avoid division by zero
            $metric['pct_diff'] = ($metric['abs_diff'] / $base) * 100;

            // Determine direction
            if ($metric['tv_value'] > $metric['mpfm_value']) {
                $metric['direction'] = 'higher';
            } elseif ($metric['tv_value'] < $metric['mpfm_value']) {
                $metric['direction'] = 'lower';
            } else {
                $metric['direction'] = 'equal';
            }
        }

        return view('tv_tests.compare_result', compact('tvTest', 'mpfmTest', 'metrics'));
    }
}
