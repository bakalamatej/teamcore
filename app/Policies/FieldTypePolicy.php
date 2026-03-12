<?php

namespace App\Policies;

use App\Models\FieldType;
use App\Models\User;

class FieldTypePolicy extends Policy
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
     * Determine if the user can view any field types.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the field type.
     */
    public function view(User $user, FieldType $fieldType): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, FieldType $fieldType): bool
    {
        return false;
    }

    public function delete(User $user, FieldType $fieldType): bool
    {
        return false;
    }
}