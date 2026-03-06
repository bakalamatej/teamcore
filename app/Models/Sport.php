<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClubSport;
use App\Models\SportFieldSport;

class Sport extends Model
{
    protected $table = 'sports';
    protected $primaryKey = 'sport_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeOrderByName($query, $order = 'asc')
    {
        return $query->orderBy('name', in_array($order, ['asc', 'desc']) ? $order : 'asc');
    }

    /**
     * Get the sport fields that have this sport.
     */
    public function sportFields()
    {
        return $this->belongsToMany(SportField::class, 'sport_fields_sports', 'sport_id', 'sport_field_id')
                    ->using(SportFieldSport::class)
                    ->withTimestamps();
    }

    /**
     * Get the event types for this sport.
     */
    public function eventTypes()
    {
        return $this->hasMany(EventType::class);
    }

    /**
     * Get the clubs for this sport.
     */
    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_sport', 'sport_id', 'club_id')
                    ->using(ClubSport::class);
    }
}
