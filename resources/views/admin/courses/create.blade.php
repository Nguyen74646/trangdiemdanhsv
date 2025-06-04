@extends('layouts.admin')

@php
    $pageTitle = 'Thêm Lớp học mới';
@endphp

@section('admin_content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thêm Lớp học mới</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.courses.store') }}">
                @csrf
                {{-- Copy nội dung form từ file admin/courses/create.blade.php bạn đã có trước đó vào đây --}}
                {{-- Đảm bảo các class CSS và cấu trúc HTML phù hợp với theme admin nếu có --}}

                <div class="mb-3">
                    <label for="name" class="form-label">Tên lớp học <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="code" class="form-label">Mã lớp học <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="lecturer_id" class="form-label">Giảng viên <span class="text-danger">*</span></label>
                    <select class="form-select @error('lecturer_id') is-invalid @enderror" id="lecturer_id" name="lecturer_id" required>
                        <option value="">Chọn giảng viên</option>
                        @foreach ($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" {{ old('lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                                {{ $lecturer->name }} ({{ $lecturer->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('lecturer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="semester" class="form-label">Học kỳ</label>
                    <input type="text" class="form-control @error('semester') is-invalid @enderror" id="semester" name="semester" value="{{ old('semester') }}" placeholder="Ví dụ: Học kỳ 1 2023-2024">
                    @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Chi tiết lịch học</label>
                    <div id="schedule-container">
                        @php
                            $schedules = old('schedule_details', [['day_of_week'=>'', 'start_time'=>'', 'end_time'=>'', 'session_name'=>'']]);
                        @endphp
                        @foreach($schedules as $index => $schedule)
                        <div class="row g-2 mb-2 schedule-item">
                            <div class="col-md-3">
                                <select name="schedule_details[{{$index}}][day_of_week]" class="form-select form-select-sm @error('schedule_details.'.$index.'.day_of_week') is-invalid @enderror">
                                    <option value="">Chọn ngày</option>
                                    <option value="Monday" {{ ($schedule['day_of_week'] ?? '') == 'Monday' ? 'selected' : ''}}>Thứ 2</option>
                                    <option value="Tuesday" {{ ($schedule['day_of_week'] ?? '') == 'Tuesday' ? 'selected' : ''}}>Thứ 3</option>
                                    <option value="Wednesday" {{ ($schedule['day_of_week'] ?? '') == 'Wednesday' ? 'selected' : ''}}>Thứ 4</option>
                                    <option value="Thursday" {{ ($schedule['day_of_week'] ?? '') == 'Thursday' ? 'selected' : ''}}>Thứ 5</option>
                                    <option value="Friday" {{ ($schedule['day_of_week'] ?? '') == 'Friday' ? 'selected' : ''}}>Thứ 6</option>
                                    <option value="Saturday" {{ ($schedule['day_of_week'] ?? '') == 'Saturday' ? 'selected' : ''}}>Thứ 7</option>
                                    <option value="Sunday" {{ ($schedule['day_of_week'] ?? '') == 'Sunday' ? 'selected' : ''}}>Chủ Nhật</option>
                                </select>
                                @error('schedule_details.'.$index.'.day_of_week') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2"><input type="time" name="schedule_details[{{$index}}][start_time]" class="form-control form-control-sm @error('schedule_details.'.$index.'.start_time') is-invalid @enderror" value="{{ $schedule['start_time'] ?? '' }}" placeholder="Giờ BĐ"> @error('schedule_details.'.$index.'.start_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror</div>
                            <div class="col-md-2"><input type="time" name="schedule_details[{{$index}}][end_time]" class="form-control form-control-sm @error('schedule_details.'.$index.'.end_time') is-invalid @enderror" value="{{ $schedule['end_time'] ?? '' }}" placeholder="Giờ KT">@error('schedule_details.'.$index.'.end_time') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror</div>
                            <div class="col-md-3"><input type="text" name="schedule_details[{{$index}}][session_name]" class="form-control form-control-sm @error('schedule_details.'.$index.'.session_name') is-invalid @enderror" value="{{ $schedule['session_name'] ?? '' }}" placeholder="Tên buổi/tiết">@error('schedule_details.'.$index.'.session_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror</div>
                            <div class="col-md-2">
                                @if($index > 0 || count($schedules) > 1)
                                <button type="button" class="btn btn-danger btn-sm remove-schedule-item">Xóa</button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-schedule-item" class="btn btn-outline-secondary btn-sm mt-2">Thêm lịch học</button>
                </div>


                <button type="submit" class="btn btn-primary">Thêm mới</button>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    {{-- Giữ nguyên script cho schedule items như đã cung cấp ở file courses/edit.blade.php --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let scheduleIndex = {{ count(old('schedule_details', [['']])) }}; // Khởi tạo từ số lượng old() hoặc 1
        const container = document.getElementById('schedule-container');
        const addButton = document.getElementById('add-schedule-item');

        function createScheduleItemHTML(index) {
            return `
                <div class="col-md-3">
                    <select name="schedule_details[${index}][day_of_week]" class="form-select form-select-sm">
                        <option value="">Chọn ngày</option><option value="Monday">Thứ 2</option><option value="Tuesday">Thứ 3</option><option value="Wednesday">Thứ 4</option><option value="Thursday">Thứ 5</option><option value="Friday">Thứ 6</option><option value="Saturday">Thứ 7</option><option value="Sunday">Chủ Nhật</option>
                    </select>
                </div>
                <div class="col-md-2"><input type="time" name="schedule_details[${index}][start_time]" class="form-control form-control-sm" placeholder="Giờ BĐ"></div>
                <div class="col-md-2"><input type="time" name="schedule_details[${index}][end_time]" class="form-control form-control-sm" placeholder="Giờ KT"></div>
                <div class="col-md-3"><input type="text" name="schedule_details[${index}][session_name]" class="form-control form-control-sm" placeholder="Tên buổi/tiết"></div>
                <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-schedule-item">Xóa</button></div>
            `;
        }
        
        function updateRemoveButtonsVisibility() {
            const items = container.querySelectorAll('.schedule-item');
            items.forEach((item, idx) => {
                const removeButton = item.querySelector('.remove-schedule-item');
                if (items.length <= 1) {
                    if(removeButton) removeButton.style.display = 'none';
                } else {
                    if(removeButton) removeButton.style.display = '';
                    else if (idx > 0 || items.length > 1) { // Add button if missing and not the only item
                        const buttonCol = item.children[4];
                        if (buttonCol && !buttonCol.querySelector('.remove-schedule-item')) {
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


        if (addButton) {
            addButton.addEventListener('click', function () {
                const newItemWrapper = document.createElement('div');
                newItemWrapper.classList.add('row', 'g-2', 'mb-2', 'schedule-item');
                newItemWrapper.innerHTML = createScheduleItemHTML(scheduleIndex);
                container.appendChild(newItemWrapper);
                scheduleIndex++;
                updateRemoveButtonsVisibility();
            });
        }

        container.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-schedule-item')) {
                 if (container.querySelectorAll('.schedule-item').length > 1) {
                    e.target.closest('.schedule-item').remove();
                    updateRemoveButtonsVisibility();
                } else {
                    alert('Phải có ít nhất một lịch học.');
                }
            }
        });
        updateRemoveButtonsVisibility(); // Initial check
    });
    </script>
@endpush
@endsection