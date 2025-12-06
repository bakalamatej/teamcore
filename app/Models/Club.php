<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasFiles;

class Club extends Model
{
    use SoftDeletes, HasFiles;

    protected $table = 'clubs';

    protected $fillable = [
        'address_id',
        'name',
        'phone',
        'email',
        'webpage',
    ];

    // -----------------------
    // Relationships
    // -----------------------
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function memberMemberships()
    {
        return $this->hasMany(MemberClub::class, 'club_id');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'member_club', 'club_id', 'member_id')
                    ->withTimestamps()
                    ->withPivot('deleted_at', 'left_at');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_club', 'club_id', 'event_id')
                    ->withTimestamps();
    }

    // -----------------------
    // Methods
    // -----------------------

    /**
     * Returns active events
     */
    public function activeEvents()
    {
        return $this->events()->wherePivotNull('deleted_at');
    }

    /**
     * Returns active members
     */
    public function activeMembers()
    {
        return $this->members()->wherePivotNull('deleted_at')->wherePivotNull('left_at');
    }

    /**
     * Validation rules
     */
    protected function validationRules(): array
    {
        return [
            'name' => 'required|string|max:30|unique:clubs,name',
            'phone' => 'required|string|max:20|unique:clubs,phone',
            'email' => 'required|email|max:56|unique:clubs,email',
            'webpage' => 'nullable|url|max:255',
            'address_id' => 'nullable|exists:addresses,id',
        ];
    }

    /**
     * Soft delete records of club
     */
    protected static function booted()
    {
        static::deleting(function ($club) {
            if ($club->isForceDeleting()) return;

            // Soft delete events
            $club->events()->updateExistingPivot(
                $club->events->pluck('id')->toArray(),
                ['deleted_at' => now()]
            );

            // Soft delete memberships
            $club->members()->updateExistingPivot(
                $club->members->pluck('id')->toArray(),
                ['deleted_at' => now()]
            );
        });
    }

}
