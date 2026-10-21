<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Lịch Thi Đấu & Kết Quả Ngoại Hạng Anh hôm nay</title>
    <meta name="description" content="Xem lịch thi đấu, tỷ số trực tiếp và kết quả bóng đá Ngoại Hạng Anh. Cập nhật tỷ số từng phút, đội hình và diễn biến trận đấu.">
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
    <meta property="og:title" content="Lịch Thi Đấu & Kết Quả Ngoại Hạng Anh Ngày {{ $dateNav['formattedSelected'] }}">
    <meta property="og:description" content="Xem lịch thi đấu, tỷ số trực tiếp và kết quả bóng đá Ngoại Hạng Anh ngày {{ $dateNav['formattedSelected'] }}.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:site_name" content="Premier League Live Tracker">

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

    {{-- CSS Nội Bộ Theo Tông Màu Chủ Đạo Ngoại Hạng Anh (Mobile-First) --}}
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
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
            --shadow-card: 0 4px 16px rgba(15, 23, 42, 0.06);
            --shadow-hover: 0 8px 24px rgba(15, 23, 42, 0.12);
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
            max-width: 1080px;
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

        /* Main Container */
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

        /* Banner Quảng Cáo Tài Trợ */
        .ad-banner-slot {
            width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 14px 16px;
            text-align: center;
            box-shadow: var(--shadow-card);
            position: relative;
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .ad-banner-slot:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
        }

        .ad-banner-slot img {
            max-width: 100%;
            height: auto;
            border-radius: var(--radius-md);
            display: inline-block;
            vertical-align: middle;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(233, 0, 82, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
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

        /* Date Navigation Bar */
        .date-nav-card {
            background: #ffffff;
            border-radius: var(--radius-md);
            padding: 16px 20px;
            border: 1px solid var(--pl-border);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }

        .date-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            max-width: 100%;
        }

        .date-btn {
            padding: 8px 18px;
            border-radius: var(--radius-full);
            font-weight: 700;
            font-size: 0.875rem;
            background: #ffffff;
            color: var(--pl-text-dark);
            border: 1px solid var(--pl-border);
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .date-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: var(--pl-purple);
        }

        .date-btn.active {
            background: var(--pl-purple);
            color: #ffffff;
            border-color: var(--pl-purple);
            box-shadow: 0 4px 12px rgba(56, 0, 60, 0.22);
        }

        .datepicker-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid var(--pl-border);
            padding: 6px 14px;
            border-radius: var(--radius-full);
        }

        .datepicker-input {
            border: none;
            background: transparent;
            font-family: inherit;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--pl-text-dark);
            outline: none;
            cursor: pointer;
        }

        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow-x: auto;
            margin-bottom: 20px;
            padding-bottom: 4px;
        }

        .filter-tab {
            padding: 8px 18px;
            border-radius: var(--radius-full);
            font-size: 0.875rem;
            font-weight: 700;
            background: #ffffff;
            color: var(--pl-text-muted);
            border: 1px solid var(--pl-border);
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .filter-tab:hover {
            color: var(--pl-purple);
            border-color: #cbd5e1;
        }

        .filter-tab.active {
            background: var(--pl-purple);
            color: #ffffff;
            border-color: var(--pl-purple);
            box-shadow: 0 4px 12px rgba(56, 0, 60, 0.2);
        }

        .filter-tab:hover {
            color: var(--pl-purple);
            border-color: #cbd5e1;
        }

        .filter-tab.active {
            background: var(--pl-pink);
            color: #ffffff;
            border-color: var(--pl-pink);
            box-shadow: 0 4px 12px rgba(233, 0, 82, 0.25);
        }

        /* Match Cards Grid */
        .matches-container {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .match-card {
            background: var(--pl-card-bg);
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 18px 20px;
            box-shadow: var(--shadow-card);
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.25s ease;
            position: relative;
        }

        .match-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            border-color: #cbd5e1;
        }

        .match-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.775rem;
            color: var(--pl-text-muted);
        }

        .match-venue {
            display: flex;
            align-items: center;
            gap: 4px;
            max-width: 65%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Match Status Badges */
        .status-badge {
            padding: 3px 10px;
            border-radius: var(--radius-full);
            font-weight: 800;
            font-size: 0.725rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-tv-guide {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: var(--radius-sm);
            font-size: 0.725rem;
            font-weight: 800;
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            white-space: nowrap;
        }

        .badge-tv-guide svg {
            width: 12px;
            height: 12px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2.2;
        }

        .badge-live {
            background: rgba(233, 0, 82, 0.1);
            color: var(--pl-pink);
            border: 1px solid rgba(233, 0, 82, 0.25);
        }

        .badge-ft {
            background: #f1f5f9;
            color: var(--pl-text-muted);
            border: 1px solid var(--pl-border);
        }

        .badge-upcoming {
            background: rgba(0, 184, 96, 0.1);
            color: var(--pl-mint-dark);
            border: 1px solid rgba(0, 184, 96, 0.25);
        }

        /* Live Pulse Dot */
        .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--pl-pink);
            animation: pulse-animation 1.2s infinite;
        }

        @keyframes pulse-animation {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.3); opacity: 1; box-shadow: 0 0 6px var(--pl-pink); }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        /* Live Minute Flashing */
        .live-clock-flash {
            color: var(--pl-pink);
            font-weight: 800;
            animation: text-flash 1.2s infinite;
        }

        @keyframes text-flash {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        /* Match Teams Layout */
        .match-main-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .team-col {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .team-col.home {
            justify-content: flex-end;
            text-align: right;
        }

        .team-col.away {
            justify-content: flex-start;
            text-align: left;
        }

        .team-logo {
            width: 44px;
            height: 44px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
            flex-shrink: 0;
        }

        .team-name {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--pl-text-dark);
        }

        /* Center Score / Time Box */
        .score-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 100px;
            text-align: center;
        }

        .score-value {
            font-size: 1.6rem;
            font-weight: 900;
            letter-spacing: 0.05em;
            color: var(--pl-purple);
            background: #f8fafc;
            padding: 4px 14px;
            border-radius: 8px;
            border: 1px solid var(--pl-border);
            display: inline-block;
        }

        .kickoff-time {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--pl-purple);
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 6px;
        }

        /* Empty State */
        .empty-state {
            background: var(--pl-card-bg);
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 48px 24px;
            text-align: center;
            box-shadow: var(--shadow-card);
        }

        .empty-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--pl-text-dark);
            margin-bottom: 8px;
        }

        .empty-desc {
            font-size: 0.9rem;
            color: var(--pl-text-muted);
            max-width: 460px;
            margin: 0 auto 20px;
        }

        .empty-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
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

        /* Responsive Mobile Styles */
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

            .date-nav-card {
                flex-direction: column;
                align-items: stretch;
                padding: 12px;
                gap: 10px;
            }

            .date-buttons {
                justify-content: space-between;
                width: 100%;
                gap: 6px;
            }

            .date-btn {
                flex: 1;
                text-align: center;
                padding: 8px 6px;
                font-size: 0.775rem;
            }

            .datepicker-form {
                width: 100%;
                justify-content: center;
                background: #f8fafc;
                padding: 6px 12px;
                border-radius: var(--radius-full);
                border: 1px solid var(--pl-border);
            }

            .filter-tabs {
                gap: 6px;
                padding-bottom: 6px;
                margin-bottom: 14px;
            }

            .filter-tab {
                padding: 6px 12px;
                font-size: 0.775rem;
            }

            .matches-container {
                gap: 10px;
            }

            .match-card {
                padding: 14px 12px;
                border-radius: 12px;
            }

            .match-card-top {
                flex-wrap: wrap;
                gap: 6px;
                margin-bottom: 10px;
            }

            .match-venue {
                max-width: 52%;
                font-size: 0.725rem;
            }

            .badge-tv-guide {
                font-size: 0.675rem;
                padding: 2px 6px;
            }

            .match-main-row {
                gap: 6px;
            }

            .team-col {
                gap: 6px;
                min-width: 0;
            }

            .team-name {
                font-size: 0.8rem;
                font-weight: 700;
                line-height: 1.25;
            }

            .team-logo {
                width: 30px;
                height: 30px;
            }

            .score-box {
                min-width: 66px;
            }

            .score-value {
                font-size: 1.2rem;
                padding: 2px 8px;
            }

            .kickoff-time {
                font-size: 0.9rem;
                padding: 3px 8px;
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

            .goal-sound-toggle-btn {
                bottom: 80px !important;
                right: 16px !important;
                width: 44px !important;
                height: 44px !important;
                font-size: 1.2rem !important;
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
    {{-- Header --}}
    <header class="site-header">
        <div class="container header-content">
            <a href="{{ route('home') }}" class="brand-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo tysongoaihang" class="brand-logo-img" width="38" height="38">
                <div class="brand-text">PREMIER<span>LEAGUE</span></div>
            </a>
            <nav class="header-nav">
                <a href="{{ route('home') }}" class="header-link active">Lịch Đấu</a>
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
                <h1 class="hero-title">Lịch Thi Đấu & Kết Quả Ngoại Hạng Anh</h1>
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
                    {{ $dateNav['dayOfWeek'] }}, ngày {{ $dateNav['formattedSelected'] }}
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
                                    <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
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
                                <button type="button" class="btn-favorite-team" data-favorite-team="{{ $homeId }}" title="Ghim đội yêu thích">
                                    <svg class="star-icon" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
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
                                <button type="button" class="btn-favorite-team" data-favorite-team="{{ $awayId }}" title="Ghim đội yêu thích">
                                    <svg class="star-icon" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
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
