<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EventFile;
use App\Enums\EventStatus;

class Event extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Event $event): void {
            $event->status ??= EventStatus::SCHEDULED;
        });
    }

    protected $table = 'events';
    protected $primaryKey = 'event_id';

    protected $fillable = [
        'sport_id',
        'parent_event_id',
        'reservation_id',
        'sport_field_id',
        'event_type_id',
        'title',
        'description',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'status' => EventStatus::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

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

    public function scopeScheduled($query)
    {
        return $query->where('status', EventStatus::SCHEDULED->value);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', EventStatus::ONGOING->value);
    }

    public function scopeFinished($query)
    {
        return $query->where('status', EventStatus::FINISHED->value);
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', EventStatus::CANCELED->value);
    }

    public function scopeByEventType($query, $eventTypeId)
    {
        if (!$eventTypeId) return $query;
        
        return $query->where('event_type_id', $eventTypeId);
    }

    public function scopeBySportField($query, $sportFieldId)
    {
        if (!$sportFieldId) return $query;
        
        return $query->where('sport_field_id', $sportFieldId);
    }

    public function scopeByClub($query, $clubId)
    {
        if (!$clubId) return $query;
        
        return $query->whereHas('clubs', function($q) use ($clubId) {
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

    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['sportField', 'eventType', 'clubs']);
    }

    public function scopeOrderByDate($query, $order = 'asc')
    {
        return $query->orderBy('start_date', in_array($order, ['asc', 'desc']) ? $order : 'asc');
    }

    // -----------------------
    // Relationships
    // -----------------------

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

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
        return $this->belongsToMany(MemberClub::class, 'event_member', 'event_id', 'member_club_id')
                    ->using(MemberEvent::class)
                    ->withTimestamps();
    }

    public function eventFiles()
    {
        return $this->belongsToMany(File::class, 'event_files', 'event_id', 'file_id')
                    ->using(EventFile::class)
                    ->withPivot('file_category_id')
                    ->withTimestamps();
    }

    public function eventStatistic()
    {
        return $this->hasOne(EventStatistic::class, 'event_id');
    }

    public function eventMemberResults()
    {
        return $this->hasMany(EventMemberResult::class, 'event_id');
    }

    public function eventClubResults()
    {
        return $this->hasMany(EventClubResult::class, 'event_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function parentEvent()
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function childEvents()
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    public function getActiveClubsAttribute()
    {
        return $this->clubs;
    }

    public function getActiveMembersAttribute()
    {
        return $this->memberClubs->map(fn($mc) => $mc->member)->filter()->values();
    }
    
    public function getLocationAttribute()
    {
        return $this->sportField?->name ?? 'N/A';
    }
}
