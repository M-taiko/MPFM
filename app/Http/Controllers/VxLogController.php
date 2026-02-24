<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DateTime;
use App\Models\WellTest;
use App\Models\PlanTask;
use App\Models\MPFMTest;
use App\Domains\Task\Models\Task as NewTask;
use App\Services\MPFMCalculationService;
use App\Services\MPFMParsingService;
use App\Services\MPFMChartService;
use App\Services\MPFMPreviewService;
use App\Services\TaskStateService;
use App\Services\MPFMValidationService;
use App\Services\MPFMDetailedMetricsExtractorService;
use App\Enums\TaskState;
use App\Notifications\TaskCompletedNotification;
use App\Notifications\TestUploadedNotification;
use App\Models\User;

class VxLogController extends Controller
{
    protected $calculationService;
    protected $parsingService;
    protected $chartService;
    protected $previewService;
    protected $taskStateService;

    public function __construct(
        MPFMCalculationService $calculationService,
        MPFMParsingService $parsingService,
        MPFMChartService $chartService,
        MPFMPreviewService $previewService,
        TaskStateService $taskStateService
    ) {
        $this->calculationService = $calculationService;
        $this->parsingService = $parsingService;
        $this->chartService = $chartService;
        $this->previewService = $previewService;
        $this->taskStateService = $taskStateService;
    }
    public function showUploadForm(Request $request)
    {
        $taskId = $request->get('task_id');
        $task = null;
        $newTask = null;
        $autoFillData = [];

        if ($taskId) {
            // Try new Task model first, then fall back to PlanTask
            $newTask = NewTask::with(['tower', 'oilString', 'operators', 'monthlyPlan'])->find($taskId);

            if ($newTask) {
                // New-style task: check access
                $isAssigned = $newTask->assigned_to_user_id == auth()->id()
                           || $newTask->operators()->where('user_id', auth()->id())->exists();

                if (!$isAssigned && !auth()->user()->hasAnyRole(['admin', 'surveillance_supervisor', 'department_supervisor'])) {
                    abort(403, 'Unauthorized access to task');
                }

                // Transition to IN_PROGRESS if still ASSIGNED_TO_DEPARTMENT
                if ($newTask->state->value === 'assigned_to_department') {
                    $sm = app(\App\Domains\Task\Services\TaskStateMachine::class);
                    try {
                        $sm->transition($newTask, \App\Enums\TaskState::IN_PROGRESS, auth()->user(), 'Started via upload form');
                    } catch (\Exception $e) {
                        // Continue even if transition fails
                    }
                }

                $autoFillData = [
                    'client'             => 'ADNOC',
                    'field'              => $newTask->tower?->oilField?->name ?? 'N/A',
                    'well'               => $newTask->title ?? ('Task #' . $newTask->id),
                    'wellhead_tower'     => $newTask->tower?->name ?? 'N/A',
                    'wellhead_string'    => $newTask->oilString?->string_identifier ?? $newTask->oilString?->name ?? 'N/A',
                    'operator'           => auth()->user()->name,
                    'test_date'          => $newTask->expected_completion_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'mpfm_device'        => 'MPFM',
                    'test_type'          => 'flow',
                    'expected_oil_rate'  => null,
                    'expected_gas_rate'  => null,
                    'expected_water_cut' => null,
                    'expected_pressure'  => null,
                    'expected_temperature' => null,
                ];
            } else {
                // Legacy PlanTask
                $task = PlanTask::with([
                    'monthlyPlan',
                    'towerModel',
                    'oilString',
                    'operators'
                ])->findOrFail($taskId);

                if (!$task->isAssignedTo(auth()->id())) {
                    abort(403, 'Unauthorized access to task');
                }

                if ($task->completion_status === 'pending') {
                    $task->update(['completion_status' => 'in_progress']);
                }

                $autoFillData = [
                    'client'             => 'ADNOC',
                    'field'              => $task->towerModel?->oilField?->name ?? $task->tower ?? 'N/A',
                    'well'               => $task->well_full_name ?? $task->well_number ?? $task->towerModel?->well_name ?? 'N/A',
                    'wellhead_tower'     => $task->towerModel?->name ?? $task->tower ?? 'N/A',
                    'wellhead_string'    => $task->oilString?->string_identifier ?? $task->string_number ?? 'N/A',
                    'operator'           => auth()->user()->name,
                    'test_date'          => $task->scheduled_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'mpfm_device'        => $task->mpfm_type ?? $task->category ?? 'MPFM',
                    'test_type'          => $task->category ?? 'flow',
                    'expected_oil_rate'  => $task->expected_oil_rate ?? $task->production_rate ?? null,
                    'expected_gas_rate'  => $task->expected_gas_rate ?? null,
                    'expected_water_cut' => $task->expected_water_cut ?? null,
                    'expected_pressure'  => $task->required_whfp ?? null,
                    'expected_temperature' => $task->expected_temperature ?? null,
                ];
            }
        }

        return view('logs.upload', compact('task', 'newTask', 'autoFillData'));
    }

