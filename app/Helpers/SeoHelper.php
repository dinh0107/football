<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\PremierLeagueService;
use App\Models\MatchOverride;

/**
 * Class SeoHelper - Hệ thống Programmatic SEO tự động cho website tysongoaihang.site
 *
 * Chuyên trách:
 * 1. Tự động sinh URL Slug chuẩn SEO có tiền tố truc-tiep và vòng đấu.
 * 2. Tự động sinh Title, Meta Description tối ưu từ khóa tìm kiếm (Long-tail keywords).
 * 3. Tự động tạo đoạn văn bản SEO (Seo Paragraph) chuẩn ngữ pháp tiếng Việt cho bot công cụ tìm kiếm.
 * 4. Phát sinh Schema.org SportsEvent & BroadcastEvent phục vụ Google Rich Results.
 */
class SeoHelper
{
    /**
     * Tên thương hiệu mặc định phục vụ branding SEO
     */
    public const SITE_NAME = 'tysongoaihang';
    public const DOMAIN = 'tysongoaihang.site';

    /**
     * Hàm sinh URL Slug tiếng Việt không dấu chuẩn SEO:
     * Định dạng: truc-tiep-[home]-vs-[away]-vong-[round]-[id]
     *
     * Hỗ trợ 2 kiểu gọi:
     * - generateMatchSlug($homeTeam, $awayTeam, $round, $matchId)
     * - generateMatchSlug($matchArray)
     *
     * @param mixed $homeTeam Hoặc mảng chi tiết trận đấu $match
     * @param string|null $awayTeam
     * @param int|string|null $round
     * @param int|null $matchId
     * @param bool $withPrefix Có gắn tiền tố 'truc-tiep-' hay không (mặc định true)
     * @return string
     */
    public static function generateMatchSlug(
        mixed $homeTeam,
        ?string $awayTeam = null,
        mixed $round = null,
        ?int $matchId = null,
        bool $withPrefix = true
    ): string {
        // Trường hợp truyền vào mảng $match
        if (is_array($homeTeam)) {
            $match = $homeTeam;
            $home = $match['teams'][0]['team']['name'] ?? 'doi-nha';
            $away = $match['teams'][1]['team']['name'] ?? 'doi-khach';
            $gw = $match['gameweek']['gameweek'] ?? ($match['gameweek']['id'] ?? null);
            $id = (int) ($match['id'] ?? 0);
            return static::generateMatchSlug($home, $away, $gw, $id, $withPrefix);
        }

        $homeSlug = Str::slug((string) $homeTeam);
        $awaySlug = Str::slug((string) $awayTeam);
        $roundText = $round ? 'vong-' . Str::slug((string) $round) : 'vong-1';
        $id = (int) $matchId;

        $prefix = $withPrefix ? 'truc-tiep-' : '';
        return "{$prefix}{$homeSlug}-vs-{$awaySlug}-{$roundText}-{$id}";
    }

    /**
     * Trích xuất Match ID từ cuối URL slug
     * Ví dụ:
     * 'truc-tiep-arsenal-vs-chelsea-vong-10-93452' -> 93452
     * 'arsenal-vs-chelsea-vong-10-93452' -> 93452
     * '93452' -> 93452
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
     * Kênh phát sóng tại Việt Nam do Admin nhập; chuỗi rỗng nếu chưa có (không đoán kênh)
     */
    public static function getTvChannel(array $match): string
    {
        if (!empty($match['tv_channel'])) {
            return $match['tv_channel'];
        }

        $id = (int) ($match['id'] ?? 0);
        if ($id > 0) {
            try {
                return (string) MatchOverride::where('match_id', $id)->value('tv_channel');
            } catch (\Throwable $e) {
                // Fallback nếu database chưa kết nối
            }
        }

        return '';
    }

    /**
     * Sinh tiêu đề (Title) chuẩn SEO tự động theo trạng thái trận đấu:
     * - Trước trận: "Trực tiếp {Home} vs {Away} ({Giờ} hôm nay) - Kênh chiếu, Tỷ số, Đội hình | tysongoaihang"
     * - Đang đá: "🔴 [LIVE {Phút}'] {Home} {ScoreHome} - {ScoreAway} {Away} - Tỷ số, Đội hình | Ngoại Hạng Anh"
     * - Sau trận: "Trực tiếp {Home} vs {Away} - Kết quả ({ScoreHome} - {ScoreAway}): Tóm tắt diễn biến, Tỷ số, Đội hình | tysongoaihang"
     */
    public static function generateMatchTitle(array $match): string
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $status = $match['status'] ?? 'U';
        $homeScore = (int) ($match['teams'][0]['score'] ?? 0);
        $awayScore = (int) ($match['teams'][1]['score'] ?? 0);
        $kickoff = $match['kickoff']['millis'] ?? null;

        $gio = $kickoff ? PremierLeagueService::formatKickoffVietnam($kickoff, 'H:i') : '20:00';
        $isToday = false;
        if ($kickoff) {
            $isToday = Carbon::createFromTimestampMs($kickoff, 'Asia/Ho_Chi_Minh')->isToday();
        }
        $timeLabel = $isToday ? "{$gio} hôm nay" : $gio;

