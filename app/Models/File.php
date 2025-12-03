<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    use SoftDeletes;

    protected $table = 'files';

    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Polymorphic relation to owner models (Event, Member, etc.)
     */
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    // -----------------------
    // Methods
    // -----------------------
}
