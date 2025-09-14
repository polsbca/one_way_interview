<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Review;
use App\Models\Response;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('recruiter');
    }

    public function show(Application $application)
    {
        $user = auth()->user();
        
        // Check if user has access to this application
        if ($application->job->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $application->load([
            'job.questions',
            'candidate',
            'responses.question',
            'reviews.reviewer'
        ]);

        // Check if user has already reviewed this application
        $userReview = $application->reviews()->where('reviewer_id', $user->id)->first();

        // Calculate overall rating from all reviews
        $overallRating = $application->reviews()->avg('rating');

        return view('recruiter.applications.show', compact('application', 'userReview', 'overallRating'));
    }

    public function review(Application $application)
    {
        $user = auth()->user();
        
        // Check if user has access to this application
        if ($application->job->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if interview is completed
        if ($application->status !== 'completed') {
            return redirect()->route('recruiter.applications.show', $application)
                ->with('error', 'Interview must be completed before reviewing.');
        }

        $application->load([
            'job.questions',
            'candidate',
            'responses.question'
        ]);

        // Check if user has already reviewed this application
        $existingReview = $application->reviews()->where('reviewer_id', $user->id)->first();

        if ($existingReview) {
            return redirect()->route('recruiter.applications.show', $application)
                ->with('info', 'You have already reviewed this application.');
        }

        return view('recruiter.applications.review', compact('application'));
    }

    public function submitReview(Request $request, Application $application)
    {
        $user = auth()->user();
        
        // Check if user has access to this application
        if ($application->job->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if interview is completed
        if ($application->status !== 'completed') {
            return back()->with('error', 'Interview must be completed before reviewing.');
        }

        // Check if user has already reviewed this application
        $existingReview = $application->reviews()->where('reviewer_id', $user->id)->first();
        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this application.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'required|string|min:10',
            'feedback' => 'nullable|string',
            'decision' => 'required|in:proceed,reject,hold',
            'response_ratings' => 'nullable|array',
            'response_ratings.*.response_id' => 'required|exists:responses,id',
            'response_ratings.*.rating' => 'required|integer|min:1|max:5',
            'response_ratings.*.comment' => 'nullable|string',
        ]);

        // Create the review
        $review = $application->reviews()->create([
            'reviewer_id' => $user->id,
            'rating' => $request->rating,
            'comments' => $request->comments,
            'feedback' => $request->feedback,
            'decision' => $request->decision,
            'reviewed_at' => now(),
        ]);

        // Handle response-specific ratings if provided
        if ($request->has('response_ratings')) {
            foreach ($request->response_ratings as $responseRating) {
                // Verify the response belongs to this application
                $response = Response::where('id', $responseRating['response_id'])
                    ->where('application_id', $application->id)
                    ->first();

                if ($response) {
                    $response->update([
                        'rating' => $responseRating['rating'],
                        'comment' => $responseRating['comment'] ?? null,
                    ]);
                }
            }
        }

        // Update application status based on decision
        $this->updateApplicationStatus($application, $request->decision);

        // Notify the candidate about the review
        $this->notifyCandidate($application, $review);

        return redirect()->route('recruiter.applications.show', $application)
            ->with('success', 'Review submitted successfully!');
    }

    public function updateReview(Request $request, Application $application, Review $review)
    {
        $user = auth()->user();
        
        // Check if user owns this review
        if ($review->reviewer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'required|string|min:10',
            'feedback' => 'nullable|string',
            'decision' => 'required|in:proceed,reject,hold',
            'response_ratings' => 'nullable|array',
            'response_ratings.*.response_id' => 'required|exists:responses,id',
            'response_ratings.*.rating' => 'required|integer|min:1|max:5',
            'response_ratings.*.comment' => 'nullable|string',
        ]);

        // Update the review
        $review->update([
            'rating' => $request->rating,
            'comments' => $request->comments,
            'feedback' => $request->feedback,
            'decision' => $request->decision,
            'reviewed_at' => now(),
        ]);

        // Handle response-specific ratings if provided
        if ($request->has('response_ratings')) {
            foreach ($request->response_ratings as $responseRating) {
                // Verify the response belongs to this application
                $response = Response::where('id', $responseRating['response_id'])
                    ->where('application_id', $application->id)
                    ->first();

                if ($response) {
                    $response->update([
                        'rating' => $responseRating['rating'],
                        'comment' => $responseRating['comment'] ?? null,
                    ]);
                }
            }
        }

        // Update application status based on decision
        $this->updateApplicationStatus($application, $request->decision);

        return redirect()->route('recruiter.applications.show', $application)
            ->with('success', 'Review updated successfully!');
    }

    protected function updateApplicationStatus(Application $application, string $decision)
    {
        switch ($decision) {
            case 'proceed':
                $application->status = 'approved';
                break;
            case 'reject':
                $application->status = 'rejected';
                break;
            case 'hold':
                $application->status = 'on_hold';
                break;
        }
        
        $application->save();
    }

    protected function notifyCandidate(Application $application, Review $review)
    {
        $decisionText = match($review->decision) {
            'proceed' => 'approved',
            'reject' => 'not selected',
            'hold' => 'on hold',
            default => 'reviewed',
        };

        \App\Models\Notification::createNotification(
            $application->candidate_id,
            'review_completed',
            'Application Review Completed',
            "Your application for {$application->job->title} has been {$decisionText}.",
            [
                'job_id' => $application->job_id,
                'application_id' => $application->id,
                'review_id' => $review->id,
                'decision' => $review->decision,
            ]
        );
    }

    public function export(Application $application)
    {
        $user = auth()->user();
        
        // Check if user has access to this application
        if ($application->job->created_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $application->load([
            'job',
            'candidate',
            'responses.question',
            'reviews.reviewer'
        ]);

        // Generate PDF or CSV export
        // This would typically use a package like dompdf or maatwebsite/excel
        // For now, we'll return a simple JSON response
        
        return response()->json([
            'application' => [
                'id' => $application->id,
                'candidate' => [
                    'name' => $application->candidate->full_name,
                    'email' => $application->candidate->email,
                    'phone' => $application->candidate->phone,
                ],
                'job' => [
                    'title' => $application->job->title,
                    'company' => $application->job->company,
                ],
                'status' => $application->status,
                'applied_at' => $application->created_at->toISOString(),
                'completed_at' => $application->updated_at->toISOString(),
            ],
            'responses' => $application->responses->map(function($response) {
                return [
                    'question' => $response->question->question_text,
                    'type' => $response->response_type,
                    'duration' => $response->duration,
                    'rating' => $response->rating,
                    'comment' => $response->comment,
                    'created_at' => $response->created_at->toISOString(),
                ];
            }),
            'reviews' => $application->reviews->map(function($review) {
                return [
                    'reviewer' => $review->reviewer->full_name,
                    'rating' => $review->rating,
                    'comments' => $review->comments,
                    'feedback' => $review->feedback,
                    'decision' => $review->decision,
                    'reviewed_at' => $review->reviewed_at->toISOString(),
                ];
            }),
        ]);
    }
}
