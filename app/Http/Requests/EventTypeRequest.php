<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EventTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $eventTypeId = $this->route('event_type')?->event_type_id;

        return [
            'name' => 'required|string|max:30|unique:event_types,name' . ($eventTypeId ? ",$eventTypeId,type_id" : ''),
            'sport_id' => 'required|integer|exists:sports,sport_id',
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
            'name.max' => 'The event type name must not exceed 30 characters.',
            'sport_id.required' => 'The sport is required.',
            'sport_id.exists' => 'The selected sport does not exist.',
        ];
    }
}
