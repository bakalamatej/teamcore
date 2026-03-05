<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventClubResult extends Model
{

    protected $table = 'event_club_results';
    protected $primaryKey = 'result_id';

    protected $fillable = [
        'event_id',
        'club_id',
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

    public function scopeByClub($query, $clubId)
    {
        if (!$clubId) return $query;
        
        return $query->where('club_id', $clubId);
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
        return $query->with(['event', 'club']);
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
     * Belongs to a specific club
     */
    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    // -----------------------
    // Helper methods
    // -----------------------

    /**
     * Update score for the club in the event
     */
    public function updateScore(int $score)
    {
        $this->score = $score;
        $this->save();
    }

    /**
     * Update ranking for the club in the event
     */
    public function updateRanking(int $ranking)
    {
        $this->ranking = $ranking;
        $this->save();
    }

    /**
     * Add or update note for the club in the event
     */
    public function updateNote(string $note)
    {
        $this->note = $note;
        $this->save();
    }
}
