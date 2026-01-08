<?php

namespace App\Http\Controllers;

use App\Models\MonthlyPlan;
use App\Models\PlanTask;
use App\Models\User;
use App\Models\Tower;
use App\Models\OilString;
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

        // Get oil strings with tower relationship for cascading dropdowns
        $oilStrings = OilString::with('tower')->get();

        return view('monthly_plans.create_task', compact('monthlyPlan', 'towers', 'operators', 'oilStrings'));
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
     * Store new task in plan
     */
    public function storeTask(Request $request, MonthlyPlan $monthlyPlan)
    {
        if (!$monthlyPlan->isEditable()) {
            return back()->with('error', 'Cannot add tasks to this plan in its current status.');
        }

        try {
            $request->validate([
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
     * Submit plan for review (Job Officer submits to MPFM Supervisor)
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
            $monthlyPlan->update(['status' => 'under_review']);

            // TODO: Send notification to MPFM Supervisors

            return redirect()
                ->route('monthly-plans.show', $monthlyPlan)
                ->with('success', 'Plan submitted for review successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit plan: ' . $e->getMessage());
        }
    }
}
