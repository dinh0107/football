<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- SEO Meta Tags Động --}}
    <title>{{ $seo['title'] ?? 'Bóng Đá Ngoại Hạng Anh - Trực Tiếp & Lịch Thi Đấu' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Cập nhật tin tức, kết quả, lịch thi đấu và bảng xếp hạng Ngoại Hạng Anh mới nhất.' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">
    <meta name="robots" content="index, follow">

    {{-- Favicon & Mobile App Icons --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Open Graph / Facebook / Zalo --}}
    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
    <meta property="og:title" content="{{ $seo['title'] ?? 'Bóng Đá Ngoại Hạng Anh' }}">
    <meta property="og:description" content="{{ $seo['description'] ?? 'Cập nhật trực tiếp kết quả Ngoại Hạng Anh mới nhất' }}">
    <meta property="og:url" content="{{ $seo['canonical'] ?? url()->current() }}">
    <meta property="og:site_name" content="Tỷ Số Ngoại Hạng Anh">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:image:secure_url" content="{{ asset('images/logo.png') }}">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="{{ $seo['title'] ?? 'Tỷ Số Ngoại Hạng Anh' }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] ?? 'Bóng Đá Ngoại Hạng Anh' }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? 'Cập nhật trực tiếp kết quả Ngoại Hạng Anh mới nhất' }}">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">

    {{-- Schema.org / SportsEvent hoặc WebSite JSON-LD Động --}}
    <script type="application/ld+json">
    {!! json_encode($seo['schema'] ?? [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Ngoại Hạng Anh Live',
        'url' => url('/'),
        'description' => 'Theo dõi trực tiếp tỷ số, lịch thi đấu và bảng xếp hạng giải bóng đá Ngoại Hạng Anh (English Premier League).'
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Pure Vanilla CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    {{-- Main Navbar --}}
    <header class="header">
        <div class="container header-inner">
            <a href="{{ route('home') }}" class="logo-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo tysongoaihang" class="logo-img" width="36" height="36" style="border-radius: 9px; object-fit: cover;" onerror="this.onerror=null; this.src='/images/logo.png';">
            </a>

            <nav class="nav-links">
                <a href="{{ route('standings') }}" class="nav-link {{ request()->routeIs('standings', 'home') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1a5.01 5.01 0 003.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/></svg>
                    Bảng Xếp Hạng
                </a>
                <a href="{{ route('fixtures') }}" class="nav-link {{ request()->routeIs('fixtures') ? 'active' : '' }}">
                    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                    Lịch Thi Đấu
                </a>
                <a href="{{ route('live') }}" class="nav-link {{ request()->routeIs('live') ? 'active' : '' }}">
                    <span class="live-badge-dot"></span>
                    Trực Tiếp
                </a>
            </nav>
        </div>
    </header>

    {{-- Ticker Bar --}}
    <div class="ticker-bar">
        <div class="container ticker-inner">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="ticker-badge">LIVE PULSE</span>
                <span style="color: var(--text-muted); font-size: 0.8rem;" id="livePollingIndicator">Cập nhật mỗi 30s từ PulseLive</span>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <a href="{{ route('fixtures') }}" class="ticker-match">
                    <span>Vòng đấu hiện tại: <strong>Mùa 2026/27</strong></span>
                </a>
                <a href="{{ route('standings') }}" class="ticker-match">
                    <span>Xem BXH mới nhất</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Page Content --}}
    <main class="main-wrapper">
        <div class="container">
            @yield('content')
        </div>
    </main>

    {{-- Mobile Bottom Bar --}}
    @include('partials.bottom-nav')

    {{-- Footer --}}
    <footer class="footer">
        <div class="container footer-inner">
            <div>
                <p><strong>Premier League Live Tracker</strong> &copy; {{ date('Y') }}. Chuyên trang phi lợi nhuận cập nhật Ngoại Hạng Anh.</p>
                <p style="font-size: 0.775rem; color: var(--text-dim); margin-top: 4px;">
                    Dữ liệu PulseLive API &bull; Không tổ chức cá độ &bull; Không phát sóng lậu.
                </p>
            </div>
            <div style="display: flex; gap: 14px; flex-wrap: wrap; font-size: 0.8rem; align-items: center;">
                <a href="{{ route('standings') }}" style="color: var(--pl-purple); font-weight: 700;">Bảng xếp hạng</a>
                <a href="{{ route('fixtures') }}" style="color: var(--pl-purple); font-weight: 700;">Lịch thi đấu</a>
                <a href="{{ route('live') }}" style="color: var(--pl-purple); font-weight: 700;">Trực tiếp</a>
                <span style="color: var(--border-light);">|</span>
                <a href="{{ route('about') }}">Giới thiệu</a>
                <a href="{{ route('contact') }}">Liên hệ</a>
                <a href="{{ route('privacy') }}">Bảo mật</a>
                <a href="{{ route('disclaimer') }}">Miễn trừ trách nhiệm</a>
            </div>
        </div>
    </footer>

    {{-- Pure Vanilla JS Engine --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/favorites.js') }}"></script>
    <script src="{{ asset('js/livescore-engine.js') }}"></script>
    @stack('scripts')
</body>
</html>