    public function processLogFile(Request $request)
    {
        $request->validate([
            'logfile' => 'required|file|max:10240',
            'client' => 'required|string',
            'field' => 'required|string',
            'well' => 'required|string',
            'operator' => 'required|string',
            'test_date' => 'required|date',
            'test_type' => 'nullable|string',
            'wellhead_tower' => 'nullable|string',
            'wellhead_string' => 'nullable|string',
            'choke_size' => 'nullable|string',
            'upstream_choke' => 'nullable|string',
            'downstream_choke' => 'nullable|string',
            'task_id' => 'nullable|exists:plan_tasks,id',
            'new_task_id' => 'nullable|exists:tasks,id',
        ]);

        // Validate file extension
        $file = $request->file('logfile');
        $allowedExtensions = ['txt', 'vxlog', 'log', 'csv'];
        $fileExtension = strtolower($file->getClientOriginalExtension());
        if (!in_array($fileExtension, $allowedExtensions)) {
            return redirect()->back()->withErrors(['logfile' => 'Invalid file type. Allowed: ' . implode(', ', $allowedExtensions)]);
        }

        try {
            if (!$file->isValid()) {
                return redirect()->back()->withErrors(['logfile' => 'Invalid file upload']);
            }

            $fileName = time() . '_' . $file->getClientOriginalName();
            $tempFilePath = $file->storeAs('temp', $fileName, 'public');

            $content = file_get_contents($file->getRealPath());
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
            if ($encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }

            $lines = explode("\n", $content);
            $parsedData = $this->parsingService->parseDynamicLog($lines);

            if (empty($parsedData['test_data']['rows'])) {
                return redirect()->back()->with('error', 'The log file could not be parsed or contains no test data.');
            }

            $displayData = $this->prepareDataForDisplay($parsedData, $request);

            $displayData['raw_log_content'] = $content;
            $displayData['temp_file_path'] = $tempFilePath;
            $displayData['filename'] = $file->getClientOriginalName();
            $displayData['task_id'] = $request->input('task_id');
            $displayData['new_task_id'] = $request->input('new_task_id');

            // Transition legacy PlanTask state when file is uploaded
            if ($request->input('task_id')) {
                $task = PlanTask::find($request->input('task_id'));
                if ($task && $task->isAssignedTo(auth()->id())) {
                    try {
                        $this->taskStateService->transition(
                            $task,
                            TaskState::MPFM_REVIEWED,
                            auth()->user(),
                            'Test log file uploaded: ' . $file->getClientOriginalName()
                        );
                    } catch (\Exception $e) {
                        \Log::warning('Failed to transition task state: ' . $e->getMessage());
                    }
                }
            }

            // Transition new-style Task to DEPARTMENT_REVIEWED when file is uploaded
            if ($request->input('new_task_id')) {
                $newTask = NewTask::find($request->input('new_task_id'));
                if ($newTask) {
                    $sm = app(\App\Domains\Task\Services\TaskStateMachine::class);
                    try {
                        if ($newTask->state->value === 'in_progress') {
                            $sm->transition($newTask, \App\Enums\TaskState::DEPARTMENT_REVIEWED, auth()->user(),
                                'Test log uploaded: ' . $file->getClientOriginalName());
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to transition new task state: ' . $e->getMessage());
                    }
                }
            }

            return view('logs.results', $displayData);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    private function prepareDataForDisplay($parsedData, $request)
    {
        $rows = $parsedData['test_data']['rows'];
        if (empty($rows)) {
            return [];
        }

        $columns = array_keys($rows[0]);
        $timeColumn = 'Date & Time';

        $averages = $this->calculationService->calculateStandardizedMetrics($rows);
        $ratesChartData = $this->chartService->prepareRatesChartData($columns, $rows, $timeColumn);
        $gorChartData = $this->chartService->prepareGorChartData($columns, $rows, $timeColumn);
        $fullAverageRow = $this->chartService->prepareFullAverageRow($parsedData);

        $startTime = $this->parseDateTimeSafe($rows[0][$timeColumn] ?? null, Carbon::today());
        $endTime = $this->parseDateTimeSafe(end($rows)[$timeColumn] ?? null, Carbon::today());

        return [
           'summary' => [
            'client' => $request->input('client', 'N/A'),
            'field' => $request->input('field', 'N/A'),
            'well' => $request->input('well', 'N/A'),
            'operator' => $request->input('operator', 'N/A'), // تمت إضافته
            'test_date' => $request->input('test_date'), // تمت إضافته
            'test_type' => $request->input('test_type', 'N/A'), // تمت إضافته
            'wellhead_tower' => $request->input('wellhead_tower', ''),
            'wellhead_string' => $request->input('wellhead_string', ''),
            'choke_size' => $request->input('choke_size', 'N/A'),
            'upstream_choke' => $request->input('upstream_choke', 'N/A'),
            'downstream_choke' => $request->input('downstream_choke', 'N/A'),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ],
            'averages' => $averages,
            'rates_chart_data' => $ratesChartData,
            'gor_chart_data' => $gorChartData,
            'parsed_data' => $parsedData,
            'full_average_row' => $fullAverageRow,
        ];
    }



































  public function storeTest(Request $request)
{
    $request->validate([
        'summary' => 'required|string',
        'averages' => 'required|string',
        'parsed_data' => 'required|string',
        'temp_file_path' => 'required|string',
        'filename' => 'required|string',
        'task_id' => 'nullable|exists:plan_tasks,id',
        'new_task_id' => 'nullable|exists:tasks,id',
    ]);

    $data = $request->all();
    $tempFilePath = $data['temp_file_path'];

    $permanentPath = null;
    if (Storage::disk('public')->exists($tempFilePath)) {
        $newFileName = basename($tempFilePath);
        $permanentPath = 'logs/' . $newFileName;
        Storage::disk('public')->move($tempFilePath, $permanentPath);
    } else {
        return redirect()->back()->with('error', 'Temporary file not found. Could not save test.');
    }

    try {
        $summary = json_decode($data['summary'], true);
        $averages = json_decode($data['averages'], true);
        $parsedData = json_decode($data['parsed_data'], true);
        $fullAverageRow = isset($data['full_average_row']) ? json_decode($data['full_average_row'], true) : [];

        // Recalculate full metrics including fluid properties
        if (isset($parsedData['test_data']['rows'])) {
            $rows = $parsedData['test_data']['rows'];
            $fullMetrics = $this->calculationService->calculateStandardizedMetrics($rows);

            // Map calculated metrics to standard field names
            $fullMetrics['water_cut'] = $fullMetrics['bsw'] ?? null; // BSW = Bottom Side Water = Water Cut
            $fullMetrics['pressure'] = $fullMetrics['avg_pressure'] ?? null;
            $fullMetrics['temperature'] = $fullMetrics['avg_temperature'] ?? null;

            $averages = $fullMetrics;
        }

        // Create WellTest record
        $wellTest = WellTest::create([
            'client' => $summary['client'],
            'field' => $summary['field'],
            'well' => $summary['well'],
            'operator' => $summary['operator'],
            'test_date' => $summary['test_date'],
            'test_type' => $summary['test_type'],
            'wellhead_tower' => $summary['wellhead_tower'],
            'wellhead_string' => $summary['wellhead_string'],
            'choke_size' => $summary['choke_size'],
            'upstream_choke' => $summary['upstream_choke'],
            'downstream_choke' => $summary['downstream_choke'],
            'start_time' => $summary['start_time'],
            'end_time' => $summary['end_time'],
            'metadata' => $parsedData['metadata'] ?? [],
            'test_data' => $parsedData['test_data'] ?? [],
            'calculated_metrics' => $averages,
            'filename' => $data['filename'],
            'log_file_path' => $permanentPath
        ]);

        // Handle task completion if task_id is present
        if (isset($data['task_id']) && $data['task_id']) {
            $task = PlanTask::findOrFail($data['task_id']);

            // Get oil field ID from the tower
            $tower = $task->towerModel;
            $oilFieldId = $tower ? $tower->oil_field_id : null;

            // Extract detailed metrics from parsed data
            $detailedMetricsExtractor = app(MPFMDetailedMetricsExtractorService::class);
            $detailedMetrics = $detailedMetricsExtractor->extractDetailedMetrics($parsedData, '');

            // Extract timing information
            $testStartedAt = $detailedMetrics['timing']['test_started_at'] ?? $summary['start_time'];
            $testCompletedAt = $detailedMetrics['timing']['test_completed_at'] ?? $summary['end_time'];
            $testDurationHours = $detailedMetrics['timing']['test_duration_hours'] ?? null;
            $testStableDurationHours = $detailedMetrics['timing']['test_stable_duration_hours'] ?? null;

            // Create MPFMTest linked to task
            $mpfmTestData = [
                'user_id' => auth()->id(),
                'oil_field_id' => $oilFieldId,
                'tower_id' => $task->tower_id,
                'oil_string_id' => $task->oil_string_id,
                'plan_task_id' => $task->id,
                'well_test_id' => $wellTest->id,
                'test_date' => $summary['test_date'] ?? now()->format('Y-m-d'),
                'oil_rate' => $averages['oil_rate'] ?? null,
                'gas_rate' => $averages['gas_rate'] ?? null,
                'water_cut' => $averages['water_cut'] ?? null,
                'pressure' => $averages['pressure'] ?? null,
                'temperature' => $averages['temperature'] ?? null,
                'gor' => $averages['gor'] ?? null,
                'calculated_kpis' => $averages,
                'log_file_path' => $permanentPath,
                'raw_log_data' => $parsedData,
                'test_duration_minutes' => $this->calculateTestDuration($summary['start_time'], $summary['end_time']),
                'test_started_at' => $testStartedAt,
                'test_completed_at' => $testCompletedAt,
                'test_duration_hours' => $testDurationHours,
                'test_stable_duration_hours' => $testStableDurationHours,
                'detailed_metrics' => $detailedMetrics,
                // Extract individual metrics from detailed metrics
                'qo_sc' => $detailedMetrics['flow_rates']['standard_conditions']['qo_sc']['value'] ?? null,
                'qw_sc' => $detailedMetrics['flow_rates']['standard_conditions']['qw_sc']['value'] ?? null,
                'qg_sc' => $detailedMetrics['flow_rates']['standard_conditions']['qg_sc']['value'] ?? null,
                'qo_lc' => $detailedMetrics['flow_rates']['line_conditions']['qo_lc']['value'] ?? null,
                'qw_lc' => $detailedMetrics['flow_rates']['line_conditions']['qw_lc']['value'] ?? null,
                'qg_lc' => $detailedMetrics['flow_rates']['line_conditions']['qg_lc']['value'] ?? null,
                'bsw' => $detailedMetrics['water_cut_metrics']['bsw']['value'] ?? null,
                'wlr' => $detailedMetrics['water_cut_metrics']['wlr']['value'] ?? null,
                'gvf' => $detailedMetrics['gas_oil_relations']['gvf']['value'] ?? null,
                'pressure_avg' => $averages['avg_pressure'] ?? null,
                'temperature_avg' => $averages['avg_temperature'] ?? null,
                'test_total_data_points' => $detailedMetrics['timing']['test_total_data_points'] ?? count($parsedData['test_data']['rows'] ?? []),
            ];

            $mpfmTest = MPFMTest::create($mpfmTestData);

            // Mark task as completed
            $task->update([
                'mpfm_test_id' => $mpfmTest->id,
                'completion_status' => 'completed',
                'completed_at' => now(),
                'state' => TaskState::COMPLETED,
            ]);

            // Notify stakeholders about test upload
            $operator = auth()->user();

            // Get department supervisor
            if ($task->department) {
                $departmentManager = User::where('department_id', $task->department->id)
                    ->whereHas('roles', function($q) {
                        $q->where('name', 'department_manager');
                    })
                    ->first();

                if ($departmentManager) {
                    $departmentManager->notify(new TestUploadedNotification($task, $operator));
                }
            }

            // Notify surveillance supervisor
            if ($task->surveillance_supervisor_id) {
                $surveillanceSupervisor = User::find($task->surveillance_supervisor_id);
                if ($surveillanceSupervisor) {
                    $surveillanceSupervisor->notify(new TestUploadedNotification($task, $operator));
                }
            }

            // Notify all admins
            $admins = User::whereHas('roles', function($q) {
                $q->where('name', 'admin');
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new TestUploadedNotification($task, $operator));
            }

            // Also notify supervisor if daily plan exists (existing logic)
            if ($task->monthlyPlan && $task->monthlyPlan->createdBy) {
                $task->monthlyPlan->createdBy->notify(new TaskCompletedNotification($task, $mpfmTest));
            }

            return redirect()->route('operator.tasks.show', $task)
                ->with('success', 'Test uploaded and task completed successfully!');
        }

        // Handle new-style Task completion if new_task_id is present
        if (isset($data['new_task_id']) && $data['new_task_id']) {
            $newTask = NewTask::find($data['new_task_id']);
            if ($newTask) {
                // Create MPFMTest linked to new task
                $mpfmTest = MPFMTest::create([
                    'user_id'          => auth()->id(),
                    'tower_id'         => $newTask->tower_id,
                    'oil_string_id'    => $newTask->oil_string_id,
                    'task_id'          => $newTask->id,
                    'well_test_id'     => $wellTest->id,
                    'test_date'        => $summary['test_date'] ?? now()->format('Y-m-d'),
                    'oil_rate'         => $averages['oil_rate'] ?? null,
                    'gas_rate'         => $averages['gas_rate'] ?? null,
                    'water_cut'        => $averages['water_cut'] ?? null,
                    'pressure'         => $averages['pressure'] ?? null,
                    'temperature'      => $averages['temperature'] ?? null,
                    'gor'              => $averages['gor'] ?? null,
                    'calculated_kpis'  => $averages,
                    'log_file_path'    => $permanentPath,
                    'raw_log_data'     => $parsedData,
                    'test_duration_minutes' => $this->calculateTestDuration($summary['start_time'], $summary['end_time']),
                ]);

                // Transition task state to DEPARTMENT_REVIEWED
                $sm = app(\App\Domains\Task\Services\TaskStateMachine::class);
                try {
                    if (in_array($newTask->state->value, ['in_progress', 'assigned_to_department'])) {
                        $sm->transition($newTask, \App\Enums\TaskState::DEPARTMENT_REVIEWED, auth()->user(),
                            'MPFM test uploaded: ' . $data['filename']);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Could not transition new task after upload: ' . $e->getMessage());
                }

                return redirect()->route('tasks.show', $newTask)
                    ->with('success', 'Test uploaded and task submitted for review!');
            }
        }

    } catch (\Exception $e) {
        if ($permanentPath && Storage::disk('public')->exists($permanentPath)) {
            Storage::disk('public')->delete($permanentPath);
        }
        return redirect()->back()->with('error', 'Failed to save test data to the database: ' . $e->getMessage());
    }

    return redirect()->route('well_tests.index')
        ->with('success', 'Well test saved successfully!');
}

/**
 * View test result for supervisor review
 */
public function viewTest($mpfmTestId)
{
    $mpfmTest = MPFMTest::findOrFail($mpfmTestId);
    $task = $mpfmTest->planTask;

    // Check authorization - only supervisors and task creator
    if (!auth()->check() || !auth()->user()->hasPermissionTo('review-test')) {
        abort(403, 'Unauthorized to view this test');
    }

    $wellTest = $mpfmTest->wellTest;
    $rows = $wellTest->test_data['rows'] ?? [];
    $columns = $wellTest->test_data['columns'] ?? [];
    $timeColumn = 'Date & Time';

    $ratesChartData = $this->chartService->prepareRatesChartData($columns, $rows, $timeColumn);
    $gorChartData = $this->chartService->prepareGorChartData($columns, $rows, $timeColumn);

    return view('mpfm_tests.show', [
        'mpfmTest' => $mpfmTest,
        'task' => $task,
        'wellTest' => $wellTest,
        'rates_chart_data' => $ratesChartData,
        'gor_chart_data' => $gorChartData,
    ]);
}

/**
 * Approve or reject test result
 */
public function approveTest(Request $request, $mpfmTestId)
{
    $mpfmTest = MPFMTest::findOrFail($mpfmTestId);
    $task = $mpfmTest->planTask;

    // Check authorization
    if (!auth()->check() || !auth()->user()->hasPermissionTo('review-test')) {
        abort(403, 'Unauthorized to review this test');
    }

    $request->validate([
        'action' => 'required|in:approve,reject',
        'remarks' => 'required_if:action,reject|nullable|string|max:1000',
    ]);

    $action = $request->input('action');
    $remarks = $request->input('remarks');

    if ($action === 'approve') {
        // Mark test as approved
        $mpfmTest->update([
            'status' => 'approved',
        ]);

        // Update task state to SURVEILLANCE_REVIEWED (next state in workflow)
        if ($task) {
            $task->update([
                'state' => TaskState::SURVEILLANCE_REVIEWED,
            ]);
        }

        // Notify operator about approval
        if ($task && $task->operator) {
            $task->operator->notify(new \App\Notifications\TestApprovedNotification($task, $mpfmTest));
        }

        return redirect()->back()->with('success', 'Test approved successfully! Operator has been notified.');
    } else {
        // Mark test as rejected and reset to draft
        $mpfmTest->update([
            'status' => 'rejected',
        ]);

        // Update task to allow re-upload
        if ($task) {
            $task->update([
                'state' => TaskState::IN_PROGRESS,
                'completion_status' => 'in_progress',
            ]);
        }

        // Notify operator to re-upload test
        if ($task && $task->operator) {
            $task->operator->notify(new \App\Notifications\TestRejectedNotification($task, $mpfmTest, $remarks));
        }

        return redirect()->back()->with('success', 'Test rejected. Operator has been notified to re-upload.');
    }
}



    public function index()
    {
        $wellTests = WellTest::orderBy('created_at', 'desc')->paginate(10);
        return view('logs.index', compact('wellTests'));
    }

    public function show($id)
    {
        $wellTest = WellTest::findOrFail($id);

        $rows = $wellTest->test_data['rows'] ?? [];
        $columns = $rows ? array_keys($rows[0]) : [];
        $timeColumn = 'Date & Time';

        $ratesChartData = $this->chartService->prepareRatesChartData($columns, $rows, $timeColumn);
        $gorChartData = $this->chartService->prepareGorChartData($columns, $rows, $timeColumn);
        $fullAverageRow = $this->chartService->prepareFullAverageRow(['test_data' => ['columns' => $columns, 'rows' => $rows]]);

        return view('logs.saved_results', [
            'wellTest' => $wellTest,
            'rates_chart_data' => $ratesChartData,
            'gor_chart_data' => $gorChartData,
            'full_average_row' => $fullAverageRow,
        ]);
    }

    // === Helper Functions ===

    /**
     * Helper function to parse datetime safely
     */
    private function parseDateTimeSafe($dateTimeStr, $defaultDate)
    {
        if (!$dateTimeStr || trim($dateTimeStr) === '') {
            return $defaultDate;
        }

        try {
            // Try different datetime formats
            $formats = [
                'Y-m-d H:i:s',
                'd-M-Y H:i:s',
                'd/m/Y H:i:s',
                'm/d/Y H:i:s',
                'Y-m-d H:i',
                'd/m/Y H:i',
                'm/d/Y H:i',
            ];

            foreach ($formats as $format) {
                $date = DateTime::createFromFormat($format, trim($dateTimeStr));
                if ($date) {
                    return Carbon::instance($date);
                }
            }

            // Fallback to default parser
            return Carbon::parse(trim($dateTimeStr));
        } catch (\Exception $e) {
            return $defaultDate;
        }
    }

    /**
     * Calculate test duration in minutes
     */
    private function calculateTestDuration($startTime, $endTime): ?int
    {
        try {
            $start = $startTime instanceof Carbon ? $startTime : Carbon::parse($startTime);
            $end = $endTime instanceof Carbon ? $endTime : Carbon::parse($endTime);
            return $start->diffInMinutes($end);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Show test approval page with validation results
     */
    public function showTestApproval($mpfmTestId)
    {
        $mpfmTest = MPFMTest::with([
            'planTask.oilString.tower',
            'latestValidation',
            'latestComparison'
        ])->findOrFail($mpfmTestId);

        // Run validation if not done yet
        if (!$mpfmTest->latestValidation || $mpfmTest->needsRevalidation()) {
            $validationService = app(MPFMValidationService::class);
            $validationService->validateTest($mpfmTest, true);
            $mpfmTest->load('latestValidation');
        }

        return view('supervisor.test_approval', compact('mpfmTest'));
    }









































}
