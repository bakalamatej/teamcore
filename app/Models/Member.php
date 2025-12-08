<?php

namespace App\Models;

use App\Models\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;

class Member extends Model
{
    use SoftDeletes, HasFiles;

    protected $table = 'members';

    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'email',
        'phone',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function clubMemberships()
    {
        return $this->hasMany(MemberClub::class, 'member_id');
    }

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'member_club', 'member_id', 'club_id')
                    ->withTimestamps()
                    ->withPivot('deleted_at');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'member_event', 'member_id', 'event_id')
                    ->using(MemberEvent::class)
                    ->withTimestamps()
                    ->withPivot('deleted_at');
    }

    public function myEvents()
    {
        $clubIds = $this->activeClubs()->get()->pluck('id');

        return Event::whereHas('members', function($q) {
                    $q->where('member_id', $this->id)
                    ->whereNull('member_event.deleted_at');
                })
                ->orWhereHas('clubs', function($q) use ($clubIds) {
                    $q->whereIn('clubs.id', $clubIds)
                    ->whereNull('event_club.deleted_at');
                })
                ->latest();
    }


    // -----------------------
    // Methods
    // -----------------------

    /**
     * Returns full name
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->surname}";
    }

    /**
     * Returns active clubs
     */
    public function activeClubs()
    {
        return $this->belongsToMany(Club::class, 'member_club', 'member_id', 'club_id')
                    ->withPivot('deleted_at', 'left_at')
                    ->wherePivotNull('deleted_at')
                    ->wherePivotNull('left_at');
    }

    /**
     * Returns active events
     */
    public function activeEvents()
    {
        return $this->events()->wherePivotNull('deleted_at');
    }

    /**
     * Validation rules
     */
    public function validate(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:30',
            'surname' => 'required|string|max:30',
            'email' => 'required|email|max:56',
            'phone' => 'nullable|string|max:20',
        ]);
    }

    /**
     * Soft delete member and his memberships in clubs
     */
    protected static function booted()
    {
        static::deleting(function ($member) {
            if ($member->isForceDeleting()) return;

            $member->clubs()->updateExistingPivot(
                $member->clubs->pluck('id')->toArray(),
                ['deleted_at' => now()]
            );
        });
    }
}
