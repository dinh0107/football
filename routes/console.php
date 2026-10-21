<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Phase 6: Command tạo sitemap.xml tự động cho Google Search Console
 */
Artisan::command('sitemap:generate', function (\App\Services\PremierLeagueService $plService) {
    $this->info('Đang tiến hành tạo file sitemap.xml cho Google Search Console...');
    $controller = new \App\Http\Controllers\SitemapController();
    $xml = $controller->generateSitemapXml($plService);
    $path = public_path('sitemap.xml');
    file_put_contents($path, $xml);
    $this->info("Đã tạo thành công file sitemap.xml tại: {$path}");
})->purpose('Tự động quét các trận đấu và trang tĩnh để xuất file sitemap.xml chuẩn Google Search Console');

