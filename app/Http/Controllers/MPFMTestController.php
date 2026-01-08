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
        $this->authorizeResource(MPFMTest::class, 'mpfm_test');
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

        return view('mpfm_tests.index', compact('oilFields', 'towers', 'oilStrings', 'users'));
    }

    /**
     * Show the form for creating a new resource (log-driven).
     */
    public function create()
    {
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

            // Get unique oil fields and towers from assigned plans
            $assignedFieldIds = $operationalPlans->pluck('oil_field_id')->unique();
            $assignedTowerIds = $operationalPlans->pluck('tower_id')->unique();

            $oilFields = OilField::whereIn('id', $assignedFieldIds)->active()->get();
            $towers = Tower::whereIn('id', $assignedTowerIds)->active()->get();
        } else {
            $operationalPlans = MPFMOperationalPlan::with(['oilField', 'tower'])->active()->get();
        }

        return view('mpfm_tests.create', compact('oilFields', 'towers', 'oilStrings', 'operationalPlans'));
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
            'mpfmOperationalPlan'
        ]);

        return view('mpfm_tests.show', compact('mpfmTest'));
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
}

