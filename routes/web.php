<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FootballController;

/*
|--------------------------------------------------------------------------
| Web Routes - Website Theo Dõi Ngoại Hạng Anh
|--------------------------------------------------------------------------
*/

// Phase 2: Trang Chủ - Lịch Thi Đấu & Kết Quả Theo Ngày (Fixtures & Results)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Bảng Xếp Hạng Ngoại Hạng Anh (Standings)
Route::get('/bang-xep-hang', [FootballController::class, 'standings'])->name('standings');

// Lịch Thi Đấu Theo Vòng Đấu
Route::get('/lich-thi-dau', [FootballController::class, 'fixtures'])->name('fixtures');

// Trung Tâm Trực Tiếp Bóng Đá & Live Scores
Route::get('/truc-tiep', [FootballController::class, 'live'])->name('live');

// Xem Trực Tiếp Trận Đấu Theo ID (Chuyển tiếp chuẩn SEO đến slug)
Route::get('/truc-tiep/{id}', [FootballController::class, 'liveMatchById'])
    ->where('id', '[0-9]+')
    ->name('match.live_id');

// Chi Tiết Trận Đấu - Slug Tiếng Việt Không Dấu + Schema.org SportsEvent
Route::get('/tran-dau/{slug}', [FootballController::class, 'matchDetail'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('match.detail');

// API nội bộ phục vụ Polling Live Scores mỗi 30s
Route::get('/api/live-scores', [FootballController::class, 'apiLiveScores'])->name('api.live_scores');
