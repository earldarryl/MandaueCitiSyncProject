<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\CustomResetPasswordNotification;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;
    use TwoFactorAuthenticatable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    // Use this for form dropdowns or role checks
    protected $fillable = [
        'name',
        'email',
        'profile_pic',
        'password',
        'contact',
        'agreed_terms',
        'terms_version',
        'agreed_at',
        'last_seen_at',
        'first_online_at',
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
            'agreed_terms' => 'boolean',
            'agreed_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'first_online_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function info()
    {
        return $this->hasOne(UserInfo::class);
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }

    public function getStatusAttribute(): string
    {
        if (!$this->last_seen_at) {
            return 'offline';
        }

        return $this->isOnline() ? 'online' : 'away';
    }

    public function markOffline(): void
    {
        $this->forceFill([
            'last_seen_at' => null,
            'first_online_at' => null
        ])->saveQuietly();
    }

    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'hr_liaison_departments',
            'hr_liaison_id',
            'department_id',
            'id',
            'department_id'
        );
    }

    public function getProfilePicAttribute($value)
    {
        return $value ?: null;
    }

    public function grievances()
    {
        return $this->hasMany(Grievance::class, 'user_id');
    }

    public function userInfo()
    {
        return $this->hasOne(UserInfo::class, 'user_id');
    }

    public function getIsDeactivatedAttribute(): bool
    {
        return !is_null($this->deleted_at);
    }
}
