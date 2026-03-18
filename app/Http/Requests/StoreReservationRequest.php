<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sport_id'                  => ['required', 'integer', Rule::exists('sports', 'sport_id')],
            'sport_field_id'            => ['required', 'integer', Rule::exists('sport_fields', 'sport_field_id')],
            'club_id'                   => ['required', 'integer', Rule::exists('clubs', 'club_id')],
            'created_by_member_club_id' => ['required', 'integer', Rule::exists('member_club', 'member_club_id')],
            'title'                     => 'required|string|max:100',
            'description'               => 'nullable|string',
            'start_date'                => 'required|date|after_or_equal:today',
            'end_date'                  => 'required|date|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'created_by_member_club_id.required' => 'Member club is required.',
            'created_by_member_club_id.exists'   => 'The selected member club does not exist.',
            'sport_id.required' => 'Sport is required.',
            'sport_id.exists' => 'The selected sport does not exist.',
            'sport_field_id.required' => 'Field is required.',
            'sport_field_id.exists' => 'The selected field does not exist.',
            'club_id.required' => 'Club is required.',
            'club_id.exists' => 'The selected club does not exist.',
            'title.required' => 'Title is required.',
            'title.max' => 'Title must not exceed 100 characters.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after_or_equal' => 'Start date must be today or in the future.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be the same as or later than the start date.',
        ];
    }
}