<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestVesselRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\TestVessel::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:test_vessels,code',
            'description' => 'nullable|string',
            'oil_field_id' => 'nullable|exists:oil_fields,id',
            'status' => 'required|in:active,inactive,maintenance',
            'location_id' => 'nullable|exists:locations,id',
        ];
    }
}
