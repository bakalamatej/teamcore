<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasFiles;
use App\Models\ClubFile;

class Club extends Model
{
    use SoftDeletes, HasFiles;

    protected $table = 'clubs';
    protected $primaryKey = 'club_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'address_id',
        'sport_id',
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

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
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

    public function clubFiles()
    {
        return $this->belongsToMany(File::class, 'club_files', 'club_id', 'file_id')
                    ->using(ClubFile::class)
                    ->withPivot('file_category')
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
