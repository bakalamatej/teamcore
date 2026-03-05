<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EventStatisticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|integer|exists:events,event_id',
            'total_participants' => 'required|integer|min:0',
            'total_teams' => 'required|integer|min:0',
        ];
    }
}
