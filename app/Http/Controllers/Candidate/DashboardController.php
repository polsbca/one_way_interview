<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use App\Models\Notification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('candidate');
    }

    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'total_applications' => $user->applications()->count(),
            'completed_interviews' => $user->applications()->where('status', 'completed')->count(),
            'pending_reviews' => $user->applications()->where('status', 'pending_review')->count(),
            'in_progress' => $user->applications()->where('status', 'in_progress')->count(),
        ];

        $recentApplications = $user->applications()
            ->with('job')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $availableJobs = Job::published()
            ->where('deadline', '>', now())
            ->whereDoesntHave('applications', function($query) use ($user) {
                $query->where('candidate_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $notifications = $user->notifications()
            ->unread()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('candidate.dashboard', compact('stats', 'recentApplications', 'availableJobs', 'notifications'));
    }
}
