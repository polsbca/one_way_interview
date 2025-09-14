<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobs = [
            [
                'title' => 'Senior Laravel Developer',
                'description' => 'We are looking for an experienced Laravel developer to join our growing team. You will be responsible for developing and maintaining web applications using the Laravel framework, working with a team of developers to create high-quality, scalable web solutions.',
                'requirements' => 'Bachelor\'s degree in Computer Science or related field. 5+ years of experience with PHP and Laravel. Strong understanding of MVC design patterns. Experience with RESTful APIs. Knowledge of front-end technologies (HTML, CSS, JavaScript). Familiarity with version control systems (Git). Excellent problem-solving skills.',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Frontend Developer (React)',
                'description' => 'Join our dynamic team as a Frontend Developer specializing in React. You will be responsible for creating engaging user interfaces and implementing responsive web designs that work seamlessly across all devices.',
                'requirements' => '3+ years of experience with React and modern JavaScript. Strong understanding of HTML5, CSS3, and JavaScript ES6+. Experience with state management libraries (Redux, Context API). Knowledge of responsive design principles. Familiarity with build tools (Webpack, Babel). Experience with testing frameworks (Jest, React Testing Library). Bachelor\'s degree in Computer Science or related field.',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Full Stack Developer',
                'description' => 'We are seeking a talented Full Stack Developer to work on our cutting-edge web applications. You will be involved in both frontend and backend development, ensuring seamless integration between user interfaces and server-side logic.',
                'requirements' => '4+ years of experience in full stack development. Proficiency in both frontend and backend technologies. Experience with Laravel, React, and Node.js. Strong understanding of database design and optimization. Knowledge of cloud services (AWS, Azure). Experience with version control and CI/CD pipelines. Excellent communication and teamwork skills.',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'Looking for a DevOps Engineer to help us streamline our development and deployment processes. You will be responsible for managing our cloud infrastructure, implementing CI/CD pipelines, and ensuring the reliability and scalability of our applications.',
                'requirements' => '3+ years of experience in DevOps or related roles. Experience with cloud platforms (AWS, Azure, GCP). Knowledge of containerization technologies (Docker, Kubernetes). Familiarity with CI/CD tools (Jenkins, GitLab CI). Experience with infrastructure as code (Terraform, CloudFormation). Strong understanding of networking and security principles. Bachelor\'s degree in Computer Science or related field.',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'UI/UX Designer',
                'description' => 'We are looking for a creative UI/UX Designer to join our design team. You will be responsible for creating intuitive and visually appealing user interfaces for our web and mobile applications.',
                'requirements' => '3+ years of experience in UI/UX design. Proficiency in design tools (Figma, Sketch, Adobe XD). Strong understanding of user-centered design principles. Experience with prototyping and user testing. Knowledge of front-end development basics. Excellent communication and presentation skills. Portfolio showcasing design projects.',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Data Scientist',
                'description' => 'Join our data team as a Data Scientist to help us derive insights from complex datasets and build machine learning models. You will work on various projects involving data analysis, predictive modeling, and statistical analysis.',
                'requirements' => 'Master\'s degree in Data Science, Statistics, Computer Science, or related field. 2+ years of experience in data science or analytics. Proficiency in Python and R. Experience with machine learning frameworks (scikit-learn, TensorFlow, PyTorch). Strong statistical analysis skills. Experience with data visualization tools (Tableau, Power BI). Knowledge of SQL and database systems.',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($jobs as $job) {
            Job::firstOrCreate(
                ['title' => $job['title']],
                $job
            );
        }

        $this->command->info('Jobs seeded successfully!');
        $this->command->info('Created 6 job positions (4 published, 2 draft)');
    }
}
