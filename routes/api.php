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
