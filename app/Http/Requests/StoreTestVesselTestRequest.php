<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestVesselTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\TestVesselTest::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'test_vessel_id' => 'required|exists:test_vessels,id',
            'oil_field_id' => 'required|exists:oil_fields,id',
            'tower_id' => 'required|exists:towers,id',
            'oil_string_id' => 'required|exists:oil_strings,id',
            'mpfm_test_id' => 'nullable|exists:mpfm_tests,id',
            'test_date' => 'required|date',
            'shift' => 'required|in:morning,afternoon,night',
            'oil_rate' => 'required|numeric|min:0',
            'gas_rate' => 'required|numeric|min:0',
            'water_cut' => 'required|numeric|min:0|max:100',
            'pressure' => 'required|numeric|min:0',
            'temperature' => 'required|numeric|min:-50|max:150',
            'gor' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,submitted,approved,rejected',
            'tv_test_request_well_id' => 'nullable|exists:tv_test_request_wells,id',
        ];
    }
}
