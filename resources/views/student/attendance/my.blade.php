@extends('layouts.app')

@section('title', 'Lịch sử Điểm danh của tôi' . ($selectedCourse ? ' - Lớp: ' . $selectedCourse->name : ''))

@section('content')
<div class="card">
    <div class="card-header">
        Lịch sử Điểm danh của tôi
        @if($selectedCourse)
            - Lớp: <strong>{{ $selectedCourse->name }} ({{ $selectedCourse->code }})</strong>
        @endif
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('student.attendance.my', ['course_id' => $selectedCourse->id ?? null]) }}" class="mb-3">
            <div class="row g-3 align-items-end">
                @if($enrolledCourses->isNotEmpty() && !$selectedCourse) {{-- Chỉ hiện filter lớp nếu không có course_id từ route --}}
                <div class="col-md-4">
                    <label for="filter_course_id" class="form-label">Lọc theo lớp học</label>
                    <select name="course_id" id="filter_course_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tất cả các lớp</option>
                        @foreach($enrolledCourses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id || (isset($selectedCourse) && $selectedCourse->id == $course->id) ? 'selected' : '' }}>
                                {{ $course->name }} ({{ $course->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @elseif ($selectedCourse)
                    <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
                @endif

                <div class="col-md-3">
                    <label for="filter_month" class="form-label">Lọc theo tháng</label>
                    <input type="month" name="filter_month" id="filter_month" class="form-control form-control-sm" value="{{ request('filter_month') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info btn-sm w-100">Lọc</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('student.attendance.my', ['course_id' => $selectedCourse->id ?? null]) }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                </div>
            </div>
        </form>

        @if($attendances->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Buổi/Tiết</th>
                            @if(!$selectedCourse) <th>Lớp học</th> @endif
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                            <th>Người điểm danh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $att)
                        <tr>
                            <td>{{ $att->attendance_date->format('d/m/Y') }}</td>
                            <td>{{ $att->session }}</td>
                            @if(!$selectedCourse)<td>{{ $att->course->name ?? 'N/A' }}</td>@endif
                            <td>
                                @if($att->status == 'present') <span class="badge bg-success">Có mặt</span>
                                @elseif($att->status == 'absent') <span class="badge bg-danger">Vắng</span>
                                @elseif($att->status == 'late') <span class="badge bg-warning text-dark">Trễ</span>
                                @elseif($att->status == 'permitted_absence') <span class="badge bg-info text-dark">Vắng có phép</span>
                                @else {{ ucfirst($att->status) }}
                                @endif
                            </td>
                            <td>{{ $att->notes }}</td>
                            <td>{{ $att->marker->name ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $attendances->appends(request()->query())->links() }}
        @else
            <p class="text-center">Không có dữ liệu điểm danh nào của bạn khớp với tiêu chí lọc.</p>
        @endif
    </div>
     <div class="card-footer">
        @if($selectedCourse)
        <a href="{{ route('student.attendance.my') }}" class="btn btn-light">Xem điểm danh tất cả các lớp</a>
        @endif
        <a href="{{ route('student.my_courses') }}" class="btn btn-outline-secondary">Quay lại danh sách lớp</a>
    </div>
</div>
@endsection