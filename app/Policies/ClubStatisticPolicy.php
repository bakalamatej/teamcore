<?php

namespace App\Policies;

use App\Models\ClubStatistic;
use App\Models\User;

class ClubStatisticPolicy extends Policy
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
    public function view(User $user, ClubStatistic $statistic): bool
    {
        return true;
    }

}