<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SportField extends Model
{
    use SoftDeletes;

    protected $table = 'sport_fields';
    protected $primaryKey = 'sport_field_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'address_id',
        'name',
        'field_type',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function sports()
    {
        return $this->belongsToMany(Sport::class, 'sport_fields_sports', 'sport_field_id', 'sport_id')
                    ->using(SportFieldSport::class)
                    ->withTimestamps();
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'sport_field_id');
    }
}
