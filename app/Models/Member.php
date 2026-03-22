<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\MemberClubRole;

class Member extends Model
{
    use SoftDeletes;

    protected $table = 'members';
    protected $primaryKey = 'member_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'date_of_birth',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    public function scopeByClub($query, $clubId)
    {
        if (!$clubId) return $query;
        
        return $query->whereHas('clubs', function($q) use ($clubId) {
            $q->where('club_id', $clubId);
        });
    }

    public function scopeByRole($query, $clubId, $role)
    {
        if (!$clubId || !$role) return $query;
        
        return $query->whereHas('clubMemberships', function($q) use ($clubId, $role) {
            $q->where('club_id', $clubId)->where('role', $role);
        });
    }

    public function scopeCoaches($query, $clubId = null, $sportId = null)
    {
        return $query->whereHas('clubMemberships', function ($q) use ($clubId, $sportId) {
            $q->byRole(MemberClubRole::COACH)->active();
            if ($clubId) $q->byClub($clubId);
            if ($sportId) $q->bySport($sportId);
        });
    }

    public function scopePlayers($query, $clubId = null, $sportId = null)
    {
        return $query->whereHas('clubMemberships', function ($q) use ($clubId, $sportId) {
            $q->byRole(MemberClubRole::PLAYER)->active();
            if ($clubId) $q->byClub($clubId);
            if ($sportId) $q->bySport($sportId);
        });
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeOrderByName($query, $order = 'asc')
    {
        return $query->orderBy('last_name', $order)->orderBy('first_name', $order);
    }

    public function scopeWithClubs($query)
    {
        return $query->with('clubs');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function clubMemberships()
    {
        return $this->hasMany(MemberClub::class, 'member_id');
    }

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'member_club', 'member_id', 'club_id')
                    ->withTimestamps()
                    ->withPivot('left_at');
    }

    public function memberStatistics()
    {
        return $this->hasManyThrough(
            MemberStatistic::class,
            MemberClub::class,
            'member_id', // Foreign key on MemberClub
            'member_club_id', // Foreign key on MemberStatistic
            'member_id', // Local key on Member
            'member_club_id' // Local key on MemberClub
        );
    }

    /**
     * Returns clubs the member can view: own clubs + clubs sharing an event.
     */
    public function visibleClubs()
    {
        $ownClubIds = $this->clubs()->pluck('clubs.club_id');
        $sportIds = $this->clubMemberships()->active()->pluck('sport_id')->unique();

        return Club::whereIn('clubs.club_id', $ownClubIds)
            ->orWhereHas('sports', fn($q) => $q->whereIn('sports.sport_id', $sportIds));
    }

    /**
     * Returns events where the member is registered or belongs to a participating club.
     */
    public function events()
    {
        $memberships = $this->clubMemberships()->active()->get(['club_id', 'sport_id']);
        $clubIds = $memberships->pluck('club_id');
        $sportIds = $memberships->pluck('sport_id');

        return Event::where(function ($q) use ($clubIds, $sportIds) {
            $q->whereIn('sport_id', $sportIds)
            ->whereHas('clubs', fn($q) => $q->whereIn('clubs.club_id', $clubIds));
        });
    }


    // -----------------------
    // Methods
    // -----------------------

    /**
     * Returns full name
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Returns active clubs
     */
    public function activeClubs()
    {
        return $this->belongsToMany(Club::class, 'member_club', 'member_id', 'club_id')
                    ->withPivot('left_at')
                    ->wherePivotNull('left_at');
    }

    public function coachEvaluations()
    {
        return $this->hasManyThrough(
            CoachEvaluation::class,
            MemberClub::class,
            'member_id',
            'coach_member_club_id',
            'member_id',
            'member_club_id'
        );
    }

    public function memberEvaluations()
    {
        return $this->hasMany(CoachEvaluation::class, 'evaluated_by_member_id');
    }

    /**
     * Returns events where the member is registered through an active membership.
     */
    public function activeEventsQuery()
    {
        return Event::whereHas('memberClubs', function ($q) {
            $q->where('member_club.member_id', $this->member_id)
              ->whereNull('member_club.left_at');
        });
    }

    /**
     * Soft delete member and his memberships in clubs
     */
    protected static function booted()
    {
        static::deleting(function ($member) {
            if ($member->isForceDeleting()) return;

            MemberClub::where('member_id', $member->member_id)
                ->whereNull('left_at')
                ->update(['left_at' => now()]);
        });
    }
}
