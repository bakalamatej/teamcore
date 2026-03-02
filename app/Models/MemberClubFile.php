<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberClubFile extends Model
{
    protected $table = 'member_club_files';

    protected $fillable = [
        'member_club_id',
        'file_id',
        'file_category',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Get the member club associated with this file relationship.
     */
    public function memberClub()
    {
        return $this->belongsTo(MemberClub::class, 'member_club_id');
    }

    /**
     * Get the file associated with this relationship.
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
