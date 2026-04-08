<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTVTestRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole(['admin', 'job_officer', 'surveillance_supervisor']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'test_vessel_id' => 'required|exists:test_vessels,id',
            'tower_id' => 'required|exists:towers,id',
            'oil_field_id' => 'required|exists:oil_fields,id',
            'test_date' => 'required|date',
            'shift' => 'required|in:morning,afternoon,night',
            'oil_string_ids' => 'required|array|min:1',
            'oil_string_ids.*' => 'exists:oil_strings,id',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'oil_string_ids.required' => 'Please select at least one oil string.',
            'oil_string_ids.min' => 'Please select at least one oil string.',
        ];
    }
}
