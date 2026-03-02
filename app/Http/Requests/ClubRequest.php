<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ClubRequest extends FormRequest
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
        $clubId = $this->route('club')?->club_id;

        return [
            'name' => 'required|string|max:30|unique:clubs,name' . ($clubId ? ",$clubId,club_id" : ''),
            'phone' => 'nullable|string|max:20|unique:clubs,phone' . ($clubId ? ",$clubId,club_id" : ''),
            'email' => 'nullable|email|max:56|unique:clubs,email' . ($clubId ? ",$clubId,club_id" : ''),
            'webpage' => 'nullable|url|max:255',
            'address_id' => 'required|integer|exists:addresses,address_id',
            'sport_id' => 'required|integer|exists:sports,sport_id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The club name is required.',
            'name.unique' => 'A club with this name already exists.',
            'name.max' => 'The club name must not exceed 30 characters.',
            'phone.unique' => 'A club with this phone number already exists.',
            'phone.max' => 'The phone number must not exceed 20 characters.',
            'email.email' => 'The email address must be valid.',
            'email.unique' => 'A club with this email address already exists.',
            'email.max' => 'The email address must not exceed 56 characters.',
            'webpage.url' => 'The webpage must be a valid URL.',
            'webpage.max' => 'The webpage must not exceed 255 characters.',
            'address_id.required' => 'The address is required.',
            'address_id.exists' => 'The selected address does not exist.',
            'sport_id.required' => 'The sport is required.',
            'sport_id.exists' => 'The selected sport does not exist.',
        ];
    }
}
