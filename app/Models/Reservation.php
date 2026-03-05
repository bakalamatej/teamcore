<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';
    protected $primaryKey = 'reservation_id';

    protected $fillable = [
        'sport_field_id',
        'club_id',
        'created_by_member_club_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function sportField()
    {
        return $this->belongsTo(SportField::class, 'sport_field_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function createdByMemberClub()
    {
        return $this->belongsTo(MemberClub::class, 'created_by_member_club_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'reservation_event', 'reservation_id', 'event_id');
    }

    public function evaluations()
    {
        return $this->hasMany(CoachEvaluation::class, 'reservation_id');
    }
}
