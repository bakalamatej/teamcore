<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

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
     * ✅ Only coaches
     */
    public function create(User $user): bool
    {
        return $this->isCoach($user);
    }

    /**
     * Determine if the user can update the event.
     * ✅ Creator + Admin
     * For now: coaches can update (since no created_by field)
     */
    public function update(User $user, Event $event): bool
    {
        return $this->isCoach($user);
    }

    /**
     * Determine if the user can delete the event.
     * ✅ Creator (if not finished) + Admin
     * For now: coaches can delete (since no created_by field)
     */
    public function delete(User $user, Event $event): bool
    {
        // Only allow deletion if event is not finished
        if ($event->status === Event::STATUS_FINISHED) {
            return false;
        }

        return $this->isCoach($user);
    }
}
