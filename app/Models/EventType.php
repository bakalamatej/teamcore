<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $table = 'event_types';
    protected $primaryKey = 'event_type_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'sport_id',
    ];

    // -----------------------
    // Type constants
    // -----------------------
    const TYPE_TRAINING   = 'Training';
    const TYPE_MATCH      = 'Match';
    const TYPE_TOURNAMENT = 'Tournament';

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeBySport($query, $sportId)
    {
        if (!$sportId) return $query;
        
        return $query->where('sport_id', $sportId);
    }

    public function scopeOrderByName($query, $order = 'asc')
    {
        return $query->orderBy('name', in_array($order, ['asc', 'desc']) ? $order : 'asc');
    }

    // -----------------------
    // Relationships
    // -----------------------
    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'event_type_id');
    }
}
