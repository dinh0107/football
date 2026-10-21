<?php

namespace App\Http\Controllers;

use App\Services\PremierLeagueService;
use App\Helpers\FootballSeoHelper;
use Illuminate\View\View;

/**
 * Controller phục vụ Phase 4: Trang Trung tâm trận đấu (Match Center) chi tiết
 */
class MatchController extends Controller
{
    public function __construct(
        protected PremierLeagueService $premierLeagueService
    ) {}

    /**
     * Hiển thị chi tiết trận đấu: Header, Diễn biến, Sân 2D & Thống kê
     *
     * @param int|string $id
     * @return View
     */
    public function show(int|string $id): View
    {
        $matchId = FootballSeoHelper::extractIdFromSlug((string) $id);
        if (!$matchId) {
            abort(404, 'Mã trận đấu không hợp lệ.');
        }

        // 1. Lấy thông tin chung, trọng tài, sân vận động
        $details = $this->premierLeagueService->getMatchDetails($matchId);
        if (!$details) {
            abort(404, 'Không tìm thấy thông tin trận đấu.');
        }

        // 2. Lấy sự kiện trận đấu (bàn thắng, thẻ phạt, thay người theo phút)
        $events = $this->premierLeagueService->getMatchEvents($matchId);

        // 3. Lấy đội hình ra sân và ma trận hàng cầu thủ cho sân 2D
        $lineups = $this->premierLeagueService->getMatchLineups($matchId);

        // 4. Lấy thống kê so sánh 2 đội (kiểm soát bóng, sút, phạt góc, phạm lỗi)
        $stats = $this->premierLeagueService->getMatchStats($matchId);

        // 5. Schema.org & SEO Metadata
        $seo = [
            'title' => FootballSeoHelper::generateTitle($details),
            'description' => FootballSeoHelper::generateMetaDescription($details),
            'schema' => FootballSeoHelper::generateSportsEventSchema($details),
        ];

        return view('match-detail', [
            'match' => $details,
            'events' => $events,
            'lineups' => $lineups,
            'stats' => $stats,
            'seo' => $seo,
        ]);
    }
}
