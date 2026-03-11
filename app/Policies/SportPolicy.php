<?php

namespace App\Policies;

use App\Models\Sport;
use App\Models\User;

class SportPolicy extends Policy
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
     * Determine if the user can view any sports.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the sport.
     */
    public function view(User $user, Sport $sport): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Sport $sport): bool
    {
        return false;
    }

    public function delete(User $user, Sport $sport): bool
    {
        return false;
    }
}
