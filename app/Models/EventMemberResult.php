<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMemberResult extends Model
{

    protected $table = 'event_member_results';
    protected $primaryKey = 'result_id';

    protected $fillable = [
        'event_id',
        'member_club_id',
        'score',
        'ranking',
        'note',
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeByEvent($query, $eventId)
    {
        if (!$eventId) return $query;
        
        return $query->where('event_id', $eventId);
    }

    public function scopeByMemberClub($query, $memberClubId)
    {
        if (!$memberClubId) return $query;
        
        return $query->where('member_club_id', $memberClubId);
    }

    public function scopeOrderByRanking($query, $order = 'asc')
    {
        return $query->orderBy('ranking', in_array($order, ['asc', 'desc']) ? $order : 'asc');
    }

    public function scopeOrderByScore($query, $order = 'desc')
    {
        return $query->orderBy('score', in_array($order, ['asc', 'desc']) ? $order : 'desc');
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['event', 'memberClub']);
    }

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Belongs to a specific event
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Belongs to a specific member club
     */
    public function memberClub()
    {
        return $this->belongsTo(MemberClub::class, 'member_club_id');
    }

    /**
     * Get member through member club
     */
    public function member()
    {
        return $this->memberClub->member();
    }

    // -----------------------
    // Helper methods
    // -----------------------

    /**
     * Update score for the member in the event
     */
    public function updateScore(int $score)
    {
        $this->score = $score;
        $this->save();
    }

    /**
     * Update ranking for the member in the event
     */
    public function updateRanking(int $ranking)
    {
        $this->ranking = $ranking;
        $this->save();
    }

    /**
     * Add or update note for the member in the event
     */
    public function updateNote(string $note)
    {
        $this->note = $note;
        $this->save();
    }
}
