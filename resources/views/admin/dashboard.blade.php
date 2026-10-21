@extends('layouts.admin')

@section('title', 'Dashboard Quản Trị Hệ Thống')

@section('content')
{{-- Page Header --}}
<div class="page-header d-print-none mb-4">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-uppercase text-secondary fw-bold">Tổng Quan Hệ Thống</div>
            <h2 class="page-title fw-black fs-2 text-dark">
                Bảng Điều Khiển Quản Trị
            </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary d-none d-sm-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                    Xem Trang Chủ
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="alert alert-important alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
            </div>
            <div>{{ session('success') }}</div>
        </div>
        <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-important alert-danger alert-dismissible" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 17h.01" /></svg>
            </div>
            <div>{{ session('error') }}</div>
        </div>
        <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

{{-- 4 THẺ THỐNG KÊ TABLER STATS --}}
<div class="row row-deck row-cards mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-primary-lt">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M16 3l-4 4l-4 -4" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-secondary text-uppercase fs-6">Ghi Đè Kênh TV Guide</div>
                        <div class="h1 mb-0">{{ $overrides->count() }}</div>
                        <div class="text-success small fw-bold">Đã phân bổ kênh</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar {{ $isMaintenance ? 'bg-danger-lt' : 'bg-success-lt' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-secondary text-uppercase fs-6">Trạng Thái Hệ Thống</div>
                        <div class="h2 mb-0">{{ $isMaintenance ? 'Bảo Trì (Down)' : 'Hoạt Động (Live)' }}</div>
                        <div class="{{ $isMaintenance ? 'text-danger' : 'text-success' }} small fw-bold">
                            {{ $isMaintenance ? 'Tạm dừng phục vụ' : 'Đang phục vụ bình thường' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-purple-lt">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 9h16" /><path d="M9 4v16" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-secondary text-uppercase fs-6">Bộ Nhớ Cache</div>
                        <div class="h2 mb-0">{{ strtoupper(config('cache.default', 'file')) }}</div>
                        <div class="text-secondary small fw-bold">High Concurrency</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar bg-yellow-lt">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-secondary text-uppercase fs-6">Banner Đang Bật</div>
                        <div class="h1 mb-0">{{ collect($banners)->filter(fn($b) => $b && $b->is_active)->count() }} / 3</div>
                        <div class="text-secondary small fw-bold">Vị trí quảng cáo</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PANEL 1: CẬP NHẬT KÊNH TV GUIDE --}}
<div class="card mb-4" id="panel-tv">
    <div class="card-header bg-light">
        <div>
            <h3 class="card-title fw-bold text-dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M16 3l-4 4l-4 -4" /></svg>
                Cập Nhật Nhanh Kênh Phát Sóng TV Guide Cho Trận Đấu
            </h3>
            <p class="card-subtitle text-secondary mb-0">Chỉ định kênh độc quyền (K+ Sport 1, TV360, FPT Play, VTVcab ON) cho từng trận đấu để hiển thị chính xác cho người dùng.</p>
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.tv_channel.update') }}" method="POST">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="match_id">Chọn Trận Đấu</label>
                    <select id="match_id" name="match_id" class="form-select" required>
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

                <div class="col-md-3">
                    <label class="form-label fw-bold" for="tv_channel">Kênh Phát Sóng (TV Guide)</label>
                    <select id="tv_channel" name="tv_channel" class="form-select" required>
                        <option value="K+ Sport 1">K+ Sport 1 (Độc quyền)</option>
                        <option value="TV360">TV360 (Viettel)</option>
                        <option value="FPT Play">FPT Play</option>
                        <option value="VTVcab ON">VTVcab ON</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" for="notes">Ghi chú (Tùy chọn)</label>
                    <input type="text" id="notes" name="notes" class="form-control" placeholder="Trận cầu tâm điểm Big 6...">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        Lưu Kênh TV
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($overrides->isNotEmpty())
        <div class="table-responsive border-top">
            <table class="table table-vcenter table-hover card-table">
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
                            <td><span class="badge bg-dark-lt fw-bold">#{{ $ov->match_id }}</span></td>
                            <td><span class="badge bg-yellow-lt fw-bold fs-5">{{ $ov->tv_channel }}</span></td>
                            <td><span class="text-secondary">{{ $ov->notes ?? '-' }}</span></td>
                            <td><span class="text-muted small">{{ $ov->updated_at->format('d/m/Y H:i') }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- PANEL 2: QUẢN LÝ BẢO TRÌ & XÓA CACHE --}}
<div class="card mb-4" id="panel-maintenance">
    <div class="card-header bg-light">
        <div>
            <h3 class="card-title fw-bold text-dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-warning" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                Quản Lý Chế Độ Bảo Trì & Xóa Cache Hệ Thống
            </h3>
            <p class="card-subtitle text-secondary mb-0">Dùng khi cần cập nhật hệ thống khẩn cấp hoặc xóa sạch bộ nhớ đệm để dữ liệu mới nhất được làm mới ngay lập tức.</p>
        </div>
    </div>

    <div class="card-body">
        <div class="d-flex flex-wrap gap-3">
            {{-- Nút Xóa Cache --}}
            <form action="{{ route('admin.cache.clear') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-warning" onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ Cache hệ thống?');">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" /></svg>
                    Xóa Sạch Cache Hệ Thống
                </button>
            </form>

            {{-- Nút Chuyển Đổi Bảo Trì --}}
            <form action="{{ route('admin.maintenance.toggle') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn {{ $isMaintenance ? 'btn-success' : 'btn-danger' }}" onclick="return confirm('Xác nhận chuyển đổi trạng thái bảo trì hệ thống?');">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>
                    {{ $isMaintenance ? 'Tắt Chế Độ Bảo Trì (Mở Lại Website)' : 'Bật Chế Độ Bảo Trì (Tạm Dừng Website)' }}
                </button>
            </form>
        </div>
    </div>
</div>

{{-- PANEL 3: QUẢN LÝ VỊ TRÍ BANNER QUẢNG CÁO --}}
<div class="card" id="panel-banners">
    <div class="card-header bg-light">
        <div>
            <h3 class="card-title fw-bold text-dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 9h16" /><path d="M9 4v16" /></svg>
                Quản Lý Các Vị Trí Đặt Banner Quảng Cáo (Ads Placement)
            </h3>
            <p class="card-subtitle text-secondary mb-0">Cấu hình ảnh banner và đường dẫn cho 3 vị trí vàng trên website: Đầu trang, Dưới tỷ số trận đấu, và Chân trang.</p>
        </div>
    </div>

    <div class="card-body">
        <div class="row row-cards">
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
                <div class="col-md-4">
                    <div class="card bg-light border">
                        <div class="card-status-top {{ ($banner && $banner->is_active) ? 'bg-success' : 'bg-secondary' }}"></div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-blue-lt text-uppercase">{{ $posKey }}</span>
                                <span class="badge {{ ($banner && $banner->is_active) ? 'bg-success-lt' : 'bg-secondary-lt' }}">
                                    {{ ($banner && $banner->is_active) ? 'Đang Bật' : 'Đang Tắt' }}
                                </span>
                            </div>

                            <h4 class="card-title fw-bold mb-3 text-dark">{{ $posTitle }}</h4>

                            <form action="{{ route('admin.banner.save') }}" method="POST">
                                @csrf
                                <input type="hidden" name="position" value="{{ $posKey }}">

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Tiêu đề Banner</label>
                                    <input type="text" name="title" class="form-control form-control-sm" value="{{ $banner->title ?? 'Banner ' . $posKey }}" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Link Ảnh Banner (Image URL)</label>
                                    <input type="url" name="image_url" class="form-control form-control-sm" value="{{ $banner->image_url ?? '' }}" placeholder="https://example.com/banner.jpg">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Đường Dẫn Đích (Target URL)</label>
                                    <input type="url" name="target_url" class="form-control form-control-sm" value="{{ $banner->target_url ?? '' }}" placeholder="https://fptplay.vn...">
                                </div>

                                <div class="mb-3">
                                    <label class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ ($banner && $banner->is_active) ? 'checked' : '' }}>
                                        <span class="form-check-label fw-bold small">Kích hoạt hiển thị banner này</span>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                    Lưu Banner
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
