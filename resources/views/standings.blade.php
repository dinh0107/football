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

    <title>Bảng Xếp Hạng Ngoại Hạng Anh {{ $seasonShort }} Mới Nhất | BXH Trực Tiếp</title>
    <meta name="description" content="Bảng xếp hạng Ngoại Hạng Anh mùa giải {{ $seasonShort }} mới nhất. Cập nhật BXH trực tiếp theo thời gian thực (As-It-Stands) và Top ghi bàn.">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Favicon & Mobile App Icons --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Bảng Xếp Hạng Ngoại Hạng Anh {{ $seasonShort }} Mới Nhất">
    <meta property="og:description" content="Bảng xếp hạng Ngoại Hạng Anh mùa giải {{ $seasonShort }} mới nhất, cập nhật trực tiếp theo thời gian thực.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:site_name" content="Premier League Live Tracker">

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

    {{-- CSS Nội Bộ Theo Tông Màu Ngoại Hạng Anh & Mobile-First --}}
    <style>
        :root {
            --pl-purple: #0f172a;
            --pl-purple-light: #1e293b;
            --pl-pink: #2563eb;
            --pl-mint: #06b6d4;
            --pl-mint-dark: #0284c7;
            --pl-green: #10b981;
            --pl-bg: #f8fafc;
            --pl-card-bg: #ffffff;
            --pl-text-dark: #0f172a;
            --pl-text-muted: #64748b;
            --pl-border: #e2e8f0;
            --ucl-blue: #1d4ed8;
            --uel-orange: #ea580c;
            --rel-red: #dc2626;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
            --shadow-card: 0 4px 16px rgba(56, 0, 60, 0.06);
            --shadow-hover: 0 8px 24px rgba(56, 0, 60, 0.12);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--pl-bg);
            color: var(--pl-text-dark);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: 100%;
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* Top Header */
        .site-header {
            background-color: #ffffff;
            color: var(--pl-text-dark);
            border-bottom: 1px solid var(--pl-border);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
            color: var(--pl-purple);
        }

        .brand-logo-img {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            border: 1px solid rgba(56, 189, 248, 0.25);
            transition: transform 0.2s ease;
        }

        .brand-logo:hover .brand-logo-img {
            transform: scale(1.05);
        }

        .brand-text span {
            color: var(--pl-pink);
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-link {
            padding: 7px 16px;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--pl-text-muted);
            transition: all 0.2s ease;
        }

        .header-link:hover {
            color: var(--pl-purple);
            background: #f3e8ff;
        }

        .header-link.active {
            color: #ffffff;
            background: var(--pl-purple);
            box-shadow: 0 4px 12px rgba(56, 0, 60, 0.25);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 24px 0 48px;
        }

        /* Page Hero */
        .hero-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
            border-radius: var(--radius-lg);
            padding: 28px 24px;
            color: #ffffff;
            margin-bottom: 24px;
            box-shadow: 0 10px 28px rgba(37, 99, 235, 0.18);
            position: relative;
            overflow: hidden;
        }

        .hero-title {
            font-size: 1.6rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .hero-subtitle {
            font-size: 0.925rem;
            opacity: 0.9;
            color: #fdf2f8;
        }

        /* Live Table As-It-Stands Banner */
        .live-banner {
            background: linear-gradient(90deg, #ffe4e6 0%, #fff1f2 100%);
            border: 1px solid #fecdd3;
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .live-banner-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pulse-circle {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--pl-pink);
            box-shadow: 0 0 8px var(--pl-pink);
            animation: pulse-ring 1.2s infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.8; }
        }

        .live-badge-text {
            font-weight: 800;
            font-size: 0.85rem;
            color: var(--pl-pink);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* Phase 7: Ghim Đội Bóng Yêu Thích & Icon Ngôi Sao */
        .btn-favorite-team {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: var(--radius-full);
            vertical-align: middle;
        }
        .btn-favorite-team:hover {
            color: #f59e0b;
            transform: scale(1.2);
        }
        .btn-favorite-team.is-favorite {
            color: #f59e0b;
        }
        .btn-favorite-team .star-icon {
            width: 17px;
            height: 17px;
            fill: currentColor;
            stroke: currentColor;
            stroke-width: 1.2;
        }

        /* Navigation Tabs: BXH & Top Scorers */
        .standings-tabs {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
            border-bottom: 2px solid var(--pl-border);
            padding-bottom: 8px;
        }

        .tab-btn {
            padding: 10px 22px;
            border-radius: var(--radius-full);
            font-size: 0.925rem;
            font-weight: 700;
            background: var(--pl-card-bg);
            color: var(--pl-text-muted);
            border: 1px solid var(--pl-border);
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: var(--pl-purple);
            border-color: #cbd5e1;
        }

        .tab-btn.active {
            background: var(--pl-purple);
            color: var(--pl-mint);
            border-color: var(--pl-purple);
            box-shadow: 0 4px 12px rgba(56, 0, 60, 0.2);
        }

        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block;
        }

        /* Table Card Container */
        .table-card {
            background: var(--pl-card-bg);
            border-radius: var(--radius-md);
            border: 1px solid var(--pl-border);
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }

        .table-toolbar {
            padding: 16px 20px;
            border-bottom: 1px solid var(--pl-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            background: #fafbfc;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1px solid var(--pl-border);
            padding: 6px 14px;
            border-radius: var(--radius-full);
        }

        .search-box input {
            border: none;
            outline: none;
            font-family: inherit;
            font-size: 0.85rem;
            color: var(--pl-text-dark);
            width: 180px;
        }

        /* Responsive Table */
        .table-scroll {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
            white-space: nowrap;
        }

        .data-table th {
            background: #f8fafc;
            padding: 12px 14px;
            font-weight: 800;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--pl-text-muted);
            border-bottom: 1px solid var(--pl-border);
        }

        .data-table td {
            padding: 14px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: var(--pl-text-dark);
        }

        .data-table tr:hover {
            background: #f8fafc;
        }

        /* Live Highlight Row */
        .row-live-highlight {
            background: rgba(233, 0, 82, 0.04);
        }

        /* Position Cell & Zone Borders */
        .pos-cell {
            width: 52px;
            text-align: center;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .zone-ucl {
            border-left: 4px solid var(--ucl-blue) !important;
            color: var(--ucl-blue);
        }

        .zone-uel {
            border-left: 4px solid var(--uel-orange) !important;
            color: var(--uel-orange);
        }

        .zone-rel {
            border-left: 4px solid var(--rel-red) !important;
            color: var(--rel-red);
        }

        /* Movement Badges */
        .move-up {
            font-size: 0.7rem;
            color: var(--pl-mint-dark);
            font-weight: 800;
            margin-left: 3px;
        }

        .move-down {
            font-size: 0.7rem;
            color: var(--rel-red);
            font-weight: 800;
            margin-left: 3px;
        }

        /* Club Column */
        .club-col {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
        }

        .club-badge {
            width: 30px;
            height: 30px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
            flex-shrink: 0;
        }

        .live-playing-badge {
            font-size: 0.65rem;
            background: rgba(233, 0, 82, 0.12);
            color: var(--pl-pink);
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .points-col {
            font-weight: 900;
            font-size: 1.05rem;
            color: var(--pl-purple);
            text-align: center;
        }

        /* Form Pills (W, D, L) */
        .form-pills {
            display: flex;
            align-items: center;
            gap: 4px;
            justify-content: center;
        }

        .form-pill {
            width: 22px;
            height: 22px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 800;
            color: #ffffff;
        }

        .pill-w { background: #10b981; }
        .pill-d { background: #64748b; }
        .pill-l { background: #ef4444; }

        /* Legend Footer */
        .table-legend {
            padding: 16px 20px;
            background: #fafbfc;
            border-top: 1px solid var(--pl-border);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
            font-size: 0.8rem;
            color: var(--pl-text-muted);
        }

        .legend-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .legend-bar {
            width: 10px;
            height: 10px;
            border-radius: 2px;
        }

        /* Top Scorers Cards / Table */
        .player-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .player-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            object-fit: cover;
            border: 2px solid var(--pl-border);
            flex-shrink: 0;
        }

        .player-name-text {
            font-weight: 800;
            color: var(--pl-text-dark);
            font-size: 0.925rem;
        }

        .player-pos-text {
            font-size: 0.75rem;
            color: var(--pl-text-muted);
        }

        .goals-badge {
            font-weight: 900;
            font-size: 1.15rem;
            color: var(--pl-pink);
            text-align: center;
        }

        .assists-badge {
            font-weight: 800;
            font-size: 0.95rem;
            color: var(--pl-purple);
            text-align: center;
        }

        /* Footer */
        .site-footer {
            background: #ffffff;
            color: var(--pl-text-muted);
            padding: 24px 0;
            margin-top: auto;
            border-top: 1px solid var(--pl-border);
            font-size: 0.825rem;
            box-shadow: 0 -2px 10px rgba(15, 23, 42, 0.02);
        }

        .footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Bottom Nav Bar cho thiết bị di động */
        .bottom-nav-mobile {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid var(--pl-border);
            z-index: 9999;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -4px 20px rgba(15, 23, 42, 0.08);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            font-size: 0.68rem;
            font-weight: 800;
            color: var(--pl-text-muted);
            text-decoration: none;
            padding: 6px 12px;
            position: relative;
            min-width: 64px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            -webkit-tap-highlight-color: transparent;
        }

        .bottom-nav-item:hover,
        .bottom-nav-item.active {
            color: var(--pl-pink);
            transform: translateY(-1px);
        }

        .bottom-nav-icon {
            width: 20px;
            height: 20px;
            fill: currentColor;
            transition: transform 0.2s ease;
        }

        .bottom-nav-item.active .bottom-nav-icon {
            transform: scale(1.1);
        }

        .bottom-nav-live-dot {
            position: absolute;
            top: 5px;
            right: 18px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #ef4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.9);
            animation: pulse-dot 1.2s infinite;
        }

        /* Responsive Mobile */
        @media (max-width: 640px) {
            body {
                padding-bottom: 74px !important;
            }
            .bottom-nav-mobile {
                display: flex;
            }
            .header-nav {
                display: none;
            }
            .header-content {
                height: 56px;
            }
            .hero-title {
                font-size: 1.25rem;
            }
            .hero-subtitle {
                font-size: 0.8rem;
            }
            .hero-banner {
                padding: 18px 14px;
                border-radius: var(--radius-md);
            }
            .standings-tabs {
                gap: 6px;
                margin-bottom: 16px;
            }
            .tab-btn {
                flex: 1;
                padding: 8px 8px;
                font-size: 0.775rem;
                text-align: center;
            }
            .table-toolbar {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
                padding: 12px;
            }
            .search-box {
                width: 100%;
            }
            .search-box input {
                width: 100%;
            }
            .data-table td, .data-table th {
                padding: 8px 6px;
                font-size: 0.775rem;
            }
            .pos-cell {
                width: 38px !important;
                font-size: 0.8rem;
            }
            .club-badge {
                width: 24px;
                height: 24px;
            }
            .club-col {
                gap: 6px;
                font-size: 0.8rem;
                min-width: 120px;
            }
            .points-col {
                font-size: 0.95rem !important;
                padding: 8px 4px !important;
            }
            .form-pills {
                gap: 2px;
            }
            .form-pill {
                width: 18px;
                height: 18px;
                font-size: 0.65rem;
            }
            .col-hide-mobile {
                display: none;
            }
            .site-footer {
                padding: 20px 0 16px;
                font-size: 0.775rem;
            }
            .footer-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    {{-- Top Header --}}
    <header class="site-header">
        <div class="container header-content">
            <a href="{{ route('home') }}" class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo tysongoaihang" class="brand-logo-img" width="38" height="38">
                <div class="brand-text">PREMIER<span>LEAGUE</span></div>
            </a>
            <nav class="header-nav">
                <a href="{{ route('home') }}" class="header-link">Lịch Đấu</a>
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
                                                <button type="button" class="btn-favorite-team" data-favorite-team="{{ $clubId }}" title="Ghim đội yêu thích">
                                                    <svg class="star-icon" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
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
                <a href="{{ route('home') }}" style="color: var(--pl-purple); font-weight: 700;">Lịch đấu</a>
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
