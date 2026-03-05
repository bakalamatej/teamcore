<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,user_id|unique:members,user_id',
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
        ];
    }
}
