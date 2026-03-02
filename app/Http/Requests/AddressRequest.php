<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddressRequest extends FormRequest
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
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'street' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'country.required' => 'The country is required.',
            'country.max' => 'The country must not exceed 100 characters.',
            'city.required' => 'The city is required.',
            'city.max' => 'The city must not exceed 100 characters.',
            'street.max' => 'The street must not exceed 100 characters.',
            'zip_code.max' => 'The postal code must not exceed 20 characters.',
        ];
    }
}
