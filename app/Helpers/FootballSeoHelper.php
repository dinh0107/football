<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use App\Services\PulseLiveService;
use Carbon\Carbon;

/**
 * Helper chuyên trách xử lý SEO, URL Slug và Schema.org/SportsEvent cho trận đấu
 */
class FootballSeoHelper
{
    /**
     * Tạo URL Slug tiếng Việt không dấu chuẩn SEO cho trận đấu
     * Định dạng: [ten-doi-nha]-vs-[ten-doi-khach]-[fixture_id]
     */
    public static function generateMatchSlug(array $match): string
    {
        $homeTeam = $match['teams'][0]['team']['name'] ?? 'Doi-Nha';
        $awayTeam = $match['teams'][1]['team']['name'] ?? 'Doi-Khach';
        $id = (int) ($match['id'] ?? 0);

        $slugBase = Str::slug("{$homeTeam} vs {$awayTeam}");
        return "{$slugBase}-{$id}";
    }

    /**
     * Trích xuất Fixture ID từ slug (ví dụ: 'arsenal-vs-chelsea-128923' -> 128923)
     */
    public static function extractIdFromSlug(string $slug): ?int
    {
        if (preg_match('/-(\d+)$/', $slug, $matches)) {
            return (int) $matches[1];
        }

        if (is_numeric($slug)) {
            return (int) $slug;
        }

        return null;
    }

    /**
     * Sinh tiêu đề trang (Title) tối ưu SEO theo từng trạng thái trận đấu
     */
    public static function generateTitle(array $match): string
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $status = $match['status'] ?? 'U';

        $homeScore = isset($match['teams'][0]['score']) ? (int) $match['teams'][0]['score'] : null;
        $awayScore = isset($match['teams'][1]['score']) ? (int) $match['teams'][1]['score'] : null;

        if ($status === 'L') {
            $clock = $match['clock']['label'] ?? 'Đang diễn ra';
            return "[TRỰC TIẾP {$clock}] {$home} {$homeScore} - {$awayScore} {$away} | Ngoại Hạng Anh";
        }

        if ($status === 'C') {
            return "[Kết quả FT] {$home} {$homeScore} - {$awayScore} {$away} | Ngoại Hạng Anh Mới Nhất";
        }

        $timeStr = '';
        if (isset($match['kickoff']['millis'])) {
            $timeStr = ' (' . PulseLiveService::formatKickoffVietnam($match['kickoff']['millis'], 'H:i d/m') . ')';
        }

        return "{$home} vs {$away}{$timeStr} | Trực Tiếp Ngoại Hạng Anh & Đội Hình";
    }

    /**
     * Sinh Meta Description chuẩn SEO hấp dẫn, thu hút click
     */
    public static function generateMetaDescription(array $match): string
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $ground = $match['ground']['name'] ?? 'Sân vận động Ngoại Hạng Anh';
        $city = $match['ground']['city'] ?? 'Vương quốc Anh';
        $status = $match['status'] ?? 'U';

        $homeScore = isset($match['teams'][0]['score']) ? (int) $match['teams'][0]['score'] : null;
        $awayScore = isset($match['teams'][1]['score']) ? (int) $match['teams'][1]['score'] : null;

        if ($status === 'L') {
            $clock = $match['clock']['label'] ?? 'Hiệp đấu';
            return "Xem trực tiếp {$home} vs {$away} vòng Ngoại Hạng Anh ({$clock}). Tỉ số đang là {$home} {$homeScore} - {$awayScore} {$away}. Cập nhật diễn biến, thẻ phạt, bàn thắng và thông số trận đấu theo thời gian thực.";
        }

        if ($status === 'C') {
            return "Kết quả trận đấu {$home} vs {$away}: Chung cuộc {$homeScore} - {$awayScore}. Xem lại diễn biến chính, danh sách cầu thủ ghi bàn, video highlights và bảng xếp hạng Ngoại Hạng Anh.";
        }

        $kickoff = isset($match['kickoff']['millis'])
            ? PulseLiveService::formatKickoffVietnam($match['kickoff']['millis'], 'H:i ngày d/m/Y')
            : 'sắp tới';

        return "Trực tiếp trận đấu {$home} vs {$away} diễn ra lúc {$kickoff} tại SVĐ {$ground} ({$city}) giải Ngoại Hạng Anh. Cập nhật đội hình ra sân dự kiến, lịch sử đối đầu và link xem trực tuyến chất lượng cao.";
    }

    /**
     * Tự động sinh JSON-LD Schema.org/SportsEvent động theo dữ liệu trận đấu
     */
    public static function generateSportsEventSchema(array $match, string $currentUrl = ''): array
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $homeId = $match['teams'][0]['team']['id'] ?? 0;
        $awayId = $match['teams'][1]['team']['id'] ?? 0;
        $groundName = $match['ground']['name'] ?? 'Premier League Stadium';
        $groundCity = $match['ground']['city'] ?? 'United Kingdom';
        $status = $match['status'] ?? 'U';

        // Xác định eventStatus theo chuẩn Schema.org
        $eventStatus = match ($status) {
            'L' => 'https://schema.org/EventLive',
            'C' => 'https://schema.org/EventCompleted',
            default => 'https://schema.org/EventScheduled',
        };

        $startDate = null;
        if (isset($match['kickoff']['millis'])) {
            $startDate = Carbon::createFromTimestamp((int) ($match['kickoff']['millis'] / 1000), 'UTC')
                ->toIso8601String();
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'SportsEvent',
            'name' => "{$home} vs {$away} - Premier League",
            'description' => self::generateMetaDescription($match),
            'url' => $currentUrl ?: url()->current(),
            'sport' => 'Soccer',
            'eventStatus' => $eventStatus,
            'organizer' => [
                '@type' => 'Organization',
                'name' => 'Premier League',
                'url' => 'https://www.premierleague.com',
                'logo' => 'https://resources.premierleague.com/premierleague/badges/100/t1.png'
            ],
            'location' => [
                '@type' => 'Place',
                'name' => $groundName,
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $groundCity,
                    'addressCountry' => 'GB'
                ]
            ],
            'homeTeam' => [
                '@type' => 'SportsTeam',
                'name' => $home,
                'logo' => PulseLiveService::getTeamBadgeUrl($homeId, 100)
            ],
            'awayTeam' => [
                '@type' => 'SportsTeam',
                'name' => $away,
                'logo' => PulseLiveService::getTeamBadgeUrl($awayId, 100)
            ],
            'competitor' => [
                [
                    '@type' => 'SportsTeam',
                    'name' => $home,
                    'logo' => PulseLiveService::getTeamBadgeUrl($homeId, 100)
                ],
                [
                    '@type' => 'SportsTeam',
                    'name' => $away,
                    'logo' => PulseLiveService::getTeamBadgeUrl($awayId, 100)
                ]
            ]
        ];

        if ($startDate) {
            $schema['startDate'] = $startDate;
        }

        return $schema;
    }
}
