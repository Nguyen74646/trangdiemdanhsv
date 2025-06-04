<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('view-student-attendance')) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function myCourses()
    {
        $student = Auth::user();
        $courses = $student->enrolledCourses()->load('lecturer'); // Lấy các lớp SV đã đăng ký
        return view('student.my_courses', compact('courses')); // Placeholder
    }

    public function myAttendance(Request $request, $course_id = null)
    {
        $student = Auth::user();
        $query = $student->attendances()->with('course', 'marker')
                        ->orderBy('attendance_date', 'desc')->orderBy('session');

        $selectedCourse = null;
        if ($course_id) {
            $selectedCourse = Course::findOrFail($course_id);
            // Kiểm tra xem sinh viên có thuộc lớp này không
            if (!in_array($student->getKey(), $selectedCourse->student_ids ?? [])) {
                 abort(403, 'Bạn không có quyền xem điểm danh của lớp này.');
            }
            $query->where('course_id', $course_id);
        }

        // Lọc theo tháng
        if ($request->filled('filter_month')) {
            $monthYear = Carbon::parse($request->filter_month);
            $query->whereBetween('attendance_date', [
                $monthYear->copy()->startOfMonth()->format('Y-m-d'),
                $monthYear->copy()->endOfMonth()->format('Y-m-d')
            ]);
        }

        $attendances = $query->paginate(30);
        $enrolledCourses = $student->enrolledCourses(); // Để student có thể lọc theo lớp

        return view('student.attendance.my', compact('attendances', 'selectedCourse', 'enrolledCourses')); // Placeholder
    }
}