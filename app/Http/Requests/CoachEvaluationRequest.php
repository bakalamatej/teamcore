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
            'coach_member_id' => [
                'required',
                'integer',
                Rule::exists('members', 'member_id'),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $isCoach = \App\Models\MemberClub::where('member_id', $value)
                        ->where('role', 'coach')
                        ->whereNull('left_at')
                        ->exists();

                    if (!$isCoach) {
                        $fail('The selected member is not a coach.');
                    }
                },
            ],

            'evaluated_by_member_id' => [
                'nullable',
                'integer',
                Rule::exists('members', 'member_id'),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value && $this->input('coach_member_id') === $value) {
                        $fail('Coach cannot evaluate themselves.');
                    }
                },
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
            'coach_member_id.required' => 'Coach is required.',
            'coach_member_id.exists' => 'The selected coach does not exist.',
            'evaluated_by_member_id.exists' => 'The selected member does not exist.',
            'rating.required' => 'Rating is required.',
            'rating.numeric' => 'Rating must be a number.',
            'rating.min' => 'Rating must be at least 1.',
            'rating.max' => 'Rating must not exceed 5.',
            'comment.max' => 'Comment must not exceed 1000 characters.',
        ];
    }
}