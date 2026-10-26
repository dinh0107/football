{{-- Reusable Mobile Bottom Navigation Bar (Chuẩn Vector SVG, không dùng emoji) --}}
<nav class="bottom-nav-mobile" aria-label="Điều hướng nhanh di động" style="z-index: 10005 !important;">
    <a href="{{ route('home') }}" class="bottom-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <svg class="bottom-nav-icon" viewBox="0 0 24 24">
            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
        </svg>
        <span>Lịch Thi Đấu</span>
    </a>

    <a href="{{ route('standings') }}" class="bottom-nav-item {{ request()->routeIs('standings') ? 'active' : '' }}">
        <svg class="bottom-nav-icon" viewBox="0 0 24 24">
            <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1a5.01 5.01 0 003.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/>
        </svg>
        <span>Bảng Xếp Hạng</span>
    </a>

    <a href="{{ route('fixtures') }}" class="bottom-nav-item {{ request()->routeIs('fixtures') ? 'active' : '' }}">
        <svg class="bottom-nav-icon" viewBox="0 0 24 24">
            <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
        </svg>
        <span>Vòng Đấu</span>
    </a>

    <a href="{{ route('live') }}" class="bottom-nav-item {{ request()->routeIs('live') ? 'active' : '' }}">
        <span class="bottom-nav-live-dot"></span>
        <svg class="bottom-nav-icon" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="8"/>
        </svg>
        <span>Trực Tiếp</span>
    </a>
</nav>
