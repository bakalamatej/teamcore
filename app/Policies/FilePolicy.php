<?php
namespace App\Policies;

use App\Enums\MemberClubRole;
use App\Models\File;
use App\Models\User;

class FilePolicy extends Policy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->is_admin) return true;
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, File $file): bool
    {
        if ($this->ownsResourceByUserId($user, $file->uploaded_by_user_id)) return true;

        $member = $user->member;
        if (!$member) return false;

        $membership = $user->activeMembership();
        if (!$membership) return false;

        $inEvent = $file->events()
            ->whereHas('clubs', fn($q) => $q->where('clubs.club_id', $membership->club_id))
            ->exists();
        if ($inEvent) return true;

        $inClub = $file->clubs()
            ->where('clubs.club_id', $membership->club_id)
            ->exists();
        if ($inClub && $membership->role === MemberClubRole::COACH) return true;

        $ownMemberClub = $file->memberClubs()
            ->where('member_club.member_id', $member->member_id)
            ->exists();
        if ($ownMemberClub) return true;

        return false;
    }

    public function update(User $user, File $file): bool
    {
        return $this->ownsResourceByUserId($user, $file->uploaded_by_user_id);
    }

    public function delete(User $user, File $file): bool
    {
        if (!$this->ownsResourceByUserId($user, $file->uploaded_by_user_id)) {
            return false;
        }

        $membership = $user->activeMembership();
        if (!$membership) return false;

        $inClub = $file->clubs()->where('clubs.club_id', $membership->club_id)->exists();
        if ($inClub) return $membership->role === MemberClubRole::COACH;

        $inMemberClub = $file->memberClubs()
            ->where('member_club.club_id', $membership->club_id)
            ->exists();
        if ($inMemberClub) return $membership->role === MemberClubRole::COACH;

        $inEvent = $file->events()
            ->whereHas('clubs', fn($q) => $q->where('clubs.club_id', $membership->club_id))
            ->exists();
        if ($inEvent) return $membership->role === MemberClubRole::COACH;

        return false;
    }
}