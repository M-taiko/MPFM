<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTowerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Tower::class);
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
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:towers,code',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
            'tower_type' => 'required|in:' . implode(',', array_keys(\App\Models\Tower::TYPES)),
        ];
    }
}

