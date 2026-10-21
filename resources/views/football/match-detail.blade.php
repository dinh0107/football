@extends('layouts.app')

@section('content')
@php
    $status = $match['status'] ?? 'U';
    $homeTeam = $match['teams'][0]['team'] ?? [];
    $awayTeam = $match['teams'][1]['team'] ?? [];
    $homeScore = $match['teams'][0]['score'] ?? null;
    $awayScore = $match['teams'][1]['score'] ?? null;
    $homeId = $homeTeam['club']['id'] ?? ($homeTeam['id'] ?? 0);
    $awayId = $awayTeam['club']['id'] ?? ($awayTeam['id'] ?? 0);
    $kickoff = $match['kickoff']['millis'] ?? null;
    $ground = $match['ground'] ?? [];
    $events = $match['events'] ?? [];
    $teamLists = $match['teamLists'] ?? [];
    $clock = $match['clock']['label'] ?? '';

    // Lọc các sự kiện quan trọng (bàn thắng, thẻ phạt, thay người)
    $keyEvents = array_filter($events, function($e) {
        return in_array($e['type'] ?? '', ['G', 'B', 'S', 'OG', 'P']);
    });
@endphp

{{-- Nút Quay Lại --}}
<div style="margin-bottom: 16px;">
    <a href="{{ route('fixtures') }}" style="color: var(--text-muted); font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">
        &larr; Quay lại danh sách lịch thi đấu
    </a>
</div>

{{-- Hero Scoreboard Header --}}
<div class="match-detail-hero hero-match-detail" 
     data-fixture-id="{{ $match['id'] ?? '' }}"
     data-home-score="{{ $homeScore ?? 0 }}"
     data-away-score="{{ $awayScore ?? 0 }}"
     data-status="{{ $status }}">
    <div class="hero-header-info">
        <span class="hero-league-badge">
            Ngoại Hạng Anh &bull; Vòng {{ $match['gameweek']['gameweek'] ?? '' }}
        </span>
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">
            {{ $kickoff ? \App\Services\PulseLiveService::formatKickoffVietnam($kickoff, 'H:i - l, d/m/Y') : '' }} (Giờ VN)
        </div>
    </div>

    <div class="hero-match-center">
        {{-- Đội Nhà --}}
        <div class="hero-team">
            <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($homeId, 100) }}" 
                 alt="{{ $homeTeam['name'] ?? '' }}" 
                 class="hero-team-badge">
            <h2 class="hero-team-name">{{ $homeTeam['name'] ?? 'Đội nhà' }}</h2>
        </div>

        {{-- Bảng Tỷ Số & Đồng Hồ Trận Đấu --}}
        <div class="hero-score-area">
            @if($status === 'L')
                <span class="hero-clock" data-fixture-clock>{{ $clock ?: 'LIVE' }}</span>
                <div class="hero-score hero-score-value" data-score-display>
                    <span data-score-home>{{ $homeScore ?? 0 }}</span> - <span data-score-away>{{ $awayScore ?? 0 }}</span>
                </div>
                <span style="color: var(--pl-pink); font-weight: 800; font-size: 0.75rem; text-transform: uppercase;">Trực Tiếp</span>
            @elseif($status === 'C')
                <span style="background: rgba(255, 255, 255, 0.1); color: #fff; padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 700;">Hết Giờ (FT)</span>
                <div class="hero-score hero-score-value" data-score-display>
                    <span data-score-home>{{ $homeScore ?? 0 }}</span> - <span data-score-away>{{ $awayScore ?? 0 }}</span>
                </div>
                @if(isset($match['halfTimeScore']))
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Hiệp 1: {{ $match['halfTimeScore']['homeScore'] ?? 0 }} - {{ $match['halfTimeScore']['awayScore'] ?? 0 }}</span>
                @endif
            @else
                <span style="background: rgba(0, 255, 135, 0.1); color: var(--pl-green); padding: 3px 10px; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 700;">Sắp Diễn Ra</span>
                <div class="hero-score" style="font-size: 2rem; color: var(--pl-green);">
                    {{ $kickoff ? \App\Services\PulseLiveService::formatKickoffVietnam($kickoff, 'H:i') : 'VS' }}
                </div>
                <span style="font-size: 0.8rem; color: var(--text-dim);">Giờ Việt Nam</span>
            @endif
        </div>

        {{-- Đội Khách --}}
        <div class="hero-team">
            <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($awayId, 100) }}" 
                 alt="{{ $awayTeam['name'] ?? '' }}" 
                 class="hero-team-badge">
            <h2 class="hero-team-name">{{ $awayTeam['name'] ?? 'Đội khách' }}</h2>
        </div>
    </div>

    {{-- Thông tin sân vận động & trọng tài --}}
    <div class="hero-match-meta">
        <span><strong>Sân:</strong> {{ $ground['name'] ?? 'Chưa rõ' }} ({{ $ground['city'] ?? '' }})</span>
        @if(!empty($match['matchOfficials']))
            <span><strong>Trọng tài:</strong> {{ $match['matchOfficials'][0]['name']['display'] ?? 'Premier League' }}</span>
        @endif
        @if(!empty($match['attendance']))
            <span><strong>Khán giả:</strong> {{ number_format($match['attendance']) }} người</span>
        @endif
    </div>
