<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $seo['title'] ?? 'Chi Tiết Trận Đấu | Premier League Match Center' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Xem trực tiếp diễn biến trận đấu, đội hình ra sân 2D và thống kê chi tiết giải Ngoại Hạng Anh.' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">
    <meta name="robots" content="index, follow">

    {{-- Open Graph / Facebook & Zalo Meta Tags --}}
    <meta property="og:type" content="{{ $seo['og']['type'] ?? 'website' }}">
    <meta property="og:title" content="{{ $seo['og']['title'] ?? ($seo['title'] ?? '') }}">
    <meta property="og:description" content="{{ $seo['og']['description'] ?? ($seo['description'] ?? '') }}">
    <meta property="og:url" content="{{ $seo['og']['url'] ?? url()->current() }}">
    <meta property="og:image" content="{{ $seo['og']['image'] ?? asset('favicon.ico') }}">
    <meta property="og:site_name" content="{{ $seo['og']['site_name'] ?? 'Premier League Live Tracker' }}">

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

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- CSS Giao Diện Match Center Đỉnh Cao --}}
    <style>
        :root {
            --pl-purple: #0f172a;
            --pl-purple-light: #1e293b;
            --pl-purple-dark: #020617;
            --pl-pink: #2563eb;
            --pl-mint: #06b6d4;
            --pl-mint-dark: #0284c7;
            --pl-green: #10b981;
            --pl-bg: #f8fafc;
            --pl-card-bg: #ffffff;
            --pl-text-dark: #0f172a;
            --pl-text-muted: #64748b;
            --pl-border: #e2e8f0;
            --pl-border-light: #f1f5f9;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 22px;
            --radius-full: 9999px;
            --shadow-subtle: 0 2px 10px rgba(15, 23, 42, 0.04);
            --shadow-card: 0 8px 30px rgba(15, 23, 42, 0.06);
            --shadow-hover: 0 14px 36px rgba(15, 23, 42, 0.12);
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
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: 100%;
            max-width: 1100px;
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
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
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

        .brand-badge {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 100%);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 900;
            font-size: 0.95rem;
            box-shadow: 0 3px 8px rgba(37, 99, 235, 0.25);
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
            background: #f1f5f9;
        }

        /* Main Content Layout */
        .main-content {
            flex: 1;
            padding: 20px 0 60px;
        }

        /* Breadcrumb Navigation */
        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--pl-text-muted);
            margin-bottom: 16px;
        }

        .breadcrumb-nav a {
            color: var(--pl-purple);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: #ffffff;
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-full);
            transition: all 0.2s ease;
            box-shadow: var(--shadow-subtle);
        }

        .breadcrumb-nav a:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateX(-2px);
        }

        /* =====================================================================
           MATCH HERO SCOREBOARD (STADIUM NIGHT GLASSMORPHISM)
           ===================================================================== */
        .match-hero-card {
            background: radial-gradient(circle at 50% -20%, #1e293b 0%, #0f172a 60%, #020617 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            padding: 32px 24px 20px;
            color: #ffffff;
            margin-bottom: 22px;
            box-shadow: 0 20px 50px -10px rgba(2, 6, 23, 0.6), 0 0 30px rgba(37, 99, 235, 0.12);
            text-align: center;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(16px);
        }

        /* Background stadium light ambience */
        .match-hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 15%;
            right: 15%;
            height: 140px;
            background: radial-gradient(ellipse at center, rgba(56, 189, 248, 0.16) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .hero-competition-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
        }

        .hero-competition-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
            padding: 5px 14px;
            border-radius: var(--radius-full);
            font-size: 0.775rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            backdrop-filter: blur(8px);
        }

        .hero-tv-guide-bar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 255, 133, 0.14);
            border: 1px solid rgba(0, 255, 133, 0.35);
            padding: 5px 14px;
            border-radius: var(--radius-full);
            color: #ffffff;
            font-size: 0.775rem;
            font-weight: 700;
            backdrop-filter: blur(8px);
        }

        .hero-tv-guide-bar strong {
            color: var(--pl-mint);
            font-weight: 900;
            letter-spacing: 0.02em;
        }

        /* Scoreboard Main Row */
        .hero-scoreboard {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
        }

        .hero-team {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 36%;
        }

        .hero-crest-wrap {
            width: 92px;
            height: 92px;
            background: rgba(255, 255, 255, 0.07);
            border: 1.5px solid rgba(255, 255, 255, 0.18);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3), inset 0 0 15px rgba(255, 255, 255, 0.05);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .hero-crest-wrap:hover {
            transform: scale(1.08);
            border-color: rgba(255, 255, 255, 0.35);
        }

        .hero-team-logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.4));
        }

        .hero-team-name {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            color: #ffffff;
            margin-top: 12px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
            line-height: 1.25;
            text-align: center;
        }

        /* Danh sách cầu thủ ghi bàn trực tiếp trên Hero Scoreboard */
        .hero-goalscorers {
            margin-top: 8px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 0.775rem;
            color: rgba(255, 255, 255, 0.85);
            text-align: center;
            max-width: 220px;
        }

        .hero-goal-item {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            line-height: 1.3;
        }

        .hero-goal-item .ball-icon {
            color: var(--pl-mint);
            font-size: 0.85rem;
        }

        .hero-goal-item strong {
            color: #ffffff;
            font-weight: 700;
        }

        .hero-goal-item .goal-minute {
            color: rgba(255, 255, 255, 0.65);
            font-weight: 600;
        }

        /* Center Score Box */
        .hero-score-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 28%;
            padding-top: 6px;
        }

        .hero-status-pill {
            padding: 5px 16px;
            border-radius: var(--radius-full);
            font-weight: 800;
            font-size: 0.775rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
        }

        .pill-live {
            background: linear-gradient(135deg, #e90052 0%, #ff176b 100%);
            color: #ffffff;
            box-shadow: 0 0 16px rgba(233, 0, 82, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .live-dot-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #00ff85;
            display: inline-block;
            box-shadow: 0 0 8px #00ff85;
            animation: pulse-dot 1.2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        .pill-ft {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #f1f5f9;
            backdrop-filter: blur(8px);
        }

        .pill-upcoming {
            background: linear-gradient(135deg, #00ff85 0%, #00b860 100%);
            color: var(--pl-purple);
            font-weight: 900;
            box-shadow: 0 0 16px rgba(0, 255, 133, 0.3);
        }

        .hero-score-display {
            font-size: 3.8rem;
            font-weight: 900;
            letter-spacing: 0.04em;
            color: #ffffff;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.5), 0 0 25px rgba(233, 0, 82, 0.25);
            margin-bottom: 6px;
        }

        .hero-score-vs {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--pl-mint);
            text-shadow: 0 0 16px rgba(0, 255, 133, 0.4);
            line-height: 1.1;
            margin-bottom: 4px;
        }

        /* Match Info Footer Ribbon */
        .hero-info-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding-top: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.75);
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .hero-info-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .hero-info-pill svg {
            color: var(--pl-mint);
            width: 14px;
            height: 14px;
        }

        .hero-info-pill strong {
            color: #ffffff;
            font-weight: 700;
        }

        /* =====================================================================
           MODERN SEGMENTED TABS NAVIGATION
           ===================================================================== */
        .match-nav-tabs {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            border-radius: var(--radius-md);
            padding: 6px;
            border: 1px solid var(--pl-border);
            box-shadow: var(--shadow-subtle);
            margin-bottom: 22px;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .match-nav-tabs::-webkit-scrollbar {
            display: none;
        }

        .match-tab-btn {
            flex: 1;
            min-width: 130px;
            text-align: center;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.875rem;
            color: var(--pl-text-muted);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .match-tab-btn:hover {
            color: var(--pl-purple);
            background: #f8fafc;
        }

        .match-tab-btn.active {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
        }

        /* Banner Quảng Cáo Tài Trợ - Polished Modern Card */
        .ad-banner-slot {
            width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 14px 16px;
            text-align: center;
            box-shadow: var(--shadow-subtle);
            position: relative;
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .ad-banner-slot:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
        }

        .ad-banner-slot img {
            max-width: 100%;
            height: auto;
            border-radius: var(--radius-sm);
            display: inline-block;
            vertical-align: middle;
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.25s ease-out;
        }

        .tab-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* =====================================================================
           TAB 1: TIMELINE (DIỄN BIẾN TRẬN ĐẤU)
           ===================================================================== */
        .timeline-card {
            background: var(--pl-card-bg);
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 28px 24px;
            box-shadow: var(--shadow-card);
        }

        /* =====================================================================
           TAB 1: DIỄN BIẾN TRẬN ĐẤU (TIMELINE) - SOFASCORE & FOTMOB STYLE
           ===================================================================== */
        .timeline-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--pl-border);
        }

        .timeline-section-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--pl-purple);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .timeline-filter-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .tl-filter-btn {
            background: #ffffff;
            border: 1px solid var(--pl-border);
            color: var(--pl-text-muted);
            font-size: 0.775rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 9999px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .tl-filter-btn:hover {
            border-color: var(--pl-purple);
            color: var(--pl-purple);
        }

        .tl-filter-btn.active {
            background: var(--pl-purple);
            color: #ffffff;
            border-color: var(--pl-purple);
            box-shadow: 0 2px 8px rgba(56, 0, 60, 0.2);
        }

        .timeline-stream {
            position: relative;
            padding: 24px 0 10px 0;
            max-width: 820px;
            margin: 0 auto;
        }

        .timeline-stream::before {
            content: '';
            position: absolute;
            top: 10px;
            bottom: 10px;
            left: 50%;
            width: 2px;
            background: linear-gradient(180deg, rgba(0, 255, 133, 0.6) 0%, rgba(56, 0, 60, 0.3) 50%, rgba(233, 0, 82, 0.6) 100%);
            transform: translateX(-50%);
            border-radius: 2px;
        }

        /* Phân đoạn thời gian (Period Divider: Bắt đầu H1, Hết H1, Bắt đầu H2, Hết Giờ) */
        .timeline-period-divider {
            position: relative;
            z-index: 3;
            display: flex;
            justify-content: center;
            margin: 28px 0;
            width: 100%;
        }

        .tl-period-pill {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            color: #ffffff;
            padding: 6px 18px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 14px rgba(30, 27, 75, 0.25);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
        }

        .tl-period-pill.period-ft {
            background: linear-gradient(135deg, var(--pl-purple) 0%, var(--pl-pink) 100%);
            box-shadow: 0 4px 18px rgba(233, 0, 82, 0.35);
        }

        .tl-period-score {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 900;
            letter-spacing: 0.05em;
        }

        .ui-rect-card {
            display: inline-block;
            width: 10px;
            height: 14px;
            border-radius: 2px;
            vertical-align: middle;
            flex-shrink: 0;
        }

        .ui-rect-card.yellow {
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            box-shadow: 0 0 6px rgba(245, 158, 11, 0.5);
            border: 1px solid #fde68a;
        }

        .ui-rect-card.red {
            background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
            box-shadow: 0 0 6px rgba(239, 68, 68, 0.5);
            border: 1px solid #fecaca;
        }

        .svg-icon-inline {
            display: inline-block;
            vertical-align: middle;
            flex-shrink: 0;
        }

        /* Từng hàng sự kiện */
        .timeline-row {
            display: flex;
            align-items: center;
            margin-bottom: 26px;
            position: relative;
            width: 100%;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .timeline-row.home-event {
            justify-content: flex-start;
            padding-right: 52%;
        }

        .timeline-row.away-event {
            justify-content: flex-end;
            padding-left: 52%;
        }

        /* Huy hiệu phút ở chính giữa trục dọc */
        .event-minute-badge {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 44px;
            height: 44px;
            background: #ffffff;
            color: var(--pl-text-dark);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 0.8rem;
            border: 3px solid #e2e8f0;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12);
            z-index: 2;
            letter-spacing: -0.02em;
            transition: transform 0.2s ease;
        }

        .timeline-row:hover .event-minute-badge {
            transform: translateX(-50%) scale(1.1);
        }

        .event-minute-badge.badge-goal {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            border-color: #a7f3d0;
            box-shadow: 0 0 16px rgba(16, 185, 129, 0.45);
        }

        .event-minute-badge.badge-card-yellow {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            color: #ffffff;
            border-color: #fde68a;
            box-shadow: 0 0 14px rgba(245, 158, 11, 0.4);
        }

        .event-minute-badge.badge-card-red {
            background: linear-gradient(135deg, #b91c1c 0%, #ef4444 100%);
            color: #ffffff;
            border-color: #fecaca;
            box-shadow: 0 0 16px rgba(239, 68, 68, 0.45);
        }

        .event-minute-badge.badge-sub {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            color: #ffffff;
            border-color: #bae6fd;
            box-shadow: 0 0 14px rgba(2, 132, 199, 0.35);
        }

        /* Thẻ nội dung sự kiện */
        .event-card {
            background: #ffffff;
            border: 1px solid var(--pl-border);
            border-radius: 16px;
            padding: 14px 18px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.05);
            width: 100%;
            transition: all 0.25s ease;
            position: relative;
        }

        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
        }

        .timeline-row.home-event .event-card {
            border-left: 4px solid var(--pl-purple);
        }

        .timeline-row.away-event .event-card {
            border-right: 4px solid var(--pl-pink);
        }

        .event-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #f1f5f9;
        }

        .event-team-meta {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.775rem;
            font-weight: 800;
            color: var(--pl-text-dark);
        }

        .event-team-meta img {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        .event-badge-tag {
            font-size: 0.7rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.03em;
        }

        .badge-tag-goal {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .badge-tag-card-yellow {
            background: rgba(245, 158, 11, 0.12);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .badge-tag-card-red {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        .badge-tag-sub {
            background: rgba(2, 132, 199, 0.1);
            color: #0284c7;
            border: 1px solid rgba(2, 132, 199, 0.25);
        }

        .event-body-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Avatar khuôn mặt cầu thủ trong timeline */
        .tl-player-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .tl-player-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
        }

        .tl-player-num {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--pl-purple);
            color: #ffffff;
            font-size: 0.6rem;
            font-weight: 900;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ffffff;
        }

        .event-player-name {
            font-weight: 800;
            font-size: 0.95rem;
            color: var(--pl-text-dark);
            line-height: 1.3;
        }

        .event-score-tag {
            color: var(--pl-pink);
            font-weight: 900;
            background: rgba(233, 0, 82, 0.08);
            padding: 2px 7px;
            border-radius: 6px;
            margin-left: 6px;
            font-size: 0.85rem;
        }

        .event-detail-desc {
            font-size: 0.775rem;
            color: var(--pl-text-muted);
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .event-assist-pill {
            background: #f1f5f9;
            color: #334155;
            padding: 2px 7px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Thay người 2 hàng In/Out */
        .tl-sub-duo {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .tl-sub-row {
            display: flex;
            align-items: center;
            gap: 9px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--pl-text-dark);
        }

        .tl-sub-face {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            flex-shrink: 0;
        }

        .tl-sub-action-badge {
            font-size: 0.7rem;
            font-weight: 900;
            padding: 2px 6px;
            border-radius: 4px;
            letter-spacing: 0.04em;
        }

        .sub-badge-in {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
        }

        .sub-badge-out {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }

        /* =====================================================================
           TAB 2: TACTICAL 2D PITCH (SÂN BÓNG 2D ĐỈNH CAO)
           ===================================================================== */
        .pitch-wrapper-card {
            background: var(--pl-card-bg);
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 28px 24px;
            box-shadow: var(--shadow-card);
        }

        .pitch-legend-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 12px 18px;
            background: #f8fafc;
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
        }

        .pitch-legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .formation-pill {
            background: #ffffff;
            border: 1px solid var(--pl-border);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.775rem;
            color: var(--pl-purple);
            font-weight: 800;
        }

        /* Sân Cỏ 2D Chuyên Nghiệp */
        .pitch-2d {
            width: 100%;
            max-width: 600px;
            height: 760px;
            margin: 0 auto 30px;
            background: radial-gradient(ellipse at 50% 50%, #1e7e34 0%, #155e27 60%, #0d441c 100%);
            border: 3px solid rgba(255, 255, 255, 0.85);
            border-radius: 16px;
            position: relative;
            box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.4), 0 16px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: 16px 0;
        }

        /* Hiệu ứng sọc cỏ cắt thẩm mỹ */
        .pitch-2d::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: repeating-linear-gradient(
                180deg,
                rgba(255, 255, 255, 0.03) 0px,
                rgba(255, 255, 255, 0.03) 38px,
                transparent 38px,
                transparent 76px
            );
            pointer-events: none;
            z-index: 1;
        }

        /* Vạch vôi sân bóng */
        .pitch-line-center {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.75);
            transform: translateY(-50%);
            z-index: 1;
        }

        .pitch-circle-center {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 120px;
            height: 120px;
            border: 2px solid rgba(255, 255, 255, 0.75);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
        }

        .pitch-spot-center {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
        }

        /* Khung thành & Vòng cấm Đội nhà (Top) */
        .pitch-box-top {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 220px;
            height: 90px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.75);
            border-left: 2px solid rgba(255, 255, 255, 0.75);
            border-right: 2px solid rgba(255, 255, 255, 0.75);
            z-index: 1;
        }

        .pitch-goalarea-top {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 110px;
            height: 40px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.75);
            border-left: 2px solid rgba(255, 255, 255, 0.75);
            border-right: 2px solid rgba(255, 255, 255, 0.75);
            z-index: 1;
        }

        /* Khung thành & Vòng cấm Đội khách (Bottom) */
        .pitch-box-bottom {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 220px;
            height: 90px;
            border-top: 2px solid rgba(255, 255, 255, 0.75);
            border-left: 2px solid rgba(255, 255, 255, 0.75);
            border-right: 2px solid rgba(255, 255, 255, 0.75);
            z-index: 1;
        }

        .pitch-goalarea-bottom {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 110px;
            height: 40px;
            border-top: 2px solid rgba(255, 255, 255, 0.75);
            border-left: 2px solid rgba(255, 255, 255, 0.75);
            border-right: 2px solid rgba(255, 255, 255, 0.75);
            z-index: 1;
        }

        /* Bố trí 2 Nửa Sân */
        .pitch-half {
            height: 48%;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            position: relative;
            z-index: 2;
        }

        .pitch-row {
            display: flex;
            align-items: center;
            justify-content: space-around;
            width: 100%;
        }

        /* Cầu thủ trên sân bóng: Hiển thị mặt + số áo + tên */
        .pitch-player-pin {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            user-select: none;
        }

        .pitch-player-pin:hover {
            transform: scale(1.22);
            z-index: 20;
        }

        .player-avatar-coin {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #ffffff;
            border: 2.5px solid #ffffff;
            position: relative;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
        }

        .shirt-home-border {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.8), 0 6px 14px rgba(0, 0, 0, 0.4);
        }

        .shirt-away-border {
            border-color: #e11d48;
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.85), 0 6px 14px rgba(0, 0, 0, 0.4);
        }

        .player-face-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            object-position: top center;
            background: #e2e8f0;
            display: block;
        }

        .player-num-badge {
            position: absolute;
            bottom: -4px;
            right: -4px;
            background: #0f172a;
            color: #ffffff;
            border-radius: var(--radius-full);
            font-size: 0.65rem;
            font-weight: 900;
            padding: 1px 5px;
            border: 1.5px solid #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.35);
            line-height: 1.2;
        }

        .shirt-home-border .player-num-badge {
            background: #2563eb;
            color: #ffffff;
        }

        .shirt-away-border .player-num-badge {
            background: #e11d48;
            color: #ffffff;
        }

        .player-captain-tag {
            position: absolute;
            top: -4px;
            left: -4px;
            background: #f59e0b;
            color: #ffffff;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 0.625rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1.5px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .player-name-tag {
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(6px);
            color: #ffffff;
            font-size: 0.725rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: var(--radius-full);
            margin-top: 5px;
            max-width: 95px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.2);
            letter-spacing: -0.01em;
        }

        /* Danh Sách Dự Bị (Substitutes) - Thiết kế dạng Thẻ Cầu Thủ Tinh Gọn */
        .bench-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        @media (max-width: 640px) {
            .bench-grid {
                grid-template-columns: 1fr;
            }
        }

        .bench-col {
            background: #ffffff;
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 18px;
            box-shadow: var(--shadow-subtle);
        }

        .bench-header {
            font-size: 0.925rem;
            font-weight: 800;
            margin-bottom: 14px;
            color: var(--pl-purple);
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 1.5px solid #f1f5f9;
        }

        .bench-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px;
            margin-bottom: 6px;
            border-radius: var(--radius-sm);
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease;
            font-size: 0.85rem;
        }

        .bench-item:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            transform: translateX(3px);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        }

        .bench-shirt {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.725rem;
            color: #38bdf8;
            flex-shrink: 0;
        }

        .bench-face-img {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            object-position: top center;
            background: #e2e8f0;
            border: 2px solid #ffffff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }

        /* =====================================================================
           TAB 3: THỐNG KÊ (STATS)
           ===================================================================== */
        .stats-card {
            background: var(--pl-card-bg);
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 28px 24px;
            box-shadow: var(--shadow-card);
        }

        .stats-row {
            margin-bottom: 22px;
        }

        .stats-header-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.95rem;
            font-weight: 800;
        }

        .stat-val-home {
            color: var(--pl-purple);
            font-size: 1.05rem;
        }

        .stat-val-away {
            color: var(--pl-pink);
            font-size: 1.05rem;
        }

        .stat-title {
            color: var(--pl-text-muted);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .dual-progress-bar {
            width: 100%;
            height: 9px;
            background: #f1f5f9;
            border-radius: var(--radius-full);
            display: flex;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .bar-fill-home {
            background: linear-gradient(90deg, #1d4ed8, #3b82f6);
            height: 100%;
            border-radius: var(--radius-full) 0 0 var(--radius-full);
            transition: width 0.6s ease;
        }

        .bar-fill-away {
            background: linear-gradient(90deg, #e11d48, #f43f5e);
            height: 100%;
            border-radius: 0 var(--radius-full) var(--radius-full) 0;
            transition: width 0.6s ease;
        }

        /* =====================================================================
           AI ASSISTANT CARD (FUTURISTIC INTELLIGENCE)
           ===================================================================== */
        .ai-assistant-card {
            background: linear-gradient(145deg, #090d16 0%, #0f172a 50%, #1e293b 100%);
            border: 1px solid rgba(56, 189, 248, 0.35);
            border-radius: var(--radius-md);
            padding: 24px;
            color: #ffffff;
            box-shadow: 0 12px 30px rgba(2, 6, 23, 0.5), inset 0 0 20px rgba(56, 189, 248, 0.08);
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .ai-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .ai-brand-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ai-icon-bubble {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--pl-pink) 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(233, 0, 82, 0.4);
        }

        .ai-badge-label {
            font-size: 0.725rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--pl-mint);
            display: block;
        }

        .ai-card-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.25;
        }

        .ai-source-tag {
            font-size: 0.75rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.7);
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 10px;
            border-radius: var(--radius-full);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .ai-content-paragraph {
            font-size: 0.925rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .ai-highlights-box {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px 18px;
        }

        .highlights-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--pl-mint);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
        }

        .ai-highlights-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .ai-highlights-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.92);
            line-height: 1.45;
        }

        .highlight-check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(0, 255, 133, 0.2);
            color: var(--pl-mint);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 900;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* =====================================================================
           COMMUNITY VOTING WIDGET (BÌNH CHỌN DỰ ĐOÁN)
           ===================================================================== */
        .community-voting-card {
            background: #ffffff;
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 24px;
            box-shadow: var(--shadow-card);
            margin-bottom: 24px;
        }

        .voting-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .voting-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(233, 0, 82, 0.08);
            border: 1px solid rgba(233, 0, 82, 0.2);
            color: var(--pl-pink);
            font-size: 0.775rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            margin-bottom: 6px;
        }

        .voting-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--pl-purple);
        }

        .voting-total-counter {
            font-size: 0.85rem;
            color: var(--pl-text-muted);
        }

        .voting-total-counter strong {
            color: var(--pl-purple);
        }

        .voting-buttons-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .btn-vote-choice {
            background: #f8fafc;
            border: 1.5px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 14px 12px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            outline: none;
        }

        .btn-vote-choice:hover:not(:disabled) {
            transform: translateY(-2px);
            border-color: var(--pl-purple);
            background: #ffffff;
            box-shadow: 0 4px 16px rgba(56, 0, 60, 0.08);
        }

        .btn-vote-choice.active-voted {
            border-color: var(--pl-pink);
            background: #fff1f2;
            box-shadow: 0 4px 16px rgba(233, 0, 82, 0.15);
        }

        .btn-vote-choice:disabled {
            cursor: default;
        }

        .vote-team-name {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--pl-text-dark);
            text-align: center;
        }

        .vote-percent-tag {
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--pl-purple);
        }

        .btn-vote-choice.active-voted .vote-percent-tag {
            color: var(--pl-pink);
        }

        .voting-progress-bar-wrap {
            margin-top: 14px;
        }

        .voting-multi-bar {
            width: 100%;
            height: 10px;
            border-radius: var(--radius-full);
            background: #f1f5f9;
            overflow: hidden;
            display: flex;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.06);
        }

        .bar-segment {
            height: 100%;
            transition: width 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .segment-home {
            background: var(--pl-purple);
        }

        .segment-draw {
            background: #94a3b8;
        }

        .segment-away {
            background: var(--pl-pink);
        }

        .voting-legend-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            font-size: 0.775rem;
            color: var(--pl-text-muted);
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }

        .legend-home .legend-dot { background: var(--pl-purple); }
        .legend-draw .legend-dot { background: #94a3b8; }
        .legend-away .legend-dot { background: var(--pl-pink); }

        .voting-feedback-msg {
            margin-top: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--pl-mint-dark);
            text-align: center;
        }

        /* Footer */
        .site-footer {
            background-color: #ffffff;
            border-top: 1px solid var(--pl-border);
            padding: 30px 0;
            margin-top: auto;
            color: var(--pl-text-muted);
            font-size: 0.85rem;
        }

        .footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Responsive Mobile */
        @media (max-width: 640px) {
            .hero-crest-wrap {
                width: 68px;
                height: 68px;
                padding: 8px;
            }
            .hero-team-logo {
                width: 50px;
                height: 50px;
            }
            .hero-team-name {
                font-size: 1.05rem;
            }
            .hero-score-display {
                font-size: 2.75rem;
            }
            .hero-score-vs {
                font-size: 1.7rem;
            }
            .pitch-2d {
                height: 620px;
            }
            .player-avatar-coin {
                width: 38px;
                height: 38px;
            }
            .player-name-tag {
                font-size: 0.625rem;
                max-width: 70px;
                padding: 1px 5px;
            }
            .player-num-badge {
                font-size: 0.55rem;
                padding: 0 3px;
                bottom: -3px;
                right: -3px;
            }
            .bench-grid {
                grid-template-columns: 1fr;
            }
            .voting-buttons-grid {
                grid-template-columns: 1fr;
            }
            .timeline-stream::before {
                left: 22px;
            }
            .event-minute-badge {
                left: 22px;
                transform: none;
            }
            .timeline-row:hover .event-minute-badge {
                transform: scale(1.05);
            }
            .timeline-period-divider {
                justify-content: flex-start;
                padding-left: 54px;
            }
            .timeline-row.home-event, .timeline-row.away-event {
                padding-left: 56px;
                padding-right: 0;
                justify-content: flex-start;
                text-align: left;
            }
            .timeline-row.away-event .event-card {
                border-right: 1px solid var(--pl-border);
                border-left: 4px solid var(--pl-pink);
            }
        }

        /* Phase 5: Realtime Goal Flash, Sound Toggle & Toast Notification */
        @keyframes goalFlashAnimation {
            0% {
                background-color: #fef08a !important;
                color: #854d0e !important;
                box-shadow: 0 0 25px rgba(250, 204, 21, 0.95);
                transform: scale(1.1);
            }
            20% {
                background-color: #fde047 !important;
                color: #713f12 !important;
                box-shadow: 0 0 35px rgba(250, 204, 21, 1);
                transform: scale(1.15);
            }
            50% {
                background-color: #fef9c3 !important;
                color: #854d0e !important;
                box-shadow: 0 0 20px rgba(250, 204, 21, 0.6);
            }
            100% {
                background-color: transparent !important;
                color: inherit !important;
                box-shadow: none;
                transform: scale(1);
            }
        }

        .flash-highlight-goal {
            animation: goalFlashAnimation 2.5s cubic-bezier(0.25, 1, 0.5, 1) forwards !important;
            border-radius: 8px !important;
            display: inline-block !important;
            padding: 2px 8px !important;
            transition: all 0.3s ease;
        }

        .goal-sound-toggle-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--pl-purple) 0%, var(--pl-purple-light) 100%);
            color: #ffffff;
            border: 2px solid rgba(0, 255, 135, 0.6);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25), 0 0 15px rgba(0, 255, 135, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 99999;
            font-size: 1.4rem;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            user-select: none;
            outline: none;
        }

        .goal-sound-toggle-btn:hover {
            transform: scale(1.12);
            border-color: var(--pl-mint);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35), 0 0 22px rgba(0, 255, 135, 0.5);
        }

        .goal-sound-toggle-btn.muted {
            background: #475569;
            color: #cbd5e1;
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        }

        .goal-sound-toggle-btn .sound-tooltip {
            position: absolute;
            bottom: 58px;
            right: 0;
            background: rgba(15, 23, 42, 0.95);
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transform: translateY(6px);
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }

        .goal-sound-toggle-btn:hover .sound-tooltip {
            opacity: 1;
            transform: translateY(0);
        }

        .goal-toast-notification {
            position: fixed;
            top: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(-40px);
            background: linear-gradient(135deg, #020617 0%, #0f172a 100%);
            border: 2px solid #10b981;
            box-shadow: 0 16px 40px rgba(2, 6, 23, 0.6), 0 0 30px rgba(16, 185, 129, 0.35);
            padding: 14px 22px;
            border-radius: var(--radius-md);
            color: #ffffff;
            z-index: 100000;
            display: flex;
            align-items: center;
            gap: 16px;
            opacity: 0;
            pointer-events: none;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-width: 90vw;
        }

        .goal-toast-notification.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
            pointer-events: auto;
        }

        .goal-toast-notification .toast-icon {
            font-size: 2rem;
            line-height: 1;
            animation: bounce 1s infinite alternate;
        }

        @keyframes bounce {
            from { transform: translateY(0) scale(1); }
            to { transform: translateY(-4px) scale(1.1); }
        }

        .goal-toast-notification .toast-body {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .goal-toast-notification .toast-heading {
            font-size: 0.75rem;
            font-weight: 900;
            letter-spacing: 0.1em;
            color: var(--pl-mint);
            text-transform: uppercase;
        }

        .goal-toast-notification .toast-match {
            font-size: 1rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .goal-toast-notification .toast-score {
            background: var(--pl-pink);
            color: #fff;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 900;
        }

        .goal-toast-notification .toast-scorer {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 2px;
        }

        .goal-toast-notification .toast-close {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.5rem;
            cursor: pointer;
            line-height: 1;
            padding: 4px;
            margin-left: 8px;
            transition: color 0.2s;
        }

        .goal-toast-notification .toast-close:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    {{-- Top Header --}}
    <header class="site-header">
        <div class="container header-content">
            <a href="{{ route('home') }}" class="brand-logo">
                <div class="brand-badge">PL</div>
                <div class="brand-text">PREMIER<span>LEAGUE</span></div>
            </a>
            <nav class="header-nav">
                <a href="{{ route('home') }}" class="header-link">Lịch Đấu</a>
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

            {{-- Breadcrumb --}}
            <div class="breadcrumb-nav">
                <a href="{{ route('home') }}">&larr; Lịch thi đấu</a>
                <span>/</span>
                <span>Trung tâm trận đấu</span>
            </div>

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
                        <h2 class="hero-team-name">{{ $homeTeam['name'] ?? 'Đội nhà' }}</h2>

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
                        <h2 class="hero-team-name">{{ $awayTeam['name'] ?? 'Đội khách' }}</h2>

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
                    <svg class="svg-icon-inline" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    Diễn Biến (Timeline)
                </button>
                <button class="match-tab-btn" data-target="tab-pitch">
                    <svg class="svg-icon-inline" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="3" y1="12" x2="21" y2="12"></line><circle cx="12" cy="12" r="3"></circle></svg>
                    Đội Hình Ra Sân (Lineups 2D)
                </button>
                <button class="match-tab-btn" data-target="tab-stats">
                    <svg class="svg-icon-inline" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    Thống Kê (Stats)
                </button>
                <button class="match-tab-btn" data-target="tab-ai">
                    <svg class="svg-icon-inline" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"></path></svg>
                    AI Nhận Định
                </button>
                <button class="match-tab-btn" data-target="tab-voting">
                    <svg class="svg-icon-inline" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="m9 12 2 2 4-4"></path><path d="M5 7c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v12H5V7Z"></path><path d="M22 19H2"></path></svg>
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
                                                        <img src="{{ $event['teamBadge'] }}" alt="" loading="lazy">
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
                                <img src="{{ $lineups['home']['badge'] }}" alt="" style="width: 26px; height: 26px; object-fit: contain;" onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                                <span style="color: var(--pl-purple);">{{ $lineups['home']['name'] }}</span>
                                <span class="formation-pill">{{ $lineups['home']['formation'] }}</span>
                            </div>
                            <div class="pitch-legend-item">
                                <span class="formation-pill">{{ $lineups['away']['formation'] }}</span>
                                <span style="color: var(--pl-pink);">{{ $lineups['away']['name'] }}</span>
                                <img src="{{ $lineups['away']['badge'] }}" alt="" style="width: 26px; height: 26px; object-fit: contain;" onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
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
                                    <img src="{{ $lineups['home']['badge'] }}" alt="" style="width: 22px; height: 22px; object-fit: contain;" onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                                    <span>{{ $lineups['home']['name'] }}</span>
                                </div>
                                @forelse($lineups['home']['substitutes'] as $sub)
                                    <div class="bench-item">
                                        <img src="{{ $sub['photo'] ?? '' }}" 
                                             alt="{{ $sub['name'] }}" 
                                             class="bench-face-img" 
                                             loading="lazy"
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
                                    <img src="{{ $lineups['away']['badge'] }}" alt="" style="width: 22px; height: 22px; object-fit: contain;" onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                                    <span>{{ $lineups['away']['name'] }}</span>
                                </div>
                                @forelse($lineups['away']['substitutes'] as $sub)
                                    <div class="bench-item">
                                        <img src="{{ $sub['photo'] ?? '' }}" 
                                             alt="{{ $sub['name'] }}" 
                                             class="bench-face-img" 
                                             loading="lazy"
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

            {{-- PHASE 8: Banner Quảng Cáo Chân Trang (banner_footer) --}}
            @if(isset($bannerFooter) && $bannerFooter && $bannerFooter->rendered_html)
                <div class="ad-banner-slot banner-footer" style="margin-top: 24px; margin-bottom: 8px;">
                    {!! $bannerFooter->rendered_html !!}
                </div>
            @endif
        </div>
    </main>

    {{-- Footer --}}
    <footer class="site-footer">
        <div class="container footer-content">
            <div>
                <strong>Premier League Match Center &copy; {{ date('Y') }}</strong>
                <p style="opacity: 0.8; font-size: 0.775rem; margin-top: 2px;">Dữ liệu chính thức từ PulseLive API &bull; Cập nhật liên tục</p>
            </div>
            <div style="display: flex; gap: 14px;">
                <a href="{{ route('home') }}" style="color: var(--pl-purple); font-weight: 700;">Lịch đấu</a>
                <a href="{{ route('standings') }}" style="color: var(--pl-purple); font-weight: 700;">Bảng xếp hạng</a>
                <a href="{{ route('live') }}" style="color: var(--pl-purple); font-weight: 700;">Trực tiếp</a>
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
