<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use App\Models\Review;
use App\Models\Notification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('recruiter');
    }

    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'total_jobs' => $user->jobs()->count(),
            'active_jobs' => $user->jobs()->where('status', 'published')->count(),
            'total_applications' => $user->jobs()->withCount('applications')->get()->sum('applications_count'),
            'pending_reviews' => $user->jobs()
                ->whereHas('applications', function($query) {
                    $query->where('status', 'completed');
                })
                ->whereDoesntHave('applications.reviews', function($query) use ($user) {
                    $query->where('reviewer_id', $user->id);
                })
                ->count(),
        ];

        $recentJobs = $user->jobs()
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentApplications = Application::whereHas('job', function($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->with(['job', 'candidate'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $pendingReviews = Application::whereHas('job', function($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->where('status', 'completed')
            ->whereDoesntHave('reviews', function($query) use ($user) {
                $query->where('reviewer_id', $user->id);
            })
            ->with(['job', 'candidate'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $notifications = $user->notifications()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('recruiter.dashboard', compact('stats', 'recentJobs', 'recentApplications', 'pendingReviews', 'notifications'));
    }
}
