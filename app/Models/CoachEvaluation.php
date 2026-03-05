<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoachEvaluation extends Model
{
    use SoftDeletes;

    protected $table = 'coach_evaluation';
    protected $primaryKey = 'evaluation_id';

    protected $fillable = [
        'coach_id',
        'evaluated_by_member_id',
        'reservation_id',
        'rating',
        'comment',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function coach()
    {
        return $this->belongsTo(Member::class, 'coach_id');
    }

    public function evaluatedByMember()
    {
        return $this->belongsTo(Member::class, 'evaluated_by_member_id');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
}
