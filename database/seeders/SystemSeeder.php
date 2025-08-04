<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\HkiSubmission;

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
            ['name' => 'Fakultas Ilmu Komputer', 'code' => 'FILKOM'],
            ['name' => 'Fakultas Teknik', 'code' => 'FT'],
            ['name' => 'Fakultas Ekonomi', 'code' => 'FE'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['code' => $dept['code']], $dept);
        }

        // Create users - ✅ CHANGED: Password default = NIDN
        $users = [
            // Super Admin (special case - keep custom password)
            [
                'nidn' => '1234567890',
                'nama' => 'Super Admin HKI',
                'username' => 'admin',
                'email' => 'admin@amikom.ac.id',
                'password' => Hash::make('admin123'), // Keep custom password for admin
                'program_studi' => 'S1 Informatika',
                'foto' => 'default.png',
                'role' => 'admin',
                'phone' => '081234567890',
                'department_id' => 1,
                'is_active' => true,
            ],
            // Regular Users (Dosen) - ✅ Password = NIDN
            [
                'nidn' => '0987654321',
                'nama' => 'Dr. Dosen Satu',
                'username' => 'dosen1',
                'email' => 'dosen1@amikom.ac.id',
                'password' => Hash::make('0987654321'), // ✅ Password = NIDN
                'program_studi' => 'S1 Informatika',
                'foto' => 'default.png',
                'role' => 'user',
                'phone' => '081234567891',
                'department_id' => 1,
                'is_active' => true,
            ],
            [
                'nidn' => '1122334455',
                'nama' => 'Dr. Dosen Dua',
                'username' => 'dosen2',
                'email' => 'dosen2@amikom.ac.id',
                'password' => Hash::make('1122334455'), // ✅ Password = NIDN
                'program_studi' => 'S1 Sistem Informasi',
                'foto' => 'default.png',
                'role' => 'user',
                'phone' => '081234567892',
                'department_id' => 2,
                'is_active' => true,
            ],
            [
                'nidn' => '5566778899',
                'nama' => 'Prof. Dosen Tiga',
                'username' => 'dosen3',
                'email' => 'dosen3@amikom.ac.id',
                'password' => Hash::make('5566778899'), // ✅ Password = NIDN
                'program_studi' => 'D3 Manajemen Informatika',
                'foto' => 'default.png',
                'role' => 'user',
                'phone' => '081234567893',
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

        
    }
}
