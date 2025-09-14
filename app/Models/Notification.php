<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }

    public function markAsUnread()
    {
        $this->is_read = false;
        $this->read_at = null;
        $this->save();
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'job_created' => 'New Job Created',
            'job_assigned' => 'Job Assigned',
            'application_submitted' => 'Application Submitted',
            'interview_completed' => 'Interview Completed',
            'review_completed' => 'Review Completed',
            'deadline_reminder' => 'Deadline Reminder',
            'status_update' => 'Status Update',
            default => 'Notification',
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'job_created' => 'briefcase',
            'job_assigned' => 'assignment',
            'application_submitted' => 'person_add',
            'interview_completed' => 'video_camera',
            'review_completed' => 'rate_review',
            'deadline_reminder' => 'schedule',
            'status_update' => 'info',
            default => 'notifications',
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'job_created' => 'primary',
            'job_assigned' => 'info',
            'application_submitted' => 'success',
            'interview_completed' => 'warning',
            'review_completed' => 'secondary',
            'deadline_reminder' => 'danger',
            'status_update' => 'info',
            default => 'dark',
        };
    }

    public static function createNotification($userId, $type, $title, $message, $data = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    public static function notifyJobAssigned($userId, $jobTitle)
    {
        return self::createNotification(
            $userId,
            'job_assigned',
            'New Job Assigned',
            "You have been assigned to review applications for: {$jobTitle}",
            ['job_title' => $jobTitle]
        );
    }

    public static function notifyApplicationSubmitted($userId, $candidateName, $jobTitle)
    {
        return self::createNotification(
            $userId,
            'application_submitted',
            'New Application Submitted',
            "{$candidateName} has submitted an application for: {$jobTitle}",
            [
                'candidate_name' => $candidateName,
                'job_title' => $jobTitle,
            ]
        );
    }

    public static function notifyInterviewCompleted($userId, $candidateName, $jobTitle)
    {
        return self::createNotification(
            $userId,
            'interview_completed',
            'Interview Completed',
            "{$candidateName} has completed the interview for: {$jobTitle}",
            [
                'candidate_name' => $candidateName,
                'job_title' => $jobTitle,
            ]
        );
    }

    public static function notifyReviewCompleted($userId, $jobTitle, $decision)
    {
        return self::createNotification(
            $userId,
            'review_completed',
            'Review Completed',
            "Your application for {$jobTitle} has been reviewed. Decision: {$decision}",
            [
                'job_title' => $jobTitle,
                'decision' => $decision,
            ]
        );
    }

    public static function notifyDeadlineReminder($userId, $jobTitle, $daysLeft)
    {
        return self::createNotification(
            $userId,
            'deadline_reminder',
            'Deadline Reminder',
            "Job deadline for {$jobTitle} is in {$daysLeft} days",
            [
                'job_title' => $jobTitle,
                'days_left' => $daysLeft,
            ]
        );
    }
}
