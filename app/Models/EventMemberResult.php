<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMemberResult extends Model
{
    protected $table = 'event_member_results';

    protected $fillable = [
        'event_id',
        'member_id',
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
     * Belongs to a specific member
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
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
