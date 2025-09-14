<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $stats = [
            'total_jobs' => Job::count(),
            'active_jobs' => Job::active()->count(),
            'total_applications' => Application::count(),
            'pending_reviews' => Application::completed()->count(),
            'total_users' => User::count(),
            'candidates' => User::where('role', 'candidate')->count(),
            'recruiters' => User::where('role', 'recruiter')->count(),
        ];

        $recentJobs = Job::with('creator')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentApplications = Application::with(['job', 'candidate'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentJobs', 'recentApplications'));
    }
}
