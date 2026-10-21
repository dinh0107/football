<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\SeoHelper;

class SeoHelperTest extends TestCase
{
    public function test_generate_match_slug_with_multiple_arguments(): void
    {
        $slug = SeoHelper::generateMatchSlug('Arsenal', 'Chelsea', 10, 93452);
        $this->assertEquals('truc-tiep-arsenal-vs-chelsea-vong-10-93452', $slug);

        $extractedId = SeoHelper::extractIdFromSlug($slug);
        $this->assertEquals(93452, $extractedId);
    }

    public function test_generate_match_slug_with_match_array(): void
    {
        $mockMatch = [
            'id' => 128923,
            'gameweek' => ['gameweek' => 1],
            'teams' => [
                ['team' => ['name' => 'Arsenal']],
                ['team' => ['name' => 'Coventry City']],
            ]
        ];

        $slug = SeoHelper::generateMatchSlug($mockMatch);
        $this->assertEquals('truc-tiep-arsenal-vs-coventry-city-vong-1-128923', $slug);
    }

    public function test_generate_match_title_states(): void
    {
        $upcomingMatch = [
            'status' => 'U',
            'kickoff' => ['millis' => 1787274000000],
            'teams' => [
                ['team' => ['name' => 'Arsenal'], 'score' => 0],
                ['team' => ['name' => 'Chelsea'], 'score' => 0],
            ]
        ];
        $upcomingTitle = SeoHelper::generateMatchTitle($upcomingMatch);
        $this->assertStringContainsString('Trực tiếp Arsenal vs Chelsea', $upcomingTitle);
        $this->assertStringContainsString('TySoNgoaiHang', $upcomingTitle);

        $liveMatch = [
            'status' => 'L',
            'clock' => ['label' => "35'"],
            'teams' => [
                ['team' => ['name' => 'Arsenal'], 'score' => 1],
                ['team' => ['name' => 'Chelsea'], 'score' => 0],
            ]
        ];
        $liveTitle = SeoHelper::generateMatchTitle($liveMatch);
        $this->assertStringContainsString('LIVE', $liveTitle);
        $this->assertStringContainsString('Arsenal 1 - 0 Chelsea', $liveTitle);

        $finishedMatch = [
            'status' => 'C',
            'teams' => [
                ['team' => ['name' => 'Arsenal'], 'score' => 3],
                ['team' => ['name' => 'Coventry City'], 'score' => 0],
            ]
        ];
        $finishedTitle = SeoHelper::generateMatchTitle($finishedMatch);
        $this->assertStringContainsString('FT 3-0', $finishedTitle);
    }

    public function test_generate_meta_description(): void
    {
        $mockMatch = [
            'id' => 128923,
            'gameweek' => ['gameweek' => 1],
            'kickoff' => ['millis' => 1787274000000],
            'teams' => [
                ['team' => ['name' => 'Arsenal']],
                ['team' => ['name' => 'Coventry City']],
            ]
        ];

        $desc = SeoHelper::generateMetaDescription($mockMatch);
        $this->assertStringContainsString('Xem trực tiếp tỷ số trận Arsenal vs Coventry City', $desc);
        $this->assertStringContainsString('kênh phát sóng trên', $desc);
        $this->assertGreaterThan(120, mb_strlen($desc));
    }

    public function test_generate_match_seo_paragraph(): void
    {
        $mockMatch = [
            'id' => 128923,
            'status' => 'U',
            'gameweek' => ['gameweek' => 1],
            'ground' => ['name' => 'Emirates Stadium', 'city' => 'London'],
            'kickoff' => ['millis' => 1787274000000],
            'teams' => [
                ['team' => ['name' => 'Arsenal']],
                ['team' => ['name' => 'Coventry City']],
            ]
        ];

        $paragraph = SeoHelper::generateMatchSeoParagraph($mockMatch);
        $this->assertStringContainsString('Arsenal', $paragraph);
        $this->assertStringContainsString('Coventry City', $paragraph);
        $this->assertStringContainsString('Vòng 1', $paragraph);
        $this->assertStringContainsString('Emirates Stadium', $paragraph);
        $this->assertStringContainsString('TySoNgoaiHang.site', $paragraph);
    }
}
