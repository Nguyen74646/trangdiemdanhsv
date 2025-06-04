@extends('layouts.app')

@section('title', 'Các lớp học của tôi')

@section('content')
<div class="card">
    <div class="card-header">
        Danh sách các lớp học bạn đã đăng ký
    </div>
    <div class="card-body">
        @if($courses->isNotEmpty())
            <div class="list-group">
                @foreach ($courses as $course)
                    <a href="{{ route('student.attendance.my', ['course_id' => $course->id]) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $course->name }} ({{ $course->code }})</h5>
                            <small class="text-muted">{{ $course->semester ?? 'N/A' }}</small>
                        </div>
                        <p class="mb-1">Giảng viên: {{ $course->lecturer->name ?? 'N/A' }}</p>
                        <small class="text-muted">Nhấn để xem lịch sử điểm danh của bạn cho lớp này.</small>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-center">Bạn chưa đăng ký tham gia lớp học nào.</p>
        @endif
    </div>
    <div class="card-footer">
        <a href="{{ route('student.attendance.my') }}" class="btn btn-info">Xem tất cả lịch sử điểm danh</a>
    </div>
</div>
@endsection