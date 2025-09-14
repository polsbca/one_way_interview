<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Response;
use Illuminate\Database\Seeder;

class ResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all applications that are not pending
        $applications = Application::whereIn('status', ['reviewed', 'proceed', 'hold', 'reject'])->get();

        foreach ($applications as $application) {
            // Get all questions for the job
            $questions = $application->job->questions;

            foreach ($questions as $question) {
                // Create a response for each question
                Response::firstOrCreate(
                    [
                        'application_id' => $application->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'video_url' => 'storage/videos/sample_response_' . $question->id . '_' . $application->id . '.mp4',
                        'duration' => rand(60, $question->time_limit - 10), // Random duration within time limit
                        'rating' => $this->generateRandomRating(),
                        'comment' => $this->generateRandomComment(),
                        'created_at' => $application->created_at->addHours(rand(1, 24)),
                        'updated_at' => $application->updated_at,
                    ]
                );
            }
        }

        $this->command->info('Responses seeded successfully!');
        $this->command->info('Created video responses for all non-pending applications');
        $this->command->info('Each response includes ratings and comments from recruiters');
    }

    /**
     * Generate a random rating between 1 and 5
     *
     * @return int|null
     */
    private function generateRandomRating()
    {
        // 70% chance of having a rating
        if (rand(1, 10) <= 7) {
            return rand(1, 5);
        }
        return null;
    }

    /**
     * Generate a random comment for a response
     *
     * @return string|null
     */
    private function generateRandomComment()
    {
        $comments = [
            "Excellent response! Shows deep technical knowledge and clear communication skills.",
            "Good explanation of the concept. Could provide more specific examples from personal experience.",
            "Well-structured answer. Demonstrates strong problem-solving abilities.",
            "Clear and concise response. Shows good understanding of the subject matter.",
            "Great insights! The candidate provided practical examples and real-world applications.",
            "Solid answer, but could benefit from more depth in certain areas.",
            "Impressive technical knowledge. The candidate explained complex concepts effectively.",
            "Good communication skills. The response was articulate and well-organized.",
            "Shows strong analytical thinking. The approach to the problem was methodical.",
            "Comprehensive answer that covers all aspects of the question thoroughly.",
            "The candidate demonstrated excellent problem-solving skills and technical expertise.",
            "Clear understanding of best practices and industry standards.",
            "Good balance between theoretical knowledge and practical application.",
            "The response showed creativity and innovative thinking in approaching the problem.",
            "Strong technical foundation with good communication abilities.",
            "The candidate provided detailed explanations with relevant examples.",
            "Excellent presentation skills and confident delivery of the response.",
            "Good understanding of the subject area with room for improvement in some areas.",
            "The response demonstrated both technical skills and soft skills effectively.",
            "Well-articulated answer that shows depth of knowledge and experience.",
        ];

        // 60% chance of having a comment
        if (rand(1, 10) <= 6) {
            return $comments[array_rand($comments)];
        }
        return null;
    }
}
