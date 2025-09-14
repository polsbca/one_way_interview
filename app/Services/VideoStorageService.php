<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Exception;

class VideoStorageService
{
    protected $disk;
    protected $maxFileSize;
    protected $allowedMimeTypes;

    public function __construct()
    {
        $this->disk = env('INTERVIEW_STORAGE_DRIVER', 'local');
        $this->maxFileSize = env('INTERVIEW_MAX_FILE_SIZE', 100 * 1024 * 1024); // 100MB default
        $this->allowedMimeTypes = [
            'video/webm',
            'video/mp4',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-ms-wmv'
        ];
    }

    /**
     * Store a video file for an interview response
     */
    public function storeInterviewVideo(UploadedFile $file, int $applicationId, int $questionId): string
    {
        // Validate file
        $this->validateVideoFile($file);

        // Generate unique filename
        $filename = $this->generateFilename($file, $applicationId, $questionId);
        
        // Determine storage path
        $path = "interviews/{$applicationId}/{$questionId}/{$filename}";

        // Store the file
        $disk = $this->getStorageDisk();
        $storedPath = $disk->putFileAs(
            dirname($path),
            $file,
            basename($filename),
            [
                'visibility' => 'private',
                'mimetype' => $file->getMimeType()
            ]
        );

        if (!$storedPath) {
            throw new Exception('Failed to store video file');
        }

        return $storedPath;
    }

    /**
     * Get a temporary URL for video playback
     */
    public function getTemporaryUrl(string $path, int $expirationMinutes = 60): string
    {
        $disk = $this->getStorageDisk();
        
        if ($this->disk === 's3' || $this->disk === 'interviews_s3') {
            return $disk->temporaryUrl(
                $path,
                now()->addMinutes($expirationMinutes),
                [
                    'ResponseContentType' => 'video/webm',
                    'ResponseContentDisposition' => 'inline'
                ]
            );
        }

        // For local storage, return the regular URL
        return $disk->url($path);
    }

    /**
     * Delete a video file
     */
    public function deleteVideo(string $path): bool
    {
        $disk = $this->getStorageDisk();
        
        try {
            return $disk->delete($path);
        } catch (Exception $e) {
            \Log::error('Failed to delete video file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file size in bytes
     */
    public function getFileSize(string $path): int
    {
        $disk = $this->getStorageDisk();
        
        try {
            return $disk->size($path);
        } catch (Exception $e) {
            \Log::error('Failed to get file size: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check if file exists
     */
    public function fileExists(string $path): bool
    {
        $disk = $this->getStorageDisk();
        
        try {
            return $disk->exists($path);
        } catch (Exception $e) {
            \Log::error('Failed to check file existence: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get storage disk instance
     */
    protected function getStorageDisk()
    {
        return Storage::disk($this->disk === 'interviews_s3' ? 's3' : $this->disk);
    }

    /**
     * Validate video file
     */
    protected function validateVideoFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            throw new Exception("File size exceeds maximum limit of " . ($this->maxFileSize / 1024 / 1024) . "MB");
        }

        // Check mime type
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new Exception("Invalid file type. Allowed types: " . implode(', ', $this->allowedMimeTypes));
        }

        // Check if file is actually a video
        if (!str_starts_with($file->getMimeType(), 'video/')) {
            throw new Exception("File must be a video");
        }
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file, int $applicationId, int $questionId): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->timestamp;
        $random = Str::random(8);
        
        return "{$applicationId}_{$questionId}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get storage statistics
     */
    public function getStorageStats(): array
    {
        $disk = $this->getStorageDisk();
        
        try {
            if ($this->disk === 's3' || $this->disk === 'interviews_s3') {
                // For S3, we can't easily get total size without listing all files
                return [
                    'driver' => $this->disk,
                    'total_files' => 'N/A',
                    'total_size' => 'N/A',
                    'available_space' => 'N/A'
                ];
            }

            // For local storage
            $path = $disk->path('');
            $totalFiles = 0;
            $totalSize = 0;

            if (is_dir($path)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $totalFiles++;
                        $totalSize += $file->getSize();
                    }
                }
            }

            $freeSpace = disk_free_space($path);

            return [
                'driver' => $this->disk,
                'total_files' => $totalFiles,
                'total_size' => $this->formatBytes($totalSize),
                'available_space' => $this->formatBytes($freeSpace)
            ];
        } catch (Exception $e) {
            \Log::error('Failed to get storage stats: ' . $e->getMessage());
            return [
                'driver' => $this->disk,
                'error' => $e->getMessage()
            ];
        }
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
     * Clean up old video files (for maintenance)
     */
    public function cleanupOldFiles(int $daysOld = 30): array
    {
        $disk = $this->getStorageDisk();
        $deletedFiles = [];
        $totalSizeFreed = 0;

        try {
            $cutoffDate = now()->subDays($daysOld);

            if ($this->disk === 's3' || $this->disk === 'interviews_s3') {
                // For S3, we would need to implement a more complex cleanup logic
                // This is a simplified version
                $files = $disk->allFiles('interviews/');
                
                foreach ($files as $file) {
                    try {
                        $lastModified = $disk->lastModified($file);
                        
                        if ($lastModified < $cutoffDate->timestamp) {
                            $size = $disk->size($file);
                            if ($disk->delete($file)) {
                                $deletedFiles[] = $file;
                                $totalSizeFreed += $size;
                            }
                        }
                    } catch (Exception $e) {
                        \Log::error("Failed to process file {$file}: " . $e->getMessage());
                    }
                }
            } else {
                // For local storage
                $path = $disk->path('interviews');
                
                if (is_dir($path)) {
                    $iterator = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                        \RecursiveIteratorIterator::CHILD_FIRST
                    );

                    foreach ($iterator as $file) {
                        if ($file->isFile() && $file->getMTime() < $cutoffDate->timestamp) {
                            $relativePath = str_replace($disk->path(''), '', $file->getPathname());
                            if ($disk->delete($relativePath)) {
                                $deletedFiles[] = $relativePath;
                                $totalSizeFreed += $file->getSize();
                            }
                        }
                    }
                }
            }

            return [
                'deleted_files' => count($deletedFiles),
                'total_size_freed' => $this->formatBytes($totalSizeFreed),
                'files' => $deletedFiles
            ];
        } catch (Exception $e) {
            \Log::error('Failed to cleanup old files: ' . $e->getMessage());
            return [
                'error' => $e->getMessage()
            ];
        }
    }
}
