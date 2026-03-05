<?php

namespace App\Policies;

use App\Models\EventType;
use App\Models\User;

class EventTypePolicy extends Policy
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
     * Determine if the user can view any event types.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the event type.
     */
    public function view(User $user, EventType $eventType): bool
    {
        return true;
    }
}
