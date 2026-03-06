<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldType extends Model
{
    protected $table = 'field_types';
    protected $primaryKey = 'field_type_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function sportFields()
    {
        return $this->hasMany(SportField::class, 'field_type_id');
    }
}
