<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_id',
        'question_text',
        'type',
        'time_limit',
        'max_attempts',
        'order',
        'is_required',
        'instructions',
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'max_attempts' => 'integer',
        'order' => 'integer',
        'is_required' => 'boolean',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function getTimeLimitFormattedAttribute()
    {
        if ($this->time_limit < 60) {
            return $this->time_limit . ' seconds';
        }
        
        $minutes = floor($this->time_limit / 60);
        $seconds = $this->time_limit % 60;
        
        if ($seconds > 0) {
            return $minutes . ' min ' . $seconds . ' sec';
        }
        
        return $minutes . ' minutes';
    }

    public function scopeVideoQuestions($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeTextQuestions($query)
    {
        return $query->where('type', 'text');
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    public function isVideoQuestion()
    {
        return $this->type === 'video';
    }

    public function isTextQuestion()
    {
        return $this->type === 'text';
    }

    public function getRemainingAttempts($applicationId)
    {
        $usedAttempts = $this->responses()
            ->where('application_id', $applicationId)
            ->count();
        
        return max(0, $this->max_attempts - $usedAttempts);
    }

    public function canAttempt($applicationId)
    {
        return $this->getRemainingAttempts($applicationId) > 0;
    }
}
