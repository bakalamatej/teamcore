<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;

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
        
        return $query->where('first_name', 'like', "%{$search}%")
                     ->orWhere('last_name', 'like', "%{$search}%")
                     ->orWhere('phone', 'like', "%{$search}%");
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

    public function scopeCoaches($query, $clubId = null)
    {
        $query->whereHas('clubMemberships', function($q) {
            $q->where('role', 'coach')->whereNull('deleted_at');
        });
        
        if ($clubId) {
            $query->where('club_id', $clubId);
        }
        
        return $query;
    }

    public function scopePlayers($query, $clubId = null)
    {
        $query->whereHas('clubMemberships', function($q) {
            $q->where('role', 'player')->whereNull('deleted_at');
        });
        
        if ($clubId) {
            $query->where('club_id', $clubId);
        }
        
        return $query;
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
                    ->withPivot('deleted_at', 'left_at');
    }

    /**
     * Get events through active club memberships
     */
    public function events()
    {
        return Event::whereHas('memberClubs', function($query) {
            $query->whereHas('memberClub', function($subQuery) {
                $subQuery->where('member_id', $this->member_id);
            });
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
                    ->withPivot('deleted_at', 'left_at')
                    ->wherePivotNull('deleted_at')
                    ->wherePivotNull('left_at');
    }

    public function coachEvaluations()
    {
        return $this->hasMany(CoachEvaluation::class, 'coach_id');
    }

    public function memberEvaluations()
    {
        return $this->hasMany(CoachEvaluation::class, 'evaluated_by_member_id');
    }

    /**
     * Returns active events through active club memberships (member_club NOT soft deleted)
     */
    public function activeEvents()
    {
        return $this->events()
                    ->whereHas('memberClubs', function($q) {
                        $q->whereNull('member_club.deleted_at');
                    });
    }

    /**
     * Validation rules
     */
    public function validate(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'email' => 'required|email|max:56',
            'phone' => 'nullable|string|max:20',
        ]);
    }

    /**
     * Soft delete member and his memberships in clubs
     */
    protected static function booted()
    {
        static::deleting(function ($member) {
            if ($member->isForceDeleting()) return;

            $member->clubs()->updateExistingPivot(
                $member->clubs->pluck('club_id')->toArray(),
                ['deleted_at' => now()]
            );
        });
    }
}
