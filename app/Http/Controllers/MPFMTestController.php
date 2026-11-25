<?php

namespace App\Http\Controllers;

use App\Models\MPFMTest;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\OilString;
use App\Models\MPFMOperationalPlan;
use App\Http\Requests\StoreMPFMTestRequest;
use App\Http\Requests\UpdateMPFMTestRequest;
use App\Services\MPFMTestService;
use App\Services\MPFMLogProcessingService;
use App\Services\DataTable\MPFMTestDataTableService;
use App\Services\MPFMValidationService;
use App\Services\MPFMHistoricalComparisonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MPFMTestController extends Controller
{
    protected $testService;
    protected $logProcessingService;
    protected $dataTableService;

    public function __construct(
        MPFMTestService $testService,
        MPFMLogProcessingService $logProcessingService,
        MPFMTestDataTableService $dataTableService
    ) {
        $this->testService = $testService;
        $this->logProcessingService = $logProcessingService;
        $this->dataTableService = $dataTableService;
        // Allow supervisors and operators to view MPFM tests
        $this->middleware('auth');
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
        $oilStrings = OilString::active()->get();
        $users = \App\Models\User::role('mpfm_operator')->get();

        $user = auth()->user();
        $base = fn () => $user->hasRole('mpfm_operator') ? MPFMTest::accessibleByOperator($user) : MPFMTest::query();

        $byStatus = $base()->selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status')->all();
        $days = collect(range(29, 0))->map(fn ($i) => now()->subDays($i)->toDateString());
        $daily = $base()->selectRaw('DATE(test_date) d, COUNT(*) c')
            ->where('test_date', '>=', $days->first())->groupBy('d')->pluck('c', 'd');
        $stats = [
            'total' => array_sum($byStatus),
            'byStatus' => $byStatus,
            'week' => $base()->where('test_date', '>=', now()->subDays(7))->count(),
            'avgOil' => (float) $base()->avg('oil_rate'),
            'avgGas' => (float) $base()->avg('gas_rate'),
            'avgWc' => (float) $base()->avg('water_cut'),
            'activity' => [
                'labels' => $days->map(fn ($d) => substr($d, 5))->values(),
                'values' => $days->map(fn ($d) => (int) ($daily[$d] ?? 0))->values(),
            ],
            'strings' => $base()->selectRaw('oil_string_id, COUNT(*) c')->whereNotNull('oil_string_id')
                ->groupBy('oil_string_id')->orderByDesc('c')->limit(6)->get()
                ->map(fn ($r) => ['name' => optional(OilString::find($r->oil_string_id))->name ?? '#'.$r->oil_string_id, 'count' => (int) $r->c])->values(),
        ];

        return view('mpfm_tests.index', compact('oilFields', 'towers', 'oilStrings', 'users', 'stats'));
    }

    /**
     * Tests are created from an uploaded VX log; the log-processing endpoints live on the upload page.
     */
    public function create(Request $request)
    {
        return redirect()->route('upload.form', $request->query());
    }

    /**
     * Show the log upload form for MPFM tests.
     */
    public function uploadForm()
    {
        return view('mpfm_tests.upload');
    }

    /**
     * Process uploaded log file and return enhanced data for dynamic display
     */
    public function processUpload(Request $request)
    {
        $result = $this->logProcessingService->processLogFile($request);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 422);
        }

        try {
            // Return enhanced data for JavaScript processing
            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            // Clean up temporary file
            if (isset($result['temp_file_path']) && Storage::disk('public')->exists($result['temp_file_path'])) {
                Storage::disk('public')->delete($result['temp_file_path']);
            }

            return response()->json([
                'success' => false,
                'error' => 'Error processing data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Safe datetime parsing.
     */
    private function parseDateTimeSafe(?string $dateTimeStr, \Carbon\Carbon $default): ?\Carbon\Carbon
    {
        if (!$dateTimeStr) {
            return null;
        }

        try {
            $formats = [
                'Y-m-d H:i:s',
                'm/d/Y H:i:s',
                'd/m/Y H:i:s',
                'Y-m-d H:i',
                'H:i:s',
                'H:i'
            ];

            foreach ($formats as $format) {
                try {
                    return \Carbon\Carbon::createFromFormat($format, $dateTimeStr);
                } catch (\Exception $e) {
                    continue;
                }
            }

            $timestamp = strtotime($dateTimeStr);
            if ($timestamp) {
                return \Carbon\Carbon::createFromTimestamp($timestamp);
            }
        } catch (\Exception $e) {
            // Ignore and return null
        }

        return null;
    }

    /**
     * Helper: Determine shift based on time.
     */
    private function determineShift(?\Carbon\Carbon $dateTime): string
    {
        if (!$dateTime) {
            return 'morning';
        }

        $hour = $dateTime->hour;
        if ($hour >= 6 && $hour < 14) {
            return 'morning';
        } elseif ($hour >= 14 && $hour < 22) {
            return 'afternoon';
        } else {
            return 'night';
        }
    }

    /**
     * Process uploaded log file for MPFM test creation
     */
    public function processLog(Request $request)
    {
        $result = $this->logProcessingService->processLogFile($request);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Show preview of processed log data before saving
     */
    public function preview(Request $request)
    {
        // Get data from request (passed from JavaScript)
        $processingResult = $request->input('processing_result');

        if (!$processingResult) {
            return redirect()->route('mpfm_tests.create')
                ->withErrors(['error' => 'No processing data available. Please upload a log file first.']);
        }

        // Prepare data for display
        $displayData = $this->prepareLogDataForDisplay($processingResult);

        // Validate MPFM data
        $validation = $this->logProcessingService->validateMPFMData($displayData['mpfm_data']);

        $displayData['validation'] = $validation;
        $displayData['processing_result'] = $processingResult;

        // Get related data for form
        $user = auth()->user();
        $oilFields = OilField::active()->get();
        $towers = collect();
        $oilStrings = collect();
        $operationalPlans = collect();

        // If user is operator, only show assigned assets
        if ($user->hasRole('mpfm_operator')) {
            $planIds = MPFMOperationalPlan::where('assigned_user_id', $user->id)
                ->where('status', 'active')
                ->pluck('id');

            $operationalPlans = MPFMOperationalPlan::whereIn('id', $planIds)
                ->with(['oilField', 'tower'])
                ->get();

            $assignedFieldIds = $operationalPlans->pluck('oil_field_id')->unique();
            $assignedTowerIds = $operationalPlans->pluck('tower_id')->unique();

            $oilFields = OilField::whereIn('id', $assignedFieldIds)->active()->get();
            $towers = Tower::whereIn('id', $assignedTowerIds)->active()->get();
        } else {
            $operationalPlans = MPFMOperationalPlan::with(['oilField', 'tower'])->active()->get();
        }

        $displayData['oilFields'] = $oilFields;
        $displayData['towers'] = $towers;
        $displayData['oilStrings'] = $oilStrings;
        $displayData['operationalPlans'] = $operationalPlans;

        return view('mpfm_tests.preview', $displayData);
    }

    /**
     * Prepare log data for display using enhanced processing results.
     */
    private function prepareLogDataForDisplay(array $processingResult): array
    {
        $processedData = $processingResult['processed_data'];
        $parsedData = $processingResult['parsed_data'];

        // Extract data from the enhanced processing result
        $averages = $processedData['averages'] ?? [];
        $mpfmData = $processedData['mpfm_data'] ?? [];
        $chartData = $processedData['chart_data'] ?? [];
        $metadata = $processedData['metadata'] ?? [];

        return [
            'filename' => $processingResult['filename'],
            'file_size' => $processingResult['file_size'],
            'raw_log_content' => $processingResult['raw_log_content'],
            'temp_file_path' => $processingResult['temp_file_path'],
            'parsed_data' => $parsedData,

            'summary' => [
                'test_date' => $mpfmData['test_date'] ?? date('Y-m-d'),
                'shift' => $mpfmData['shift'] ?? 'morning',
                'start_time' => $metadata['start_time'] ?? null,
                'end_time' => $metadata['end_time'] ?? null,
                'duration_minutes' => $metadata['duration_minutes'] ?? null,
                'total_rows' => $metadata['total_rows'] ?? 0,
                'sampling_interval' => $metadata['sampling_interval_seconds'] ?? null,
            ],

            'averages' => $averages,
            'mpfm_data' => $mpfmData,
            'chart_data' => $chartData,
            'rates_chart_data' => $chartData['rates_chart'] ?? null,
            'gor_chart_data' => $chartData['gor_chart'] ?? null,
            'water_cut_chart_data' => $chartData['water_cut_chart'] ?? null,
            'temperature_chart_data' => $chartData['temperature_chart'] ?? null,
            'pressure_chart_data' => $chartData['pressure_chart'] ?? null,
            'production_comparison_chart_data' => $chartData['production_comparison_chart'] ?? null,
            'chart_time_column' => $chartData['time_column'] ?? 'Date & Time',

            'data_quality' => $processedData['data_quality'] ?? [],
            'full_average_row' => $averages, // For compatibility
        ];
    }

    /**
     * Store MPFM test from log data
     */
    public function storeFromLog(Request $request)
    {
        $request->validate([
            'temp_file_path' => 'required|string',
            'mpfm_data' => 'required|array',
            'oil_field_id' => 'required|exists:oil_fields,id',
            'tower_id' => 'required|exists:towers,id',
            'oil_string_id' => 'required|exists:oil_strings,id',
            'mpfm_operational_plan_id' => 'nullable|exists:mpfm_operational_plans,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();

        // Validate operator access
        if ($user->hasRole('mpfm_operator')) {
            if (!$this->testService->validateOperatorAccess(
                $user,
                $request->oil_field_id,
                $request->tower_id,
                $request->oil_string_id
            )) {
                return redirect()->back()
                    ->withErrors(['error' => 'You do not have access to create tests for these assets.'])
                    ->withInput();
            }
        }

        try {
            // Move log file to permanent storage
            $permanentPath = $this->logProcessingService->moveToPermanentStorage($request->temp_file_path);

            if (!$permanentPath) {
                return redirect()->back()
                    ->withErrors(['error' => 'Failed to save log file.'])
                    ->withInput();
            }

            // Prepare test data
            $mpfmData = $request->mpfm_data;
            $testData = array_merge($mpfmData, [
                'user_id' => $user->id,
                'oil_field_id' => $request->oil_field_id,
                'tower_id' => $request->tower_id,
                'oil_string_id' => $request->oil_string_id,
                'mpfm_operational_plan_id' => $request->mpfm_operational_plan_id,
                'notes' => $request->notes,
                'status' => 'draft',
                'log_file_path' => $permanentPath,
                'raw_log_data' => $request->input('parsed_data', []),
            ]);

            // Create test with KPIs
            $test = $this->testService->createOrUpdate($testData);

            return redirect()->route('mpfm_tests.show', $test)
                ->with('success', 'MPFM test created successfully from log file!');

        } catch (\Exception $e) {
            // Clean up file if it was moved
            if (isset($permanentPath) && Storage::disk('public')->exists($permanentPath)) {
                Storage::disk('public')->delete($permanentPath);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Failed to create MPFM test: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMPFMTestRequest $request)
    {
        $user = auth()->user();

        // Validate operator access
        if ($user->hasRole('mpfm_operator')) {
            if (!$this->testService->validateOperatorAccess(
                $user,
                $request->oil_field_id,
                $request->tower_id,
                $request->oil_string_id
            )) {
                return redirect()->back()
                    ->withErrors(['error' => 'You do not have access to create tests for these assets.'])
                    ->withInput();
            }
        }

        $data = $request->validated();
        $data['user_id'] = $user->id;

        $test = $this->testService->createOrUpdate($data);

        return redirect()->route('mpfm_tests.index')
            ->with('success', 'MPFM Test created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MPFMTest $mpfmTest)
    {
        $mpfmTest->load([
            'user',
            'oilField',
            'tower',
            'oilString',
            'mpfmOperationalPlan',
            'wellTest'
        ]);

        // Get well test data if available
        $wellTest = $mpfmTest->wellTest;
        $wellTestData = null;
        $metrics = [];
        $rows = [];
        $columns = [];
        $flowRatesChartData = null;
        $gorChartData = null;
        $pressureTempChartData = null;

        if ($wellTest) {
            // Extract test data from well test
            $rows = $wellTest->test_data['rows'] ?? [];
            $columns = $wellTest->test_data['columns'] ?? [];

            // Use calculated metrics from well test
            if (!empty($wellTest->calculated_metrics)) {
                $metrics = $wellTest->calculated_metrics;
            } else {
                // Fallback: calculate metrics if not stored
                $metricsService = app(\App\Services\MetricsService::class);
                $metrics = $metricsService->calculateMetrics($rows, $columns);
            }

            // Prepare chart data
            $chartService = app(\App\Services\ChartService::class);
            $flowRatesChartData = $chartService->prepareFlowRatesChartData($rows, $columns);
            $gorChartData = $chartService->prepareGorChartData($rows, $columns);
            $pressureTempChartData = $chartService->preparePressureTempChartData($rows, $columns);

            $wellTestData = $wellTest;
        }

        // Fallback: Prepare KPIs from MPFM test if no well test
        $kpis = [];
        if ($mpfmTest->calculated_kpis) {
            $kpis = $mpfmTest->calculated_kpis;
        }

        // Fallback to raw database fields if calculated_kpis is empty
        if (empty($kpis['oil_rate']) && $mpfmTest->qo_sc) {
            $kpis['oil_rate'] = $mpfmTest->qo_sc ?? ($mpfmTest->qo_lc ?? 0);
        }
        if (empty($kpis['gas_rate']) && $mpfmTest->qg_sc) {
            $kpis['gas_rate'] = $mpfmTest->qg_sc ?? ($mpfmTest->qg_lc ?? 0);
        }
        if (empty($kpis['avg_pressure']) && $mpfmTest->pressure_avg) {
            $kpis['pressure'] = $mpfmTest->pressure_avg;
        }
        if (empty($kpis['avg_temperature']) && $mpfmTest->temperature_avg) {
            $kpis['temperature'] = $mpfmTest->temperature_avg;
        }

        $relatedTv = \App\Models\TestVesselTest::with('testVessel')
            ->where('oil_string_id', $mpfmTest->oil_string_id)
            ->latest('test_date')->limit(5)->get();

        return view('mpfm_tests.show_modern', compact(
            'mpfmTest',
            'kpis',
            'wellTest',
            'wellTestData',
            'metrics',
            'rows',
            'columns',
            'flowRatesChartData',
            'gorChartData',
            'pressureTempChartData',
            'relatedTv'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MPFMTest $mpfmTest)
    {
        $user = auth()->user();
        $oilFields = OilField::active()->get();
        $towers = Tower::where('oil_field_id', $mpfmTest->oil_field_id)->active()->get();
        $oilStrings = OilString::where('tower_id', $mpfmTest->tower_id)->active()->get();
        $operationalPlans = MPFMOperationalPlan::with(['oilField', 'tower'])->active()->get();

        return view('mpfm_tests.edit', compact(
            'mpfmTest',
            'oilFields',
            'towers',
            'oilStrings',
            'operationalPlans'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMPFMTestRequest $request, MPFMTest $mpfmTest)
    {
        $user = auth()->user();

        // Validate operator access if changing assets
        if ($user->hasRole('mpfm_operator')) {
            if ($request->oil_field_id != $mpfmTest->oil_field_id ||
                $request->tower_id != $mpfmTest->tower_id ||
                $request->oil_string_id != $mpfmTest->oil_string_id) {

                if (!$this->testService->validateOperatorAccess(
                    $user,
                    $request->oil_field_id,
                    $request->tower_id,
                    $request->oil_string_id
                )) {
                    return redirect()->back()
                        ->withErrors(['error' => 'You do not have access to these assets.'])
                        ->withInput();
                }
            }
        }

        $this->testService->createOrUpdate($request->validated(), $mpfmTest);

        return redirect()->route('mpfm_tests.index')
            ->with('success', 'MPFM Test updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MPFMTest $mpfmTest)
    {
        $mpfmTest->delete();

        return redirect()->route('mpfm_tests.index')
            ->with('success', 'MPFM Test deleted successfully.');
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

    /**
     * Show test with historical comparison
     */
    public function showWithHistory(MPFMTest $mpfmTest)
    {
        $mpfmTest->load([
            'planTask.oilString',
            'latestValidation',
            'latestComparison'
        ]);

        // Auto-validate if no validation exists
        if (!$mpfmTest->latestValidation && $mpfmTest->raw_log_data) {
            try {
                $validationService = app(MPFMValidationService::class);
                $validationService->validateTest($mpfmTest);
                $mpfmTest->load('latestValidation');
            } catch (\Exception $e) {
                // Silently fail if validation service not available
            }
        }

        // Get all related tests for the same oil string for trending
        $trendingCharts = $this->prepareTrendingCharts($mpfmTest);

        return view('mpfm_tests.show_with_history', compact('mpfmTest', 'trendingCharts'));
    }

    /**
     * Prepare trending chart data for a test's oil string
     */
    private function prepareTrendingCharts(MPFMTest $mpfmTest): array
    {
        if (!$mpfmTest->oil_string_id) {
            return [];
        }

        // Get all approved tests for this oil string, ordered by test date
        $relatedTests = MPFMTest::where('oil_string_id', $mpfmTest->oil_string_id)
            ->where('status', 'approved')
            ->orderBy('test_date', 'asc')
            ->get();

        if ($relatedTests->isEmpty()) {
            return [];
        }

        // Prepare chart data
        $testDates = $relatedTests->map(fn($test) => $test->test_date->format('M d, Y'))->toArray();

        return [
            'production_rates' => [
                'labels' => $testDates,
                'datasets' => [
                    [
                        'label' => 'Oil Rate SC (STB/d)',
                        'data' => $relatedTests->pluck('qo_sc')->toArray(),
                        'borderColor' => '#667eea',
                        'backgroundColor' => 'rgba(102, 126, 234, 0.1)',
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'Water Rate SC (STB/d)',
                        'data' => $relatedTests->pluck('qw_sc')->toArray(),
                        'borderColor' => '#e74c3c',
                        'backgroundColor' => 'rgba(231, 76, 60, 0.1)',
                        'tension' => 0.4,
                    ],
                ]
            ],
            'gor_trend' => [
                'labels' => $testDates,
                'datasets' => [
                    [
                        'label' => 'GOR (Scf/STB)',
                        'data' => $relatedTests->pluck('gor')->toArray(),
                        'borderColor' => '#f39c12',
                        'backgroundColor' => 'rgba(243, 156, 18, 0.1)',
                        'tension' => 0.4,
                    ]
                ]
            ],
            'bsw_trend' => [
                'labels' => $testDates,
                'datasets' => [
                    [
                        'label' => 'BSW (%)',
                        'data' => $relatedTests->pluck('bsw')->toArray(),
                        'borderColor' => '#e67e22',
                        'backgroundColor' => 'rgba(230, 126, 34, 0.1)',
                        'tension' => 0.4,
                    ]
                ]
            ],
            'wlr_trend' => [
                'labels' => $testDates,
                'datasets' => [
                    [
                        'label' => 'WLR',
                        'data' => $relatedTests->pluck('wlr')->toArray(),
                        'borderColor' => '#3498db',
                        'backgroundColor' => 'rgba(52, 152, 219, 0.1)',
                        'tension' => 0.4,
                    ]
                ]
            ]
        ];
    }

    /**
     * Show string analysis with all tests and trends
     */
    public function stringAnalysis($oilStringId)
    {
        $oilString = OilString::with('tower', 'tower.oilField')->findOrFail($oilStringId);

        // Get MPFM Tests
        $mpfmTests = MPFMTest::where('oil_string_id', $oilStringId)
            ->with(['oilField', 'tower', 'user'])
            ->orderBy('test_date', 'desc')
            ->get();

        // Get TV Tests
        $tvTests = \App\Models\TestVesselTest::where('oil_string_id', $oilStringId)
            ->with(['testVessel', 'oilField', 'tower', 'user'])
            ->orderBy('test_date', 'desc')
            ->get();

        // Merge and sort by date
        $allTests = collect()
            ->merge($mpfmTests->map(fn($t) => ['type' => 'mpfm', 'test' => $t]))
            ->merge($tvTests->map(fn($t) => ['type' => 'tv', 'test' => $t]))
            ->sortByDesc(fn($item) => $item['test']->test_date);

        $comparisonService = app(MPFMHistoricalComparisonService::class);
        $trendingData = $comparisonService->getStringTrendingAnalysis($oilStringId);

        $wlAccess = auth()->user()?->canAccessWireline() ?? false;
        $intAccess = auth()->user()?->canAccessIntegrity() ?? false;
        $wireline = $planTasks = $tasks = $analysis = $integrity = null;
        if ($wlAccess || $intAccess) {
            // Wire Line users see everything on the well: wire line history plus work from every department
            $wireline = app(\App\Services\Wireline\WellWirelineSummary::class)->build($oilString);
            $planTasks = \App\Models\PlanTask::where('oil_string_id', $oilStringId)
                ->with(['department', 'assignedOperator'])->orderByDesc('scheduled_date')->get();
            $tasks = \App\Domains\Task\Models\Task::where('oil_string_id', $oilStringId)
                ->with(['department', 'assignedTo'])->orderByDesc('created_at')->get();
            // Integrity feeds the analysis: health score, production state, PI caveats and recommendations
            $integrity = $intAccess ? app(\App\Services\Integrity\WellIntegrityAssessor::class)->assess($oilString) : null;
            $analysis = app(\App\Services\WellAnalysisService::class)->analyze($oilString, $mpfmTests, $tvTests, $wireline, $planTasks, $tasks, $integrity);
        }

        return view('mpfm_tests.string_analysis', compact('oilString', 'allTests', 'mpfmTests', 'tvTests', 'trendingData', 'wlAccess', 'intAccess', 'wireline', 'planTasks', 'tasks', 'analysis', 'integrity'));
    }

    /**
     * Show trending dashboard for all strings
     */
    public function trendingDashboard(Request $request)
    {
        $strings = OilString::with('tower.oilField')->get();

        $comparisonService = app(MPFMHistoricalComparisonService::class);

        $testsByString = MPFMTest::select('id', 'oil_string_id', 'test_date', 'oil_rate', 'water_cut', 'status')
            ->orderBy('test_date')->get()->groupBy('oil_string_id');

        $stringsWithAlerts = $strings->map(function ($string) use ($comparisonService, $testsByString) {
            $trending = $comparisonService->getStringTrendingAnalysis($string->id);
            $tests = $testsByString->get($string->id, collect());

            return [
                'string' => $string,
                'alerts' => $trending['alerts'] ?? [],
                'alert_count' => count($trending['alerts'] ?? []),
                'tests' => $tests->count(),
                'latest' => $tests->last(),
                'spark' => $tests->take(-8)->pluck('oil_rate')->map(fn ($v) => (float) $v)->values()->all(),
            ];
        })->sortByDesc('alert_count')->values();

        return view('mpfm_tests.trending_dashboard', compact('stringsWithAlerts'));
    }

    /**
     * Revalidate a test
     */
    public function revalidate(MPFMTest $mpfmTest)
    {
        $validationService = app(MPFMValidationService::class);
        $validationService->validateTest($mpfmTest, true);

        return redirect()->back()->with('success', 'Test revalidated successfully');
    }

    /**
     * Download log file for a test
     */
    public function downloadLog(MPFMTest $mpfmTest)
    {
        if (!$mpfmTest->log_file_path) {
            return redirect()->back()->with('error', 'No log file associated with this test');
        }

        $filePath = storage_path('app/public/' . $mpfmTest->log_file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Log file not found');
        }

        return response()->download($filePath, basename($filePath));
    }

    /**
     * Approve test by Department Supervisor
     * Transitions: draft/submitted -> approved_by_department
     * Next step: Surveillance Supervisor review
     */
    public function approveByDepartment(Request $request, MPFMTest $mpfmTest)
    {
        // Check authorization
        if (!auth()->user()->hasRole(['department_supervisor', 'hq_supervisor'])) {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $mpfmTest->update([
                'status' => 'approved_by_department',
                'supervisor_remarks' => $validated['approval_notes'] ?? null,
                'department_supervisor_id' => auth()->id(),
                'approved_by_department_at' => now(),
            ]);

            // Log state transition
            \App\Models\Activity::create([
                'subject_type' => MPFMTest::class,
                'subject_id' => $mpfmTest->id,
                'description' => 'Test approved by Department Supervisor',
                'causer_id' => auth()->id(),
                'properties' => [
                    'from_status' => 'submitted',
                    'to_status' => 'approved_by_department',
                    'notes' => $validated['approval_notes'] ?? null,
                ],
            ]);

            // Send notification to Surveillance Supervisor
            $surveillanceSupervisors = \App\Models\User::role('surveillance_supervisor')
                ->where('department_id', $mpfmTest->department_id)
                ->get();

            foreach ($surveillanceSupervisors as $supervisor) {
                $supervisor->notify(new \App\Notifications\TestAwaitingSurveillanceApprovalNotification($mpfmTest));
            }

            return redirect()->back()->with('success', 'Test approved by Department. Forwarded to Surveillance Supervisor for review.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error approving test: ' . $e->getMessage());
        }
    }

    /**
     * Approve test by Surveillance Supervisor
     * Can skip Department Supervisor level
     * Transitions: draft/submitted/approved_by_department -> approved_by_surveillance
     * Next step: Automatic forward to Job Officer
     */
    public function approveBySurveillance(Request $request, MPFMTest $mpfmTest)
    {
        // Check authorization
        if (!auth()->user()->hasRole(['surveillance_supervisor', 'hq_supervisor'])) {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $oldStatus = $mpfmTest->status;

            $mpfmTest->update([
                'status' => 'approved_by_surveillance',
                'surveillance_supervisor_id' => auth()->id(),
                'approved_by_surveillance_at' => now(),
                'supervisor_remarks' => $validated['approval_notes'] ?? $mpfmTest->supervisor_remarks,
            ]);

            // Log state transition
            \App\Models\Activity::create([
                'subject_type' => MPFMTest::class,
                'subject_id' => $mpfmTest->id,
                'description' => 'Test approved by Surveillance Supervisor (skipped Department level)',
                'causer_id' => auth()->id(),
                'properties' => [
                    'from_status' => $oldStatus,
                    'to_status' => 'approved_by_surveillance',
                    'notes' => $validated['approval_notes'] ?? null,
                ],
            ]);

            // Notify Department Supervisor about the approval
            $departmentSupervisor = \App\Models\User::find($mpfmTest->department_supervisor_id ?? null);
            if ($departmentSupervisor) {
                $departmentSupervisor->notify(new \App\Notifications\TestApprovedBySurveillanceNotification($mpfmTest));
            }

            // Forward to Job Officer
            $jobOfficers = \App\Models\User::role('job_officer')
                ->where('department_id', $mpfmTest->department_id)
                ->get();

            foreach ($jobOfficers as $officer) {
                $officer->notify(new \App\Notifications\TestReadyForJobOfficerReviewNotification($mpfmTest));
            }

            // Notify Operator
            $mpfmTest->user->notify(new \App\Notifications\TestApprovedNotification($mpfmTest));

            return redirect()->back()->with('success', 'Test approved by Surveillance. Automatically forwarded to Job Officer.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error approving test: ' . $e->getMessage());
        }
    }

    /**
     * Reject test (from any supervisor level)
     * Transitions: any -> rejected
     * Action: Request operator to re-upload/resubmit
     */
    public function rejectTest(Request $request, MPFMTest $mpfmTest)
    {
        // Check authorization
        if (!auth()->user()->hasRole(['department_supervisor', 'surveillance_supervisor', 'hq_supervisor'])) {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|in:invalid_data,equipment_failure,test_interrupted,calibration_issue,environmental_conditions,data_quality,other',
            'rejection_details' => 'required|string|max:2000',
        ]);

        try {
            $rejectedByRole = auth()->user()->roles->first()->name ?? 'supervisor';

            $mpfmTest->update([
                'status' => 'rejected',
                'rejected_reason' => $validated['rejection_reason'],
                'rejected_details' => $validated['rejection_details'],
                'rejected_by_user_id' => auth()->id(),
                'rejected_by_role' => $rejectedByRole,
                'rejected_at' => now(),
            ]);

            // Log state transition
            \App\Models\Activity::create([
                'subject_type' => MPFMTest::class,
                'subject_id' => $mpfmTest->id,
                'description' => 'Test rejected by ' . ucfirst(str_replace('_', ' ', $rejectedByRole)),
                'causer_id' => auth()->id(),
                'properties' => [
                    'rejection_reason' => $validated['rejection_reason'],
                    'rejection_details' => $validated['rejection_details'],
                ],
            ]);

            // Notify Operator to re-upload
            $mpfmTest->user->notify(new \App\Notifications\TestRejectedNotification($mpfmTest, $validated['rejection_reason'], $validated['rejection_details']));

            return redirect()->back()->with('success', 'Test rejected. Operator has been notified to re-upload.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error rejecting test: ' . $e->getMessage());
        }
    }

    /**
     * Request a re-test (from any supervisor level)
     * Creates a new task for operator to perform re-test
     * Keeps original test data for reference
     */
    public function requestRetest(Request $request, MPFMTest $mpfmTest)
    {
        // Check authorization
        if (!auth()->user()->hasRole(['department_supervisor', 'surveillance_supervisor', 'hq_supervisor'])) {
            return redirect()->back()->with('error', 'Unauthorized action');
        }

        $validated = $request->validate([
            'retest_reason' => 'required|string|in:data_inconsistency,incomplete_test,equipment_suspect,validation_failed,trend_unusual,anomaly_detected,other',
            'retest_details' => 'required|string|max:2000',
            'expected_retest_date' => 'required|date|after:today',
            'retest_priority' => 'required|string|in:high,normal,low',
        ]);

        try {
            // Mark original test as pending re-test
            $mpfmTest->update([
                'status' => 'retest_requested',
                'retest_reason' => $validated['retest_reason'],
                'retest_details' => $validated['retest_details'],
                'retest_requested_by_user_id' => auth()->id(),
                'retest_requested_at' => now(),
            ]);

            // Create a new task for the operator to perform re-test
            $newTask = \App\Domains\Task\Models\Task::create([
                'title' => 'Re-test MPFM - ' . $mpfmTest->oilString->name . ' (Test #' . $mpfmTest->id . ')',
                'description' => $validated['retest_details'],
                'department_id' => $mpfmTest->department_id,
                'task_type' => 'mpfm',
                'state' => \App\Enums\TaskState::DRAFT,
                'priority' => $this->mapPriorityLevel($validated['retest_priority']),
                'oil_string_id' => $mpfmTest->oil_string_id,
                'tower_id' => $mpfmTest->tower_id,
                'assigned_to_user_id' => $mpfmTest->user_id, // Assign back to same operator
                'created_by_user_id' => auth()->id(),
                'expected_completion_at' => \Carbon\Carbon::parse($validated['expected_retest_date']),
                'type_specific_data' => [
                    'original_test_id' => $mpfmTest->id,
                    'retest_reason' => $validated['retest_reason'],
                    'retest_instructions' => $validated['retest_details'],
                ],
            ]);

            // Log state transition
            \App\Models\Activity::create([
                'subject_type' => MPFMTest::class,
                'subject_id' => $mpfmTest->id,
                'description' => 'Re-test requested by ' . auth()->user()->name,
                'causer_id' => auth()->id(),
                'properties' => [
                    'new_task_id' => $newTask->id,
                    'retest_reason' => $validated['retest_reason'],
                    'expected_retest_date' => $validated['expected_retest_date'],
                ],
            ]);

            // Notify Operator about the new task
            $mpfmTest->user->notify(new \App\Notifications\ReteststRequestedNotification($mpfmTest, $newTask, $validated['retest_details']));

            // Notify Department Supervisor
            if ($mpfmTest->department->supervisor) {
                $mpfmTest->department->supervisor->notify(new \App\Notifications\RetestTaskCreatedNotification($newTask, $mpfmTest));
            }

            return redirect()->back()->with('success', 'Re-test task created and operator notified.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating re-test task: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to map priority level
     */
    private function mapPriorityLevel($level)
    {
        return match($level) {
            'high' => 'critical',
            'normal' => 'normal',
            'low' => 'low',
            default => 'normal',
        };
    }
}

