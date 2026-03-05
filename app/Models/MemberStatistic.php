<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberStatistic extends Model
{
    protected $table = 'member_statistics';
    protected $primaryKey = 'stat_id';

    protected $fillable = [
        'member_club_id',
        'events_attended',
        'training_sessions',
        'matches_played',
        'total_wins',
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeByMemberClub($query, $memberClubId)
    {
        if (!$memberClubId) return $query;
        
        return $query->where('member_club_id', $memberClubId);
    }

    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->orderBy('total_wins', 'desc')
                     ->orderBy('matches_played', 'desc')
                     ->limit($limit);
    }

    public function scopeMostActive($query, $limit = 10)
    {
        return $query->orderBy('events_attended', 'desc')
                     ->limit($limit);
    }

    public function scopeByMinEventsAttended($query, $minEvents)
    {
        if (!$minEvents) return $query;
        
        return $query->where('events_attended', '>=', $minEvents);
    }

    public function scopeWithRelation($query)
    {
        return $query->with('memberClub');
    }

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Belongs to a specific membership of a member in a club.
     */
    public function memberClub()
    {
        return $this->belongsTo(MemberClub::class, 'member_club_id');
    }
    
    // -----------------------
    // Increment methods
    // -----------------------

    /**
     * Increment events attended by 1 or custom amount
     */
    public function incrementEventsAttended(int $amount = 1)
    {
        $this->events_attended += $amount;
        $this->save();
    }

    /**
     * Increment training sessions by 1 or custom amount
     */
    public function incrementTrainingSessions(int $amount = 1)
    {
        $this->training_sessions += $amount;
        $this->save();
    }

    /**
     * Increment matches played by 1 or custom amount
     */
    public function incrementMatchesPlayed(int $amount = 1)
    {
        $this->matches_played += $amount;
        $this->save();
    }
}
