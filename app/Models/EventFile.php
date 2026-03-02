<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventFile extends Model
{
    protected $table = 'event_files';

    protected $fillable = [
        'event_id',
        'file_id',
        'file_category',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Get the event associated with this file relationship.
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the file associated with this relationship.
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
