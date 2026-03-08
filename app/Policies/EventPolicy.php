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

        $member = $user->member;
        if (!$member) return false;

        return $member->activeClubs()
            ->whereIn('club_id', $event->clubs()->pluck('club_id'))
            ->exists();
    }

    /**
     * Determine if the user can delete the event.
     */
     public function delete(User $user, Event $event): bool
    {
        if ($event->status === EventStatus::FINISHED) return false;
        if (!$this->isCoach($user)) return false;

        $member = $user->member;
        if (!$member) return false;

        return $member->activeClubs()
            ->whereIn('club_id', $event->clubs()->pluck('club_id'))
            ->exists();
    }

    /**
     * Determine if the user can register for the event.
     */
    public function register(User $user, Event $event): bool
    {
        // Check if member is in one of the event's clubs
        $member = $user->member;
        if (!$member) {
            return false;
        }

        return $member->activeClubs()
            ->whereIn('club_id', $event->clubs()->pluck('club_id'))
            ->exists();
    }

    /**
     * Determine if the user can unregister from the event.
     */
    public function unregister(User $user, Event $event): bool
    {
        if ($event->status === EventStatus::FINISHED) return false;

        $member = $user->member;
        if (!$member) return false;

        return $member->events()
            ->where('event_id', $event->event_id)
            ->exists();
    }
}
