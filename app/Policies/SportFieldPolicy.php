<?php

namespace App\Policies;

use App\Models\SportField;
use App\Models\User;

class SportFieldPolicy extends Policy
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
     * Determine if the user can view any sport fields.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the sport field.
     */
    public function view(User $user, SportField $sportField): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, SportField $sportField): bool
    {
        return false;
    }

    public function delete(User $user, SportField $sportField): bool
    {
        return false;
    }
}
