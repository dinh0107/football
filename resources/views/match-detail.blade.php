<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $seo['title'] ?? 'Chi Tiết Trận Đấu' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Xem trực tiếp diễn biến trận đấu, đội hình ra sân 2D và thống kê chi tiết giải Ngoại Hạng Anh.' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">
    <meta name="robots" content="index, follow">

    {{-- Favicon & Mobile App Icons --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Open Graph / Facebook & Zalo Meta Tags --}}
    <meta property="og:type" content="{{ $seo['og']['type'] ?? 'website' }}">
    <meta property="og:title" content="{{ $seo['og']['title'] ?? ($seo['title'] ?? '') }}">
    <meta property="og:description" content="{{ $seo['og']['description'] ?? ($seo['description'] ?? '') }}">
    <meta property="og:url" content="{{ $seo['og']['url'] ?? url()->current() }}">
    <meta property="og:site_name" content="Tỷ Số Ngoại Hạng Anh">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:image" content="{{ $seo['og']['image'] ?? asset('images/logo.png') }}">
    <meta property="og:image:secure_url" content="{{ $seo['og']['image'] ?? asset('images/logo.png') }}">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="{{ $seo['og']['title'] ?? ($seo['title'] ?? '') }}">

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="{{ $seo['twitter']['card'] ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $seo['twitter']['title'] ?? ($seo['title'] ?? '') }}">
    <meta name="twitter:description" content="{{ $seo['twitter']['description'] ?? ($seo['description'] ?? '') }}">
    <meta name="twitter:image" content="{{ $seo['twitter']['image'] ?? ($seo['og']['image'] ?? '') }}">

    {{-- Schema.org / SportsEvent JSON-LD --}}
    @if(!empty($seo['schema']))
    <script type="application/ld+json">
    {!! json_encode($seo['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endif

    {{-- Schema.org / BreadcrumbList JSON-LD cho Google Mobile --}}
    @if(!empty($seo['breadcrumbs_schema']))
    <script type="application/ld+json">
    {!! json_encode($seo['breadcrumbs_schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endif

    {{-- Google Font & CDN Preconnect --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://resources.premierleague.com" crossorigin>
    <link rel="dns-prefetch" href="https://resources.premierleague.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- CSS Giao Diện Match Center --}}
    <link rel="stylesheet" href="{{ asset('css/match-detail.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    {{-- Top Header --}}
    <header class="site-header">
        <div class="container header-content">
            <a href="{{ route('home') }}" class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo tysongoaihang" class="brand-logo-img" width="38" height="38" onerror="this.onerror=null; this.src='/images/logo.png';">
                <div class="brand-text">PREMIER<span>LEAGUE</span></div>
            </a>
            <nav class="header-nav">
                <a href="{{ route('home') }}" class="header-link">Lịch Thi Đấu</a>
                <a href="{{ route('standings') }}" class="header-link">Bảng Xếp Hạng</a>
                <a href="{{ route('live') }}" class="header-link">Trực Tiếp</a>
            </nav>
        </div>
    </header>

    @php
        $homeTeam = $match['teams'][0]['team'] ?? [];
        $awayTeam = $match['teams'][1]['team'] ?? [];
        $homeScore = $match['teams'][0]['score'] ?? null;
        $awayScore = $match['teams'][1]['score'] ?? null;
        $homeId = (int) ($homeTeam['club']['id'] ?? ($homeTeam['id'] ?? 0));
        $awayId = (int) ($awayTeam['club']['id'] ?? ($awayTeam['id'] ?? 0));
        $status = $match['status'] ?? 'U';
        $ground = $match['ground'] ?? [];
        $clock = $match['clock']['label'] ?? '00\'';
        $kickoff = $match['kickoff']['millis'] ?? null;

        // Trích xuất danh sách bàn thắng của từng đội để hiển thị trực tiếp trên Hero Scoreboard
        $homeGoals = array_filter($events ?? [], fn($e) => !empty($e['isHome']) && in_array($e['type'] ?? '', ['G', 'OG', 'P']));
        $awayGoals = array_filter($events ?? [], fn($e) => empty($e['isHome']) && in_array($e['type'] ?? '', ['G', 'OG', 'P']));
    @endphp

    {{-- Main Content --}}
    <main class="main-content">
        <div class="container">
            {{-- PHASE 8: Banner Quảng Cáo Đầu Trang (banner_top) --}}
            @php
                $bannerTop = \App\Models\AdBanner::getActiveBanner('banner_top');
                $bannerBelowScore = \App\Models\AdBanner::getActiveBanner('banner_below_score');
                $bannerFooter = \App\Models\AdBanner::getActiveBanner('banner_footer');
            @endphp
            @if($bannerTop && $bannerTop->rendered_html)
                <div class="ad-banner-slot banner-top" style="margin-bottom: 16px;">
                    {!! $bannerTop->rendered_html !!}
                </div>
            @endif

            {{-- Breadcrumb Điều Hướng Chuẩn SEO (Trang chủ -> Lịch thi đấu -> Vòng đấu -> Trận đấu) --}}
            <nav class="breadcrumb-nav" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    Trang chủ
                </a>
                <span class="breadcrumb-sep">/</span>
                <a href="{{ route('fixtures') }}">Lịch thi đấu</a>
                <span class="breadcrumb-sep">/</span>
                <a href="{{ route('fixtures', ['vong' => $match['gameweek']['id'] ?? ($match['gameweek']['gameweek'] ?? 1)]) }}">
                    Vòng {{ $match['gameweek']['gameweek'] ?? 1 }}
                </a>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current">{{ $homeTeam['name'] ?? 'Đội nhà' }} vs {{ $awayTeam['name'] ?? 'Đội khách' }}</span>
            </nav>

            {{-- Match Header Hero (Stadium Night Edition) --}}
            <div class="match-hero-card"
                 data-fixture-id="{{ (int)$match['id'] }}"
                 data-home-score="{{ $homeScore ?? 0 }}"
                 data-away-score="{{ $awayScore ?? 0 }}"
                 data-home-name="{{ $homeTeam['name'] ?? 'Đội nhà' }}"
                 data-away-name="{{ $awayTeam['name'] ?? 'Đội khách' }}"
                 data-status="{{ $status }}">
                
                {{-- Thanh giải đấu & Kênh TV phát sóng --}}
                <div class="hero-competition-bar">
                    <span class="hero-competition-tag">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        Premier League &bull; Vòng {{ $match['gameweek']['gameweek'] ?? 1 }}
                    </span>
                    <span class="hero-tv-guide-bar">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
                        Trực tiếp trên: <strong>{{ $tvChannel ?? ($match['tv_channel'] ?? 'K+ Sport 1') }}</strong>
                    </span>
                </div>

                {{-- Tiêu đề H1 Duy Nhất Chuẩn SEO On-Page --}}
                <h1 class="match-hero-h1-title">
                    Trực tiếp {{ $homeTeam['name'] ?? 'Đội nhà' }} vs {{ $awayTeam['name'] ?? 'Đội khách' }} - Vòng {{ $match['gameweek']['gameweek'] ?? 1 }} Ngoại Hạng Anh
                </h1>

                {{-- Tiêu đề H2 Kênh phát sóng & Giờ thi đấu --}}
                <h2 class="seo-section-h2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Kênh phát sóng trực tiếp và Thời gian thi đấu
                </h2>

                {{-- Khối Văn Bản SEO Tự Động (Programmatic SEO Content) --}}
                @if(!empty($seoParagraph))
                <div class="seo-intro-card">
                    <div class="seo-intro-header">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        Tổng quan trước trận đấu
                    </div>
                    <p class="seo-intro-text">{{ $seoParagraph }}</p>
                </div>
                @endif

                {{-- Bảng Tỷ Số Chính --}}
                <div class="hero-scoreboard">
                    {{-- Đội Nhà --}}
                    <div class="hero-team">
                        <div class="hero-crest-wrap">
                            <img src="{{ \App\Services\PremierLeagueService::getTeamBadgeUrl($homeId, 100) }}" 
                                 alt="{{ $homeTeam['name'] ?? '' }}" 
                                 class="hero-team-logo"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/100/t1.png';">
                        </div>
                        <div class="hero-team-name">{{ $homeTeam['name'] ?? 'Đội nhà' }}</div>

                        {{-- Cầu thủ ghi bàn Đội Nhà --}}
                        @if(!empty($homeGoals))
                            <div class="hero-goalscorers home-scorers">
                                @foreach($homeGoals as $g)
                                    <span class="hero-goal-item">
                                        <span class="ball-icon">
                                            <svg class="svg-icon-inline" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <circle cx="12" cy="12" r="4"></circle>
                                            </svg>
                                        </span>
                                        <strong>{{ $g['playerName'] }}</strong>
                                        <span class="goal-minute">{{ $g['clock'] }}</span>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Tỷ số ở giữa --}}
                    <div class="hero-score-center">
                        @if($status === 'L')
                            <span class="hero-status-pill pill-live">
                                <span class="live-dot-pulse"></span>
                                <span data-fixture-clock>{{ $clock }}</span> &bull; TRỰC TIẾP
                            </span>
                            <div class="hero-score-display" data-score-display>
                                <span data-score-home>{{ $homeScore ?? 0 }}</span> - <span data-score-away>{{ $awayScore ?? 0 }}</span>
                            </div>
                        @elseif($status === 'C')
                            <span class="hero-status-pill pill-ft">HẾT GIỜ (FT)</span>
                            <div class="hero-score-display" data-score-display>
                                <span data-score-home>{{ $homeScore ?? 0 }}</span> - <span data-score-away>{{ $awayScore ?? 0 }}</span>
                            </div>
                        @else
                            <span class="hero-status-pill pill-upcoming">SẮP DIỄN RA</span>
                            <div class="hero-score-vs" data-score-display>
                                {{ $kickoff ? \App\Services\PremierLeagueService::formatKickoffVietnam($kickoff, 'H:i') : 'VS' }}
                            </div>
                            <span style="font-size: 0.8rem; color: rgba(255,255,255,0.7); font-weight: 600;">
                                {{ $kickoff ? \App\Services\PremierLeagueService::formatKickoffVietnam($kickoff, 'd/m/Y') : '' }}
                            </span>
                        @endif
                    </div>

                    {{-- Đội Khách --}}
                    <div class="hero-team">
                        <div class="hero-crest-wrap">
                            <img src="{{ \App\Services\PremierLeagueService::getTeamBadgeUrl($awayId, 100) }}" 
                                 alt="{{ $awayTeam['name'] ?? '' }}" 
                                 class="hero-team-logo"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/100/t1.png';">
                        </div>
                        <div class="hero-team-name">{{ $awayTeam['name'] ?? 'Đội khách' }}</div>

                        {{-- Cầu thủ ghi bàn Đội Khách --}}
                        @if(!empty($awayGoals))
                            <div class="hero-goalscorers away-scorers">
                                @foreach($awayGoals as $g)
                                    <span class="hero-goal-item">
                                        <span class="ball-icon">
                                            <svg class="svg-icon-inline" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <circle cx="12" cy="12" r="4"></circle>
                                            </svg>
                                        </span>
                                        <strong>{{ $g['playerName'] }}</strong>
                                        <span class="goal-minute">{{ $g['clock'] }}</span>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Thông tin sân vận động & trọng tài --}}
                <div class="hero-info-footer">
                    <span class="hero-info-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        Sân: <strong>{{ $ground['name'] ?? 'Chưa rõ' }} ({{ $ground['city'] ?? 'UK' }})</strong>
                    </span>
                    @if(!empty($match['matchOfficials']))
                        <span class="hero-info-pill">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                            Trọng tài: <strong>{{ $match['matchOfficials'][0]['name']['display'] ?? 'Premier League' }}</strong>
                        </span>
                    @endif
                    <span class="hero-info-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Thời gian: <strong>{{ $kickoff ? \App\Services\PremierLeagueService::formatKickoffVietnam($kickoff, 'H:i - d/m/Y') : '' }}</strong> (GMT+7)
                    </span>
                    <span class="hero-info-pill">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
                        Kênh TV: <strong style="color: var(--pl-mint);">{{ $tvChannel ?? ($match['tv_channel'] ?? 'K+ Sport 1') }}</strong>
                    </span>
                </div>
            </div>

            {{-- PHASE 8: Banner Quảng Cáo Dưới Tỷ Số (banner_below_score) --}}
            @if(isset($bannerBelowScore) && $bannerBelowScore && $bannerBelowScore->rendered_html)
                <div class="ad-banner-slot banner-below-score" style="margin-top: 18px; margin-bottom: 22px;">
                    {!! $bannerBelowScore->rendered_html !!}
                </div>
            @endif

            {{-- Hệ thống Tab điều hướng Match Center --}}
            <div class="match-nav-tabs">
                <button class="match-tab-btn active" data-target="tab-events">
                    <svg class="svg-icon-inline" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Diễn Biến
                </button>
                <button class="match-tab-btn" data-target="tab-pitch">
                    <svg class="svg-icon-inline" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="3" y1="12" x2="21" y2="12"></line><circle cx="12" cy="12" r="3"></circle></svg>
                    Đội Hình
                </button>
                <button class="match-tab-btn" data-target="tab-stats">
                    <svg class="svg-icon-inline" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    Thống Kê
                </button>
                <button class="match-tab-btn" data-target="tab-ai">
                    <svg class="svg-icon-inline" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    Nhận Định AI
                </button>
                <button class="match-tab-btn" data-target="tab-voting">
                    <svg class="svg-icon-inline" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="9 12 11.5 14.5 15.5 9.5"></polyline></svg>
                    Dự Đoán
                </button>
            </div>

            {{-- TAB 1: DIỄN BIẾN (TIMELINE) --}}
            <div id="tab-events" class="tab-pane active">
                <div class="timeline-card">
                    <div class="timeline-header-bar">
                        <h3 class="timeline-section-title">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            Dòng Thời Gian Diễn Biến Trận Đấu
                            @if($status === 'L')
                                <span class="live-dot-pulse" style="margin-left: 6px;"></span>
                            @endif
                        </h3>

                        {{-- Bộ lọc sự kiện thông minh: Tất Cả / Bàn Thắng / Thẻ Phạt / Thay Người --}}
                        <div class="timeline-filter-group">
                            <button type="button" class="tl-filter-btn active" data-filter="all">
                                Tất Cả
                            </button>
                            <button type="button" class="tl-filter-btn" data-filter="goal">
                                <svg class="svg-icon-inline" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="4"></circle></svg>
                                Bàn Thắng
                            </button>
                            <button type="button" class="tl-filter-btn" data-filter="card">
                                <span class="ui-rect-card yellow" style="width: 8px; height: 11px;"></span>
                                Thẻ Phạt
                            </button>
                            <button type="button" class="tl-filter-btn" data-filter="sub">
                                <svg class="svg-icon-inline" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                                Thay Người
                            </button>
                        </div>
                    </div>

                    @if(!empty($events))
                        <div class="timeline-stream">
                            @foreach($events as $event)
                                @if(($event['type'] ?? '') === 'PERIOD')
                                    {{-- Mốc thời gian bắt đầu / kết thúc hiệp đấu --}}
                                    <div class="timeline-period-divider" data-category="period">
                                        <div class="tl-period-pill {{ ($event['clock'] ?? '') === 'FT' ? 'period-ft' : '' }}">
                                            <svg class="svg-icon-inline" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            <span>{{ $event['desc'] }}</span>
                                            @if(!empty($event['score']))
                                                <span class="tl-period-score">{{ $event['score'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    {{-- Từng hàng sự kiện bàn thắng, thẻ phạt, thay người --}}
                                    @php
                                        $cat = $event['category'] ?? 'other';
                                        $isHome = !empty($event['isHome']);
                                        $badgeClass = 'badge-general';
                                        if ($cat === 'goal') $badgeClass = 'badge-goal';
                                        elseif ($cat === 'card') {
                                            $badgeClass = (($event['desc'] ?? '') === 'Thẻ đỏ') ? 'badge-card-red' : 'badge-card-yellow';
                                        } elseif ($cat === 'sub') {
                                            $badgeClass = 'badge-sub';
                                        }
                                    @endphp
                                    <div class="timeline-row {{ $isHome ? 'home-event' : 'away-event' }}" data-category="{{ $cat }}">
                                        {{-- Huy hiệu phút chính giữa --}}
                                        <div class="event-minute-badge {{ $badgeClass }}">
                                            {{ $event['clock'] }}
                                        </div>

                                        {{-- Thẻ chi tiết sự kiện --}}
                                        <div class="event-card">
                                            {{-- Header của card: Logo đội bóng + Loại sự kiện + Tỷ số lúc đó --}}
                                            <div class="event-card-header">
                                                <div class="event-team-meta">
                                                    @if(!empty($event['teamBadge']))
                                                        <img src="{{ $event['teamBadge'] }}" alt="{{ $event['teamName'] ?? 'Logo CLB' }}" loading="lazy">
                                                    @endif
                                                    <span>{{ $event['teamName'] ?? ($isHome ? ($homeTeam['name'] ?? 'Đội nhà') : ($awayTeam['name'] ?? 'Đội khách')) }}</span>
                                                </div>

                                                <div style="display: flex; align-items: center; gap: 6px;">
                                                    @if($cat === 'goal')
                                                        <span class="event-badge-tag badge-tag-goal">
                                                            <svg class="svg-icon-inline" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                                                                <circle cx="12" cy="12" r="10"></circle>
                                                                <circle cx="12" cy="12" r="4"></circle>
                                                            </svg>
                                                            {{ $event['desc'] }}
                                                        </span>
                                                    @elseif($cat === 'card')
                                                        @if(($event['desc'] ?? '') === 'Thẻ đỏ')
                                                            <span class="event-badge-tag badge-tag-card-red">
                                                                <span class="ui-rect-card red"></span> THẺ ĐỎ
                                                            </span>
                                                        @else
                                                            <span class="event-badge-tag badge-tag-card-yellow">
                                                                <span class="ui-rect-card yellow"></span> THẺ VÀNG
                                                            </span>
                                                        @endif
                                                    @elseif($cat === 'sub')
                                                        <span class="event-badge-tag badge-tag-sub">
                                                            <svg class="svg-icon-inline" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                                                                <polyline points="17 1 21 5 17 9"></polyline>
                                                                <path d="M3 11V9a4 4 0 0 1 4-4h14"></path>
                                                                <polyline points="7 23 3 19 7 15"></polyline>
                                                                <path d="M21 13v2a4 4 0 0 1-4 4H3"></path>
                                                            </svg>
                                                            THAY NGƯỜI
                                                        </span>
                                                    @endif

                                                    @if(!empty($event['score']))
                                                        <span class="event-score-tag">{{ $event['score'] }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Thân sự kiện --}}
                                            @if($cat === 'sub' && (!empty($event['subIn']) || !empty($event['subOut'])))
                                                {{-- Cặp cầu thủ Thay người Vào/Ra --}}
                                                <div class="tl-sub-duo">
                                                    @if(!empty($event['subIn']))
                                                        <div class="tl-sub-row">
                                                            <span class="tl-sub-action-badge sub-badge-in">
                                                                <svg class="svg-icon-inline" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
                                                                VÀO
                                                            </span>
                                                            <img src="{{ $event['subIn']['photo'] ?? '' }}" 
                                                                 alt="{{ $event['subIn']['name'] }}" 
                                                                 class="tl-sub-face" 
                                                                 loading="lazy"
                                                                 onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png';">
                                                            <span>{{ $event['subIn']['name'] }}</span>
                                                            @if(!empty($event['subIn']['shirtNum']))
                                                                <span style="font-size: 0.75rem; color: var(--pl-text-muted);">#{{ $event['subIn']['shirtNum'] }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if(!empty($event['subOut']))
                                                        <div class="tl-sub-row">
                                                            <span class="tl-sub-action-badge sub-badge-out">
                                                                <svg class="svg-icon-inline" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                                RA
                                                            </span>
                                                            <img src="{{ $event['subOut']['photo'] ?? '' }}" 
                                                                 alt="{{ $event['subOut']['name'] }}" 
                                                                 class="tl-sub-face" 
                                                                 loading="lazy"
                                                                 onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png';">
                                                            <span style="color: var(--pl-text-muted);">{{ $event['subOut']['name'] }}</span>
                                                            @if(!empty($event['subOut']['shirtNum']))
                                                                <span style="font-size: 0.75rem; color: var(--pl-text-muted);">#{{ $event['subOut']['shirtNum'] }}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                {{-- Bàn thắng & Thẻ phạt --}}
                                                <div class="event-body-main">
                                                    {{-- Avatar khuôn mặt cầu thủ --}}
                                                    <div class="tl-player-avatar">
                                                        <img src="{{ $event['playerPhoto'] ?? 'https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png' }}" 
                                                             alt="{{ $event['playerName'] }}" 
                                                             loading="lazy"
                                                             onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png';">
                                                        @if(!empty($event['playerNum']))
                                                            <span class="tl-player-num">{{ $event['playerNum'] }}</span>
                                                        @endif
                                                    </div>

                                                    <div style="flex: 1; min-width: 0;">
                                                        <div class="event-player-name">
                                                            {{ $event['playerName'] }}
                                                        </div>
                                                        <div class="event-detail-desc">
                                                            <span>{{ $event['desc'] }}</span>
                                                            @if(!empty($event['assistName']))
                                                                <span class="event-assist-pill">
                                                                    <svg class="svg-icon-inline" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M2 17l10 5 10-5M2 12l10 5 10-5M12 2L2 7l10 5 10-5-10-5z"></path></svg>
                                                                    Kiến tạo: <strong>{{ $event['assistName'] }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 48px 16px; color: var(--pl-text-muted);">
                            <div style="display: flex; justify-content: center; margin-bottom: 12px; color: var(--pl-text-muted);">
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            @if($status === 'U')
                                <strong style="color: var(--pl-text-dark); display: block; margin-bottom: 6px;">Trận đấu chưa bắt đầu</strong>
                                Diễn biến bàn thắng, thẻ phạt và thay người sẽ được cập nhật liên tục từng phút ngay khi bóng lăn.
                            @else
                                <strong style="color: var(--pl-text-dark); display: block; margin-bottom: 6px;">Không có sự kiện đặc biệt</strong>
                                Trận đấu diễn ra mà không ghi nhận bàn thắng hoặc thẻ phạt đáng chú ý nào.
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- TAB 2: ĐỘI HÌNH RA SÂN & SÂN BÓNG 2D --}}
            <div id="tab-pitch" class="tab-pane">
                <div class="pitch-wrapper-card">
                    @if(!empty($lineups['hasLineups']) && !empty($lineups['home']) && !empty($lineups['away']))
                        <div class="pitch-legend-bar">
                            <div class="pitch-legend-item">
                                <img src="{{ $lineups['home']['badge'] }}" alt="Logo {{ $lineups['home']['name'] }}" style="width: 26px; height: 26px; object-fit: contain;" onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                                <span style="color: var(--pl-purple);">{{ $lineups['home']['name'] }}</span>
                                <span class="formation-pill">{{ $lineups['home']['formation'] }}</span>
                            </div>
                            <div class="pitch-legend-item">
                                <span class="formation-pill">{{ $lineups['away']['formation'] }}</span>
                                <span style="color: var(--pl-pink);">{{ $lineups['away']['name'] }}</span>
                                <img src="{{ $lineups['away']['badge'] }}" alt="Logo {{ $lineups['away']['name'] }}" style="width: 26px; height: 26px; object-fit: contain;" onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                            </div>
                        </div>

                        {{-- Sân bóng 2D màu cỏ xanh & vạch vôi trắng vẽ bằng CSS thuần --}}
                        <div class="pitch-2d">
                            {{-- Vạch kẻ sân bóng --}}
                            <div class="pitch-box-top"></div>
                            <div class="pitch-goalarea-top"></div>
                            
                            <div class="pitch-line-center"></div>
                            <div class="pitch-circle-center"></div>
                            <div class="pitch-spot-center"></div>

                            <div class="pitch-box-bottom"></div>
                            <div class="pitch-goalarea-bottom"></div>

                            {{-- Nửa sân Đội Nhà (Top Half) --}}
                            <div class="pitch-half">
                                @foreach($lineups['home']['pitchRows'] as $row)
                                    <div class="pitch-row">
                                        @foreach($row as $player)
                                            <div class="pitch-player-pin" title="{{ $player['name'] }} (#{{ $player['shirtNum'] }}) &bull; {{ $player['position'] }}">
                                                <div class="player-avatar-coin shirt-home-border">
                                                    <img src="{{ $player['photo'] ?? '' }}" 
                                                         alt="{{ $player['name'] }}" 
                                                         class="player-face-img" 
                                                         loading="lazy"
                                                         decoding="async"
                                                         onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png';">
                                                    <span class="player-num-badge">{{ $player['shirtNum'] }}</span>
                                                    @if(!empty($player['isCaptain']))
                                                        <span class="player-captain-tag" title="Đội trưởng">C</span>
                                                    @endif
                                                </div>
                                                <div class="player-name-tag">{{ $player['lastName'] }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            {{-- Nửa sân Đội Khách (Bottom Half - đảo chiều) --}}
                            <div class="pitch-half">
                                @foreach(array_reverse($lineups['away']['pitchRows']) as $row)
                                    <div class="pitch-row">
                                        @foreach($row as $player)
                                            <div class="pitch-player-pin" title="{{ $player['name'] }} (#{{ $player['shirtNum'] }}) &bull; {{ $player['position'] }}">
                                                <div class="player-name-tag">{{ $player['lastName'] }}</div>
                                                <div class="player-avatar-coin shirt-away-border">
                                                    <img src="{{ $player['photo'] ?? '' }}" 
                                                         alt="{{ $player['name'] }}" 
                                                         class="player-face-img" 
                                                         loading="lazy"
                                                         decoding="async"
                                                         onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png';">
                                                    <span class="player-num-badge">{{ $player['shirtNum'] }}</span>
                                                    @if(!empty($player['isCaptain']))
                                                        <span class="player-captain-tag" title="Đội trưởng">C</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Danh sách cầu thủ dự bị (Bench) --}}
                        <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--pl-purple); margin-bottom: 14px;">Danh Sách Cầu Thủ Dự Bị</h4>
                        <div class="bench-grid">
                            {{-- Dự bị Đội nhà --}}
                            <div class="bench-col">
                                <div class="bench-header">
                                    <img src="{{ $lineups['home']['badge'] }}" alt="Logo {{ $lineups['home']['name'] }}" style="width: 22px; height: 22px; object-fit: contain;" onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                                    <span>{{ $lineups['home']['name'] }}</span>
                                </div>
                                @forelse($lineups['home']['substitutes'] as $sub)
                                    <div class="bench-item">
                                        <img src="{{ $sub['photo'] ?? '' }}" 
                                             alt="{{ $sub['name'] }}" 
                                             class="bench-face-img" 
                                             loading="lazy"
                                             decoding="async"
                                             onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png';">
                                        <span class="bench-shirt">{{ $sub['shirtNum'] }}</span>
                                        <span style="font-weight: 700;">{{ $sub['name'] }}</span>
                                        <span style="color: var(--pl-text-muted); margin-left: auto; font-size: 0.75rem; font-weight: 600;">{{ $sub['position'] }}</span>
                                    </div>
                                @empty
                                    <div style="font-size: 0.8rem; color: var(--pl-text-muted); padding: 8px 0;">Chưa có thông tin dự bị.</div>
                                @endforelse
                            </div>

                            {{-- Dự bị Đội khách --}}
                            <div class="bench-col">
                                <div class="bench-header">
                                    <img src="{{ $lineups['away']['badge'] }}" alt="Logo {{ $lineups['away']['name'] }}" style="width: 22px; height: 22px; object-fit: contain;" onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                                    <span>{{ $lineups['away']['name'] }}</span>
                                </div>
                                @forelse($lineups['away']['substitutes'] as $sub)
                                    <div class="bench-item">
                                        <img src="{{ $sub['photo'] ?? '' }}" 
                                             alt="{{ $sub['name'] }}" 
                                             class="bench-face-img" 
                                             loading="lazy"
                                             decoding="async"
                                             onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png';">
                                        <span class="bench-shirt">{{ $sub['shirtNum'] }}</span>
                                        <span style="font-weight: 700;">{{ $sub['name'] }}</span>
                                        <span style="color: var(--pl-text-muted); margin-left: auto; font-size: 0.75rem; font-weight: 600;">{{ $sub['position'] }}</span>
                                    </div>
                                @empty
                                    <div style="font-size: 0.8rem; color: var(--pl-text-muted); padding: 8px 0;">Chưa có thông tin dự bị.</div>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div style="text-align: center; padding: 48px 16px; color: var(--pl-text-muted);">
                            <div style="display: flex; justify-content: center; margin-bottom: 12px; color: var(--pl-text-muted);">
                                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                    <line x1="3" y1="12" x2="21" y2="12"></line>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </div>
                            <strong style="color: var(--pl-text-dark); display: block; margin-bottom: 6px; font-size: 1.05rem;">Đội hình ra sân chưa được công bố</strong>
                            Đội hình chính thức và sơ đồ chiến thuật 2 đội thường được ban tổ chức Premier League xác nhận 60 phút trước giờ bóng lăn.
                        </div>
                    @endif
                </div>
            </div>

            {{-- TAB 3: THỐNG KÊ (STATS) --}}
            <div id="tab-stats" class="tab-pane">
                <div class="stats-card">
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 24px; color: var(--pl-purple); display: flex; align-items: center; gap: 8px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        Thống Kê Chi Tiết Trận Đấu
                    </h3>

                    @php
                        $calcPercentage = function ($valH, $valA) {
                            $total = $valH + $valA;
                            if ($total <= 0) return [50, 50];
                            $pctH = round(($valH / $total) * 100);
                            return [$pctH, 100 - $pctH];
                        };

                        $statsList = [
                            [
                                'title' => 'Kiểm soát bóng',
                                'valH' => ($stats['possession']['home'] ?? 50) . '%',
                                'valA' => ($stats['possession']['away'] ?? 50) . '%',
                                'pctH' => $stats['possession']['home'] ?? 50,
                                'pctA' => $stats['possession']['away'] ?? 50,
                            ],
                            [
                                'title' => 'Tổng số cú sút',
                                'valH' => $stats['shots']['home'] ?? 0,
                                'valA' => $stats['shots']['away'] ?? 0,
                                'pct' => $calcPercentage($stats['shots']['home'] ?? 0, $stats['shots']['away'] ?? 0),
                            ],
                            [
                                'title' => 'Sút trúng đích',
                                'valH' => $stats['shotsOnTarget']['home'] ?? 0,
                                'valA' => $stats['shotsOnTarget']['away'] ?? 0,
                                'pct' => $calcPercentage($stats['shotsOnTarget']['home'] ?? 0, $stats['shotsOnTarget']['away'] ?? 0),
                            ],
                            [
                                'title' => 'Phạt góc',
                                'valH' => $stats['corners']['home'] ?? 0,
                                'valA' => $stats['corners']['away'] ?? 0,
                                'pct' => $calcPercentage($stats['corners']['home'] ?? 0, $stats['corners']['away'] ?? 0),
                            ],
                            [
                                'title' => 'Phạm lỗi',
                                'valH' => $stats['fouls']['home'] ?? 0,
                                'valA' => $stats['fouls']['away'] ?? 0,
                                'pct' => $calcPercentage($stats['fouls']['home'] ?? 0, $stats['fouls']['away'] ?? 0),
                            ],
                        ];
                    @endphp

                    @foreach($statsList as $item)
                        @php
                            $pctH = $item['pctH'] ?? ($item['pct'][0] ?? 50);
                            $pctA = $item['pctA'] ?? ($item['pct'][1] ?? 50);
                        @endphp
                        <div class="stats-row">
                            <div class="stats-header-info">
                                <span class="stat-val-home">{{ $item['valH'] }}</span>
                                <span class="stat-title">{{ $item['title'] }}</span>
                                <span class="stat-val-away">{{ $item['valA'] }}</span>
                            </div>
                            <div class="dual-progress-bar">
                                <div class="bar-fill-home" style="width: {{ $pctH }}%;"></div>
                                <div class="bar-fill-away" style="width: {{ $pctA }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- TAB 4: TRỢ LÝ AI TỰ ĐỘNG NHẬN ĐỊNH & TÓM TẮT TRẬN ĐẤU --}}
            <div id="tab-ai" class="tab-pane">
                @if(!empty($aiInsight))
                    <div class="ai-assistant-card">
                        <div class="ai-header">
                            <div class="ai-brand-group">
                                <div class="ai-icon-bubble">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                                </div>
                                <div>
                                    <span class="ai-badge-label">{{ $aiInsight['badge'] ?? 'Trợ Lý AI Phân Tích' }}</span>
                                    <h3 class="ai-card-title">{{ $aiInsight['title'] ?? 'Nhận Định & Điểm Nhấn Chiến Thuật' }}</h3>
                                </div>
                            </div>
                            <span class="ai-source-tag">{{ $aiInsight['source'] ?? 'Google Gemini AI' }}</span>
                        </div>

                        @if(!empty($aiInsight['content']))
                            <p class="ai-content-paragraph">{{ $aiInsight['content'] }}</p>
                        @endif

                        @if(!empty($aiInsight['highlights']))
                            <div class="ai-highlights-box">
                                <h4 class="highlights-title">
                                    {{ $status === 'C' ? '3 Điểm Nhấn Định Đoạt Trận Đấu:' : '3 Trọng Tâm Chiến Thuật Đáng Chú Ý:' }}
                                </h4>
                                <ul class="ai-highlights-list">
                                    @foreach($aiInsight['highlights'] as $highlight)
                                        <li>
                                            <span class="highlight-check">✓</span>
                                            <span>{{ $highlight }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="stats-card" style="text-align: center; padding: 48px 16px; color: var(--pl-text-muted);">
                        <div style="display: flex; justify-content: center; margin-bottom: 12px; color: var(--pl-text-muted);">
                            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"></path>
                            </svg>
                        </div>
                        <strong style="color: var(--pl-text-dark); display: block; margin-bottom: 6px;">Trợ lý AI đang xử lý dữ liệu</strong>
                        Phân tích nhận định chuyên sâu và điểm nhấn chiến thuật sẽ sẵn sàng trong giây lát.
                    </div>
                @endif
            </div>

            {{-- TAB 5: BÌNH CHỌN DỰ ĐOÁN KẾT QUẢ CỘNG ĐỒNG --}}
            <div id="tab-voting" class="tab-pane">
                <div class="community-voting-card" id="matchVotingWidget" data-match-id="{{ (int)$match['id'] }}">
                    <div class="voting-header">
                        <div>
                            <span class="voting-badge">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                Dự Đoán Cộng Đồng
                            </span>
                            <h3 class="voting-title">Bạn dự đoán kết quả trận đấu này?</h3>
                        </div>
                        <span class="voting-total-counter">
                            <strong id="totalVotesCount">{{ $voteStats['total_votes'] ?? 0 }}</strong> lượt bình chọn
                        </span>
                    </div>

                    {{-- Nút bấm bình chọn 3 lựa chọn --}}
                    <div class="voting-buttons-grid">
                        <button type="button" class="btn-vote-choice {{ ($voteStats['user_choice'] ?? '') === 'home' ? 'active-voted' : '' }}" 
                                data-choice="home" {{ ($voteStats['has_voted'] ?? false) ? 'disabled' : '' }}>
                            <span class="vote-team-name">{{ $homeTeam['name'] ?? 'Đội nhà' }} thắng</span>
                            <span class="vote-percent-tag" id="homeVoteTag">{{ $voteStats['home']['percent'] ?? 45 }}%</span>
                        </button>

                        <button type="button" class="btn-vote-choice {{ ($voteStats['user_choice'] ?? '') === 'draw' ? 'active-voted' : '' }}" 
                                data-choice="draw" {{ ($voteStats['has_voted'] ?? false) ? 'disabled' : '' }}>
                            <span class="vote-team-name">Hai đội Hòa</span>
                            <span class="vote-percent-tag" id="drawVoteTag">{{ $voteStats['draw']['percent'] ?? 25 }}%</span>
                        </button>

                        <button type="button" class="btn-vote-choice {{ ($voteStats['user_choice'] ?? '') === 'away' ? 'active-voted' : '' }}" 
                                data-choice="away" {{ ($voteStats['has_voted'] ?? false) ? 'disabled' : '' }}>
                            <span class="vote-team-name">{{ $awayTeam['name'] ?? 'Đội khách' }} thắng</span>
                            <span class="vote-percent-tag" id="awayVoteTag">{{ $voteStats['away']['percent'] ?? 30 }}%</span>
                        </button>
                    </div>

                    {{-- Thanh tỷ lệ phần trăm bình chọn trực quan: [Đội nhà thắng 65%] - [Hòa 15%] - [Đội khách thắng 20%] --}}
                    <div class="voting-progress-bar-wrap">
                        <div class="voting-multi-bar">
                            <div class="bar-segment segment-home" id="barHomeSegment" style="width: {{ $voteStats['home']['percent'] ?? 45 }}%;"></div>
                            <div class="bar-segment segment-draw" id="barDrawSegment" style="width: {{ $voteStats['draw']['percent'] ?? 25 }}%;"></div>
                            <div class="bar-segment segment-away" id="barAwaySegment" style="width: {{ $voteStats['away']['percent'] ?? 30 }}%;"></div>
                        </div>
                        <div class="voting-legend-row">
                            <span class="legend-item legend-home">
                                <span class="legend-dot"></span> {{ $homeTeam['shortName'] ?? ($homeTeam['name'] ?? 'Đội nhà') }}: <strong id="legendHomeVal">{{ $voteStats['home']['percent'] ?? 45 }}%</strong>
                            </span>
                            <span class="legend-item legend-draw">
                                <span class="legend-dot"></span> Hòa: <strong id="legendDrawVal">{{ $voteStats['draw']['percent'] ?? 25 }}%</strong>
                            </span>
                            <span class="legend-item legend-away">
                                <span class="legend-dot"></span> {{ $awayTeam['shortName'] ?? ($awayTeam['name'] ?? 'Đội khách') }}: <strong id="legendAwayVal">{{ $voteStats['away']['percent'] ?? 30 }}%</strong>
                            </span>
                        </div>
                    </div>

                    <div class="voting-feedback-msg" id="votingFeedbackMsg" style="{{ ($voteStats['has_voted'] ?? false) ? 'display: block;' : 'display: none;' }}">
                        {{ ($voteStats['has_voted'] ?? false) ? '✓ Bạn đã ghi nhận dự đoán cho trận đấu này.' : '' }}
                    </div>
                </div>
            </div>

            {{-- Thông tin trận đấu & Hướng dẫn xem trực tiếp (SEO Match Guide) --}}
            <div class="seo-content-box">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    Thông Tin Trận Đấu & Hướng Dẫn Theo Dõi Trực Tiếp
                </h2>
                <p>
                    Cuộc đọ sức đỉnh cao giữa <strong>{{ $homeTeam['name'] ?? 'Đội nhà' }}</strong> và <strong>{{ $awayTeam['name'] ?? 'Đội khách' }}</strong> thuộc khuôn khổ <strong>Vòng {{ $match['gameweek']['gameweek'] ?? 1 }}</strong> giải bóng đá Ngoại Hạng Anh (English Premier League) mùa giải 2026/2027. Cả hai câu lạc bộ đều mang trong mình quyết tâm chiến đấu cao nhất nhằm giành trọn 3 điểm số quý giá, qua đó củng cố vững chắc mục tiêu và thứ hạng trên bảng xếp hạng tổng.
                </p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin: 16px 0;">
                    <div style="background: var(--bg-primary); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                        <strong style="display: block; margin-bottom: 6px; color: var(--pl-purple);">Kênh phát sóng trực tiếp:</strong>
                        <p style="margin: 0; font-size: 0.875rem;">
                            Trận đấu được truyền hình trực tiếp tại Việt Nam trên các kênh sóng bản quyền <strong>{{ $tvChannel }}</strong>. Người hâm mộ có thể đón xem các pha bóng tốc độ và bàn thắng mãn nhãn với chất lượng hình ảnh sắc nét.
                        </p>
                    </div>
                    <div style="background: var(--bg-primary); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                        <strong style="display: block; margin-bottom: 6px; color: var(--pl-purple);">Tỷ số trực tuyến theo thời gian thực:</strong>
                        <p style="margin: 0; font-size: 0.875rem;">
                            Hệ thống tự động cập nhật tỷ số livescore, cầu thủ lập công, thẻ phạt, diễn biến thay người và nhịp độ bù giờ của trận đấu liên tục mà không cần người dùng phải tải lại trang.
                        </p>
                    </div>
                </div>
                <p>
                    <strong>Đội hình ra sân chính thức:</strong> Danh sách 11 cầu thủ đá chính và các nhân tố dự bị chiến lược được ban tổ chức Premier League công bố 60 phút trước giờ bóng lăn. Bạn có thể theo dõi cách bố trí chiến thuật trực quan trên sơ đồ sân cỏ 2D tại tab "Đội hình".
                </p>
                <p>
                    <strong>Thống kê chuyên sâu:</strong> Toàn bộ các thông số kỹ thuật như tỷ lệ kiểm soát bóng, tổng số cú sút, số pha dứt điểm trúng đích, phạt góc và phạm lỗi của {{ $homeTeam['name'] ?? 'Đội nhà' }} và {{ $awayTeam['name'] ?? 'Đội khách' }} được cập nhật chi tiết tại tab "Thống kê" giúp bạn có cái nhìn đa chiều về cục diện trận đấu.
                </p>
            </div>

            {{-- PHASE 8: Banner Quảng Cáo Chân Trang (banner_footer) --}}
            @if(isset($bannerFooter) && $bannerFooter && $bannerFooter->rendered_html)
                <div class="ad-banner-slot banner-footer" style="margin-top: 24px; margin-bottom: 8px;">
                    {!! $bannerFooter->rendered_html !!}
                </div>
            @endif
        </div>
    </main>

    {{-- Mobile Bottom Bar --}}
    @include('partials.bottom-nav')

    {{-- Footer --}}
    <footer class="site-footer">
        <div class="container footer-content">
            <div>
                <strong>Premier League Match Center &copy; {{ date('Y') }}</strong>
                <p style="opacity: 0.8; font-size: 0.775rem; margin-top: 2px;">Chuyên trang phi lợi nhuận cập nhật Ngoại Hạng Anh &bull; Không tổ chức cá độ &bull; Không phát sóng lậu</p>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap; font-size: 0.8rem; align-items: center;">
                <a href="{{ route('home') }}" style="color: var(--pl-purple); font-weight: 700;">Lịch thi đấu</a>
                <a href="{{ route('standings') }}" style="color: var(--pl-purple); font-weight: 700;">Bảng xếp hạng</a>
                <a href="{{ route('live') }}" style="color: var(--pl-purple); font-weight: 700;">Trực tiếp</a>
                <span style="color: var(--pl-border);">|</span>
                <a href="{{ route('about') }}">Giới thiệu</a>
                <a href="{{ route('contact') }}">Liên hệ</a>
                <a href="{{ route('privacy') }}">Bảo mật</a>
                <a href="{{ route('disclaimer') }}">Miễn trừ trách nhiệm</a>
            </div>
        </div>
    </footer>

    {{-- Vanilla JS Điều Hướng Tabs (Không Reload Trang) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabButtons = document.querySelectorAll('.match-tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    tabButtons.forEach(b => b.classList.remove('active'));
                    tabPanes.forEach(p => p.classList.remove('active'));

                    this.classList.add('active');
                    const targetId = this.getAttribute('data-target');
                    const targetPane = document.getElementById(targetId);
                    if (targetPane) {
                        targetPane.classList.add('active');
                    }
                });
            });
        });
    </script>

    {{-- Vanilla JS Bộ Lọc Dòng Thời Gian Diễn Biến (Timeline) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tlFilterBtns = document.querySelectorAll('.tl-filter-btn');
            const tlRows = document.querySelectorAll('.timeline-row');
            const tlDividers = document.querySelectorAll('.timeline-period-divider');

            tlFilterBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    tlFilterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const filter = this.getAttribute('data-filter');

                    tlRows.forEach(row => {
                        const cat = row.getAttribute('data-category');
                        if (filter === 'all' || cat === filter) {
                            row.style.display = 'flex';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    tlDividers.forEach(div => {
                        div.style.display = (filter === 'all' || filter === 'goal') ? 'flex' : 'none';
                    });
                });
            });
        });
    </script>

    {{-- Vanilla JS Bình Chọn Dự Đoán Kết Quả Cộng Đồng --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const votingWidget = document.getElementById('matchVotingWidget');
            if (!votingWidget) return;

            const matchId = votingWidget.getAttribute('data-match-id');
            const voteButtons = votingWidget.querySelectorAll('.btn-vote-choice');
            const feedbackMsg = document.getElementById('votingFeedbackMsg');
            const totalCountEl = document.getElementById('totalVotesCount');

            const homeTag = document.getElementById('homeVoteTag');
            const drawTag = document.getElementById('drawVoteTag');
            const awayTag = document.getElementById('awayVoteTag');

            const barHome = document.getElementById('barHomeSegment');
            const barDraw = document.getElementById('barDrawSegment');
            const barAway = document.getElementById('barAwaySegment');

            const legHome = document.getElementById('legendHomeVal');
            const legDraw = document.getElementById('legendDrawVal');
            const legAway = document.getElementById('legendAwayVal');

            function updateVotingUI(stats, userChoice) {
                if (totalCountEl && stats.total_votes !== undefined) {
                    totalCountEl.textContent = stats.total_votes;
                }

                if (homeTag && stats.home) homeTag.textContent = stats.home.percent + '%';
                if (drawTag && stats.draw) drawTag.textContent = stats.draw.percent + '%';
                if (awayTag && stats.away) awayTag.textContent = stats.away.percent + '%';

                if (barHome && stats.home) barHome.style.width = stats.home.percent + '%';
                if (barDraw && stats.draw) barDraw.style.width = stats.draw.percent + '%';
                if (barAway && stats.away) barAway.style.width = stats.away.percent + '%';

                if (legHome && stats.home) legHome.textContent = stats.home.percent + '%';
                if (legDraw && stats.draw) legDraw.textContent = stats.draw.percent + '%';
                if (legAway && stats.away) legAway.textContent = stats.away.percent + '%';

                voteButtons.forEach(btn => {
                    const choice = btn.getAttribute('data-choice');
                    if (choice === userChoice) {
                        btn.classList.add('active-voted');
                    } else {
                        btn.classList.remove('active-voted');
                    }
                    btn.disabled = true;
                });
            }

            voteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const choice = this.getAttribute('data-choice');
                    if (!choice) return;

                    voteButtons.forEach(b => b.disabled = true);

                    fetch('/api/matches/' + matchId + '/vote', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ choice: choice })
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(result => {
                        if (result.status === 200 || result.status === 201) {
                            if (feedbackMsg) {
                                feedbackMsg.textContent = '✓ ' + (result.body.message || 'Bình chọn dự đoán thành công!');
                                feedbackMsg.style.display = 'block';
                            }
                            if (result.body.stats) {
                                updateVotingUI(result.body.stats, choice);
                            }
                        } else {
                            if (feedbackMsg) {
                                feedbackMsg.textContent = result.body.message || 'Bạn đã bình chọn cho trận này.';
                                feedbackMsg.style.display = 'block';
                            }
                            if (result.body.stats) {
                                updateVotingUI(result.body.stats, result.body.stats.user_choice || choice);
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi khi gửi bình chọn:', err);
                        voteButtons.forEach(b => b.disabled = false);
                    });
                });
            });
        });
    </script>
    <script src="{{ asset('js/favorites.js') }}"></script>
    <script src="{{ asset('js/livescore-engine.js') }}"></script>
</body>
</html>
