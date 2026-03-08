<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;

class FilePolicy extends Policy
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
     * Determine if the user can view any files.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the file.
     */
    public function view(User $user, File $file): bool
    {
        // Vlastník súboru
        if ($this->ownsResourceByUserId($user, $file->uploaded_by_user_id)) {
            return true;
        }

        $member = $user->member;
        if (!$member) return false;

        // Súbor patrí klubu ktorého je členom
        $inClub = $file->clubs()
            ->whereIn('club_id', $member->activeClubs()->pluck('club_id'))
            ->exists();
        if ($inClub) return true;

        // Súbor patrí eventu ktorého sa zúčastňuje
        $inEvent = $file->events()
            ->whereHas('memberClubs', fn($q) => 
                $q->where('member_club.member_id', $member->member_id)
            )
            ->exists();
        if ($inEvent) return true;

        return false;
    }

    /**
     * Determine if the user can create files.
     */
    public function create(User $user): bool
    {
        return $this->isMember($user);
    }

    /**
     * Determine if the user can update the file.
     * (Not typically used for files)
     */
    public function update(User $user, File $file): bool
    {
        return $this->ownsResourceByUserId($user, $file->uploaded_by_user_id);
    }

    /**
     * Determine if the user can delete the file.
     */
    public function delete(User $user, File $file): bool
    {
        return $this->ownsResourceByUserId($user, $file->uploaded_by_user_id);
    }
}
