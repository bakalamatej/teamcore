<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventClub extends Pivot
{
    use SoftDeletes;

    protected $table = 'event_club';

    protected $fillable = [
        'event_id',
        'club_id',
    ];

    protected $dates = ['deleted_at'];

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
