<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * Returns the associated member record
     */
    public function member()
    {
        return $this->hasOne(Member::class, 'user_id');
    }

    // -----------------------
    // Methods
    // -----------------------

    /**
     * Returns the active clubs of the user.
     */
    public function activeClubs()
    {
        return $this->member?->activeClubs();
    }

    /**
     * Returns the active events of the user.
     */
    public function activeEvents()
    {
        return $this->member?->activeEvents();
    }

    /**
     * Returns the full name of the user.
     */
    public function fullName()
    {
        return $this->member ? "{$this->member->first_name} {$this->member->last_name}" : null;
    }

    // -----------------------
    // Role helpers
    // -----------------------
    
    /**
     * Get user's primary role (admin takes precedence)
     */
    public function getRole(): string
    {
        if ($this->is_admin) {
            return 'admin';
        }

        if (!$this->member) {
            return 'player';
        }
        
        $role = $this->member->activeClubs()
            ->orderBy('member_club.created_at', 'asc')
            ->first()?->pivot->role ?? 'player';
        
        return $role;
    }

    public function isPlayer(): bool
    {
        return $this->getRole() === 'player';
    }

    public function isCoach(): bool
    {
        return $this->getRole() === 'coach';
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}