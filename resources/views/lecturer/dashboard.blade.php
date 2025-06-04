@extends('layouts.app')

@section('title', 'Giảng viên Dashboard')

@section('content')
<div class="card">
    <div class="card-header">Giảng viên Dashboard</div>
    <div class="card-body">
        <h5 class="card-title">Chào mừng, Giảng viên {{ $user->name }}!</h5>
        <p class="card-text">Đây là khu vực dành cho giảng viên.</p>
        <a href="{{ route('lecturer.courses.index') }}" class="btn btn-primary">Xem danh sách lớp học của tôi</a>
        <a href="{{ route('reports.attendance') }}" class="btn btn-info">Xem báo cáo điểm danh</a>
        {{-- Thêm các link chức năng khác cho giảng viên nếu có --}}
    </div>
</div>

<div class="mt-4">
    <h4>Thông báo hoặc Lịch học sắp tới:</h4>
    {{--  Hiển thị thông báo hoặc lịch học gần nhất của giảng viên --}}
    <p class="text-muted"><em>(Chức năng này sẽ được phát triển thêm)</em></p>
</div>
@endsection