<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LiveMatchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::get('/matches/live', [LiveMatchController::class, 'index'])->name('api.matches.live');

// Phase 7: Tính năng Bình chọn Dự đoán kết quả trận đấu (Community Match Voting)
Route::post('/matches/{id}/vote', [\App\Http\Controllers\MatchVoteController::class, 'vote'])->name('api.match.vote');
Route::get('/matches/{id}/votes', [\App\Http\Controllers\MatchVoteController::class, 'stats'])->name('api.match.votes');

