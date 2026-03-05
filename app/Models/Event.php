<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasFiles;
use App\Models\EventFile;

class Event extends Model
{
    use SoftDeletes, HasFiles;

    protected $table = 'events';
    protected $primaryKey = 'event_id';

    protected $fillable = [
        'parent_event_id',
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
    const STATUS_CANCELLED = 'canceled';
    const STATUS_FINISHED  = 'finished';
    const STATUS_ONGOING   = 'ongoing';

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
                ->withTimestamps();
    }

    public function memberClubs()
    {
        return $this->belongsToMany(MemberClub::class, 'member_event', 'event_id', 'member_club_id')
                    ->using(MemberEvent::class)
                    ->withTimestamps();
    }

    public function eventFiles()
    {
        return $this->belongsToMany(File::class, 'event_files', 'event_id', 'file_id')
                    ->using(EventFile::class)
                    ->withPivot('file_category')
                    ->withTimestamps();
    }

    public function eventStatistic()
    {
        return $this->hasOne(EventStatistic::class, 'event_id');
    }

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_event', 'event_id', 'reservation_id')
                    ->using(ReservationEvent::class)
                    ->withTimestamps();
    }

    public function parentEvent()
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function childEvents()
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    /**
     * Returns active member clubs (members attending this event)
     */
    public function activeMemberClubs()
    {
        return $this->memberClubs();
    }

    /**
     * Returns active clubs
     */
    public function activeClubs()
    {
        return $this->clubs();
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
     * Event soft deletes are handled automatically
     */
    protected static function booted()
    {
        // No custom cascade behavior needed for event_club (hard delete only)
    }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}