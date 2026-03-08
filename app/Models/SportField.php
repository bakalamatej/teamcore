<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SportField extends Model
{
    use SoftDeletes;

    protected $table = 'sport_fields';
    protected $primaryKey = 'sport_field_id';

    protected $fillable = [
        'address_id',
        'name',
        'field_type_id',
    ];
    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeByFieldType($query, $fieldTypeId)
    {
        if (!$fieldTypeId) return $query;
        
        return $query->where('field_type_id', $fieldTypeId);
    }

    public function scopeByAddress($query, $addressId)
    {
        if (!$addressId) return $query;
        
        return $query->where('address_id', $addressId);
    }

    public function scopeByCity($query, $city)
    {
        if (!$city) return $query;
        
        return $query->whereHas('address', function($q) use ($city) {
            $q->where('city', 'like', "%{$city}%");
        });
    }

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeWithAddress($query)
    {
        return $query->with('address');
    }

    // -----------------------
    // Relationships
    // -----------------------

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function fieldType()
    {
        return $this->belongsTo(FieldType::class, 'field_type_id');
    }

    public function sports()
    {
        return $this->belongsToMany(Sport::class, 'sport_fields_sports', 'sport_field_id', 'sport_id')
                    ->using(SportFieldSport::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'sport_field_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'sport_field_id');
    }
}
