<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOilStringRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\OilString::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tower_id' => 'required|exists:towers,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:oil_strings,code',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,maintenance',
        ];
    }
}

