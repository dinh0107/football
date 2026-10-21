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
