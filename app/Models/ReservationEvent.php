<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationEvent extends Model
{
    protected $table = 'reservation_event';
    public $timestamps = true;

    protected $fillable = [
        'reservation_id',
        'event_id',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
