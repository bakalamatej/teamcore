<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;
    
    protected $table = 'addresses';
    protected $primaryKey = 'address_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'country',
        'city',
        'street',
        'zip_code',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function clubs()
    {
        return $this->hasMany(Club::class, 'address_id');
    }

    public function sportFields()
    {
        return $this->hasMany(SportField::class, 'address_id');
    }
}
