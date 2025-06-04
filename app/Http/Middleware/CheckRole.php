<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  // Các vai trò được phép
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        if (!$user || !in_array($user->role, $roles)) {
            // Có thể thay bằng abort(403, 'Unauthorized action.');
            // Hoặc redirect về trang dashboard với thông báo lỗi
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập vào khu vực này.');
        }

        return $next($request);
    }
}