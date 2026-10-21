<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Service chính thức kết nối và xử lý dữ liệu bóng đá Ngoại Hạng Anh từ PulseLive API
 */
class PremierLeagueService
{
    /**
     * Base URL của Football API PulseLive
     */
    protected string $baseUrl = 'https://footballapi.pulselive.com/football';

    /**
     * Headers server-side giả lập trình duyệt chính thức để tránh chặn CORS
     */
    protected array $headers = [
        'Origin' => 'https://www.premierleague.com',
        'Referer' => 'https://www.premierleague.com/',
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
        'Accept' => 'application/json, text/plain, */*',
        'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
    ];

    /**
     * ID giải đấu Ngoại Hạng Anh (EN_PR) trên PulseLive luôn là 1
     */
    public const COMPETITION_ID = 1;

    /**
     * Khởi tạo HTTP Client kèm headers xác thực
     */
    protected function client()
    {
        return Http::withHeaders($this->headers)
            ->timeout(10)
            ->retry(2, 200);
    }

    /**
     * Lấy Mùa giải hiện tại của Premier League
     */
    public function getCurrentCompSeason(): array
    {
        return Cache::remember('epl_current_comp_season', 86400, function () {
            try {
                $response = $this->client()->get("{$this->baseUrl}/competitions/" . self::COMPETITION_ID . "/compseasons");
                if ($response->successful()) {
                    $seasons = $response->json('content', []);
                    if (!empty($seasons)) {
                        return [
                            'id' => (int) $seasons[0]['id'],
                            'label' => $seasons[0]['label'] ?? '2026/27'
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Lỗi lấy CompSeason Premier League: ' . $e->getMessage());
            }

            return ['id' => 841, 'label' => 'English Premier League Season 2026/2027'];
        });
    }

    /**
     * Lấy danh sách vòng đấu (Gameweeks) của mùa giải
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
     * Lấy danh sách trận đấu và kết quả theo ngày (Fixtures & Results by Date)
     * Yêu cầu Phase 2:
     * - Tham số: $dateString định dạng YYYY-MM-DD
     * - API: https://footballapi.pulselive.com/football/fixtures với comps=1, pageSize=40, sort=asc
     * - Cache::remember(): 60s (nếu là hôm nay) hoặc 1 ngày (nếu là quá khứ/tương lai)
     *
     * @param string $dateString Ngày định dạng YYYY-MM-DD
     * @return array Danh sách trận đấu diễn ra trong ngày
     */
    public function getFixturesByDate(string $dateString): array
    {
        $today = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
        $isToday = ($dateString === $today);
        
        // TTL: 60s nếu là ngày hôm nay (để cập nhật tỉ số live), 1 ngày (86400s) nếu là ngày khác
        $ttl = $isToday ? 60 : 86400;
        $cacheKey = "epl_fixtures_by_date_{$dateString}";

        return Cache::remember($cacheKey, $ttl, function () use ($dateString) {
            try {
                $season = $this->getCurrentCompSeason()['id'];
                $gameweeks = $this->getGameweeks($season);

                // Xác định gameweek tương ứng với ngày được chọn để lọc chính xác từ PulseLive
                $targetCarbon = Carbon::parse($dateString, 'Asia/Ho_Chi_Minh');
                $matchedGwId = null;

                foreach ($gameweeks as $gw) {
                    if (isset($gw['from']['millis']) && isset($gw['until']['millis'])) {
                        $from = Carbon::createFromTimestampMs($gw['from']['millis'], 'Asia/Ho_Chi_Minh')->startOfDay()->subDay();
                        $until = Carbon::createFromTimestampMs($gw['until']['millis'], 'Asia/Ho_Chi_Minh')->endOfDay()->addDay();
                        if ($targetCarbon->between($from, $until)) {
                            $matchedGwId = (int) $gw['id'];
                            break;
                        }
                    }
                }

                // Thiết lập params gọi API PulseLive theo đúng yêu cầu: comps=1, pageSize=40, sort=asc
                $params = [
                    'comps' => self::COMPETITION_ID,
                    'compSeasons' => $season,
                    'pageSize' => 40,
                    'sort' => 'asc',
                ];

                if ($matchedGwId) {
                    $params['gameweeks'] = $matchedGwId;
                }

                $response = $this->client()->get("{$this->baseUrl}/fixtures", $params);
                if ($response->successful()) {
                    $content = $response->json('content', []);

                    // Lọc chính xác các trận đấu diễn ra vào ngày $dateString theo giờ Việt Nam (GMT+7)
                    $filtered = array_values(array_filter($content, function ($fixture) use ($dateString) {
                        if (!isset($fixture['kickoff']['millis'])) {
                            return false;
                        }
                        $matchDate = Carbon::createFromTimestampMs($fixture['kickoff']['millis'], 'Asia/Ho_Chi_Minh')->toDateString();
                        return $matchDate === $dateString;
                    }));

                    return $filtered;
                }
            } catch (\Throwable $e) {
                Log::error("Lỗi khi lấy trận đấu ngày {$dateString} từ PulseLive: " . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Lấy Bảng Xếp Hạng
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
                Log::error('Lỗi khi lấy Bảng Xếp Hạng: ' . $e->getMessage());
            }

            return ['season' => [], 'entries' => []];
        });
    }

    /**
     * Lấy các trận đấu đang diễn ra trực tiếp (Live Matches) - Cache 30s
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
                    'statuses' => 'L',
                    'page' => 0,
                    'pageSize' => 20,
                    'sort' => 'asc',
                ];

                $response = $this->client()->get("{$this->baseUrl}/fixtures", $params);
                if ($response->successful()) {
                    return $response->json('content', []);
                }
            } catch (\Throwable $e) {
                Log::error('Lỗi khi lấy Live Matches: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Lấy Chi Tiết Một Trận Đấu
     */
    public function getMatchDetail(int $fixtureId): ?array
    {
        $metaKey = "epl_fixture_meta_{$fixtureId}";
        $cachedMeta = Cache::get($metaKey);

        $ttl = 3600;
        if ($cachedMeta === 'L') {
            $ttl = 30;
        } elseif ($cachedMeta === 'C') {
            $ttl = 86400;
        }

        $cacheKey = "epl_match_detail_{$fixtureId}";

        return Cache::remember($cacheKey, $ttl, function () use ($fixtureId, $metaKey) {
            try {
                $response = $this->client()->get("{$this->baseUrl}/fixtures/{$fixtureId}");
                if ($response->successful()) {
                    $data = $response->json();
                    $status = $data['status'] ?? 'U';
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
     * Lấy vòng đấu hiện tại
     */
    public function getCurrentGameweek(?int $seasonId = null): ?array
    {
        $gameweeks = $this->getGameweeks($seasonId);
        if (empty($gameweeks)) {
            return null;
        }

        foreach ($gameweeks as $gw) {
            if (in_array($gw['status'] ?? '', ['L', 'U'])) {
                return $gw;
            }
        }

        return end($gameweeks) ?: null;
    }

    /**
     * Lấy Lịch Thi Đấu & Kết Quả
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
                Log::error('Lỗi khi lấy Lịch Thi Đấu: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Helper lấy link logo CLB từ CDN Premier League
     */
    public static function getTeamBadgeUrl(int|float $teamId, int $size = 70): string
    {
        $id = (int) $teamId;
        return "https://resources.premierleague.com/premierleague/badges/{$size}/t{$id}.png";
    }

    /**
     * Helper chuyển đổi timestamp sang giờ Việt Nam (GMT+7)
     */
    public static function formatKickoffVietnam(int|float $millis, string $format = 'H:i - d/m/Y'): string
    {
        $timestamp = (int) ($millis / 1000);
        return Carbon::createFromTimestamp($timestamp, 'UTC')
            ->setTimezone('Asia/Ho_Chi_Minh')
            ->format($format);
    }
}
