<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\EventStatus;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:5|max:80',
            'sport_id' => ['required', 'integer', Rule::exists('sports', 'sport_id')],
            'sport_field_id' => ['required', 'integer', Rule::exists('sport_fields', 'sport_field_id')],
            'event_type_id' => [
                'required',
                'integer',
                Rule::exists('event_types', 'event_type_id')->where(fn($query) => $query->where('sport_id', $this->input('sport_id'))),
            ],
            'parent_event_id' => ['nullable', 'integer', Rule::exists('events', 'event_id')],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => ['required', Rule::enum(EventStatus::class)],
            'description' => 'nullable|string|min:10',
            'club_ids' => ['required', 'array', 'min:1'],
            'club_ids.*' => [
                'integer',
                Rule::exists('clubs', 'club_id'),
                Rule::exists('club_sport', 'club_id')->where(fn($query) => $query->where('sport_id', $this->input('sport_id'))),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'title.min' => 'Title must be at least 5 characters.',
            'sport_id.required' => 'Sport is required.',
            'sport_id.exists' => 'Selected sport does not exist.',
            'sport_field_id.exists' => 'Selected sport field does not exist.',
            'event_type_id.exists' => 'Selected event type is invalid for selected sport.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'club_ids.required' => 'At least one club is required.',
            'club_ids.min' => 'At least one club is required.',
            'club_ids.*.exists' => 'One or more selected clubs is invalid for selected sport.',
        ];
    }
}