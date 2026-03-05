<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $table = 'event_types';
    protected $primaryKey = 'event_type_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'sport_id',
    ];

    // -----------------------
    // Type constants
    // -----------------------
    const TYPE_TRAINING = 'training';
    const TYPE_MATCH = 'match';
    const TYPE_COMPETITION = 'competition';

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

    /**
     * Validation rules
     */
    protected function validationRules(): array
    {
        return [
            'name' => 'required|string|max:30|unique:event_types,name',
        ];
    }
}
