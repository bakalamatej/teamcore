<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasFiles;
use App\Models\ClubFile;
use App\Models\ClubSport;

class Club extends Model
{
    use SoftDeletes, HasFiles;

    protected $table = 'clubs';
    protected $primaryKey = 'club_id';

    protected $fillable = [
        'address_id',
        'sport_id',
        'name',
        'phone',
        'email',
        'webpage',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // -----------------------
    // Relationships
    // -----------------------
    
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function sports()
    {
        return $this->belongsToMany(Sport::class, 'club_sport', 'club_id', 'sport_id')
                    ->using(ClubSport::class);
    }

    public function memberMemberships()
    {
        return $this->hasMany(MemberClub::class, 'club_id');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'member_club', 'club_id', 'member_id')
                    ->withTimestamps()
                    ->withPivot('deleted_at', 'left_at', 'role');
    }

    public function coaches()
    {
        return $this->members()
                    ->wherePivot('role', 'coach')
                    ->wherePivotNull('deleted_at')
                    ->wherePivotNull('left_at');
    }

    public function players()
    {
        return $this->members()
                    ->wherePivot('role', 'player')
                    ->wherePivotNull('deleted_at')
                    ->wherePivotNull('left_at');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_club', 'club_id', 'event_id')
                    ->withTimestamps();
    }

    public function clubFiles()
    {
        return $this->belongsToMany(File::class, 'club_files', 'club_id', 'file_id')
                    ->using(ClubFile::class)
                    ->withPivot('file_category')
                    ->withTimestamps();
    }

    public function clubStatistic()
    {
        return $this->hasOne(ClubStatistic::class, 'club_id');
    }

    public function eventClubResults()
    {
        return $this->hasMany(EventClubResult::class, 'club_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'club_id');
    }

    // -----------------------
    // Scopes
    // -----------------------
    
    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%")
                     ->orWhere('phone', 'like', "%{$search}%");
    }

    public function scopeByCity($query, $city)
    {
        if (!$city) return $query;
        
        return $query->whereHas('address', function($q) use ($city) {
            $q->where('city', $city);
        });
    }

    public function scopeByCountry($query, $country)
    {
        if (!$country) return $query;
        
        return $query->whereHas('address', function($q) use ($country) {
            $q->where('country', $country);
        });
    }

    public function scopeBySport($query, $sportId)
    {
        return $sportId ? $query->where('sport_id', $sportId) : $query;
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeSortBy($query, $sort = 'name', $order = 'asc')
    {
        $allowed = ['name', 'created_at', 'email', 'phone'];
        
        if (!in_array($sort, $allowed)) {
            return $query->orderBy('name', 'asc');
        }
        
        $order = in_array($order, ['asc', 'desc']) ? $order : 'asc';
        return $query->orderBy($sort, $order);
    }

    public function scopeWithRelations($query)
    {
        return $query->with('address', 'sport', 'members', 'clubFiles');
    }

    // -----------------------
    // Methods
    // -----------------------

    public function activeMembers()
    {
        return $this->members()
                    ->wherePivotNull('deleted_at')
                    ->wherePivotNull('left_at');
    }

    public function getActiveMembersCount()
    {
        return $this->activeMembers()->count();
    }

    public function hasMember(Member $member)
    {
        return $this->activeMembers()
                    ->where('member_id', $member->member_id)
                    ->exists();
    }

    public function getMemberRole(Member $member)
    {
        return $this->members()
                    ->where('member_id', $member->member_id)
                    ->first()
                    ?->pivot->role;
    }

    public function isCoach(Member $member)
    {
        return $this->getMemberRole($member) === 'coach';
    }

    public function activeEvents()
    {
        return $this->events();
    }
}