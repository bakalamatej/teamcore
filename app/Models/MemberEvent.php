<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberEvent extends Pivot
{
    use SoftDeletes;

    protected $table = 'member_event';

    protected $fillable = [
        'member_id',
        'event_id',
    ];

    protected $dates = ['deleted_at'];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
