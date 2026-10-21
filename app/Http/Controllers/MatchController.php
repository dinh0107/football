<?php

namespace App\Http\Controllers;

use App\Services\PremierLeagueService;
use App\Services\AiMatchService;
use App\Helpers\FootballSeoHelper;
use App\Models\MatchVote;
use Illuminate\View\View;

/**
 * Controller phục vụ Trang Trung tâm trận đấu (Match Center) chi tiết
 */
class MatchController extends Controller
{
    public function __construct(
        protected PremierLeagueService $premierLeagueService,
        protected AiMatchService $aiMatchService
    ) {}

    /**
     * Hiển thị chi tiết trận đấu: Header, Diễn biến, Sân 2D, Thống kê, AI Insight & Vote
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

        // 5. Kênh phát sóng tại Việt Nam (TV Guide) & SEO Metadata đầy đủ
        $tvChannel = FootballSeoHelper::getTvChannel($details);
        $details['tv_channel'] = $tvChannel;

        $seo = FootballSeoHelper::generateSeoMetadata($details);

        // 6. Phase 7: Thống kê bình chọn dự đoán trận đấu (Community Match Voting)
        $userIp = (string) request()->ip();
        $voteStats = MatchVote::getStatsForMatch($matchId, $userIp);

        // 7. Phase 7: Trợ lý AI Tự động nhận định & Tóm tắt trận đấu
        $status = $details['status'] ?? 'U';
        $homeTeam = $details['teams'][0]['team'] ?? [];
        $awayTeam = $details['teams'][1]['team'] ?? [];
        $ground = $details['ground']['name'] ?? 'SVĐ Ngoại Hạng Anh';

        if ($status === 'C') {
            $aiInsight = $this->aiMatchService->generateMatchSummary($events, $stats, $details);
        } else {
            $aiInsight = $this->aiMatchService->generateMatchPreview($homeTeam, $awayTeam, [
                'match_id' => $matchId,
                'ground' => $ground,
            ]);
        }

        return view('match-detail', [
            'match' => $details,
            'events' => $events,
            'lineups' => $lineups,
            'stats' => $stats,
            'seo' => $seo,
            'tvChannel' => $tvChannel,
            'voteStats' => $voteStats,
            'aiInsight' => $aiInsight,
        ]);
    }
}
