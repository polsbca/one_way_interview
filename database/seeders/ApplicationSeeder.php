<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all published jobs and candidates
        $publishedJobs = Job::where('status', 'published')->get();
        $candidates = User::where('role', 'candidate')->get();

        // Create applications for each candidate to different jobs
        $applications = [
            // Emma Wilson applications
            [
                'candidate_email' => 'emma.wilson@example.com',
                'job_title' => 'Senior Laravel Developer',
                'status' => 'reviewed',
            ],
            [
                'candidate_email' => 'emma.wilson@example.com',
                'job_title' => 'Full Stack Developer',
                'status' => 'proceed',
            ],

            // James Davis applications
            [
                'candidate_email' => 'james.davis@example.com',
                'job_title' => 'Frontend Developer (React)',
                'status' => 'pending',
            ],
            [
                'candidate_email' => 'james.davis@example.com',
                'job_title' => 'Senior Laravel Developer',
                'status' => 'reviewed',
            ],

            // Olivia Miller applications
            [
                'candidate_email' => 'olivia.miller@example.com',
                'job_title' => 'Full Stack Developer',
                'status' => 'pending',
            ],
            [
                'candidate_email' => 'olivia.miller@example.com',
                'job_title' => 'DevOps Engineer',
                'status' => 'hold',
            ],

            // William Garcia applications
            [
                'candidate_email' => 'william.garcia@example.com',
                'job_title' => 'Frontend Developer (React)',
                'status' => 'proceed',
            ],
            [
                'candidate_email' => 'william.garcia@example.com',
                'job_title' => 'Full Stack Developer',
                'status' => 'reviewed',
            ],

            // Sophia Martinez applications
            [
                'candidate_email' => 'sophia.martinez@example.com',
                'job_title' => 'Senior Laravel Developer',
                'status' => 'pending',
            ],
            [
                'candidate_email' => 'sophia.martinez@example.com',
                'job_title' => 'Data Scientist',
                'status' => 'reject',
            ],

            // Benjamin Anderson applications
            [
                'candidate_email' => 'benjamin.anderson@example.com',
                'job_title' => 'DevOps Engineer',
                'status' => 'reviewed',
            ],
            [
                'candidate_email' => 'benjamin.anderson@example.com',
                'job_title' => 'Full Stack Developer',
                'status' => 'pending',
            ],

            // Isabella Taylor applications
            [
                'candidate_email' => 'isabella.taylor@example.com',
                'job_title' => 'Frontend Developer (React)',
                'status' => 'proceed',
            ],
            [
                'candidate_email' => 'isabella.taylor@example.com',
                'job_title' => 'UI/UX Designer',
                'status' => 'pending',
            ],

            // Lucas Thomas applications
            [
                'candidate_email' => 'lucas.thomas@example.com',
                'job_title' => 'Data Scientist',
                'status' => 'reviewed',
            ],
            [
                'candidate_email' => 'lucas.thomas@example.com',
                'job_title' => 'DevOps Engineer',
                'status' => 'hold',
            ],
        ];

        foreach ($applications as $applicationData) {
            $candidate = $candidates->firstWhere('email', $applicationData['candidate_email']);
            $job = $publishedJobs->firstWhere('title', $applicationData['job_title']);

            if ($candidate && $job) {
                Application::firstOrCreate(
                    [
                        'job_id' => $job->id,
                        'user_id' => $candidate->id,
                    ],
                    [
                        'status' => $applicationData['status'],
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(0, 7)),
                    ]
                );
            }
        }

        $this->command->info('Applications seeded successfully!');
        $this->command->info('Created 16 applications across different candidates and jobs');
        $this->command->info('Application status distribution: pending, reviewed, proceed, reject, hold');
    }
}
