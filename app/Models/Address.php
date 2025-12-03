<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;
    
    protected $table = 'addresses';

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
}
