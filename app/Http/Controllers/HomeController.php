<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('welcome');
    }
    public function dashboard()
{
    $user = Auth::user();
    switch ($user->role) {
        case 'admin':
            return view('admin.dashboard', compact('user'));
        case 'giangvien':
            return view('lecturer.dashboard', compact('user'));
        case 'sinhvien':
            return view('student.dashboard', compact('user'));
        default:
            // Có thể là một trang dashboard chung hoặc trang lỗi nếu vai trò không xác định
            return view('dashboard', compact('user')); // Trang dashboard.blade.php chung
    }
}
}
