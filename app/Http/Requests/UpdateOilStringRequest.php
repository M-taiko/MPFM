<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOilStringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('oil_string'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $oilStringId = $this->route('oil_string')->id ?? $this->route('oil_string');

        return [
            'tower_id' => 'required|exists:towers,id',
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:255', Rule::unique('oil_strings', 'code')->ignore($oilStringId)],
            'string_number' => 'nullable|integer|min:1|max:99',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
        ];
    }
}

