<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CoachClubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $clubId = $this->route('club')?->club_id;
        return [
            'name' => [
                'required',
                'string',
                'max:30',
                Rule::unique('clubs', 'name')->ignore($clubId, 'club_id'),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('clubs', 'phone')->ignore($clubId, 'club_id'),
            ],
            'email' => [
                'nullable',
                'email',
                'max:56',
                Rule::unique('clubs', 'email')->ignore($clubId, 'club_id'),
            ],
            'webpage' => 'nullable|url|max:255',
            'address_id' => ['nullable', 'integer', Rule::exists('addresses', 'address_id')],
            'country' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'city' => ['required_without:address_id', 'nullable', 'string', 'max:100'],
            'street' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
        ];
    }

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
            'address_id.exists' => 'The selected address does not exist.',
            'country.required_without' => 'Country is required when not selecting an existing address.',
            'city.required_without' => 'City is required when not selecting an existing address.',
        ];
    }
}