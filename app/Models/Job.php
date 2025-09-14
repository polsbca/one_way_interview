<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'company',
        'location',
        'department',
        'salary_min',
        'salary_max',
        'status',
        'deadline',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function publishedApplications()
    {
        return $this->applications()->where('status', '!=', 'draft');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->published()->where(function ($q) {
            $q->whereNull('deadline')->orWhere('deadline', '>', now());
        });
    }

    public function getSalaryRangeAttribute()
    {
        if ($this->salary_min && $this->salary_max) {
            return '$' . number_format($this->salary_min) . ' - $' . number_format($this->salary_max);
        }
        
        if ($this->salary_min) {
            return 'From $' . number_format($this->salary_min);
        }
        
        if ($this->salary_max) {
            return 'Up to $' . number_format($this->salary_max);
        }
        
        return 'Competitive';
    }

    public function getDaysLeftAttribute()
    {
        if (!$this->deadline) {
            return null;
        }
        
        return max(0, now()->diffInDays($this->deadline, false));
    }

    public function isExpired()
    {
        return $this->deadline && $this->deadline < now();
    }

    public function canApply()
    {
        return $this->status === 'published' && !$this->isExpired();
    }
}
