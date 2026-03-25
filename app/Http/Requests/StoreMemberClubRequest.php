<?php

namespace App\Http\Requests;

use App\Models\MemberClub;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\MemberClubRole;

class StoreMemberClubRequest extends FormRequest
{
    /**
     * Admin middleware already protects panel routes.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var \App\Models\Member $member */
        $member = $this->route('member');

        return [
            'club_id' => [
                'required',
                'integer',
                Rule::exists('clubs', 'club_id'),
                function ($attr, $value, $fail) use ($member) {
                    $exists = MemberClub::where('member_id', $member->member_id)
                        ->where('club_id', $value)
                        ->whereNull('left_at')
                        ->exists();
                    if ($exists) {
                        $fail('This member already has an active membership in this club.');
                    }
                },
            ],
            'role' => ['required', Rule::enum(MemberClubRole::class)],
            'joined_at' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'club_id.required'   => 'Club is required.',
            'club_id.exists'     => 'Selected club does not exist.',
            'role.required'      => 'Role is required.',
            'role.enum' => 'Role must be player or coach.',
            'joined_at.required' => 'Join date is required.',
            'joined_at.date'     => 'Join date must be a valid date.',
        ];
    }
}
