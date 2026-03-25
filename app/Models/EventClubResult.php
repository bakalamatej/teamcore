<?php

namespace App\Models;

use App\Enums\ResultType;
use Illuminate\Database\Eloquent\Model;

class EventClubResult extends Model
{

    protected $table = 'event_club_results';
    protected $primaryKey = 'result_id';

    protected $fillable = [
        'event_id',
        'club_id',
        'value',
        'result_type',
        'ranking',
        'note',
    ];

    protected $casts = [
        'result_type' => ResultType::class,
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;

        return $query->where(fn($q) =>
            $q->whereHas('event', fn($q) => $q->where('title', 'like', "%{$search}%"))
              ->orWhereHas('club', fn($q) => $q->where('name', 'like', "%{$search}%"))
        );
    }

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

    public function scopeOrderByValue($query, $order = 'desc')
    {
        return $query->orderBy('value', in_array($order, ['asc', 'desc']) ? $order : 'desc');
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
     * Update value for the club in the event
     */
    public function updateValue(string|int|float $value)
    {
        $this->value = $value;
        $this->save();
    }

    /**
     * Update result type for the club in the event
     */
    public function updateResultType(ResultType $resultType)
    {
        $this->result_type = $resultType;
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
