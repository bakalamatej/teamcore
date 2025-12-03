<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberClub extends Model
{
    use SoftDeletes;

    protected $table = 'member_club';

    protected $fillable = [
        'member_id',
        'club_id',
        'joined_at',
        'left_at',
    ];

    protected $dates = ['joined_at', 'left_at', 'deleted_at'];

    // -----------------------
    // Relationships
    // -----------------------

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}
