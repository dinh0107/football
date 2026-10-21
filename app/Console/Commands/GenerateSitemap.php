<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PremierLeagueService;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Cache;

/**
 * Command: php artisan sitemap:generate
 *
 * Tự động tạo hệ thống Sitemap Index Đa Tầng chuẩn Google Search Console:
 * 1. public/sitemap.xml         (File gốc Sitemap Index)
 * 2. public/sitemap-upcoming.xml (Các trận sắp đá trong 7 ngày tới - priority 0.9, daily)
 * 3. public/sitemap-results.xml  (Các trận đã kết thúc status 'C' - priority 0.6, weekly)
 * 4. public/sitemap-static.xml   (Các trang tĩnh cốt lõi - priority 0.8, daily)
 */
class GenerateSitemap extends Command
{
    /**
     * Tên và cú pháp của lệnh Artisan
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * Mô tả lệnh
     *
     * @var string
     */
    protected $description = 'Tự động tạo hệ thống Sitemap Index Đa Tầng (Upcoming, Results, Static) chuẩn Google Search Console';

    /**
     * Thực thi lệnh Artisan
     */
    public function handle(PremierLeagueService $plService): int
    {
        $this->info('Đang bắt đầu quét dữ liệu và tạo hệ thống Sitemap Index Đa Tầng...');

        $controller = new SitemapController();
        $files = $controller->generateAllSitemaps($plService);

        // 1. Ghi file Sitemap Index gốc
        $indexPath = public_path('sitemap.xml');
        file_put_contents($indexPath, $files['index']);

        // 2. Ghi file Sitemap Upcoming (các trận sắp đá 7 ngày tới)
        $upcomingPath = public_path('sitemap-upcoming.xml');
        file_put_contents($upcomingPath, $files['upcoming']);

        // 3. Ghi file Sitemap Results (các trận đã kết thúc status 'C')
        $resultsPath = public_path('sitemap-results.xml');
        file_put_contents($resultsPath, $files['results']);

        // 4. Ghi file Sitemap Static (các trang tĩnh)
        $staticPath = public_path('sitemap-static.xml');
        file_put_contents($staticPath, $files['static']);

        // Xóa cache để các request dynamic cập nhật tức thì
        Cache::forget('sitemap_index_xml');
        Cache::forget('sitemap_upcoming_xml');
        Cache::forget('sitemap_results_xml');
        Cache::forget('sitemap_static_xml');

        $this->info("Đã tạo thành công file sitemap.xml (Sitemap Index) tại: {$indexPath}");
        $this->info("Đã tạo thành công file sitemap-upcoming.xml tại: {$upcomingPath}");
        $this->info("Đã tạo thành công file sitemap-results.xml tại: {$resultsPath}");
        $this->info("Đã tạo thành công file sitemap-static.xml tại: {$staticPath}");

        return Command::SUCCESS;
    }
}
