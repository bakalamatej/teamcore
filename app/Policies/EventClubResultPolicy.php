<?php

namespace App\Policies;

use App\Models\EventClubResult;
use App\Models\User;

class EventClubResultPolicy extends Policy
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
     * Determine if the user can view any results.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the result.
     */
    public function view(User $user, EventClubResult $result): bool
    {
        return true;
    }

    /**
     * Determine if the user can create results.
     */
    public function create(User $user): bool
    {
        return $this->isCoach($user);
    }

    /**
     * Determine if the user can update the result.
     */
    public function update(User $user, EventClubResult $result): bool
    {
        if (!$this->isCoach($user)) return false;

        $member = $user->member;
        if (!$member) return false;

        return $member->activeClubs()
            ->where('club_id', $result->club_id)
            ->exists();
    }

    /**
     * Determine if the user can delete the result.
     */
    public function delete(User $user, EventClubResult $result): bool
    {
        if (!$this->isCoach($user)) return false;

        $member = $user->member;
        if (!$member) return false;

        return $member->activeClubs()
            ->where('club_id', $result->club_id)
            ->exists();
    }

}
