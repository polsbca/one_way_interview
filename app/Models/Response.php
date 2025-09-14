<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Response extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'question_id',
        'video_path',
        'text_response',
        'duration',
        'file_size',
        'attempt_number',
        'status',
        'recorded_at',
        'rating',
        'comment',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'duration' => 'integer',
        'file_size' => 'integer',
        'attempt_number' => 'integer',
        'recorded_at' => 'datetime',
        'rating' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getFileSizeFormattedAttribute()
    {
        if ($this->file_size === null) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getDurationFormattedAttribute()
    {
        if ($this->duration === null) {
            return 'N/A';
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        if ($minutes > 0) {
            return sprintf('%02d:%02d', $minutes, $seconds);
        }

        return sprintf('00:%02d', $seconds);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRecording($query)
    {
        return $query->where('status', 'recording');
    }

    public function scopeUploaded($query)
    {
        return $query->where('status', 'uploaded');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRecording()
    {
        return $this->status === 'recording';
    }

    public function isUploaded()
    {
        return $this->status === 'uploaded';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function hasVideoResponse()
    {
        return !empty($this->video_path);
    }

    public function hasTextResponse()
    {
        return !empty($this->text_response);
    }

    public function getResponseContent()
    {
        if ($this->hasVideoResponse()) {
            return $this->video_path;
        }

        if ($this->hasTextResponse()) {
            return $this->text_response;
        }

        return null;
    }

    public function markAsRecording()
    {
        $this->status = 'recording';
        $this->save();
    }

    public function markAsUploaded($videoPath = null, $duration = null, $fileSize = null)
    {
        $this->status = 'uploaded';
        $this->recorded_at = now();
        
        if ($videoPath !== null) {
            $this->video_path = $videoPath;
        }
        
        if ($duration !== null) {
            $this->duration = $duration;
        }
        
        if ($fileSize !== null) {
            $this->file_size = $fileSize;
        }
        
        $this->save();
    }

    public function markAsFailed()
    {
        $this->status = 'failed';
        $this->save();
    }

    public function addRating($rating, $comment = null, $reviewerId = null)
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }

        $this->rating = $rating;
        $this->comment = $comment;
        $this->reviewed_by = $reviewerId ?? auth()->id();
        $this->reviewed_at = now();
        $this->save();

        return $this;
    }

    public function isReviewed()
    {
        return $this->rating !== null && $this->reviewed_by !== null;
    }

    public function getResponseTypeAttribute()
    {
        return $this->hasVideoResponse() ? 'video' : 'text';
    }

    public function getResponseDataAttribute()
    {
        return $this->hasVideoResponse() ? $this->video_path : $this->text_response;
    }
}
