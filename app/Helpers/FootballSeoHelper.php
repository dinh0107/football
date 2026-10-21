<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use App\Services\PulseLiveService;
use Carbon\Carbon;

/**
 * Helper chuyên trách xử lý SEO, URL Slug, TV Guide và Schema.org/SportsEvent cho trận đấu
 */
class FootballSeoHelper
{
    /**
     * Tạo URL Slug tiếng Việt không dấu chuẩn SEO cho trận đấu
     * Định dạng: [ten-doi-nha]-vs-[ten-doi-khach]-vong-[so-vong]-[fixture_id]
     * Ví dụ: arsenal-vs-chelsea-vong-10-93452
     */
    public static function generateMatchSlug(array $match): string
    {
        $homeTeam = $match['teams'][0]['team']['name'] ?? 'doi-nha';
        $awayTeam = $match['teams'][1]['team']['name'] ?? 'doi-khach';
        $id = (int) ($match['id'] ?? 0);

        $gwNumber = $match['gameweek']['gameweek'] ?? ($match['gameweek']['id'] ?? null);
        $vongText = $gwNumber ? "vong {$gwNumber}" : '';

        $slugBase = Str::slug(trim("{$homeTeam} vs {$awayTeam} {$vongText}"));
        return "{$slugBase}-{$id}";
    }

    /**
     * Trích xuất Fixture ID từ slug (ví dụ: 'arsenal-vs-chelsea-vong-10-93452' -> 93452)
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
     * Xác định Kênh phát sóng tại Việt Nam (TV Guide)
     * Thêm kênh bản quyền tại VN: K+ Sport 1, FPT Play, TV360, VTVcab ON
     */
    public static function getTvChannel(array $match): string
    {
        if (!empty($match['tv_channel'])) {
            return $match['tv_channel'];
        }

        $id = (int) ($match['id'] ?? 0);
        $homeId = (int) ($match['teams'][0]['team']['club']['id'] ?? ($match['teams'][0]['team']['id'] ?? 0));
        $awayId = (int) ($match['teams'][1]['team']['club']['id'] ?? ($match['teams'][1]['team']['id'] ?? 0));

        // Các CLB Big 6: Arsenal (1), Chelsea (11), Liverpool (10), Man City (11), Man Utd (12), Tottenham (21)
        $bigTeams = [1, 10, 11, 12, 14, 21];
        if (in_array($homeId, $bigTeams) || in_array($awayId, $bigTeams)) {
            $channels = ['K+ Sport 1', 'TV360', 'FPT Play'];
            return $channels[$id % count($channels)];
        }

        $allChannels = ['K+ Sport 1', 'FPT Play', 'TV360', 'VTVcab ON'];
        return $allChannels[$id % count($allChannels)];
    }

    /**
     * Sinh tiêu đề trang (Title) chuẩn SEO động theo yêu cầu Phase 6:
     * "Trực tiếp {Home} vs {Away} ({Giờ} hôm nay) - Tỷ số, Đội hình"
     */
    public static function generateTitle(array $match): string
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $status = $match['status'] ?? 'U';
        $kickoff = $match['kickoff']['millis'] ?? null;

        $gio = $kickoff ? PulseLiveService::formatKickoffVietnam($kickoff, 'H:i') : '20:00';
        $isToday = false;
        if ($kickoff) {
            $isToday = Carbon::createFromTimestampMs($kickoff, 'Asia/Ho_Chi_Minh')->isToday();
        }

        $timeLabel = $isToday ? "{$gio} hôm nay" : $gio;

        if ($status === 'L') {
            $homeScore = (int) ($match['teams'][0]['score'] ?? 0);
            $awayScore = (int) ($match['teams'][1]['score'] ?? 0);
            $clock = $match['clock']['label'] ?? 'LIVE';
            return "Trực tiếp {$home} vs {$away} ({$clock} {$homeScore}-{$awayScore}) - Tỷ số, Đội hình";
        }

