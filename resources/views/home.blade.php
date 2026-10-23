<!DOCTYPE html>
<html lang="vi">
<head>
    @include('partials.gtm-head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Lịch Thi Đấu & Tỷ Số Ngoại Hạng Anh Trực Tiếp Premier League</title>
    <meta name="description" content="Xem lịch thi đấu và tỷ số các trận đấu Ngoại Hạng Anh trực tiếp hôm nay, kết quả bóng đá Premier League mới nhất theo ngày. Cập nhật bảng xếp hạng và diễn biến từng phút.">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Favicon & Mobile App Icons --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Open Graph / Facebook / Zalo --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Lịch Thi Đấu & Kết Quả Ngoại Hạng Anh Ngày {{ $dateNav['formattedSelected'] }}">
    <meta property="og:description" content="Xem lịch thi đấu, tỷ số trực tiếp và kết quả bóng đá Ngoại Hạng Anh ngày {{ $dateNav['formattedSelected'] }}.">
    <meta property="og:site_name" content="Tỷ Số Ngoại Hạng Anh">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:image:secure_url" content="{{ asset('images/logo.png') }}">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="Lịch Thi Đấu & Kết Quả Ngoại Hạng Anh">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Lịch Thi Đấu & Kết Quả Ngoại Hạng Anh Ngày {{ $dateNav['formattedSelected'] }}">
    <meta name="twitter:description" content="Xem lịch thi đấu, tỷ số trực tiếp và kết quả bóng đá Ngoại Hạng Anh ngày {{ $dateNav['formattedSelected'] }}.">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">

    {{-- Schema.org Structured Data --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'SportsOrganization',
        'name' => 'Premier League Live Tracker',
        'sport' => 'Football',
        'url' => url('/'),
        'description' => 'Nền tảng theo dõi kết quả, lịch thi đấu và bảng xếp hạng Ngoại Hạng Anh trực tiếp.'
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- CSS Giao Diện Trang Chủ Ngoại Hạng Anh --}}
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('partials.gtm-body')
    {{-- Header --}}
    <header class="site-header">
        <div class="container header-content">
            <a href="{{ route('home') }}" class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo tysongoaihang" class="brand-logo-img" width="38" height="38" onerror="this.onerror=null; this.src='/images/logo.png';">
                <div class="brand-text">PREMIER<span>LEAGUE</span></div>
            </a>
            <nav class="header-nav">
                <a href="{{ route('home') }}" class="header-link active">Lịch Thi Đấu</a>
                <a href="{{ route('standings') }}" class="header-link">Bảng Xếp Hạng</a>
                <a href="{{ route('live') }}" class="header-link">Trực Tiếp</a>
            </nav>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="main-content">
        <div class="container">
            {{-- PHASE 8: Banner Quảng Cáo Đầu Trang (banner_top) --}}
            @php
                $bannerTop = \App\Models\AdBanner::getActiveBanner('banner_top');
                $bannerFooter = \App\Models\AdBanner::getActiveBanner('banner_footer');
            @endphp
            @if($bannerTop && $bannerTop->rendered_html)
                <div class="ad-banner-slot banner-top" style="margin-bottom: 20px;">
                    {!! $bannerTop->rendered_html !!}
                </div>
            @endif

            {{-- Hero Section --}}
            <div class="hero-banner">
                <h1 class="hero-title">Lịch Thi Đấu, Tỷ Số Trực Tiếp & Bảng Xếp Hạng Ngoại Hạng Anh Premier League</h1>
                <p class="hero-subtitle">Mùa giải {{ $season['label'] ?? '2026/27' }} &bull; Dữ liệu thời gian thực từ PulseLive API chính thức.</p>
            </div>

            {{-- Date Navigation Card --}}
            <div class="date-nav-card">
                <div class="date-buttons">
                    <a href="{{ route('home', ['date' => $dateNav['yesterday']]) }}" 
                       class="date-btn {{ $dateNav['selected'] === $dateNav['yesterday'] ? 'active' : '' }}">
                        Hôm qua
                    </a>
                    <a href="{{ route('home', ['date' => $dateNav['today']]) }}" 
                       class="date-btn {{ $dateNav['selected'] === $dateNav['today'] ? 'active' : '' }}">
                        Hôm nay
                    </a>
                    <a href="{{ route('home', ['date' => $dateNav['tomorrow']]) }}" 
                       class="date-btn {{ $dateNav['selected'] === $dateNav['tomorrow'] ? 'active' : '' }}">
                        Ngày mai
                    </a>
                </div>

                <div class="datepicker-wrapper">
                    <label for="datePicker" style="font-size: 0.8rem; font-weight: 700; color: var(--pl-text-muted);">Chọn ngày:</label>
                    <input type="date" id="datePicker" class="datepicker-input" 
                           value="{{ $dateNav['selected'] }}" 
                           onchange="window.location.href = '{{ route('home') }}?date=' + this.value">
                </div>
            </div>

            {{-- Tiêu đề ngày thi đấu --}}
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--pl-purple);">
                    Lịch Thi Đấu & Kết Quả Các Trận Đấu Ngoại Hạng Anh: {{ $dateNav['dayOfWeek'] }}, ngày {{ $dateNav['formattedSelected'] }}
                </h2>
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--pl-text-muted);">
                    {{ count($allMatches) }} trận đấu
                </span>
            </div>

            {{-- Thanh Tab Lọc Nhanh (Vanilla JS) --}}
            <div class="filter-tabs" id="filterTabs">
                <button class="filter-tab active" data-filter="all">Tất cả ({{ count($allMatches) }})</button>
                <button class="filter-tab" data-filter="L">
                    Đang đá <span class="pulse-dot" style="display: inline-block; vertical-align: middle; width: 6px; height: 6px; margin: 0 3px;"></span> ({{ count($liveMatches) }})
                </button>
                <button class="filter-tab" data-filter="U">Sắp đá ({{ count($upcomingMatches) }})</button>
                <button class="filter-tab" data-filter="C">Đã kết thúc ({{ count($finishedMatches) }})</button>
            </div>

            {{-- Danh Sách Trận Đấu --}}
            <div class="matches-container" id="matchesContainer">
                @forelse($allMatches as $match)
                    @php
                        $slug = \App\Helpers\FootballSeoHelper::generateMatchSlug($match);
                        $tvChannel = \App\Helpers\FootballSeoHelper::getTvChannel($match);
                        $status = $match['status'] ?? 'U';
                        $homeTeam = $match['teams'][0]['team'] ?? [];
                        $awayTeam = $match['teams'][1]['team'] ?? [];
                        $homeScore = $match['teams'][0]['score'] ?? null;
                        $awayScore = $match['teams'][1]['score'] ?? null;
                        $homeId = $homeTeam['club']['id'] ?? ($homeTeam['id'] ?? 0);
                        $awayId = $awayTeam['club']['id'] ?? ($awayTeam['id'] ?? 0);
                        $kickoff = $match['kickoff']['millis'] ?? null;
                        $ground = $match['ground']['name'] ?? 'SVĐ Ngoại Hạng Anh';
                        $clock = $match['clock']['label'] ?? 'LIVE';
                    @endphp
                    <a href="{{ route('match.detail', ['slug' => $slug]) }}" 
                       class="match-card match-item" 
                       data-fixture-id="{{ (int)$match['id'] }}"
                       data-home-id="{{ $homeId }}"
                       data-away-id="{{ $awayId }}"
                       data-home-score="{{ $homeScore ?? 0 }}"
                       data-away-score="{{ $awayScore ?? 0 }}"
                       data-home-name="{{ $homeTeam['name'] ?? 'Đội nhà' }}"
                       data-away-name="{{ $awayTeam['name'] ?? 'Đội khách' }}"
                       data-status="{{ $status }}">
                        <div class="match-card-top">
                            <span class="match-venue">{{ $ground }}</span>

                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span class="badge-tv-guide">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
                                    {{ $tvChannel }}
                                </span>

                                @if($status === 'L')
                                    <span class="status-badge badge-live">
                                        <span class="pulse-dot"></span>
                                        <span class="live-clock-flash" data-fixture-clock>{{ $clock }}</span>
                                    </span>
                                @elseif($status === 'C')
                                    <span class="status-badge badge-ft">Hết giờ (FT)</span>
                                @else
                                    <span class="status-badge badge-upcoming">
                                        {{ $kickoff ? \App\Services\PremierLeagueService::formatKickoffVietnam($kickoff, 'H:i') : 'Sắp diễn ra' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="match-main-row">
                            {{-- Đội Nhà --}}
                            <div class="team-col home">
                                <button type="button" class="btn-favorite-team" data-favorite-team="{{ $homeId }}" title="Ghim đội yêu thích" aria-label="Ghim đội yêu thích">
                                    <svg class="star-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                </button>
                                <span class="team-name">{{ $homeTeam['name'] ?? 'Đội nhà' }}</span>
                                <img src="{{ \App\Services\PremierLeagueService::getTeamBadgeUrl($homeId, 70) }}" 
                                     alt="{{ $homeTeam['name'] ?? '' }}" 
                                     class="team-logo" loading="lazy"
                                     onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                            </div>

                            {{-- Tỉ Số hoặc Giờ Thi Đấu --}}
                            <div class="score-box">
                                @if($status === 'L' || $status === 'C')
                                    <div class="score-value" data-score-display>
                                        <span data-score-home>{{ $homeScore ?? 0 }}</span> - <span data-score-away>{{ $awayScore ?? 0 }}</span>
                                    </div>
                                    @if($status === 'L')
                                        <span style="font-size: 0.7rem; color: var(--pl-pink); font-weight: 800; margin-top: 4px;">TRỰC TIẾP</span>
                                    @endif
                                @else
                                    <div class="kickoff-time">
                                        {{ $kickoff ? \App\Services\PremierLeagueService::formatKickoffVietnam($kickoff, 'H:i') : 'VS' }}
                                    </div>
                                    <span style="font-size: 0.7rem; color: var(--pl-text-muted); margin-top: 4px;">Giờ VN</span>
                                @endif
                            </div>

                            {{-- Đội Khách --}}
                            <div class="team-col away">
                                <img src="{{ \App\Services\PremierLeagueService::getTeamBadgeUrl($awayId, 70) }}" 
                                     alt="{{ $awayTeam['name'] ?? '' }}" 
                                     class="team-logo" loading="lazy"
                                     onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                                <span class="team-name">{{ $awayTeam['name'] ?? 'Đội khách' }}</span>
                                <button type="button" class="btn-favorite-team" data-favorite-team="{{ $awayId }}" title="Ghim đội yêu thích" aria-label="Ghim đội yêu thích">
                                    <svg class="star-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                </button>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state">
                        <div style="margin-bottom: 12px; color: var(--pl-purple); display: flex; justify-content: center;">
                            <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m4.93 4.93 4.24 4.24"></path>
                                <path d="m14.83 9.17 4.24-4.24"></path>
                                <path d="m14.83 14.83 4.24 4.24"></path>
                                <path d="m9.17 14.83-4.24 4.24"></path>
                                <circle cx="12" cy="12" r="4"></circle>
                            </svg>
                        </div>
                        <h3 class="empty-title">Không có trận đấu Ngoại Hạng Anh nào diễn ra trong ngày này</h3>
                        <p class="empty-desc">
                            Ngày {{ $dateNav['formattedSelected'] }} không có lịch thi đấu. Hãy chọn mốc ngày khác hoặc xem lịch thi đấu các vòng đấu sắp tới.
                        </p>
                        <div class="empty-actions">
                            <a href="{{ route('home', ['date' => '2026-10-24']) }}" class="date-btn active">
                                Xem vòng 8 (24/10/2026)
                            </a>
                            <a href="{{ route('standings') }}" class="date-btn">
                                Xem Bảng Xếp Hạng
                            </a>
                        </div>
                    </div>
                @endforelse

                {{-- Thông báo khi lọc tab không có trận --}}
                <div id="filterEmptyMessage" class="empty-state" style="display: none;">
                    <p class="empty-desc" style="margin-bottom: 0;">
                        Không có trận đấu nào thuộc nhóm này trong ngày {{ $dateNav['formattedSelected'] }}.
                    </p>
                </div>
            </div>

            {{-- Khối thông tin biên tập SEO về Chuyên Trang Tỷ Số Ngoại Hạng Anh --}}
            <div class="seo-content-box" style="margin-top: 28px;">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
                    Tỷ Số Trực Tiếp, Lịch Thi Đấu & Bảng Xếp Hạng Ngoại Hạng Anh Premier League
                </h2>
                <p>
                    Chào mừng bạn đến với chuyên trang <strong>tysongoaihang.site</strong> - nền tảng dữ liệu thể thao trực tuyến chuyên sâu, cập nhật nhanh và chính xác nhất tỷ số bóng đá Ngoại Hạng Anh (English Premier League) mùa giải 2026/2027. Toàn bộ thông tin lịch thi đấu, kết quả trận đấu, bảng xếp hạng 20 câu lạc bộ và các thông số chuyên môn được đồng bộ trực tiếp từ ban tổ chức Premier League.
                </p>
                <p>
                    <strong>Những tiện ích vượt trội dành cho người hâm mộ:</strong>
                </p>
                <ul>
                    <li><strong>Theo dõi tỷ số trực tuyến (Livescore 24/7):</strong> Cập nhật biến động tỷ số, cầu thủ ghi bàn, thẻ vàng, thẻ đỏ và diễn biến từng phút của các cặp đấu tâm điểm có sự góp mặt của Manchester City, Arsenal, Liverpool, Manchester United, Chelsea, Tottenham Hotspur cùng các đội bóng tại giải đấu.</li>
                    <li><strong>Lịch thi đấu & Kênh phát sóng bản quyền:</strong> Tra cứu giờ thi đấu chính xác theo giờ Việt Nam, kèm thông tin kênh tiếp sóng trực tiếp (K+ Sport, TV360, FPT Play).</li>
                    <li><strong>Sa bàn chiến thuật 2D & Đội hình ra sân:</strong> Nắm bắt đội hình chính thức 60 phút trước giờ bóng lăn, quan sát cách sắp xếp chiến thuật của các huấn luyện viên hàng đầu thế giới.</li>
                    <li><strong>Bảng xếp hạng trực tiếp (Live Table):</strong> Tự động tính toán điểm số và phản ánh ngay lập tức sự thay đổi vị trí của các đội trong suốt 90 phút thi đấu.</li>
                </ul>
                <p>
                    Trang web được tối ưu hóa cho mọi thiết bị di động và máy tính, tốc độ tải trang tức thì, giao diện trực quan chuẩn quốc tế mang lại trải nghiệm bóng đá trọn vẹn và hoàn hảo nhất.
                </p>
            </div>

            {{-- PHASE 8: Banner Quảng Cáo Chân Trang (banner_footer) --}}
            @if(isset($bannerFooter) && $bannerFooter && $bannerFooter->rendered_html)
                <div class="ad-banner-slot banner-footer" style="margin-top: 24px; margin-bottom: 10px;">
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
                <strong>Premier League Tracker &copy; {{ date('Y') }}</strong>
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

    {{-- Vanilla JavaScript Lọc Nhanh Theo Tab (Không Tải Lại Trang) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.filter-tab');
            const matchItems = document.querySelectorAll('.match-item');
            const emptyMsg = document.getElementById('filterEmptyMessage');

            if (!tabs.length || !matchItems.length) return;

            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    // Update active tab styling
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const filterValue = this.getAttribute('data-filter');
                    let visibleCount = 0;

                    matchItems.forEach(item => {
                        const itemStatus = item.getAttribute('data-status');
                        if (filterValue === 'all' || itemStatus === filterValue) {
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Hiển thị thông báo nếu không có trận nào khớp bộ lọc
                    if (emptyMsg) {
                        emptyMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
                    }
                });
            });
        });
    </script>
    <script src="{{ asset('js/favorites.js') }}"></script>
    <script src="{{ asset('js/livescore-engine.js') }}"></script>
</body>
</html>
