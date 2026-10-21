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
     * Lấy Bảng Xếp Hạng (Standings) gốc từ PulseLive
     * Cache trong 30 phút (1800 giây) theo yêu cầu Phase 3
     */
    public function getStandings(?int $seasonId = null): array
    {
        $season = $seasonId ?: $this->getCurrentCompSeason()['id'];
        $cacheKey = "epl_standings_{$season}";

        return Cache::remember($cacheKey, 1800, function () use ($season) {
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
     * Lấy Bảng Xếp Hạng Trực Tiếp (Live Table As-It-Stands)
     * Tự động kết hợp tỷ số tạm thời của các trận đang đá (status 'L')
     * Cộng tạm điểm (+3 thắng, +1 hòa) và tính lại hiệu số, sau đó sắp xếp lại thứ hạng thời gian thực
     */
    public function getLiveStandings(?int $seasonId = null): array
    {
        $season = $seasonId ?: $this->getCurrentCompSeason()['id'];
        $standingsData = $this->getStandings($season);
        $liveMatches = $this->getLiveMatches($season);

        $entries = $standingsData['entries'] ?? [];
        $hasLive = !empty($liveMatches);

        if (!$hasLive || empty($entries)) {
            // Không có trận live: giữ nguyên bảng xếp hạng gốc
            foreach ($entries as $index => &$entry) {
                $entry['originalPosition'] = (int) ($entry['position'] ?? ($index + 1));
                $entry['isLiveNow'] = false;
                $entry['movement'] = 0;
            }
            return [
                'isLive' => false,
                'season' => $standingsData['season'] ?? [],
                'entries' => $entries,
                'liveMatchesCount' => 0
            ];
        }

        // Tạo mảng đánh chỉ mục theo ID CLB để xử lý cộng điểm
        $indexed = [];
        foreach ($entries as $index => $entry) {
            $teamId = (int) ($entry['team']['club']['id'] ?? ($entry['team']['id'] ?? 0));
            $entry['originalPosition'] = (int) ($entry['position'] ?? ($index + 1));
            $entry['isLiveNow'] = false;
            $indexed[$teamId] = $entry;
        }

        // Tính điểm và hiệu số tạm thời từ các trận đang live
        foreach ($liveMatches as $match) {
            $homeId = (int) ($match['teams'][0]['team']['club']['id'] ?? ($match['teams'][0]['team']['id'] ?? 0));
            $awayId = (int) ($match['teams'][1]['team']['club']['id'] ?? ($match['teams'][1]['team']['id'] ?? 0));
            
            $homeScore = (int) ($match['teams'][0]['score'] ?? 0);
            $awayScore = (int) ($match['teams'][1]['score'] ?? 0);

            if (isset($indexed[$homeId])) {
                $indexed[$homeId]['isLiveNow'] = true;
                $indexed[$homeId]['overall']['played'] += 1;
                $indexed[$homeId]['overall']['goalsFor'] += $homeScore;
                $indexed[$homeId]['overall']['goalsAgainst'] += $awayScore;
                $indexed[$homeId]['overall']['goalsDifference'] = $indexed[$homeId]['overall']['goalsFor'] - $indexed[$homeId]['overall']['goalsAgainst'];

                if ($homeScore > $awayScore) {
                    $indexed[$homeId]['overall']['won'] += 1;
                    $indexed[$homeId]['overall']['points'] += 3;
                } elseif ($homeScore === $awayScore) {
                    $indexed[$homeId]['overall']['drawn'] += 1;
                    $indexed[$homeId]['overall']['points'] += 1;
                } else {
                    $indexed[$homeId]['overall']['lost'] += 1;
                }
            }

            if (isset($indexed[$awayId])) {
                $indexed[$awayId]['isLiveNow'] = true;
                $indexed[$awayId]['overall']['played'] += 1;
                $indexed[$awayId]['overall']['goalsFor'] += $awayScore;
                $indexed[$awayId]['overall']['goalsAgainst'] += $homeScore;
                $indexed[$awayId]['overall']['goalsDifference'] = $indexed[$awayId]['overall']['goalsFor'] - $indexed[$awayId]['overall']['goalsAgainst'];

                if ($awayScore > $homeScore) {
                    $indexed[$awayId]['overall']['won'] += 1;
                    $indexed[$awayId]['overall']['points'] += 3;
                } elseif ($homeScore === $awayScore) {
                    $indexed[$awayId]['overall']['drawn'] += 1;
                    $indexed[$awayId]['overall']['points'] += 1;
                } else {
                    $indexed[$awayId]['overall']['lost'] += 1;
                }
            }
        }

        // Sắp xếp lại thứ hạng theo luật Premier League:
        // 1. Điểm số (points) giảm dần
        // 2. Hiệu số bàn thắng bại (goalsDifference) giảm dần
        // 3. Số bàn thắng ghi được (goalsFor) giảm dần
        // 4. Tên đội bóng (name) tăng dần
        $sorted = array_values($indexed);
        usort($sorted, function ($a, $b) {
            $ptsA = $a['overall']['points'] ?? 0;
            $ptsB = $b['overall']['points'] ?? 0;
            if ($ptsA !== $ptsB) {
                return $ptsB <=> $ptsA;
            }

            $gdA = $a['overall']['goalsDifference'] ?? 0;
            $gdB = $b['overall']['goalsDifference'] ?? 0;
            if ($gdA !== $gdB) {
                return $gdB <=> $gdA;
            }

            $gfA = $a['overall']['goalsFor'] ?? 0;
            $gfB = $b['overall']['goalsFor'] ?? 0;
            if ($gfA !== $gfB) {
                return $gfB <=> $gfA;
            }

            return strcmp($a['team']['name'] ?? '', $b['team']['name'] ?? '');
        });

        // Cập nhật lại vị trí hiển thị và mức độ di chuyển hạng
        foreach ($sorted as $idx => &$item) {
            $newPos = $idx + 1;
            $item['position'] = $newPos;
            $orig = $item['originalPosition'] ?? $newPos;
            $item['movement'] = $orig - $newPos; // >0: tăng hạng, <0: tụt hạng, 0: giữ nguyên
        }

        return [
            'isLive' => true,
            'season' => $standingsData['season'] ?? [],
            'entries' => $sorted,
            'liveMatchesCount' => count($liveMatches)
        ];
    }

    /**
     * Lấy danh sách Top 10 cầu thủ ghi bàn nhiều nhất mùa giải (Top Scorers) kèm số kiến tạo
     * Cache trong 1 giờ (3600s)
     */
    public function getTopScorers(?int $seasonId = null): array
    {
        $season = $seasonId ?: $this->getCurrentCompSeason()['id'];
        $cacheKey = "epl_top_scorers_{$season}";

        return Cache::remember($cacheKey, 3600, function () use ($season) {
            try {
                // 1. Lấy danh sách ghi bàn (Goals)
                $goalsRes = $this->client()->get("{$this->baseUrl}/stats/ranked/players/goals", [
                    'comps' => self::COMPETITION_ID,
                    'compSeasons' => $season,
                    'page' => 0,
                    'pageSize' => 10,
                ]);

                // 2. Lấy danh sách kiến tạo (Assists)
                $assistsRes = $this->client()->get("{$this->baseUrl}/stats/ranked/players/goal_assist", [
                    'comps' => self::COMPETITION_ID,
                    'compSeasons' => $season,
                    'page' => 0,
                    'pageSize' => 30,
                ]);

                $assistsMap = [];
                if ($assistsRes->successful()) {
                    $assistContent = $assistsRes->json('stats.content', []);
                    foreach ($assistContent as $ast) {
                        $pId = (int) ($ast['owner']['id'] ?? ($ast['owner']['playerId'] ?? 0));
                        $assistsMap[$pId] = (int) ($ast['value'] ?? 0);
                    }
                }

                if ($goalsRes->successful()) {
                    $content = $goalsRes->json('stats.content', []);
                    $topScorers = [];

                    foreach ($content as $item) {
                        $owner = $item['owner'] ?? [];
                        $pId = (int) ($owner['id'] ?? ($owner['playerId'] ?? 0));
                        $club = $owner['currentTeam']['club'] ?? ($owner['currentTeam'] ?? []);
                        $clubId = (int) ($club['id'] ?? 0);

                        $topScorers[] = [
                            'rank' => (int) ($item['rank'] ?? 1),
                            'playerId' => $pId,
                            'name' => $owner['name']['display'] ?? 'Cầu thủ',
                            'position' => $owner['info']['positionInfo'] ?? ($owner['info']['position'] ?? 'Tiền đạo'),
                            'nationality' => $owner['nationalTeam']['country'] ?? ($owner['nationalTeam']['demonym'] ?? ''),
                            'teamName' => $owner['currentTeam']['name'] ?? 'CLB',
                            'teamShort' => $owner['currentTeam']['shortName'] ?? '',
                            'teamBadge' => self::getTeamBadgeUrl($clubId, 50),
                            'goals' => (int) ($item['value'] ?? 0),
                            'assists' => $assistsMap[$pId] ?? 0,
                            'photo' => "https://resources.premierleague.com/premierleague/photos/players/250x250/p{$pId}.png",
                        ];
                    }

                    return $topScorers;
                }
            } catch (\Throwable $e) {
                Log::error('Lỗi khi lấy danh sách Top Scorers: ' . $e->getMessage());
            }

            return [];
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
