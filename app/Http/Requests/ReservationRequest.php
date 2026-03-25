<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReservationRequest extends FormRequest
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
            'sport_field_id' => [
                'required',
                'integer',
                Rule::exists('sport_fields', 'sport_field_id'),
            ],

            'created_by_member_club_id' => [
                'required',
                'integer',
                Rule::exists('member_club', 'member_club_id'),
            ],

            'title' => 'required|string|min:5|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'created_by_member_club_id.required' => 'Member club is required.',
            'created_by_member_club_id.exists' => 'The selected member club does not exist.',

            'sport_field_id.required' => 'Field is required.',
            'sport_field_id.exists' => 'The selected field does not exist.',

            'title.required' => 'Title is required.',
            'title.min' => 'Title must be at least 5 characters.',
            'title.max' => 'Title must not exceed 255 characters.',

            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',

            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be later than the start date.',
        ];
    }
}