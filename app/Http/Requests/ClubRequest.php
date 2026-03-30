<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClubRequest extends FormRequest
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
        $clubId = $this->route('club')?->club_id;

        return [
            'name' => [
                'required',
                'string',
                'max:30',
                Rule::unique('clubs', 'name')
                    ->where(fn ($query) => $query->where('sport_id', $this->input('sport_id')))
                    ->ignore($clubId, 'club_id'),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(\+421|0)[1-9]\d{1,8}$/',
            ],
            'email' => [
                'nullable',
                'email',
                'max:56',
            ],
            'webpage' => 'nullable|url|max:255',
            'address_id' => ['nullable', 'integer', Rule::exists('addresses', 'address_id')],
            'country' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'city' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'street' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'sport_id' => ['required', 'integer', Rule::exists('sports', 'sport_id')],
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
            'address_id.exists' => 'The selected address does not exist.',
            'country.required_without' => 'Country is required when not selecting an existing address.',
            'city.required_without' => 'City is required when not selecting an existing address.',
            'sport_id.required' => 'The sport is required.',
            'sport_id.exists' => 'The selected sport does not exist.',
        ];
    }
}
