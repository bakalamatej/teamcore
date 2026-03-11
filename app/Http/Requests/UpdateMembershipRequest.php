<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMembershipRequest extends FormRequest
{
    /**
     * Admin middleware already protects panel routes.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'memberships'              => ['sometimes', 'array'],
            'memberships.*.role'       => ['required_with:memberships', 'in:player,coach'],
            'memberships.*.sport_id'   => ['required_with:memberships', 'integer', Rule::exists('sports', 'sport_id')],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'                  => 'Email is required.',
            'email.email'                     => 'Email must be a valid email address.',
            'email.unique'                    => 'This email address is already in use.',
            'first_name.required'             => 'First name is required.',
            'last_name.required'              => 'Last name is required.',
            'memberships.*.role.required_with' => 'Each membership must have a role.',
            'memberships.*.role.in'           => 'Role must be player or coach.',
            'memberships.*.sport_id.required_with' => 'Each membership must have a sport.',
            'memberships.*.sport_id.exists'   => 'Selected sport does not exist.',
        ];
    }
}
