@extends('layouts.admin')

@php
    $pageTitle = 'Chi tiết Lớp học: ' . $course->name;
@endphp

@section('admin_content')
<div class="container-fluid">
    <div class="card shadow mb-3">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Chi tiết Lớp học: {{ $course->name }} ({{ $course->code }})</h6>
            <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit fa-sm"></i> Sửa thông tin lớp
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Giảng viên:</strong> {{ $course->lecturer->name ?? 'N/A' }}</p>
                    <p><strong>Email Giảng viên:</strong> {{ $course->lecturer->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Học kỳ:</strong> {{ $course->semester ?? 'N/A' }}</p>
                    <p><strong>Ngày tạo:</strong> {{ $course->created_at ? $course->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                </div>
            </div>
            <hr>
            <p><strong>Lịch học:</strong></p>
            @if(!empty($course->schedule_details))
                <ul class="list-unstyled">
                @foreach($course->schedule_details as $schedule)
                    <li>
                        <i class="far fa-calendar-alt me-1 text-info"></i>
                        <strong>{{ $schedule['day_of_week'] ?? 'N/A' }}</strong>:
                        <i class="far fa-clock me-1 text-success"></i>
                        {{ $schedule['start_time'] ?? 'N/A' }} - {{ $schedule['end_time'] ?? 'N/A' }}
                        <em>({{ $schedule['session_name'] ?? 'N/A' }})</em>
                    </li>
                @endforeach
                </ul>
            @else
                <p class="text-muted">Chưa có lịch học chi tiết.</p>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quản lý Sinh viên trong lớp</h6>
        </div>
        <div class="card-body">
            <h6>Danh sách sinh viên hiện tại ({{ $studentsInCourse->count() }})</h6>
            @if($studentsInCourse->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Mã SV</th>
                            <th>Tên SV</th>
                            <th>Email</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentsInCourse as $student)
                        <tr>
                            <td>{{ $student->student_id_code }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>
                                <form action="{{ route('admin.courses.remove_student', [$course->id, $student->id]) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sinh viên này khỏi lớp?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-user-minus"></i> Xóa khỏi lớp</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">Chưa có sinh viên nào trong lớp.</p>
            @endif

            <hr class="my-4">
            <h6>Thêm sinh viên vào lớp</h6>
            <form action="{{ route('admin.courses.add_student', $course->id) }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-8">
                    <label for="student_id" class="form-label">Chọn sinh viên</label>
                    <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                        <option value="">-- Chọn sinh viên --</option>
                        @foreach($allStudents as $studentOption) {{-- Đổi tên biến để không trùng $student bên trên --}}
                            <option value="{{ $studentOption->id }}">{{ $studentOption->name }} ({{ $studentOption->student_id_code }})</option>
                        @endforeach
                    </select>
                    @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-user-plus"></i> Thêm vào lớp</button>
                </div>
            </form>
            @if($allStudents->isEmpty())
                <p class="text-muted mt-2 small">Không còn sinh viên nào để thêm (tất cả sinh viên có vai trò 'sinhvien' đã ở trong lớp này hoặc không có sinh viên nào trong hệ thống).</p>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Quay lại danh sách lớp</a>
        </div>
    </div>
</div>
@endsection