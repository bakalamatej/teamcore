<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubStatistic extends Model
{
    protected $table = 'club_statistics';
    protected $primaryKey = 'stat_id';

    protected $fillable = [
        'club_id',
        'active_members',
        'total_coaches',
        'total_events',
        'total_wins',
        'total_loses',
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeByClub($query, $clubId)
    {
        if (!$clubId) return $query;
        
        return $query->where('club_id', $clubId);
    }

    public function scopeTopClubs($query, $limit = 10)
    {
        return $query->orderBy('total_wins', 'desc')
                     ->orderBy('total_events', 'desc')
                     ->limit($limit);
    }

    public function scopeByMinActiveMembers($query, $minMembers)
    {
        if (!$minMembers) return $query;
        
        return $query->where('active_members', '>=', $minMembers);
    }

    public function scopeByMinEvents($query, $minEvents)
    {
        if (!$minEvents) return $query;
        
        return $query->where('total_events', '>=', $minEvents);
    }

    public function scopeOrderByWins($query, $order = 'desc')
    {
        return $query->orderBy('total_wins', in_array($order, ['asc', 'desc']) ? $order : 'desc');
    }

    public function scopeOrderByMembers($query, $order = 'desc')
    {
        return $query->orderBy('active_members', in_array($order, ['asc', 'desc']) ? $order : 'desc');
    }

    public function scopeWithClub($query)
    {
        return $query->with('club');
    }

    // -----------------------
    // Relationships
    // -----------------------

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}
