<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CoachEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // auth middleware handles this
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'coach_member_club_id' => [
                'required',
                'integer',
                Rule::exists('member_club', 'member_club_id'),
            ],
            'reservation_id' => [
                'required',
                'integer',
                Rule::exists('reservations', 'reservation_id'),
            ],
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'coach_member_club_id.required' => 'Coach is required.',
            'coach_member_club_id.exists' => 'The selected coach does not exist.',
            'reservation_id.required' => 'Reservation is required.',
            'reservation_id.exists' => 'The selected reservation does not exist.',
            'rating.required' => 'Rating is required.',
            'rating.min' => 'Rating must be at least 1.',
            'rating.max' => 'Rating must not exceed 5.',
            'comment.max' => 'Comment must not exceed 1000 characters.',
        ];
    }
}
