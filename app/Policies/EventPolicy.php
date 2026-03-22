<?php
namespace App\Policies;
use App\Models\Event;
use App\Models\User;
use App\Enums\EventStatus;

class EventPolicy extends Policy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_admin) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->isCoach($user);
    }

    public function update(User $user, Event $event): bool
    {
        if (in_array($event->status, [EventStatus::FINISHED, EventStatus::ONGOING])) return false;
        if (!$this->isCoach($user)) return false;
        $membership = $user->activeMembership();
        if (!$membership) return false;
        return $this->isCoachInClub($user, (int) $membership->club_id)
            && $event->clubs()->where('clubs.club_id', $membership->club_id)->exists();
    }

    public function delete(User $user, Event $event): bool
    {
        if (in_array($event->status, [EventStatus::FINISHED, EventStatus::ONGOING])) return false;
        if (!$this->isCoach($user)) return false;
        $membership = $user->activeMembership();
        if (!$membership) return false;
        return $this->isCoachInClub($user, (int) $membership->club_id)
            && $event->clubs()->where('clubs.club_id', $membership->club_id)->exists();
    }

    public function register(User $user, Event $event): bool
    {
        $membership = $user->activeMembership();
        if (!$membership) return false;
        if ((int) $membership->sport_id !== (int) $event->sport_id) return false;
        return $event->clubs()->where('clubs.club_id', $membership->club_id)->exists();
    }

    public function unregister(User $user, Event $event): bool
    {
        if ($event->status === EventStatus::FINISHED) return false;
        $membership = $user->activeMembership();
        if (!$membership) return false;
        if ((int) $membership->sport_id !== (int) $event->sport_id) return false;
        if (!$event->clubs()->where('clubs.club_id', $membership->club_id)->exists()) return false;
        return $membership->events()->where('events.event_id', $event->event_id)->exists();
    }

    public function editResults(User $user, Event $event): bool
    {
        if ($event->status !== EventStatus::FINISHED) return false;
        $membership = $user->activeMembership();
        if (!$membership) return false;
        return $this->isCoachInClub($user, (int) $membership->club_id)
            && $event->clubs()->where('clubs.club_id', $membership->club_id)->exists();
    }

    public function storeResults(User $user, Event $event): bool
    {
        return $this->editResults($user, $event);
    }
}