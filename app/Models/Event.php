<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasFiles;

class Event extends Model
{
    use SoftDeletes, HasFiles;

    protected $table = 'events';

    protected $fillable = [
        'sport_field_id',
        'event_type_id',
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    // -----------------------
    // Status constants
    // -----------------------
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FINISHED  = 'finished';

    // -----------------------
    // Relationships
    // -----------------------

    public function sportField()
    {
        return $this->belongsTo(SportField::class, 'sport_field_id');
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'event_club', 'event_id', 'club_id')
                ->using(EventClub::class) 
                ->withTimestamps()
                ->withPivot('deleted_at');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'member_event', 'event_id', 'member_id')
                    ->using(MemberEvent::class)
                    ->withTimestamps()
                    ->withPivot('deleted_at');
    }

    /**
     * Returns active members
     */
    public function activeMembers()
    {
        return $this->members()->wherePivotNull('deleted_at');
    }

    /**
     * Returns active clubs
     */
    public function activeClubs()
    {
        return $this->clubs()->wherePivotNull('deleted_at');
    }

    public function allClubs()
    {
        return $this->clubs();
    }

    /**
     * Returns sport field name
     */
    public function getLocationAttribute()
    {
        return $this->sportField?->name ?? 'N/A';
    }

    /**
     * Soft delete records of event
     */
    protected static function booted()
    {
        static::deleting(function ($event) {
            if ($event->isForceDeleting()) return;

            $event->clubs()->updateExistingPivot(
                $event->clubs->pluck('id')->toArray(),
                ['deleted_at' => now()]
            );
        });
    }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}