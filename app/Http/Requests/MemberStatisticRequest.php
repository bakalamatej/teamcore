<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MemberStatisticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'member_club_id' => 'required|integer|exists:member_club,member_club_id',
            'events_attended' => 'required|integer|min:0',
            'training_sessions' => 'required|integer|min:0',
            'matches_played' => 'required|integer|min:0',
            'total_wins' => 'required|integer|min:0',
        ];
    }
}
