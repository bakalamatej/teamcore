<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventClubResult extends Model
{
    use SoftDeletes;

    protected $table = 'event_club_results';

    protected $fillable = [
        'event_id',
        'club_id',
        'score',
        'ranking',
        'note',
    ];

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
