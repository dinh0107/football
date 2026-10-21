<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware bảo vệ trang Quản trị Admin
 */
class EnsureAdminUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->guest(route('admin.login'))
                ->with('warning', 'Vui lòng đăng nhập tài khoản Quản trị viên.');
        }

        $user = Auth::user();
        if (!$user->is_admin) {
            abort(403, 'Bạn không có quyền truy cập vào bảng điều khiển quản trị.');
        }

        return $next($request);
    }
}
