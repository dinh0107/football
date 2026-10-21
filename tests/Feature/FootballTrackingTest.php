<?php

namespace Tests\Feature;

use Tests\TestCase;

class FootballTrackingTest extends TestCase
{
    /**
     * Test Bảng xếp hạng load 200 OK và chứa từ khóa chính
     */
    public function test_standings_page_loads_successfully(): void
    {
        $response = $this->get('/bang-xep-hang');
        $response->assertStatus(200);
        $response->assertSee('Bảng Xếp Hạng Ngoại Hạng Anh');
        $response->assertSee('Manchester City');
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
        // 1. Kiểm tra 301 Redirect khi slug chưa chuẩn
        $wrongSlugResponse = $this->get('/tran-dau/random-slug-128923');
        $wrongSlugResponse->assertStatus(301);
        $wrongSlugResponse->assertRedirect('/tran-dau/arsenal-vs-coventry-city-128923');

        // 2. Kiểm tra trang chuẩn SEO 200 OK
        $correctResponse = $this->get('/tran-dau/arsenal-vs-coventry-city-128923');
        $correctResponse->assertStatus(200);
        $correctResponse->assertSee('Arsenal');
        $correctResponse->assertSee('Coventry City');
        $correctResponse->assertSee('SportsEvent');
        $correctResponse->assertSee('https://schema.org');
    }
}
