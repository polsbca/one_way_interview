<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use App\Models\Question;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('recruiter');
    }

    public function index()
    {
        $user = auth()->user();
        
        $jobs = $user->jobs()
            ->withCount('applications')
            ->withCount(['applications as completed_applications' => function($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('recruiter.jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        $user = auth()->user();
        
        // Check if user owns this job
        if ($job->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $job->load(['questions' => function($query) {
            $query->orderBy('order');
        }]);

        $applications = $job->applications()
            ->with(['candidate', 'reviews'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_applications' => $applications->count(),
            'completed_interviews' => $applications->where('status', 'completed')->count(),
            'pending_reviews' => $applications->where('status', 'completed')
                ->filter(function($application) use ($user) {
                    return !$application->reviews->contains('reviewer_id', $user->id);
                })->count(),
            'average_rating' => $applications->whereNotNull('reviews_avg_rating')->avg('reviews_avg_rating'),
        ];

        return view('recruiter.jobs.show', compact('job', 'applications', 'stats'));
    }

    public function applications(Job $job)
    {
        $user = auth()->user();
        
        // Check if user owns this job
        if ($job->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $applications = $job->applications()
            ->with(['candidate', 'reviews.reviewer'])
            ->withCount(['responses as completed_responses' => function($query) {
                $query->where('status', 'submitted');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('recruiter.jobs.applications', compact('job', 'applications'));
    }

    public function analytics(Job $job)
    {
        $user = auth()->user();
        
        // Check if user owns this job
        if ($job->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $job->load(['applications.candidate', 'applications.responses.question', 'applications.reviews']);

        // Application status breakdown
        $statusBreakdown = $job->applications()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Question completion rates
        $questionStats = $job->questions()->with(['responses' => function($query) use ($job) {
            $query->whereIn('application_id', $job->applications()->pluck('id'));
        }])->get()->map(function($question) {
            $totalApplications = $question->responses->count();
            $completedResponses = $question->responses->where('status', 'submitted')->count();
            
            return [
                'question_text' => $question->question_text,
                'type' => $question->type,
                'total_applications' => $totalApplications,
                'completed_responses' => $completedResponses,
                'completion_rate' => $totalApplications > 0 ? ($completedResponses / $totalApplications) * 100 : 0,
                'average_duration' => $question->responses->where('status', 'submitted')->avg('duration'),
            ];
        });

        // Review statistics
        $reviewStats = [
            'total_reviews' => $job->applications->sum(function($application) {
                return $application->reviews->count();
            }),
            'average_rating' => $job->applications->flatMap->reviews->avg('rating'),
            'rating_distribution' => $job->applications->flatMap->reviews
                ->groupBy('rating')
                ->map->count()
                ->sortKeys(),
        ];

        // Time-based analytics
        $dailyApplications = $job->applications()
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        $dailyCompletions = $job->applications()
            ->where('status', 'completed')
            ->selectRaw('DATE(updated_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        return view('recruiter.jobs.analytics', compact(
            'job', 
            'statusBreakdown', 
            'questionStats', 
            'reviewStats',
            'dailyApplications',
            'dailyCompletions'
        ));
    }
}
