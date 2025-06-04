@extends('layouts.admin') {{-- Kế thừa từ layout admin --}}

@php
    $pageTitle = 'Cài đặt Hệ thống'; // Đặt tiêu đề cụ thể
@endphp

@section('admin_content') {{-- Đặt nội dung vào section này --}}
    <div class="container-fluid">
        <h1>{{ $pageTitle }}</h1>
        <p>Đây là nội dung của trang cài đặt hệ thống...</p>
        {{-- Thêm các form, bảng biểu, v.v. tại đây --}}
    </div>
@endsection

@push('scripts_page') {{-- Nếu có script riêng cho trang này --}}
    <script>
        // JS specific to settings page
    </script>
@endpush