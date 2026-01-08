<?php

namespace App\Http\Controllers;

use App\Models\WellTest;
use App\Models\TestData;
use App\Models\OperatorPlan;
use App\Services\LogParserService;
use App\Services\MetricsService;
use App\Services\ChartService;
use App\Services\OperatorPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class WellTestController extends Controller
{
    protected $logParserService;
    protected $metricsService;
    protected $chartService;
    protected $operatorPlanService;

    public function __construct(
        LogParserService $logParserService,
        MetricsService $metricsService,
        ChartService $chartService,
        OperatorPlanService $operatorPlanService
    ) {
        $this->logParserService = $logParserService;
        $this->metricsService = $metricsService;
        $this->chartService = $chartService;
        $this->operatorPlanService = $operatorPlanService;
    }

    public function index()
    {
        $wellTests = WellTest::orderBy('created_at', 'desc')->paginate(10);
        return view('well_tests.index', compact('wellTests'));
    }

    public function show($id)
    {
        $wellTest = WellTest::findOrFail($id);

        // Get test data from JSON column
        $rows = $wellTest->test_data['rows'] ?? [];
        $columns = $wellTest->test_data['columns'] ?? [];

        // Calculate metrics if not already stored
        if (empty($wellTest->calculated_metrics)) {
            $metrics = $this->metricsService->calculateMetrics($rows, $columns);
            $wellTest->update(['calculated_metrics' => $metrics]);
        } else {
            $metrics = $wellTest->calculated_metrics;
        }

        // Prepare chart data
        $flowRatesChartData = $this->chartService->prepareFlowRatesChartData($rows, $columns);
        $gorChartData = $this->chartService->prepareGorChartData($rows, $columns);
        $pressureTempChartData = $this->chartService->preparePressureTempChartData($rows, $columns);

        return view('well_tests.show', [
            'wellTest' => $wellTest,
            'rows' => $rows,
            'columns' => $columns,
            'metrics' => $metrics,
            'rates_chart_data' => $flowRatesChartData,
            'gor_chart_data' => $gorChartData,
            'pressure_temp_chart_data' => $pressureTempChartData,
        ]);
    }

    public function create()
    {
        return view('well_tests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'logfile' => 'required|file|mimes:txt,vxlog,log,csv,tsv|max:10240', // Added tsv and increased size
            'client' => 'required|string|max:255',
            'field' => 'required|string|max:255',
            'well' => 'required|string|max:255',
            'operator' => 'required|string|max:255',
            'test_date' => 'required|date',
            'test_type' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            // Parse the log file
            $parsedData = $this->logParserService->parseLogFile($request->file('logfile'));

            // Validate parsed data
            $validationErrors = $this->logParserService->validateParsedData($parsedData);
            if (!empty($validationErrors)) {
                throw new \Exception('Invalid file format: ' . implode(', ', $validationErrors));
            }

            // Create the WellTest record
            $wellTest = WellTest::create([
                'client' => $request->client,
                'field' => $request->field,
                'well' => $request->well,
                'operator' => $request->operator,
                'test_date' => $request->test_date,
                'test_type' => $request->test_type,
                'filename' => $request->file('logfile')->getClientOriginalName(),
                'log_file_path' => $request->file('logfile')->store('well_test_logs', 'public'),
            ]);

            // Save test data rows to TestData model
            foreach ($parsedData['rows'] as $index => $row) {
                TestData::create([
                    'well_test_id' => $wellTest->id,
                    'data_row' => $row,
                    'row_index' => $index,
                ]);
            }

            // Calculate and store metrics
            $metrics = $this->metricsService->calculateMetrics($parsedData['rows'], $parsedData['columns']);
            $wellTest->update([
                'calculated_metrics' => $metrics
            ]);

            // Generate default operator plan
            $defaultPlanSteps = $this->operatorPlanService->generateDefaultPlan($wellTest);
            $this->operatorPlanService->createMultipleOperatorPlans($defaultPlanSteps, $wellTest->id);
        });

        return redirect()->route('well_tests.index')
            ->with('success', 'Well test created successfully!');
    }

    public function edit($id)
    {
        $wellTest = WellTest::findOrFail($id);
        return view('well_tests.edit', compact('wellTest'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'client' => 'required|string|max:255',
            'field' => 'required|string|max:255',
            'well' => 'required|string|max:255',
            'operator' => 'required|string|max:255',
            'test_date' => 'required|date',
            'test_type' => 'nullable|string|max:255',
            'wellhead_tower' => 'nullable|string|max:255',
            'wellhead_string' => 'nullable|string|max:255',
            'choke_size' => 'nullable|string|max:255',
            'upstream_choke' => 'nullable|string|max:255',
            'downstream_choke' => 'nullable|string|max:255',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
        ]);

        $wellTest = WellTest::findOrFail($id);
        $wellTest->update($request->all());

        return redirect()->route('well_tests.index')
            ->with('success', 'Well test updated successfully!');
    }

    public function destroy($id)
    {
        $wellTest = WellTest::with(['testData', 'operatorPlans'])->findOrFail($id);

        DB::transaction(function () use ($wellTest) {
            // Delete related test data and operator plans
            $wellTest->testData()->delete();
            $wellTest->operatorPlans()->delete();

            // Delete the log file if it exists
            if ($wellTest->log_file_path && Storage::disk('public')->exists($wellTest->log_file_path)) {
                Storage::disk('public')->delete($wellTest->log_file_path);
            }

            $wellTest->delete();
        });

        return redirect()->route('well_tests.index')
            ->with('success', 'Well test deleted successfully!');
    }

    public function getDatatables()
    {
        $query = WellTest::query();

        return DataTables::of($query)
            ->addColumn('display_name', function ($wellTest) {
                return $wellTest->well . ' - ' . $wellTest->test_date;
            })
            ->addColumn('action', function ($wellTest) {
                return '
                    <a href="' . route('well_tests.show', $wellTest->id) . '" class="btn btn-sm btn-primary">View</a>
                    <a href="' . route('well_tests.edit', $wellTest->id) . '" class="btn btn-sm btn-warning">Edit</a>
                    <form method="POST" action="' . route('well_tests.destroy', $wellTest->id) . '" class="d-inline">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                    </form>
                ';
            })
            ->editColumn('created_at', function ($wellTest) {
                return $wellTest->created_at->format('Y-m-d H:i');
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function upload()
    {
        return view('well_tests.upload');
    }

    public function compareForm()
    {
        $wellTests = WellTest::orderBy('test_date', 'desc')->get();
        return view('well_tests.compare', compact('wellTests'));
    }

    public function compare(Request $request)
    {
        $request->validate([
            'test_ids' => 'required|array|min:2',
            'test_ids.*' => 'exists:well_tests,id'
        ]);

        $ids = $request->test_ids;
        $wellTests = WellTest::whereIn('id', $ids)->get();

        $comparisonData = [];
        foreach ($wellTests as $wellTest) {
            // Get test data from JSON column
            $rows = $wellTest->test_data['rows'] ?? [];
            $columns = $wellTest->test_data['columns'] ?? [];

            // Use calculated_metrics if available, otherwise it's already in correct format
            $metrics = $wellTest->calculated_metrics ?? [];

            $comparisonData[] = [
                'id' => $wellTest->id,
                'well' => $wellTest->well,
                'client' => $wellTest->client,
                'field' => $wellTest->field,
                'operator' => $wellTest->operator,
                'test_date' => $wellTest->test_date,
                'test_type' => $wellTest->test_type,
                'wellhead_tower' => $wellTest->wellhead_tower,
                'wellhead_string' => $wellTest->wellhead_string,
                'choke_size' => $wellTest->choke_size,
                'upstream_choke' => $wellTest->upstream_choke,
                'downstream_choke' => $wellTest->downstream_choke,
                'start_time' => $wellTest->start_time ? $wellTest->start_time->format('Y-m-d H:i') : null,
                'end_time' => $wellTest->end_time ? $wellTest->end_time->format('Y-m-d H:i') : null,
                'test_data' => [
                    'rows' => $rows,
                    'columns' => $columns
                ],
                'metrics' => $metrics
            ];
        }

        return view('well_tests.compare-results', compact('comparisonData'));
    }

    // Removed: duplicate method - use compare() instead
}
