<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('candidate');
    }

    public function index()
    {
        $user = auth()->user();
        
        $jobs = Job::published()
            ->where('deadline', '>', now())
            ->whereDoesntHave('applications', function($query) use ($user) {
                $query->where('candidate_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('candidate.jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        $user = auth()->user();
        
        // Check if user has already applied
        $hasApplied = $job->applications()->where('candidate_id', $user->id)->exists();
        
        $job->load(['questions' => function($query) {
            $query->orderBy('order');
        }]);

        return view('candidate.jobs.show', compact('job', 'hasApplied'));
    }

    public function apply(Request $request, Job $job)
    {
        $user = auth()->user();
        
        // Check if user has already applied
        if ($job->applications()->where('candidate_id', $user->id)->exists()) {
            return redirect()->route('candidate.jobs.show', $job)
                ->with('error', 'You have already applied for this job.');
        }

        // Check if job is still accepting applications
        if ($job->status !== 'published' || ($job->deadline && $job->deadline->isPast())) {
            return redirect()->route('candidate.jobs.index')
                ->with('error', 'This job is no longer accepting applications.');
        }

        $request->validate([
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'cover_letter' => 'nullable|string|max:5000',
        ]);

        $application = $job->applications()->create([
            'candidate_id' => $user->id,
            'status' => 'applied',
            'personal_info' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'cover_letter' => $request->cover_letter,
        ]);

        // Handle resume upload
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $application->resume_path = $path;
            $application->save();
        }

        // Notify recruiters
        $this->notifyRecruiters($job, $user);

        return redirect()->route('candidate.interview.start', $application)
            ->with('success', 'Application submitted successfully! You can now start the interview.');
    }

    protected function notifyRecruiters(Job $job, $candidate)
    {
        // This would typically notify recruiters about the new application
        // For now, we'll just log it or create a notification
        \App\Models\Notification::createNotification(
            $job->created_by,
            'application_submitted',
            'New Application Received',
            "{$candidate->full_name} has applied for {$job->title}",
            [
                'job_id' => $job->id,
                'candidate_id' => $candidate->id,
                'application_id' => $job->applications()->latest()->first()->id,
            ]
        );
    }
}
