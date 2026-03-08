<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventClubResultRequest extends FormRequest
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
            'event_id' => ['required', 'integer', Rule::exists('events', 'event_id')],
            'club_id' => ['required', 'integer', Rule::exists('clubs', 'club_id')],
            'score' => 'nullable|numeric|min:0',
            'ranking' => 'nullable|integer|min:1',
            'note' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'event_id.required' => 'Event is required.',
            'event_id.exists' => 'The selected event does not exist.',
            'club_id.required' => 'Club is required.',
            'club_id.exists' => 'The selected club does not exist.',
            'score.min' => 'Score must be a non-negative number.',
            'ranking.min' => 'Ranking must be at least 1.',
        ];
    }
}