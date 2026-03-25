<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ReservationStatus;

class Reservation extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Reservation $reservation): void {
            $reservation->status ??= ReservationStatus::APPROVED;
        });
    }

    protected $table = 'reservations';
    protected $primaryKey = 'reservation_id';

    protected $fillable = [
        'sport_field_id',
        'created_by_member_club_id',
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'status' => ReservationStatus::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    public function scopeByStatus($query, $status)
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ReservationStatus::APPROVED->value);
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', ReservationStatus::CANCELED->value);
    }

    public function scopeConverted($query)
    {
        return $query->where('status', ReservationStatus::CONVERTED->value);
    }

    public function scopeBySportField($query, $sportFieldId)
    {
        if (!$sportFieldId) {
            return $query;
        }

        return $query->where('sport_field_id', $sportFieldId);
    }

    public function scopeByClub($query, $clubId)
    {
        if (!$clubId) {
            return $query;
        }

        return $query->whereHas('createdByMemberClub', function ($q) use ($clubId) {
            $q->where('club_id', $clubId);
        });
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->whereDate('start_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('end_date', '<=', $endDate);
        }

        return $query;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeWithRelations($query)
    {
        return $query->with([
            'sportField',
            'createdByMemberClub.club',
            'createdByMemberClub.club.sport',
        ]);
    }

    // -----------------------
    // Relationships
    // -----------------------

    public function sportField()
    {
        return $this->belongsTo(SportField::class, 'sport_field_id');
    }

    public function createdByMemberClub()
    {
        return $this->belongsTo(MemberClub::class, 'created_by_member_club_id', 'member_club_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'reservation_id');
    }

    // -----------------------
    // Accessors
    // -----------------------

    public function getSportAttribute()
    {
        return $this->createdByMemberClub?->club?->sport;
    }

    public function getClubAttribute()
    {
        return $this->createdByMemberClub?->club;
    }

    public function getLocationAttribute()
    {
        return $this->sportField?->name ?? 'N/A';
    }
}