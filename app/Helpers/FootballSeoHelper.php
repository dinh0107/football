<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Helper chuyên trách xử lý SEO, URL Slug, TV Guide và Schema.org/SportsEvent cho trận đấu
 */
class FootballSeoHelper
{
    /**
     * Slug canonical duy nhất, phải khớp sitemap và redirect 301 trong MatchController
     * Ví dụ: truc-tiep-arsenal-vs-chelsea-vong-10-93452
     */
    public static function generateMatchSlug(array $match): string
    {
        return SeoHelper::generateMatchSlug($match);
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
     * Kênh phát sóng do Admin nhập; chuỗi rỗng nếu chưa có
     */
    public static function getTvChannel(array $match): string
    {
        return SeoHelper::getTvChannel($match);
    }

    /**
     * Sinh tiêu đề trang (Title) chuẩn SEO động:
     * "Trực tiếp {Home} vs {Away} ({Giờ} hôm nay | {Giờ} ngày {d/m}) - Tỷ số, Đội hình"
     */
    public static function generateTitle(array $match): string
    {
        $home = $match['teams'][0]['team']['name'] ?? 'Đội nhà';
        $away = $match['teams'][1]['team']['name'] ?? 'Đội khách';
        $status = $match['status'] ?? 'U';
        $kickoff = $match['kickoff']['millis'] ?? null;

        $timeLabel = '20:00';
        if ($kickoff) {
            $kickoffVn = Carbon::createFromTimestampMs($kickoff, 'Asia/Ho_Chi_Minh');
            $timeLabel = $kickoffVn->isToday()
                ? $kickoffVn->format('H:i') . ' hôm nay'
                : $kickoffVn->format('H:i') . ' ngày ' . $kickoffVn->format('d/m');
        }

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
     * Sinh bộ OpenGraph và Twitter Card Metadata hoàn chỉnh
     */
    public static function generateSeoMetadata(array $match): array
    {
        $canonicalUrl = route('match.detail', ['slug' => self::generateMatchSlug($match)]);
        $title = self::generateTitle($match);
        $description = SeoHelper::generateMetaDescription($match);
        $ogImage = asset('images/og-default.png');

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonicalUrl,
            'tv_channel' => self::getTvChannel($match),
            'og' => [
                'title' => $title,
                'description' => $description,
                'url' => $canonicalUrl,
                'type' => 'website',
                'image' => $ogImage,
                'site_name' => 'Tỷ Số Ngoại Hạng Anh',
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'title' => $title,
                'description' => $description,
                'image' => $ogImage,
            ],
            'schema' => SeoHelper::generateSchemaOrg($match),
            'breadcrumbs_schema' => SeoHelper::generateBreadcrumbSchema($match),
        ];
    }

    /**
     * Tự động sinh đoạn văn bản SEO chuẩn ngữ pháp tiếng Việt
     */
    public static function generateMatchSeoParagraph(array $match): string
    {
        return SeoHelper::generateMatchSeoParagraph($match);
    }

    /**
     * Sinh Schema.org BreadcrumbList chuẩn Google Mobile Search
     */
    public static function generateBreadcrumbSchema(array $match): array
    {
        return SeoHelper::generateBreadcrumbSchema($match);
    }
}
