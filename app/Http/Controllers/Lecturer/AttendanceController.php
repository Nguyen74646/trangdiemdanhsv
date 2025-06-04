<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AttendanceController extends Controller
{
    public function showMarkingForm(Course $course, Request $request)
    {
        if (Gate::denies('mark-attendance', $course)) {
            abort(403);
        }

        // Lấy danh sách sinh viên của lớp học (tránh loadMissing vì MongoDB không hỗ trợ Eager Loading như Eloquent MySQL)
        $students = User::whereIn('_id', $course->student_ids ?? [])
            ->where('role', 'sinhvien')
            ->get();

        $attendanceDate = $request->input('attendance_date') ? Carbon::parse($request->input('attendance_date')) : Carbon::today();
        $session = $request->input('session', '');

        $existingAttendance = Attendance::where('course_id', $course->getKey())
            ->where('attendance_date', $attendanceDate->format('Y-m-d'))
            ->where('session', $session)
            ->get()
            ->keyBy('student_id');

        return view('lecturer.attendance.mark_form', compact('course', 'students', 'attendanceDate', 'session', 'existingAttendance'));
    }

    public function storeAttendance(Request $request, Course $course)
    {
        if (Gate::denies('mark-attendance', $course)) {
            abort(403);
        }

        $validated = $request->validate([
            'attendance_date' => 'required|date_format:Y-m-d',
            'session' => 'required|string|max:100',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,_id',
            'attendances.*.status' => 'required|in:present,absent,late,permitted_absence',
            'attendances.*.notes' => 'nullable|string|max:255',
        ]);

        $lecturerId = Auth::id();
        $attendanceDate = Carbon::parse($validated['attendance_date']);

        DB::beginTransaction();
        try {
            foreach ($validated['attendances'] as $studentAttendance) {
                Attendance::updateOrCreate(
                    [
                        'course_id' => $course->getKey(),
                        'student_id' => $studentAttendance['student_id'],
                        'attendance_date' => $attendanceDate->format('Y-m-d'),
                        'session' => $validated['session'],
                    ],
                    [
                        'status' => $studentAttendance['status'],
                        'notes' => $studentAttendance['notes'] ?? null,
                        'marked_by_id' => $lecturerId,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('lecturer.attendance.history', $course->id)
                ->with('success', 'Điểm danh thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing attendance: ' . $e->getMessage(), [
                'course_id' => $course->id,
                'request_data' => $request->all()
            ]);
            return back()->withInput()->with('error', 'Lỗi khi điểm danh: ' . $e->getMessage());
        }
    }

    public function attendanceHistory(Course $course, Request $request)
    {
        if (Gate::denies('view-course-attendance-history', $course)) {
            abort(403);
        }

        $query = Attendance::query()
            ->where('course_id', $course->getKey())
            ->orderBy('attendance_date', 'desc')
            ->orderBy('session', 'asc');

        if ($request->filled('filter_date')) {
            $filterDate = Carbon::parse($request->filter_date)->format('Y-m-d');
            $query->where('attendance_date', $filterDate);
        }

        if ($request->filled('filter_student_id')) {
            $query->where('student_id', $request->filter_student_id);
        }

        if ($request->filled('filter_month')) {
            $monthYear = Carbon::parse($request->filter_month);
            $query->whereBetween('attendance_date', [
                $monthYear->copy()->startOfMonth()->format('Y-m-d'),
                $monthYear->copy()->endOfMonth()->format('Y-m-d'),
            ]);
        }

        $attendances = $query->paginate(30)->appends($request->query());

        // Lấy danh sách sinh viên của lớp học để lọc trong view
        $studentsInCourse = User::whereIn('_id', $course->student_ids ?? [])->where('role', 'sinhvien')->get();

        return view('lecturer.attendance.history', compact('course', 'attendances', 'studentsInCourse'));
    }

    public function editAttendance(Course $course, $attendance_id)
    {
        $attendance = Attendance::findOrFail($attendance_id);

        if (Gate::denies('mark-attendance', $course) || $attendance->course_id !== $course->getKey()) {
            abort(403, "Bạn không có quyền sửa điểm danh này hoặc điểm danh không thuộc lớp học này.");
        }

        // Lấy dữ liệu sinh viên liên quan (không dùng loadMissing)
        $student = User::find($attendance->student_id);

        return view('lecturer.attendance.edit_form', compact('course', 'attendance', 'student'));
    }

    public function updateAttendance(Request $request, Course $course, $attendance_id)
    {
        $attendance = Attendance::findOrFail($attendance_id);

        if (Gate::denies('mark-attendance', $course) || $attendance->course_id !== $course->getKey()) {
            abort(403, "Bạn không có quyền sửa điểm danh này hoặc điểm danh không thuộc lớp học này.");
        }

        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,permitted_absence',
            'notes' => 'nullable|string|max:255',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'marked_by_id' => Auth::id(),
        ]);

        return redirect()->route('lecturer.attendance.history', $course->id)
            ->with('success', 'Cập nhật điểm danh thành công.');
    }
}
