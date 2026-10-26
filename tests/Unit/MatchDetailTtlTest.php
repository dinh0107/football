<?php

namespace Tests\Unit;

use App\Services\PremierLeagueService;
use PHPUnit\Framework\TestCase;

class MatchDetailTtlTest extends TestCase
{
    public function test_upcoming_match_cache_expires_at_kickoff(): void
    {
        $inTwoHours = (time() + 7200) * 1000;
        $ttl = PremierLeagueService::matchDetailTtl(['status' => 'U', 'kickoff' => ['millis' => $inTwoHours]]);
        $this->assertEqualsWithDelta(7200, $ttl, 5);
    }

    public function test_upcoming_match_past_kickoff_uses_short_ttl(): void
    {
        $tenMinutesAgo = (time() - 600) * 1000;
        $this->assertSame(20, PremierLeagueService::matchDetailTtl(['status' => 'U', 'kickoff' => ['millis' => $tenMinutesAgo]]));
    }

    public function test_far_future_match_is_capped_at_three_days(): void
    {
        $inTenDays = (time() + 864000) * 1000;
        $this->assertSame(259200, PremierLeagueService::matchDetailTtl(['status' => 'U', 'kickoff' => ['millis' => $inTenDays]]));
    }

    public function test_fixtures_ttl_follows_match_status(): void
    {
        $now = time();
        $completed = ['status' => 'C', 'kickoff' => ['millis' => ($now - 86400) * 1000]];
        $live = ['status' => 'L', 'kickoff' => ['millis' => ($now - 1800) * 1000]];
        $justKickedOff = ['status' => 'U', 'kickoff' => ['millis' => ($now - 60) * 1000]];
        $postponed = ['status' => 'U', 'kickoff' => ['millis' => ($now - 86400) * 1000]];
        $inOneHour = ['status' => 'U', 'kickoff' => ['millis' => ($now + 3600) * 1000]];

        $this->assertSame(86400, PremierLeagueService::fixturesTtl([$completed]));
        $this->assertSame(86400, PremierLeagueService::fixturesTtl([]));
        $this->assertSame(60, PremierLeagueService::fixturesTtl([$completed, $live, $inOneHour]));
        $this->assertSame(60, PremierLeagueService::fixturesTtl([$justKickedOff]));
        $this->assertEqualsWithDelta(3600, PremierLeagueService::fixturesTtl([$completed, $postponed, $inOneHour]), 5);
    }

    public function test_live_and_completed_ttl(): void
    {
        $this->assertSame(20, PremierLeagueService::matchDetailTtl(['status' => 'L']));
        $this->assertSame(20, PremierLeagueService::matchDetailTtl(['status' => 'I']));
        $this->assertSame(604800, PremierLeagueService::matchDetailTtl(['status' => 'C']));
    }
}
