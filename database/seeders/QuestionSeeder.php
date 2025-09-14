<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all jobs
        $jobs = Job::all();

        // Questions for Senior Laravel Developer
        $laravelQuestions = [
            [
                'question' => 'Tell us about your experience with Laravel and what makes you passionate about working with this framework.',
                'time_limit' => 120,
                'order' => 1,
            ],
            [
                'question' => 'Describe a challenging Laravel project you\'ve worked on and how you overcame the technical difficulties.',
                'time_limit' => 180,
                'order' => 2,
            ],
            [
                'question' => 'How do you approach testing in Laravel? Explain your experience with PHPUnit and testing best practices.',
                'time_limit' => 150,
                'order' => 3,
            ],
            [
                'question' => 'What Laravel packages or tools do you frequently use and why? How do they improve your development workflow?',
                'time_limit' => 120,
                'order' => 4,
            ],
            [
                'question' => 'How do you handle database optimization and performance tuning in Laravel applications?',
                'time_limit' => 150,
                'order' => 5,
            ],
        ];

        // Questions for Frontend Developer (React)
        $reactQuestions = [
            [
                'question' => 'Walk us through your experience with React and what you find most exciting about working with this library.',
                'time_limit' => 120,
                'order' => 1,
            ],
            [
                'question' => 'Explain the concept of hooks in React and provide an example of how you\'ve used custom hooks in a project.',
                'time_limit' => 180,
                'order' => 2,
            ],
            [
                'question' => 'How do you approach state management in large React applications? Compare different approaches you\'ve used.',
                'time_limit' => 150,
                'order' => 3,
            ],
            [
                'question' => 'Describe your experience with React performance optimization. What techniques do you use to improve application speed?',
                'time_limit' => 150,
                'order' => 4,
            ],
        ];

        // Questions for Full Stack Developer
        $fullStackQuestions = [
            [
                'question' => 'Tell us about your full stack development experience and how you bridge the gap between frontend and backend.',
                'time_limit' => 120,
                'order' => 1,
            ],
            [
                'question' => 'Describe a complex full stack project you\'ve built. What were the main challenges and how did you solve them?',
                'time_limit' => 180,
                'order' => 2,
            ],
            [
                'question' => 'How do you ensure seamless integration between frontend React components and Laravel APIs?',
                'time_limit' => 150,
                'order' => 3,
            ],
            [
                'question' => 'What strategies do you use for database design and optimization in full stack applications?',
                'time_limit' => 150,
                'order' => 4,
            ],
            [
                'question' => 'How do you approach debugging and troubleshooting issues that span both frontend and backend?',
                'time_limit' => 120,
                'order' => 5,
            ],
        ];

        // Questions for DevOps Engineer
        $devopsQuestions = [
            [
                'question' => 'Tell us about your experience with DevOps and what drives your interest in this field.',
                'time_limit' => 120,
                'order' => 1,
            ],
            [
                'question' => 'Describe your experience with containerization and orchestration technologies like Docker and Kubernetes.',
                'time_limit' => 180,
                'order' => 2,
            ],
            [
                'question' => 'How do you approach implementing CI/CD pipelines? What tools have you used and why?',
                'time_limit' => 150,
                'order' => 3,
            ],
            [
                'question' => 'Explain your experience with infrastructure as code and cloud platforms.',
                'time_limit' => 150,
                'order' => 4,
            ],
        ];

        // Questions for UI/UX Designer
        $designQuestions = [
            [
                'question' => 'Walk us through your design process and how you approach creating user-centered designs.',
                'time_limit' => 120,
                'order' => 1,
            ],
            [
                'question' => 'Describe a challenging design project you\'ve worked on and how you addressed user feedback and requirements.',
                'time_limit' => 180,
                'order' => 2,
            ],
            [
                'question' => 'How do you balance aesthetic design with functionality and usability in your work?',
                'time_limit' => 150,
                'order' => 3,
            ],
            [
                'question' => 'What design tools and methodologies do you prefer and why? How do they enhance your workflow?',
                'time_limit' => 120,
                'order' => 4,
            ],
        ];

        // Questions for Data Scientist
        $dataScienceQuestions = [
            [
                'question' => 'Tell us about your background in data science and what areas of data analysis interest you most.',
                'time_limit' => 120,
                'order' => 1,
            ],
            [
                'question' => 'Describe a data science project you\'re proud of. What was the problem, your approach, and the results?',
                'time_limit' => 180,
                'order' => 2,
            ],
            [
                'question' => 'How do you approach feature selection and model evaluation in machine learning projects?',
                'time_limit' => 150,
                'order' => 3,
            ],
            [
                'question' => 'What programming languages and tools do you use for data analysis and machine learning? Why?',
                'time_limit' => 120,
                'order' => 4,
            ],
        ];

        // Assign questions to jobs based on job titles
        foreach ($jobs as $job) {
            $questions = [];
            
            switch ($job->title) {
                case 'Senior Laravel Developer':
                    $questions = $laravelQuestions;
                    break;
                case 'Frontend Developer (React)':
                    $questions = $reactQuestions;
                    break;
                case 'Full Stack Developer':
                    $questions = $fullStackQuestions;
                    break;
                case 'DevOps Engineer':
                    $questions = $devopsQuestions;
                    break;
                case 'UI/UX Designer':
                    $questions = $designQuestions;
                    break;
                case 'Data Scientist':
                    $questions = $dataScienceQuestions;
                    break;
            }

            foreach ($questions as $question) {
                Question::firstOrCreate(
                    [
                        'job_id' => $job->id,
                        'question' => $question['question'],
                    ],
                    [
                        'time_limit' => $question['time_limit'],
                        'order' => $question['order'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('Questions seeded successfully!');
        $this->command->info('Created questions for all job positions');
    }
}
