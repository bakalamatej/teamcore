<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventMemberResultRequest extends FormRequest
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
            'member_club_id' => ['required', 'integer', Rule::exists('member_club', 'member_club_id')],
            'score' => 'nullable|numeric|min:0',
            'ranking' => 'nullable|integer|min:1',
            'note' => 'nullable|string|max:500',
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
            'member_club_id.required' => 'Member of the club is required.',
            'member_club_id.exists' => 'The selected member of the club does not exist.',
            'score.min' => 'Score must be a non-negative integer.',
            'ranking.min' => 'Ranking must be at least 1.',
            'note.max' => 'Note must not exceed 500 characters.',
        ];
    }}
