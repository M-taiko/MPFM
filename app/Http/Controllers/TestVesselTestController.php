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
        $byStatus = TestVesselTest::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status')->all();
        $days = collect(range(29, 0))->map(fn ($i) => now()->subDays($i)->toDateString());
        $daily = TestVesselTest::selectRaw('DATE(test_date) d, COUNT(*) c')
            ->where('test_date', '>=', $days->first())->groupBy('d')->pluck('c', 'd');
        $stats = [
            'total' => array_sum($byStatus),
            'byStatus' => $byStatus,
            'week' => TestVesselTest::where('test_date', '>=', now()->subDays(7))->count(),
            'linked' => TestVesselTest::whereNotNull('mpfm_test_id')->count(),
            'avgOil' => (float) TestVesselTest::avg('oil_rate'),
            'avgWc' => (float) TestVesselTest::avg('water_cut'),
            'activity' => [
                'labels' => $days->map(fn ($d) => substr($d, 5))->values(),
                'values' => $days->map(fn ($d) => (int) ($daily[$d] ?? 0))->values(),
            ],
            'vessels' => TestVessel::withCount('tests')->orderByDesc('tests_count')->limit(6)->get(['id', 'name'])
                ->map(fn ($v) => ['name' => $v->name, 'count' => $v->tests_count])->values(),
        ];
        return view('tv_tests.index', compact('oilFields', 'testVessels', 'stats'));
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
        $mpfmTests = MPFMTest::with('oilString')->orderByDesc('test_date')->limit(300)->get();

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
        $tvTest->load('testVessel', 'user', 'oilField', 'tower', 'oilString', 'mpfmTest', 'reviewedBy');

        // last tests on the same oil string (oldest first) for the trend charts
        $history = TestVesselTest::where('oil_string_id', $tvTest->oil_string_id)
            ->orderByDesc('test_date')->orderByDesc('id')
            ->limit(12)->get()->reverse()->values()
            ->map(fn ($t) => [
                'id' => $t->id,
                'date' => $t->test_date->format('Y-m-d'),
                'oil' => (float) $t->oil_rate,
                'gas' => (float) $t->gas_rate,
                'wc' => (float) $t->water_cut,
            ]);

        $mpfmForWell = MPFMTest::where('oil_string_id', $tvTest->oil_string_id)
            ->orderByDesc('test_date')->get();
        $mpfmNearby = $mpfmForWell->filter(fn ($m) => abs($m->test_date->diffInDays($tvTest->test_date)) <= 7)->values();

        return view('tv_tests.show', compact('tvTest', 'history', 'mpfmForWell', 'mpfmNearby'));
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

        $tvTest = TestVesselTest::with(['oilField', 'tower', 'oilString', 'user'])->findOrFail($tvTestId);
        $mpfmTest = MPFMTest::with(['oilField', 'tower', 'oilString', 'user', 'wellTest'])->findOrFail($mpfmTestId);

        // Get MPFM metrics - prefer wellTest data which is more accurate
        $mpfmMetrics = [];
        if ($mpfmTest->wellTest && $mpfmTest->wellTest->calculated_metrics) {
            $mpfmMetrics = $mpfmTest->wellTest->calculated_metrics;
        } elseif ($mpfmTest->calculated_kpis) {
            $mpfmMetrics = $mpfmTest->calculated_kpis;
        }

        // Well test metrics use bsw / avg_pressure / avg_temperature; fall back to the MPFM test's own fields
        $mpfmMetrics += [
            'oil_rate' => $mpfmTest->oil_rate,
            'gas_rate' => $mpfmTest->gas_rate,
        ];
        $mpfmMetrics['water_cut'] = $mpfmMetrics['water_cut'] ?? $mpfmMetrics['bsw'] ?? $mpfmTest->water_cut;
        $mpfmMetrics['pressure'] = $mpfmMetrics['pressure'] ?? $mpfmMetrics['avg_pressure'] ?? null;
        $mpfmMetrics['temperature'] = $mpfmMetrics['temperature'] ?? $mpfmMetrics['avg_temperature'] ?? null;

        // Get TV metrics
        $tvMetrics = [
            'oil_rate' => $tvTest->oil_rate,
            'gas_rate' => $tvTest->gas_rate,
            'water_cut' => $tvTest->water_cut,
            'pressure' => $tvTest->pressure,
            'temperature' => $tvTest->temperature,
            'gor' => $tvTest->gor,
        ];

        // Calculate differences for all metrics
        $metrics = [];
        $metricNames = ['oil_rate', 'gas_rate', 'water_cut', 'pressure', 'temperature', 'gor'];

        foreach ($metricNames as $name) {
            $tvValue = $tvMetrics[$name] ?? null;
            $mpfmValue = $mpfmMetrics[$name] ?? null;

            if ($tvValue !== null && $mpfmValue !== null) {
                $metrics[$name] = [
                    'tv_value' => $tvValue,
                    'mpfm_value' => $mpfmValue,
                    'abs_diff' => abs($tvValue - $mpfmValue),
                ];

                // Calculate percentage difference
                $base = max(abs($mpfmValue), 0.0001);
                $metrics[$name]['pct_diff'] = ($metrics[$name]['abs_diff'] / $base) * 100;

                // Determine direction
                if ($tvValue > $mpfmValue) {
                    $metrics[$name]['direction'] = 'higher';
                } elseif ($tvValue < $mpfmValue) {
                    $metrics[$name]['direction'] = 'lower';
                } else {
                    $metrics[$name]['direction'] = 'equal';
                }
            }
        }

        return view('tv_tests.compare_result_modern', compact('tvTest', 'mpfmTest', 'metrics', 'tvMetrics', 'mpfmMetrics'));
    }
}
