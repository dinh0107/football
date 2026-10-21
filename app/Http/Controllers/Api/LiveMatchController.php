<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PremierLeagueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LiveMatchController extends Controller
{
    public function __construct(
        protected PremierLeagueService $plService
    ) {}

    /**
     * GET /api/matches/live
     * Trả về JSON danh sách các trận đấu đang diễn ra kèm:
     * id, home_score, away_score, minute, status, danh sách sự kiện mới nhất.
     * Tối ưu cache Redis/File với thời gian 15-20 giây.
     */
    public function index(Request $request): JsonResponse
    {
        $fixtureId = $request->filled('fixture_id') ? (int) $request->input('fixture_id') : null;

        // Chế độ giả lập test bàn thắng/demo kiểm thử trực quan
        if ($request->boolean('test_goal') || $request->boolean('demo')) {
            $demoHomeScore = $request->has('home_score') ? (int) $request->input('home_score') : 2;
            $demoAwayScore = $request->has('away_score') ? (int) $request->input('away_score') : 1;
            $demoMatchId = $fixtureId ?: 999999;

            return response()->json([
                [
                    'id' => $demoMatchId,
                    'home_team' => [
                        'id' => 1,
                        'name' => 'Arsenal',
                        'shortName' => 'ARS',
                        'badge' => $this->plService->getTeamBadgeUrl(1),
                    ],
                    'away_team' => [
                        'id' => 11,
                        'name' => 'Chelsea',
                        'shortName' => 'CHE',
                        'badge' => $this->plService->getTeamBadgeUrl(11),
                    ],
                    'home_score' => $demoHomeScore,
                    'away_score' => $demoAwayScore,
                    'minute' => "78'",
                    'status' => 'L',
                    'latest_events' => [
                        [
                            'type' => 'G',
                            'clock' => "78'",
                            'minute' => 78,
                            'icon' => 'goal',
                            'desc' => 'Bàn thắng',
                            'isHome' => true,
                            'playerName' => 'Bukayo Saka',
                            'score' => "{$demoHomeScore} - {$demoAwayScore}",
                        ]
                    ],
                    'updated_at' => now()->toIso8601String(),
                ]
            ]);
        }

        // Cache 15 giây theo chuẩn yêu cầu (15-20s)
        $cacheKey = 'epl_api_live_matches_feed_' . ($fixtureId ?: 'all');
        $ttl = 15;

        $matches = Cache::remember($cacheKey, $ttl, function () use ($fixtureId) {
            return $this->plService->getLiveFeed($fixtureId);
        });

        return response()->json($matches);
    }
}
