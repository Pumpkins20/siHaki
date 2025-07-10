<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\HkiSubmission;
use Illuminate\Support\Facades\Hash;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create departments first
        $departments = [
            ['name' => 'Teknik Informatika', 'code' => 'TI'],
            ['name' => 'Sistem Informasi', 'code' => 'SI'],
            ['name' => 'Manajemen Informatika', 'code' => 'MI'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }

        // Create users
        $users = [
            [
                'nidn' => '1234567890',
                'nama' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@amikom.ac.id',
                'password' => Hash::make('password123'),
                'program_studi' => 'S1 Informatika',
                'foto' => 'default.png',
                'role' => 'admin',
                'phone' => '081234567890',
                'department_id' => 1,
                'is_active' => true,
            ],
            [
                'nidn' => '0987654321',
                'nama' => 'Dr. Reviewer Utama',
                'username' => 'reviewer1',
                'email' => 'reviewer1@amikom.ac.id',
                'password' => Hash::make('password123'),
                'program_studi' => 'S1 Sistem Informasi', // Sekarang sudah sesuai dengan enum
                'foto' => 'default.png',
                'role' => 'reviewer',
                'phone' => '081234567891',
                'department_id' => 2,
                'is_active' => true,
            ],
            [
                'nidn' => '1122334455',
                'nama' => 'User Dosen',
                'username' => 'user1',
                'email' => 'user1@amikom.ac.id',
                'password' => Hash::make('password123'),
                'program_studi' => 'D3 Manajemen Informatika',
                'foto' => 'default.png',
                'role' => 'user',
                'phone' => '081234567892',
                'department_id' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Create sample submissions
        $submissions = [
            [
                'user_id' => 3,
                'title' => 'Sistem Informasi Akademik Terintegrasi',
                'type' => 'copyright',
                'description' => 'Sistem informasi untuk mengelola data akademik mahasiswa dan dosen secara terintegrasi.',
                'status' => 'submitted',
                'submission_date' => now(),
            ],
            [
                'user_id' => 3,
                'title' => 'Aplikasi Mobile Learning Berbasis Android',
                'type' => 'copyright',
                'description' => 'Aplikasi mobile untuk pembelajaran online berbasis platform Android.',
                'status' => 'under_review',
                'submission_date' => now()->subDays(5),
                'reviewer_id' => 2,
            ],
        ];

        foreach ($submissions as $submissionData) {
            HkiSubmission::firstOrCreate(
                ['title' => $submissionData['title']],
                $submissionData
            );
        }
    }
}
