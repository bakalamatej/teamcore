<?php

namespace App\Policies;

use App\Models\Club;
use App\Models\User;

class ClubPolicy extends Policy
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
     * Determine if the user can view any clubs.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the club.
     */
    public function view(User $user, Club $club): bool
    {
        return true;
    }

    /**
     * Determine if the user can create clubs.
     */
    public function create(User $user): bool
    {
        return $this->isCoach($user);
    }

    /**
     * Determine if the user can update the club.
     */
    public function update(User $user, Club $club): bool
    {
        return $this->isCoachInClub($user, $club->club_id);
    }

    public function delete(User $user, Club $club): bool
    {
        return false;
    }
}
