@extends('layouts.app')

@section('title', 'Báo cáo Điểm danh')

@section('content')
<div class="card">
    <div class="card-header">
        Báo cáo Điểm danh Tổng hợp
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.attendance') }}" class="mb-4 p-3 border rounded">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Từ ngày</label>
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ $request->input('start_date', $startDate->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Đến ngày</label>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ $request->input('end_date', $endDate->format('Y-m-d')) }}">
                </div>

                @if(Auth::user()->role == 'admin')
                <div class="col-md-3">
                    <label for="course_id" class="form-label">Lớp học (Admin)</label>
                    <select name="course_id" id="course_id" class="form-select form-select-sm">
                        <option value="">Tất cả các lớp</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $request->input('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }} ({{ $course->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="student_id" class="form-label">Sinh viên (Admin)</label>
                    <select name="student_id" id="student_id" class="form-select form-select-sm">
                        <option value="">Tất cả sinh viên</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ $request->input('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->student_id_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @elseif(Auth::user()->role == 'giangvien')
                 <div class="col-md-4">
                    <label for="course_id_lecturer" class="form-label">Lớp học của tôi</label>
                    <select name="course_id" id="course_id_lecturer" class="form-select form-select-sm">
                        <option value="">Tất cả lớp tôi dạy</option>
                        @foreach(Auth::user()->teachingCourses as $course)
                            <option value="{{ $course->id }}" {{ $request->input('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }} ({{ $course->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            <div class="row mt-2">
                 <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Xem Báo cáo</button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('reports.attendance') }}" class="btn btn-secondary btn-sm w-100">Reset bộ lọc</a>
                </div>
            </div>
        </form>

        @if($stats->count() > 0)
            <h5 class="mt-4">Kết quả báo cáo từ {{ $startDate->format('d/m/Y') }} đến {{ $endDate->format('d/m/Y') }}</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Lớp học</th>
                            <th>Mã SV</th>
                            <th>Tên Sinh viên</th>
                            <th>Tổng số buổi</th>
                            <th>Có mặt</th>
                            <th>Vắng</th>
                            <th>Trễ</th>
                            <th>Vắng P</th>
                            <th>Tỷ lệ CC (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stats as $stat)
                        <tr>
                            <td>{{ $stat['course_name'] ?? 'N/A' }} ({{ $stat['course_code'] ?? 'N/A' }})</td>
                            <td>{{ $stat['student_code'] ?? 'N/A' }}</td>
                            <td>{{ $stat['student_name'] ?? 'N/A' }}</td>
                            <td>{{ $stat['total_sessions'] ?? 0 }}</td>
                            <td>{{ $stat['present_sessions'] ?? 0 }}</td>
                            <td>{{ $stat['absent_sessions'] ?? 0 }}</td>
                            <td>{{ $stat['late_sessions'] ?? 0 }}</td>
                            <td>{{ $stat['permitted_absence_sessions'] ?? 0 }}</td>
                            <td>{{ number_format($stat['attendance_rate'] ?? 0, 2) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center mt-4">Không có dữ liệu thống kê nào cho khoảng thời gian và bộ lọc đã chọn.</p>
        @endif
    </div>
</div>
@endsection