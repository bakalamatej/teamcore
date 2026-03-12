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

    // -----------------------
    // Relationships
    // -----------------------

    public function sportFields()
    {
        return $this->hasMany(SportField::class, 'field_type_id');
    }
}
