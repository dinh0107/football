<?php

namespace App\Http\Controllers;

use App\Services\PremierLeagueService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

/**
 * Controller phục vụ Trang chủ - Lịch thi đấu & Kết quả theo ngày (Fixtures & Results)
 */
class HomeController extends Controller
{
    public function __construct(
        protected PremierLeagueService $premierLeagueService
    ) {}

    /**
     * Hiển thị danh sách trận đấu và kết quả theo ngày đã chọn
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // 1. Nhận tham số date (mặc định là hôm nay theo múi giờ Việt Nam Asia/Ho_Chi_Minh)
        $today = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
        $selectedDate = $request->query('date', $today);

        // Kiểm tra hợp lệ định dạng YYYY-MM-DD và ngày có thật (2026-13-45 từng gây lỗi 500)
        if (!is_string($selectedDate) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $selectedDate, $d) || !checkdate((int) $d[2], (int) $d[3], (int) $d[1])) {
            $selectedDate = $today;
        }

        // 2. Tạo 3 mốc ngày: Hôm qua, Hôm nay, Ngày mai theo chuẩn giờ Việt Nam
        $todayCarbon = Carbon::now('Asia/Ho_Chi_Minh');
        $selectedCarbon = Carbon::parse($selectedDate, 'Asia/Ho_Chi_Minh');

        $dateNav = [
            'yesterday' => $todayCarbon->copy()->subDay()->toDateString(),
            'today' => $today,
            'tomorrow' => $todayCarbon->copy()->addDay()->toDateString(),
            'selected' => $selectedDate,
            'isToday' => ($selectedDate === $today),
            'formattedSelected' => $selectedCarbon->format('d/m/Y'),
            'dayOfWeek' => $this->formatDayOfWeekVietnam($selectedCarbon),
        ];

        // 3. Gọi Service lấy danh sách trận theo ngày
        $fixtures = $this->premierLeagueService->getFixturesByDate($selectedDate);

        // 4. Phân loại danh sách trận đấu
        $liveMatches = [];
        $upcomingMatches = [];
        $finishedMatches = [];

        foreach ($fixtures as $match) {
            $status = $match['status'] ?? 'U';
            if ($status === 'L') {
                $liveMatches[] = $match;
            } elseif ($status === 'C') {
                $finishedMatches[] = $match;
            } else {
                $upcomingMatches[] = $match;
            }
        }

        $season = $this->premierLeagueService->getCurrentCompSeason();

        // 5. Trả về view home.blade.php
        return view('home', [
            'dateNav' => $dateNav,
            'liveMatches' => $liveMatches,
            'upcomingMatches' => $upcomingMatches,
            'finishedMatches' => $finishedMatches,
            'allMatches' => $fixtures,
            'season' => $season,
        ]);
    }

    /**
     * Helper chuyển đổi thứ trong tuần sang tiếng Việt thân thiện
     */
    protected function formatDayOfWeekVietnam(Carbon $date): string
    {
        return match ($date->dayOfWeek) {
            Carbon::SUNDAY => 'Chủ Nhật',
            Carbon::MONDAY => 'Thứ Hai',
            Carbon::TUESDAY => 'Thứ Ba',
            Carbon::WEDNESDAY => 'Thứ Tư',
            Carbon::THURSDAY => 'Thứ Năm',
            Carbon::FRIDAY => 'Thứ Sáu',
            Carbon::SATURDAY => 'Thứ Bảy',
        };
    }
}
