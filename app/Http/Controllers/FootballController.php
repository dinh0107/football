<?php

namespace App\Http\Controllers;

use App\Helpers\FootballSeoHelper;
use App\Services\PulseLiveService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

/**
 * Controller quản lý hiển thị Bảng xếp hạng, Lịch thi đấu, Trực tiếp và Chi tiết trận đấu Ngoại Hạng Anh
 */
class FootballController extends Controller
{
    public function __construct(
        protected PulseLiveService $pulseLiveService
    ) {}

    /**
     * Trang Bảng Xếp Hạng Ngoại Hạng Anh (Standings)
     * URL: / hoặc /bang-xep-hang
     */
    public function standings(): View
    {
        $season = $this->pulseLiveService->getCurrentCompSeason();
        $standingsData = $this->pulseLiveService->getStandings($season['id']);
        $liveMatches = $this->pulseLiveService->getLiveMatches($season['id']);
        $currentGameweek = $this->pulseLiveService->getCurrentGameweek($season['id']);

        $seo = [
            'title' => "Bảng Xếp Hạng Ngoại Hạng Anh {$season['label']} Mới Nhất | BXH Premier League",
            'description' => "Cập nhật bảng xếp hạng Ngoại Hạng Anh mùa giải {$season['label']} chuẩn xác 24/7. Thứ hạng, điểm số, hiệu số và phong độ các đội Arsenal, Man City, Liverpool, MU.",
            'canonical' => route('standings'),
            'type' => 'website'
        ];

        return view('football.standings', [
            'season' => $season,
            'standings' => $standingsData['entries'] ?? [],
            'liveMatches' => $liveMatches,
            'currentGameweek' => $currentGameweek,
            'seo' => $seo,
        ]);
    }

    /**
     * Trang Lịch Thi Đấu & Kết Quả Ngoại Hạng Anh (Fixtures)
     * URL: /lich-thi-dau
     */
    public function fixtures(Request $request): View
    {
        $season = $this->pulseLiveService->getCurrentCompSeason();
        $gameweeks = $this->pulseLiveService->getGameweeks($season['id']);
        $currentGw = $this->pulseLiveService->getCurrentGameweek($season['id']);

        // Xác định vòng đấu được chọn (theo query param hoặc vòng hiện tại)
        $selectedGwId = $request->query('vong');
        if ($selectedGwId) {
            $selectedGwId = (int) $selectedGwId;
            $selectedGw = collect($gameweeks)->firstWhere('id', $selectedGwId) ?? $currentGw;
        } else {
            $selectedGw = $currentGw;
            $selectedGwId = $selectedGw['id'] ?? null;
        }

        $fixtures = $this->pulseLiveService->getFixtures($selectedGwId, $season['id']);
        $gwNumber = $selectedGw['gameweek'] ?? 'Hiện tại';

        $seo = [
            'title' => "Lịch Thi Đấu & Kết Quả Ngoại Hạng Anh Vòng {$gwNumber} | Premier League",
            'description' => "Lịch thi đấu và kết quả bóng đá Ngoại Hạng Anh vòng {$gwNumber} mùa giải {$season['label']}. Tỉ số, giờ thi đấu theo giờ Việt Nam và kênh phát sóng trực tiếp.",
            'canonical' => route('fixtures', ['vong' => $selectedGwId]),
            'type' => 'website'
        ];

        return view('football.fixtures', [
            'season' => $season,
            'gameweeks' => $gameweeks,
            'selectedGw' => $selectedGw,
            'fixtures' => $fixtures,
            'seo' => $seo,
        ]);
    }

    /**
     * Trang Trực Tiếp Bóng Đá Ngoại Hạng Anh (Live Center)
     * URL: /truc-tiep
     */
    public function live(): View
    {
        $season = $this->pulseLiveService->getCurrentCompSeason();
        $liveMatches = $this->pulseLiveService->getLiveMatches($season['id']);
        $currentGw = $this->pulseLiveService->getCurrentGameweek($season['id']);
        
        // Lấy thêm các trận của vòng hiện tại để hiển thị khi chưa có trận live
        $currentGwFixtures = $currentGw ? $this->pulseLiveService->getFixtures($currentGw['id'], $season['id']) : [];

        $liveCount = count($liveMatches);
        $titlePrefix = $liveCount > 0 ? "[{$liveCount} Trận Đang Đá]" : "[Trực Tiếp]";

        $seo = [
            'title' => "{$titlePrefix} Trực Tiếp Bóng Đá Ngoại Hạng Anh Hôm Nay | Livescore 24/7",
            'description' => "Theo dõi trực tiếp tỷ số Ngoại Hạng Anh hôm nay với tốc độ cập nhật siêu nhanh. Tường thuật trực tiếp diễn biến trận đấu, bàn thắng, thẻ phạt và BXH trực tuyến.",
            'canonical' => route('live'),
            'type' => 'website'
        ];

        return view('football.live', [
            'season' => $season,
            'liveMatches' => $liveMatches,
            'currentGw' => $currentGw,
            'fixtures' => $currentGwFixtures,
            'seo' => $seo,
        ]);
    }

