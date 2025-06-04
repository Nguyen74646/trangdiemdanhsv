<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa toàn bộ documents trong collection users
        User::raw(function ($collection) {
            return $collection->deleteMany([]);
        });

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Giang Vien A',
            'email' => 'giangvien.a@example.com',
            'password' => Hash::make('password'),
            'role' => 'giangvien',
        ]);

        User::create([
            'name' => 'Giang Vien B',
            'email' => 'giangvien.b@example.com',
            'password' => Hash::make('password'),
            'role' => 'giangvien',
        ]);

        User::create([
            'name' => 'Sinh Vien 001',
            'email' => 'sv001@example.com',
            'password' => Hash::make('password'),
            'role' => 'sinhvien',
            'student_id_code' => 'SV001',
        ]);

        User::create([
            'name' => 'Sinh Vien 002',
            'email' => 'sv002@example.com',
            'password' => Hash::make('password'),
            'role' => 'sinhvien',
            'student_id_code' => 'SV002',
        ]);

        User::create([
            'name' => 'Sinh Vien 003',
            'email' => 'sv003@example.com',
            'password' => Hash::make('password'),
            'role' => 'sinhvien',
            'student_id_code' => 'SV003',
        ]);
    }
}
