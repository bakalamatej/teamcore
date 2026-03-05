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
    // Relationships
    // -----------------------

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
