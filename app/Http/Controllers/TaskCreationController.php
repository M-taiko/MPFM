<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\OilString;
use App\Models\Tower;
use App\Domains\MPFM\Models\DepartmentTestType;
use Illuminate\Http\Request;

class TaskCreationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show task creation form
     */
    public function create()
    {
        $towers = Tower::with('oilStrings')->get();
        $departments = Department::active()->get();
        $monthlyPlans = \App\Models\MonthlyPlan::where('status', 'active')
            ->orWhere('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tasks.create-advanced', compact('towers', 'departments', 'monthlyPlans'));
    }

    /**
     * Get oil strings for a specific tower (AJAX)
     */
    public function getOilStrings(Request $request)
    {
        $request->validate([
            'tower_id' => 'required|exists:towers,id',
        ]);

        $oilStrings = OilString::where('tower_id', $request->tower_id)
            ->active()
            ->get(['id', 'name', 'code']);

        return response()->json([
            'success' => true,
            'data' => $oilStrings,
        ]);
    }

    /**
     * Get department requirements/fields (AJAX)
     */
    public function getDepartmentRequirements(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);

        $department = Department::findOrFail($request->department_id);

        // Get test types for the department
        $testTypes = DepartmentTestType::forDepartment($department->id)
            ->active()
            ->get(['id', 'name', 'description', 'required_fields']);

        // Get custom fields from workflow config
        $workflow = $department->workflow;
        $customFields = $workflow ? $workflow->getCustomFields() : [];

        return response()->json([
            'success' => true,
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
                'code' => $department->code,
            ],
            'testTypes' => $testTypes,
            'customFields' => $customFields,
            'requiredFields' => $this->getRequiredFieldsForDepartment($department),
        ]);
    }

    /**
     * Get test type details with field requirements (AJAX)
     */
    public function getTestTypeDetails(Request $request)
    {
        $request->validate([
            'test_type_id' => 'required|exists:department_test_types,id',
        ]);

        $testType = DepartmentTestType::findOrFail($request->test_type_id);

        return response()->json([
            'success' => true,
            'testType' => [
                'id' => $testType->id,
                'name' => $testType->name,
                'description' => $testType->description,
                'requiredFields' => $testType->getRequiredFields(),
                'validationRules' => $testType->getValidationRules(),
                'estimatedDuration' => $testType->getEstimatedDuration(),
            ],
        ]);
    }

    /**
     * Store new task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tower_id' => 'required|exists:towers,id',
            'oil_string_id' => 'required|exists:oil_strings,id',
            'department_id' => 'required|exists:departments,id',
            'monthly_plan_id' => 'required|exists:monthly_plans,id',
            'test_type_id' => 'nullable|exists:department_test_types,id',
            'priority' => 'required|in:low,medium,high,critical',
            'expected_completion_at' => 'nullable|date_format:Y-m-d\TH:i',
            'department_requirements' => 'nullable|array',
        ]);

        // Verify oil string belongs to tower
        $oilString = OilString::findOrFail($validated['oil_string_id']);
        if ($oilString->tower_id != $validated['tower_id']) {
            return back()->with('error', 'Oil string does not belong to selected tower');
        }

        // Verify test type belongs to department
        if ($validated['test_type_id']) {
            $testType = DepartmentTestType::findOrFail($validated['test_type_id']);
            if ($testType->department_id != $validated['department_id']) {
                return back()->with('error', 'Test type does not belong to selected department');
            }
        }

        // Get department for task type
        $department = Department::findOrFail($validated['department_id']);
        $taskType = strtolower(str_replace(' ', '_', $department->code ?? 'general'));

        // Parse datetime-local format (Y-m-d\TH:i)
        $expectedCompletion = null;
        if ($validated['expected_completion_at']) {
            // datetime-local format is: 2026-01-28T14:30
            $expectedCompletion = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['expected_completion_at']);
        }

        // Create task
        $task = \App\Domains\Task\Models\Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'monthly_plan_id' => $validated['monthly_plan_id'],
            'department_id' => $validated['department_id'],
            'state' => \App\Enums\TaskState::DRAFT->value,
            'priority' => $validated['priority'],
            'oil_string_id' => $validated['oil_string_id'],
            'tower_id' => $validated['tower_id'],
            'expected_completion_at' => $expectedCompletion,
            'created_by_user_id' => auth()->id(),
            'task_type' => $taskType,
            'type_specific_data' => $validated['department_requirements'] ?? [],
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully. Assign it to an operator now.');
    }

    /**
     * Get required fields for a department
     */
    private function getRequiredFieldsForDepartment(Department $department): array
    {
        $fields = [];

        $testTypes = DepartmentTestType::forDepartment($department->id)
            ->active()
            ->get();

        foreach ($testTypes as $testType) {
            $requiredFields = $testType->getRequiredFields();
            foreach ($requiredFields as $field) {
                if (!in_array($field, $fields)) {
                    $fields[] = $field;
                }
            }
        }

        return $fields;
    }
}
