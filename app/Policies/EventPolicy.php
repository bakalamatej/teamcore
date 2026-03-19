<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use App\Enums\EventStatus;

class EventPolicy extends Policy
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
     * Determine if the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the event.
     */
    public function view(User $user, Event $event): bool
    {
        return true;
    }

    /**
     * Determine if the user can create events.
     */
    public function create(User $user): bool
    {
        return $this->isCoach($user);
    }

    /**
     * Determine if the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        if (!$this->isCoach($user)) return false;

        $membership = $user->activeMembership();
        if (!$membership) return false;

        return $this->isCoachInClub($user, (int) $membership->club_id)
            && $event->clubs()->where('clubs.club_id', $membership->club_id)->exists();
    }

    /**
     * Determine if the user can delete the event.
     */
     public function delete(User $user, Event $event): bool
    {
        if ($event->status === EventStatus::FINISHED) return false;
        if (!$this->isCoach($user)) return false;

        $membership = $user->activeMembership();
        if (!$membership) return false;

        return $this->isCoachInClub($user, (int) $membership->club_id)
            && $event->clubs()->where('clubs.club_id', $membership->club_id)->exists();
    }

    /**
     * Determine if the user can register for the event.
     */
    public function register(User $user, Event $event): bool
    {
        $membership = $user->activeMembership();
        if (!$membership) {
            return false;
        }

        if ((int) $membership->sport_id !== (int) $event->sport_id) {
            return false;
        }

        return $event->clubs()->where('clubs.club_id', $membership->club_id)->exists();
    }

    /**
     * Determine if the user can unregister from the event.
     */
    public function unregister(User $user, Event $event): bool
    {
        if ($event->status === EventStatus::FINISHED) return false;

        $membership = $user->activeMembership();
        if (!$membership) return false;

        if ((int) $membership->sport_id !== (int) $event->sport_id) {
            return false;
        }

        if (!$event->clubs()->where('clubs.club_id', $membership->club_id)->exists()) {
            return false;
        }

        return $membership->events()->where('event_id', $event->event_id)->exists();
    }
}
