<?php

namespace App\Http\Controllers;

use App\Services\PremierLeagueService;
use App\Services\AiMatchService;
use App\Helpers\SeoHelper;
use App\Helpers\FootballSeoHelper;
use App\Models\MatchVote;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Controller phục vụ Trang Trung tâm trận đấu (Match Center) chi tiết & Programmatic SEO
 */
class MatchController extends Controller
{
    public function __construct(
        protected PremierLeagueService $premierLeagueService,
        protected AiMatchService $aiMatchService
    ) {}

    /**
     * Hiển thị chi tiết trận đấu: Header, Diễn biến, Sân 2D, Thống kê, AI Insight & SEO
     *
     * @param string $slug
     * @return View|RedirectResponse
     */
    public function show(string $slug): View|RedirectResponse
    {
        // Trích xuất match ID từ đuôi URL slug
        $matchId = SeoHelper::extractIdFromSlug($slug);
        if (!$matchId) {
            abort(404, 'Mã trận đấu không hợp lệ.');
        }

        // 1. Lấy thông tin chung, trọng tài, sân vận động
        $details = $this->premierLeagueService->getMatchDetails($matchId);
        if (!$details) {
            abort(404, 'Không tìm thấy thông tin trận đấu.');
        }

        // Xử lý 301 Permanent Redirect nếu URL slug bị sai tên đội hoặc sai định dạng cấu trúc
        $homeTeamName = $details['teams'][0]['team']['name'] ?? 'doi-nha';
        $awayTeamName = $details['teams'][1]['team']['name'] ?? 'doi-khach';

        $canonicalSlugWithPrefix = SeoHelper::generateMatchSlug($details, withPrefix: true);
        $canonicalSlugWithoutPrefix = SeoHelper::generateMatchSlug($details, withPrefix: false);
        $canonicalSimpleSlug = \Illuminate\Support\Str::slug("{$homeTeamName} vs {$awayTeamName}") . "-{$matchId}";

        $validSlugs = [
            $canonicalSlugWithPrefix,
            $canonicalSlugWithoutPrefix,
            $canonicalSimpleSlug,
        ];

        if (!is_numeric($slug) && !in_array($slug, $validSlugs)) {
            return redirect()->route('match.detail', ['slug' => $canonicalSlugWithPrefix], 301);
        }

        $status = $details['status'] ?? 'U';

        // 2. Lấy sự kiện trận đấu (bàn thắng, thẻ phạt, thay người theo phút)
        // Trận chưa đá chắc chắn chưa có sự kiện -> bỏ qua tính toán, trả về mảng rỗng ngay lập tức
        $events = ($status === 'U') ? [] : $this->premierLeagueService->getMatchEvents($matchId);

        // 3. Lấy đội hình ra sân và ma trận hàng cầu thủ cho sân 2D
        // Nếu trận chưa đá và chưa có danh sách cầu thủ ban tổ chức công bố -> trả về rỗng ngay lập tức
        $lineups = (empty($details['teamLists']) && $status === 'U')
            ? ['hasLineups' => false, 'home' => null, 'away' => null]
            : $this->premierLeagueService->getMatchLineups($matchId);

        // 4. Lấy thống kê so sánh 2 đội (kiểm soát bóng, sút, phạt góc, phạm lỗi)
        // Trận chưa đá -> trả về default stats ngay lập tức không tốn tài nguyên
        $stats = ($status === 'U') ? [
            'possession' => ['home' => 50, 'away' => 50],
            'shots' => ['home' => 0, 'away' => 0],
            'shotsOnTarget' => ['home' => 0, 'away' => 0],
            'corners' => ['home' => 0, 'away' => 0],
            'fouls' => ['home' => 0, 'away' => 0],
        ] : $this->premierLeagueService->getMatchStats($matchId);

        // 5. Kênh phát sóng tại Việt Nam (TV Guide) & SEO Metadata đầy đủ
        $tvChannel = SeoHelper::getTvChannel($details);
        $details['tv_channel'] = $tvChannel;

        $seo = FootballSeoHelper::generateSeoMetadata($details);
        $seoParagraph = SeoHelper::generateMatchSeoParagraph($details);

        // 6. Thống kê bình chọn dự đoán trận đấu (Community Match Voting)
        $userIp = (string) request()->ip();
        $voteStats = MatchVote::getStatsForMatch($matchId, $userIp);

        // 7. Trợ lý AI Tự động nhận định & Tóm tắt trận đấu
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
            'seoParagraph' => $seoParagraph,
            'tvChannel' => $tvChannel,
            'voteStats' => $voteStats,
            'aiInsight' => $aiInsight,
        ]);
    }
}
