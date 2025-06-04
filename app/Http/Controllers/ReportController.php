<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Cho raw query
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('view-attendance-reports')) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function attendanceReport(Request $request)
    {
        $user = Auth::user();
        $courses = Course::orderBy('name')->get();
        $students = User::where('role', 'sinhvien')->orderBy('name')->get(); // Dùng cho admin filter

        // Default date range (current month)
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();

        // Base query using MongoDB aggregation
        $pipeline = [];

        // Match stage for date range
        $pipeline[] = [
            '$match' => [
                'attendance_date' => [
                    '$gte' => new \MongoDB\BSON\UTCDateTime($startDate->copy()->startOfDay()->getTimestamp() * 1000),
                    '$lte' => new \MongoDB\BSON\UTCDateTime($endDate->copy()->endOfDay()->getTimestamp() * 1000),
                ]
            ]
        ];

        // Filter by course (if provided)
        if ($request->filled('course_id')) {
            $pipeline[] = ['$match' => ['course_id' => new \MongoDB\BSON\ObjectId($request->input('course_id'))]];
        } elseif ($user->role === 'giangvien') {
            // Giảng viên chỉ xem được các lớp mình dạy
             $lecturerCourseIds = $user->teachingCourses()->pluck('_id')->map(fn($id) => new \MongoDB\BSON\ObjectId($id))->toArray();
             if (empty($lecturerCourseIds)) { // Nếu GV không có lớp nào
                 $stats = collect();
                 return view('reports.attendance', compact('stats', 'courses', 'students', 'startDate', 'endDate', 'request'));
             }
             $pipeline[] = ['$match' => ['course_id' => ['$in' => $lecturerCourseIds]]];
        }


        // Filter by student (if provided and user is admin)
        if ($request->filled('student_id') && $user->role === 'admin') {
            $pipeline[] = ['$match' => ['student_id' => new \MongoDB\BSON\ObjectId($request->input('student_id'))]];
        }

        // Group stage to calculate stats
        $pipeline[] = [
            '$group' => [
                '_id' => [
                    'student_id' => '$student_id',
                    'course_id' => '$course_id',
                    // 'year' => ['$year' => '$attendance_date'], // Nếu muốn group theo năm, tháng...
                    // 'month' => ['$month' => '$attendance_date'],
                ],
                'total_sessions' => ['$sum' => 1],
                'present_sessions' => ['$sum' => ['$cond' => [['$eq' => ['$status', 'present']], 1, 0]]],
                'absent_sessions' => ['$sum' => ['$cond' => [['$eq' => ['$status', 'absent']], 1, 0]]],
                'late_sessions' => ['$sum' => ['$cond' => [['$eq' => ['$status', 'late']], 1, 0]]],
                'permitted_absence_sessions' => ['$sum' => ['$cond' => [['$eq' => ['$status', 'permitted_absence']], 1, 0]]],
            ]
        ];

        // Lookup student info
        $pipeline[] = [
            '$lookup' => [
                'from' => 'users',
                'localField' => '_id.student_id',
                'foreignField' => '_id',
                'as' => 'student_info'
            ]
        ];
        $pipeline[] = ['$unwind' => '$student_info'];

        // Lookup course info
        $pipeline[] = [
            '$lookup' => [
                'from' => 'courses',
                'localField' => '_id.course_id',
                'foreignField' => '_id',
                'as' => 'course_info'
            ]
        ];
        $pipeline[] = ['$unwind' => '$course_info'];

        // Project stage to reshape output
        $pipeline[] = [
            '$project' => [
                '_id' => 0,
                'student_id' => '$_id.student_id',
                'student_name' => '$student_info.name',
                'student_code' => '$student_info.student_id_code',
                'course_id' => '$_id.course_id',
                'course_name' => '$course_info.name',
                'course_code' => '$course_info.code',
                'total_sessions' => 1,
                'present_sessions' => 1,
                'absent_sessions' => 1,
                'late_sessions' => 1,
                'permitted_absence_sessions' => 1,
                'attendance_rate' => [
                    '$cond' => [
                        'if' => ['$gt' => ['$total_sessions', 0]],
                        'then' => [
                            '$multiply' => [
                                [
                                    '$divide' => [
                                        ['$add' => ['$present_sessions', '$late_sessions', '$permitted_absence_sessions']], // Coi trễ và vắng có phép là có đi học
                                        '$total_sessions'
                                    ]
                                ],
                                100
                            ]
                        ],
                        'else' => 0
                    ]
                ]
            ]
        ];

        // Sort
        $pipeline[] = ['$sort' => ['course_name' => 1, 'student_name' => 1]];


        $stats = Attendance::raw(function ($collection) use ($pipeline) {
            return $collection->aggregate($pipeline);
        });
        // $stats = collect($stats); // Chuyển Cursor thành Collection Laravel


        return view('reports.attendance', compact('stats', 'courses', 'students', 'startDate', 'endDate', 'request')); // Placeholder
    }
}