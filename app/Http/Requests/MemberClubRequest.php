<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\MemberClubRole;

class MemberClubRequest extends FormRequest
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
            'member_id' => ['required', 'integer', Rule::exists('members', 'member_id')],
            'club_id' => ['required', 'integer', Rule::exists('clubs', 'club_id')],
            'sport_id' => ['required', 'integer', Rule::exists('club_sport', 'sport_id')->where('club_id', $this->club_id)],
            'role' => ['required', Rule::enum(MemberClubRole::class)],
            'joined_at' => 'required|date',
            'left_at' => 'nullable|date|after_or_equal:joined_at',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'member_id.required' => 'Member is required.',
            'member_id.exists' => 'The selected member does not exist.',
            'club_id.required' => 'Club is required.',
            'club_id.exists' => 'The selected club does not exist.',
            'sport_id.required' => 'Sport is required.',
            'sport_id.exists' => 'The selected sport is not offered by this club.',
            'role.required' => 'Role is required.',
            'role.enum' => 'Role must be either player or coach.',
            'joined_at.required' => 'Join date is required.',
            'joined_at.date' => 'Join date must be a valid date.',
            'left_at.date' => 'Leave date must be a valid date.',
            'left_at.after_or_equal' => 'Leave date must be the same as or later than the join date.',
        ];
    }
}
