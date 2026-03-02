<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SportFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30',
            'field_type' => 'required|string|max:20',
            'address_id' => 'nullable|integer|exists:addresses,address_id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The sport field name is required.',
            'name.max' => 'The sport field name must not exceed 30 characters.',
            'field_type.required' => 'The field type is required.',
            'field_type.max' => 'The field type must not exceed 20 characters.',
            'address_id.exists' => 'The selected address does not exist.',
        ];
    }
}
