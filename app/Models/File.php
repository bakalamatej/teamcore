<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClubFile;
use App\Models\EventFile;
use App\Models\MemberClubFile;

class File extends Model
{

    protected $table = 'files';
    protected $primaryKey = 'file_id';

    protected $fillable = [
        'uploaded_by_user_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Get the user who uploaded this file.
     */
    public function uploadedByUser()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    /**
     * Get clubs that have this file.
     */
    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_files', 'file_id', 'club_id')
                    ->using(ClubFile::class)
                    ->withPivot('file_category')
                    ->withTimestamps();
    }

    /**
     * Get events that have this file.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_files', 'file_id', 'event_id')
                    ->using(EventFile::class)
                    ->withPivot('file_category')
                    ->withTimestamps();
    }

    /**
     * Get member clubs that have this file.
     */
    public function memberClubs()
    {
        return $this->belongsToMany(MemberClub::class, 'member_club_files', 'file_id', 'member_club_id')
                    ->using(MemberClubFile::class)
                    ->withPivot('file_category')
                    ->withTimestamps();
    }
}
