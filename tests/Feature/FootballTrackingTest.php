<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\PremierLeagueService;
use Carbon\Carbon;

class FootballTrackingTest extends TestCase
{
    /**
     * Test Phase 2: Trang chủ mặc định hiển thị ngày hôm nay và các nút điều hướng
     */
    public function test_home_page_loads_with_default_date(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Lịch Thi Đấu & Kết Quả Ngoại Hạng Anh', false);
        $response->assertSee('Hôm qua');
        $response->assertSee('Hôm nay');
        $response->assertSee('Ngày mai');
        $response->assertSee('datePicker');
    }

    /**
     * Test Phase 2: Trang chủ với tham số ?date=YYYY-MM-DD tải danh sách trận đấu và lọc theo tab
     */
    public function test_home_page_with_custom_date_loads_matches(): void
    {
        $response = $this->get('/?date=2026-10-24');
        $response->assertStatus(200);
        $response->assertSee('Arsenal');
        $response->assertSee('Everton');
        $response->assertSee('Chelsea');
        $response->assertSee('Tottenham Hotspur');
        $response->assertSee('data-filter="all"', false);
        $response->assertSee('data-filter="L"', false);
    }

    /**
     * Test Phase 2: PremierLeagueService::getFixturesByDate() hoạt động và cache dữ liệu
     */
    public function test_get_fixtures_by_date_service(): void
    {
        $service = app(PremierLeagueService::class);
        $matches = $service->getFixturesByDate('2026-10-24');

        $this->assertIsArray($matches);
        $this->assertNotEmpty($matches);
        $this->assertEquals('Aston Villa', $matches[1]['teams'][0]['team']['name'] ?? '');
    }

    /**
     * Test Phase 4: Route /truc-tiep/{id} tải trực tiếp Match Center (200 OK)
     */
    public function test_live_match_by_id_route_loads_match_center(): void
    {
        $response = $this->get('/truc-tiep/128923');
        $response->assertStatus(200);
        $response->assertSee('Arsenal');
        $response->assertSee('Coventry City');
        $response->assertSee('tab-events');
    }

    /**
     * Test Phase 3: Bảng xếp hạng load 200 OK với đầy đủ cột, đánh dấu khu vực và Tab Top Vua Phá Lưới
     */
    public function test_standings_page_loads_successfully(): void
    {
        $response = $this->get('/bang-xep-hang');
        $response->assertStatus(200);
        $response->assertSee('Bảng Xếp Hạng Ngoại Hạng Anh');
        $response->assertSee('Manchester City');
        $response->assertSee('Top Vua Phá Lưới');
        $response->assertSee('zone-ucl');
        $response->assertSee('zone-uel');
        $response->assertSee('zone-rel');
    }

    /**
     * Test Phase 3: PremierLeagueService::getLiveStandings() và getTopScorers()
     */
    public function test_phase_3_live_standings_and_top_scorers_service(): void
    {
        $service = app(PremierLeagueService::class);
        
        // 1. Kiểm tra live standings
        $liveStandings = $service->getLiveStandings();
        $this->assertIsArray($liveStandings);
        $this->assertArrayHasKey('entries', $liveStandings);
        $this->assertCount(20, $liveStandings['entries']);

        // 2. Kiểm tra top scorers
        $topScorers = $service->getTopScorers();
        $this->assertIsArray($topScorers);
        $this->assertCount(10, $topScorers);
        $this->assertArrayHasKey('goals', $topScorers[0]);
        $this->assertArrayHasKey('assists', $topScorers[0]);
    }

    /**
     * Test Lịch thi đấu load 200 OK
     */
    public function test_fixtures_page_loads_successfully(): void
    {
        $response = $this->get('/lich-thi-dau');
        $response->assertStatus(200);
        $response->assertSee('Lịch Thi Đấu &amp; Kết Quả', false);
    }

    /**
     * Test Trang Trực tiếp load 200 OK
     */
    public function test_live_page_loads_successfully(): void
    {
        $response = $this->get('/truc-tiep');
        $response->assertStatus(200);
        $response->assertSee('Trực Tiếp Tỷ Số Ngoại Hạng Anh');
    }

