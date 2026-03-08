<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SportFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // auth middleware handles this
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30',
            'field_type_id' => ['required', 'integer', Rule::exists('field_types', 'field_type_id')],
            'address_id' => ['nullable', 'integer', Rule::exists('addresses', 'address_id')],
            'country' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'city' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'street' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'sport_ids' => 'required|array',
            'sport_ids.*' => ['integer', Rule::exists('sports', 'sport_id')],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The sport field name is required.',
            'name.max' => 'The sport field name must not exceed 30 characters.',
            'field_type_id.required' => 'The field type is required.',
            'field_type_id.exists' => 'The selected field type does not exist.',
            'address_id.exists' => 'The selected address does not exist.',
            'country.required_without' => 'Country is required when not selecting an existing address.',
            'city.required_without' => 'City is required when not selecting an existing address.',
            'sport_ids.required' => 'At least one sport is required.',
            'sport_ids.*.exists' => 'One or more selected sports do not exist.',
        ];
    }
}
