@extends('layouts.admin')

@php
    $pageTitle = 'Quản lý Lớp học';
@endphp

@section('admin_content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Lớp học</h6>
            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus fa-sm"></i> Thêm Lớp học mới
            </a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.courses.index') }}" class="mb-3">
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm tên lớp, mã lớp..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-info btn-sm w-100">Lọc</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTableCourses" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên Lớp học</th>
                            <th>Mã Lớp</th>
                            <th>Giảng viên</th>
                            <th>Học kỳ</th>
                            <th>Số SV</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $index => $course)
                        <tr>
                            <td>{{ $courses->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('admin.courses.show', $course->id) }}">{{ $course->name }}</a>
                            </td>
                            <td>{{ $course->code }}</td>
                            <td>{{ $course->lecturer->name ?? 'N/A' }}</td>
                            <td>{{ $course->semester ?? 'N/A' }}</td>
                            <td>{{ count($course->student_ids ?? []) }}</td>
                            <td>
                                <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-sm btn-info" title="Xem và Quản lý SV">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-sm btn-warning" title="Sửa thông tin lớp">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa lớp học này? Hành động này không thể hoàn tác.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa lớp học">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Không có lớp học nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $courses->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection