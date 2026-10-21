<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service kết nối và xử lý dữ liệu từ PulseLive API (Backend chính thức của Premier League)
 */
class PulseLiveService
{
    /**
     * Base URL của Football API PulseLive
     */
    protected string $baseUrl = 'https://footballapi.pulselive.com/football';

    /**
     * Headers bắt buộc để tránh CORS và xác thực phía server
     */
    protected array $headers = [
        'Origin' => 'https://www.premierleague.com',
        'Referer' => 'https://www.premierleague.com/',
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
        'Accept' => 'application/json, text/plain, */*',
        'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
    ];

    /**
     * ID Giải đấu Ngoại Hạng Anh (EN_PR) trên PulseLive luôn là 1
     */
    public const COMPETITION_ID = 1;

    /**
     * Tạo HTTP Client với các headers chuẩn
     */
    protected function client()
    {
        return Http::withHeaders($this->headers)
            ->timeout(10)
            ->retry(2, 200);
    }

    /**
     * Tự động xác định CompSeason ID mới nhất của Premier League
     * Cache trong 24 giờ (86400 giây)
     */
    public function getCurrentCompSeason(): array
    {
        return Cache::remember('epl_current_comp_season', 86400, function () {
            try {
                $response = $this->client()->get("{$this->baseUrl}/competitions/" . self::COMPETITION_ID . "/compseasons");
                if ($response->successful()) {
                    $seasons = $response->json('content', []);
                    if (!empty($seasons)) {
                        // Season mới nhất luôn nằm ở phần tử đầu tiên
                        return [
                            'id' => (int) $seasons[0]['id'],
                            'label' => $seasons[0]['label'] ?? '2026/27'
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Không thể lấy CompSeason từ PulseLive: ' . $e->getMessage());
            }

            // Fallback mùa giải mặc định
            return ['id' => 841, 'label' => 'English Premier League Season 2026/2027'];
        });
    }

    /**
     * Lấy Bảng Xếp Hạng (Standings) Ngoại Hạng Anh
     * Cache: 1 giờ (3600s) theo yêu cầu cốt lõi
     */
    public function getStandings(?int $seasonId = null): array
    {
        $season = $seasonId ?: $this->getCurrentCompSeason()['id'];
        $cacheKey = "epl_standings_{$season}";

        return Cache::remember($cacheKey, 3600, function () use ($season) {
            try {
                $response = $this->client()->get("{$this->baseUrl}/compseasons/{$season}/standings");
                if ($response->successful()) {
                    $tables = $response->json('tables', []);
                    if (!empty($tables) && isset($tables[0]['entries'])) {
                        return [
                            'season' => $response->json('compSeason', []),
                            'entries' => $tables[0]['entries']
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Lỗi khi lấy Bảng Xếp Hạng từ PulseLive: ' . $e->getMessage());
            }

            return ['season' => [], 'entries' => []];
        });
    }

    /**
     * Lấy danh sách các vòng đấu (Gameweeks) của mùa giải
     * Cache: 1 ngày (86400s)
     */
    public function getGameweeks(?int $seasonId = null): array
    {
        $season = $seasonId ?: $this->getCurrentCompSeason()['id'];
        $cacheKey = "epl_gameweeks_{$season}";

        return Cache::remember($cacheKey, 86400, function () use ($season) {
            try {
                $response = $this->client()->get("{$this->baseUrl}/compseasons/{$season}/gameweeks");
                if ($response->successful()) {
                    return $response->json('gameweeks', []);
                }
            } catch (\Throwable $e) {
                Log::error('Lỗi khi lấy Gameweeks từ PulseLive: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Tìm vòng đấu hiện tại (đang diễn ra hoặc sắp tới nhất)
     */
    public function getCurrentGameweek(?int $seasonId = null): ?array
    {
        $gameweeks = $this->getGameweeks($seasonId);
        if (empty($gameweeks)) {
            return null;
        }

        // Tìm vòng đầu tiên có status = 'L' (Live) hoặc 'U' (Upcoming)
        foreach ($gameweeks as $gw) {
            if (in_array($gw['status'] ?? '', ['L', 'U'])) {
                return $gw;
            }
        }

        // Nếu tất cả đã hoàn thành, trả về vòng cuối cùng
        return end($gameweeks) ?: null;
    }

    /**
     * Lấy Lịch Thi Đấu & Kết Quả (Fixtures)
     * Cache: 1 ngày (86400s) theo yêu cầu cốt lõi
     */
    public function getFixtures(?int $gameweekId = null, ?int $seasonId = null): array
    {
        $season = $seasonId ?: $this->getCurrentCompSeason()['id'];
        $cacheKey = "epl_fixtures_{$season}_gw_" . ($gameweekId ?? 'all');

        return Cache::remember($cacheKey, 86400, function () use ($season, $gameweekId) {
            try {
                $params = [
                    'comps' => self::COMPETITION_ID,
                    'compSeasons' => $season,
                    'page' => 0,
                    'pageSize' => 40,
                    'sort' => 'asc',
                ];

                if ($gameweekId) {
                    $params['gameweeks'] = $gameweekId;
                }

                $response = $this->client()->get("{$this->baseUrl}/fixtures", $params);
                if ($response->successful()) {
                    return $response->json('content', []);
                }
            } catch (\Throwable $e) {
                Log::error('Lỗi khi lấy Lịch Thi Đấu từ PulseLive: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Lấy các trận đấu đang diễn ra trực tiếp (Live Matches)
     * Cache: 30 giây (30s) theo yêu cầu cốt lõi để giảm tải và cập nhật sát thực tế
     */
    public function getLiveMatches(?int $seasonId = null): array
    {
        $season = $seasonId ?: $this->getCurrentCompSeason()['id'];
        $cacheKey = "epl_live_matches_{$season}";

        return Cache::remember($cacheKey, 30, function () use ($season) {
            try {
                $params = [
                    'comps' => self::COMPETITION_ID,
                    'compSeasons' => $season,
                    'statuses' => 'L', // Chỉ lấy các trận đang Live
                    'page' => 0,
                    'pageSize' => 20,
                    'sort' => 'asc',
                ];

                $response = $this->client()->get("{$this->baseUrl}/fixtures", $params);
                if ($response->successful()) {
                    return $response->json('content', []);
                }
            } catch (\Throwable $e) {
                Log::error('Lỗi khi lấy Live Matches từ PulseLive: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Lấy Chi Tiết Một Trận Đấu (Match Detail: Đội hình, bàn thắng, thẻ phạt, sự kiện)
     * Cache linh hoạt:
     * - Nếu trận đang Live: Cache 30 giây
     * - Nếu trận đã Kết thúc (C): Cache 1 ngày (86400s)
     * - Nếu trận Chưa đá (U): Cache 1 giờ (3600s)
     */
    public function getMatchDetail(int $fixtureId): ?array
    {
        // Kiểm tra xem đã có cache sẵn chưa (kiểm tra status)
        $metaKey = "epl_fixture_meta_{$fixtureId}";
        $cachedMeta = Cache::get($metaKey);

        $ttl = 3600; // Mặc định 1h
        if ($cachedMeta === 'L') {
            $ttl = 30; // Đang live => 30s
        } elseif ($cachedMeta === 'C') {
            $ttl = 86400; // Đã xong => 1 ngày
        }

        $cacheKey = "epl_match_detail_{$fixtureId}";

        return Cache::remember($cacheKey, $ttl, function () use ($fixtureId, $metaKey) {
            try {
                $response = $this->client()->get("{$this->baseUrl}/fixtures/{$fixtureId}");
                if ($response->successful()) {
                    $data = $response->json();
                    $status = $data['status'] ?? 'U';

                    // Cập nhật trạng thái meta để quyết định TTL lần sau
                    Cache::put($metaKey, $status, 86400);

                    return $data;
                }
            } catch (\Throwable $e) {
                Log::error("Lỗi khi lấy chi tiết trận {$fixtureId}: " . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Helper lấy link logo CLB chuẩn từ CDN chính thức của Premier League
     */
    public static function getTeamBadgeUrl(int|float $teamId, int $size = 70): string
    {
        $id = (int) $teamId;
        return "https://resources.premierleague.com/premierleague/badges/{$size}/t{$id}.png";
    }

    /**
     * Helper chuyển đổi timestamp mili-giây sang giờ Việt Nam (GMT+7)
     */
    public static function formatKickoffVietnam(int|float $millis, string $format = 'H:i - d/m/Y'): string
    {
        $timestamp = (int) ($millis / 1000);
        return Carbon::createFromTimestamp($timestamp, 'UTC')
            ->setTimezone('Asia/Ho_Chi_Minh')
            ->format($format);
    }
}
