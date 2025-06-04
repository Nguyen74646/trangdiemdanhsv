@extends('layouts.admin')

@section('title', 'Sửa Lớp học: ' . $course->name)

@section('content')
<div class="card">
    <div class="card-header">Sửa thông tin Lớp học: <strong>{{ $course->name }}</strong></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.courses.update', $course->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Tên lớp học</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $course->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="code" class="form-label">Mã lớp học</label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $course->code) }}" required>
                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="lecturer_id" class="form-label">Giảng viên</label>
                <select class="form-select @error('lecturer_id') is-invalid @enderror" id="lecturer_id" name="lecturer_id" required>
                    <option value="">Chọn giảng viên</option>
                    @foreach ($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}" {{ old('lecturer_id', $course->lecturer_id) == $lecturer->id ? 'selected' : '' }}>
                            {{ $lecturer->name }} ({{ $lecturer->email }})
                        </option>
                    @endforeach
                </select>
                @error('lecturer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

             <div class="mb-3">
                <label for="semester" class="form-label">Học kỳ</label>
                <input type="text" class="form-control @error('semester') is-invalid @enderror" id="semester" name="semester" value="{{ old('semester', $course->semester) }}" placeholder="Ví dụ: Học kỳ 1 2023-2024">
                @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Chi tiết lịch học</label>
                <div id="schedule-container">
                    @php
                        // old() sẽ ưu tiên hơn $course->schedule_details khi có lỗi validation và form được submit lại
                        $schedules = old('schedule_details', $course->schedule_details ?? [['day_of_week'=>'', 'start_time'=>'', 'end_time'=>'', 'session_name'=>'']]);
                        if (empty($schedules)) $schedules = [['day_of_week'=>'', 'start_time'=>'', 'end_time'=>'', 'session_name'=>'']]; // Đảm bảo có ít nhất 1 dòng nếu $course->schedule_details rỗng
                    @endphp
                    @foreach($schedules as $index => $schedule)
                    <div class="row g-2 mb-2 schedule-item">
                        <div class="col-md-3">
                            <select name="schedule_details[{{$index}}][day_of_week]" class="form-select">
                                <option value="">Chọn ngày</option>
                                <option value="Monday" {{ ($schedule['day_of_week'] ?? '') == 'Monday' ? 'selected' : ''}}>Thứ 2</option>
                                <option value="Tuesday" {{ ($schedule['day_of_week'] ?? '') == 'Tuesday' ? 'selected' : ''}}>Thứ 3</option>
                                <option value="Wednesday" {{ ($schedule['day_of_week'] ?? '') == 'Wednesday' ? 'selected' : ''}}>Thứ 4</option>
                                <option value="Thursday" {{ ($schedule['day_of_week'] ?? '') == 'Thursday' ? 'selected' : ''}}>Thứ 5</option>
                                <option value="Friday" {{ ($schedule['day_of_week'] ?? '') == 'Friday' ? 'selected' : ''}}>Thứ 6</option>
                                <option value="Saturday" {{ ($schedule['day_of_week'] ?? '') == 'Saturday' ? 'selected' : ''}}>Thứ 7</option>
                                <option value="Sunday" {{ ($schedule['day_of_week'] ?? '') == 'Sunday' ? 'selected' : ''}}>Chủ Nhật</option>
                            </select>
                        </div>
                        <div class="col-md-2"><input type="time" name="schedule_details[{{$index}}][start_time]" class="form-control" value="{{ $schedule['start_time'] ?? '' }}" placeholder="Giờ BĐ"></div>
                        <div class="col-md-2"><input type="time" name="schedule_details[{{$index}}][end_time]" class="form-control" value="{{ $schedule['end_time'] ?? '' }}" placeholder="Giờ KT"></div>
                        <div class="col-md-3"><input type="text" name="schedule_details[{{$index}}][session_name]" class="form-control" value="{{ $schedule['session_name'] ?? '' }}" placeholder="Tên buổi/tiết"></div>
                        <div class="col-md-2">
                            @if($index > 0 || count($schedules) > 1) {{-- Cho phép xóa nếu không phải là item duy nhất --}}
                            <button type="button" class="btn btn-danger btn-sm remove-schedule-item">Xóa</button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="add-schedule-item" class="btn btn-outline-secondary btn-sm mt-2">Thêm lịch học</button>
                <small class="form-text text-muted">Bạn có thể thêm nhiều buổi học cho lớp.</small>
                @error('schedule_details') <div class="text-danger small">{{ $message }}</div> @enderror>
                @error('schedule_details.*.day_of_week') <div class="text-danger small">Ngày trong tuần của lịch học không hợp lệ.</div> @enderror
                @error('schedule_details.*.start_time') <div class="text-danger small">Giờ bắt đầu của lịch học không hợp lệ.</div> @enderror>
                @error('schedule_details.*.end_time') <div class="text-danger small">Giờ kết thúc của lịch học không hợp lệ hoặc trước giờ bắt đầu.</div> @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Cập nhật Lớp học</button>
                <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-info">Xem chi tiết Lớp</a>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Hủy và Quay lại</a>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let scheduleIndex = {{ count($schedules) }};
        const container = document.getElementById('schedule-container');
        const addButton = document.getElementById('add-schedule-item');

        if (addButton) {
            addButton.addEventListener('click', function () {
                const newItemHtml = `
                    <div class="col-md-3">
                        <select name="schedule_details[${scheduleIndex}][day_of_week]" class="form-select">
                            <option value="">Chọn ngày</option>
                            <option value="Monday">Thứ 2</option>
                            <option value="Tuesday">Thứ 3</option>
                            <option value="Wednesday">Thứ 4</option>
                            <option value="Thursday">Thứ 5</option>
                            <option value="Friday">Thứ 6</option>
                            <option value="Saturday">Thứ 7</option>
                            <option value="Sunday">Chủ Nhật</option>
                        </select>
                    </div>
                    <div class="col-md-2"><input type="time" name="schedule_details[${scheduleIndex}][start_time]" class="form-control" placeholder="Giờ BĐ"></div>
                    <div class="col-md-2"><input type="time" name="schedule_details[${scheduleIndex}][end_time]" class="form-control" placeholder="Giờ KT"></div>
                    <div class="col-md-3"><input type="text" name="schedule_details[${scheduleIndex}][session_name]" class="form-control" placeholder="Tên buổi/tiết"></div>
                    <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-schedule-item">Xóa</button></div>
                `;
                const newItemWrapper = document.createElement('div');
                newItemWrapper.classList.add('row', 'g-2', 'mb-2', 'schedule-item');
                newItemWrapper.innerHTML = newItemHtml;
                container.appendChild(newItemWrapper);
                scheduleIndex++;
                updateRemoveButtons();
            });
        }

        function updateRemoveButtons() {
            const items = container.querySelectorAll('.schedule-item');
            items.forEach((item, index) => {
                let removeButton = item.querySelector('.remove-schedule-item');
                if (items.length <= 1) { // Nếu chỉ còn 1 item
                    if (removeButton) {
                        removeButton.style.display = 'none'; // Ẩn nút xóa
                    }
                } else {
                    if (removeButton) {
                         removeButton.style.display = ''; // Hiện nút xóa
                    } else if (index > 0 || items.length > 1) { // Nếu chưa có nút xóa và không phải item đầu tiên (hoặc có nhiều hơn 1)
                        const buttonCol = item.children[4]; // Cột thứ 5 (index 4)
                        if(buttonCol && !buttonCol.querySelector('.remove-schedule-item')){
                           const newRemoveButton = document.createElement('button');
                           newRemoveButton.type = 'button';
                           newRemoveButton.classList.add('btn', 'btn-danger', 'btn-sm', 'remove-schedule-item');
                           newRemoveButton.textContent = 'Xóa';
                           buttonCol.appendChild(newRemoveButton);
                        }
                    }
                }
            });
        }


        container.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-schedule-item')) {
                if (container.querySelectorAll('.schedule-item').length > 1) {
                    e.target.closest('.schedule-item').remove();
                    updateRemoveButtons();
                } else {
                    alert('Phải có ít nhất một lịch học.');
                }
            }
        });
        updateRemoveButtons(); // Initial call to set button visibility
    });
</script>
@endpush
@endsection