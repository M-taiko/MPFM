<?php

namespace App\Http\Controllers;

use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use App\Models\User;
use App\Models\Tower;
use App\Models\OilString;
use App\Models\Department;
use App\Enums\TaskState;
use App\Services\TaskStateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display list of all monthly plans (Job Officer view)
     */
    public function index()
    {
        $plans = MonthlyPlan::with('creator', 'reviewer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('monthly_plans.index', compact('plans'));
    }

    /**
     * Show form to create new monthly plan (Job Officer)
     */
    public function create()
    {
        return view('monthly_plans.create');
    }

    /**
     * Store new monthly plan (Job Officer)
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_month' => 'required|string|max:255',
            'monthly_target_kpi' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $plan = MonthlyPlan::create([
                'plan_name' => $request->plan_name,
                'plan_month' => $request->plan_month,
                'monthly_target_kpi' => $request->monthly_target_kpi,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'status' => 'draft',
            ]);

            return redirect()
                ->route('monthly-plans.show', $plan)
                ->with('success', 'Monthly plan created successfully. Now add tasks to the plan.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to create plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show monthly plan details
     */
    public function show(MonthlyPlan $monthlyPlan)
    {
        $monthlyPlan->load([
            'creator',
            'reviewer',
            'tasks.operator',
            'tasks.towerModel',
            'tasks.oilString'
        ]);

        // Get statistics
        $stats = [
            'total' => $monthlyPlan->tasks()->count(),
            'pending' => $monthlyPlan->tasks()->where('completion_status', 'pending')->count(),
            'in_progress' => $monthlyPlan->tasks()->where('completion_status', 'in_progress')->count(),
            'completed' => $monthlyPlan->tasks()->where('completion_status', 'completed')->count(),
            'failed' => $monthlyPlan->tasks()->where('completion_status', 'failed')->count(),
            'unassigned' => $monthlyPlan->tasks()->whereNull('assigned_operator_id')->count(),
        ];

        // Get operators list for assignment
        $operators = User::role('operator')->get();

        // Get towers list
        $towers = Tower::all();

        return view('monthly_plans.show', compact('monthlyPlan', 'stats', 'operators', 'towers'));
    }

    /**
     * Show form to edit plan
     */
    public function edit(MonthlyPlan $monthlyPlan)
    {
        // Only allow editing if draft or under_review
        if (!$monthlyPlan->isEditable()) {
            return back()->with('error', 'This plan cannot be edited in its current status.');
        }

        return view('monthly_plans.edit', compact('monthlyPlan'));
    }

    /**
     * Update monthly plan
     */
    public function update(Request $request, MonthlyPlan $monthlyPlan)
    {
        if (!$monthlyPlan->isEditable()) {
            return back()->with('error', 'This plan cannot be edited in its current status.');
        }

        $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_month' => 'required|string|max:255',
            'monthly_target_kpi' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $monthlyPlan->update($request->only([
                'plan_name',
                'plan_month',
                'monthly_target_kpi',
                'notes',
            ]));

            return redirect()
                ->route('monthly-plans.show', $monthlyPlan)
                ->with('success', 'Monthly plan updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete monthly plan
     */
    public function destroy(MonthlyPlan $monthlyPlan)
    {
        // Only allow deletion if draft
        if ($monthlyPlan->status !== 'draft') {
            return back()->with('error', 'Only draft plans can be deleted.');
        }

        try {
            $monthlyPlan->delete();

            return redirect()
                ->route('monthly-plans.index')
                ->with('success', 'Monthly plan deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete plan: ' . $e->getMessage());
        }
    }

    /**
     * Show form to add task to plan
     */
    public function createTask(MonthlyPlan $monthlyPlan)
    {
        if (!$monthlyPlan->isEditable()) {
            return back()->with('error', 'Cannot add tasks to this plan in its current status.');
        }

        $towers = Tower::all();
        $operators = User::role('operator')->get();
        $departments = Department::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // Get oil strings with tower relationship for cascading dropdowns
        $oilStrings = OilString::with('tower')->get();

        return view('monthly_plans.create_task', compact('monthlyPlan', 'towers', 'operators', 'departments', 'oilStrings'));
    }

    /**
     * Get wells for a specific tower (AJAX endpoint)
     */
    public function getWellsByTower($towerId)
    {
        try {
            $wells = OilString::where('tower_id', $towerId)
                ->select('name')
                ->distinct()
                ->orderBy('name')
                ->get()
                ->map(function($oilString) {
                    // Parse well number from name (format: "WellNumber/StringNumber")
                    $parts = explode('/', $oilString->name);
                    return $parts[0] ?? $oilString->name;
                })
                ->unique()
                ->values();

            return response()->json($wells);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load wells'], 500);
        }
    }

    /**
     * Get strings for a specific well in a tower (AJAX endpoint)
     */
    public function getStringsByWell(Request $request)
    {
        try {
            $towerId = $request->tower_id;
            $wellNumber = $request->well_number;

            if (!$towerId || !$wellNumber) {
                return response()->json(['error' => 'Missing tower or well information'], 400);
            }

            $strings = OilString::where('tower_id', $towerId)
                ->where('name', 'like', $wellNumber . '/%')
                ->get()
                ->map(function($oilString) {
                    // Parse string number from name (format: "WellNumber/StringNumber")
                    $parts = explode('/', $oilString->name);
                    return $parts[1] ?? '';
                })
                ->filter()
                ->unique()
                ->values();

            return response()->json($strings);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load strings'], 500);
        }
    }

    /**
     * Get department-specific task fields configuration (AJAX endpoint)
     */
    public function getDepartmentFields($departmentId)
    {
        try {
            // Handle both direct ID and potential model binding
            $department = $departmentId instanceof Department
                ? $departmentId
                : Department::findOrFail($departmentId);

            // Define department-specific field configurations
            $departmentFields = [
                'MPFM' => [
                    'required_fields' => ['tower', 'test_location', 'mpfm_type', 'electrification_status'],
                    'optional_fields' => ['flow_route', 'reservoir_layer', 'dhsv_type', 'priority', 'required_whfp', 'production_rate', 'scheduled_date'],
                    'hidden_fields' => [],
                    'title' => 'MPFM Task Details',
                    'description' => 'Multi-Phase Flow Meter task configuration',
                    'help_text' => 'Configure the MPFM testing parameters for this task'
                ],
                'WIRELINE' => [
                    'required_fields' => ['tower', 'reservoir_layer', 'priority'],
                    'optional_fields' => ['flow_route', 'test_location', 'scheduled_date'],
                    'hidden_fields' => ['mpfm_type', 'dhsv_type', 'electrification_status', 'required_whfp', 'production_rate'],
                    'title' => 'Wireline Task Details',
                    'description' => 'Wireline operations task configuration',
                    'help_text' => 'Configure the wireline operations parameters'
                ],
                'PRODUCTION' => [
                    'required_fields' => ['production_rate', 'scheduled_date'],
                    'optional_fields' => ['tower', 'test_location', 'priority'],
                    'hidden_fields' => ['mpfm_type', 'dhsv_type', 'electrification_status', 'flow_route', 'reservoir_layer', 'required_whfp'],
                    'title' => 'Production Task Details',
                    'description' => 'Production operations task configuration',
                    'help_text' => 'Configure the production operations parameters'
                ],
                'DRILLING' => [
                    'required_fields' => ['tower', 'priority', 'scheduled_date'],
                    'optional_fields' => ['test_location', 'production_rate'],
                    'hidden_fields' => ['mpfm_type', 'dhsv_type', 'electrification_status', 'flow_route', 'reservoir_layer', 'required_whfp'],
                    'title' => 'Drilling Task Details',
                    'description' => 'Drilling operations task configuration',
                    'help_text' => 'Configure the drilling operations parameters'
                ],
                'COMPLETION' => [
                    'required_fields' => ['tower', 'scheduled_date'],
                    'optional_fields' => ['test_location', 'priority', 'flow_route'],
                    'hidden_fields' => ['mpfm_type', 'dhsv_type', 'electrification_status', 'reservoir_layer', 'required_whfp', 'production_rate'],
                    'title' => 'Completion Task Details',
                    'description' => 'Completion operations task configuration',
                    'help_text' => 'Configure the completion operations parameters'
                ],
                'WORKOVER' => [
                    'required_fields' => ['tower', 'priority', 'scheduled_date'],
                    'optional_fields' => ['test_location', 'flow_route'],
                    'hidden_fields' => ['mpfm_type', 'dhsv_type', 'electrification_status', 'reservoir_layer', 'required_whfp', 'production_rate'],
                    'title' => 'Workover Task Details',
                    'description' => 'Workover operations task configuration',
                    'help_text' => 'Configure the workover operations parameters'
                ],
                'MAINTENANCE' => [
                    'required_fields' => ['tower', 'priority'],
                    'optional_fields' => ['scheduled_date', 'test_location'],
                    'hidden_fields' => ['mpfm_type', 'dhsv_type', 'electrification_status', 'flow_route', 'reservoir_layer', 'required_whfp', 'production_rate'],
                    'title' => 'Maintenance Task Details',
                    'description' => 'Maintenance operations task configuration',
                    'help_text' => 'Configure the maintenance operations parameters'
                ]
            ];

            // Get department-specific configuration
            $deptCode = $department->code;
            $config = $departmentFields[$deptCode] ?? $departmentFields['MPFM'];

            return response()->json([
                'success' => true,
                'department_name' => $department->name,
                'department_code' => $deptCode,
                'config' => $config
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load department fields'], 500);
        }
    }

    /**
     * Store new task in plan
     */
    public function storeTask(Request $request, MonthlyPlan $monthlyPlan)
    {
        if (!$monthlyPlan->isEditable()) {
            return back()->with('error', 'Cannot add tasks to this plan in its current status.');
        }

        try {
            $request->validate([
            'department_id' => 'required|exists:departments,id',
            'sno' => 'required|integer',
            'well_number' => 'required|string',
            'string_number' => 'required|string',
            'tower' => 'nullable|string',
            'flow_route' => 'nullable|string',
            'reservoir_layer' => 'nullable|string',
            'test_location' => 'nullable|string',
            'mpfm_type' => 'nullable|string',
            'dhsv_type' => 'nullable|string',
            'category' => 'nullable|string',
            'required_whfp' => 'nullable|numeric',
            'scheduled_date' => 'nullable|date',
            'priority' => 'nullable|in:normal,high,top',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Find or create tower
            $tower = null;
            if ($request->tower) {
                $tower = Tower::firstOrCreate(
                    ['name' => $request->tower],
                    ['oil_field_id' => 1] // Default oil field
                );
            }

            // Find or create oil string
            $oilString = null;
            $wellFullName = $request->well_number . '/' . $request->string_number;
            if ($tower) {
                $oilString = OilString::firstOrCreate([
                    'name' => $wellFullName,
                    'tower_id' => $tower->id
                ]);
            }

            // Create task
            $task = PlanTask::create([
                'monthly_plan_id' => $monthlyPlan->id,
                'department_id' => $request->department_id,
                'sno' => $request->sno,
                'well_number' => $request->well_number,
                'string_number' => $request->string_number,
                'well_full_name' => $wellFullName,
                'tower' => $request->tower,
                'tower_id' => $tower?->id,
                'oil_string_id' => $oilString?->id,
                'flow_route' => $request->flow_route,
                'reservoir_layer' => $request->reservoir_layer,
                'test_location' => $request->test_location,
                'electrification_status' => $request->electrification_status,
                'mpfm_type' => $request->mpfm_type,
                'dhsv_type' => $request->dhsv_type,
                'sub_category' => $request->sub_category,
                'category' => $request->category,
                'required_whfp' => $request->required_whfp,
                'production_rate' => $request->production_rate,
                'scheduled_date' => $request->scheduled_date,
                'priority' => $request->priority ?? 'normal',
                'supervisor_remarks' => $request->supervisor_remarks,
                'hq_notes' => $request->hq_notes,
            ]);

            // Update plan statistics
            $monthlyPlan->updateStatistics();

            DB::commit();

            return redirect()
                ->route('monthly-plans.show', $monthlyPlan)
                ->with('success', 'Task added to plan successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to add task: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete task from plan
     */
    public function deleteTask(MonthlyPlan $monthlyPlan, PlanTask $task)
    {
        if (!$monthlyPlan->isEditable()) {
            return back()->with('error', 'Cannot delete tasks from this plan in its current status.');
        }

        if ($task->monthly_plan_id !== $monthlyPlan->id) {
            return back()->with('error', 'Task does not belong to this plan.');
        }

        try {
            DB::beginTransaction();

            $task->delete();

            // Update plan statistics
            $monthlyPlan->updateStatistics();

            DB::commit();

            return redirect()
                ->route('monthly-plans.show', $monthlyPlan)
                ->with('success', 'Task deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete task: ' . $e->getMessage());
        }
    }

    /**
     * Submit plan for review (Job Officer submits to Surveillance Supervisor)
     */
    public function submitForReview(MonthlyPlan $monthlyPlan)
    {
        if ($monthlyPlan->status !== 'draft') {
            return back()->with('error', 'Only draft plans can be submitted for review.');
        }

        if ($monthlyPlan->tasks()->count() === 0) {
            return back()->with('error', 'Cannot submit plan with no tasks.');
        }

        try {
            DB::transaction(function () use ($monthlyPlan) {
                // Update plan status
                $monthlyPlan->update(['status' => 'under_review']);

                // Transition all draft tasks to SUBMITTED_TO_SURVEILLANCE
                $stateService = app(TaskStateService::class);
                $draftTasks = $monthlyPlan->tasks()->where('state', TaskState::DRAFT)->get();

                foreach ($draftTasks as $task) {
                    try {
                        $stateService->transition(
                            $task,
                            TaskState::SUBMITTED_TO_SURVEILLANCE,
                            auth()->user(),
                            'Submitted with monthly plan for surveillance review'
                        );
                    } catch (\Exception $e) {
                        // Log but don't fail the whole transaction
                        \Log::warning("Failed to transition task {$task->id}: {$e->getMessage()}");
                    }
                }
            });

            return redirect()
                ->route('monthly-plans.show', $monthlyPlan)
                ->with('success', 'Plan and tasks submitted for surveillance review successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit plan: ' . $e->getMessage());
        }
    }

    /**
     * Get task details as modal content with timeline
     */
    public function getTaskDetails($monthlyPlan, $task)
    {
        try {
            // Handle both model instances and IDs
            if (!$monthlyPlan instanceof MonthlyPlan) {
                $monthlyPlan = MonthlyPlan::findOrFail($monthlyPlan);
            }

            if (!$task instanceof PlanTask) {
                $task = PlanTask::findOrFail($task);
            }

            // Verify task belongs to this plan
            if ($task->monthly_plan_id != $monthlyPlan->id) {
                abort(403, 'Task does not belong to this plan');
            }

            // Load state histories with relationships
            $task->load([
                'stateHistories' => function ($query) {
                    $query->latest('created_at');
                },
                'stateHistories.changedBy',
                'monthlyPlan',
                'operator',
                'mpfmTest'
            ]);

            return view('monthly_plans.task_modal_content', compact('task', 'monthlyPlan'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
