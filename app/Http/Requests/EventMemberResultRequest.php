<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EventMemberResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user() && (Auth::user()->isAdmin() || Auth::user()->isCoach());
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|integer|exists:events,event_id',
            'member_club_id' => 'required|integer|exists:member_club,member_club_id',
            'score' => 'nullable|integer|min:0',
            'ranking' => 'nullable|integer|min:1',
            'note' => 'nullable|string',
        ];
    }
}
