@extends('layouts.app')

@section('content')
<div class="page-hero">
    <h1 class="page-title">Bảng Xếp Hạng Ngoại Hạng Anh {{ $season['label'] ?? '' }}</h1>
    <p class="page-subtitle">Dữ liệu chính thức cập nhật trực tiếp từ Premier League PulseLive API (Được lưu bộ nhớ đệm Cache 1 giờ).</p>
</div>

{{-- Khối Trận Đấu Đang Live (Nếu Có) --}}
@if(!empty($liveMatches))
<div class="card" style="margin-bottom: 24px; border-color: rgba(233, 0, 82, 0.4); background: linear-gradient(135deg, rgba(233, 0, 82, 0.1) 0%, var(--bg-card) 100%);">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span class="live-badge-dot"></span>
            <strong style="color: var(--pl-pink); text-transform: uppercase; font-size: 0.85rem;">Trận Đấu Đang Diễn Ra Trực Tiếp</strong>
        </div>
        <a href="{{ route('live') }}" style="color: var(--pl-green); font-size: 0.8rem; font-weight: 700;">Xem tất cả &rarr;</a>
    </div>

    <div class="fixture-grid">
        @foreach($liveMatches as $match)
            @php
                $slug = \App\Helpers\FootballSeoHelper::generateMatchSlug($match);
                $tvChannel = \App\Helpers\FootballSeoHelper::getTvChannel($match);
            @endphp
            <a href="{{ route('match.detail', ['slug' => $slug]) }}" class="match-card" data-fixture-id="{{ $match['id'] }}">
                <div class="match-card-header">
                    <span>{{ $match['ground']['name'] ?? 'Premier League' }}</span>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="badge-tv-guide">
                            <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
                            {{ $tvChannel }}
                        </span>
                        <span class="match-status-tag status-live">
                            <span class="live-badge-dot" style="width: 6px; height: 6px;"></span>
                            <span class="match-clock">{{ $match['clock']['label'] ?? 'LIVE' }}</span>
                        </span>
                    </div>
                </div>
                <div class="match-teams-box">
                    <div class="match-team">
                        <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($match['teams'][0]['team']['id'], 70) }}" 
                             alt="{{ $match['teams'][0]['team']['name'] }}" 
                             class="team-badge-lg" loading="lazy">
                        <span class="match-team-name">{{ $match['teams'][0]['team']['shortName'] ?? $match['teams'][0]['team']['name'] }}</span>
                    </div>
                    <div class="match-score-box">
                        <div class="score-display">
                            <span class="home-score">{{ $match['teams'][0]['score'] ?? 0 }}</span> - <span class="away-score">{{ $match['teams'][1]['score'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="match-team">
                        <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($match['teams'][1]['team']['id'], 70) }}" 
                             alt="{{ $match['teams'][1]['team']['name'] }}" 
                             class="team-badge-lg" loading="lazy">
                        <span class="match-team-name">{{ $match['teams'][1]['team']['shortName'] ?? $match['teams'][1]['team']['name'] }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

{{-- Thanh Công Cụ & Tìm Kiếm CLB --}}
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; align-items: center; gap: 8px;">
        <span style="font-weight: 700; font-size: 0.95rem;">BXH Chi Tiết</span>
        <span style="font-size: 0.8rem; color: var(--text-muted);">(Tổng cộng 20 CLB)</span>
    </div>
    <div class="search-box">
        <svg class="svg-icon" viewBox="0 0 24 24" style="color: var(--text-dim);"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        <input type="text" id="tableSearchInput" placeholder="Tìm tên câu lạc bộ...">
    </div>
</div>

{{-- Bảng Xếp Hạng --}}
<div class="table-responsive">
    <table class="standings-table">
        <thead>
            <tr>
                <th style="width: 48px; text-align: center;">Hạng</th>
                <th>Câu lạc bộ</th>
                <th style="text-align: center;">Trận</th>
                <th style="text-align: center;">Thắng</th>
                <th style="text-align: center;">Hòa</th>
                <th style="text-align: center;">Bại</th>
                <th style="text-align: center;">BT</th>
                <th style="text-align: center;">BB</th>
                <th style="text-align: center;">HS</th>
                <th style="text-align: center;">Điểm</th>
                <th style="text-align: center;">5 Trận Gần Nhất</th>
            </tr>
        </thead>
        <tbody id="standingsTableBody">
            @forelse($standings as $item)
                @php
                    $pos = (int) ($item['position'] ?? 0);
                    $team = $item['team'] ?? [];
                    $clubId = $team['club']['id'] ?? ($team['id'] ?? 0);
                    $overall = $item['overall'] ?? [];
                    $form = $item['form'] ?? [];

                    $posClass = '';
                    if ($pos <= 4) {
                        $posClass = 'ucl-pos';
                    } elseif ($pos == 5) {
                        $posClass = 'uel-pos';
                    } elseif ($pos >= 18) {
                        $posClass = 'rel-pos';
                    }
                @endphp
                <tr data-team-name="{{ $team['name'] ?? '' }}">
                    <td class="pos-cell {{ $posClass }}">
                        {{ $pos }}
                    </td>
                    <td>
                        <div class="team-cell">
                            <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($clubId, 50) }}" 
                                 alt="{{ $team['name'] ?? 'CLB' }} logo" 
                                 class="team-badge-img" loading="lazy">
                            <span class="team-name">{{ $team['name'] ?? 'Chưa rõ' }}</span>
                        </div>
                    </td>
                    <td style="text-align: center; font-weight: 600;">{{ $overall['played'] ?? 0 }}</td>
                    <td style="text-align: center; color: #10b981;">{{ $overall['won'] ?? 0 }}</td>
                    <td style="text-align: center; color: var(--text-muted);">{{ $overall['drawn'] ?? 0 }}</td>
                    <td style="text-align: center; color: #ef4444;">{{ $overall['lost'] ?? 0 }}</td>
                    <td style="text-align: center; color: var(--text-muted);">{{ $overall['goalsFor'] ?? 0 }}</td>
                    <td style="text-align: center; color: var(--text-muted);">{{ $overall['goalsAgainst'] ?? 0 }}</td>
                    <td style="text-align: center; font-weight: 600; color: {{ ($overall['goalsDifference'] ?? 0) >= 0 ? 'var(--pl-green)' : '#ef4444' }};">
                        {{ ($overall['goalsDifference'] ?? 0) > 0 ? '+' : '' }}{{ $overall['goalsDifference'] ?? 0 }}
                    </td>
                    <td style="text-align: center;" class="pts-cell">{{ $overall['points'] ?? 0 }}</td>
                    <td style="text-align: center;">
                        @if(!empty($form))
                            @foreach(array_slice($form, -5) as $result)
                                @php
                                    $resUpper = strtoupper($result['outcome'] ?? $result);
                                    $resClass = match($resUpper) {
                                        'W', 'WIN' => 'form-w',
                                        'D', 'DRAW' => 'form-d',
                                        default => 'form-l'
                                    };
                                    $resLabel = match($resUpper) {
                                        'W', 'WIN' => 'W',
                                        'D', 'DRAW' => 'D',
                                        default => 'L'
                                    };
                                @endphp
                                <span class="form-pill {{ $resClass }}">{{ $resLabel }}</span>
                            @endforeach
                        @else
                            <span style="color: var(--text-dim); font-size: 0.8rem;">--</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 32px; color: var(--text-muted);">
                        Đang đồng bộ dữ liệu bảng xếp hạng từ Premier League...
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="standings-legend">
        <div class="legend-item">
            <span class="legend-color" style="background: var(--ucl-color);"></span>
            <span>Vòng bảng UEFA Champions League (Top 4)</span>
        </div>
        <div class="legend-item">
            <span class="legend-color" style="background: var(--uel-color);"></span>
            <span>Vòng bảng UEFA Europa League (Hạng 5)</span>
        </div>
        <div class="legend-item">
            <span class="legend-color" style="background: var(--rel-color);"></span>
            <span>Khu vực xuống hạng EFL Championship (Hạng 18 - 20)</span>
        </div>
    </div>
</div>
@endsection