</div>

{{-- Tabs Điều Hướng (Vanilla JS) --}}
<div class="tabs-nav">
    <button class="tab-btn active" data-tab="tab-timeline">Diễn Biến & Bàn Thắng</button>
    <button class="tab-btn" data-tab="tab-lineups">Đội Hình Ra Sân</button>
    <button class="tab-btn" data-tab="tab-info">Thông Tin Trận Đấu</button>
</div>

{{-- Tab 1: Diễn Biến Trận Đấu (Timeline) --}}
<div id="tab-timeline" class="tab-content active">
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 20px;">Dòng Thời Gian Sự Kiện</h3>

        @if(!empty($keyEvents))
            <div class="timeline">
                @foreach($keyEvents as $ev)
                    @php
                        $isHome = ($ev['teamId'] ?? 0) == $homeId;
                        $clockLabel = $ev['clock']['label'] ?? '00\'';
                        $type = $ev['type'] ?? '';
                        $isRed = ($type === 'B' && ($ev['cardType'] ?? '') === 'RED');
                    @endphp
                    <div class="timeline-event {{ $isHome ? 'event-home' : 'event-away' }}">
                        <div class="timeline-minute">{{ $clockLabel }}</div>
                        <div class="timeline-body">
                            @if($type === 'G')
                                <span class="badge-goal">GOAL</span> <span>Bàn thắng</span>
                            @elseif($type === 'OG')
                                <span class="badge-goal" style="color: #f59e0b; border-color: rgba(245, 158, 11, 0.3);">OG</span> <span>Phản lưới</span>
                            @elseif($type === 'P')
                                <span class="badge-goal">PEN</span> <span>Penalty</span>
                            @elseif($type === 'B' && $isRed)
                                <span class="badge-card-red"></span> <span>Thẻ đỏ</span>
                            @elseif($type === 'B')
                                <span class="badge-card-yellow"></span> <span>Thẻ vàng</span>
                            @elseif($type === 'S')
                                <span style="color: var(--text-dim); font-weight: 700;">SUB</span> <span>Thay người</span>
                            @else
                                <span>Sự kiện</span>
                            @endif
                            @if(isset($ev['score']))
                                <strong style="color: var(--pl-green);">({{ $ev['score']['homeScore'] }} - {{ $ev['score']['awayScore'] }})</strong>
                            @endif
                            <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $isHome ? ($homeTeam['shortName'] ?? '') : ($awayTeam['shortName'] ?? '') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-muted); text-align: center; padding: 24px 0;">
                @if($status === 'U')
                    Trận đấu chưa bắt đầu. Diễn biến bàn thắng và thẻ phạt sẽ tự động cập nhật ngay khi bóng lăn.
                @else
                    Không có sự kiện ghi bàn hay thẻ phạt đặc biệt nào trong trận đấu này.
                @endif
            </p>
        @endif
    </div>
</div>

