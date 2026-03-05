<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy extends Policy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_admin) {
            return true;
        }
        return null;
    }

    /**
     * Determine if the user can view any members.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the member.
     */
    public function view(User $user, Member $member): bool
    {
        return $this->ownsMemberById($user, $member->member_id);
    }

    /**
     * Determine if the user can update the member.
     */
    public function update(User $user, Member $member): bool
    {
        return $this->ownsMemberById($user, $member->member_id);
    }
}
