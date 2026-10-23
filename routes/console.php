<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Task Scheduling: Tự động chạy lệnh sinh Sitemap Index đa tầng hàng ngày lúc 02:00 sáng
 */
Schedule::command('sitemap:generate')
    ->dailyAt('02:00')
    ->description('Tự động cập nhật hệ thống Sitemap Index đa tầng hàng ngày');

/**
 * Task Scheduling: Tự động làm nóng bộ nhớ đệm (Pre-warm Cache) hàng ngày lúc 03:00 sáng
 */
Schedule::command('epl:warm-cache --limit=150')
    ->dailyAt('03:00')
    ->description('Tự động làm nóng cache trận đấu hàng ngày để website luôn đạt tốc độ tối đa');

