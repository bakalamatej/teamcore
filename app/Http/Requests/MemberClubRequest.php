<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MemberClubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'member_id' => 'required|integer|exists:members,member_id',
            'club_id' => 'required|integer|exists:clubs,club_id',
            'role' => 'required|in:player,coach',
            'joined_at' => 'required|date',
            'left_at' => 'nullable|date|after_or_equal:joined_at',
        ];
    }
}
