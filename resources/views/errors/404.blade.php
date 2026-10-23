@php
    $seo = [
        'title' => 'Không tìm thấy trang | Tỷ Số Ngoại Hạng Anh',
        'description' => 'Trang bạn tìm không tồn tại hoặc đã được chuyển. Xem lịch thi đấu, tỷ số trực tiếp và bảng xếp hạng Ngoại Hạng Anh.',
        'canonical' => url()->current(),
        'robots' => 'noindex, follow',
    ];
@endphp
@extends('layouts.app')

@section('content')
<div style="max-width: 640px; margin: 0 auto; padding: 56px 0 72px; text-align: center;">
    <div style="font-size: 4rem; font-weight: 900; color: var(--pl-pink); letter-spacing: -0.04em; line-height: 1;">404</div>
    <h1 style="font-size: 1.6rem; font-weight: 900; margin: 16px 0 10px;">Không tìm thấy trang</h1>
    <p style="color: var(--text-muted); line-height: 1.6; margin: 0 0 28px;">
        Trang bạn tìm không tồn tại, đã bị xoá hoặc trận đấu chưa có dữ liệu. Bạn có thể quay lại các mục chính dưới đây.
    </p>
    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
        <a href="{{ route('home') }}" style="background: var(--pl-purple); color: #ffffff; padding: 10px 20px; border-radius: var(--radius-full); font-weight: 700;">Trang chủ</a>
        <a href="{{ route('fixtures') }}" style="border: 1px solid var(--border-light); padding: 10px 20px; border-radius: var(--radius-full); font-weight: 700;">Lịch thi đấu</a>
        <a href="{{ route('standings') }}" style="border: 1px solid var(--border-light); padding: 10px 20px; border-radius: var(--radius-full); font-weight: 700;">Bảng xếp hạng</a>
        <a href="{{ route('live') }}" style="border: 1px solid var(--border-light); padding: 10px 20px; border-radius: var(--radius-full); font-weight: 700;">Trực tiếp</a>
    </div>
</div>
@endsection
