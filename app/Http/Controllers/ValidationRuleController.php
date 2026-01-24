<?php

namespace App\Http\Controllers;

use App\Models\MPFMValidationRule;
use App\Models\OilField;
use App\Models\Tower;
use App\Models\OilString;
use App\Services\MPFMValidationRuleService;
use Illuminate\Http\Request;

class ValidationRuleController extends Controller
{
    protected MPFMValidationRuleService $ruleService;

    public function __construct(MPFMValidationRuleService $ruleService)
    {
        $this->ruleService = $ruleService;
        $this->middleware('auth');
        $this->middleware('permission:manage-validation-rules');
    }

    public function index()
    {
        $rules = MPFMValidationRule::with('oilField', 'tower', 'oilString')
            ->paginate(20);

        return view('admin.validation-rules.index', compact('rules'));
    }

    public function create()
    {
        $oilFields = OilField::orderBy('name')->get();
        $towers = Tower::orderBy('name')->get();
        $strings = OilString::orderBy('name')->get();

        return view('admin.validation-rules.create', compact('oilFields', 'towers', 'strings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|in:range,percentage_change,trend,anomaly',
            'parameter' => 'required|string',
            'oil_field_id' => 'nullable|exists:oil_fields,id',
            'tower_id' => 'nullable|exists:towers,id',
            'oil_string_id' => 'nullable|exists:oil_strings,id',
            'thresholds' => 'required|json',
            'severity' => 'required|in:critical,major,minor',
            'auto_action' => 'required|in:block,warn,info',
            'is_active' => 'boolean',
        ]);

        $validated['thresholds'] = json_decode($request->thresholds, true);
        $validated['created_by'] = auth()->id();

        MPFMValidationRule::create($validated);

        return redirect()->route('validation-rules.index')
            ->with('success', 'Validation rule created successfully');
    }

    public function edit(MPFMValidationRule $validationRule)
    {
        $oilFields = OilField::orderBy('name')->get();
        $towers = Tower::orderBy('name')->get();
        $strings = OilString::orderBy('name')->get();

        return view('admin.validation-rules.edit', compact('validationRule', 'oilFields', 'towers', 'strings'));
    }

    public function update(Request $request, MPFMValidationRule $validationRule)
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|in:range,percentage_change,trend,anomaly',
            'parameter' => 'required|string',
            'oil_field_id' => 'nullable|exists:oil_fields,id',
            'tower_id' => 'nullable|exists:towers,id',
            'oil_string_id' => 'nullable|exists:oil_strings,id',
            'thresholds' => 'required|json',
            'severity' => 'required|in:critical,major,minor',
            'auto_action' => 'required|in:block,warn,info',
            'is_active' => 'boolean',
        ]);

        $validated['thresholds'] = json_decode($request->thresholds, true);

        $validationRule->update($validated);

        return redirect()->route('validation-rules.index')
            ->with('success', 'Validation rule updated successfully');
    }

    public function destroy(MPFMValidationRule $validationRule)
    {
        $validationRule->delete();

        return redirect()->route('validation-rules.index')
            ->with('success', 'Validation rule deleted successfully');
    }

    public function toggleActive(MPFMValidationRule $validationRule)
    {
        $validationRule->update(['is_active' => !$validationRule->is_active]);

        return redirect()->back()
            ->with('success', 'Validation rule updated successfully');
    }
}
