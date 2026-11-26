<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOilFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('oil_field'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $oilFieldId = $this->route('oil_field')->id ?? $this->route('oil_field');
        
        return [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:255', Rule::unique('oil_fields', 'code')->ignore($oilFieldId)],
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'status' => 'required|in:active,inactive,maintenance',
        ];
    }
}

