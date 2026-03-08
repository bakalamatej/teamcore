<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventTypeRequest extends FormRequest
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
        $eventTypeId = $this->route('event_type')?->event_type_id;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('event_types')->where('sport_id', $this->sport_id)->ignore($eventTypeId, 'event_type_id'),
            ],
            'sport_id' => ['required', 'integer', Rule::exists('sports', 'sport_id')],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The event type name is required.',
            'name.unique' => 'An event type with this name already exists.',
            'name.max' => 'The event type name must not exceed 50 characters.',
            'sport_id.required' => 'The sport is required.',
            'sport_id.exists' => 'The selected sport does not exist.',
        ];
    }
}
