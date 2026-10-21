<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballController;

/*
|--------------------------------------------------------------------------
| Web Routes - Website Theo Dõi Ngoại Hạng Anh
|--------------------------------------------------------------------------
*/

// Trang chủ & Bảng Xếp Hạng (Cache 1h)
Route::get('/', [FootballController::class, 'standings'])->name('home');
Route::get('/bang-xep-hang', [FootballController::class, 'standings'])->name('standings');

// Lịch Thi Đấu & Kết Quả (Cache 1 ngày)
Route::get('/lich-thi-dau', [FootballController::class, 'fixtures'])->name('fixtures');

// Trực Tiếp Bóng Đá & Live Scores (Cache 30s)
Route::get('/truc-tiep', [FootballController::class, 'live'])->name('live');

// Chi Tiết Trận Đấu - Slug Tiếng Việt Không Dấu + Schema.org (Cache linh hoạt theo trạng thái)
Route::get('/tran-dau/{slug}', [FootballController::class, 'matchDetail'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('match.detail');

// API nội bộ phục vụ Polling thời gian thực không tải lại trang
Route::get('/api/live-scores', [FootballController::class, 'apiLiveScores'])->name('api.live_scores');
