<?php

namespace App\Models;

use App\Enums\ResultType;
use Illuminate\Database\Eloquent\Model;

class EventMemberResult extends Model
{

    protected $table = 'event_member_results';
    protected $primaryKey = 'result_id';

    protected $fillable = [
        'event_id',
        'member_club_id',
        'value',
        'result_type',
        'ranking',
        'note',
    ];

    protected $casts = [
        'result_type' => ResultType::class,
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeByEvent($query, $eventId)
    {
        if (!$eventId) return $query;
        
        return $query->where('event_id', $eventId);
    }

    public function scopeByMemberClub($query, $memberClubId)
    {
        if (!$memberClubId) return $query;
        
        return $query->where('member_club_id', $memberClubId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('event', fn($q) => $q->where('title', 'like', '%' . $search . '%'))
              ->orWhereHas('memberClub.member', fn($q) =>
                  $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
              );
        });
    }

    public function scopeOrderByRanking($query, $order = 'asc')
    {
        return $query->orderBy('ranking', in_array($order, ['asc', 'desc']) ? $order : 'asc');
    }

    public function scopeOrderByValue($query, $order = 'desc')
    {
        return $query->orderBy('value', in_array($order, ['asc', 'desc']) ? $order : 'desc');
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['event', 'memberClub']);
    }

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Belongs to a specific event
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Belongs to a specific member club
     */
    public function memberClub()
    {
        return $this->belongsTo(MemberClub::class, 'member_club_id');
    }

    /**
     * Get member through member club (eager-loadable with with('member'))
     */
    public function member()
    {
        return $this->hasOneThrough(
            Member::class,
            MemberClub::class,
            'member_club_id', // member_club.member_club_id
            'member_id',      // members.member_id
            'member_club_id', // event_member_results.member_club_id
            'member_id'       // member_club.member_id
        );
    }

    // -----------------------
    // Helper methods
    // -----------------------

    /**
     * Update value for the member in the event
     */
    public function updateValue(string|int|float $value)
    {
        $this->value = $value;
        $this->save();
    }

    /**
     * Update result type for the member in the event
     */
    public function updateResultType(ResultType $resultType)
    {
        $this->result_type = $resultType;
        $this->save();
    }

    /**
     * Update ranking for the member in the event
     */
    public function updateRanking(int $ranking)
    {
        $this->ranking = $ranking;
        $this->save();
    }

    /**
     * Add or update note for the member in the event
     */
    public function updateNote(string $note)
    {
        $this->note = $note;
        $this->save();
    }
}
