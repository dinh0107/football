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
            <li aria-current="page" style="color: var(--pl-pink); font-weight: 700;">Miễn Trừ Trách Nhiệm</li>
        </ol>
    </nav>

    {{-- Hero Header --}}
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: var(--radius-lg); padding: 36px 32px; color: #ffffff; margin-bottom: 32px; box-shadow: var(--shadow-md); position: relative; overflow: hidden;">
        <div style="position: absolute; right: -15px; bottom: -20px; opacity: 0.06; pointer-events: none;">
            <svg viewBox="0 0 24 24" style="width: 240px; height: 240px; fill: #ffffff;"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
        </div>
        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); padding: 4px 14px; border-radius: var(--radius-full); margin-bottom: 14px;">
            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #f59e0b;"></span>
            <span style="font-size: 0.75rem; font-weight: 800; color: #fbbf24; text-transform: uppercase; letter-spacing: 0.05em;">Tuyên Bố Pháp Lý</span>
        </div>
        <h1 style="font-size: clamp(1.375rem, 5.6vw, 2rem); font-weight: 900; line-height: 1.25; margin-bottom: 12px; letter-spacing: -0.02em;">
            Tuyên Bố Bản Quyền & Miễn Trừ Trách Nhiệm
        </h1>
        <p style="font-size: clamp(0.9375rem, 3.85vw, 1.05rem); color: #cbd5e1; max-width: 680px; line-height: 1.6; margin: 0;">
            Quy định pháp lý, phạm vi trách nhiệm dữ liệu và các nguyên tắc hoạt động phi thương mại tại TySoNgoaiHang.site.
        </p>
    </div>

    {{-- Nội dung tuyên bố --}}
    <div style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 28px; line-height: 1.75; color: var(--text-muted); font-size: clamp(0.875rem, 3.6vw, 0.95rem);">
        
        {{-- Mục 1: Dữ liệu công khai --}}
        <section>
            <h2 style="font-size: clamp(1.0625rem, 4.4vw, 1.25rem); font-weight: 800; color: var(--pl-purple); margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--pl-pink);"></span>
                1. Tính Chất Dữ Liệu Thể Thao Công Khai
            </h2>
            <p>
                <strong>TySoNgoaiHang.site</strong> là cổng thông tin điện tử chuyên tổng hợp và hiển thị các số liệu thống kê thể thao công khai (Public Sports Statistics) từ giải Ngoại Hạng Anh (EPL). Mọi dữ liệu bao gồm: lịch thi đấu, tỷ số trực tiếp, thông số trận đấu, danh sách ghi bàn và bảng xếp hạng được trích xuất từ các API thể thao chính thống và nhà cung cấp dữ liệu PulseLive.
            </p>
            <p>
                Chúng tôi nỗ lực tối đa để đảm bảo dữ liệu được cập nhật nhanh chóng và chính xác nhất theo thời gian thực. Tuy nhiên, website không đảm bảo tuyệt đối rằng hệ thống sẽ không có độ trễ do đường truyền internet hoặc sự cố từ nhà cung cấp dữ liệu nguồn.
            </p>
        </section>

        {{-- Mục 2: Tuyệt đối không cá độ --}}
        <section style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: var(--radius-md); padding: 22px; color: #92400e;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <svg class="svg-icon svg-icon-lg" viewBox="0 0 24 24" style="color: #d97706; flex-shrink: 0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                <h2 style="font-size: clamp(1rem, 4.1vw, 1.15rem); font-weight: 800; color: #78350f; margin: 0;">
                    2. Nghiêm Cấm & Không Tổ Chức Cá Độ Dưới Mọi Hình Thức
                </h2>
            </div>
            <p style="margin-bottom: 8px; font-size: clamp(0.875rem, 3.6vw, 0.925rem);">
                Website <strong>KHÔNG</strong> phải là nhà cái, <strong>KHÔNG</strong> tổ chức, nhận cược, tiếp tay, môi giới hay quảng cáo cho các hoạt động cá độ bóng đá, cờ bạc trực tuyến trái phép theo quy định của pháp luật nước Cộng hòa Xã hội Chủ nghĩa Việt Nam.
            </p>
            <p style="margin: 0; font-size: 0.875rem;">
                Tính năng "Bình chọn dự đoán kết quả" trên website hoàn toàn là hoạt động khảo sát ý kiến mang tính giải trí cộng đồng, không có bất kỳ giải thưởng hay giao dịch tiền tệ nào.
            </p>
        </section>

        {{-- Mục 3: Không lưu trữ luồng phát lậu --}}
        <section style="background: #f8fafc; border: 1.5px solid var(--border-light); border-radius: var(--radius-md); padding: 22px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <svg class="svg-icon svg-icon-lg" viewBox="0 0 24 24" style="color: #dc2626; flex-shrink: 0;"><path d="M21 3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h5v2h8v-2h5c1.1 0 1.99-.9 1.99-2L23 5c0-1.1-.9-2-2-2zm0 14H3V5h18v12z"/></svg>
                <h2 style="font-size: clamp(1rem, 4.1vw, 1.15rem); font-weight: 800; color: var(--pl-purple); margin: 0;">
                    3. Không Lưu Trữ Hoặc Tiếp Sóng Luồng Video Lậu (No Pirated Streams)
                </h2>
            </div>
            <p style="margin-bottom: 8px; font-size: clamp(0.875rem, 3.6vw, 0.925rem);">
                Website <strong>TUYỆT ĐỐI KHÔNG</strong> phát sóng, tiếp sóng, nhúng liên kết lậu hoặc lưu trữ bất kỳ luồng video trực tiếp (video streams) nào vi phạm bản quyền truyền hình.
            </p>
            <p style="margin: 0; font-size: 0.875rem;">
                Mục "Kênh phát sóng" hiển thị trên trang lịch thi đấu chỉ nhằm mục đích hướng dẫn người xem theo dõi trên các đài truyền hình và đơn vị sở hữu bản quyền hợp pháp tại Việt Nam (như K+, FPT Play, TV360, VTVCab...).
            </p>
        </section>

        {{-- Mục 4: Sở hữu trí tuệ --}}
        <section>
            <h2 style="font-size: clamp(1.0625rem, 4.4vw, 1.25rem); font-weight: 800; color: var(--pl-purple); margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--pl-pink);"></span>
                4. Quyền Sở Hữu Trí Tuệ & Thương Hiệu
            </h2>
            <p>
                Tất cả tên gọi câu lạc bộ, biểu trưng (logo), hình ảnh cầu thủ và thương hiệu giải đấu <em>Premier League</em> đều thuộc quyền sở hữu độc quyền của <strong>The Football Association Premier League Limited</strong> và các câu lạc bộ thành viên.
            </p>
            <p>
                Việc website sử dụng các hình ảnh nhận diện này chỉ phục vụ mục đích thông tin, nhận diện câu lạc bộ trong kết quả thi đấu thể thao cho cộng đồng fan hâm mộ (Fair Use) và không hàm ý bất kỳ sự bảo trợ thương mại chính thức nào.
            </p>
        </section>

        {{-- Mục 5: Yêu cầu gỡ bỏ --}}
        <section>
            <h2 style="font-size: clamp(1.0625rem, 4.4vw, 1.25rem); font-weight: 800; color: var(--pl-purple); margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--pl-pink);"></span>
                5. Khiếu Nại Bản Quyền (DMCA & Gỡ Bỏ)
            </h2>
            <p>
                Nếu bạn là chủ sở hữu bản quyền hoặc đại diện pháp lý và nhận thấy bất kỳ nội dung nào trên website có dấu hiệu vi phạm quyền tác giả, xin vui lòng gửi thông báo kèm bằng chứng sở hữu đến hòm thư:
            </p>
            <p style="margin-top: 6px;">
                Email tiếp nhận: <a href="mailto:lienhe@tysongoaihang.site" style="color: var(--pl-pink); font-weight: 800; font-size: clamp(0.9375rem, 3.85vw, 1.05rem);">lienhe@tysongoaihang.site</a>
            </p>
            <p style="margin-top: 6px; font-size: 0.875rem;">
                Chúng tôi sẽ tiến hành xác minh và gỡ bỏ nội dung liên quan trong vòng 24 - 48 giờ làm việc sau khi nhận được yêu cầu hợp lệ.
            </p>
        </section>

    </div>
</div>
@endsection
