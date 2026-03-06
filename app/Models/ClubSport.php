<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClubSport extends Pivot
{
    protected $table = 'club_sport';
    public $timestamps = false;

    protected $fillable = [
        'club_id',
        'sport_id',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }
}
