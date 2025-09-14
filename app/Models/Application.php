<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_id',
        'candidate_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'resume_path',
        'cover_letter',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function candidate()
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getProgressPercentageAttribute()
    {
        $totalQuestions = $this->job->questions()->count();
        if ($totalQuestions === 0) {
            return 0;
        }

        $answeredQuestions = $this->responses()->where('status', 'uploaded')->count();
        
        return round(($answeredQuestions / $totalQuestions) * 100);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'secondary',
            'in_progress' => 'info',
            'completed' => 'success',
            'approved' => 'success',
            'rejected' => 'danger',
            'on_hold' => 'warning',
            default => 'secondary',
        };
    }

    public function getProgressColorAttribute()
    {
        $percentage = $this->progress_percentage;
        
        if ($percentage >= 100) {
            return 'success';
        } elseif ($percentage >= 50) {
            return 'info';
        } elseif ($percentage >= 25) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public function getCompletedResponsesAttribute()
    {
        return $this->responses()->where('status', 'submitted')->count();
    }

    public function getRemainingQuestionsAttribute()
    {
        $totalQuestions = $this->job->questions()->count();
        $answeredQuestions = $this->responses()->where('status', 'uploaded')->count();
        
        return max(0, $totalQuestions - $answeredQuestions);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isReviewed()
    {
        return $this->status === 'reviewed';
    }

    public function canStartInterview()
    {
        return $this->isPending() && $this->job->canApply();
    }

    public function canContinueInterview()
    {
        return $this->isInProgress() && !$this->job->isExpired();
    }

    public function startInterview()
    {
        if (!$this->canStartInterview()) {
            return false;
        }

        $this->status = 'in_progress';
        $this->started_at = now();
        $this->save();

        return true;
    }

    public function completeInterview()
    {
        if (!$this->isInProgress()) {
            return false;
        }

        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();

        return true;
    }

    public function hasAnsweredAllQuestions()
    {
        $totalQuestions = $this->job->questions()->count();
        $answeredQuestions = $this->responses()->where('status', 'uploaded')->count();
        
        return $totalQuestions > 0 && $answeredQuestions >= $totalQuestions;
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating');
    }
}
