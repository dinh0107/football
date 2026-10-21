<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\PremierLeagueService;
use App\Models\MatchOverride;

/**
 * Class SeoHelper - Hệ thống Programmatic SEO tự động cho website TySoNgoaiHang.site
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
    public const SITE_NAME = 'TySoNgoaiHang';
    public const DOMAIN = 'TySoNgoaiHang.site';

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
     * Xác định Kênh phát sóng tại Việt Nam (TV Guide)
     * Ưu tiên cấu hình Admin ghi đè -> Phân bổ bản quyền K+ Sport 1, FPT Play, TV360, VTVcab ON
     */
    public static function getTvChannel(array $match): string
    {
        if (!empty($match['tv_channel'])) {
            return $match['tv_channel'];
        }

        $id = (int) ($match['id'] ?? 0);
        if ($id > 0) {
            try {
                $override = MatchOverride::where('match_id', $id)->value('tv_channel');
                if ($override) {
                    return $override;
                }
            } catch (\Throwable $e) {
                // Fallback nếu database chưa kết nối
            }
        }

        $homeId = (int) ($match['teams'][0]['team']['club']['id'] ?? ($match['teams'][0]['team']['id'] ?? 0));
        $awayId = (int) ($match['teams'][1]['team']['club']['id'] ?? ($match['teams'][1]['team']['id'] ?? 0));

        // Big 6 Premier League: Arsenal, Chelsea, Liverpool, Man City, Man Utd, Tottenham
        $bigTeams = [1, 10, 11, 12, 14, 21];
        if (in_array($homeId, $bigTeams) || in_array($awayId, $bigTeams)) {
            $channels = ['K+ Sport 1', 'TV360', 'FPT Play'];
            return $channels[$id % count($channels)];
        }

        $allChannels = ['K+ Sport 1', 'FPT Play', 'TV360', 'VTVcab ON'];
        return $allChannels[$id % count($allChannels)];
    }

    /**
     * Sinh tiêu đề (Title) chuẩn SEO tự động theo trạng thái trận đấu:
     * - Trước trận: "Trực tiếp {Home} vs {Away} ({Giờ} hôm nay) - Kênh chiếu, Tỷ số, Đội hình | TySoNgoaiHang"
     * - Đang đá: "🔴 [LIVE {Phút}'] {Home} {ScoreHome} - {ScoreAway} {Away} - Tỷ số, Đội hình | Ngoại Hạng Anh"
     * - Sau trận: "Trực tiếp {Home} vs {Away} - Kết quả ({ScoreHome} - {ScoreAway}): Tóm tắt diễn biến, Tỷ số, Đội hình | TySoNgoaiHang"
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
            return "[LIVE {$clock}] {$home} {$homeScore} - {$awayScore} {$away} - Tỷ số, Đội hình | Ngoại Hạng Anh";
        }

        if ($status === 'C') {
            // Đã kết thúc (Full Time)
            return "Trực tiếp {$home} vs {$away} (FT {$homeScore}-{$awayScore}) - Tỷ số, Đội hình | Kết quả mới nhất";
        }

        // Chưa diễn ra (Upcoming)
        return "Trực tiếp {$home} vs {$away} ({$timeLabel}) - Kênh chiếu, Tỷ số, Đội hình | " . static::SITE_NAME;
    }

    /**
     * Sinh thẻ Meta Description (150-160 ký tự) tối ưu từ khóa Google:
     * Chứa: "trực tiếp tỷ số", "{Home} vs {Away}", "giờ", "ngày", "vòng", "đội hình ra sân", "kênh phát sóng"
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

        return "Xem trực tiếp tỷ số trận {$home} vs {$away} lúc {$gio} ngày {$ngay} Vòng {$gw} Ngoại Hạng Anh. Cập nhật đội hình ra sân, thống kê và kênh phát sóng trên {$tv} chính xác nhất.";
    }

    /**
     * Tự động sinh đoạn văn bản SEO (3-4 câu hoàn chỉnh) cho bot tìm kiếm:
     * Cung cấp text content tự nhiên, chuẩn ngữ pháp tiếng Việt, tích hợp đa dạng từ khóa tìm kiếm.
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

        $p1 = "Trận đấu tâm điểm giữa {$home} và {$away} thuộc khuôn khổ Vòng {$gw} giải bóng đá Ngoại Hạng Anh (English Premier League) diễn ra vào lúc {$gio} ngày {$ngay} trên {$stadium} ({$city}).";

        if ($status === 'C') {
            $homeScore = (int) ($match['teams'][0]['score'] ?? 0);
            $awayScore = (int) ($match['teams'][1]['score'] ?? 0);
            $p2 = "Trận cầu đã chính thức khép lại với tỷ số chung cuộc {$home} {$homeScore} - {$awayScore} {$away}.";
            $p3 = "Người hâm mộ có thể xem lại tóm tắt các pha ghi bàn, danh sách thẻ phạt, đội hình ra sân chính thức và toàn bộ thống kê kỹ thuật chi tiết tại " . static::DOMAIN . ".";
            $p4 = "Kết quả này tác động trực tiếp đến vị trí của cả hai câu lạc bộ trên bảng xếp hạng Ngoại Hạng Anh mùa giải hiện tại.";
            return "{$p1} {$p2} {$p3} {$p4}";
        }

        if ($status === 'L') {
            $clock = $match['clock']['label'] ?? 'Đang diễn ra';
            $homeScore = (int) ($match['teams'][0]['score'] ?? 0);
            $awayScore = (int) ($match['teams'][1]['score'] ?? 0);
            $p2 = "Hiện tại màn so tài đang diễn ra với nhịp độ nghẹt thở ({$clock}), tỷ số tạm thời là {$home} {$homeScore} - {$awayScore} {$away}.";
            $p3 = "Quý khán giả có thể theo dõi trực tiếp trận đấu qua sóng truyền hình {$tv} hoặc cập nhật diễn biến, đội hình và tỷ số trực tuyến từng phút tại " . static::DOMAIN . ".";
            $p4 = "Cả hai đội bóng đang thi đấu đầy quyết tâm nhằm giành trọn 3 điểm số quan trọng cho cuộc đua thứ hạng.";
            return "{$p1} {$p2} {$p3} {$p4}";
        }

        $p2 = "Khán giả yêu bóng đá có thể đón xem trực tiếp màn so tài kịch tính này qua kênh truyền hình {$tv} hoặc theo dõi cập nhật tỷ số trực tuyến, sơ đồ chiến thuật 2D và đội hình ra sân tại " . static::DOMAIN . ".";
        $p3 = "Cả {$home} và {$away} đều đang có sự chuẩn bị kỹ lưỡng về mặt nhân sự với quyết tâm cao nhất để giành trọn vẹn 3 điểm.";
        $p4 = "Mọi diễn biến mới nhất, phong độ đối đầu và nhận định chuyên sâu sẽ được cập nhật liên tục trước giờ bóng lăn.";

        return "{$p1} {$p2} {$p3} {$p4}";
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
        $channel = static::getTvChannel($match);
        $status = $match['status'] ?? 'U';

        $eventStatus = match ($status) {
            'L' => 'https://schema.org/EventLive',
            'C' => 'https://schema.org/EventCompleted',
            'P' => 'https://schema.org/EventPostponed',
            default => 'https://schema.org/EventScheduled',
        };

        $kickoff = $match['kickoff']['millis'] ?? null;
        $startDate = $kickoff
            ? Carbon::createFromTimestampMs($kickoff, 'Asia/Ho_Chi_Minh')->toIso8601String()
            : Carbon::now('Asia/Ho_Chi_Minh')->toIso8601String();

        $canonicalSlug = static::generateMatchSlug($match);
        $canonicalUrl = route('match.detail', ['slug' => $canonicalSlug]);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'SportsEvent',
            'name' => "Trực tiếp {$home} vs {$away}",
            'description' => static::generateMetaDescription($match),
            'startDate' => $startDate,
            'eventStatus' => $eventStatus,
            'url' => $canonicalUrl,
            'sport' => 'Football',
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
                'logo' => PremierLeagueService::getTeamBadgeUrl($homeId, 100),
            ],
            'awayTeam' => [
                '@type' => 'SportsTeam',
                'name' => $away,
                'logo' => PremierLeagueService::getTeamBadgeUrl($awayId, 100),
            ],
            'broadcast' => [
                '@type' => 'BroadcastEvent',
                'name' => "Phát sóng trực tiếp {$home} vs {$away}",
                'broadcaster' => [
                    '@type' => 'Organization',
                    'name' => $channel,
                ],
                'isLiveBroadcast' => true,
                'videoFormat' => 'HD',
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
