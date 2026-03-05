<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

class AddressPolicy extends Policy
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
     * Determine if the user can view any addresses.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the address.
     */
    public function view(User $user, Address $address): bool
    {
        return true;
    }
}
