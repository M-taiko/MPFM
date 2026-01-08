<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMPFMOperationalPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\MPFMOperationalPlan::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assigned_user_id' => 'required|exists:users,id',
            'oil_field_id' => 'required|exists:oil_fields,id',
            'tower_id' => 'required|exists:towers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'plan_date' => 'required|date',
            'shift' => 'nullable|in:morning,afternoon,night',
            'status' => 'nullable|in:draft,active,completed,cancelled',
            'assigned_oil_string_ids' => 'required|array|min:1',
            'assigned_oil_string_ids.*' => 'exists:oil_strings,id',
            'notes' => 'nullable|string',
        ];
    }
}

