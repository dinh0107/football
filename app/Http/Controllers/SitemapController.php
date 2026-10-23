<?php

namespace App\Http\Controllers;

use App\Services\PremierLeagueService;
use App\Helpers\SeoHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Controller quản lý Hệ thống Sitemap Index Đa Tầng chuẩn Google Search Console
 *
 * Gồm 4 endpoints:
 * 1. /sitemap.xml          -> Sitemap Index trỏ tới 3 sitemap con
 * 2. /sitemap-upcoming.xml -> Các trận sắp diễn ra trong 7 ngày tới (priority 0.9, daily)
 * 3. /sitemap-results.xml  -> Các trận đã kết thúc (status 'C') của mùa giải (priority 0.6, weekly)
 * 4. /sitemap-static.xml   -> Các trang tĩnh cốt lõi (priority 0.8, daily)
 */
class SitemapController extends Controller
{
    /**
     * Endpoint gốc: GET /sitemap.xml (Sitemap Index)
     */
    public function index(PremierLeagueService $plService): Response
    {
        $xml = Cache::remember('sitemap_index_xml', 1800, function () use ($plService) {
            $files = $this->generateAllSitemaps($plService);
            return $files['index'];
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * Endpoint con 1: GET /sitemap-upcoming.xml
     */
    public function upcoming(PremierLeagueService $plService): Response
    {
        $xml = Cache::remember('sitemap_upcoming_xml', 1800, function () use ($plService) {
            $files = $this->generateAllSitemaps($plService);
            return $files['upcoming'];
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * Endpoint con 2: GET /sitemap-results.xml
     */
    public function results(PremierLeagueService $plService): Response
    {
        $xml = Cache::remember('sitemap_results_xml', 3600, function () use ($plService) {
            $files = $this->generateAllSitemaps($plService);
            return $files['results'];
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * Endpoint con 3: GET /sitemap-static.xml
     */
    public function staticPages(PremierLeagueService $plService): Response
    {
        $xml = Cache::remember('sitemap_static_xml', 86400, function () use ($plService) {
            $files = $this->generateAllSitemaps($plService);
            return $files['static'];
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * Thu thập dữ liệu trận đấu và sinh trọn bộ 4 file sitemap
     *
     * @param PremierLeagueService $plService
     * @return array{index: string, upcoming: string, results: string, static: string}
     */
    public function generateAllSitemaps(PremierLeagueService $plService): array
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $nowIso = $now->toIso8601String();

        // 1. Phân loại trang tĩnh (sitemap-static.xml)
        $staticUrls = [
            [
                'loc' => url('/'),
                'lastmod' => $nowIso,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => url('/bang-xep-hang'),
                'lastmod' => $nowIso,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => url('/lich-thi-dau'),
                'lastmod' => $nowIso,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => url('/truc-tiep'),
                'lastmod' => $nowIso,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => url('/gioi-thieu'),
                'lastmod' => $nowIso,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => url('/lien-he'),
                'lastmod' => $nowIso,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ],
            [
                'loc' => url('/chinh-sach-bao-mat'),
                'lastmod' => $nowIso,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => url('/mien-tru-trach-nhiem'),
                'lastmod' => $nowIso,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
        ];

        // 2. Thu thập danh sách trận đấu và phân loại tự động
        $upcomingUrls = [];
        $resultsUrls = [];

        try {
            $seasonId = $plService->getCurrentCompSeason()['id'] ?? null;
            $allFixtures = $plService->getFixtures(null, $seasonId);

            // Quét các ngày trong 7 ngày tới
            for ($offset = 0; $offset <= 7; $offset++) {
                $dateStr = $now->copy()->addDays($offset)->format('Y-m-d');
                $dateMatches = $plService->getFixturesByDate($dateStr);
                if (!empty($dateMatches)) {
                    $allFixtures = array_merge($allFixtures, $dateMatches);
                }
            }

            $seenIds = [];
            foreach ($allFixtures as $match) {
                $id = (int) ($match['id'] ?? 0);
                if (!$id || isset($seenIds[$id])) {
                    continue;
                }
                $seenIds[$id] = true;

                $slug = SeoHelper::generateMatchSlug($match);
                $loc = route('match.detail', ['slug' => $slug]);

                $status = $match['status'] ?? 'U';
                $kickoffMillis = $match['kickoff']['millis'] ?? null;
                $kickoffAt = $kickoffMillis ? Carbon::createFromTimestampMs($kickoffMillis, 'Asia/Ho_Chi_Minh') : null;
                // Google bỏ qua lastmod ở tương lai, nên trận chưa đá không khai lastmod
                $lastmod = $kickoffAt && $kickoffAt->lte($now) ? $kickoffAt->toIso8601String() : null;

                // LOGIC LUÂN CHUYỂN TỰ ĐỘNG THEO TRẠNG THÁI TRẬN:
                // Nếu status = 'C' (Completed/Đã xong) -> chuyển sang sitemap-results.xml (priority 0.6, weekly)
                // Nếu status != 'C' (Upcoming/Live)   -> thuộc về sitemap-upcoming.xml (priority 0.9, daily)
                if ($status === 'C') {
                    $resultsUrls[] = [
                        'loc' => $loc,
                        'lastmod' => $lastmod,
                        'changefreq' => 'weekly',
                        'priority' => '0.6',
                    ];
                } else {
                    $upcomingUrls[] = [
                        'loc' => $loc,
                        'lastmod' => $lastmod,
                        'changefreq' => 'daily',
                        'priority' => '0.9',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('Lỗi khi phân loại Sitemap đa tầng: ' . $e->getMessage());
        }

        // Tạo XML cho 3 sitemap con
        $xmlUpcoming = $this->renderUrlsetXml($upcomingUrls);
        $xmlResults = $this->renderUrlsetXml($resultsUrls);
        $xmlStatic = $this->renderUrlsetXml($staticUrls);

        // Tạo XML cho Sitemap Index gốc
        $subSitemaps = [
            ['loc' => url('/sitemap-upcoming.xml'), 'lastmod' => $nowIso],
            ['loc' => url('/sitemap-results.xml'), 'lastmod' => $nowIso],
            ['loc' => url('/sitemap-static.xml'), 'lastmod' => $nowIso],
        ];
        $xmlIndex = $this->renderSitemapIndexXml($subSitemaps);

        return [
            'index' => $xmlIndex,
            'upcoming' => $xmlUpcoming,
            'results' => $xmlResults,
            'static' => $xmlStatic,
        ];
    }

    /**
     * Render XML cho Sitemap Index (Google Standard)
     */
    public function renderSitemapIndexXml(array $subSitemaps): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($subSitemaps as $s) {
            $loc = htmlspecialchars($s['loc'], ENT_XML1, 'UTF-8');
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            $xml .= "    <lastmod>{$s['lastmod']}</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }
        $xml .= '</sitemapindex>';
        return $xml;
    }

    /**
     * Render XML cho một URL set chuẩn
     */
    public function renderUrlsetXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

        foreach ($urls as $u) {
            $loc = htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8');
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$loc}</loc>\n";
            if (!empty($u['lastmod'])) {
                $xml .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
            }
            $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        return $xml;
    }

    /**
     * Tương thích ngược: Sinh chuỗi Sitemap XML đơn lẻ nếu cần
     */
    public function generateSitemapXml(PremierLeagueService $plService): string
    {
        $files = $this->generateAllSitemaps($plService);
        return $files['index'];
    }
}
