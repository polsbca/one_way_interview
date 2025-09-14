<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_id',
        'reviewer_id',
        'overall_rating',
        'comments',
        'feedback',
        'decision',
        'reviewed_at',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function getRatingStarsAttribute()
    {
        if ($this->overall_rating === null) {
            return '';
        }

        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->overall_rating) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }

        return $stars;
    }

    public function getDecisionLabelAttribute()
    {
        return match($this->decision) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'hold' => 'On Hold',
            default => 'Unknown',
        };
    }

    public function getDecisionColorAttribute()
    {
        return match($this->decision) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'hold' => 'info',
            default => 'secondary',
        };
    }

    public function scopePending($query)
    {
        return $query->where('decision', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('decision', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('decision', 'rejected');
    }

    public function scopeOnHold($query)
    {
        return $query->where('decision', 'hold');
    }

    public function isPending()
    {
        return $this->decision === 'pending';
    }

    public function isApproved()
    {
        return $this->decision === 'approved';
    }

    public function isRejected()
    {
        return $this->decision === 'rejected';
    }

    public function isOnHold()
    {
        return $this->decision === 'hold';
    }

    public function markAsReviewed()
    {
        $this->reviewed_at = now();
        $this->save();
    }

    public function approve()
    {
        $this->decision = 'approved';
        $this->markAsReviewed();
    }

    public function reject()
    {
        $this->decision = 'rejected';
        $this->markAsReviewed();
    }

    public function putOnHold()
    {
        $this->decision = 'hold';
        $this->markAsReviewed();
    }
}
