<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EventClubResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|integer|exists:events,event_id',
            'club_id' => 'required|integer|exists:clubs,club_id',
            'score' => 'nullable|integer|min:0',
            'ranking' => 'nullable|integer|min:1',
            'note' => 'nullable|string',
        ];
    }
}
