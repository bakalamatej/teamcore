<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CoachEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'coach_id' => 'required|integer|exists:members,member_id',
            'evaluated_by_member_id' => 'required|integer|exists:members,member_id',
            'reservation_id' => 'required|integer|exists:reservations,reservation_id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ];
    }
}
