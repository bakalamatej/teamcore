<?php

namespace App\Http\Requests;

use App\Models\MemberClub;
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
        /** @var \App\Models\MemberClub $memberClub */
        $memberClub = $this->route('memberClub');
        $memberId = $memberClub?->member_id ?? $this->input('member_id');

        return [
            'member_id' => ['required', 'integer', Rule::exists('members', 'member_id')],
            'club_id' => [
                'required',
                'integer',
                Rule::exists('clubs', 'club_id'),
                // Validate unique active membership only if left_at is null (not leaving)
                function ($attr, $value, $fail) use ($memberId) {
                    if (!$this->input('left_at')) {
                        $query = MemberClub::where('member_id', $memberId)
                            ->where('club_id', $value)
                            ->where('sport_id', $this->input('sport_id'))
                            ->whereNull('left_at');
                        
                        // Exclude current record on update
                        if ($this->route('memberClub')) {
                            $query->where('member_club_id', '!=', $this->route('memberClub')->member_club_id);
                        }
                        
                        if ($query->exists()) {
                            $fail('This member already has an active membership in this club for the selected sport.');
                        }
                    }
                },
            ],
            'sport_id' => ['required', 'integer', Rule::exists('club_sport', 'sport_id')->where('club_id', $this->input('club_id'))],
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
