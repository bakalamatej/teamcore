<?php

namespace App\Policies;

use App\Enums\MemberClubRole;
use App\Models\MemberClub;
use App\Models\User;

class Policy
{
    /**
     * Check if user is a member (has member record).
     */
    protected function isMember(User $user): bool
    {
        return $user->member !== null;
    }

    /**
     * Check if user is a coach anywhere.
     */
    protected function isCoach(User $user): bool
    {
        return $user->isCoach();
    }

    /**
     * Check if user is a coach in a specific club.
     */
    protected function isCoachInClub(User $user, int $clubId): bool
    {
        $membership = $user->activeMembership();
        if (!$membership) {
            return false;
        }

        $role = $membership->role;
        $roleValue = is_object($role) && isset($role->value) ? $role->value : (string) $role;

        return (int) $membership->club_id === (int) $clubId
            && $roleValue === MemberClubRole::COACH->value;
    }

    /**
     * Check if user is a member of a specific club.
     */
    protected function isClubMember(User $user, int $clubId): bool
    {
        $member = $user->member;
        if (!$member) {
            return false;
        }

        return $member->clubMemberships()
            ->where('club_id', $clubId)
            ->whereNull('left_at')
            ->exists();
    }

    /**
     * Check if user owns resource by user_id (for User-related models).
     * Usage: $this->ownsResourceByUserId($user, $file->uploaded_by_user_id)
     */
    protected function ownsResourceByUserId(User $user, ?int $resourceUserId): bool
    {
        if (!$resourceUserId) {
            return false;
        }

        return $user->user_id === $resourceUserId;
    }

    /**
     * Check if user owns resource by member_id (generic check).
     * Usage: $this->ownsMemberById($user, $model->created_by_member_id)
     *        $this->ownsMemberById($user, $evaluation->evaluated_by_member_id)
     */
    protected function ownsMemberById(User $user, ?int $memberIdToCheck): bool
    {
        if (!$memberIdToCheck) {
            return false;
        }

        return $user->member && $user->member->member_id === $memberIdToCheck;
    }

    /**
     * Check if user created a model (by member_id comparison).
     * Usage: $this->isCreatorByMemberId($user, $model->creator_member_id)
     */
    protected function isCreatorByMemberId(User $user, ?int $creatorMemberId): bool
    {
        if (!$creatorMemberId) {
            return false;
        }

        $member = $user->member;
        if (!$member) {
            return false;
        }

        return $member->member_id === $creatorMemberId;
    }

    /**
     * Check if user created a reservation (via MemberClub relationship).
     * Usage: $this->isReservationCreator($user, $reservation->created_by_member_club_id)
     */
    protected function isReservationCreator(User $user, ?int $createdByMemberClubId): bool
    {
        if (!$createdByMemberClubId || !$user->member) {
            return false;
        }

        return MemberClub::where('member_club_id', $createdByMemberClubId)
            ->where('member_id', $user->member->member_id)
            ->exists();
    }
}
