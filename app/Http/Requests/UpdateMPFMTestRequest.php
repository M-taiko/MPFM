<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMPFMTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('mpfm_test'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'oil_field_id' => 'required|exists:oil_fields,id',
            'tower_id' => 'required|exists:towers,id',
            'oil_string_id' => 'required|exists:oil_strings,id',
            'mpfm_operational_plan_id' => 'nullable|exists:mpfm_operational_plans,id',
            'test_date' => 'required|date',
            'shift' => 'required|in:morning,afternoon,night',
            'oil_rate' => 'nullable|numeric|min:0',
            'gas_rate' => 'nullable|numeric|min:0',
            'water_cut' => 'nullable|numeric|min:0|max:100',
            'pressure' => 'nullable|numeric|min:0',
            'temperature' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:draft,submitted,approved,rejected',
        ];
    }
}

