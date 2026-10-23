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
                        $rawLabel = $seasons[0]['label'] ?? '2026/27';
                        $cleanLabel = preg_match('/\d{4}\/\d{2,4}/', $rawLabel, $m) ? $m[0] : $rawLabel;
                        return [
                            'id' => (int) $seasons[0]['id'],
                            'label' => $cleanLabel
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Lỗi lấy CompSeason Premier League: ' . $e->getMessage());
            }

            return ['id' => 841, 'label' => '2026/27'];
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
                            'photo' => self::getPlayerPhotoUrl($owner['altIds']['opta'] ?? null, $owner['name']['display'] ?? null, 110, 140),
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
     * Phase 5: Lấy Feed dữ liệu các trận đấu đang live cho Realtime Engine
     * Trả về danh sách gồm: id, home_score, away_score, minute, status, home_team, away_team, latest_events
     * Cache 15-20 giây theo yêu cầu kỹ thuật
     */
    public function getLiveFeed(?int $fixtureId = null): array
    {
        $cacheKey = 'epl_api_live_feed_' . ($fixtureId ?: 'all');

        return Cache::remember($cacheKey, 15, function () use ($fixtureId) {
            $feed = [];

            if ($fixtureId) {
                $detail = $this->getMatchDetail($fixtureId);
                if ($detail) {
                    $feed[] = $this->formatLiveFixtureData($detail);
                }
                return $feed;
            }

            $liveMatches = $this->getLiveMatches();
            foreach ($liveMatches as $match) {
                $feed[] = $this->formatLiveFixtureData($match);
            }

            return $feed;
        });
    }

    /**
     * Định dạng dữ liệu một trận đấu chuẩn hóa cho API realtime
     */
    public function formatLiveFixtureData(array $match): array
    {
        $fixtureId = (int) ($match['id'] ?? 0);
        $home = $match['teams'][0] ?? [];
        $away = $match['teams'][1] ?? [];

        $homeTeam = $home['team'] ?? [];
        $awayTeam = $away['team'] ?? [];

        $homeId = (int) ($homeTeam['club']['id'] ?? ($homeTeam['id'] ?? 0));
        $awayId = (int) ($awayTeam['club']['id'] ?? ($awayTeam['id'] ?? 0));

        $homeScore = (int) ($home['score'] ?? 0);
        $awayScore = (int) ($away['score'] ?? 0);

        $minuteLabel = $match['clock']['label'] ?? null;
        if (!$minuteLabel) {
            $secs = (int) ($match['clock']['secs'] ?? 0);
            $minuteLabel = $secs > 0 ? (ceil($secs / 60) . "'") : "1'";
        }

        // Lấy danh sách sự kiện mới nhất (tối đa 5 sự kiện)
        $allEvents = $this->getMatchEvents($fixtureId);
        $latestEvents = array_slice(array_reverse($allEvents), 0, 5);

        return [
            'id' => $fixtureId,
            'home_team' => [
                'id' => $homeId,
                'name' => $homeTeam['name'] ?? 'Đội nhà',
                'shortName' => $homeTeam['shortName'] ?? ($homeTeam['club']['abbr'] ?? 'HOME'),
                'badge' => $this->getTeamBadgeUrl($homeId),
            ],
            'away_team' => [
                'id' => $awayId,
                'name' => $awayTeam['name'] ?? 'Đội khách',
                'shortName' => $awayTeam['shortName'] ?? ($awayTeam['club']['abbr'] ?? 'AWAY'),
                'badge' => $this->getTeamBadgeUrl($awayId),
            ],
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'minute' => $minuteLabel,
            'status' => $match['status'] ?? 'L',
            'latest_events' => $latestEvents,
            'updated_at' => Carbon::now('Asia/Ho_Chi_Minh')->toIso8601String(),
        ];
    }

    /**
     * Lấy Chi Tiết Một Trận Đấu
     */
    public function getMatchDetail(int $fixtureId): ?array
    {
        $metaKey = "epl_fixture_meta_{$fixtureId}";
        $cachedMeta = Cache::get($metaKey);

        // Mặc định:
        // - Trận Live ('L'): 20 giây
        // - Trận Đã xong ('C'): 7 ngày (604800s) - kết quả vĩnh viễn không thay đổi
        // - Trận Chưa đá ('U'): 3 ngày (259200s) - lịch thi đấu cố định
        $ttl = 259200;
        if ($cachedMeta === 'L') {
            $ttl = 20;
        } elseif ($cachedMeta === 'C') {
            $ttl = 604800;
        }

        $cacheKey = "epl_match_detail_{$fixtureId}";

        return Cache::remember($cacheKey, $ttl, function () use ($fixtureId, $metaKey) {
            try {
                $response = $this->client()->get("{$this->baseUrl}/fixtures/{$fixtureId}");
                if ($response->successful()) {
                    $data = $response->json();
                    $status = $data['status'] ?? 'U';
                    $storeTtl = ($status === 'L') ? 20 : (($status === 'C') ? 604800 : 259200);
                    Cache::put($metaKey, $status, $storeTtl);
                    return $data;
                }
            } catch (\Throwable $e) {
                Log::error("Lỗi khi lấy chi tiết trận {$fixtureId}: " . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Phase 4: Lấy thông tin chung trận đấu, sân vận động, trọng tài
     */
    public function getMatchDetails(int $matchId): ?array
    {
        return $this->getMatchDetail($matchId);
    }

    /**
     * Phase 4: Lấy danh sách sự kiện bàn thắng, thẻ phạt, thay người theo phút
     */
    public function getMatchEvents(int $matchId): array
    {
        $match = $this->getMatchDetail($matchId);
        if (!$match || empty($match['events'])) {
            return [];
        }

        // Tạo map ID cầu thủ sang thông tin chi tiết (tên, số áo, ảnh chân dung Opta)
        $playersMap = [];
        foreach ($match['teamLists'] ?? [] as $tList) {
            foreach (array_merge($tList['lineup'] ?? [], $tList['substitutes'] ?? []) as $p) {
                $pId = (int) ($p['id'] ?? 0);
                $optaId = $p['altIds']['opta'] ?? null;
                $playersMap[$pId] = [
                    'name' => $p['name']['display'] ?? 'Cầu thủ',
                    'shortName' => $p['name']['last'] ?? ($p['name']['display'] ?? 'Cầu thủ'),
                    'shirtNum' => $p['shirtNum'] ?? '',
                    'photo' => self::getPlayerPhotoUrl($optaId, $p['name']['display'] ?? null, 110, 140)
                ];
            }
        }

        $homeId = (int) ($match['teams'][0]['team']['id'] ?? ($match['teams'][0]['team']['club']['id'] ?? 0));
        $awayId = (int) ($match['teams'][1]['team']['id'] ?? ($match['teams'][1]['team']['club']['id'] ?? 0));
        $homeName = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $awayName = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $homeBadge = $this->getTeamBadgeUrl($homeId);
        $awayBadge = $this->getTeamBadgeUrl($awayId);

        $events = [];
        $subGroups = [];

        foreach ($match['events'] as $e) {
            $type = $e['type'] ?? '';
            $clock = preg_replace("/'00$/", "'", $e['clock']['label'] ?? '00\'');
            $secs = (int) ($e['clock']['secs'] ?? 0);
            $phase = (string) ($e['phase'] ?? '1');
            $score = isset($e['score']) ? ($e['score']['homeScore'] . ' - ' . $e['score']['awayScore']) : null;

            // Bắt đầu hiệp đấu
            if ($type === 'PS') {
                $events[] = [
                    'type' => 'PERIOD',
                    'category' => 'period',
                    'clock' => ($phase === '1') ? "1'" : "46'",
                    'secs' => $secs,
                    'phase' => (int) $phase,
                    'pOrder' => 0,
                    'icon' => 'whistle',
                    'desc' => ($phase === '1') ? 'Bắt đầu Hiệp 1' : 'Bắt đầu Hiệp 2',
                    'isHome' => null,
                    'playerName' => ($phase === '1') ? 'Trọng tài nổi còi bắt đầu trận đấu' : 'Bắt đầu hiệp thi đấu thứ hai',
                    'score' => $score,
                ];
                continue;
            }

            // Kết thúc hiệp đấu
            if ($type === 'PE') {
                $events[] = [
                    'type' => 'PERIOD',
                    'category' => 'period',
                    'clock' => ($phase === '1') ? 'HT' : 'FT',
                    'secs' => $secs,
                    'phase' => (int) $phase,
                    'pOrder' => 2,
                    'icon' => ($phase === '1') ? 'half-time' : 'full-time',
                    'desc' => ($phase === '1') ? 'Hết Hiệp 1 (Nghỉ giữa hiệp)' : 'Hết Giờ (Kết thúc trận đấu)',
                    'isHome' => null,
                    'playerName' => ($phase === '1') ? 'Tạm dừng hiệp 1' : 'Trọng tài nổi còi mãn cuộc',
                    'score' => $score,
                ];
                continue;
            }

            // Gom nhóm các sự kiện thay người theo mã id
            if ($type === 'S') {
                $subKey = $e['id'] ?? ($secs . '_' . ($e['teamId'] ?? ''));
                $subGroups[$subKey][] = $e;
                continue;
            }

            if (!in_array($type, ['G', 'OG', 'P', 'B'])) {
                continue;
            }

            $personId = (int) ($e['personId'] ?? 0);
            $assistId = (int) ($e['assistId'] ?? 0);
            $teamId = (int) ($e['teamId'] ?? 0);
            $isHome = ($teamId === $homeId);

            $pInfo = $playersMap[$personId] ?? [
                'name' => "Cầu thủ #{$personId}",
                'shortName' => 'Cầu thủ',
                'shirtNum' => '',
                'photo' => 'https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png'
            ];

            $icon = 'goal';
            $desc = 'Bàn thắng';
            $category = 'goal';

            if ($type === 'G') {
                $icon = 'goal';
                $desc = 'Bàn thắng';
                $category = 'goal';
            } elseif ($type === 'OG') {
                $icon = 'own-goal';
                $desc = 'Phản lưới nhà (OG)';
                $category = 'goal';
            } elseif ($type === 'P') {
                $icon = 'penalty';
                $desc = 'Phạt đền (Penalty)';
                $category = 'goal';
            } elseif ($type === 'B') {
                $isRed = (($e['cardType'] ?? '') === 'RED' || ($e['description'] ?? '') === 'R');
                $icon = $isRed ? 'red-card' : 'yellow-card';
                $desc = $isRed ? 'Thẻ đỏ' : 'Thẻ vàng';
                $category = 'card';
            }

            $events[] = [
                'type' => $type,
                'category' => $category,
                'clock' => $clock,
                'secs' => $secs,
                'phase' => (int) $phase,
                'pOrder' => 1,
                'minute' => (int) ceil($secs / 60),
                'icon' => $icon,
                'desc' => $desc,
                'isHome' => $isHome,
                'teamId' => $teamId,
                'teamName' => $isHome ? $homeName : $awayName,
                'teamBadge' => $isHome ? $homeBadge : $awayBadge,
                'playerName' => $pInfo['name'],
                'playerPhoto' => $pInfo['photo'],
                'playerNum' => $pInfo['shirtNum'],
                'assistName' => $assistId ? ($playersMap[$assistId]['name'] ?? null) : null,
                'score' => $score,
            ];
        }

        // Xử lý các cặp thay người
        foreach ($subGroups as $subKey => $group) {
            $in = null;
            $out = null;
            $teamId = 0;
            $secs = 0;
            $clock = "00'";
            $phase = 1;
            $score = null;

            foreach ($group as $item) {
                $teamId = (int) ($item['teamId'] ?? 0);
                $secs = (int) ($item['clock']['secs'] ?? 0);
                $clock = preg_replace("/'00$/", "'", $item['clock']['label'] ?? "00'");
                $phase = (int) ($item['phase'] ?? 1);
                if (isset($item['score'])) {
                    $score = $item['score']['homeScore'] . ' - ' . $item['score']['awayScore'];
                }
                $desc = strtoupper($item['description'] ?? '');
                $pId = (int) ($item['personId'] ?? 0);
                $pData = $playersMap[$pId] ?? [
                    'name' => "Cầu thủ #{$pId}",
                    'shortName' => 'Cầu thủ',
                    'shirtNum' => '',
                    'photo' => 'https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png'
                ];
                if ($desc === 'ON') {
                    $in = $pData;
                } else {
                    $out = $pData;
                }
            }

            $isHome = ($teamId === $homeId);
            $inName = $in['name'] ?? 'Cầu thủ vào';
            $outName = $out['name'] ?? 'Cầu thủ ra';

            $events[] = [
                'type' => 'S',
                'category' => 'sub',
                'clock' => $clock,
                'secs' => $secs,
                'phase' => $phase,
                'pOrder' => 1,
                'minute' => (int) ceil($secs / 60),
                'icon' => 'sub',
                'desc' => 'Thay người',
                'isHome' => $isHome,
                'teamId' => $teamId,
                'teamName' => $isHome ? $homeName : $awayName,
                'teamBadge' => $isHome ? $homeBadge : $awayBadge,
                'playerName' => "{$inName} vào sân (thay {$outName})",
                'subIn' => $in,
                'subOut' => $out,
                'score' => $score,
            ];
        }

        // Sắp xếp các sự kiện theo đúng thứ tự thời gian trận đấu
        usort($events, function ($a, $b) {
            if ($a['phase'] !== $b['phase']) {
                return $a['phase'] <=> $b['phase'];
            }
            if ($a['pOrder'] !== $b['pOrder']) {
                return $a['pOrder'] <=> $b['pOrder'];
            }
            return $a['secs'] <=> $b['secs'];
        });

        return $events;
    }

    /**
     * Phase 4: Lấy danh sách 11 cầu thủ đá chính, dự bị và sơ đồ chiến thuật 2D cho sân bóng
     */
    public function getMatchLineups(int $matchId): array
    {
        $match = $this->getMatchDetail($matchId);
        if (!$match || empty($match['teamLists'])) {
            return [
                'hasLineups' => false,
                'home' => null,
                'away' => null
            ];
        }

        $teamLists = $match['teamLists'];
        $formatTeam = function ($tData, $teamMeta) {
            $formationLabel = $tData['formation']['label'] ?? '4-4-2';
            $playerMatrix = $tData['formation']['players'] ?? [];

            // Map tất cả starters theo ID
            $startersMap = [];
            foreach ($tData['lineup'] ?? [] as $st) {
                $optaId = $st['altIds']['opta'] ?? null;
                $photo = self::getPlayerPhotoUrl($optaId, $st['name']['display'] ?? null, 110, 140);

                $startersMap[$st['id']] = [
                    'id' => $st['id'],
                    'optaId' => $optaId,
                    'photo' => $photo,
                    'name' => $st['name']['display'] ?? '',
                    'lastName' => $st['name']['last'] ?? ($st['name']['display'] ?? ''),
                    'shirtNum' => $st['matchShirtNumber'] ?? ($st['info']['shirtNum'] ?? '-'),
                    'position' => $st['matchPosition'] ?? ($st['info']['position'] ?? ''),
                    'isCaptain' => !empty($st['captain']),
                ];
            }

            // Xây dựng danh sách hàng cầu thủ (formation lines) cho sân 2D
            $pitchRows = [];
            if (!empty($playerMatrix)) {
                foreach ($playerMatrix as $rowIdx => $pIds) {
                    $rowPlayers = [];
                    foreach ($pIds as $pId) {
                        if (isset($startersMap[$pId])) {
                            $rowPlayers[] = $startersMap[$pId];
                        }
                    }
                    if (!empty($rowPlayers)) {
                        $pitchRows[] = $rowPlayers;
                    }
                }
            }

            // Danh sách dự bị
            $subs = [];
            foreach ($tData['substitutes'] ?? [] as $sub) {
                $optaId = $sub['altIds']['opta'] ?? null;
                $photo = self::getPlayerPhotoUrl($optaId, $sub['name']['display'] ?? null, 110, 140);

                $subs[] = [
                    'id' => $sub['id'],
                    'optaId' => $optaId,
                    'photo' => $photo,
                    'name' => $sub['name']['display'] ?? '',
                    'lastName' => $sub['name']['last'] ?? ($sub['name']['display'] ?? ''),
                    'shirtNum' => $sub['matchShirtNumber'] ?? ($sub['info']['shirtNum'] ?? '-'),
                    'position' => $sub['matchPosition'] ?? ($sub['info']['position'] ?? ''),
                    'isCaptain' => !empty($sub['captain']),
                ];
            }

            return [
                'name' => $teamMeta['name'] ?? '',
                'shortName' => $teamMeta['shortName'] ?? '',
                'id' => $teamMeta['id'] ?? 0,
                'badge' => self::getTeamBadgeUrl($teamMeta['club']['id'] ?? ($teamMeta['id'] ?? 0), 70),
                'formation' => $formationLabel,
                'pitchRows' => $pitchRows,
                'starters' => array_values($startersMap),
                'substitutes' => $subs,
            ];
        };

        $homeMeta = $match['teams'][0]['team'] ?? [];
        $awayMeta = $match['teams'][1]['team'] ?? [];

        return [
            'hasLineups' => true,
            'home' => isset($teamLists[0]) ? $formatTeam($teamLists[0], $homeMeta) : null,
            'away' => isset($teamLists[1]) ? $formatTeam($teamLists[1], $awayMeta) : null,
        ];
    }

    /**
     * Phase 4: Lấy thống kê kiểm soát bóng, sút, sút trúng đích, phạt góc, phạm lỗi
     */
    public function getMatchStats(int $matchId): array
    {
        $match = $this->getMatchDetail($matchId);
        $defaultStats = [
            'possession' => ['home' => 50, 'away' => 50],
            'shots' => ['home' => 0, 'away' => 0],
            'shotsOnTarget' => ['home' => 0, 'away' => 0],
            'corners' => ['home' => 0, 'away' => 0],
            'fouls' => ['home' => 0, 'away' => 0],
        ];

        if (!$match) {
            return $defaultStats;
        }

        $status = $match['status'] ?? 'U';

        // Trận chưa diễn ra: chưa có dữ liệu thống kê, trả về mặc định ngay lập tức 0ms
        if ($status === 'U') {
            return $defaultStats;
        }

        // Trận kết thúc ('C'): Cache 24 giờ (86400s). Trận đang Live: Cache 30 giây.
        $ttl = ($status === 'C') ? 86400 : 30;
        $cacheKey = "epl_match_stats_{$matchId}";

        return Cache::remember($cacheKey, $ttl, function () use ($matchId, $match, $defaultStats) {
            try {
                $homeId = (int) ($match['teams'][0]['team']['club']['id'] ?? ($match['teams'][0]['team']['id'] ?? 0));
                $awayId = (int) ($match['teams'][1]['team']['club']['id'] ?? ($match['teams'][1]['team']['id'] ?? 0));

                $res = $this->client()->get("{$this->baseUrl}/stats/match/{$matchId}");
                if ($res->successful()) {
                    $data = $res->json('data', []);
                    $extractTeamStats = function ($tId) use ($data) {
                        $dict = [];
                        foreach ($data[(string)$tId]['M'] ?? [] as $st) {
                            $dict[$st['name']] = $st['value'];
                        }
                        return $dict;
                    };

                    $homeMap = $extractTeamStats($homeId);
                    $awayMap = $extractTeamStats($awayId);

                    $posHome = (float) ($homeMap['possession_percentage'] ?? 50.0);
                    $posAway = (float) ($awayMap['possession_percentage'] ?? (100.0 - $posHome));

                    $onTargetH = (int) ($homeMap['ontarget_scoring_att'] ?? 0);
                    $onTargetA = (int) ($awayMap['ontarget_scoring_att'] ?? 0);
                    $offTargetH = (int) ($homeMap['shot_off_target'] ?? 0);
                    $offTargetA = (int) ($awayMap['shot_off_target'] ?? 0);

                    $shotsH = (int) ($homeMap['total_scoring_att'] ?? ($onTargetH + $offTargetH));
                    $shotsA = (int) ($awayMap['total_scoring_att'] ?? ($onTargetA + $offTargetA));

                    $cornersH = (int) ($homeMap['won_corners'] ?? ($homeMap['corner_taken'] ?? 0));
                    $cornersA = (int) ($awayMap['won_corners'] ?? ($awayMap['corner_taken'] ?? 0));

                    $foulsH = (int) ($homeMap['fk_foul_lost'] ?? 0);
                    $foulsA = (int) ($awayMap['fk_foul_lost'] ?? 0);

                    return [
                        'possession' => ['home' => $posHome, 'away' => $posAway],
                        'shots' => ['home' => $shotsH, 'away' => $shotsA],
                        'shotsOnTarget' => ['home' => $onTargetH, 'away' => $onTargetA],
                        'corners' => ['home' => $cornersH, 'away' => $cornersA],
                        'fouls' => ['home' => $foulsH, 'away' => $foulsA],
                    ];
                }
            } catch (\Throwable $e) {
                Log::error("Lỗi khi lấy match stats {$matchId}: " . $e->getMessage());
            }

            return $defaultStats;
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
                    // 1 mùa = 380 trận, lấy trọn trong 1 trang để sitemap không sót trận
                    'pageSize' => 400,
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
     * Bảng ánh xạ Pulse Team ID sang Mã Logo Chính thức trên CDN Premier League
     */
    protected static array $pulseToBadgeCodeMap = [
        1   => 3,   // Arsenal
        2   => 7,   // Aston Villa
        3   => 91,  // Bournemouth
        127 => 91,  // Bournemouth (Pulse)
        4   => 8,   // Chelsea
        5   => 9,   // Coventry City
        6   => 31,  // Crystal Palace
        7   => 11,  // Everton
        8   => 40,  // Ipswich Town
        9   => 2,   // Leeds United
        10  => 14,  // Liverpool
        11  => 43,  // Manchester City
        12  => 1,   // Manchester United
        13  => 26,  // Leicester City (pulse 26)
        14  => 45,  // Norwich City
        15  => 17,  // Nottingham Forest
        18  => 49,  // Sheffield United
        20  => 20,  // Southampton
        21  => 6,   // Tottenham Hotspur
        23  => 4,   // Newcastle United
        25  => 21,  // West Ham United
        26  => 13,  // Leicester City
        29  => 56,  // Sunderland
        34  => 54,  // Fulham
        38  => 39,  // Wolverhampton Wanderers
        41  => 88,  // Hull City
        43  => 90,  // Burnley
        130 => 94,  // Brentford
        131 => 36,  // Brighton & Hove Albion
        163 => 102, // Luton Town
    ];

    /**
     * Bảng ánh xạ Tên cầu thủ nổi bật sang Photo Code chuẩn trên CDN Premier League
     */
    protected static array $playerNamePhotoCodeMap = [
        'haaland' => '223094',
        'erling haaland' => '223094',
        'salah' => '118748',
        'mohamed salah' => '118748',
        'isak' => '219168',
        'alexander isak' => '219168',
        'palmer' => '244851',
        'cole palmer' => '244851',
        'saka' => '223340',
        'bukayo saka' => '223340',
        'calvert-lewin' => '177815',
        'dominic calvert-lewin' => '177815',
        'watkins' => '178301',
        'ollie watkins' => '178301',
        'son' => '85971',
        'heung-min son' => '85971',
        'joao pedro' => '475168',
        'joão pedro' => '475168',
        'foden' => '209244',
        'phil foden' => '209244',
        'maddison' => '172780',
        'james maddison' => '172780',
        'bowen' => '178186',
        'jarrod bowen' => '178186',
        'wood' => '60689',
        'chris wood' => '60689',
        'jackson' => '517052',
        'nicolas jackson' => '517052',
        'diaz' => '244731',
        'luis diaz' => '244731',
        'bellingham' => '444145',
        'rice' => '204480',
        'declan rice' => '204480',
        'odegaard' => '184029',
        'martin odegaard' => '184029',
        'fernandes' => '141746',
        'bruno fernandes' => '141746',
        'rashford' => '176297',
        'marcus rashford' => '176297',
    ];

    /**
     * Helper lấy link logo CLB từ CDN Premier League (đã chuẩn hóa mã logo tránh 403)
     */
    public static function getTeamBadgeUrl(int|float $teamId, int $size = 70): string
    {
        $id = (int) $teamId;
        if ($id <= 0) {
            return "https://resources.premierleague.com/premierleague/badges/{$size}/t1.png";
        }

        // Lấy mã logo chuẩn xác từ bảng mapping
        $badgeCode = self::$pulseToBadgeCodeMap[$id] ?? $id;

        return "https://resources.premierleague.com/premierleague/badges/{$size}/t{$badgeCode}.png";
    }

    /**
     * Helper lấy link ảnh chân dung cầu thủ từ CDN Premier League
     */
    public static function getPlayerPhotoUrl(?string $optaId = null, ?string $playerName = null, int $width = 110, int $height = 140): string
    {
        $missingPhoto = "https://resources.premierleague.com/premierleague/photos/players/{$width}x{$height}/Photo-Missing.png";

        // 1. Nếu có Opta ID dạng "p12345" hoặc "12345"
        if (!empty($optaId)) {
            $code = str_starts_with($optaId, 'p') ? $optaId : "p{$optaId}";
            return "https://resources.premierleague.com/premierleague/photos/players/{$width}x{$height}/{$code}.png";
        }

        // 2. Tra cứu theo tên cầu thủ nếu có
        if (!empty($playerName)) {
            $norm = mb_strtolower(trim($playerName));
            if (isset(self::$playerNamePhotoCodeMap[$norm])) {
                $code = "p" . self::$playerNamePhotoCodeMap[$norm];
                return "https://resources.premierleague.com/premierleague/photos/players/{$width}x{$height}/{$code}.png";
            }
        }

        return $missingPhoto;
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
