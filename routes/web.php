<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FootballController;
use App\Http\Controllers\StandingsController;
use App\Http\Controllers\MatchController;

/*
|--------------------------------------------------------------------------
| Web Routes - Website Theo Dõi Ngoại Hạng Anh
|--------------------------------------------------------------------------
*/

// Phase 2: Trang Chủ - Lịch Thi Đấu & Kết Quả Theo Ngày (Fixtures & Results)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Phase 3: Bảng Xếp Hạng Ngoại Hạng Anh & Live Table As-It-Stands
Route::get('/bang-xep-hang', [StandingsController::class, 'index'])->name('standings');

// Lịch Thi Đấu Theo Vòng Đấu
Route::get('/lich-thi-dau', [FootballController::class, 'fixtures'])->name('fixtures');

// Trung Tâm Trực Tiếp Bóng Đá & Live Scores
Route::get('/truc-tiep', [FootballController::class, 'live'])->name('live');

// Phase 6: URL Trận Đấu Chuẩn SEO: /truc-tiep/{slug}-{id} (Ví dụ: /truc-tiep/arsenal-vs-chelsea-vong-10-93452)
Route::get('/truc-tiep/{slug}', [MatchController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('match.detail');

// Tương thích ngược: /match/{id} và /tran-dau/{slug} tự động chuyển hướng 301
Route::get('/match/{id}', function ($id) {
    return redirect('/truc-tiep/' . $id, 301);
});

Route::get('/tran-dau/{slug}', function ($slug) {
    return redirect('/truc-tiep/' . $slug, 301);
});

// Phase 6: Sitemap XML động cho Google Search Console
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// API nội bộ phục vụ Polling Live Scores mỗi 30s
Route::get('/api/live-scores', [FootballController::class, 'apiLiveScores'])->name('api.live_scores');
