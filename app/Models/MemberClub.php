<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MemberClubFile;
use App\Enums\MemberClubRole;

class MemberClub extends Model
{

    protected $table = 'member_club';
    protected $primaryKey = 'member_club_id';

    protected $fillable = [
        'member_id',
        'club_id',
        'sport_id',
        'role',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'role'      => MemberClubRole::class,
        'joined_at' => 'date',
        'left_at'   => 'date',
    ];

    // -----------------------
    // Scopes
    // -----------------------
    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;

        return $query->whereHas('member', fn($q) => $q->where(fn($q) => $q
            ->where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%")
        ));
    }

    public function scopeBySport($query, $sportId)
    {
        return $query->where('sport_id', $sportId);
    }

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

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function memberClubFiles()
    {
        return $this->belongsToMany(File::class, 'member_club_files', 'member_club_id', 'file_id')
                    ->using(MemberClubFile::class)
                    ->withPivot('file_category_id')
                    ->withTimestamps();
    }
}

