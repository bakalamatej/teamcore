<?php

namespace App\Policies;

use App\Models\EventMemberResult;
use App\Models\User;

class EventMemberResultPolicy extends Policy
{
    /**     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_admin) {
            return true;
        }
        return null;
    }

    /**     * Determine if the user can view any results.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the result.
     */
    public function view(User $user, EventMemberResult $result): bool
    {
        return true;
    }

}
