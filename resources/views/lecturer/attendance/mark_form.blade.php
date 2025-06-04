@extends('layouts.app')

@section('title', 'Điểm danh Lớp: ' . $course->name)

@section('content')
<div class="card">
    <div class="card-header">
        Điểm danh cho Lớp: <strong>{{ $course->name }}</strong> ({{ $course->code }})
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('lecturer.attendance.store', $course) }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="attendance_date" class="form-label">Ngày điểm danh</label>
                    <input type="date" class="form-control @error('attendance_date') is-invalid @enderror" id="attendance_date" name="attendance_date" value="{{ old('attendance_date', $attendanceDate->format('Y-m-d')) }}" required>
                    @error('attendance_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label for="session" class="form-label">Buổi/Tiết học</label>
                    <input type="text" class="form-control @error('session') is-invalid @enderror" id="session" name="session" value="{{ old('session', $session) }}" placeholder="Ví dụ: Tiết 1-3, Buổi sáng" required>
                    @error('session') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 d-flex align-items-end">
                     <button type="button" id="loadExistingAttendance" class="btn btn-info w-100">Tải điểm danh (nếu có)</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã SV</th>
                            <th>Tên Sinh viên</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $index => $student)
                        @php
                            $currentAttendance = $existingAttendance[$student->id] ?? null;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->student_id_code }}</td>
                            <td>
                                {{ $student->name }}
                                <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">
                            </td>
                            <td>
                                <select name="attendances[{{ $index }}][status]" class="form-select form-select-sm status-select">
                                    <option value="present" {{ old('attendances.'.$index.'.status', $currentAttendance->status ?? 'present') == 'present' ? 'selected' : '' }}>Có mặt</option>
                                    <option value="absent" {{ old('attendances.'.$index.'.status', $currentAttendance->status ?? '') == 'absent' ? 'selected' : '' }}>Vắng</option>
                                    <option value="late" {{ old('attendances.'.$index.'.status', $currentAttendance->status ?? '') == 'late' ? 'selected' : '' }}>Trễ</option>
                                    <option value="permitted_absence" {{ old('attendances.'.$index.'.status', $currentAttendance->status ?? '') == 'permitted_absence' ? 'selected' : '' }}>Vắng có phép</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="attendances[{{ $index }}][notes]" class="form-control form-control-sm" value="{{ old('attendances.'.$index.'.notes', $currentAttendance->notes ?? '') }}" placeholder="Ghi chú (nếu có)">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Lớp này chưa có sinh viên.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->isNotEmpty())
            <div class="mt-3 d-flex justify-content-between">
                 <button type="submit" class="btn btn-primary">Lưu Điểm danh</button>
                 <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="markAllPresent">Đánh dấu tất cả "Có mặt"</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="markAllAbsent">Đánh dấu tất cả "Vắng"</button>
                 </div>
            </div>
            @endif
        </form>
        <hr>
        <a href="{{ route('lecturer.courses.show', $course) }}" class="btn btn-secondary">Quay lại chi tiết lớp</a>
    </div>
</div>
@push('scripts')
<script>
    document.getElementById('loadExistingAttendance').addEventListener('click', function() {
        const date = document.getElementById('attendance_date').value;
        const session = document.getElementById('session').value;
        if (date && session) {
            // Submit the form via GET to reload the page with data
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('attendance_date', date);
            currentUrl.searchParams.set('session', session);
            window.location.href = currentUrl.toString();
        } else {
            alert('Vui lòng chọn ngày và nhập buổi/tiết học.');
        }
    });

    document.getElementById('markAllPresent')?.addEventListener('click', function() {
        document.querySelectorAll('.status-select').forEach(select => select.value = 'present');
    });
     document.getElementById('markAllAbsent')?.addEventListener('click', function() {
        document.querySelectorAll('.status-select').forEach(select => select.value = 'absent');
    });
</script>
@endpush
@endsection