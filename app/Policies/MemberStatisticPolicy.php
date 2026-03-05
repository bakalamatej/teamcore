<?php

namespace App\Policies;

use App\Models\MemberStatistic;
use App\Models\User;

class MemberStatisticPolicy extends Policy
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
    public function view(User $user, MemberStatistic $statistic): bool
    {
        return true;
    }

}
