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
        'total_draws',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}