    /**
     * Test API Live Scores trả về JSON hợp lệ
     */
    public function test_api_live_scores_returns_valid_json(): void
    {
        $response = $this->getJson('/api/live-scores');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'timestamp',
            'count',
            'matches'
        ]);
    }

    /**
     * Test Phase 4: Chi tiết trận đấu chuẩn Match Center: Header, Diễn biến, Sân 2D & Thống kê
     */
    public function test_match_detail_match_center_features(): void
    {
        $response = $this->get('/truc-tiep/arsenal-vs-coventry-city-128923');
        $response->assertStatus(200);
        $response->assertSee('Arsenal');
        $response->assertSee('Coventry City');
        $response->assertSee('tab-events');
        $response->assertSee('tab-pitch');
        $response->assertSee('tab-stats');
        $response->assertSee('pitch-2d');
        $response->assertSee('pitch-line-center');
        $response->assertSee('dual-progress-bar');
        $response->assertSee('Kiểm soát bóng');
        $response->assertSee('SportsEvent');

        // Test truy cập trực tiếp qua ID /truc-tiep/{id}
        $idResponse = $this->get('/truc-tiep/128923');
        $idResponse->assertStatus(200);
        $idResponse->assertSee('Arsenal');

        // Test 301 Redirect tương thích ngược từ /tran-dau/{slug} và /match/{id}
        $redirect1 = $this->get('/tran-dau/arsenal-vs-coventry-city-128923');
        $redirect1->assertStatus(301);
        $redirect1->assertRedirect('/truc-tiep/arsenal-vs-coventry-city-128923');

        $redirect2 = $this->get('/match/128923');
        $redirect2->assertStatus(301);
        $redirect2->assertRedirect('/truc-tiep/128923');
    }

    /**
     * Test Phase 4: PremierLeagueService match methods
     */
    public function test_phase_4_service_methods(): void
    {
        $service = app(PremierLeagueService::class);
        
        $details = $service->getMatchDetails(128923);
        $this->assertNotNull($details);
        $this->assertEquals('Arsenal', $details['teams'][0]['team']['name'] ?? '');

        $events = $service->getMatchEvents(128923);
        $this->assertIsArray($events);
        $this->assertNotEmpty($events);

        $lineups = $service->getMatchLineups(128923);
        $this->assertTrue($lineups['hasLineups']);
        $this->assertNotEmpty($lineups['home']['starters']);
        $this->assertNotEmpty($lineups['home']['pitchRows']);

        $stats = $service->getMatchStats(128923);
        $this->assertArrayHasKey('possession', $stats);
        $this->assertArrayHasKey('shots', $stats);
    }

    /**
     * Test Phase 5: API Endpoint GET /api/matches/live trả về JSON hợp lệ
     */
    public function test_phase_5_api_live_matches_endpoint(): void
    {
        $response = $this->getJson('/api/matches/live');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    /**
     * Test Phase 5: API Endpoint hỗ trợ test_goal và fixture_id với đầy đủ cấu trúc
     */
    public function test_phase_5_api_live_matches_structure_and_fixture_query(): void
    {
        // 1. Test cấu trúc với test_goal=1
        $demoResponse = $this->getJson('/api/matches/live?test_goal=1');
        $demoResponse->assertStatus(200);
        $demoResponse->assertJsonStructure([
            '*' => [
                'id',
                'home_team' => ['id', 'name', 'shortName', 'badge'],
                'away_team' => ['id', 'name', 'shortName', 'badge'],
                'home_score',
                'away_score',
                'minute',
                'status',
                'latest_events',
                'updated_at',
            ]
        ]);

        $firstMatch = $demoResponse->json()[0];
        $this->assertEquals(2, $firstMatch['home_score']);
        $this->assertEquals(1, $firstMatch['away_score']);
        $this->assertEquals('L', $firstMatch['status']);
        $this->assertNotEmpty($firstMatch['latest_events']);

        // 2. Test với fixture_id cụ thể
        $fixtureResponse = $this->getJson('/api/matches/live?fixture_id=128923');
        $fixtureResponse->assertStatus(200);
        $fixtureData = $fixtureResponse->json();
        $this->assertIsArray($fixtureData);
        if (!empty($fixtureData)) {
            $this->assertEquals(128923, $fixtureData[0]['id']);
            $this->assertArrayHasKey('home_score', $fixtureData[0]);
            $this->assertArrayHasKey('away_score', $fixtureData[0]);
            $this->assertArrayHasKey('minute', $fixtureData[0]);
            $this->assertArrayHasKey('status', $fixtureData[0]);
            $this->assertArrayHasKey('latest_events', $fixtureData[0]);
        }
    }

    /**
     * Test Phase 5: File JS thuần, âm thanh goal.mp3 và tích hợp vào Blade
     */
    public function test_phase_5_assets_and_blade_integration(): void
    {
        // Kiểm tra file livescore-engine.js tồn tại và chứa các hàm cốt lõi
        $jsPath = public_path('js/livescore-engine.js');
        $this->assertFileExists($jsPath);
        $jsContent = file_get_contents($jsPath);
        $this->assertStringContainsString('diffAndUpdateMatch', $jsContent);
        $this->assertStringContainsString('flash-highlight-goal', $jsContent);
        $this->assertStringContainsString('playGoalSound', $jsContent);
        $this->assertStringContainsString('triggerGoalTabTitle', $jsContent);
        $this->assertStringContainsString('eplGoalSoundToggle', $jsContent);
        $this->assertStringContainsString('⚽ VÀO!', $jsContent);

        // Kiểm tra file âm thanh goal.mp3 tồn tại và có dung lượng hợp lệ
        $soundPath = public_path('sounds/goal.mp3');
        $this->assertFileExists($soundPath);
        $this->assertGreaterThan(1000, filesize($soundPath));

        // Kiểm tra trang chủ và trang match center nhúng livescore-engine.js
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('livescore-engine.js');

        $matchRes = $this->get('/truc-tiep/128923');
        $matchRes->assertStatus(200);
        $matchRes->assertSee('livescore-engine.js');
        $matchRes->assertSee('data-fixture-id="128923"', false);
        $matchRes->assertSee('data-score-home', false);
        $matchRes->assertSee('data-score-away', false);
    }

    /**
     * Test Phase 6: URL thân thiện chuẩn SEO /truc-tiep/{slug}-{id} và Helper FootballSeoHelper
     */
    public function test_phase_6_seo_urls_and_slug_generation(): void
    {
        $mockMatch = [
            'id' => 93452,
            'gameweek' => ['gameweek' => 10],
            'teams' => [
                ['team' => ['name' => 'Arsenal']],
                ['team' => ['name' => 'Chelsea']],
            ]
        ];

        $slug = \App\Helpers\FootballSeoHelper::generateMatchSlug($mockMatch);
        $this->assertEquals('arsenal-vs-chelsea-vong-10-93452', $slug);

        $extractedId = \App\Helpers\FootballSeoHelper::extractIdFromSlug($slug);
        $this->assertEquals(93452, $extractedId);

        // Test truy cập trận đấu qua URL slug chuẩn SEO
        $res = $this->get('/truc-tiep/arsenal-vs-coventry-city-vong-1-128923');
        $res->assertStatus(200);
        $res->assertSee('Arsenal');
        $res->assertSee('Coventry City');
    }

    /**
     * Test Phase 6: Tiện ích Kênh phát sóng tại Việt Nam (TV Guide)
     */
    public function test_phase_6_tv_channel_guide_badges(): void
    {
        // 1. Kiểm tra FootballSeoHelper gán kênh bản quyền VN
        $mockMatch = [
            'id' => 128923,
            'teams' => [
                ['team' => ['id' => 1, 'name' => 'Arsenal']],
                ['team' => ['id' => 99, 'name' => 'Other Team']],
            ]
        ];
        $channel = \App\Helpers\FootballSeoHelper::getTvChannel($mockMatch);
        $this->assertContains($channel, ['K+ Sport 1', 'TV360', 'FPT Play', 'VTVcab ON']);

        // 2. Kiểm tra badge kênh phát sóng trên Trang chủ
        $homeRes = $this->get('/?date=2026-10-24');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('badge-tv-guide');

        // 3. Kiểm tra thông tin kênh phát sóng trên trang chi tiết trận đấu
        $matchRes = $this->get('/truc-tiep/128923');
        $matchRes->assertStatus(200);
        $matchRes->assertSee('Trực tiếp trên:');
        $matchRes->assertSee('Kênh TV:');
    }

    /**
     * Test Phase 6: Dynamic Meta Tags, OpenGraph và Schema.org/SportsEvent JSON-LD
     */
    public function test_phase_6_dynamic_meta_tags_and_schema(): void
    {
        $matchRes = $this->get('/truc-tiep/128923');
        $matchRes->assertStatus(200);

        // Title chuẩn SEO động
        $matchRes->assertSee('<title>Trực tiếp Arsenal vs Coventry City', false);
        $matchRes->assertSee('Tỷ số, Đội hình', false);

        // Meta Description chuẩn SEO chứa đầy đủ từ khóa
        $matchRes->assertSee('Xem trực tiếp tỷ số trận Arsenal vs Coventry City', false);
        $matchRes->assertSee('kênh phát sóng trên', false);

        // OpenGraph và Twitter Tags
        $matchRes->assertSee('og:title', false);
        $matchRes->assertSee('og:description', false);
        $matchRes->assertSee('og:image', false);
        $matchRes->assertSee('og:url', false);
        $matchRes->assertSee('twitter:card', false);

        // Schema.org SportsEvent Rich Snippets
        $matchRes->assertSee('application/ld+json', false);
        $matchRes->assertSee('SportsEvent', false);
        $matchRes->assertSee('BroadcastEvent', false);
    }

    /**
     * Test Phase 6: Route /sitemap.xml và Artisan Command sitemap:generate
     */
    public function test_phase_6_sitemap_xml_and_console_command(): void
    {
        // 1. Kiểm tra Route /sitemap.xml
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee('<urlset', false);
        $response->assertSee('/bang-xep-hang', false);
        $response->assertSee('/lich-thi-dau', false);
        $response->assertSee('/truc-tiep', false);

        // 2. Kiểm tra Artisan Command sitemap:generate
        $this->artisan('sitemap:generate')
            ->expectsOutputToContain('sitemap.xml')
            ->assertExitCode(0);

        $this->assertFileExists(public_path('sitemap.xml'));
        $xmlContent = file_get_contents(public_path('sitemap.xml'));
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $xmlContent);
        $this->assertStringContainsString('<loc>', $xmlContent);
    }
}

