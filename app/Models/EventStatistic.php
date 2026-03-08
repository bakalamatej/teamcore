<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventStatistic extends Model
{
    protected $table = 'event_statistics';
    protected $primaryKey = 'stat_id';

    protected $fillable = [
        'event_id',
        'total_participants',
        'total_teams',
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        return $query->whereHas('event', fn($q) => $q->where('title', 'like', '%' . $search . '%'));
    }

    public function scopeByEvent($query, $eventId)
    {
        if (!$eventId) return $query;
        
        return $query->where('event_id', $eventId);
    }

    public function scopeByMinParticipants($query, $minParticipants)
    {
        if (!$minParticipants) return $query;
        
        return $query->where('total_participants', '>=', $minParticipants);
    }

    public function scopeByMinTeams($query, $minTeams)
    {
        if (!$minTeams) return $query;
        
        return $query->where('total_teams', '>=', $minTeams);
    }

    public function scopeOrderByParticipants($query, $order = 'desc')
    {
        return $query->orderBy('total_participants', in_array($order, ['asc', 'desc']) ? $order : 'desc');
    }

    public function scopeWithEvent($query)
    {
        return $query->with('event');
    }

    // -----------------------
    // Relationships
    // -----------------------

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
