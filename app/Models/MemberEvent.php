<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MemberEvent extends Pivot
{
    protected $table = 'member_event';

    protected $fillable = [
        'member_club_id',
        'event_id',
    ];

    public function memberClub()
    {
        return $this->belongsTo(MemberClub::class, 'member_club_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
