@extends('layouts.app')

@section('title', 'Sửa Điểm danh cho ' . $attendance->student->name)

@section('content')
<div class="card">
    <div class="card-header">
        Sửa Điểm danh Lớp: <strong>{{ $course->name }}</strong> - Sinh viên: <strong>{{ $attendance->student->name }} ({{$attendance->student->student_id_code}})</strong>
        <br>Ngày: {{ $attendance->attendance_date->format('d/m/Y') }} - Buổi: {{ $attendance->session }}
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('lecturer.attendance.update', [$course, $attendance->id]) }}">
            @csrf
            @method('PUT')

            <div class="mb-3 row">
                <label for="status" class="col-sm-3 col-form-label">Trạng thái</label>
                <div class="col-sm-9">
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Có mặt</option>
                        <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Vắng</option>
                        <option value="late" {{ old('status', $attendance->status) == 'late' ? 'selected' : '' }}>Trễ</option>
                        <option value="permitted_absence" {{ old('status', $attendance->status) == 'permitted_absence' ? 'selected' : '' }}>Vắng có phép</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 row">
                <label for="notes" class="col-sm-3 col-form-label">Ghi chú</label>
                <div class="col-sm-9">
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary">Cập nhật Điểm danh</button>
                    <a href="{{ route('lecturer.attendance.history', $course) }}" class="btn btn-secondary">Hủy</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection