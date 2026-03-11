<?php

namespace App\Policies;

use App\Models\EventStatistic;
use App\Models\User;

class EventStatisticPolicy extends Policy
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

    /**     * Determine if the user can view any statistics.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the statistic.
     */
    public function view(User $user, EventStatistic $statistic): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, EventStatistic $statistic): bool
    {
        return false;
    }

    public function delete(User $user, EventStatistic $statistic): bool
    {
        return false;
    }
}
