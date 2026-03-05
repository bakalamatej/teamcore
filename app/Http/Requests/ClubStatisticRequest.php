<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ClubStatisticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'club_id' => 'required|integer|exists:clubs,club_id',
            'active_members' => 'required|integer|min:0',
            'total_coaches' => 'required|integer|min:0',
            'total_events' => 'required|integer|min:0',
            'total_wins' => 'required|integer|min:0',
            'total_loses' => 'required|integer|min:0',
            'total_draws' => 'required|integer|min:0',
        ];
    }
}
