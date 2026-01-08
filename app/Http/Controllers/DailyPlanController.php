<?php

namespace App\Http\Controllers;

use App\Models\DailyPlan;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\User;
use App\Models\OilString;
use App\Models\PlanTask;
use App\Services\DailyPlanService;
use App\Services\PlanTxtParserService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DailyPlanController extends Controller
{
    protected $dailyPlanService;
    protected $planTxtParserService;

    public function __construct(
        DailyPlanService $dailyPlanService,
        PlanTxtParserService $planTxtParserService
    ) {
        $this->dailyPlanService = $dailyPlanService;
        $this->planTxtParserService = $planTxtParserService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of daily plans
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DailyPlan::with(['creator', 'oilField', 'tower'])
                ->createdBy(auth()->id());

            if ($request->filled('date_from')) {
                $query->whereDate('plan_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('plan_date', '<=', $request->date_to);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('oil_field_id')) {
                $query->where('oil_field_id', $request->oil_field_id);
            }

            return datatables()
                ->eloquent($query)
                ->addColumn('action', function ($plan) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<a href="' . route('daily-plans.show', $plan) . '" class="btn btn-sm btn-info" title="View"><i class="fe fe-eye"></i></a>';

                    if ($plan->status === 'draft') {
                        $actions .= '<a href="' . route('daily-plans.edit', $plan) . '" class="btn btn-sm btn-primary" title="Edit"><i class="fe fe-edit"></i></a>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->addColumn('status_badge', function ($plan) {
                    return '<span class="badge ' . $plan->status_badge_class . '">' . ucfirst($plan->status) . '</span>';
                })
                ->addColumn('progress_bar', function ($plan) {
                    $percentage = $plan->completion_percentage;
                    $class = $percentage == 100 ? 'success' : ($percentage > 0 ? 'info' : 'secondary');
                    return '<div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-' . $class . '" style="width: ' . $percentage . '%">' . $percentage . '%</div>
                            </div>';
                })
                ->rawColumns(['action', 'status_badge', 'progress_bar'])
                ->make(true);
        }

        $oilFields = OilField::all();
        return view('daily_plans.index', compact('oilFields'));
    }

    /**
     * Show the form for creating a new plan
     */
    public function create()
    {
        $oilFields = OilField::with('towers')->get();
        $operators = User::role('operator')->get();

        return view('daily_plans.create', compact('oilFields', 'operators'));
    }

    /**
     * Store a newly created plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_date' => 'required|date',
            'oil_field_id' => 'required|exists:oil_fields,id',
            'tower_id' => 'nullable|exists:towers,id',
            'plan_notes' => 'nullable|string',
        ]);

        $plan = $this->dailyPlanService->createPlan(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        return redirect()
            ->route('daily-plans.show', $plan)
            ->with('success', 'Daily plan created successfully. Now add tasks to the plan.');
    }

    /**
     * Display the specified plan
     */
    public function show(DailyPlan $dailyPlan)
    {
        $dailyPlan->load(['tasks.operator', 'tasks.oilString.tower', 'oilField', 'tower']);
        $stats = $this->dailyPlanService->getPlanStatistics($dailyPlan);

        return view('daily_plans.show', compact('dailyPlan', 'stats'));
    }

    /**
     * Show the form for editing the plan
     */
    public function edit(DailyPlan $dailyPlan)
    {
        if ($dailyPlan->status !== 'draft') {
            return redirect()
                ->route('daily-plans.show', $dailyPlan)
                ->with('error', 'Only draft plans can be edited.');
        }

        $oilFields = OilField::with('towers')->get();

        return view('daily_plans.edit', compact('dailyPlan', 'oilFields'));
    }

    /**
     * Update the specified plan
     */
    public function update(Request $request, DailyPlan $dailyPlan)
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_date' => 'required|date',
            'oil_field_id' => 'required|exists:oil_fields,id',
            'tower_id' => 'nullable|exists:towers,id',
            'plan_notes' => 'nullable|string',
        ]);

        $this->dailyPlanService->updatePlan($dailyPlan, $validated);

        return redirect()
            ->route('daily-plans.show', $dailyPlan)
            ->with('success', 'Plan updated successfully.');
    }

    /**
     * Remove the specified plan
     */
    public function destroy(DailyPlan $dailyPlan)
    {
        if (!$dailyPlan->canBeDeleted()) {
            return back()->with('error', 'Cannot delete plan with tasks in progress or completed.');
        }

        $dailyPlan->delete();

        return redirect()
            ->route('daily-plans.index')
            ->with('success', 'Plan deleted successfully.');
    }

    /**
     * Activate a plan
     */
    public function activate(DailyPlan $dailyPlan)
    {
        try {
            $this->dailyPlanService->activatePlan($dailyPlan);
            return back()->with('success', 'Plan activated successfully. Operators can now see their tasks.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel a plan
     */
    public function cancel(Request $request, DailyPlan $dailyPlan)
    {
        $reason = $request->input('reason');
        $this->dailyPlanService->cancelPlan($dailyPlan, $reason);

        return back()->with('success', 'Plan cancelled successfully.');
    }

    /**
     * Clone a plan to a new date
     */
    public function clone(Request $request, DailyPlan $dailyPlan)
    {
        $validated = $request->validate([
            'new_date' => 'required|date|after:yesterday',
            'new_name' => 'nullable|string|max:255',
        ]);

        $newPlan = $this->dailyPlanService->clonePlan(
            $dailyPlan,
            Carbon::parse($validated['new_date']),
            $validated['new_name'] ?? null
        );

        return redirect()
            ->route('daily-plans.show', $newPlan)
            ->with('success', 'Plan cloned successfully.');
    }

    /**
     * Show import from plan.txt form
     */
    public function showImportForm()
    {
        $operators = User::role('operator')->get();
        return view('daily_plans.import', compact('operators'));
    }

    /**
     * Import plan from plan.txt file
     */
    public function importFromPlanTxt(Request $request)
    {
        $request->validate([
            'plan_file' => 'required|file|mimes:txt|max:2048',
            'plan_name' => 'required|string|max:255',
            'plan_date' => 'required|date',
        ]);

        try {
            $file = $request->file('plan_file');
            $filePath = $file->getRealPath();

            // Parse the plan.txt file
            $parsedData = $this->planTxtParserService->parsePlanFile($filePath);

            if (empty($parsedData['tasks'])) {
                return back()->with('error', 'No tasks found in the plan file.');
            }

            // Create the daily plan
            $plan = DailyPlan::create([
                'plan_name' => $request->plan_name,
                'plan_date' => $request->plan_date,
                'status' => 'draft',
                'created_by' => auth()->id(),
                'monthly_target_kpi' => $parsedData['header']['monthly_target_kpi'] ?? null,
                'total_tests' => $parsedData['header']['total_tests'] ?? count($parsedData['tasks']),
                'plan_notes' => 'Imported from plan.txt on ' . now()->format('Y-m-d H:i:s'),
            ]);

            // Import tasks
            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($parsedData['tasks'] as $taskData) {
                try {
                    // Find or create tower
                    $tower = Tower::firstOrCreate(
                        ['name' => $taskData['tower']],
                        ['oil_field_id' => 1] // Default oil field
                    );

                    // Find or create oil string
                    $oilString = OilString::firstOrCreate(
                        [
                            'name' => $taskData['well_name'],
                            'tower_id' => $tower->id
                        ]
                    );

                    // Find operator by name
                    $operator = null;
                    if (!empty($taskData['operator'])) {
                        $operator = User::where('name', 'LIKE', '%' . $taskData['operator'] . '%')
                            ->role('operator')
                            ->first();
                    }

                    // Create plan task
                    PlanTask::create([
                        'daily_plan_id' => $plan->id,
                        'sno' => $taskData['sno'],
                        'well_name' => $taskData['well_name'],
                        'tower_id' => $tower->id,
                        'oil_string_id' => $oilString->id,
                        'flow_route' => $taskData['flow_route'],
                        'reservoir_layer' => $taskData['reservoir_layer'],
                        'test_location' => $taskData['test_location'],
                        'mpfm_type' => $taskData['mpfm_type'],
                        'dhsv' => $taskData['dhsv'],
                        'sub_category' => $taskData['sub_category'],
                        'category' => $taskData['category'],
                        'required_whfp' => $taskData['required_whfp'],
                        'tested_whfp' => $taskData['tested_whfp'],
                        'test_date' => $taskData['test_date'] ?? $request->plan_date,
                        'assigned_to' => $operator?->id,
                        'status' => $taskData['status'],
                        'priority' => $taskData['priority'],
                        'final_status' => $taskData['final_status'],
                        'completion_notes' => $taskData['remarks'],
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Task {$taskData['sno']}: " . $e->getMessage();
                }
            }

            $message = "Plan imported successfully! {$imported} tasks imported";
            if ($skipped > 0) {
                $message .= ", {$skipped} tasks skipped.";
            }

            if (!empty($errors)) {
                session()->flash('import_errors', $errors);
            }

            return redirect()
                ->route('daily-plans.show', $plan)
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to import plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form to approve a plan (MPFM Supervisor)
     */
    public function showApprovalForm(DailyPlan $dailyPlan)
    {
        if ($dailyPlan->status !== 'draft') {
            return back()->with('error', 'Only draft plans can be approved.');
        }

        $dailyPlan->load(['tasks.operator', 'tasks.oilString.tower']);
        $operators = User::role('operator')->get();

        return view('daily_plans.approve', compact('dailyPlan', 'operators'));
    }

    /**
     * Approve a plan (MPFM Supervisor)
     */
    public function approve(DailyPlan $dailyPlan)
    {
        try {
            $dailyPlan->update([
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'status' => 'approved',
            ]);

            // Optionally activate immediately
            $this->dailyPlanService->activatePlan($dailyPlan);

            return redirect()
                ->route('daily-plans.show', $dailyPlan)
                ->with('success', 'Plan approved and activated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject a plan (MPFM Supervisor)
     */
    public function reject(Request $request, DailyPlan $dailyPlan)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $dailyPlan->update([
            'status' => 'rejected',
            'plan_notes' => ($dailyPlan->plan_notes ?? '') . "\n\nRejection Reason: " . $request->rejection_reason,
        ]);

        return redirect()
            ->route('daily-plans.index')
            ->with('success', 'Plan rejected successfully.');
    }
}
