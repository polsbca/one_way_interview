<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Response;
use App\Models\Application;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResponseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('recruiter');
    }

    public function rate(Request $request, Response $response)
    {
        try {
            $user = auth()->user();
            
            // Check if user has access to this response through the application
            $application = $response->application;
            if ($application->job->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.',
                    'status' => 403,
                ], 403);
            }

            // Validate input using ValidationService
            $validator = ValidationService::validateResponseRating($request->all());
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                ], 422);
            }

            $response->addRating(
                $request->rating,
                $request->comment,
                $user->id
            );

            // Log the rating action
            \Log::info('Response rated', [
                'response_id' => $response->id,
                'application_id' => $application->id,
                'job_id' => $application->job->id,
                'rating' => $request->rating,
                'reviewer_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Response rated successfully',
                'response' => [
                    'id' => $response->id,
                    'rating' => $response->rating,
                    'comment' => $response->comment,
                    'reviewed_by' => $response->reviewer->full_name,
                    'reviewed_at' => $response->reviewed_at->format('M d, Y H:i'),
                ]
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Response rating failed', [
                'error' => $e->getMessage(),
                'response_id' => $response->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to rate response. Please try again.',
                'status' => 500,
            ], 500);
        }
    }
}
