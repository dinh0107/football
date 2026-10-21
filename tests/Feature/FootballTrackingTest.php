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
     * Test Phase 2: Route /truc-tiep/{id} chuyển hướng 302 đến slug trận đấu
     */
    public function test_live_match_by_id_route_redirects(): void
    {
        $response = $this->get('/truc-tiep/128923');
        $response->assertStatus(302);
        $response->assertRedirect('/tran-dau/arsenal-vs-coventry-city-128923');
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
     * Test Chi tiết trận đấu chuẩn SEO, Schema.org và 301 Canonical Redirect
     */
    public function test_match_detail_with_canonical_redirect_and_schema(): void
    {
        $wrongSlugResponse = $this->get('/tran-dau/random-slug-128923');
        $wrongSlugResponse->assertStatus(301);
        $wrongSlugResponse->assertRedirect('/tran-dau/arsenal-vs-coventry-city-128923');

        $correctResponse = $this->get('/tran-dau/arsenal-vs-coventry-city-128923');
        $correctResponse->assertStatus(200);
        $correctResponse->assertSee('Arsenal');
        $correctResponse->assertSee('Coventry City');
        $correctResponse->assertSee('SportsEvent');
        $correctResponse->assertSee('https://schema.org');
    }
}
