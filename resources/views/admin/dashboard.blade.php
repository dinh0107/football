@extends('layouts.app')

@push('styles')
<style>
    /* Admin Control Panel Styles */
    .admin-header-bar {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: var(--radius-lg);
        padding: 28px 32px;
        color: #ffffff;
        margin-bottom: 28px;
        box-shadow: var(--shadow-md);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .admin-title-area h1 {
        font-size: 1.5rem;
        font-weight: 900;
        letter-spacing: -0.02em;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .admin-title-area p {
        font-size: 0.875rem;
        color: #94a3b8;
    }
    .admin-user-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 8px 16px;
        border-radius: var(--radius-full);
    }
    .admin-user-badge span {
        font-size: 0.85rem;
        color: #e2e8f0;
    }
    .btn-admin-logout {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.4);
        padding: 6px 14px;
        border-radius: var(--radius-full);
        font-weight: 700;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-admin-logout:hover {
        background: #ef4444;
        color: #ffffff;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }
    .stat-card {
        background: #ffffff;
        border: 1px solid var(--border-light);
        border-radius: var(--radius-md);
        padding: 20px 22px;
        box-shadow: var(--shadow-subtle);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .stat-label {
        font-size: 0.775rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.04em;
    }
    .stat-value {
        font-size: 1.8rem;
        font-weight: 900;
        color: var(--pl-purple);
        margin: 6px 0;
    }
    .stat-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: var(--radius-full);
    }
    .pill-green {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .pill-red {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* Panels */
    .panel-card {
        background: #ffffff;
        border: 1px solid var(--border-light);
        border-radius: var(--radius-md);
        padding: 26px 28px;
        margin-bottom: 26px;
        box-shadow: var(--shadow-subtle);
    }
    .panel-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--pl-purple);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
    }

    /* Form Controls */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
        margin-top: 16px;
        align-items: flex-end;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-dark);
    }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-light);
        border-radius: 8px;
        font-size: 0.875rem;
        font-family: inherit;
        background: #f8fafc;
        color: var(--text-dark);
        outline: none;
        transition: all 0.2s;
    }
    .form-control:focus {
        border-color: var(--pl-pink);
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    /* Buttons */
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.85rem;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .btn-primary {
        background: var(--pl-purple);
        color: #ffffff;
    }
    .btn-primary:hover {
        background: #1e293b;
        transform: translateY(-1px);
    }
    .btn-warning {
        background: #f59e0b;
        color: #ffffff;
    }
    .btn-warning:hover {
        background: #d97706;
    }
    .btn-danger {
        background: #ef4444;
        color: #ffffff;
    }
    .btn-danger:hover {
        background: #dc2626;
    }

    /* Table */
    .table-wrap {
        overflow-x: auto;
        margin-top: 18px;
        border-radius: 8px;
        border: 1px solid var(--border-light);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        text-align: left;
    }
    .data-table th {
        background: #f1f5f9;
        padding: 12px 16px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .data-table td {
        padding: 12px 16px;
        border-top: 1px solid var(--border-light);
        color: var(--text-dark);
    }

    /* Banner Grid */
    .banner-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
        margin-top: 18px;
    }
    .banner-box {
        border: 1px solid var(--border-light);
        border-radius: var(--radius-md);
        padding: 18px;
        background: #f8fafc;
    }
    .banner-box-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .banner-pos-tag {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 3px 8px;
        border-radius: 6px;
        background: #e0e7ff;
        color: #3730a3;
    }

    /* Alerts */
    .alert {
        padding: 12px 18px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 700;
        margin-bottom: 22px;
    }
    .alert-success {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .alert-error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
</style>
@endpush

@section('content')
<div style="padding: 20px 0 40px;">
    {{-- Breadcrumb điều hướng --}}
    <nav class="breadcrumb-nav" aria-label="Breadcrumb" style="margin-bottom: 18px;">
        <ol class="breadcrumb-list" style="display: flex; align-items: center; gap: 8px; list-style: none; padding: 0; margin: 0; font-size: 0.85rem; color: var(--text-dim);">
            <li>
                <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: 5px; color: var(--text-muted); font-weight: 600;">
                    <svg class="svg-icon" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                    Trang Chủ
                </a>
            </li>
            <li style="color: var(--border-light);">/</li>
            <li aria-current="page" style="color: var(--pl-purple); font-weight: 800;">Quản Trị Hệ Thống</li>
        </ol>
    </nav>

    {{-- Admin Header Bar --}}
    <div class="admin-header-bar">
        <div class="admin-title-area">
            <h1>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                Bảng Điều Khiển Quản Trị
            </h1>
            <p>Quản lý kênh TV Guide, tối ưu bộ nhớ Cache, bảo trì hệ thống và banner quảng cáo</p>
        </div>

        <div class="admin-user-badge">
            <span>Xin chào, <strong>{{ Auth::user()->name ?? 'Admin' }}</strong></span>
            <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-admin-logout">Đăng Xuất</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- Stat Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Ghi Đè Kênh TV Guide</span>
            <div class="stat-value">{{ $overrides->count() }}</div>
            <span class="stat-status-pill pill-green">Đã thiết lập</span>
        </div>

        <div class="stat-card">
            <span class="stat-label">Trạng Thái Hệ Thống</span>
            <div class="stat-value" style="font-size: 1.25rem;">
                {{ $isMaintenance ? 'Bảo Trì (Down)' : 'Hoạt Động (Live)' }}
            </div>
            <span class="stat-status-pill {{ $isMaintenance ? 'pill-red' : 'pill-green' }}">
                {{ $isMaintenance ? 'Đang bật bảo trì' : 'Đang phục vụ bình thường' }}
            </span>
        </div>

        <div class="stat-card">
            <span class="stat-label">Bộ Nhớ Cache & Session</span>
            <div class="stat-value" style="font-size: 1.25rem;">{{ config('cache.default', 'file') }}</div>
            <span class="stat-status-pill pill-green">Chịu tải cao (High Concurrency)</span>
        </div>

        <div class="stat-card">
            <span class="stat-label">Banner Quảng Cáo Hoạt Động</span>
            <div class="stat-value">
                {{ collect($banners)->filter(fn($b) => $b && $b->is_active)->count() }} / 3
            </div>
            <span class="stat-status-pill pill-green">Đầu trang, Tỷ số, Footer</span>
        </div>
    </div>

    {{-- PANEL 1: CẬP NHẬT KÊNH PHÁT SÓNG TV GUIDE CUỐI TUẦN --}}
    <div class="panel-card">
        <h2 class="panel-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
            Cập Nhật Nhanh Kênh Phát Sóng TV Guide Cho Trận Đấu
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 16px;">
            Chỉ định kênh phát sóng độc quyền (K+ Sport 1, TV360, FPT Play, VTVcab ON) cho từng trận đấu để hiển thị chính xác cho người dùng.
        </p>

        <form action="{{ route('admin.tv_channel.update') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="match_id">Chọn Trận Đấu</label>
                    <select id="match_id" name="match_id" class="form-control" required>
                        <option value="">-- Chọn trận đấu --</option>
                        @foreach($fixtures as $f)
                            @php
                                $fId = $f['id'] ?? 0;
                                $hName = $f['teams'][0]['team']['name'] ?? 'Home';
                                $aName = $f['teams'][1]['team']['name'] ?? 'Away';
                            @endphp
                            <option value="{{ $fId }}">
                                #{{ $fId }} - {{ $hName }} vs {{ $aName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tv_channel">Kênh Phát Sóng (TV Guide)</label>
                    <select id="tv_channel" name="tv_channel" class="form-control" required>
                        <option value="K+ Sport 1">K+ Sport 1 (Độc quyền)</option>
                        <option value="TV360">TV360 (Viettel)</option>
                        <option value="FPT Play">FPT Play</option>
                        <option value="VTVcab ON">VTVcab ON</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="notes">Ghi chú (Tùy chọn)</label>
                    <input type="text" id="notes" name="notes" class="form-control" placeholder="Trận cầu tâm điểm Big 6...">
                </div>

                <button type="submit" class="btn-action btn-primary">
                    Lưu Kênh TV
                </button>
            </div>
        </form>

        @if($overrides->isNotEmpty())
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Match ID</th>
                            <th>Kênh Phát Sóng</th>
                            <th>Ghi Chú</th>
                            <th>Thời Gian Cập Nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overrides as $ov)
                            <tr>
                                <td><strong>#{{ $ov->match_id }}</strong></td>
                                <td><span class="badge-tv" style="background: #fef3c7; color: #92400e; padding: 3px 8px; border-radius: 6px; font-weight: 800;">{{ $ov->tv_channel }}</span></td>
                                <td>{{ $ov->notes ?? '-' }}</td>
                                <td>{{ $ov->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- PANEL 2: QUẢN LÝ BẢO TRÌ & XÓA CACHE THỦ CÔNG --}}
    <div class="panel-card">
        <h2 class="panel-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0M12 2v10"></path></svg>
            Quản Lý Chế Độ Bảo Trì & Xóa Cache Hệ Thống
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
            Dùng khi cần cập nhật hệ thống khẩn cấp hoặc xóa sạch bộ nhớ đệm Redis/File để dữ liệu mới nhất được làm mới ngay lập tức.
        </p>

        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            {{-- Nút Xóa Cache --}}
            <form action="{{ route('admin.cache.clear') }}" method="POST">
                @csrf
                <button type="submit" class="btn-action btn-warning" onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ Cache hệ thống?');">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
                    Xóa Sạch Cache Hệ Thống
                </button>
            </form>

            {{-- Nút Chuyển Đổi Bảo Trì --}}
            <form action="{{ route('admin.maintenance.toggle') }}" method="POST">
                @csrf
                <button type="submit" class="btn-action {{ $isMaintenance ? 'btn-primary' : 'btn-danger' }}" onclick="return confirm('Xác nhận chuyển đổi trạng thái bảo trì hệ thống?');">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    {{ $isMaintenance ? 'Tắt Chế Độ Bảo Trì (Mở Lại Website)' : 'Bật Chế Độ Bảo Trì (Tạm Dừng Website)' }}
                </button>
            </form>
        </div>
    </div>

    {{-- PANEL 3: QUẢN LÝ CÁC VỊ TRÍ ĐẶT BANNER QUẢNG CÁO --}}
    <div class="panel-card">
        <h2 class="panel-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
            Quản Lý Các Vị Trí Đặt Banner Quảng Cáo (Ads Placement)
        </h2>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
            Cấu hình ảnh banner và đường dẫn cho 3 vị trí vàng trên website: Đầu trang, Dưới tỷ số trận đấu, và Chân trang.
        </p>

        <div class="banner-grid">
            @php
                $positions = [
                    'banner_top' => 'Đầu Trang (Header Top Banner)',
                    'banner_below_score' => 'Dưới Tỷ Số (Match Score Hero)',
                    'banner_footer' => 'Chân Trang (Footer Banner)',
                ];
            @endphp

            @foreach($positions as $posKey => $posTitle)
                @php
                    $banner = $banners[$posKey] ?? null;
                @endphp
                <div class="banner-box">
                    <div class="banner-box-header">
                        <span class="banner-pos-tag">{{ $posKey }}</span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: {{ ($banner && $banner->is_active) ? '#059669' : '#94a3b8' }}">
                            {{ ($banner && $banner->is_active) ? '● Đang Bật' : '○ Đang Tắt' }}
                        </span>
                    </div>
                    <h3 style="font-size: 0.95rem; font-weight: 800; margin-bottom: 12px; color: var(--pl-purple);">{{ $posTitle }}</h3>

                    <form action="{{ route('admin.banner.save') }}" method="POST">
                        @csrf
                        <input type="hidden" name="position" value="{{ $posKey }}">

                        <div class="form-group" style="margin-bottom: 10px;">
                            <label class="form-label">Tiêu đề Banner</label>
                            <input type="text" name="title" class="form-control" value="{{ $banner->title ?? 'Banner ' . $posKey }}" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 10px;">
                            <label class="form-label">Link Ảnh Banner (Image URL)</label>
                            <input type="url" name="image_url" class="form-control" value="{{ $banner->image_url ?? '' }}" placeholder="https://example.com/banner.jpg">
                        </div>

                        <div class="form-group" style="margin-bottom: 10px;">
                            <label class="form-label">Đường Dẫn Đích (Target URL)</label>
                            <input type="url" name="target_url" class="form-control" value="{{ $banner->target_url ?? '' }}" placeholder="https://fptplay.vn...">
                        </div>

                        <div style="margin: 12px 0;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">
                                <input type="checkbox" name="is_active" value="1" {{ ($banner && $banner->is_active) ? 'checked' : '' }}>
                                Kích hoạt hiển thị banner này
                            </label>
                        </div>

                        <button type="submit" class="btn-action btn-primary" style="width: 100%;">
                            Lưu Banner
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
