@extends('layouts.app')

@section('content')
<div class="page-hero">
    <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(233, 0, 82, 0.15); border: 1px solid rgba(233, 0, 82, 0.3); padding: 4px 14px; border-radius: var(--radius-full); margin-bottom: 12px;">
        <span class="live-badge-dot"></span>
        <span style="font-weight: 800; font-size: 0.8rem; color: var(--pl-pink); text-transform: uppercase;">Trung Tâm Trực Tiếp</span>
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
                        $tvChannel = \App\Helpers\FootballSeoHelper::getTvChannel($match);
                        $home = $match['teams'][0] ?? [];
                        $away = $match['teams'][1] ?? [];
                    @endphp
                    <a href="{{ route('match.detail', ['slug' => $slug]) }}" class="match-card" 
                       data-fixture-id="{{ $match['id'] ?? '' }}"
                       data-home-score="{{ $home['score'] ?? 0 }}"
                       data-away-score="{{ $away['score'] ?? 0 }}"
                       data-status="L"
                       style="border-color: #fecdd3; background: linear-gradient(135deg, #fff1f2 0%, #ffffff 100%);">
                        <div class="match-card-header">
                            <span class="match-card-venue">
                                <svg class="svg-icon" viewBox="0 0 24 24" style="color: var(--text-dim); width: 14px; height: 14px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                {{ $match['ground']['name'] ?? 'Premier League' }}
                            </span>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span class="badge-tv-guide">
                                    <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
                                    {{ $tvChannel }}
                                </span>
                                <span class="match-status-tag status-live">
                                    <span class="live-badge-dot" style="width: 6px; height: 6px;"></span>
                                    <span class="match-clock" data-fixture-clock>{{ $match['clock']['label'] ?? 'LIVE' }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="match-teams-box">
                            <div class="match-team">
                                <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($home['team']['club']['id'] ?? ($home['team']['id'] ?? 0), 70) }}" 
                                     alt="{{ $home['team']['name'] ?? '' }}" 
                                     class="team-badge-lg" loading="lazy"
                                     onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
                                <span class="match-team-name">{{ $home['team']['shortName'] ?? ($home['team']['name'] ?? '') }}</span>
                            </div>

                            <div class="match-score-box">
                                <div class="score-display" data-score-display style="border-color: #fecdd3; color: var(--text-main);">
                                    <span class="home-score" data-score-home>{{ $home['score'] ?? 0 }}</span> - <span class="away-score" data-score-away>{{ $away['score'] ?? 0 }}</span>
                                </div>
                                <span style="font-size: 0.7rem; color: var(--pl-pink); margin-top: 6px; font-weight: 700;">TRỰC TIẾP</span>
                            </div>

                            <div class="match-team">
                                <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($away['team']['club']['id'] ?? ($away['team']['id'] ?? 0), 70) }}" 
                                     alt="{{ $away['team']['name'] ?? '' }}" 
                                     class="team-badge-lg" loading="lazy"
                                     onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
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
        <div class="card" style="text-align: center; padding: 40px 20px; margin-bottom: 32px; border-color: var(--border-light);">
            <div style="margin-bottom: 12px;">
                <svg class="svg-icon-lg" style="width: 40px; height: 40px; color: var(--pl-green);" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--pl-purple); margin-bottom: 8px;">
                Hiện tại không có trận đấu nào đang đá trực tiếp
            </h3>
            <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 20px; font-size: 0.9rem;">
                Hệ thống luôn sẵn sàng tự động đồng bộ khi bóng lăn. Dưới đây là các trận đấu thuộc vòng <strong>{{ $currentGw['gameweek'] ?? '' }}</strong> sắp diễn ra.
            </p>
            <a href="{{ route('fixtures') }}" class="nav-link active" style="display: inline-flex;">
                Xem Toàn Bộ Lịch Thi Đấu
            </a>
        </div>
    @endif

    {{-- Danh Sách Trận Đấu Vòng Này --}}
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--pl-purple);">
                Lịch Thi Đấu & Kết Quả Vòng {{ $currentGw['gameweek'] ?? 'Hiện Tại' }} Ngoại Hạng Anh
            </h2>
            <span style="font-size: 0.8rem; color: var(--text-muted);">Giờ Việt Nam (GMT+7)</span>
        </div>

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
                @endphp
                <a href="{{ route('match.detail', ['slug' => $slug]) }}" class="match-card" data-fixture-id="{{ $match['id'] ?? '' }}">
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
                        <div class="match-team">
                            <img src="{{ \App\Services\PulseLiveService::getTeamBadgeUrl($homeTeam['club']['id'] ?? ($homeTeam['id'] ?? 0), 70) }}" 
                                 alt="{{ $homeTeam['name'] ?? '' }}" 
                                 class="team-badge-lg" loading="lazy"
                                 onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
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
                                 class="team-badge-lg" loading="lazy"
                                 onerror="this.onerror=null; this.src='https://resources.premierleague.com/premierleague/badges/70/t1.png';">
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

{{-- Khối thông tin biên tập SEO về Trung Tâm Trực Tiếp Bóng Đá --}}
<div class="seo-content-box">
    <h2>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
        Trung Tâm Trực Tiếp & Cập Nhật Tỷ Số Ngoại Hạng Anh Trực Tuyến
    </h2>
    <p>
        Trung tâm trực tiếp bóng đá tại <strong>tysongoaihang.site</strong> cung cấp giải pháp theo dõi tỷ số trực tuyến (livescore) Ngoại Hạng Anh siêu tốc, chuẩn xác và tức thì theo thời gian thực. Hệ thống được kết nối trực tiếp với nền tảng dữ liệu chính thức Premier League PulseLive, đảm bảo mọi biến động trên sân cỏ đều được phản hồi tức thì tới người xem.
    </p>
    <p>
        <strong>Các tính năng nổi bật khi theo dõi trực tiếp:</strong>
    </p>
    <ul>
        <li><strong>Tự động làm mới dữ liệu (Live Engine):</strong> Tỷ số, đồng hồ thời gian thi đấu và tình huống bù giờ được đồng bộ ngầm sau mỗi 30 giây mà không cần người dùng thao tác tải lại trang.</li>
        <li><strong>Chi tiết diễn biến bàn thắng & thẻ phạt:</strong> Ghi nhận chính xác cầu thủ lập công, thời gian ghi bàn, thẻ vàng, thẻ đỏ và các tình huống thay người chiến thuật.</li>
        <li><strong>Sa bàn đội hình chiến thuật 2D:</strong> Trực quan hóa vị trí di chuyển và vai trò chiến thuật của 22 cầu thủ trên sân cỏ ngay khi ban huấn luyện công bố đội hình ra sân.</li>
        <li><strong>Thông số kỹ thuật toàn diện:</strong> Tỷ lệ kiểm soát bóng, số cú sút trúng đích, phạt góc và các pha cản phá xuất sắc của thủ môn hai đội.</li>
    </ul>
    <p>
        Đồng thời, chuyên trang cung cấp hướng dẫn lịch phát sóng truyền hình tại Việt Nam (K+ Sport, TV360, FPT Play), giúp người hâm mộ không bỏ lỡ bất kỳ khoảnh khắc rực lửa nào của giải đấu số một nước Anh.
    </p>
</div>
@endsection
