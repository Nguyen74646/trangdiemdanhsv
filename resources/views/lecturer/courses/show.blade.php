@extends('layouts.app')

@section('title', 'Chi tiết Lớp học: ' . $course->name)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Chi tiết Lớp học: <strong>{{ $course->name }} ({{ $course->code }})</strong></span>
        <div>
            <a href="{{ route('lecturer.attendance.mark_form', $course) }}" class="btn btn-primary btn-sm">Điểm danh</a>
            <a href="{{ route('lecturer.attendance.history', $course) }}" class="btn btn-secondary btn-sm">Lịch sử điểm danh</a>
        </div>
    </div>
    <div class="card-body">
        <p><strong>Giảng viên:</strong> {{ $course->lecturer->name ?? 'N/A' }}</p>
        <p><strong>Học kỳ:</strong> {{ $course->semester ?? 'N/A' }}</p>
        <p><strong>Lịch học:</strong></p>
        @if(!empty($course->schedule_details))
            <ul>
            @foreach($course->schedule_details as $schedule)
                <li>
                    {{ $schedule['day_of_week'] ?? 'N/A' }}:
                    {{ $schedule['start_time'] ?? 'N/A' }} - {{ $schedule['end_time'] ?? 'N/A' }}
                    ({{ $schedule['session_name'] ?? 'N/A' }})
                </li>
            @endforeach
            </ul>
        @else
            <p>Chưa có lịch học chi tiết.</p>
        @endif

        <hr>
        <h5>Danh sách Sinh viên trong lớp ({{ $course->students->count() }})</h5>
        @if($course->students->isNotEmpty())
        <table class="table table-sm table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mã SV</th>
                    <th>Tên Sinh viên</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($course->students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->student_id_code }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p>Chưa có sinh viên nào trong lớp này.</p>
        @endif
    </div>
    <div class="card-footer">
        <a href="{{ route('lecturer.courses.index') }}" class="btn btn-light">Quay lại danh sách lớp</a>
    </div>
</div>
@endsection