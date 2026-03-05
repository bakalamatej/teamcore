<?php

namespace App\Policies;

use App\Models\MemberClub;
use App\Models\User;

class MemberClubPolicy extends Policy
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
     * Determine if the user can view any member clubs.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the member club.
     */
    public function view(User $user, MemberClub $memberClub): bool
    {
        // User can view their own membership
        if ($this->isCreatorByMemberId($user, $memberClub->member_id)) {
            return true;
        }

        // Club members can view each other's memberships
        if ($user->member) {
            return $user->member->clubs()
                ->where('club_id', $memberClub->club_id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can delete the member club.
     */
    public function delete(User $user, MemberClub $memberClub): bool
    {
        // Member can remove themselves from club
        return $this->isCreatorByMemberId($user, $memberClub->member_id);
    }
}
