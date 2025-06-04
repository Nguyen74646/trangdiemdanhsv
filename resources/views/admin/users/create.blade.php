@extends('layouts.admin')

@php
    $pageTitle = 'Thêm Người dùng mới';
@endphp

@section('admin_content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thêm Người dùng mới</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Tên người dùng <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="">Chọn vai trò</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="giangvien" {{ old('role') == 'giangvien' ? 'selected' : '' }}>Giảng viên</option>
                        <option value="sinhvien" {{ old('role') == 'sinhvien' ? 'selected' : '' }}>Sinh viên</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3" id="student_id_code_field" style="{{ old('role') == 'sinhvien' ? '' : 'display:none;' }}">
                    <label for="student_id_code" class="form-label">Mã sinh viên (Bắt buộc nếu là Sinh viên)</label>
                    <input type="text" class="form-control @error('student_id_code') is-invalid @enderror" id="student_id_code" name="student_id_code" value="{{ old('student_id_code') }}">
                    @error('student_id_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Thêm mới</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const studentIdField = document.getElementById('student_id_code_field');
        const studentIdInput = document.getElementById('student_id_code');

        function toggleStudentIdField() {
            if (roleSelect.value === 'sinhvien') {
                studentIdField.style.display = 'block';
                studentIdInput.required = true; // Bắt buộc nhập nếu là sinh viên
            } else {
                studentIdField.style.display = 'none';
                studentIdInput.required = false;
                studentIdInput.value = ''; // Xóa giá trị nếu không phải sinh viên
            }
        }

        if(roleSelect) {
            roleSelect.addEventListener('change', toggleStudentIdField);
            toggleStudentIdField(); // Kích hoạt khi tải trang
        }
    });
</script>
@endpush
@endsection