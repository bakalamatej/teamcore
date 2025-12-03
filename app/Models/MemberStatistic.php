<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberStatistic extends Model
{
    protected $table = 'member_statistics';

    protected $fillable = [
        'member_club_id',
        'events_attended',
        'training_sessions',
        'matches_played',
        'goals_scored',
    ];

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

    /**
     * Increment goals scored by 1 or custom amount
     */
    public function incrementGoalsScored(int $amount = 1)
    {
        $this->goals_scored += $amount;
        $this->save();
    }
}
