<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    protected $table = 'event_types';

    protected $fillable = [
        'name',
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
