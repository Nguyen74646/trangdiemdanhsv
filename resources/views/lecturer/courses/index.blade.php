@extends('layouts.app')

@section('title', 'Danh sách Lớp học của tôi')

@section('content')
<div class="card">
    <div class="card-header">
        Danh sách Lớp học bạn đang giảng dạy
    </div>
    <div class="card-body">
        @if($courses->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên Lớp học</th>
                            <th>Mã Lớp</th>
                            <th>Học kỳ</th>
                            <th>Số Sinh viên</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $index => $course)
                        <tr>
                            <td>{{ $courses->firstItem() + $index }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->code }}</td>
                            <td>{{ $course->semester ?? 'N/A' }}</td>
                            <td>{{ count($course->student_ids ?? []) }}</td>
                            <td>
                                <a href="{{ route('lecturer.courses.show', $course) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i> Chi tiết
                                </a>
                                <a href="{{ route('lecturer.attendance.mark_form', $course) }}" class="btn btn-sm btn-primary" title="Điểm danh">
                                    <i class="fas fa-user-check"></i> Điểm danh
                                </a>
                                <a href="{{ route('lecturer.attendance.history', $course) }}" class="btn btn-sm btn-secondary" title="Lịch sử điểm danh">
                                    <i class="fas fa-history"></i> Lịch sử
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $courses->links() }}
        @else
            <p class="text-center">Bạn chưa được phân công giảng dạy lớp học nào.</p>
        @endif
    </div>
</div>
@endsection