<?php

namespace App\Http\Controllers;

use App\Models\Response;
use App\Models\Application;
use App\Services\VideoStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    protected $videoStorageService;

    public function __construct(VideoStorageService $videoStorageService)
    {
        $this->videoStorageService = $videoStorageService;
        $this->middleware('auth');
    }

    /**
     * Stream a video response securely
     */
    public function stream(Response $response, Request $request)
    {
        // Check if user has permission to view this video
        if (!$this->canViewVideo($response)) {
            abort(403, 'Unauthorized access to video');
        }

        // Check if video file exists
        if (!$response->file_path || !$this->videoStorageService->fileExists($response->file_path)) {
            abort(404, 'Video file not found');
        }

        try {
            // Generate temporary URL for S3 or direct URL for local storage
            $url = $this->videoStorageService->getTemporaryUrl($response->file_path, 60);
            
            return redirect()->away($url);
        } catch (\Exception $e) {
            \Log::error('Failed to generate video URL: ' . $e->getMessage());
            abort(500, 'Failed to load video');
        }
    }

    /**
     * Get video metadata
     */
    public function metadata(Response $response)
    {
        // Check if user has permission to view this video
        if (!$this->canViewVideo($response)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if video file exists
        if (!$response->file_path || !$this->videoStorageService->fileExists($response->file_path)) {
            return response()->json(['error' => 'Video file not found'], 404);
        }

        try {
            $fileSize = $this->videoStorageService->getFileSize($response->file_path);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'file_size' => $fileSize,
                    'file_size_formatted' => $this->formatBytes($fileSize),
                    'duration' => $response->duration,
                    'response_type' => $response->response_type,
                    'created_at' => $response->created_at->toISOString(),
                    'attempt_number' => $response->attempt_number,
                    'question_text' => $response->question->question_text,
                    'candidate_name' => $response->application->candidate->full_name,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to get video metadata: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get video metadata'], 500);
        }
    }

    /**
     * Download a video response (for authorized users only)
     */
    public function download(Response $response)
    {
        // Check if user has permission to download this video
        if (!$this->canDownloadVideo($response)) {
            abort(403, 'Unauthorized access to download video');
        }

        // Check if video file exists
        if (!$response->file_path || !$this->videoStorageService->fileExists($response->file_path)) {
            abort(404, 'Video file not found');
        }

        try {
            $disk = $this->videoStorageService->getStorageDisk();
            
            if (method_exists($disk, 'download')) {
                return $disk->download($response->file_path, "interview_response_{$response->id}.webm");
            }

            // Fallback for local storage
            $path = $disk->path($response->file_path);
            if (file_exists($path)) {
                return response()->download($path, "interview_response_{$response->id}.webm");
            }

            abort(404, 'Video file not found');
        } catch (\Exception $e) {
            \Log::error('Failed to download video: ' . $e->getMessage());
            abort(500, 'Failed to download video');
        }
    }

    /**
     * Check if user can view the video
     */
    protected function canViewVideo(Response $response): bool
    {
        $user = Auth::user();
        
        // Admin can view all videos
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Recruiters can view videos for jobs they created or are assigned to
        if ($user->hasRole('recruiter')) {
            $application = $response->application;
            $job = $application->job;
            
            // Job creator can view
            if ($job->created_by === $user->id) {
                return true;
            }
            
            // Check if recruiter is assigned to this job (if assignment system exists)
            // This would depend on your specific business logic
            return true; // For now, allow all recruiters
        }
        
        // Candidates can only view their own video responses
        if ($user->hasRole('candidate')) {
            return $response->application->candidate_id === $user->id;
        }
        
        return false;
    }

    /**
     * Check if user can download the video
     */
    protected function canDownloadVideo(Response $response): bool
    {
        $user = Auth::user();
        
        // Only admins and recruiters can download videos
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('recruiter')) {
            $application = $response->application;
            $job = $application->job;
            
            // Job creator can download
            if ($job->created_by === $user->id) {
                return true;
            }
            
            // Check if recruiter is assigned to this job
            return true; // For now, allow all recruiters
        }
        
        // Candidates cannot download videos
        return false;
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 Bytes';

        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get video thumbnail (if supported)
     */
    public function thumbnail(Response $response)
    {
        // Check if user has permission to view this video
        if (!$this->canViewVideo($response)) {
            abort(403, 'Unauthorized access to video thumbnail');
        }

        // For now, return a placeholder or generate thumbnail if possible
        // This would require video processing libraries like FFMPEG
        
        // Return a placeholder image for now
        $placeholderPath = public_path('images/video-thumbnail-placeholder.jpg');
        
        if (file_exists($placeholderPath)) {
            return response()->file($placeholderPath, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=86400' // Cache for 1 day
            ]);
        }

        // If no placeholder exists, return a simple SVG
        $svg = '<svg width="320" height="180" xmlns="http://www.w3.org/2000/svg">
            <rect width="320" height="180" fill="#f0f0f0"/>
            <text x="160" y="90" text-anchor="middle" font-family="Arial, sans-serif" font-size="16" fill="#666">
                Video Thumbnail
            </text>
        </svg>';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400'
        ]);
    }

    /**
     * Batch video operations (for admin/recruiter dashboards)
     */
    public function batchMetadata(Request $request)
    {
        $request->validate([
            'response_ids' => 'required|array',
            'response_ids.*' => 'exists:responses,id'
        ]);

        $user = Auth::user();
        $responseIds = $request->input('response_ids');
        
        // Get responses that user has permission to view
        $responses = Response::whereIn('id', $responseIds)
            ->with(['application.candidate', 'question'])
            ->get()
            ->filter(function ($response) use ($user) {
                return $this->canViewVideo($response);
            });

        $metadata = [];
        
        foreach ($responses as $response) {
            try {
                if ($response->file_path && $this->videoStorageService->fileExists($response->file_path)) {
                    $fileSize = $this->videoStorageService->getFileSize($response->file_path);
                    
                    $metadata[] = [
                        'id' => $response->id,
                        'file_size' => $fileSize,
                        'file_size_formatted' => $this->formatBytes($fileSize),
                        'duration' => $response->duration,
                        'response_type' => $response->response_type,
                        'created_at' => $response->created_at->toISOString(),
                        'attempt_number' => $response->attempt_number,
                        'question_text' => $response->question->question_text,
                        'candidate_name' => $response->application->candidate->full_name,
                        'job_title' => $response->application->job->title,
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Failed to get metadata for response {$response->id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'data' => $metadata
        ]);
    }
}
