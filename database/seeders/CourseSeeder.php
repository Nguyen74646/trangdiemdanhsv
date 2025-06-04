<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::truncate();

        $lecturerA = User::where('email', 'giangvien.a@example.com')->first();
        $lecturerB = User::where('email', 'giangvien.b@example.com')->first();

        $student1 = User::where('student_id_code', 'SV001')->first();
        $student2 = User::where('student_id_code', 'SV002')->first();
        $student3 = User::where('student_id_code', 'SV003')->first();

        if ($lecturerA && $student1 && $student2) {
            Course::create([
                'name' => 'Lập trình Web Nâng Cao',
                'code' => 'WEBNC2023',
                'lecturer_id' => $lecturerA->getKey(),
                'student_ids' => [$student1->getKey(), $student2->getKey()],
                'semester' => 'Học Kỳ 1 2023-2024',
                'schedule_details' => [
                    ['day_of_week' => 'Monday', 'start_time' => '07:00', 'end_time' => '09:30', 'session_name' => 'Tiết 1-3'],
                    ['day_of_week' => 'Wednesday', 'start_time' => '07:00', 'end_time' => '09:30', 'session_name' => 'Tiết 1-3'],
                ]
            ]);
        }

        if ($lecturerB && $student2 && $student3) {
             Course::create([
                'name' => 'Cơ sở dữ liệu MongoDB',
                'code' => 'MONGO2023',
                'lecturer_id' => $lecturerB->getKey(),
                'student_ids' => [$student2->getKey(), $student3->getKey()],
                'semester' => 'Học Kỳ 1 2023-2024',
                'schedule_details' => [
                    ['day_of_week' => 'Tuesday', 'start_time' => '13:00', 'end_time' => '15:30', 'session_name' => 'Tiết 7-9'],
                    ['day_of_week' => 'Thursday', 'start_time' => '13:00', 'end_time' => '15:30', 'session_name' => 'Tiết 7-9'],
                ]
            ]);
        }
    }
}