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
     * ✅ All users can download
     */
    public function view(User $user, File $file): bool
    {
        return true;
    }

    /**
     * Determine if the user can create files.
     * ✅ Upload: Relevant members
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
     * ✅ Creator + Admin
     */
    public function delete(User $user, File $file): bool
    {
        return $this->ownsResourceByUserId($user, $file->uploaded_by_user_id);
    }
}
