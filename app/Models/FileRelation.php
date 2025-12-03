<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileRelation extends Model
{
    protected $table = 'file_relations';

    protected $fillable = [
        'file_id',
        'fileable_type',
        'fileable_id',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Returns the file associated with this relation.
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    /**
     * Polymorphic relationship: returns the model to which the file is attached.
     */
    public function fileable()
    {
        return $this->morphTo();
    }
}
