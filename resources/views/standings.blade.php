<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @php
        $rawSeason = $season['label'] ?? '2026/27';
        $seasonShort = preg_match('/\d{4}\/\d{2,4}/', $rawSeason, $m) ? $m[0] : '2026/27';
    @endphp

    <title>Bảng Xếp Hạng Ngoại Hạng Anh {{ $seasonShort }} - BXH Bóng Đá Premier League Mới Nhất</title>
    <meta name="description" content="Bảng xếp hạng Ngoại Hạng Anh mùa giải {{ $seasonShort }} mới nhất. Cập nhật BXH Premier League trực tiếp theo thời gian thực, điểm số, hiệu số và Top ghi bàn.">
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
    <meta property="og:title" content="Bảng Xếp Hạng Ngoại Hạng Anh {{ $seasonShort }} Mới Nhất">
    <meta property="og:description" content="Bảng xếp hạng Ngoại Hạng Anh mùa giải {{ $seasonShort }} mới nhất, cập nhật trực tiếp theo thời gian thực.">
    <meta property="og:site_name" content="Tỷ Số Ngoại Hạng Anh">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:image:secure_url" content="{{ asset('images/logo.png') }}">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="Bảng Xếp Hạng Ngoại Hạng Anh">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Bảng Xếp Hạng Ngoại Hạng Anh {{ $seasonShort }} Mới Nhất">
    <meta name="twitter:description" content="Bảng xếp hạng Ngoại Hạng Anh mùa giải {{ $seasonShort }} mới nhất.">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">

    {{-- Schema.org Structured Data --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'SportsOrganization',
        'name' => 'Bảng Xếp Hạng Premier League',
        'sport' => 'Football',
        'url' => url()->current(),
        'description' => 'Bảng xếp hạng Ngoại Hạng Anh và bảng thành tích ghi bàn của các cầu thủ hàng đầu.'
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- CSS Bảng Xếp Hạng Ngoại Hạng Anh --}}
    <link rel="stylesheet" href="{{ asset('css/standings.css') }}">
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
                <a href="{{ route('standings') }}" class="header-link active">Bảng Xếp Hạng</a>
                <a href="{{ route('live') }}" class="header-link">Trực Tiếp</a>
            </nav>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="main-content">
        <div class="container">
            {{-- Hero Banner --}}
            <div class="hero-banner">
                <h1 class="hero-title">Bảng Xếp Hạng Ngoại Hạng Anh {{ $seasonShort }}</h1>
                <p class="hero-subtitle">Bảng xếp hạng đầy đủ, bảng trực tiếp thời gian thực (Live Table As-It-Stands) & Top Vua phá lưới.</p>
            </div>

            {{-- Thông báo Live Table As-It-Stands --}}
            @if($isLiveMode)
                <div class="live-banner">
                    <div class="live-banner-left">
                        <span class="pulse-circle"></span>
                        <div>
                            <span class="live-badge-text">BXH Trực Tiếp (Live Table As-It-Stands)</span>
                            <div style="font-size: 0.8rem; color: #881337; margin-top: 2px;">
                                Đang tự động cộng điểm tạm thời và tính lại thứ hạng theo diễn biến của <strong>{{ $liveMatchesCount }}</strong> trận đấu trực tiếp!
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('live') }}" style="font-size: 0.8rem; font-weight: 800; color: var(--pl-pink);">
                        Xem các trận đang đá &rarr;
                    </a>
                </div>
            @endif

            {{-- Tabs Điều Hướng: BXH Chính vs Top Vua Phá Lưới --}}
            <div class="standings-tabs">
                <button class="tab-btn active" data-target="tab-standings">
                    Bảng Xếp Hạng Đầy Đủ
                </button>
                <button class="tab-btn" data-target="tab-scorers">
                    Top Vua Phá Lưới
                </button>
            </div>

            {{-- TAB 1: BẢNG XẾP HẠNG ĐẦY ĐỦ --}}
            <div id="tab-standings" class="tab-pane active">
                <div class="table-card">
                    <div class="table-toolbar">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <strong style="font-size: 0.95rem; color: var(--pl-purple);">Premier League Standings</strong>
                            <span style="font-size: 0.8rem; color: var(--pl-text-muted);">(Tổng cộng 20 CLB &bull; Cache 30 phút)</span>
                        </div>
                        <div class="search-box">
                            <span style="color: var(--pl-text-muted); font-size: 0.85rem;">Tìm:</span>
                            <input type="text" id="clubSearchInput" placeholder="Tên câu lạc bộ...">
                        </div>
                    </div>

                    <div class="table-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="text-align: center; width: 50px;">#</th>
                                    <th>Câu lạc bộ</th>
                                    <th style="text-align: center;">Trận</th>
                                    <th style="text-align: center;">Thắng</th>
                                    <th style="text-align: center;">Hòa</th>
                                    <th style="text-align: center;">Thua</th>
                                    <th class="col-hide-mobile" style="text-align: center;">BT-BB</th>
                                    <th style="text-align: center;">Hiệu số</th>
                                    <th style="text-align: center; width: 70px;">Điểm</th>
                                    <th style="text-align: center;">5 Trận Gần Nhất</th>
                                </tr>
                            </thead>
                            <tbody id="standingsTableBody">
                                @forelse($standings as $entry)
                                    @php
                                        $pos = (int) ($entry['position'] ?? 1);
                                        $team = $entry['team'] ?? [];
                                        $clubId = (int) ($team['club']['id'] ?? ($team['id'] ?? 0));
                                        $overall = $entry['overall'] ?? [];
                                        $form = $entry['form'] ?? [];
                                        $isLiveNow = !empty($entry['isLiveNow']);
                                        $movement = (int) ($entry['movement'] ?? 0);

                                        // Xác định viền màu theo quy định cúp châu Âu & xuống hạng
                                        $zoneClass = '';
                                        if ($pos >= 1 && $pos <= 4) {
                                            $zoneClass = 'zone-ucl';
                                        } elseif ($pos === 5) {
                                            $zoneClass = 'zone-uel';
                                        } elseif ($pos >= 18) {
                                            $zoneClass = 'zone-rel';
                                        }
                                    @endphp
                                    <tr class="table-row {{ $isLiveNow ? 'row-live-highlight' : '' }}" data-team="{{ strtolower($team['name'] ?? '') }}">
                                        {{-- Thứ hạng (#) --}}
                                        <td class="pos-cell {{ $zoneClass }}">
                                            <span>{{ $pos }}</span>
                                            @if($isLiveMode && $movement > 0)
                                                <span class="move-up" title="Tăng {{ $movement }} bậc">▲{{ $movement }}</span>
                                            @elseif($isLiveMode && $movement < 0)
                                                <span class="move-down" title="Tụt {{ abs($movement) }} bậc">▼{{ abs($movement) }}</span>
                                            @endif
                                        </td>

                                        {{-- CLB --}}
                                        <td>
                                            <div class="club-col">
                                                <button type="button" class="btn-favorite-team" data-favorite-team="{{ $clubId }}" title="Ghim đội yêu thích" aria-label="Ghim đội yêu thích">
                                                    <svg class="star-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                                </button>
                                                <img src="{{ \App\Services\PremierLeagueService::getTeamBadgeUrl($clubId, 50) }}" 
                                                     alt="{{ $team['name'] ?? 'CLB' }}" 
                                                     class="club-badge" loading="lazy"
                                                     onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/50/t1.png';">
                                                <div>
                                                    <div>{{ $team['name'] ?? 'Chưa rõ' }}</div>
                                                    @if($isLiveNow)
                                                        <span class="live-playing-badge">ĐANG ĐÁ</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Thống kê thi đấu --}}
                                        <td style="text-align: center; font-weight: 700;">{{ $overall['played'] ?? 0 }}</td>
                                        <td style="text-align: center; color: #10b981; font-weight: 700;">{{ $overall['won'] ?? 0 }}</td>
                                        <td style="text-align: center; color: var(--pl-text-muted);">{{ $overall['drawn'] ?? 0 }}</td>
                                        <td style="text-align: center; color: #ef4444; font-weight: 700;">{{ $overall['lost'] ?? 0 }}</td>
                                        <td class="col-hide-mobile" style="text-align: center; color: var(--pl-text-muted);">{{ $overall['goalsFor'] ?? 0 }}-{{ $overall['goalsAgainst'] ?? 0 }}</td>
                                        <td style="text-align: center; font-weight: 700; color: {{ ($overall['goalsDifference'] ?? 0) >= 0 ? 'var(--pl-mint-dark)' : '#ef4444' }};">
                                            {{ ($overall['goalsDifference'] ?? 0) > 0 ? '+' : '' }}{{ $overall['goalsDifference'] ?? 0 }}
                                        </td>
                                        
                                        {{-- Điểm (PTS) --}}
                                        <td class="points-col">{{ $overall['points'] ?? 0 }}</td>

                                        {{-- 5 Trận Gần Nhất --}}
                                        <td style="text-align: center;">
                                            @if(!empty($form))
                                                <div class="form-pills">
                                                    @foreach(array_slice($form, -5) as $f)
                                                        @php
                                                            $out = strtoupper($f['outcome'] ?? (string)$f);
                                                            $pillClass = match($out) {
                                                                'W', 'WIN' => 'pill-w',
                                                                'D', 'DRAW' => 'pill-d',
                                                                default => 'pill-l',
                                                            };
                                                            $label = match($out) {
                                                                'W', 'WIN' => 'W',
                                                                'D', 'DRAW' => 'D',
                                                                default => 'L',
                                                            };
                                                        @endphp
                                                        <span class="form-pill {{ $pillClass }}">{{ $label }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span style="color: var(--pl-text-muted); font-size: 0.8rem;">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" style="text-align: center; padding: 36px; color: var(--pl-text-muted);">
                                            Đang đồng bộ dữ liệu Bảng xếp hạng từ Premier League API...
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Legend Chú Thích Khu Vực Màu --}}
                    <div class="table-legend">
                        <div class="legend-indicator">
                            <span class="legend-bar" style="background: var(--ucl-blue);"></span>
                            <span>Hạng 1 - 4: Vòng bảng UEFA Champions League</span>
                        </div>
                        <div class="legend-indicator">
                            <span class="legend-bar" style="background: var(--uel-orange);"></span>
                            <span>Hạng 5: Vòng bảng UEFA Europa League</span>
                        </div>
                        <div class="legend-indicator">
                            <span class="legend-bar" style="background: var(--rel-red);"></span>
                            <span>Hạng 18 - 20: Xuống hạng Championship</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: TOP VUA PHÁ LƯỚI --}}
            <div id="tab-scorers" class="tab-pane">
                <div class="table-card">
                    <div class="table-toolbar">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <strong style="font-size: 0.95rem; color: var(--pl-purple);">Top 10 Vua Phá Lưới Ngoại Hạng Anh</strong>
                            <span style="font-size: 0.8rem; color: var(--pl-text-muted);">(Cập nhật mùa giải {{ $season['label'] ?? '' }})</span>
                        </div>
                    </div>

                    <div class="table-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="text-align: center; width: 50px;">Hạng</th>
                                    <th>Cầu thủ</th>
                                    <th>Câu lạc bộ</th>
                                    <th style="text-align: center;">Quốc tịch</th>
                                    <th style="text-align: center;">Bàn thắng</th>
                                    <th style="text-align: center;">Kiến tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topScorers as $scorer)
                                    <tr>
                                        <td style="text-align: center; font-weight: 800; font-size: 1rem; color: var(--pl-purple);">
                                            {{ $scorer['rank'] ?? 1 }}
                                        </td>
                                        <td>
                                            <div class="player-row">
                                                <img src="{{ $scorer['photo'] }}" 
                                                     alt="{{ $scorer['name'] }}" 
                                                     class="player-avatar" 
                                                     loading="lazy"
                                                     onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/photos/players/110x140/Photo-Missing.png';">
                                                <div>
                                                    <div class="player-name-text">{{ $scorer['name'] }}</div>
                                                    <div class="player-pos-text">{{ $scorer['position'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <img src="{{ $scorer['teamBadge'] }}" 
                                                     alt="{{ $scorer['teamName'] }}" 
                                                     style="width: 24px; height: 24px; object-fit: contain;"
                                                     loading="lazy"
                                                     onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/50/t1.png';">
                                                <span style="font-weight: 700;">{{ $scorer['teamName'] }}</span>
                                            </div>
                                        </td>
                                        <td style="text-align: center; color: var(--pl-text-muted);">
                                            {{ $scorer['nationality'] ?: '--' }}
                                        </td>
                                        <td class="goals-badge">{{ $scorer['goals'] }}</td>
                                        <td class="assists-badge">{{ $scorer['assists'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 32px; color: var(--pl-text-muted);">
                                            Đang tải danh sách cầu thủ ghi bàn...
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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

    {{-- Vanilla JS Chuyển Tab & Lọc Realtime --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chuyển Tab: Bảng Xếp Hạng & Top Vua Phá Lưới
            const tabButtons = document.querySelectorAll('.tab-btn');
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

            // Lọc nhanh tên CLB trên Bảng Xếp Hạng
            const searchInput = document.getElementById('clubSearchInput');
            const rows = document.querySelectorAll('.table-row');

            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const query = e.target.value.toLowerCase().trim();
                    rows.forEach(row => {
                        const teamName = row.getAttribute('data-team') || '';
                        if (teamName.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
    <script src="{{ asset('js/favorites.js') }}"></script>
</body>
</html>
