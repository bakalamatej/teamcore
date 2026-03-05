<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventClub extends Pivot
{

    protected $table = 'event_club';

    protected $fillable = [
        'event_id',
        'club_id',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}
