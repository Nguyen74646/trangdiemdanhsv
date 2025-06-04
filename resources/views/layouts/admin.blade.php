@extends('layouts.app') {{-- Kế thừa từ layout chung của ứng dụng (có navbar chính và footer) --}}

@section('title', 'Admin Panel - ' . ($pageTitle ?? 'Dashboard')) {{-- $pageTitle sẽ được truyền từ các view con --}}

@push('styles')
{{-- Thêm CSS riêng cho admin panel nếu cần --}}
<style>
    /* Đảm bảo body có padding-top bằng chiều cao navbar chính nếu navbar chính là fixed-top */
    /* body { padding-top: 56px; } /* Ví dụ, nếu navbar chính cao 56px */

    .admin-wrapper {
        display: flex;
        /* 
           Chiều cao tối thiểu: 100vh (toàn màn hình) 
           trừ đi chiều cao của navbar chính (ví dụ 56px)
           và chiều cao của footer (ví dụ 56px) nếu footer là fixed-bottom.
           Nếu footer không phải fixed-bottom, bạn có thể chỉ cần trừ chiều cao navbar.
        */
        min-height: calc(100vh - 56px @if(true) - 56px @endif); /* Giả sử footer cũng cao 56px và là fixed */
        margin-top: 56px; /* Đẩy wrapper xuống dưới navbar chính */
    }
    .admin-sidebar {
        width: 250px;
        background-color: #2c3e50; /* Một màu tối hơn, ví dụ màu của AdminLTE, SB Admin */
        padding-top: 1rem; /* Khoảng cách từ top của sidebar */
        position: fixed;
        left: 0;
        top: 56px; /* Ngay dưới navbar chính */
        bottom: @if(true) 56px @else 0 @endif; /* Đến trên footer (nếu footer fixed) hoặc đến cuối màn hình */
        overflow-y: auto; /* Cho phép cuộn nếu nội dung sidebar dài */
        z-index: 1000; /* Đảm bảo sidebar ở trên một số element khác nếu cần */
        border-right: 1px solid #34495e; /* Đường viền phải cho sidebar */
    }
    .admin-sidebar .nav-link {
        color: #bdc3c7; /* Màu chữ cho link */
        padding: .75rem 1.25rem;
        font-size: 0.9rem;
        border-left: 3px solid transparent; /* Để tạo hiệu ứng active */
        transition: all 0.2s ease-in-out;
    }
    .admin-sidebar .nav-link:hover {
        color: #ffffff;
        background-color: #34495e; /* Màu nền khi hover */
        border-left-color: #1abc9c; /* Màu active khi hover */
    }
    .admin-sidebar .nav-link.active {
        color: #ffffff; /* Màu chữ cho link active */
        background-color: #16a085; /* Màu nền cho link active */
        border-left-color: #1abc9c; /* Màu viền trái cho link active */
        font-weight: 500;
    }
    .admin-sidebar .nav-link .fas,
    .admin-sidebar .nav-link .far,
    .admin-sidebar .nav-link .fab { /* Cho các loại icon FontAwesome */
        margin-right: 10px;
        width: 20px; /* Đảm bảo icon thẳng hàng */
        text-align: center;
    }
    .admin-sidebar .nav-header {
        padding: .75rem 1.25rem;
        font-size: .75rem;
        color: #7f8c8d; /* Màu chữ cho header */
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 600;
        margin-top: 0.5rem;
    }
    .admin-content {
        flex-grow: 1;
        padding: 1.5rem; /* Khoảng cách nội dung chính */
        margin-left: 250px; /* Bằng width của sidebar */
        background-color: #ecf0f1; /* Màu nền sáng cho content */
        /* padding-bottom để tránh bị footer che nếu footer là fixed-bottom */
        padding-bottom: @if(true) calc(56px + 1.5rem) @else 1.5rem @endif;
    }

    /* Responsive: Ẩn sidebar và điều chỉnh content cho màn hình nhỏ */
    @media (max-width: 991.98px) { /* Bootstrap's md breakpoint */
        .admin-sidebar {
            /* Có thể ẩn hoàn toàn hoặc thu nhỏ lại, hoặc chuyển thành offcanvas menu */
            /* Ví dụ: ẩn hoàn toàn */
            /* display: none; */

            /* Hoặc thu nhỏ */
             width: 70px; /* Thu nhỏ sidebar */
        }
         .admin-sidebar .nav-link span { /* Ẩn chữ, chỉ hiện icon */
            display: none;
        }
        .admin-sidebar .nav-header {
            text-align: center;
        }
         .admin-sidebar .nav-link .fas,
         .admin-sidebar .nav-link .far,
         .admin-sidebar .nav-link .fab {
            margin-right: 0;
        }
        .admin-content {
            margin-left: 70px; /* Bằng width sidebar thu nhỏ */
        }
    }

    @media (max-width: 767.98px) { /* Bootstrap's sm breakpoint */
        .admin-sidebar {
            /* Trên màn hình rất nhỏ, có thể chuyển sidebar thành menu dạng toggle (offcanvas) */
            /* Hoặc ẩn hoàn toàn và dựa vào navbar chính nếu có dropdown admin */
            width: 100%;
            height: auto;
            position: static;
            border-right: none;
            border-bottom: 1px solid #34495e;
        }
         .admin-sidebar .nav-link span {
            display: inline; /* Hiện lại chữ */
        }
        .admin-content {
            margin-left: 0;
        }
        .admin-wrapper {
            flex-direction: column; /* Xếp chồng lên nhau */
            margin-top: 0; /* Reset margin-top nếu navbar chính cũng responsive */
        }
         /* Cần điều chỉnh lại padding-top của body nếu navbar chính thay đổi chiều cao hoặc ẩn */
         body { padding-top: 0; } /* Ví dụ nếu navbar chính bị ẩn hoặc thay đổi */
         .admin-wrapper { margin-top: 0; } /* Reset nếu cần */
         .admin-sidebar { top: 0; bottom: auto; } /* Reset nếu là static */
    }
