<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $seo['title'] ?? 'Chi Tiết Trận Đấu | Premier League Match Center' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Xem trực tiếp diễn biến trận đấu, đội hình ra sân 2D và thống kê chi tiết giải Ngoại Hạng Anh.' }}">
    <link rel="canonical" href="{{ url()->current() }}">

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

    {{-- CSS Nội Bộ Hoàn Chỉnh: Match Center & Sân Bóng 2D (Mobile-First) --}}
    <style>
        :root {
            --pl-purple: #38003c;
            --pl-purple-light: #52055a;
            --pl-pink: #e90052;
            --pl-mint: #00ff85;
            --pl-mint-dark: #00b860;
            --pl-bg: #f4f5f8;
            --pl-card-bg: #ffffff;
            --pl-text-dark: #1f0426;
            --pl-text-muted: #64748b;
            --pl-border: #e2e8f0;
            --pitch-grass-1: #247530;
            --pitch-grass-2: #1e6428;
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

        .brand-badge {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--pl-purple) 0%, var(--pl-pink) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 900;
            font-size: 0.95rem;
            box-shadow: 0 3px 8px rgba(233, 0, 82, 0.25);
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
            padding: 20px 0 48px;
        }

        .breadcrumb-nav {
            margin-bottom: 16px;
            font-size: 0.85rem;
            color: var(--pl-text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .breadcrumb-nav a:hover {
            color: var(--pl-purple);
            text-decoration: underline;
        }

        /* Match Header Hero Card */
        .match-hero-card {
            background: linear-gradient(135deg, #38003c 0%, #6b21a8 50%, #db2777 100%);
            border-radius: var(--radius-lg);
            padding: 32px 24px;
            color: #ffffff;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(107, 33, 168, 0.2);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-competition-tag {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            color: var(--pl-mint);
            padding: 4px 14px;
            border-radius: var(--radius-full);
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 20px;
        }

        .hero-scoreboard {
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 16px;
            margin-bottom: 24px;
        }

        .hero-team {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 35%;
            gap: 10px;
        }

        .hero-team-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.3));
        }

        .hero-team-name {
            font-size: 1.25rem;
            font-weight: 900;
            letter-spacing: -0.01em;
            color: #ffffff;
        }

        .hero-score-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 30%;
        }

        .hero-score-display {
            font-size: 3rem;
            font-weight: 900;
            letter-spacing: 0.05em;
            color: #ffffff;
            line-height: 1;
            margin-bottom: 8px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .hero-status-pill {
            padding: 4px 14px;
            border-radius: var(--radius-full);
            font-weight: 800;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .pill-live {
            background: var(--pl-pink);
            color: #ffffff;
            box-shadow: 0 0 12px rgba(233, 0, 82, 0.5);
            animation: pulse-badge 1.2s infinite;
        }

        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .pill-ft {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        .pill-upcoming {
            background: var(--pl-mint);
            color: var(--pl-purple);
        }

        .hero-info-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            font-size: 0.85rem;
            color: #e2e8f0;
            flex-wrap: wrap;
        }

        /* Match Tabs Navigation */
        .match-nav-tabs {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--pl-card-bg);
            border-radius: var(--radius-md);
            padding: 6px;
            border: 1px solid var(--pl-border);
            box-shadow: var(--shadow-card);
            margin-bottom: 24px;
            overflow-x: auto;
        }

        .match-tab-btn {
            flex: 1;
            text-align: center;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.925rem;
            color: var(--pl-text-muted);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .match-tab-btn:hover {
            color: var(--pl-purple);
            background: #f8fafc;
        }

        .match-tab-btn.active {
            background: var(--pl-purple);
            color: var(--pl-mint);
            box-shadow: 0 4px 12px rgba(56, 0, 60, 0.15);
        }

        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block;
        }

        /* TAB 1: TIMELINE (DIỄN BIẾN) */
        .timeline-card {
            background: var(--pl-card-bg);
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 24px;
            box-shadow: var(--shadow-card);
        }

        .timeline-list {
            position: relative;
            padding: 20px 0;
            max-width: 720px;
            margin: 0 auto;
        }

        .timeline-list::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 2px;
            background: var(--pl-border);
            transform: translateX(-50%);
        }

        .timeline-event-row {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            position: relative;
            width: 100%;
        }

        .timeline-event-row.event-home {
            justify-content: flex-start;
            padding-right: 54%;
        }

        .timeline-event-row.event-away {
            justify-content: flex-end;
            padding-left: 54%;
        }

        .event-minute-circle {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 38px;
            height: 38px;
            background: var(--pl-purple);
            color: var(--pl-mint);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.775rem;
            border: 3px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            z-index: 2;
        }

        .event-card-bubble {
            background: #ffffff;
            border: 1px solid var(--pl-border);
            border-radius: 12px;
            padding: 10px 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 100%;
        }

        .event-icon-badge {
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .event-player-title {
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--pl-text-dark);
        }

        .event-sub-desc {
            font-size: 0.775rem;
            color: var(--pl-text-muted);
        }

        /* TAB 2: SÂN BÓNG 2D (LINEUP PITCH) BẰNG CSS THUẦN */
        .pitch-wrapper-card {
            background: var(--pl-card-bg);
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 24px;
            box-shadow: var(--shadow-card);
        }

        .pitch-legend-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .pitch-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            font-size: 0.95rem;
        }

        /* Sân bóng 2D CSS Thuần */
        .pitch-2d {
            width: 100%;
            max-width: 640px;
            height: 780px;
            margin: 0 auto 30px;
            background: repeating-linear-gradient(
                180deg,
                var(--pitch-grass-1),
                var(--pitch-grass-1) 39px,
                var(--pitch-grass-2) 39px,
                var(--pitch-grass-2) 78px
            );
            border: 3px solid rgba(255, 255, 255, 0.85);
            border-radius: 12px;
            position: relative;
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.35), 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: 16px 0;
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

        /* Cầu thủ trên sân bóng */
        .pitch-player-pin {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .pitch-player-pin:hover {
            transform: scale(1.15);
        }

        .shirt-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.8rem;
            border: 2px solid #ffffff;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
        }

        .shirt-home-color {
            background: var(--pl-purple);
            color: var(--pl-mint);
        }

        .shirt-away-color {
            background: var(--pl-pink);
            color: #ffffff;
        }

        .player-name-tag {
            background: rgba(15, 23, 42, 0.75);
            color: #ffffff;
            font-size: 0.675rem;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
            margin-top: 3px;
            max-width: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
        }

        /* Danh Sách Dự Bị (Substitutes) */
        .bench-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 24px;
        }

        .bench-col {
            background: #f8fafc;
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 16px;
        }

        .bench-header {
            font-size: 0.9rem;
            font-weight: 800;
            margin-bottom: 12px;
            color: var(--pl-purple);
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--pl-border);
        }

        .bench-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.825rem;
        }

        .bench-shirt {
            width: 22px;
            height: 22px;
            border-radius: 4px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.7rem;
            color: var(--pl-purple);
        }

        /* TAB 3: THỐNG KÊ (STATS) */
        .stats-card {
            background: var(--pl-card-bg);
            border: 1px solid var(--pl-border);
            border-radius: var(--radius-md);
            padding: 24px;
            box-shadow: var(--shadow-card);
        }

        .stats-row {
            margin-bottom: 24px;
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
            font-size: 1.1rem;
        }

        .stat-title {
            color: var(--pl-text-muted);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-val-away {
            color: var(--pl-pink);
            font-size: 1.1rem;
        }

        /* Progress Bar Kép (Progress Bar So Sánh) */
        .dual-progress-bar {
            width: 100%;
            height: 10px;
            background: #f1f5f9;
            border-radius: var(--radius-full);
            display: flex;
            overflow: hidden;
            border: 1px solid var(--pl-border);
        }

        .bar-fill-home {
            background: var(--pl-purple);
            height: 100%;
            transition: width 0.4s ease;
        }

        .bar-fill-away {
            background: var(--pl-pink);
            height: 100%;
            transition: width 0.4s ease;
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

        /* Responsive Mobile */
        @media (max-width: 640px) {
            .hero-team-logo {
                width: 52px;
                height: 52px;
            }
            .hero-team-name {
                font-size: 1rem;
            }
            .hero-score-display {
                font-size: 2.25rem;
            }
            .pitch-2d {
                height: 620px;
            }
            .bench-grid {
                grid-template-columns: 1fr;
            }
            .timeline-list::before {
                left: 20px;
            }
            .event-minute-circle {
                left: 20px;
                transform: none;
            }
            .timeline-event-row.event-home, .timeline-event-row.event-away {
                padding-left: 54px;
                padding-right: 0;
                justify-content: flex-start;
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
            background: linear-gradient(135deg, #1f0426 0%, #38003c 100%);
            border: 2px solid var(--pl-mint);
            box-shadow: 0 16px 40px rgba(56, 0, 60, 0.4), 0 0 30px rgba(0, 255, 135, 0.4);
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
    @endphp

    {{-- Main Content --}}
    <main class="main-content">
        <div class="container">
            {{-- Breadcrumb --}}
            <div class="breadcrumb-nav">
                <a href="{{ route('home') }}">&larr; Lịch thi đấu</a>
                <span>/</span>
                <span>Trung tâm trận đấu</span>
            </div>

            {{-- Match Header Hero --}}
            <div class="match-hero-card"
                 data-fixture-id="{{ (int)$match['id'] }}"
                 data-home-score="{{ $homeScore ?? 0 }}"
                 data-away-score="{{ $awayScore ?? 0 }}"
                 data-home-name="{{ $homeTeam['name'] ?? 'Đội nhà' }}"
                 data-away-name="{{ $awayTeam['name'] ?? 'Đội khách' }}"
                 data-status="{{ $status }}">
                <span class="hero-competition-tag">
                    Premier League &bull; Vòng {{ $match['gameweek']['gameweek'] ?? 1 }}
                </span>

                <div class="hero-scoreboard">
                    {{-- Đội Nhà --}}
                    <div class="hero-team">
                        <img src="{{ \App\Services\PremierLeagueService::getTeamBadgeUrl($homeId, 100) }}" 
                             alt="{{ $homeTeam['name'] ?? '' }}" 
                             class="hero-team-logo">
                        <h2 class="hero-team-name">{{ $homeTeam['name'] ?? 'Đội nhà' }}</h2>
                    </div>

                    {{-- Tỷ số ở giữa --}}
                    <div class="hero-score-center">
                        @if($status === 'L' || $status === 'C')
                            <div class="hero-score-display" data-score-display>
                                <span data-score-home>{{ $homeScore ?? 0 }}</span> - <span data-score-away>{{ $awayScore ?? 0 }}</span>
                            </div>
                            @if($status === 'L')
                                <span class="hero-status-pill pill-live"><span data-fixture-clock>{{ $clock }}</span> &bull; TRỰC TIẾP</span>
                            @else
                                <span class="hero-status-pill pill-ft">HẾT GIỜ (FT)</span>
                            @endif
                        @else
                            <div class="hero-score-display" data-score-display style="font-size: 2.2rem; color: var(--pl-mint);">
                                {{ $kickoff ? \App\Services\PremierLeagueService::formatKickoffVietnam($kickoff, 'H:i') : 'VS' }}
                            </div>
                            <span class="hero-status-pill pill-upcoming">SẮP DIỄN RA</span>
                        @endif
                    </div>

                    {{-- Đội Khách --}}
                    <div class="hero-team">
                        <img src="{{ \App\Services\PremierLeagueService::getTeamBadgeUrl($awayId, 100) }}" 
                             alt="{{ $awayTeam['name'] ?? '' }}" 
                             class="hero-team-logo">
                        <h2 class="hero-team-name">{{ $awayTeam['name'] ?? 'Đội khách' }}</h2>
                    </div>
                </div>

                <div class="hero-info-footer">
                    <span>Sân: <strong>{{ $ground['name'] ?? 'Chưa rõ' }} ({{ $ground['city'] ?? 'UK' }})</strong></span>
                    @if(!empty($match['matchOfficials']))
                        <span>Trọng tài: <strong>{{ $match['matchOfficials'][0]['name']['display'] ?? 'Premier League' }}</strong></span>
                    @endif
                    <span>Thời gian: <strong>{{ $kickoff ? \App\Services\PremierLeagueService::formatKickoffVietnam($kickoff, 'H:i - d/m/Y') : '' }}</strong> (GMT+7)</span>
                </div>
            </div>

            {{-- Hệ thống Tab chuyển đổi bằng JS thuần --}}
            <div class="match-nav-tabs">
                <button class="match-tab-btn active" data-target="tab-events">Diễn Biến (Timeline)</button>
                <button class="match-tab-btn" data-target="tab-pitch">Đội Hình Ra Sân (Lineup Pitch)</button>
                <button class="match-tab-btn" data-target="tab-stats">Thống Kê (Stats)</button>
            </div>

            {{-- TAB 1: DIỄN BIẾN (TIMELINE) --}}
            <div id="tab-events" class="tab-pane active">
                <div class="timeline-card">
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 20px; color: var(--pl-purple);">
                        Dòng Thời Gian Trận Đấu
                    </h3>

                    @if(!empty($events))
                        <div class="timeline-list">
                            @foreach($events as $event)
                                <div class="timeline-event-row {{ $event['isHome'] ? 'event-home' : 'event-away' }}">
                                    <div class="event-minute-circle">{{ $event['clock'] }}</div>
                                    <div class="event-card-bubble">
                                        <span class="event-icon-badge">{{ $event['icon'] }}</span>
                                        <div>
                                            <div class="event-player-title">
                                                {{ $event['playerName'] }}
                                                @if(!empty($event['score']))
                                                    <span style="color: var(--pl-pink); margin-left: 4px;">({{ $event['score'] }})</span>
                                                @endif
                                            </div>
                                            <div class="event-sub-desc">
                                                {{ $event['desc'] }}
                                                @if(!empty($event['assistName']))
                                                    &bull; Kiến tạo: {{ $event['assistName'] }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 16px; color: var(--pl-text-muted);">
                            @if($status === 'U')
                                Trận đấu chưa diễn ra. Diễn biến bàn thắng và thẻ phạt sẽ tự động cập nhật ngay khi bóng lăn.
                            @else
                                Không có sự kiện bàn thắng hay thẻ phạt đặc biệt nào được ghi nhận.
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
                                <img src="{{ $lineups['home']['badge'] }}" alt="" style="width: 24px; height: 24px;">
                                <span style="color: var(--pl-purple);">{{ $lineups['home']['name'] }}</span>
                                <span style="font-size: 0.8rem; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">{{ $lineups['home']['formation'] }}</span>
                            </div>
                            <div class="pitch-legend-item">
                                <span style="font-size: 0.8rem; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">{{ $lineups['away']['formation'] }}</span>
                                <span style="color: var(--pl-pink);">{{ $lineups['away']['name'] }}</span>
                                <img src="{{ $lineups['away']['badge'] }}" alt="" style="width: 24px; height: 24px;">
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
                                            <div class="pitch-player-pin" title="{{ $player['name'] }} (#{{ $player['shirtNum'] }})">
                                                <div class="shirt-circle shirt-home-color">
                                                    {{ $player['shirtNum'] }}
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
                                            <div class="pitch-player-pin" title="{{ $player['name'] }} (#{{ $player['shirtNum'] }})">
                                                <div class="player-name-tag">{{ $player['lastName'] }}</div>
                                                <div class="shirt-circle shirt-away-color">
                                                    {{ $player['shirtNum'] }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Danh sách cầu thủ dự bị (Bench) --}}
                        <h4 style="font-size: 1rem; font-weight: 800; color: var(--pl-purple); margin-bottom: 12px;">Cầu Thủ Dự Bị</h4>
                        <div class="bench-grid">
                            {{-- Dự bị Đội nhà --}}
                            <div class="bench-col">
                                <div class="bench-header">
                                    <img src="{{ $lineups['home']['badge'] }}" alt="" style="width: 20px; height: 20px;">
                                    <span>{{ $lineups['home']['name'] }}</span>
                                </div>
                                @forelse($lineups['home']['substitutes'] as $sub)
                                    <div class="bench-item">
                                        <span class="bench-shirt">{{ $sub['shirtNum'] }}</span>
                                        <span style="font-weight: 700;">{{ $sub['name'] }}</span>
                                        <span style="color: var(--pl-text-muted); margin-left: auto;">{{ $sub['position'] }}</span>
                                    </div>
                                @empty
                                    <div style="font-size: 0.8rem; color: var(--pl-text-muted);">Chưa có thông tin dự bị.</div>
                                @endforelse
                            </div>

                            {{-- Dự bị Đội khách --}}
                            <div class="bench-col">
                                <div class="bench-header">
                                    <img src="{{ $lineups['away']['badge'] }}" alt="" style="width: 20px; height: 20px;">
                                    <span>{{ $lineups['away']['name'] }}</span>
                                </div>
                                @forelse($lineups['away']['substitutes'] as $sub)
                                    <div class="bench-item">
                                        <span class="bench-shirt">{{ $sub['shirtNum'] }}</span>
                                        <span style="font-weight: 700;">{{ $sub['name'] }}</span>
                                        <span style="color: var(--pl-text-muted); margin-left: auto;">{{ $sub['position'] }}</span>
                                    </div>
                                @empty
                                    <div style="font-size: 0.8rem; color: var(--pl-text-muted);">Chưa có thông tin dự bị.</div>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div style="text-align: center; padding: 48px 16px; color: var(--pl-text-muted);">
                            <div style="font-size: 2rem; margin-bottom: 8px;">📋</div>
                            <strong style="color: var(--pl-text-dark); display: block; margin-bottom: 6px;">Đội hình ra sân chưa được công bố</strong>
                            Đội hình chính thức và sơ đồ chiến thuật thường được xác nhận 60 phút trước giờ bóng lăn.
                        </div>
                    @endif
                </div>
            </div>

            {{-- TAB 3: THỐNG KÊ (STATS) --}}
            <div id="tab-stats" class="tab-pane">
                <div class="stats-card">
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 24px; color: var(--pl-purple);">
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
    <script src="{{ asset('js/livescore-engine.js') }}"></script>
</body>
</html>