    /**
     * Trang Chi Tiết Trận Đấu (Match Detail)
     * URL: /tran-dau/{slug}
     * Cấu trúc slug: /tran-dau/[doi-nha]-vs-[doi-khach]-[fixture_id]
     */
    public function matchDetail(Request $request, string $slug): View|RedirectResponse
    {
        $fixtureId = FootballSeoHelper::extractIdFromSlug($slug);
        if (!$fixtureId) {
            abort(404, 'Không tìm thấy thông tin trận đấu.');
        }

        $match = $this->pulseLiveService->getMatchDetail($fixtureId);
        if (!$match) {
            abort(404, 'Trận đấu không tồn tại hoặc dữ liệu tạm thời chưa sẵn sàng.');
        }

        // Kiểm tra Canonical URL: nếu slug không khớp chuẩn SEO, 301 Redirect về URL chuẩn
        $canonicalSlug = FootballSeoHelper::generateMatchSlug($match);
        if ($slug !== $canonicalSlug) {
            return redirect()->route('match.detail', ['slug' => $canonicalSlug], 301);
        }

        // Tự động sinh dữ liệu SEO động
        $pageTitle = FootballSeoHelper::generateTitle($match);
        $metaDescription = FootballSeoHelper::generateMetaDescription($match);
        $schemaJsonLd = FootballSeoHelper::generateSportsEventSchema($match, route('match.detail', ['slug' => $canonicalSlug]));

        $seo = [
            'title' => $pageTitle,
            'description' => $metaDescription,
            'canonical' => route('match.detail', ['slug' => $canonicalSlug]),
            'type' => 'article',
            'schema' => $schemaJsonLd
        ];

        return view('football.match-detail', [
            'match' => $match,
            'seo' => $seo,
        ]);
    }

    /**
     * Endpoint API nội bộ trả về dữ liệu Live Scores để Vanilla JS tự động cập nhật mỗi 30s
     * URL: /api/live-scores
     */
    public function apiLiveScores(): JsonResponse
    {
        $season = $this->pulseLiveService->getCurrentCompSeason();
        $liveMatches = $this->pulseLiveService->getLiveMatches($season['id']);
        
        $formatted = array_map(function ($m) {
            return [
                'id' => (int) $m['id'],
                'slug' => FootballSeoHelper::generateMatchSlug($m),
                'status' => $m['status'] ?? 'U',
                'clock' => $m['clock']['label'] ?? '',
                'home' => [
                    'name' => $m['teams'][0]['team']['name'] ?? '',
                    'shortName' => $m['teams'][0]['team']['shortName'] ?? '',
                    'score' => isset($m['teams'][0]['score']) ? (int) $m['teams'][0]['score'] : null,
                    'badge' => PulseLiveService::getTeamBadgeUrl($m['teams'][0]['team']['id'] ?? 0, 70),
                ],
                'away' => [
                    'name' => $m['teams'][1]['team']['name'] ?? '',
                    'shortName' => $m['teams'][1]['team']['shortName'] ?? '',
                    'score' => isset($m['teams'][1]['score']) ? (int) $m['teams'][1]['score'] : null,
                    'badge' => PulseLiveService::getTeamBadgeUrl($m['teams'][1]['team']['id'] ?? 0, 70),
                ]
            ];
        }, $liveMatches);

        return response()->json([
            'success' => true,
            'timestamp' => now()->toISOString(),
            'count' => count($formatted),
            'matches' => $formatted
        ]);
    }

    /**
     * Chuyển hướng hoặc xem trực tiếp trận đấu theo ID (Ví dụ: /truc-tiep/128923)
     */
    public function liveMatchById(int $id): RedirectResponse
    {
        $match = $this->pulseLiveService->getMatchDetail($id);
        if (!$match) {
            abort(404, 'Không tìm thấy thông tin trận đấu.');
        }

        $slug = FootballSeoHelper::generateMatchSlug($match);
        return redirect()->route('match.detail', ['slug' => $slug]);
    }
}
