<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasFiles;
use App\Models\MemberClubFile;

class MemberClub extends Model
{
    use SoftDeletes, HasFiles;

    protected $table = 'member_club';
    protected $primaryKey = 'member_club_id';

    protected $fillable = [
        'member_id',
        'club_id',
        'role',
        'joined_at',
        'left_at',
    ];

    protected $dates = ['joined_at', 'left_at', 'deleted_at'];

    // -----------------------
    // Role constants
    // -----------------------
    const ROLE_PLAYER = 'player';
    const ROLE_COACH = 'coach';

    // -----------------------
    // Scopes
    // -----------------------
    public function scopeByClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('left_at');
    }

    // -----------------------
    // Relationships
    // -----------------------

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function memberClubFiles()
    {
        return $this->belongsToMany(File::class, 'member_club_files', 'member_club_id', 'file_id')
                    ->using(MemberClubFile::class)
                    ->withPivot('file_category')
                    ->withTimestamps();
    }
}