</style>
@endpush

@section('content') {{-- Ghi đè section 'content' của layouts.app --}}
<div class="admin-wrapper">
    {{-- Sidebar cho Admin --}}
    <nav class="admin-sidebar">
        <div class="position-sticky pt-3"> {{-- Thêm padding-top cho nội dung sidebar --}}
            <ul class="nav flex-column">
               

                <li class="nav-header">Quản lý Hệ thống</li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users-cog"></i> <span>Người dùng</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('admin.courses.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                        <i class="fas fa-chalkboard-teacher"></i> <span>Lớp học</span>
                    </a>
                </li>
                {{-- Ví dụ thêm mục quản lý Sinh viên (nếu có controller riêng) --}}
                {{--
                <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('admin.students.*') ? 'active' : '' }}" href="#"> {{-- Thay # bằng route --}}
                        {{-- <i class="fas fa-user-graduate"></i> <span>Sinh viên</span>
                    </a>
                </li>
                --}}

                <li class="nav-header">Báo cáo & Thống kê</li>
                 <li class="nav-item">
                    <a class="nav-link {{ Request::routeIs('reports.attendance') ? 'active' : '' }}" href="{{ route('reports.attendance') }}">
                        <i class="fas fa-chart-pie"></i> <span>Báo cáo Điểm danh</span>
                    </a>
                </li>

                {{-- Thêm các mục menu khác cho admin tại đây --}}
                {{-- Ví dụ: Cài đặt hệ thống
                <li class="nav-header">Cài đặt</li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-cogs"></i> <span>Cài đặt chung</span>
                    </a>
                </li>
                --}}

                <li class="nav-header mt-auto"> {{-- Đẩy mục này xuống cuối nếu sidebar không cuộn và đủ cao --}}
                    Tài khoản
                </li>
                 <li class="nav-item">
                    {{-- Link về trang chủ hoặc dashboard của user (nếu admin cũng có thể là user thường) --}}
                    {{-- <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-user-circle"></i> Quay lại User View
                    </a> --}}
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                        <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                    </a>
                    <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    {{-- Nội dung chính của trang admin --}}
    <main class="admin-content" role="main">
        {{-- Không cần @include('partials.alerts') ở đây nữa nếu nó đã có trong layouts.app và không bị ghi đè --}}
        {{-- Tuy nhiên, nếu bạn muốn alerts chỉ hiển thị trong admin_content, thì đặt ở đây là đúng --}}
        @include('partials.alerts')
        @yield('admin_content') {{-- Đây là nơi nội dung của từng trang admin cụ thể sẽ được chèn vào --}}
    </main>
</div>
@endsection

@push('scripts')
{{-- Thêm JS riêng cho admin panel nếu cần --}}
{{-- Ví dụ: Script để toggle sidebar trên màn hình nhỏ nếu bạn làm menu offcanvas --}}
@endpush