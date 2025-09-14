<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Question;
use App\Models\Response;
use App\Services\VideoStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InterviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('candidate');
    }

    public function start(Application $application)
    {
        $user = auth()->user();
        
        // Check if application belongs to user
        if ($application->candidate_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if interview is already completed
        if ($application->status === 'completed') {
            return redirect()->route('candidate.interview.completed', $application)
                ->with('info', 'You have already completed this interview.');
        }

        // Load questions
        $questions = $application->job->questions()->orderBy('order')->get();
        
        // Get first unanswered question
        $firstQuestion = $questions->first(function($question) use ($application) {
            $responses = $application->responses()->where('question_id', $question->id)->count();
            return $responses < $question->max_attempts;
        });

        if (!$firstQuestion) {
            // All questions answered
            $application->update(['status' => 'completed']);
            return redirect()->route('candidate.interview.completed', $application)
                ->with('success', 'Interview completed successfully!');
        }

        return redirect()->route('candidate.interview.question', [$application, $firstQuestion]);
    }

    public function question(Application $application, Question $question)
    {
        $user = auth()->user();
        
        // Check if application belongs to user
        if ($application->candidate_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if question belongs to the job
        if ($question->job_id !== $application->job_id) {
            abort(404, 'Question not found.');
        }

        // Check if interview is completed
        if ($application->status === 'completed') {
            return redirect()->route('candidate.interview.completed', $application)
                ->with('info', 'You have already completed this interview.');
        }

        // Check if user has exceeded max attempts
        $attempts = $application->responses()->where('question_id', $question->id)->count();
        if ($attempts >= $question->max_attempts) {
            return redirect()->route('candidate.interview.next', $application)
                ->with('error', 'You have exceeded the maximum number of attempts for this question.');
        }

        // Get previous responses for this question
        $previousResponses = $application->responses()
            ->where('question_id', $question->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('candidate.interview.question', compact('application', 'question', 'previousResponses', 'attempts'));
    }

    public function submitResponse(Request $request, Application $application, Question $question)
    {
        $user = auth()->user();
        
        // Check if application belongs to user
        if ($application->candidate_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if question belongs to the job
        if ($question->job_id !== $application->job_id) {
            abort(404, 'Question not found.');
        }

        // Check if user has exceeded max attempts
        $attempts = $application->responses()->where('question_id', $question->id)->count();
        if ($attempts >= $question->max_attempts) {
            return response()->json(['error' => 'Maximum attempts exceeded'], 422);
        }

        $request->validate([
            'response_type' => 'required|in:video,text',
            'response_data' => 'required|string',
            'duration' => 'required|integer|min:0',
        ]);

        $response = $application->responses()->create([
            'question_id' => $question->id,
            'response_type' => $request->response_type,
            'response_data' => $request->response_data,
            'duration' => $request->duration,
            'attempt_number' => $attempts + 1,
            'status' => 'submitted',
        ]);

        // Handle file upload if it's a video response
        if ($request->response_type === 'video' && $request->hasFile('video_file')) {
            try {
                $videoStorage = new VideoStorageService();
                $path = $videoStorage->storeInterviewVideo($request->file('video_file'), $application->id, $question->id);
                $response->file_path = $path;
                $response->file_size = $request->file('video_file')->getSize();
                $response->save();
            } catch (\Exception $e) {
                // Delete the response if video storage fails
                $response->delete();
                return response()->json(['error' => 'Failed to store video: ' . $e->getMessage()], 500);
            }
        }

        // Update application status
        $this->updateApplicationStatus($application);

        return response()->json([
            'success' => true,
            'next_question_url' => route('candidate.interview.next', $application),
            'message' => 'Response submitted successfully!'
        ]);
    }

    public function nextQuestion(Application $application)
    {
        $user = auth()->user();
        
        // Check if application belongs to user
        if ($application->candidate_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Get all questions
        $questions = $application->job->questions()->orderBy('order')->get();
        
        // Find next unanswered question
        $nextQuestion = $questions->first(function($question) use ($application) {
            $responses = $application->responses()->where('question_id', $question->id)->count();
            return $responses < $question->max_attempts;
        });

        if ($nextQuestion) {
            return redirect()->route('candidate.interview.question', [$application, $nextQuestion]);
        }

        // All questions answered
        $application->update(['status' => 'completed']);
        return redirect()->route('candidate.interview.completed', $application);
    }

    public function completed(Application $application)
    {
        $user = auth()->user();
        
        // Check if application belongs to user
        if ($application->candidate_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $application->load(['responses.question', 'job']);

        return view('candidate.interview.completed', compact('application'));
    }

    protected function updateApplicationStatus(Application $application)
    {
        $questions = $application->job->questions()->count();
        $answeredQuestions = $application->responses()->distinct('question_id')->count('question_id');

        if ($answeredQuestions === 0) {
            $application->update(['status' => 'applied']);
        } elseif ($answeredQuestions < $questions) {
            $application->update(['status' => 'in_progress']);
        } else {
            $application->update(['status' => 'completed']);
        }
    }
}
