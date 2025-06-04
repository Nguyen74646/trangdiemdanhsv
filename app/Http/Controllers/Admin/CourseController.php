<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    public function __construct()
    {
        // Áp dụng Gate 'manage-courses'
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('manage-courses')) {
                abort(403);
            }
            return $next($request);
        })->except(['show']); // Cho phép giảng viên xem chi tiết lớp nếu được gán quyền sau
    }

    public function index(Request $request)
    {
        $query = Course::with('lecturer');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        $courses = $query->orderBy('name')->paginate(15);
        return view('admin.courses.index', compact('courses')); // Placeholder
    }

    public function create()
    {
        $lecturers = User::where('role', 'giangvien')->orderBy('name')->get();
        return view('admin.courses.create', compact('lecturers')); // Placeholder
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:courses,code',
            'lecturer_id' => 'required|exists:users,_id,role,giangvien',
            'semester' => 'nullable|string|max:100',
            'schedule_details' => 'nullable|array',
            'schedule_details.*.day_of_week' => 'required_with:schedule_details|string',
            'schedule_details.*.start_time' => 'required_with:schedule_details|date_format:H:i',
            'schedule_details.*.end_time' => 'required_with:schedule_details|date_format:H:i|after:schedule_details.*.start_time',
            'schedule_details.*.session_name' => 'nullable|string',
        ]);

        Course::create($validated);
        return redirect()->route('admin.courses.index')->with('success', 'Tạo lớp học thành công.');
    }

    public function show(Course $course)
    {
        $course->load('lecturer', 'students'); // Eager load
        $studentsInCourse = $course->students;
        $allStudents = User::where('role', 'sinhvien')
                            ->whereNotIn('_id', $course->student_ids ?? []) // Loại trừ SV đã có trong lớp
                            ->orderBy('name')->get();
        return view('admin.courses.show', compact('course', 'studentsInCourse', 'allStudents')); // Placeholder
    }

    public function edit(Course $course)
    {
        $lecturers = User::where('role', 'giangvien')->orderBy('name')->get();
        return view('admin.courses.edit', compact('course', 'lecturers')); // Placeholder
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('courses')->ignore($course->id)],
            'lecturer_id' => 'required|exists:users,_id,role,giangvien',
            'semester' => 'nullable|string|max:100',
            'schedule_details' => 'nullable|array',
            'schedule_details.*.day_of_week' => 'required_with:schedule_details|string',
            'schedule_details.*.start_time' => 'required_with:schedule_details|date_format:H:i',
            'schedule_details.*.end_time' => 'required_with:schedule_details|date_format:H:i|after:schedule_details.*.start_time',
            'schedule_details.*.session_name' => 'nullable|string',
        ]);

        $course->update($validated);
        return redirect()->route('admin.courses.index')->with('success', 'Cập nhật lớp học thành công.');
    }

    public function destroy(Course $course)
    {
        // Cân nhắc: Nếu có điểm danh liên quan, có thể không cho xóa hoặc cần xử lý phức tạp hơn
        if ($course->attendances()->exists()) {
            return redirect()->route('admin.courses.index')->with('error', 'Không thể xóa lớp học đã có dữ liệu điểm danh. Hãy xem xét việc ẩn hoặc lưu trữ lớp học.');
        }
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Xóa lớp học thành công.');
    }

    // Quản lý sinh viên trong lớp
    public function manageStudents(Course $course)
    {
        // This can be part of show method or a separate view
        $course->load('students');
        $studentsNotInCourse = User::where('role', 'sinhvien')
                                   ->whereNotIn('_id', $course->student_ids ?? [])
                                   ->orderBy('name')
                                   ->get();
        return view('admin.courses.manage_students', compact('course', 'studentsNotInCourse')); // Placeholder
    }

    public function addStudentToCourse(Request $request, Course $course)
    {
        $request->validate([
            'student_id' => 'required|exists:users,_id,role,sinhvien',
        ]);

        $studentId = $request->input('student_id');
        if (!in_array($studentId, $course->student_ids ?? [])) {
            $course->push('student_ids', $studentId, true); // true để đảm bảo unique
        }
        return back()->with('success', 'Thêm sinh viên vào lớp thành công.');
    }

    public function removeStudentFromCourse(Course $course, $studentId)
    {
        $student = User::findOrFail($studentId); // Đảm bảo student tồn tại
        if ($student->role !== 'sinhvien') {
            return back()->with('error', 'ID không phải của sinh viên.');
        }

        $course->pull('student_ids', $studentId);
        // Cân nhắc: Xóa các bản ghi điểm danh của SV này trong lớp này? Hay giữ lại?
        // Attendance::where('course_id', $course->getKey())->where('student_id', $studentId)->delete();

        return back()->with('success', 'Xóa sinh viên khỏi lớp thành công.');
    }
}