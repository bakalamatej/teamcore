<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        return $user->isCoach() || $user->isAdmin();
    } 

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|min:5|max:80',

            'sport_field_id' => 'required|integer|exists:sport_fields,sport_field_id',
            'event_type_id' => 'required|integer|exists:event_types,event_type_id',

            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',

            'description' => 'nullable|string|min:10',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required.',
            'title.min' => 'Title must be at least 5 characters.',
            'sport_field_id.exists' => 'Selected sport field does not exist.',
            'event_type_id.exists' => 'Selected event type does not exist.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
        ];
    }
}
