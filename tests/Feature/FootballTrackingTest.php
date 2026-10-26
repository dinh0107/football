<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\PremierLeagueService;
use Carbon\Carbon;

class FootballTrackingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (file_exists(storage_path('framework/down'))) {
            @unlink(storage_path('framework/down'));
        }
        $this->artisan('migrate');
    }

    protected function tearDown(): void
    {
        if (file_exists(storage_path('framework/down'))) {
            @unlink(storage_path('framework/down'));
        }
        parent::tearDown();
    }

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

    public function test_home_page_invalid_date_falls_back_instead_of_500(): void
    {
        $this->get('/?date=2026-13-45')->assertStatus(200);
        $this->get('/?date=2026-02-30')->assertStatus(200);
        $this->get('/?date[]=x')->assertStatus(200);
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
        $this->assertEquals('truc-tiep-arsenal-vs-chelsea-vong-10-93452', $slug);

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
        // 1. Không có kênh Admin nhập thì không tự đoán kênh
        $mockMatch = [
            'id' => 128923,
            'teams' => [
                ['team' => ['id' => 1, 'name' => 'Arsenal']],
                ['team' => ['id' => 99, 'name' => 'Other Team']],
            ]
        ];
        $this->assertSame('', \App\Helpers\FootballSeoHelper::getTvChannel($mockMatch));

        $matchRes = $this->get('/truc-tiep/128923');
        $matchRes->assertStatus(200);
        $matchRes->assertDontSee('Trực tiếp trên:');
        $matchRes->assertDontSee('Kênh TV:');

        // 2. Admin nhập kênh thì hiển thị trên trang chi tiết trận đấu
        \App\Models\MatchOverride::create(['match_id' => 128923, 'tv_channel' => 'K+ Sport 1']);
        $this->assertSame('K+ Sport 1', \App\Helpers\FootballSeoHelper::getTvChannel($mockMatch));

        $homeRes = $this->get('/?date=2026-10-24');
        $homeRes->assertStatus(200);

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
        $matchRes->assertSee('Coventry City Vòng 1 Ngoại Hạng Anh', false);
        $matchRes->assertSee('đội hình ra sân và thống kê trận đấu', false);
        $matchRes->assertSee('og-default.png', false);

        // OpenGraph và Twitter Tags
        $matchRes->assertSee('og:title', false);
        $matchRes->assertSee('og:description', false);
        $matchRes->assertSee('og:image', false);
        $matchRes->assertSee('og:url', false);
        $matchRes->assertSee('twitter:card', false);

        // Schema.org SportsEvent & BreadcrumbList Rich Snippets
        $matchRes->assertSee('application/ld+json', false);
        $matchRes->assertSee('SportsEvent', false);
        $matchRes->assertDontSee('BroadcastEvent', false);
        $matchRes->assertSee('BreadcrumbList', false);
        $matchRes->assertSee('Lịch Thi Đấu Ngoại Hạng Anh', false);
    }

    /**
     * Test Phase 6: Route /sitemap.xml và Artisan Command sitemap:generate
     */
    public function test_phase_6_sitemap_xml_and_console_command(): void
    {
        // 1. Kiểm tra Route /sitemap.xml (Sitemap Index)
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $response->assertSee('<sitemapindex', false);
        $response->assertSee('sitemap-upcoming.xml', false);
        $response->assertSee('sitemap-results.xml', false);
        $response->assertSee('sitemap-static.xml', false);

        // Kiểm tra Route /sitemap-static.xml
        $staticRes = $this->get('/sitemap-static.xml');
        $staticRes->assertStatus(200);
        $staticRes->assertSee('<urlset', false);
        $staticRes->assertSee('/bang-xep-hang', false);
        $staticRes->assertSee('/lich-thi-dau', false);
        $staticRes->assertSee('/truc-tiep', false);

        // 2. Kiểm tra Artisan Command sitemap:generate
        $this->artisan('sitemap:generate')
            ->expectsOutputToContain('sitemap.xml')
            ->assertExitCode(0);

        $this->assertFileExists(public_path('sitemap.xml'));
        $this->assertFileExists(public_path('sitemap-upcoming.xml'));
        $this->assertFileExists(public_path('sitemap-results.xml'));
        $this->assertFileExists(public_path('sitemap-static.xml'));

        $xmlContent = file_get_contents(public_path('sitemap.xml'));
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $xmlContent);
        $this->assertStringContainsString('<sitemapindex', $xmlContent);
        $this->assertStringContainsString('<loc>', $xmlContent);
    }

    /**
     * Test Phase 7: API Bình chọn Dự đoán kết quả (Community Match Voting) và chặn 1 IP vote 1 lần
     */
    public function test_phase_7_community_match_voting_api(): void
    {
        $matchId = 999901;

        // 1. Lấy thống kê ban đầu qua GET /api/matches/{id}/votes
        $getRes = $this->getJson("/api/matches/{$matchId}/votes");
        $getRes->assertStatus(200);
        $getRes->assertJsonStructure([
            'match_id',
            'total_votes',
            'home' => ['count', 'percent'],
            'draw' => ['count', 'percent'],
            'away' => ['count', 'percent'],
            'has_voted',
            'user_choice',
        ]);
        $this->assertEquals(0, $getRes->json('total_votes'));
        $this->assertFalse($getRes->json('has_voted'));

        // 2. Gửi lượt bình chọn đầu tiên qua POST /api/matches/{id}/vote
        $postRes = $this->postJson("/api/matches/{$matchId}/vote", [
            'choice' => 'home'
        ]);
        $postRes->assertStatus(200);
        $postRes->assertJson([
            'success' => true,
            'message' => 'Bình chọn dự đoán thành công!',
        ]);
        $this->assertEquals(1, $postRes->json('stats.total_votes'));
        $this->assertEquals(100.0, $postRes->json('stats.home.percent'));
        $this->assertTrue($postRes->json('stats.has_voted'));
        $this->assertEquals('home', $postRes->json('stats.user_choice'));

        // 3. Chặn cùng 1 IP vote lần 2 cho cùng 1 trận (phải trả về 409 Conflict)
        $duplicateRes = $this->postJson("/api/matches/{$matchId}/vote", [
            'choice' => 'away'
        ]);
        $duplicateRes->assertStatus(409);
        $duplicateRes->assertJson([
            'success' => false,
            'message' => 'Bạn đã bình chọn cho trận đấu này rồi.',
        ]);
        $this->assertEquals(1, $duplicateRes->json('stats.total_votes'));

        // 4. Validate dữ liệu không hợp lệ
        $invalidRes = $this->postJson("/api/matches/{$matchId}/vote", [
            'choice' => 'invalid_choice'
        ]);
        $invalidRes->assertStatus(422);
    }

    /**
     * Test Phase 7: Ghim trận & Đội bóng yêu thích (Favorites)
     */
    public function test_phase_7_favorites_engine_and_star_icons(): void
    {
        // 1. Kiểm tra file JS favorites.js tồn tại và chứa hàm quản lý
        $jsPath = public_path('js/favorites.js');
        $this->assertFileExists($jsPath);
        $jsContent = file_get_contents($jsPath);
        $this->assertStringContainsString('epl_favorite_teams', $jsContent);
        $this->assertStringContainsString('toggleFavorite', $jsContent);
        $this->assertStringContainsString('reorderMatchesOnHome', $jsContent);
        $this->assertStringContainsString('badge-favorite-pinned', $jsContent);

        // 2. Kiểm tra Trang Chủ có icon Ngôi sao và data-home-id / data-away-id
        $homeRes = $this->get('/?date=2026-10-24');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('btn-favorite-team', false);
        $homeRes->assertSee('star-icon', false);
        $homeRes->assertSee('data-home-id', false);
        $homeRes->assertSee('data-away-id', false);
        $homeRes->assertSee('favorites.js');

        // 3. Kiểm tra trang Bảng Xếp Hạng có icon Ngôi sao cho từng CLB
        $standingsRes = $this->get('/bang-xep-hang');
        $standingsRes->assertStatus(200);
        $standingsRes->assertSee('btn-favorite-team', false);
        $standingsRes->assertSee('data-favorite-team', false);
        $standingsRes->assertSee('favorites.js');
    }

    /**
     * Test Phase 7: Trợ lý AI Tự động nhận định & Tóm tắt trận đấu
     */
    public function test_phase_7_ai_match_service_and_blade_cards(): void
    {
        $aiService = app(\App\Services\AiMatchService::class);

        // 1. Kiểm tra AiMatchService::generateMatchPreview
        $preview = $aiService->generateMatchPreview('Arsenal', 'Chelsea', [
            'ground' => 'Emirates Stadium',
        ]);
        $this->assertIsArray($preview);
        $this->assertEquals('preview', $preview['type']);
        $this->assertStringContainsString('Arsenal', $preview['title']);
        $this->assertStringContainsString('Chelsea', $preview['title']);
        $this->assertNotEmpty($preview['content']);
        $this->assertCount(3, $preview['highlights']);

        // 2. Kiểm tra AiMatchService::generateMatchSummary
        $summary = $aiService->generateMatchSummary([
            ['clock' => '25', 'description' => 'Bàn thắng', 'type' => 'G']
        ], [], [
            'teams' => [
                ['team' => ['name' => 'Arsenal'], 'score' => 2],
                ['team' => ['name' => 'Chelsea'], 'score' => 1],
            ]
        ]);
        $this->assertIsArray($summary);
        $this->assertEquals('summary', $summary['type']);
        $this->assertCount(3, $summary['highlights']);

        // 3. Kiểm tra giao diện Match Center hiển thị cả Widget Bình chọn và Thẻ Trợ Lý AI
        $matchRes = $this->get('/truc-tiep/128923');
        $matchRes->assertStatus(200);
        $matchRes->assertSee('matchVotingWidget');
        $matchRes->assertSee('Dự Đoán Cộng Đồng');
        $matchRes->assertSee('btn-vote-choice');
        $matchRes->assertSee('voting-multi-bar');
        $matchRes->assertSee('ai-assistant-card');
        $matchRes->assertSee('3 Điểm Nhấn');
    }

    /**
     * Test Phase 8: Đăng nhập Admin bảo mật & Auth Middleware
     */
    public function test_phase_8_admin_auth_and_middleware(): void
    {
        // 1. Khách chưa đăng nhập truy cập /admin phải bị chuyển hướng đến /admin/login
        $guestRes = $this->get('/admin');
        $guestRes->assertRedirect('/admin/login');

        // 2. Trang đăng nhập hiển thị form đúng chuẩn
        $loginPageRes = $this->get('/admin/login');
        $loginPageRes->assertStatus(200);
        $loginPageRes->assertSee('Đăng Nhập Quản Trị');
        $loginPageRes->assertSee('name="email"', false);
        $loginPageRes->assertSee('name="password"', false);

        // 3. Người dùng thường (không phải admin) truy cập bị chặn 403 Forbidden
        $regularUser = \App\Models\User::factory()->create([
            'is_admin' => false,
        ]);
        $forbiddenRes = $this->actingAs($regularUser)->get('/admin');
        $forbiddenRes->assertStatus(403);

        // 4. Đăng nhập với tài khoản Admin thành công
        $adminUser = \App\Models\User::factory()->create([
            'email' => 'superadmin@premierleague.local',
            'password' => bcrypt('secret123456'),
            'is_admin' => true,
        ]);

        $postLoginRes = $this->post('/admin/login', [
            'email' => 'superadmin@premierleague.local',
            'password' => 'secret123456',
        ]);
        $postLoginRes->assertRedirect('/admin');
        $this->assertAuthenticatedAs($adminUser);

        // 5. Admin truy cập Dashboard /admin thành công
        $dashRes = $this->actingAs($adminUser)->get('/admin');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Bảng Điều Khiển Quản Trị');
        $dashRes->assertSee('Cập Nhật Nhanh Kênh Phát Sóng TV Guide');
        $dashRes->assertSee('Quản Lý Chế Độ Bảo Trì & Xóa Cache', false);
        $dashRes->assertSee('Quản Lý Các Vị Trí Đặt Banner Quảng Cáo');
    }

    /**
     * Test Phase 8: Cập nhật nhanh thông tin kênh phát sóng TV cho trận đấu
     */
    public function test_phase_8_admin_tv_override_functionality(): void
    {
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);

        // Cập nhật đè kênh TV cho trận 128923 thành 'FPT Play Độc Quyền'
        $updateRes = $this->actingAs($admin)->post('/admin/tv-channel', [
            'match_id' => 128923,
            'tv_channel' => 'FPT Play Độc Quyền',
            'notes' => 'Trận cầu tâm điểm cuối tuần',
        ]);

        $updateRes->assertRedirect('/admin');
        $updateRes->assertSessionHas('success');

        // Kiểm tra cơ sở dữ liệu đã lưu
        $this->assertDatabaseHas('match_overrides', [
            'match_id' => 128923,
            'tv_channel' => 'FPT Play Độc Quyền',
        ]);

        // Kiểm tra FootballSeoHelper::getTvChannel ưu tiên kênh override
        $channel = \App\Helpers\FootballSeoHelper::getTvChannel(['id' => 128923]);
        $this->assertEquals('FPT Play Độc Quyền', $channel);
    }

    /**
     * Test Phase 8: Thao tác Xóa cache thủ công
     */
    public function test_phase_8_admin_cache_clear_control(): void
    {
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);

        \Illuminate\Support\Facades\Cache::put('test_epl_key', 'test_value', 60);
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('test_epl_key'));

        $clearRes = $this->actingAs($admin)->post('/admin/cache/clear');
        $clearRes->assertRedirect('/admin');
        $clearRes->assertSessionHas('success');

        $this->assertFalse(\Illuminate\Support\Facades\Cache::has('test_epl_key'));
    }

    /**
     * Test Phase 8: Quản lý banner quảng cáo và hiển thị trên giao diện người dùng
     */
    public function test_phase_8_ad_banner_management_and_view_rendering(): void
    {
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);

        // 1. Tạo banner đầu trang (banner_top)
        $bannerTopRes = $this->actingAs($admin)->post('/admin/banner', [
            'position' => 'banner_top',
            'title' => 'Khuyến Mãi Độc Quyền K+',
            'image_url' => 'https://example.com/banner-top.jpg',
            'target_url' => 'https://kplus.vn/khuyen-mai',
            'alt_text' => 'Xem Ngoại Hạng Anh trên K+',
            'is_active' => '1',
        ]);
        $bannerTopRes->assertRedirect('/admin');

        // 2. Tạo banner dưới tỷ số (banner_below_score)
        $bannerScoreRes = $this->actingAs($admin)->post('/admin/banner', [
            'position' => 'banner_below_score',
            'title' => 'Đặt Cược Thể Thao FPT Play',
            'image_url' => 'https://example.com/banner-score.jpg',
            'target_url' => 'https://fptplay.vn',
            'alt_text' => 'FPT Play Thể Thao',
            'is_active' => '1',
        ]);
        $bannerScoreRes->assertRedirect('/admin');

        // 3. Kiểm tra Model AdBanner::getActiveBanner
        $activeTop = \App\Models\AdBanner::getActiveBanner('banner_top');
        $this->assertNotNull($activeTop);
        $this->assertEquals('Khuyến Mãi Độc Quyền K+', $activeTop->title);
        $this->assertStringContainsString('https://example.com/banner-top.jpg', $activeTop->rendered_html);

        $activeScore = \App\Models\AdBanner::getActiveBanner('banner_below_score');
        $this->assertNotNull($activeScore);
        $this->assertStringContainsString('https://example.com/banner-score.jpg', $activeScore->rendered_html);

        // 4. Kiểm tra trang chủ hiển thị banner_top
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('banner-top');
        $homeRes->assertSee('https://example.com/banner-top.jpg');

        // 5. Kiểm tra trang Match Center hiển thị banner_below_score
        $matchRes = $this->get('/truc-tiep/128923');
        $matchRes->assertStatus(200);
        $matchRes->assertSee('banner-below-score');
        $matchRes->assertSee('https://example.com/banner-score.jpg');
    }
}


