<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;
    
    protected $table = 'addresses';
    protected $primaryKey = 'address_id';

    protected $fillable = [
        'country',
        'city',
        'street',
        'zip_code',
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeByCity($query, $city)
    {
        if (!$city) return $query;
        
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeByCountry($query, $country)
    {
        if (!$country) return $query;
        
        return $query->where('country', 'like', "%{$country}%");
    }

    public function scopeByZipCode($query, $zipCode)
    {
        if (!$zipCode) return $query;
        
        return $query->where('zip_code', $zipCode);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where('city', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%");
    }

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
