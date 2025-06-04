@extends('layouts.app')

@section('title', 'Lịch sử Điểm danh Lớp: ' . $course->name)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Lịch sử Điểm danh: <strong>{{ $course->name }} ({{ $course->code }})</strong></span>
        <a href="{{ route('lecturer.attendance.mark_form', $course) }}" class="btn btn-primary btn-sm">Điểm danh mới</a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('lecturer.attendance.history', $course) }}" class="mb-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter_date" class="form-label">Lọc theo ngày</label>
                    <input type="date" name="filter_date" id="filter_date" class="form-control form-control-sm" value="{{ request('filter_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="filter_month" class="form-label">Lọc theo tháng</label>
                    <input type="month" name="filter_month" id="filter_month" class="form-control form-control-sm" value="{{ request('filter_month') }}">
                </div>
                <div class="col-md-3">
                    <label for="filter_student_id" class="form-label">Lọc theo sinh viên</label>
                    <select name="filter_student_id" id="filter_student_id" class="form-select form-select-sm">
                        <option value="">Tất cả sinh viên</option>
                        @foreach($course->students as $student)
                            <option value="{{ $student->id }}" {{ request('filter_student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->student_id_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-info btn-sm w-100">Lọc</button>
                    <a href="{{ route('lecturer.attendance.history', $course) }}" class="btn btn-secondary btn-sm w-100 mt-1">Reset</a>
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
                            <th>Sinh viên</th>
                            <th>Mã SV</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                            <th>Người điểm danh</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $att)
                        <tr>
                            <td>{{ $att->attendance_date->format('d/m/Y') }}</td>
                            <td>{{ $att->session }}</td>
                            <td>{{ $att->student->name ?? 'N/A' }}</td>
                            <td>{{ $att->student->student_id_code ?? 'N/A' }}</td>
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
                            <td>
                                @can('mark-attendance', $course) {{-- Hoặc kiểm tra cụ thể hơn nếu cần --}}
                                <a href="{{ route('lecturer.attendance.edit', [$course, $att->id]) }}" class="btn btn-warning btn-sm" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $attendances->appends(request()->query())->links() }}
        @else
            <p class="text-center">Không có dữ liệu điểm danh nào khớp với tiêu chí lọc.</p>
        @endif
    </div>
    <div class="card-footer">
         <a href="{{ route('lecturer.courses.show', $course) }}" class="btn btn-light">Quay lại chi tiết lớp</a>
    </div>
</div>
@endsection