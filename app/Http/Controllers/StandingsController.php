<?php

namespace App\Http\Controllers;

use App\Services\PremierLeagueService;
use Illuminate\View\View;

/**
 * Controller phục vụ Phase 3: Bảng Xếp Hạng Ngoại Hạng Anh đầy đủ & BXH Trực Tiếp (Live Table As-It-Stands)
 */
class StandingsController extends Controller
{
    public function __construct(
        protected PremierLeagueService $premierLeagueService
    ) {}

    /**
     * Hiển thị Bảng Xếp Hạng Ngoại Hạng Anh và Top Vua Phá Lưới
     *
     * @return View
     */
    public function index(): View
    {
        // 1. Lấy dữ liệu Bảng Xếp Hạng (tự động tính điểm Live nếu có trận đang diễn ra)
        $liveStandingsData = $this->premierLeagueService->getLiveStandings();
        
        // 2. Lấy danh sách Top 10 Vua phá lưới kèm số kiến tạo
        $topScorers = $this->premierLeagueService->getTopScorers();

        $season = $liveStandingsData['season'] ?? $this->premierLeagueService->getCurrentCompSeason();
        $isLiveMode = $liveStandingsData['isLive'] ?? false;
        $liveMatchesCount = $liveStandingsData['liveMatchesCount'] ?? 0;

        // 3. Trả về view standings.blade.php
        return view('standings', [
            'standings' => $liveStandingsData['entries'] ?? [],
            'topScorers' => $topScorers,
            'season' => $season,
            'isLiveMode' => $isLiveMode,
            'liveMatchesCount' => $liveMatchesCount,
        ]);
    }
}
