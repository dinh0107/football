<?php

namespace App\Http\Controllers;

use App\Services\PremierLeagueService;
use App\Helpers\FootballSeoHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Controller phục vụ Phase 6: Xuất file Sitemap XML động cho Google Search Console
 */
class SitemapController extends Controller
{
    /**
     * Route GET /sitemap.xml
     * Trả về file XML chuẩn sitemap theo giao thức sitemaps.org
     */
    public function index(PremierLeagueService $plService): Response
    {
        $xml = Cache::remember('epl_sitemap_xml', 1800, function () use ($plService) {
            return $this->generateSitemapXml($plService);
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * Quét các trang tĩnh và danh sách trận đấu để tạo chuỗi XML hoàn chỉnh
     */
    public function generateSitemapXml(PremierLeagueService $plService): string
    {
        $baseUrl = url('/');
        $urls = [];

        // 1. Các trang tĩnh quan trọng
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

        // 2. Quét danh sách các trận đấu Ngoại Hạng Anh (các trận trong tuần và mùa giải)
        try {
            $seasonId = $plService->getCurrentCompSeason()['id'] ?? null;
            $fixtures = $plService->getFixtures(null, $seasonId);

            // Quét các trận đấu trong tuần (hôm nay +- 3 ngày)
            $today = Carbon::now('Asia/Ho_Chi_Minh');
            for ($offset = -3; $offset <= 3; $offset++) {
                $dateStr = $today->copy()->addDays($offset)->format('Y-m-d');
                $dateMatches = $plService->getFixturesByDate($dateStr);
                if (!empty($dateMatches)) {
                    $fixtures = array_merge($fixtures, $dateMatches);
                }
            }

            $seenIds = [];
            foreach ($fixtures as $match) {
                $id = (int) ($match['id'] ?? 0);
                if (!$id || isset($seenIds[$id])) {
                    continue;
                }
                $seenIds[$id] = true;

                $slug = FootballSeoHelper::generateMatchSlug($match);
                $loc = route('match.detail', ['slug' => $slug]);

                $status = $match['status'] ?? 'U';
                $changefreq = match ($status) {
                    'L' => 'always',
                    'U' => 'hourly',
                    default => 'monthly',
                };

                $priority = ($status === 'L') ? '0.9' : '0.8';

                $lastmod = Carbon::now('Asia/Ho_Chi_Minh')->toIso8601String();
                if (!empty($match['kickoff']['millis'])) {
                    $lastmod = Carbon::createFromTimestampMs($match['kickoff']['millis'], 'Asia/Ho_Chi_Minh')->toIso8601String();
                }

                $urls[] = [
                    'loc' => $loc,
                    'lastmod' => $lastmod,
                    'changefreq' => $changefreq,
                    'priority' => $priority,
                ];
            }
        } catch (\Throwable $e) {
            Log::error('Lỗi khi sinh Sitemap XML: ' . $e->getMessage());
        }

        // Tạo cấu trúc chuẩn XML cho Google Search Console
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

        return $xml;
    }
}
