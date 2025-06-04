@extends('layouts.app')

@section('title', 'Sinh viên Dashboard')

@section('content')
<div class="card">
    <div class="card-header">Sinh viên Dashboard</div>
    <div class="card-body">
        <h5 class="card-title">Chào mừng, Sinh viên {{ $user->name }}! (Mã SV: {{ $user->student_id_code ?? 'N/A' }})</h5>
        <p class="card-text">Đây là khu vực dành cho sinh viên.</p>
        <a href="{{ route('student.my_courses') }}" class="btn btn-primary">Xem các lớp học đã đăng ký</a>
        <a href="{{ route('student.attendance.my') }}" class="btn btn-info">Xem lịch sử điểm danh của tôi</a>
        {{-- Thêm các link chức năng khác cho sinh viên nếu có --}}
    </div>
</div>

<div class="mt-4">
    <h4>Thông báo hoặc Lịch học hôm nay:</h4>
    {{--  Hiển thị thông báo hoặc lịch học gần nhất của sinh viên --}}
    <p class="text-muted"><em>(Chức năng này sẽ được phát triển thêm)</em></p>
</div>
@endsection