@extends('layouts.app')

@section('title', 'Sửa Người dùng: ' . $user->name)

@section('content')
<div class="card">
    <div class="card-header">Sửa thông tin Người dùng: <strong>{{ $user->name }}</strong></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Tên người dùng</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu (Để trống nếu không muốn thay đổi)</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Vai trò</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="giangvien" {{ old('role', $user->role) == 'giangvien' ? 'selected' : '' }}>Giảng viên</option>
                    <option value="sinhvien" {{ old('role', $user->role) == 'sinhvien' ? 'selected' : '' }}>Sinh viên</option>
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3" id="student_id_code_field" style="{{ old('role', $user->role) == 'sinhvien' ? '' : 'display:none;' }}">
                <label for="student_id_code" class="form-label">Mã sinh viên (nếu là Sinh viên)</label>
                <input type="text" class="form-control @error('student_id_code') is-invalid @enderror" id="student_id_code" name="student_id_code" value="{{ old('student_id_code', $user->student_id_code) }}">
                @error('student_id_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Cập nhật Người dùng</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy và Quay lại</a>
            </div>
        </form>
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
                // studentIdInput.required = true; // Bạn có thể bỏ required ở đây nếu admin có thể tạo SV chưa có mã
            } else {
                studentIdField.style.display = 'none';
                // studentIdInput.required = false;
                studentIdInput.value = ''; // Xóa giá trị nếu không phải sinh viên
            }
        }

        roleSelect.addEventListener('change', toggleStudentIdField);
        // Kích hoạt khi tải trang để đảm bảo trạng thái đúng nếu có lỗi validation hoặc khi edit
        toggleStudentIdField();
    });
</script>
@endpush
@endsection