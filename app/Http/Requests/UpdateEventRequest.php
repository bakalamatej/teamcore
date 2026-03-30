<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\EventStatus;
use App\Models\Club;
use App\Models\SportField;
use App\Models\EventType;

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
        $eventType = EventType::find($this->input('event_type_id'));
        $eventSportId = $eventType?->sport_id;

        $eventId = $this->route('event')?->event_id ?? $this->route('event');

        return [
            'title' => 'required|string|min:5|max:80',

            'sport_field_id' => [
                'required',
                'integer',
                Rule::exists('sport_fields', 'sport_field_id'),
            ],

            'event_type_id' => [
                'required',
                'integer',
                Rule::exists('event_types', 'event_type_id'),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $sportFieldId = $this->input('sport_field_id');
                    
                    if (!$value || !$sportFieldId) {
                        return;
                    }

                    $eventType = EventType::find($value);
                    $sportField = SportField::find($sportFieldId);

                    if (!$eventType || !$sportField) {
                        return;
                    }

                    $hasMatchingSport = $sportField->sports()
                        ->where('sport_fields_sports.sport_id', $eventType->sport_id)
                        ->exists();

                    if (!$hasMatchingSport) {
                        $fail('Event type sport must match the sport field sport.');
                    }
                },
            ],

            'parent_event_id' => [
                'nullable',
                'integer',
                Rule::exists('events', 'event_id'),
                Rule::notIn([$eventId]),
            ],

            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',

            'status' => [
                'required',
                Rule::enum(EventStatus::class),
            ],

            'description' => 'nullable|string|min:10',

            'club_ids' => ['required', 'array', 'min:1'],

            'club_ids.*' => [
                'integer',
                Rule::exists('clubs', 'club_id'),
                function (string $attribute, mixed $value, \Closure $fail) use ($eventSportId): void {
                    if (!$eventSportId) {
                        return;
                    }

                    if (!Club::where('club_id', $value)->where('sport_id', $eventSportId)->exists()) {
                        $fail('One or more selected clubs is invalid for selected event type sport.');
                    }
                },
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
            'sport_field_id.exists' => 'Selected sport field does not exist.',
            'event_type_id.exists' => 'Selected event type is invalid for selected sport.',
            'parent_event_id.exists' => 'Selected parent event does not exist.',
            'parent_event_id.not_in' => 'Event cannot be its own parent.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'club_ids.required' => 'At least one club is required.',
            'club_ids.min' => 'At least one club is required.',
            'club_ids.*.exists' => 'One or more selected clubs does not exist.',
        ];
    }
}