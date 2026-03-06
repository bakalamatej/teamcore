<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClubFile extends Pivot
{
    protected $table = 'club_files';

    protected $fillable = [
        'club_id',
        'file_id',
        'category_id',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Get the club associated with this file relationship.
     */
    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    /**
     * Get the file associated with this relationship.
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
