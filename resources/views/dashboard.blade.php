@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">{{ __('Dashboard Chung') }}</div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                {{ __('Bạn đã đăng nhập thành công!') }}
                <p>Xin chào, {{ Auth::user()->name }}!</p>
                <p>Vai trò của bạn: {{ ucfirst(Auth::user()->role) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection