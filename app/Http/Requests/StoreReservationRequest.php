<?php

namespace App\Http\Requests;

use App\Models\MemberClub;
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
            'sport_field_id' => [
                'required',
                'integer',
                Rule::exists('sport_fields', 'sport_field_id'),
            ],

            'created_by_member_club_id' => [
                'required',
                'integer',
                Rule::exists('member_club', 'member_club_id'),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $membership = MemberClub::query()
                        ->where('member_club_id', $value)
                        ->whereNull('left_at')
                        ->first();

                    if (!$membership) {
                        $fail('The selected member club is not active.');
                        return;
                    }

                    if ($this->user()?->is_admin) {
                        return;
                    }

                    $userMemberId = $this->user()?->member?->member_id;

                    if (!$userMemberId || (int) $membership->member_id !== (int) $userMemberId) {
                        $fail('You can only create a reservation for your own active club membership.');
                    }
                },
            ],

            'title' => 'required|string|min:5|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ];
    }

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
            'start_date.after_or_equal' => 'Start date must be today or in the future.',

            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be later than the start date.',
        ];
    }
}