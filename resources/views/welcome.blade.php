@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8 text-center">
            <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'Laravel') }} Logo" style="max-height: 120px; margin-bottom: 20px;">
            {{-- Thay 'logo.png' bằng đường dẫn đến logo của bạn trong thư mục public --}}
            {{-- Hoặc bỏ qua nếu không có logo --}}

            <h1 class="display-5 fw-bold">{{ config('XIN CHÀO', 'Hệ Thống Điểm Danh Sinh Viên') }}</h1>
            <p class="fs-5 text-muted mt-3">
                Chào mừng bạn đến với hệ thống quản lý điểm danh sinh viên trực tuyến.
                Nền tảng giúp giảng viên dễ dàng theo dõi chuyên cần và sinh viên nắm bắt tình hình học tập của mình.
            </p>

            <hr class="my-4">
            @if(!Auth::check())
            <p class="lead">
                Để bắt đầu, vui lòng đăng nhập.
            </p>
            @endif

            
        </div>
    </div>

    <div class="row mt-5 pt-4 border-top">
        <div class="col-md-4 mb-3">
            <h4><i class="fas fa-cogs me-2 text-primary"></i>Dễ dàng quản lý</h4>
            <p>Quản lý thông tin lớp học, sinh viên và giảng viên một cách hiệu quả và tập trung.</p>
        </div>
        <div class="col-md-4 mb-3">
            <h4><i class="fas fa-user-check me-2 text-success"></i>Điểm danh nhanh chóng</h4>
            <p>Giảng viên có thể thực hiện điểm danh trực tuyến một cách thuận tiện, tiết kiệm thời gian.</p>
        </div>
        <div class="col-md-4 mb-3">
            <h4><i class="fas fa-chart-bar me-2 text-info"></i>Báo cáo trực quan</h4>
            <p>Theo dõi tỷ lệ chuyên cần, xuất báo cáo chi tiết theo ngày, tháng, học kỳ.</p>
        </div>
    </div>
</div>

{{-- Bạn có thể thêm các section khác như "Tính năng nổi bật", "Liên hệ", v.v. --}}

@endsection

{{-- Nếu bạn muốn trang home này không dùng navbar và footer của layout app,
   bạn có thể tạo một layout riêng cho trang landing page hoặc không kế thừa layout nào cả
   và tự viết toàn bộ HTML.
   Ví dụ, không dùng @extends('layouts.app') và tự thêm <head>, <body> --}}