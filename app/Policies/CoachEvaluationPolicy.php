<?php

namespace App\Policies;

use App\Models\CoachEvaluation;
use App\Models\User;

class CoachEvaluationPolicy extends Policy
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

    /**
     * Determine if the user can view any evaluations.
     */
    public function viewAny(User $user): bool
    {
        return $this->isMember($user);
    }

    /**
     * Determine if the user can view the evaluation.
     */ 
    public function view(User $user, CoachEvaluation $evaluation): bool
    {
        return $this->isMember($user);
    }

    /**
     * Determine if the user can create evaluations.
     */
    public function create(User $user): bool
    {
        return $this->isMember($user);
    }

    /**
     * Determine if the user can update the evaluation.
     */
    public function update(User $user, CoachEvaluation $evaluation): bool
    {
        return $this->ownsMemberById($user, $evaluation->evaluated_by_member_id);
    }

    /**
     * Determine if the user can delete the evaluation.
     */
    public function delete(User $user, CoachEvaluation $evaluation): bool
    {
        return $this->ownsMemberById($user, $evaluation->evaluated_by_member_id);
    }
}