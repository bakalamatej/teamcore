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

    public $timestamps = false;

    // -----------------------
    // Type constants
    // -----------------------
    const TYPE_TRAINING = 'training';
    const TYPE_MATCH = 'match';
    const TYPE_COMPETITION = 'competition';

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
