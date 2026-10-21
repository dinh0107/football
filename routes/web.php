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

// Phase 4: Trung Tâm Trận Đấu Chi Tiết (Header, Diễn biến, Sân bóng 2D, Thống kê)
Route::get('/truc-tiep/{id}', [MatchController::class, 'show'])
    ->where('id', '[0-9]+')
    ->name('match.live_id');

Route::get('/tran-dau/{slug}', [MatchController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('match.detail');

// API nội bộ phục vụ Polling Live Scores mỗi 30s
Route::get('/api/live-scores', [FootballController::class, 'apiLiveScores'])->name('api.live_scores');
