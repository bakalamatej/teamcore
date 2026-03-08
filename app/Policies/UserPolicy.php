<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends Policy
{
    public function before(User $user, string $ability): bool|null
{
    if ($user->is_admin) {
        
        if ($ability === 'delete') {
            return null; 
        }
        return true; 
    }
    return null;
}

public function delete(User $user, User $model): bool
{
    return $user->user_id !== $model->user_id;
}
}
