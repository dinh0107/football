@extends('layouts.app')

@section('content')
<div class="page-hero">
    <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(233, 0, 82, 0.15); border: 1px solid rgba(233, 0, 82, 0.3); padding: 4px 14px; border-radius: var(--radius-full); margin-bottom: 12px;">
        <span class="live-badge-dot"></span>
        <span style="font-weight: 800; font-size: 0.8rem; color: var(--pl-pink); text-transform: uppercase;">Trung Tâm Trực Tiếp (Livescore 24/7)</span>
    </div>
    <h1 class="page-title">Trực Tiếp Tỷ Số Ngoại Hạng Anh</h1>
    <p class="page-subtitle">Dữ liệu thời gian thực được đồng bộ qua PulseLive API & Laravel Cache 30s. Tự động cập nhật không cần reload.</p>
</div>

<div class="live-section">
    {{-- Danh Sách Trận Đang Live --}}
    @if(!empty($liveMatches))
        <div style="margin-bottom: 32px;">
            <h2 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 16px; color: var(--pl-pink); display: flex; align-items: center; gap: 8px;">
                <span class="live-badge-dot"></span> Đang Diễn Ra ({{ count($liveMatches) }} Trận)
            </h2>

            <div class="fixture-grid">
                @foreach($liveMatches as $match)
                    @php
                        $slug = \App\Helpers\FootballSeoHelper::generateMatchSlug($match);
                        $home = $match['teams'][0] ?? [];
                        $away = $match['teams'][1] ?? [];
                    @endphp
                    <a href="{{ route('match.detail', ['slug' => $slug]) }}" class="match-card" data-fixture-id="{{ $match['id'] ?? '' }}"
                       style="border-color: rgba(233, 0, 82, 0.4); background: linear-gradient(135deg, rgba(233, 0, 82, 0.08) 0%, var(--bg-card) 100%);">
                        <div class="match-card-header">
                            <span>🏟️ {{ $match['ground']['name'] ?? 'Premier League' }}</span>
                            <span class="match-status-tag status-live">
                                <span class="live-badge-dot" style="width: 6px; height: 6px;"></span>
                                <span class="match-clock">{{ $match['clock']['label'] ?? 'LIVE' }}</span>
                            </span>
                        </div>

                        <div class="match-teams-box">
                            <div class="match-team">
                                <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($home['team']['club']['id'] ?? ($home['team']['id'] ?? 0), 70) }}" 
                                     alt="{{ $home['team']['name'] ?? '' }}" 
                                     class="team-badge-lg" loading="lazy">
                                <span class="match-team-name">{{ $home['team']['shortName'] ?? ($home['team']['name'] ?? '') }}</span>
                            </div>

                            <div class="match-score-box">
                                <div class="score-display" style="border-color: rgba(233, 0, 82, 0.5); color: #fff;">
                                    <span class="home-score">{{ $home['score'] ?? 0 }}</span> - <span class="away-score">{{ $away['score'] ?? 0 }}</span>
                                </div>
                                <span style="font-size: 0.7rem; color: var(--pl-pink); margin-top: 6px; font-weight: 700;">TRỰC TIẾP</span>
                            </div>

                            <div class="match-team">
                                <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($away['team']['club']['id'] ?? ($away['team']['id'] ?? 0), 70) }}" 
                                     alt="{{ $away['team']['name'] ?? '' }}" 
                                     class="team-badge-lg" loading="lazy">
                                <span class="match-team-name">{{ $away['team']['shortName'] ?? ($away['team']['name'] ?? '') }}</span>
                            </div>
                        </div>

                        <div class="match-card-footer">
                            <span>Vòng {{ $match['gameweek']['gameweek'] ?? '' }}</span>
                            <span class="cta-view">Vào phòng trực tiếp &rarr;</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        {{-- Thông báo khi chưa có trận nào đang đá --}}
        <div class="card" style="text-align: center; padding: 40px 20px; margin-bottom: 32px; border-color: rgba(0, 255, 135, 0.2);">
            <div style="font-size: 2.5rem; margin-bottom: 12px;">⚽</div>
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 8px;">
                Hiện tại không có trận đấu nào đang đá trực tiếp
            </h3>
            <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 20px; font-size: 0.9rem;">
                Hệ thống luôn sẵn sàng tự động đồng bộ khi bóng lăn. Dưới đây là các trận đấu thuộc vòng <strong>{{ $currentGw['gameweek'] ?? '' }}</strong> sắp diễn ra.
            </p>
            <a href="{{ route('fixtures') }}" class="nav-link active" style="display: inline-flex;">
                📅 Xem Toàn Bộ Lịch Thi Đấu
            </a>
        </div>
    @endif

    {{-- Danh Sách Trận Đấu Vòng Này --}}
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 1.15rem; font-weight: 800; color: #fff;">
                Lịch Đấu & Kết Quả Vòng {{ $currentGw['gameweek'] ?? 'Hiện Tại' }}
            </h2>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Giờ Việt Nam (GMT+7)</span>
        </div>

        <div class="fixture-grid">
            @forelse($fixtures as $match)
                @php
                    $slug = \App\Helpers\FootballSeoHelper::generateMatchSlug($match);
                    $status = $match['status'] ?? 'U';
                    $homeTeam = $match['teams'][0]['team'] ?? [];
                    $awayTeam = $match['teams'][1]['team'] ?? [];
                    $homeScore = $match['teams'][0]['score'] ?? null;
                    $awayScore = $match['teams'][1]['score'] ?? null;
                    $kickoff = $match['kickoff']['millis'] ?? null;
                @endphp
                <a href="{{ route('match.detail', ['slug' => $slug]) }}" class="match-card" data-fixture-id="{{ $match['id'] ?? '' }}">
                    <div class="match-card-header">
                        <span class="match-card-venue">🏟️ {{ $match['ground']['name'] ?? 'SVĐ Ngoại Hạng Anh' }}</span>
                        
                        @if($status === 'L')
                            <span class="match-status-tag status-live">
                                <span class="live-badge-dot" style="width: 6px; height: 6px;"></span>
                                <span class="match-clock">{{ $match['clock']['label'] ?? 'LIVE' }}</span>
                            </span>
                        @elseif($status === 'C')
                            <span class="match-status-tag status-ft">Hết giờ (FT)</span>
                        @else
                            <span class="match-status-tag status-upcoming">
                                {{ $kickoff ? \App\Services\PulseLiveService::formatKickoffVietnam($kickoff, 'd/m H:i') : 'Sắp diễn ra' }}
                            </span>
                        @endif
                    </div>

                    <div class="match-teams-box">
                        <div class="match-team">
                            <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($homeTeam['club']['id'] ?? ($homeTeam['id'] ?? 0), 70) }}" 
                                 alt="{{ $homeTeam['name'] ?? '' }}" 
                                 class="team-badge-lg" loading="lazy">
                            <span class="match-team-name">{{ $homeTeam['shortName'] ?? ($homeTeam['name'] ?? '') }}</span>
                        </div>

                        <div class="match-score-box">
                            @if($status === 'C' || $status === 'L')
                                <div class="score-display">
                                    <span class="home-score">{{ $homeScore ?? 0 }}</span> - <span class="away-score">{{ $awayScore ?? 0 }}</span>
                                </div>
                            @else
                                <div class="kickoff-time">
                                    {{ $kickoff ? \App\Services\PulseLiveService::formatKickoffVietnam($kickoff, 'H:i') : 'VS' }}
                                </div>
                            @endif
                        </div>

                        <div class="match-team">
                            <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($awayTeam['club']['id'] ?? ($awayTeam['id'] ?? 0), 70) }}" 
                                 alt="{{ $awayTeam['name'] ?? '' }}" 
                                 class="team-badge-lg" loading="lazy">
                            <span class="match-team-name">{{ $awayTeam['shortName'] ?? ($awayTeam['name'] ?? '') }}</span>
                        </div>
                    </div>

                    <div class="match-card-footer">
                        <span>Vòng {{ $match['gameweek']['gameweek'] ?? '' }}</span>
                        <span class="cta-view">Xem chi tiết &rarr;</span>
                    </div>
                </a>
            @empty
                <p style="color: var(--text-muted);">Không có trận đấu nào trong vòng này.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