{{-- Tab 2: Đội Hình Ra Sân (Lineups) --}}
<div id="tab-lineups" class="tab-content">
    @if(!empty($teamLists) && count($teamLists) >= 2)
        <div class="lineup-grid">
            {{-- Đội hình Đội Nhà --}}
            <div class="lineup-column">
                <div class="lineup-title">
                    <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($homeId, 50) }}" 
                         alt="{{ $homeTeam['name'] ?? '' }}" 
                         style="width: 28px; height: 28px; object-fit: contain;">
                    <div>
                        <div>{{ $homeTeam['name'] ?? 'Đội nhà' }}</div>
                        <div style="font-size: 0.75rem; color: var(--pl-green); font-weight: 600;">
                            Sơ đồ: {{ $teamLists[0]['formation']['label'] ?? 'Đang cập nhật' }}
                        </div>
                    </div>
                </div>

                <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin: 12px 0 8px;">Đội Hình Chính Thức (Starting XI)</div>
                @forelse($teamLists[0]['lineup'] ?? [] as $player)
                    <div class="player-row">
                        <span class="shirt-num">{{ $player['matchShirtNumber'] ?? ($player['info']['shirtNum'] ?? '-') }}</span>
                        <span style="font-weight: 600; color: #fff;">{{ $player['name']['display'] ?? 'Cầu thủ' }}</span>
                        <span style="font-size: 0.75rem; color: var(--text-dim); margin-left: auto;">{{ $player['matchPosition'] ?? '' }}</span>
                    </div>
                @empty
                    <p style="color: var(--text-dim); font-size: 0.85rem;">Chưa có danh sách ra sân.</p>
                @endforelse

                @if(!empty($teamLists[0]['substitutes']))
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin: 20px 0 8px;">Dự Bị (Substitutes)</div>
                    @foreach($teamLists[0]['substitutes'] as $player)
                        <div class="player-row">
                            <span class="shirt-num">{{ $player['matchShirtNumber'] ?? ($player['info']['shirtNum'] ?? '-') }}</span>
                            <span style="color: var(--text-muted);">{{ $player['name']['display'] ?? 'Cầu thủ' }}</span>
                            <span style="font-size: 0.75rem; color: var(--text-dim); margin-left: auto;">{{ $player['matchPosition'] ?? '' }}</span>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Đội hình Đội Khách --}}
            <div class="lineup-column">
                <div class="lineup-title">
                    <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($awayId, 50) }}" 
                         alt="{{ $awayTeam['name'] ?? '' }}" 
                         style="width: 28px; height: 28px; object-fit: contain;">
                    <div>
                        <div>{{ $awayTeam['name'] ?? 'Đội khách' }}</div>
                        <div style="font-size: 0.75rem; color: var(--pl-green); font-weight: 600;">
                            Sơ đồ: {{ $teamLists[1]['formation']['label'] ?? 'Đang cập nhật' }}
                        </div>
                    </div>
                </div>

                <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin: 12px 0 8px;">Đội Hình Chính Thức (Starting XI)</div>
                @forelse($teamLists[1]['lineup'] ?? [] as $player)
                    <div class="player-row">
                        <span class="shirt-num">{{ $player['matchShirtNumber'] ?? ($player['info']['shirtNum'] ?? '-') }}</span>
                        <span style="font-weight: 600; color: #fff;">{{ $player['name']['display'] ?? 'Cầu thủ' }}</span>
                        <span style="font-size: 0.75rem; color: var(--text-dim); margin-left: auto;">{{ $player['matchPosition'] ?? '' }}</span>
                    </div>
                @empty
                    <p style="color: var(--text-dim); font-size: 0.85rem;">Chưa có danh sách ra sân.</p>
                @endforelse

                @if(!empty($teamLists[1]['substitutes']))
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin: 20px 0 8px;">Dự Bị (Substitutes)</div>
                    @foreach($teamLists[1]['substitutes'] as $player)
                        <div class="player-row">
                            <span class="shirt-num">{{ $player['matchShirtNumber'] ?? ($player['info']['shirtNum'] ?? '-') }}</span>
                            <span style="color: var(--text-muted);">{{ $player['name']['display'] ?? 'Cầu thủ' }}</span>
                            <span style="font-size: 0.75rem; color: var(--text-dim); margin-left: auto;">{{ $player['matchPosition'] ?? '' }}</span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @else
        <div class="card" style="text-align: center; padding: 32px;">
            <p style="color: var(--text-muted);">
                Đội hình ra sân chính thức thường được công bố 60 phút trước giờ bóng lăn. Vui lòng quay lại sau!
            </p>
        </div>
    @endif
</div>

{{-- Tab 3: Thông Tin Trận Đấu (Info) --}}
<div id="tab-info" class="tab-content">
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 16px;">Chi Tiết Trận Đấu & Sân Vận Động</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; font-size: 0.9rem;">
            <div style="background: rgba(255, 255, 255, 0.02); padding: 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                <div style="color: var(--text-dim); font-size: 0.8rem;">Sân Vận Động</div>
                <strong style="color: #fff; font-size: 1rem;">{{ $ground['name'] ?? 'Premier League Ground' }}</strong>
                <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 4px;">{{ $ground['city'] ?? 'Vương quốc Anh' }}</div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.02); padding: 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                <div style="color: var(--text-dim); font-size: 0.8rem;">Giải Đấu & Vòng</div>
                <strong style="color: #fff; font-size: 1rem;">Ngoại Hạng Anh (Premier League)</strong>
                <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 4px;">Vòng đấu thứ {{ $match['gameweek']['gameweek'] ?? 1 }}</div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.02); padding: 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                <div style="color: var(--text-dim); font-size: 0.8rem;">Thời Gian Bắt Đầu (Việt Nam)</div>
                <strong style="color: var(--pl-green); font-size: 1rem;">{{ $kickoff ? \App\Services\PulseLiveService::formatKickoffVietnam($kickoff, 'H:i - d/m/Y') : 'Chưa định ngày' }}</strong>
                <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 4px;">Múi giờ Asia/Ho_Chi_Minh (GMT+7)</div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.02); padding: 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-light);">
                <div style="color: var(--text-dim); font-size: 0.8rem;">Trạng Thái Kỹ Thuật (PulseLive)</div>
                <strong style="color: #fff; font-size: 1rem;">ID Trận: {{ $match['id'] ?? '' }} &bull; Trạng thái: {{ $status }}</strong>
                <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 4px;">Nguồn dữ liệu: PulseLive Official API</div>
            </div>
        </div>
    </div>
</div>
@endsection
