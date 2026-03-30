<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachEvaluation extends Model
{
    protected $table = 'coach_evaluation';
    protected $primaryKey = 'evaluation_id';

    protected $fillable = [
        'coach_member_id',
        'evaluated_by_member_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        return $query->whereHas('coach', fn($q) => 
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
        );
    }

    public function scopeSearchByEvaluator($query, $search)
    {
        if (!$search) return $query;
        return $query->where(function($q) use ($search) {
            $q->whereHas('evaluatedByMember', fn($q) => 
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
            )->orWhere('comment', 'like', "%{$search}%");
        });
    }

    public function scopeSearchByCoach($query, $search)
    {
        if (!$search) return $query;
        return $query->whereHas('coach', fn($q) => 
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
        );
    }

    public function scopeByMember($query, $memberId)
    {
        if (!$memberId) return $query;
        return $query->where('evaluated_by_member_id', $memberId);
    }

    public function scopeByCoach($query, $coachMemberId)
    {
        if (!$coachMemberId) return $query;
        return $query->where('coach_member_id', $coachMemberId);
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
        return $query->with(['coach', 'evaluatedByMember']);
    }

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Get the coach (member) who gave the evaluation
     */
    public function coach()
    {
        return $this->belongsTo(Member::class, 'coach_member_id');
    }

    /**
     * Get the member who was evaluated
     */
    public function evaluatedByMember()
    {
        return $this->belongsTo(Member::class, 'evaluated_by_member_id');
    }
}