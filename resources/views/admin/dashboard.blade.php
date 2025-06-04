@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="card">
    <div class="card-header">Admin Dashboard</div>
    <div class="card-body">
        <h5 class="card-title">Chào mừng, {{ $user->name }}!</h5>
        <p class="card-text">Đây là khu vực quản trị hệ thống.</p>
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Quản lý người dùng</a>
        <a href="{{ route('admin.courses.index') }}" class="btn btn-info">Quản lý lớp học</a>
    </div>
</div>
@endsection