        if ($status === 'C') {
            $homeScore = (int) ($match['teams'][0]['score'] ?? 0);
            $awayScore = (int) ($match['teams'][1]['score'] ?? 0);
            return "Trực tiếp {$home} vs {$away} (FT {$homeScore}-{$awayScore}) - Tỷ số, Đội hình";
        }

        return "Trực tiếp {$home} vs {$away} ({$timeLabel}) - Tỷ số, Đội hình";
    }

    /**
     * Sinh Meta Description chuẩn SEO chứa đầy đủ từ khóa theo yêu cầu Phase 6:
     * "Xem trực tiếp tỷ số trận {Home} vs {Away} lúc {Giờ} ngày {Ngày}. Cập nhật diễn biến, đội hình chính thức và kênh phát sóng trên {TV}."
     */
    public static function generateMetaDescription(array $match): string
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $kickoff = $match['kickoff']['millis'] ?? null;
        $tvChannel = self::getTvChannel($match);

        $gio = $kickoff ? PulseLiveService::formatKickoffVietnam($kickoff, 'H:i') : '20:00';
        $ngay = $kickoff ? PulseLiveService::formatKickoffVietnam($kickoff, 'd/m/Y') : 'hôm nay';

        return "Xem trực tiếp tỷ số trận {$home} vs {$away} lúc {$gio} ngày {$ngay}. Cập nhật diễn biến, đội hình chính thức và kênh phát sóng trên {$tvChannel}.";
    }

    /**
     * Tự động sinh JSON-LD Schema.org/SportsEvent chuẩn Google Rich Snippets
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
        $tvChannel = self::getTvChannel($match);

        // Xác định eventStatus theo chuẩn Schema.org
        $eventStatus = match ($status) {
            'L' => 'https://schema.org/EventLive',
            'C' => 'https://schema.org/EventCompleted',
            default => 'https://schema.org/EventScheduled',
        };

        $startDate = null;
        if (isset($match['kickoff']['millis'])) {
            $startDate = Carbon::createFromTimestamp((int) ($match['kickoff']['millis'] / 1000), 'Asia/Ho_Chi_Minh')
                ->toIso8601String();
        }

        $canonicalUrl = $currentUrl ?: route('match.detail', ['slug' => self::generateMatchSlug($match)]);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'SportsEvent',
            'name' => "Trực tiếp {$home} vs {$away} - Ngoại Hạng Anh",
            'description' => self::generateMetaDescription($match),
            'url' => $canonicalUrl,
            'sport' => 'Bóng đá',
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
            ],
            'publication' => [
                '@type' => 'BroadcastEvent',
                'isLiveBroadcast' => true,
                'videoFormat' => 'HD',
                'publishedOn' => [
                    '@type' => 'BroadcastService',
                    'name' => $tvChannel,
                    'broadcaster' => [
                        '@type' => 'Organization',
                        'name' => $tvChannel
                    ]
                ]
            ]
        ];

        if ($startDate) {
            $schema['startDate'] = $startDate;
        }

        return $schema;
    }

    /**
     * Sinh bộ OpenGraph và Twitter Card Metadata hoàn chỉnh
     */
    public static function generateSeoMetadata(array $match): array
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $homeId = $match['teams'][0]['team']['id'] ?? 1;
        $canonicalSlug = self::generateMatchSlug($match);
        $canonicalUrl = route('match.detail', ['slug' => $canonicalSlug]);
        $tvChannel = self::getTvChannel($match);

        $badgeImage = PulseLiveService::getTeamBadgeUrl($homeId, 100);

        return [
            'title' => self::generateTitle($match),
            'description' => self::generateMetaDescription($match),
            'canonical' => $canonicalUrl,
            'tv_channel' => $tvChannel,
            'og' => [
                'title' => self::generateTitle($match),
                'description' => self::generateMetaDescription($match),
                'url' => $canonicalUrl,
                'type' => 'website',
                'image' => $badgeImage,
                'site_name' => 'Premier League Live Tracker',
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => self::generateTitle($match),
                'description' => self::generateMetaDescription($match),
                'image' => $badgeImage,
            ],
            'schema' => self::generateSportsEventSchema($match, $canonicalUrl),
        ];
    }
}
