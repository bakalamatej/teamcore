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
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where('file_name', 'like', "%{$search}%");
    }

    public function scopeByFileType($query, $fileType)
    {
        if (!$fileType) return $query;
        
        return $query->where('file_type', 'like', "%{$fileType}%");
    }

    public function scopeByUploadedUser($query, $userId)
    {
        if (!$userId) return $query;
        
        return $query->where('uploaded_by_user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeOrderByDate($query, $order = 'desc')
    {
        return $query->orderBy('created_at', in_array($order, ['asc', 'desc']) ? $order : 'desc');
    }

    public function scopeOrderBySize($query, $order = 'desc')
    {
        return $query->orderBy('file_size', in_array($order, ['asc', 'desc']) ? $order : 'desc');
    }

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
                    ->withPivot('file_category_id')
                    ->withTimestamps();
    }

    /**
     * Get events that have this file.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_files', 'file_id', 'event_id')
                    ->using(EventFile::class)
                    ->withPivot('file_category_id')
                    ->withTimestamps();
    }

    /**
     * Get member clubs that have this file.
     */
    public function memberClubs()
    {
        return $this->belongsToMany(MemberClub::class, 'member_club_files', 'file_id', 'member_club_id')
                    ->using(MemberClubFile::class)
                    ->withPivot('file_category_id')
                    ->withTimestamps();
    }
}

