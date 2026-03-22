<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachEvaluation extends Model
{
    protected $table = 'coach_evaluation';
    protected $primaryKey = 'evaluation_id';

    protected $fillable = [
        'coach_member_club_id',
        'evaluated_by_member_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];

    // -----------------------
    // Rating constants (1-5 scale)
    // -----------------------
    const RATING_POOR = 1;
    const RATING_FAIR = 2;
    const RATING_GOOD = 3;
    const RATING_VERY_GOOD = 4;
    const RATING_EXCELLENT = 5;

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;

        return $query->whereHas('coach.member', fn($q) => $q->where('first_name', 'like', "%{$search}%")
            ->orWhere('last_name', 'like', "%{$search}%"));
    }

    public function scopeSearchByEvaluator($query, $search)
    {
        if (!$search) return $query;
        return $query->where(function($q) use ($search) {
            $q->whereHas('evaluatedByMember', fn($q) => $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%"))
            ->orWhere('comment', 'like', "%{$search}%");
        });
    }

    public function scopeByCoach($query, $coachId)
    {
        if (!$coachId) return $query;
        
        return $query->where('coach_member_club_id', $coachId);
    }

    public function scopeByMember($query, $memberId)
    {
        if (!$memberId) return $query;

        return $query->where('evaluated_by_member_id', $memberId);
    }

    public function scopeByRating($query, $rating)
    {
        if (!$rating) return $query;
        
        return $query->where('rating', $rating);
    }

    public function scopeByRatingRange($query, $minRating, $maxRating = 5)
    {
        return $query->whereBetween('rating', [$minRating, $maxRating]);
    }

    public function scopeMinRating($query, $minRating)
    {
        if (!$minRating) return $query;
        
        return $query->where('rating', '>=', $minRating);
    }



    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeOrderByRating($query, $order = 'desc')
    {
        return $query->orderBy('rating', in_array($order, ['asc', 'desc']) ? $order : 'desc');
    }

    public function scopeOrderByDate($query, $order = 'desc')
    {
        return $query->orderBy('created_at', in_array($order, ['asc', 'desc']) ? $order : 'desc');
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['coach', 'evaluatedByMember', 'reservation']);
    }

    // -----------------------
    // Relationships
    // -----------------------

    public function coach()
    {
        return $this->belongsTo(MemberClub::class, 'coach_member_club_id');
    }

    public function evaluatedByMember()
    {
        return $this->belongsTo(Member::class, 'evaluated_by_member_id');
    }


}
