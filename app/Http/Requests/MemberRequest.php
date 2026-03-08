<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth middleware handles this
    }

    public function rules(): array
    {
        $memberId = $this->route('member')?->member_id;

        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'user_id'),
                Rule::unique('members', 'user_id')->ignore($memberId, 'member_id'),
            ],
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'user_id.unique' => 'This user already has a member profile.',
            'first_name.required' => 'First name is required.',
            'first_name.max' => 'First name must not exceed 100 characters.',
            'last_name.required' => 'Last name is required.',
            'last_name.max' => 'Last name must not exceed 100 characters.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'date_of_birth.date' => 'Date of birth must be a valid date.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
        ];
    }
}
