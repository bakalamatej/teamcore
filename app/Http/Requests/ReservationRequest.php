<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user() && (Auth::user()->isAdmin() || Auth::user()->isCoach());
    }

    public function rules(): array
    {
        return [
            'sport_field_id' => 'required|integer|exists:sport_fields,sport_field_id',
            'club_id' => 'required|integer|exists:clubs,club_id',
            'created_by_member_club_id' => 'required|integer|exists:member_club,member_club_id',
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:pending,approved,rejected,canceled',
        ];
    }
}
