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
        'matches_played',
        'tournaments_attended',
        'total_wins',
        'total_losses',
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;

        return $query->whereHas('club', fn($q) => $q->where('name', 'like', "%{$search}%"));
    }

    public function scopeByClub($query, $clubId)
    {
        if (!$clubId) return $query;
        
        return $query->where('club_id', $clubId);
    }

    public function scopeTopClubs($query, $limit = 10)
    {
        return $query->orderBy('total_wins', 'desc')
                     ->orderBy('matches_played', 'desc')
                     ->limit($limit);
    }

    public function scopeByMinActiveMembers($query, $minMembers)
    {
        if (!$minMembers) return $query;

        return $query->where('active_members', '>=', $minMembers);
    }

    public function scopeByMinMatches($query, $minMatches)
    {
        if (!$minMatches) return $query;

        return $query->where('matches_played', '>=', $minMatches);
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
