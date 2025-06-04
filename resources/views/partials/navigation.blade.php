<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ Auth::check() ? route('dashboard') : url('/') }}">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 30px; margin-right: 10px;"> {{-- Thêm logo nếu có --}}
            {{ config('app.name', 'Hệ thống Điểm Danh') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>

                    {{-- ADMIN MENU --}}
                    @if(Auth::user()->role == 'admin')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->is('admin/*') ? 'active' : '' }}" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-shield me-1"></i>Quản trị Admin
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                        <i class="fas fa-users-cog me-1"></i>Quản lý Người dùng
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>Quản lý Lớp học
                                    </a>
                                </li>
                                {{-- Thêm các mục admin khác nếu có --}}
                            </ul>
                        </li>
                    @endif

                    {{-- LECTURER MENU --}}
                    @if(Auth::user()->role == 'giangvien' || Auth::user()->role == 'admin') {{-- Admin cũng có thể truy cập mục này nếu muốn --}}
                        <li class="nav-item dropdown">
                             <a class="nav-link dropdown-toggle {{ request()->is('lecturer/*') ? 'active' : '' }}" href="#" id="lecturerDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-chalkboard me-1"></i>Giảng viên
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="lecturerDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('lecturer.courses.*') ? 'active' : '' }}" href="{{ route('lecturer.courses.index') }}">
                                        <i class="fas fa-list-alt me-1"></i>Lớp học của tôi
                                    </a>
                                </li>
                                {{-- Giảng viên cũng có thể xem báo cáo, nên có thể gộp vào đây hoặc để riêng --}}
                            </ul>
                        </li>
                    @endif


                    {{-- STUDENT MENU --}}
                    @if(Auth::user()->role == 'sinhvien' || Auth::user()->role == 'admin') {{-- Admin cũng có thể truy cập mục này để test --}}
                         <li class="nav-item dropdown">
                             <a class="nav-link dropdown-toggle {{ request()->is('student/*') ? 'active' : '' }}" href="#" id="studentDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-graduate me-1"></i>Sinh viên
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="studentDropdown">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('student.my_courses') ? 'active' : '' }}" href="{{ route('student.my_courses') }}">
                                        <i class="fas fa-book-reader me-1"></i>Lớp học đã đăng ký
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('student.attendance.my') ? 'active' : '' }}" href="{{ route('student.attendance.my') }}">
                                        <i class="fas fa-calendar-check me-1"></i>Điểm danh của tôi
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                     {{-- REPORT MENU (chung cho admin và giảng viên) --}}
                    @can('view-attendance-reports') {{-- Hoặc: @if(in_array(Auth::user()->role, ['admin', 'giangvien'])) --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.attendance') ? 'active' : '' }}" href="{{ route('reports.attendance') }}">
                                <i class="fas fa-chart-pie me-1"></i>Báo cáo Điểm danh
                            </a>
                        </li>
                    @endcan

                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-1"></i>{{ __('Đăng nhập') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}"><i class="fas fa-user-plus me-1"></i>{{ __('Đăng ký') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fas fa-user-circle me-1"></i>
                            {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            {{-- <a class="dropdown-item" href="#">
                                <i class="fas fa-user-edit me-1"></i>Thông tin cá nhân
                            </a>
                            <div class="dropdown-divider"></div> --}}
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i>{{ __('Đăng xuất') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>