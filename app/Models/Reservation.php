<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

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
    // Status constants
    // -----------------------
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELED = 'canceled';

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        
        return $query->where('title', 'like', "%{$search}%")
                     ->orWhere('description', 'like', "%{$search}%");
    }

    public function scopeByStatus($query, $status)
    {
        if (!$status) return $query;
        
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeBySportField($query, $sportFieldId)
    {
        if (!$sportFieldId) return $query;
        
        return $query->where('sport_field_id', $sportFieldId);
    }

    public function scopeByClub($query, $clubId)
    {
        if (!$clubId) return $query;
        
        return $query->where('club_id', $clubId);
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

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeOrderByDate($query, $order = 'asc')
    {
        return $query->orderBy('start_date', in_array($order, ['asc', 'desc']) ? $order : 'asc');
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['sportField', 'club', 'createdByMemberClub']);
    }

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
