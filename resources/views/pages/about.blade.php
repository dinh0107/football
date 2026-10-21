@extends('layouts.app')

@section('content')
<div class="page-container" style="max-width: 920px; margin: 0 auto; padding: 24px 0 48px;">
    {{-- Breadcrumb điều hướng --}}
    <nav class="breadcrumb-nav" aria-label="Breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb-list" style="display: flex; align-items: center; gap: 8px; list-style: none; padding: 0; margin: 0; font-size: 0.85rem; color: var(--text-dim);">
            <li>
                <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: 5px; color: var(--text-muted); font-weight: 600;">
                    <svg class="svg-icon" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                    Trang Chủ
                </a>
            </li>
            <li style="color: var(--border-light);">/</li>
            <li aria-current="page" style="color: var(--pl-pink); font-weight: 700;">Giới Thiệu</li>
        </ol>
    </nav>

    {{-- Hero Header --}}
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: var(--radius-lg); padding: 36px 32px; color: #ffffff; margin-bottom: 32px; box-shadow: var(--shadow-md); position: relative; overflow: hidden;">
        <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.06; pointer-events: none;">
            <svg viewBox="0 0 24 24" style="width: 260px; height: 260px; fill: #ffffff;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
        </div>
        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.35); padding: 4px 14px; border-radius: var(--radius-full); margin-bottom: 14px;">
            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #38bdf8;"></span>
            <span style="font-size: 0.75rem; font-weight: 800; color: #38bdf8; text-transform: uppercase; letter-spacing: 0.05em;">Về Dự Án Của Chúng Tôi</span>
        </div>
        <h1 style="font-size: 2rem; font-weight: 900; line-height: 1.25; margin-bottom: 12px; letter-spacing: -0.02em;">
            Giới Thiệu TySoNgoaiHang.site
        </h1>
        <p style="font-size: 1.05rem; color: #cbd5e1; max-width: 680px; line-height: 1.6; margin: 0;">
            Chuyên trang phi lợi nhuận cập nhật lịch thi đấu, tỷ số và phân tích số liệu Ngoại Hạng Anh dành cho cộng đồng hâm mộ bóng đá Việt Nam.
        </p>
    </div>

    {{-- Nội dung chi tiết --}}
    <div style="display: flex; flex-direction: column; gap: 24px;">
        {{-- Card 1: Mục đích hoạt động --}}
        <div style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: #eff6ff; display: flex; align-items: center; justify-content: center; color: var(--pl-pink);">
                    <svg class="svg-icon svg-icon-lg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--pl-purple); margin: 0;">Mục Đích Hoạt Động & Sứ Mệnh</h2>
            </div>
            <p style="font-size: 0.95rem; line-height: 1.7; color: var(--text-muted); margin-bottom: 14px;">
                <strong>TySoNgoaiHang.site</strong> được sáng lập bởi một nhóm các kỹ sư công nghệ và những người yêu mến bóng đá Ngoại Hạng Anh (English Premier League) tại Việt Nam. Website hoạt động với tiêu chí <strong>phi lợi nhuận 100%</strong>, ra đời với sứ mệnh mang đến cho cộng đồng người hâm mộ một nền tảng theo dõi bóng đá hiện đại, tốc độ cao, không quảng cáo rác độc hại và hoàn toàn bảo mật.
            </p>
            <p style="font-size: 0.95rem; line-height: 1.7; color: var(--text-muted); margin: 0;">
                Tại đây, bạn có thể dễ dàng tra cứu lịch thi đấu theo từng vòng, bảng xếp hạng trực tiếp theo thời gian thực (Live Table As-It-Stands), xem diễn biến từng phút của trận đấu và lịch phát sóng truyền hình chính thức trên các kênh tại Việt Nam.
            </p>
        </div>

        {{-- Card 2: Giá trị cốt lõi --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
            <div style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 22px; box-shadow: var(--shadow-sm);">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #059669; margin-bottom: 12px;">
                    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M13 2.05v3.03c3.39.49 6 3.39 6 6.92 0 .9-.18 1.75-.48 2.54l2.6 1.53c.56-1.24.88-2.62.88-4.07 0-5.18-3.95-9.45-9-9.95zM12 19c-3.87 0-7-3.13-7-7 0-3.53 2.61-6.43 6-6.92V2.05c-5.06.5-9 4.76-9 9.95 0 5.52 4.47 10 9.99 10 3.31 0 6.24-1.61 8.04-4.09l-2.54-1.51C15.82 17.72 14.04 19 12 19z"/></svg>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--pl-purple); margin-bottom: 8px;">Dữ Liệu Trực Tiếp PulseLive</h3>
                <p style="font-size: 0.875rem; line-height: 1.6; color: var(--text-muted); margin: 0;">
                    Toàn bộ lịch thi đấu, bàn thắng, đội hình và thông số kỹ thuật được đồng bộ trực tiếp từ nguồn API dữ liệu chính thức, đảm bảo tính chuẩn xác và tức thời.
                </p>
            </div>

            <div style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 22px; box-shadow: var(--shadow-sm);">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb; margin-bottom: 12px;">
                    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--pl-purple); margin-bottom: 8px;">Minh Bạch & Văn Minh</h3>
                <p style="font-size: 0.875rem; line-height: 1.6; color: var(--text-muted); margin: 0;">
                    Tuyệt đối không tiếp tay cho cờ bạc cá độ, không đặt pop-up hay mã độc hại. Trải nghiệm người dùng văn minh, sạch sẽ và an toàn tuyệt đối.
                </p>
            </div>

            <div style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 22px; box-shadow: var(--shadow-sm);">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #d97706; margin-bottom: 12px;">
                    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--pl-purple); margin-bottom: 8px;">Cộng Đồng Hâm Mộ Việt Nam</h3>
                <p style="font-size: 0.875rem; line-height: 1.6; color: var(--text-muted); margin: 0;">
                    Nơi kết nối các fan hâm mộ Arsenal, Man United, Liverpool, Chelsea, Man City... cùng bình chọn dự đoán tỷ số và giao lưu thể thao lành mạnh.
                </p>
            </div>
        </div>

        {{-- Card 3: Thông tin vận hành --}}
        <div style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm);">
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--pl-purple); margin-bottom: 14px;">Thông Tin Vận Hành & Bản Quyền</h2>
            <p style="font-size: 0.95rem; line-height: 1.7; color: var(--text-muted); margin-bottom: 14px;">
                Tên thương hiệu, hình ảnh logo các câu lạc bộ bóng đá, danh sách cầu thủ và tên giải đấu <em>Premier League</em> thuộc quyền sở hữu trí tuệ của ban tổ chức The Football Association Premier League Limited và các câu lạc bộ thành viên. Website sử dụng các thông tin này theo nguyên tắc thông tin thể thao công khai (Fair Use) và không nhằm mục đích thương mại hóa.
            </p>
            <div style="display: flex; gap: 14px; flex-wrap: wrap; margin-top: 20px;">
                <a href="{{ route('contact') }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: var(--radius-full); background: var(--pl-purple); color: #ffffff; font-weight: 700; font-size: 0.875rem;">
                    Liên hệ với chúng tôi &rarr;
                </a>
                <a href="{{ route('disclaimer') }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: var(--radius-full); background: #f1f5f9; color: var(--text-main); font-weight: 700; font-size: 0.875rem; border: 1px solid var(--border-light);">
                    Tuyên bố miễn trừ trách nhiệm
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
