<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin user if not exists
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        // Create recruiter users
        $recruiters = [
            [
                'name' => 'John Smith',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@example.com',
                'password' => Hash::make('password'),
                'role' => 'recruiter',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sarah Johnson',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@example.com',
                'password' => Hash::make('password'),
                'role' => 'recruiter',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Michael Brown',
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email' => 'michael.brown@example.com',
                'password' => Hash::make('password'),
                'role' => 'recruiter',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($recruiters as $recruiter) {
            if (!User::where('email', $recruiter['email'])->exists()) {
                User::create($recruiter);
            }
        }

        // Create candidate users
        $candidates = [
            [
                'name' => 'Emma Wilson',
                'first_name' => 'Emma',
                'last_name' => 'Wilson',
                'email' => 'emma.wilson@example.com',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'James Davis',
                'first_name' => 'James',
                'last_name' => 'Davis',
                'email' => 'james.davis@example.com',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Olivia Miller',
                'first_name' => 'Olivia',
                'last_name' => 'Miller',
                'email' => 'olivia.miller@example.com',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'William Garcia',
                'first_name' => 'William',
                'last_name' => 'Garcia',
                'email' => 'william.garcia@example.com',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Sophia Martinez',
                'first_name' => 'Sophia',
                'last_name' => 'Martinez',
                'email' => 'sophia.martinez@example.com',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Benjamin Anderson',
                'first_name' => 'Benjamin',
                'last_name' => 'Anderson',
                'email' => 'benjamin.anderson@example.com',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Isabella Taylor',
                'first_name' => 'Isabella',
                'last_name' => 'Taylor',
                'email' => 'isabella.taylor@example.com',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Lucas Thomas',
                'first_name' => 'Lucas',
                'last_name' => 'Thomas',
                'email' => 'lucas.thomas@example.com',
                'password' => Hash::make('password'),
                'role' => 'candidate',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($candidates as $candidate) {
            if (!User::where('email', $candidate['email'])->exists()) {
                User::create($candidate);
            }
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Recruiters: john.smith@example.com, sarah.johnson@example.com, michael.brown@example.com / password');
        $this->command->info('Candidates: 8 candidates created with email format firstname.lastname@example.com / password');
    }
}
