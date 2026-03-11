<?php

namespace App\Policies;

use App\Models\MemberClub;
use App\Models\User;

class CoachPolicy extends Policy
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
     * Determine if the user can access coach views.
     */
    public function viewAny(User $user): bool
    {
        return $this->isCoach($user);
    }

    public function view(User $user, MemberClub $memberClub): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, MemberClub $memberClub): bool
    {
        return false;
    }

    public function delete(User $user, MemberClub $memberClub): bool
    {
        return false;
    }
}
