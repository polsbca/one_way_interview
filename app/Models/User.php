<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
        'phone',
        'avatar',
        'bio',
        'is_active',
        'last_login_at',
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
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is recruiter
     */
    public function isRecruiter(): bool
    {
        return $this->role === 'recruiter';
    }

    /**
     * Check if user is candidate
     */
    public function isCandidate(): bool
    {
        return $this->role === 'candidate';
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get jobs created by this user (admin/recruiter)
     */
    public function createdJobs()
    {
        return $this->hasMany(Job::class, 'created_by');
    }

    /**
     * Get applications by this user (candidate)
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'candidate_id');
    }

    /**
     * Get reviews by this user (recruiter)
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Get notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Get unread notifications for this user
     */
    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    /**
     * Get read notifications for this user
     */
    public function readNotifications()
    {
        return $this->notifications()->read();
    }

    /**
     * Check if user has unread notifications
     */
    public function hasUnreadNotifications(): bool
    {
        return $this->unreadNotifications()->exists();
    }

    /**
     * Get count of unread notifications
     */
    public function unreadNotificationsCount(): int
    {
        return $this->unreadNotifications()->count();
    }
}