        if ($status === 'L') {
            // Đang diễn ra (Live)
            $clock = $match['clock']['label'] ?? 'Đang đá';
            return "[LIVE {$clock}] {$home} {$homeScore} - {$awayScore} {$away} - Tỷ số, Đội hình";
        }

        if ($status === 'C') {
            // Đã kết thúc (Full Time)
            return "Trực tiếp {$home} vs {$away} (FT {$homeScore}-{$awayScore}) - Tỷ số, Đội hình";
        }

        // Chưa diễn ra (Upcoming)
        return "Trực tiếp {$home} vs {$away} ({$timeLabel}) - Kênh chiếu, Tỷ số, Đội hình";
    }

    /**
     * Sinh thẻ Meta Description theo trạng thái trận, chỉ nhắc kênh phát sóng khi Admin đã nhập
     */
    public static function generateMetaDescription(array $match): string
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $gw = $match['gameweek']['gameweek'] ?? ($match['gameweek']['id'] ?? '1');
        $kickoff = $match['kickoff']['millis'] ?? null;

        $gio = $kickoff ? PremierLeagueService::formatKickoffVietnam($kickoff, 'H:i') : '20:00';
        $ngay = $kickoff ? PremierLeagueService::formatKickoffVietnam($kickoff, 'd/m/Y') : Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y');
        $tv = static::getTvChannel($match);

        if (($match['status'] ?? 'U') === 'C') {
            $homeScore = (int) ($match['teams'][0]['score'] ?? 0);
            $awayScore = (int) ($match['teams'][1]['score'] ?? 0);
            return "Kết quả {$home} {$homeScore}-{$awayScore} {$away} Vòng {$gw} Ngoại Hạng Anh ngày {$ngay}. Xem lại diễn biến, bàn thắng, thẻ phạt, đội hình ra sân và thống kê trận đấu.";
        }

        $tvText = $tv ? " Kênh phát sóng trên {$tv}." : '';
        return "Xem trực tiếp tỷ số trận {$home} vs {$away} lúc {$gio} ngày {$ngay} Vòng {$gw} Ngoại Hạng Anh. Cập nhật diễn biến, đội hình ra sân và thống kê trận đấu.{$tvText}";
    }

    /**
     * Đoạn văn mô tả trận đấu, chỉ dùng dữ kiện có thật từ API (không hứa hẹn nội dung site không có)
     */
    public static function generateMatchSeoParagraph(array $match): string
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $gw = $match['gameweek']['gameweek'] ?? ($match['gameweek']['id'] ?? '1');
        $stadium = $match['ground']['name'] ?? 'sân vận động Premier League';
        $city = $match['ground']['city'] ?? 'Vương quốc Anh';
        $kickoff = $match['kickoff']['millis'] ?? null;

        $gio = $kickoff ? PremierLeagueService::formatKickoffVietnam($kickoff, 'H:i') : '20:00';
        $ngay = $kickoff ? PremierLeagueService::formatKickoffVietnam($kickoff, 'd/m/Y') : Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y');
        $tv = static::getTvChannel($match);
        $status = $match['status'] ?? 'U';
        $homeScore = (int) ($match['teams'][0]['score'] ?? 0);
        $awayScore = (int) ($match['teams'][1]['score'] ?? 0);

        $sentences = ["{$home} tiếp đón {$away} tại {$stadium} ({$city}) ở Vòng {$gw} Ngoại Hạng Anh, lúc {$gio} ngày {$ngay} (giờ Việt Nam)."];

        if ($status === 'C') {
            $sentences[] = "Trận đấu đã kết thúc với tỷ số {$home} {$homeScore} - {$awayScore} {$away}.";
            if (!empty($match['attendance'])) {
                $sentences[] = 'Có ' . number_format((int) $match['attendance'], 0, ',', '.') . ' khán giả theo dõi trên sân.';
            }
            $sentences[] = 'Diễn biến bàn thắng, thẻ phạt, thay người, đội hình ra sân và thống kê của trận được lưu đầy đủ tại ' . static::DOMAIN . '.';
        } elseif ($status === 'L') {
            $clock = $match['clock']['label'] ?? 'đang diễn ra';
            $sentences[] = "Trận đấu đang diễn ra ({$clock}), tỷ số hiện tại là {$home} {$homeScore} - {$awayScore} {$away}.";
            $sentences[] = 'Tỷ số, bàn thắng, thẻ phạt và thay người được cập nhật tự động tại ' . static::DOMAIN . '.';
        } else {
            $sentences[] = 'Đội hình ra sân chính thức thường được công bố khoảng 60 phút trước giờ bóng lăn và sẽ hiển thị trên sa bàn 2D cùng tỷ số trực tuyến tại ' . static::DOMAIN . '.';
        }

        if ($tv && $status !== 'C') {
            $sentences[] = "Trận đấu được phát sóng trực tiếp tại Việt Nam trên {$tv}.";
        }

        return implode(' ', $sentences);
    }

    /**
     * Sinh Schema.org chuẩn Google SportsEvent & BroadcastEvent
     */
    public static function generateSchemaOrg(array $match): array
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Home Team';
        $away = $match['teams'][1]['team']['name'] ?? 'Away Team';
        $homeId = (int) ($match['teams'][0]['team']['club']['id'] ?? ($match['teams'][0]['team']['id'] ?? 1));
        $awayId = (int) ($match['teams'][1]['team']['club']['id'] ?? ($match['teams'][1]['team']['id'] ?? 2));
        $stadium = $match['ground']['name'] ?? 'Premier League Stadium';
        $city = $match['ground']['city'] ?? 'England';
        $status = $match['status'] ?? 'U';

        $eventStatus = match ($status) {
            'L' => 'https://schema.org/EventLive',
            'C' => 'https://schema.org/EventCompleted',
            'P' => 'https://schema.org/EventPostponed',
            default => 'https://schema.org/EventScheduled',
        };

        $kickoff = $match['kickoff']['millis'] ?? null;
        $startCarbon = $kickoff
            ? Carbon::createFromTimestampMs($kickoff, 'Asia/Ho_Chi_Minh')
            : Carbon::now('Asia/Ho_Chi_Minh');
        $startDate = $startCarbon->toIso8601String();
        $endDate = $startCarbon->copy()->addMinutes(115)->toIso8601String();

        $canonicalSlug = static::generateMatchSlug($match);
        $canonicalUrl = route('match.detail', ['slug' => $canonicalSlug]);
        $homeLogo = PremierLeagueService::getTeamBadgeUrl($homeId, 100);
        $awayLogo = PremierLeagueService::getTeamBadgeUrl($awayId, 100);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'SportsEvent',
            'name' => "Trực tiếp {$home} vs {$away}",
            'description' => static::generateMetaDescription($match),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'eventStatus' => $eventStatus,
            'url' => $canonicalUrl,
            'sport' => 'Football',
            'image' => [asset('images/og-default.png')],
            'organizer' => [
                '@type' => 'Organization',
                'name' => 'Premier League',
                'url' => 'https://www.premierleague.com',
            ],
            'location' => [
                '@type' => 'Place',
                'name' => $stadium,
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $city,
                    'addressCountry' => 'GB',
                ],
            ],
            'homeTeam' => [
                '@type' => 'SportsTeam',
                'name' => $home,
                'logo' => $homeLogo,
            ],
            'awayTeam' => [
                '@type' => 'SportsTeam',
                'name' => $away,
                'logo' => $awayLogo,
            ],
            'performer' => [
                [
                    '@type' => 'SportsTeam',
                    'name' => $home,
                    'logo' => $homeLogo,
                ],
                [
                    '@type' => 'SportsTeam',
                    'name' => $away,
                    'logo' => $awayLogo,
                ],
            ],
        ];
    }

    /**
     * Đóng gói toàn bộ SEO metadata cho view
     */
    public static function generateSeoMetadata(array $match): array
    {
        $canonicalSlug = static::generateMatchSlug($match);
        $canonicalUrl = route('match.detail', ['slug' => $canonicalSlug]);
        $homeId = (int) ($match['teams'][0]['team']['club']['id'] ?? ($match['teams'][0]['team']['id'] ?? 1));

        return [
            'title' => static::generateMatchTitle($match),
            'description' => static::generateMetaDescription($match),
            'canonical' => $canonicalUrl,
            'type' => 'article',
            'og' => [
                'type' => 'article',
                'title' => static::generateMatchTitle($match),
                'description' => static::generateMetaDescription($match),
                'url' => $canonicalUrl,
                'image' => PremierLeagueService::getTeamBadgeUrl($homeId, 100),
                'site_name' => static::SITE_NAME,
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => static::generateMatchTitle($match),
                'description' => static::generateMetaDescription($match),
                'image' => PremierLeagueService::getTeamBadgeUrl($homeId, 100),
            ],
            'schema' => static::generateSchemaOrg($match),
            'breadcrumbs_schema' => static::generateBreadcrumbSchema($match),
        ];
    }

    /**
     * Sinh Schema.org BreadcrumbList chuẩn Google Mobile Search
     * Phân tầng: Trang Chủ -> Lịch Thi Đấu Ngoại Hạng Anh -> Vòng [Số] -> [Đội Nhà] vs [Đội Khách]
     */
    public static function generateBreadcrumbSchema(array $match): array
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $gw = $match['gameweek']['gameweek'] ?? ($match['gameweek']['id'] ?? '1');
        $gwId = $match['gameweek']['id'] ?? $gw;
        $canonicalSlug = static::generateMatchSlug($match);
        $canonicalUrl = route('match.detail', ['slug' => $canonicalSlug]);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Trang Chủ',
                    'item' => url('/'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Lịch Thi Đấu Ngoại Hạng Anh',
                    'item' => url('/lich-thi-dau'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => "Vòng {$gw}",
                    'item' => url('/lich-thi-dau?vong=' . $gwId),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 4,
                    'name' => "{$home} vs {$away}",
                    'item' => $canonicalUrl,
                ],
            ],
        ];
    }
}
