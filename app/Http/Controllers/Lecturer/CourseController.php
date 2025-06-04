<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('view-lecturer-courses')) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $lecturer = Auth::user();
        $courses = $lecturer->teachingCourses()->orderBy('name')->paginate(15);
        return view('lecturer.courses.index', compact('courses')); // Placeholder
    }

    public function show(Course $course)
    {
        // Đảm bảo giảng viên này dạy lớp này
        if (Gate::denies('mark-attendance', $course)) { // Dùng gate này vì nó kiểm tra lecturer_id
            abort(403);
        }
        $course->load('students'); // Eager load students
        return view('lecturer.courses.show', compact('course')); // Placeholder
    }
}