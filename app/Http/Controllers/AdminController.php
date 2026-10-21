<?php

namespace App\Http\Controllers;

use App\Models\MatchOverride;
use App\Models\AdBanner;
use App\Services\PremierLeagueService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * Controller phục vụ Phase 8: Trang Quản trị Admin (TV Guide, Cache, Bảo trì, Banner Quảng Cáo)
 */
class AdminController extends Controller
{
    /**
     * Hiển thị trang đăng nhập Admin
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Xử lý đăng nhập Admin
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            if ($user->is_admin) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Đăng nhập trang quản trị thành công.');
            }

            Auth::logout();
            return back()->withErrors(['email' => 'Tài khoản này không có quyền quản trị viên.']);
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    /**
     * Đăng xuất Admin
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Đã đăng xuất an toàn.');
    }

    /**
     * Dashboard Quản Trị Hệ Thống
     */
    public function dashboard(PremierLeagueService $plService): View
    {
        // 1. Lấy danh sách trận đấu gần nhất / cuối tuần
        $fixtures = [];
        try {
            $fixtures = $plService->getFixtures();
        } catch (\Throwable $e) {
            $fixtures = [];
        }

        // 2. Lấy các bản ghi đè TV Guide
        $overrides = MatchOverride::all()->keyBy('match_id');

        // 3. Lấy cấu hình các vị trí Banner quảng cáo
        $banners = [
            'banner_top' => AdBanner::where('position', 'banner_top')->latest()->first(),
            'banner_below_score' => AdBanner::where('position', 'banner_below_score')->latest()->first(),
            'banner_footer' => AdBanner::where('position', 'banner_footer')->latest()->first(),
        ];

        // 4. Kiểm tra trạng thái bảo trì
        $isMaintenance = app()->isDownForMaintenance();

        return view('admin.dashboard', [
            'fixtures' => $fixtures,
            'overrides' => $overrides,
            'banners' => $banners,
            'isMaintenance' => $isMaintenance,
        ]);
    }

    /**
     * Cập nhật nhanh kênh phát sóng TV Guide cho trận đấu
     */
    public function updateTvChannel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'match_id' => 'required|integer',
            'tv_channel' => 'required|string|max:100',
            'notes' => 'nullable|string|max:255',
        ]);

        MatchOverride::updateOrCreate(
            ['match_id' => $validated['match_id']],
            [
                'tv_channel' => $validated['tv_channel'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        Cache::forget("match_details_{$validated['match_id']}");

        return redirect()->route('admin.dashboard')->with('success', "Đã cập nhật kênh phát sóng [{$validated['tv_channel']}] cho trận #{$validated['match_id']} thành công!");
    }

    /**
     * Xóa sạch toàn bộ bộ nhớ Cache hệ thống (Redis/File)
     */
    public function clearCache(): RedirectResponse
    {
        try {
            Cache::flush();
            Artisan::call('cache:clear');
            return redirect()->route('admin.dashboard')->with('success', 'Đã làm sạch toàn bộ Cache hệ thống thành công!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Lỗi khi xóa Cache: ' . $e->getMessage());
        }
    }

    /**
     * Bật hoặc Tắt chế độ bảo trì hệ thống
     */
    public function toggleMaintenance(): RedirectResponse
    {
        try {
            if (app()->isDownForMaintenance()) {
                Artisan::call('up');
                $msg = 'Đã TẮT chế độ bảo trì. Website hoạt động bình thường!';
            } else {
                Artisan::call('down', [
                    '--secret' => 'admin-epl-bypass',
                ]);
                $msg = 'Đã BẬT chế độ bảo trì hệ thống. Người dùng sẽ thấy trang bảo trì.';
            }

            return redirect()->route('admin.dashboard')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Lỗi chuyển đổi bảo trì: ' . $e->getMessage());
        }
    }

    /**
     * Lưu cấu hình Banner quảng cáo cho từng vị trí
     */
    public function saveBanner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'position' => 'required|in:banner_top,banner_below_score,banner_footer',
            'title' => 'required|string|max:150',
            'image_url' => 'nullable|string|max:500',
            'target_url' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'html_code' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        AdBanner::updateOrCreate(
            ['position' => $validated['position']],
            $validated
        );

        Cache::forget("ad_banner_{$validated['position']}");

        return redirect()->route('admin.dashboard')->with('success', "Đã cập nhật banner quảng cáo cho vị trí [{$validated['position']}] thành công!");
    }
}
