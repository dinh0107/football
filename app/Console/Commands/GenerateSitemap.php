<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PremierLeagueService;
use App\Helpers\SeoHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Command: php artisan sitemap:generate
 *
 * Tự động tạo file public/sitemap.xml chứa danh sách URL trận đấu chuẩn SEO
 * Đón đầu lập chỉ mục Google Search Console cho các trận đấu cuối tuần trong 7 ngày tới.
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
    protected $description = 'Tự động quét các trận đấu trong 7 ngày tới và trang tĩnh để xuất file sitemap.xml chuẩn Google Search Console';

    /**
     * Thực thi lệnh Artisan
     */
    public function handle(PremierLeagueService $plService): int
    {
        $this->info('Đang bắt đầu quét dữ liệu và tạo file sitemap.xml tự động...');

        $baseUrl = url('/');
        $urls = [];

        // 1. Thêm các trang tĩnh cốt lõi
        $urls[] = [
            'loc' => $baseUrl,
            'lastmod' => Carbon::now('Asia/Ho_Chi_Minh')->toIso8601String(),
            'changefreq' => 'daily',
            'priority' => '1.0',
        ];

        $urls[] = [
            'loc' => url('/bang-xep-hang'),
            'lastmod' => Carbon::now('Asia/Ho_Chi_Minh')->toIso8601String(),
            'changefreq' => 'hourly',
            'priority' => '0.9',
        ];

        $urls[] = [
            'loc' => url('/lich-thi-dau'),
            'lastmod' => Carbon::now('Asia/Ho_Chi_Minh')->toIso8601String(),
            'changefreq' => 'daily',
            'priority' => '0.9',
        ];

        $urls[] = [
            'loc' => url('/truc-tiep'),
            'lastmod' => Carbon::now('Asia/Ho_Chi_Minh')->toIso8601String(),
            'changefreq' => 'always',
            'priority' => '0.9',
        ];

        // 2. Quét tất cả các trận đấu trong vòng 7 ngày tới (đón đầu các trận cuối tuần)
        $today = Carbon::now('Asia/Ho_Chi_Minh');
        $allMatches = [];

        try {
            // Lấy các trận theo mùa giải hiện tại
            $seasonId = $plService->getCurrentCompSeason()['id'] ?? null;
            $seasonFixtures = $plService->getFixtures(null, $seasonId);
            if (!empty($seasonFixtures)) {
                $allMatches = array_merge($allMatches, $seasonFixtures);
            }

            // Quét từng ngày trong vòng 7 ngày tới (+0 đến +7 ngày)
            for ($i = 0; $i <= 7; $i++) {
                $dateStr = $today->copy()->addDays($i)->format('Y-m-d');
                $matchesOnDate = $plService->getFixturesByDate($dateStr);
                if (!empty($matchesOnDate)) {
                    $allMatches = array_merge($allMatches, $matchesOnDate);
                }
            }

            $seenIds = [];
            foreach ($allMatches as $match) {
                $id = (int) ($match['id'] ?? 0);
                if (!$id || isset($seenIds[$id])) {
                    continue;
                }
                $seenIds[$id] = true;

                // Sinh URL slug chuẩn SEO bằng SeoHelper
                $slug = SeoHelper::generateMatchSlug($match);
                $loc = route('match.detail', ['slug' => $slug]);

                $lastmod = Carbon::now('Asia/Ho_Chi_Minh')->toIso8601String();
                if (!empty($match['kickoff']['millis'])) {
                    $lastmod = Carbon::createFromTimestampMs($match['kickoff']['millis'], 'Asia/Ho_Chi_Minh')->toIso8601String();
                }

                // Thiết lập priority = 0.9 và changefreq = hourly cho các trận đón đầu index
                $urls[] = [
                    'loc' => $loc,
                    'lastmod' => $lastmod,
                    'changefreq' => 'hourly',
                    'priority' => '0.9',
                ];
            }
        } catch (\Throwable $e) {
            $this->warn('Cảnh báo khi tải dữ liệu trận đấu: ' . $e->getMessage());
            Log::error('GenerateSitemap command error: ' . $e->getMessage());
        }

        // 3. Xây dựng cấu trúc XML chuẩn giao thức sitemaps.org
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        foreach ($urls as $u) {
            $locEscaped = htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8');
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$locEscaped}</loc>\n";
            $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        // 4. Lưu ra file public/sitemap.xml
        $targetPath = public_path('sitemap.xml');
        file_put_contents($targetPath, $xml);

        $totalUrls = count($urls);
        $this->info("Đã tạo thành công file sitemap.xml tại: {$targetPath} với {$totalUrls} URLs.");

        return Command::SUCCESS;
    }
}
