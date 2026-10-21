@extends('layouts.app')

@section('content')
<div class="page-hero">
    <h1 class="page-title">Lịch Thi Đấu & Kết Quả Ngoại Hạng Anh {{ $season['label'] ?? '' }}</h1>
    <p class="page-subtitle">Theo dõi lịch thi đấu, tỷ số trực tiếp và kết quả tất cả các vòng đấu theo giờ Việt Nam (Cache 1 ngày).</p>
</div>

{{-- Chọn Vòng Đấu (Gameweek Horizontal Scroll Pills) --}}
@if(!empty($gameweeks))
<div class="gw-selector-container">
    @foreach($gameweeks as $gw)
        @php
            $gwId = $gw['id'] ?? 0;
            $gwNumber = $gw['gameweek'] ?? 1;
            $isActive = ($selectedGw['id'] ?? null) == $gwId;
            $isLive = ($gw['status'] ?? '') === 'L';
        @endphp
        <a href="{{ route('fixtures', ['vong' => $gwId]) }}" 
           class="gw-pill {{ $isActive ? 'active' : '' }}">
            @if($isLive)
                <span class="live-badge-dot" style="width: 6px; height: 6px;"></span>
            @endif
            Vòng {{ $gwNumber }}
        </a>
    @endforeach
</div>
@endif

{{-- Tiêu đề Vòng được chọn --}}
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--pl-purple);">
            Trận Đấu Vòng {{ $selectedGw['gameweek'] ?? 'Hiện tại' }}
        </h2>
        <span style="font-size: 0.825rem; color: var(--text-muted);">
            @if(isset($selectedGw['from']['millis']))
                Diễn ra từ: {{ \App\Services\PulseLiveService::formatKickoffVietnam($selectedGw['from']['millis'], 'd/m/Y') }}
            @endif
        </span>
    </div>
    <span style="background: #f1f5f9; border: 1px solid var(--border-light); padding: 4px 14px; border-radius: var(--radius-full); font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">
        {{ count($fixtures) }} trận đấu
    </span>
</div>

{{-- Danh Sách Trận Đấu --}}
<div class="fixture-grid">
    @forelse($fixtures as $match)
        @php
            $slug = \App\Helpers\FootballSeoHelper::generateMatchSlug($match);
            $tvChannel = \App\Helpers\FootballSeoHelper::getTvChannel($match);
            $status = $match['status'] ?? 'U';
            $homeTeam = $match['teams'][0]['team'] ?? [];
            $awayTeam = $match['teams'][1]['team'] ?? [];
            $homeScore = $match['teams'][0]['score'] ?? null;
            $awayScore = $match['teams'][1]['score'] ?? null;
            $kickoff = $match['kickoff']['millis'] ?? null;
            $homeClubId = $homeTeam['club']['id'] ?? ($homeTeam['id'] ?? 0);
            $awayClubId = $awayTeam['club']['id'] ?? ($awayTeam['id'] ?? 0);
        @endphp
        <a href="{{ route('match.detail', ['slug' => $slug]) }}" class="match-card" 
           data-fixture-id="{{ $match['id'] ?? '' }}"
           data-home-id="{{ $homeClubId }}"
           data-away-id="{{ $awayClubId }}">
            <div class="match-card-header">
                <span class="match-card-venue">
                    <svg class="svg-icon" viewBox="0 0 24 24" style="color: var(--text-dim); width: 14px; height: 14px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    {{ $match['ground']['name'] ?? 'SVĐ Ngoại Hạng Anh' }}
                </span>
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="badge-tv-guide">
                        <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
                        {{ $tvChannel }}
                    </span>

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
            </div>

            <div class="match-teams-box">
                {{-- Đội Nhà --}}
                <div class="match-team">
                    <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($homeClubId, 70) }}" 
                         alt="{{ $homeTeam['name'] ?? 'Đội nhà' }}" 
                         class="team-badge-lg" loading="lazy"
                         onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                    <div style="display: flex; align-items: center; gap: 4px; justify-content: center;">
                        <span class="match-team-name">{{ $homeTeam['shortName'] ?? ($homeTeam['name'] ?? 'Đội nhà') }}</span>
                        <button type="button" class="btn-favorite-team" data-favorite-team="{{ $homeClubId }}" title="Ghim đội yêu thích">
                            <svg class="star-icon" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </button>
                    </div>
                </div>

                {{-- Tỉ Số hoặc Giờ Đá --}}
                <div class="match-score-box">
                    @if($status === 'C' || $status === 'L')
                        <div class="score-display">
                            <span class="home-score">{{ $homeScore ?? 0 }}</span> - <span class="away-score">{{ $awayScore ?? 0 }}</span>
                        </div>
                    @else
                        <div class="kickoff-time">
                            {{ $kickoff ? \App\Services\PulseLiveService::formatKickoffVietnam($kickoff, 'H:i') : 'VS' }}
                        </div>
                        <span style="font-size: 0.7rem; color: var(--text-dim); margin-top: 4px;">GMT+7</span>
                    @endif
                </div>

                {{-- Đội Khách --}}
                <div class="match-team">
                    <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($awayClubId, 70) }}" 
                         alt="{{ $awayTeam['name'] ?? 'Đội khách' }}" 
                         class="team-badge-lg" loading="lazy"
                         onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                    <div style="display: flex; align-items: center; gap: 4px; justify-content: center;">
                        <span class="match-team-name">{{ $awayTeam['shortName'] ?? ($awayTeam['name'] ?? 'Đội khách') }}</span>
                        <button type="button" class="btn-favorite-team" data-favorite-team="{{ $awayClubId }}" title="Ghim đội yêu thích">
                            <svg class="star-icon" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="match-card-footer">
                <span>Vòng {{ $match['gameweek']['gameweek'] ?? '' }}</span>
                <span class="cta-view">Chi tiết &rarr;</span>
            </div>
        </a>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 48px; background: var(--bg-card); border-radius: var(--radius-md); border: 1px solid var(--border-light);">
            <p style="color: var(--text-muted); font-size: 1rem;">Không có trận đấu nào được tìm thấy cho vòng này.</p>
        </div>
    @endforelse
</div>
@endsection
