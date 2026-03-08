<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password_hash',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
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
            'password_hash' => 'hashed',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    // -----------------------
    // Scopes
    // -----------------------

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('email', 'like', "%{$search}%")
              ->orWhereHas('member', function ($q) use ($search) {
                  $q->where(function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
              });
        });
    }

    public function scopeByEmail($query, $email)
    {
        if (!$email) return $query;
        
        return $query->where('email', $email);
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeRegularUsers($query)
    {
        return $query->where('is_admin', false);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    public function scopeOrderByEmail($query, $order = 'asc')
    {
        return $query->orderBy('email', in_array($order, ['asc', 'desc']) ? $order : 'asc');
    }

    public function scopeWithMember($query)
    {
        return $query->with('member');
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

    /**
     * Get files uploaded by this user
     */
    public function uploadedFiles()
    {
        return $this->hasMany(File::class, 'uploaded_by_user_id');
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
        if (!$this->member) return false;
        
        return $this->member->activeClubs()
            ->wherePivot('role', 'player')
            ->exists();
    }

    public function isCoach(): bool
    {
        if (!$this->member) return false;

        return $this->member->activeClubs()
            ->wherePivot('role', 'coach')
            ->exists();
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}