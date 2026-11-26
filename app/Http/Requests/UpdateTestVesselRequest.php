<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestVesselRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('test_vessel'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $vesselId = $this->route('test_vessel')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:test_vessels,code,' . $vesselId,
            'description' => 'nullable|string',
            'oil_field_id' => 'nullable|exists:oil_fields,id',
            'status' => 'required|in:active,inactive,maintenance',
            'location_id' => 'nullable|exists:locations,id',
        ];
    }
}
