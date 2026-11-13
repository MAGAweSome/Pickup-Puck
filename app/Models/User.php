<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Rawilk\Settings\Models\HasSettings;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasSettings, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'completed_onboarding',
        'level',
        'mobility',
        'role_preference',
        'jersey_number',
        'manager_id',
        'notify_team_assignment',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'completed_onboarding' => 'boolean',
        'level' => 'integer',
        'mobility' => 'integer',
        'jersey_number' => 'integer',
        'manager_id' => 'integer',
        'notify_team_assignment' => 'boolean',
    ];

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    /**
     * Connected profiles managed by this account.
     */
    public function managedProfiles()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /**
     * The manager account that controls this profile (if managed).
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Check if this user is a managed/dependent profile.
     */
    public function isManaged(): bool
    {
        return !is_null($this->manager_id);
    }

    /**
     * Check if this user is allowed to control other profiles.
     * Rule: You cannot control another account if you are already a managed account!
     */
    public function canManageOthers(): bool
    {
        return is_null($this->manager_id);
    }

    public function isAdministrator()
   {
       return $this->hasRole('1');
   } 
}
