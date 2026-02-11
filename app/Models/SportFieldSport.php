<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SportFieldSport extends Pivot
{
    protected $table = 'sport_fields_sports';

    protected $fillable = [
        'sport_field_id',
        'sport_id',
    ];

    public $timestamps = true;

    public function sportField()
    {
        return $this->belongsTo(SportField::class, 'sport_field_id');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }
}
