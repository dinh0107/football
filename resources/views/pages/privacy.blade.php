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
            <li aria-current="page" style="color: var(--pl-pink); font-weight: 700;">Chính Sách Bảo Mật</li>
        </ol>
    </nav>

    {{-- Hero Header --}}
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: var(--radius-lg); padding: 36px 32px; color: #ffffff; margin-bottom: 32px; box-shadow: var(--shadow-md); position: relative; overflow: hidden;">
        <div style="position: absolute; right: -15px; bottom: -20px; opacity: 0.06; pointer-events: none;">
            <svg viewBox="0 0 24 24" style="width: 240px; height: 240px; fill: #ffffff;"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
        </div>
        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); padding: 4px 14px; border-radius: var(--radius-full); margin-bottom: 14px;">
            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span>
            <span style="font-size: 0.75rem; font-weight: 800; color: #34d399; text-transform: uppercase; letter-spacing: 0.05em;">Cam Kết Bảo Vệ Quyền Riêng Tư</span>
        </div>
        <h1 style="font-size: clamp(1.375rem, 5.6vw, 2rem); font-weight: 900; line-height: 1.25; margin-bottom: 12px; letter-spacing: -0.02em;">
            Chính Sách Bảo Mật Thông Tin
        </h1>
        <p style="font-size: clamp(0.9375rem, 3.85vw, 1.05rem); color: #cbd5e1; max-width: 680px; line-height: 1.6; margin: 0;">
            TySoNgoaiHang.site cam kết tôn trọng tuyệt đối quyền riêng tư và khẳng định không thu thập trái phép bất kỳ dữ liệu cá nhân nào của người dùng.
        </p>
    </div>

    {{-- Nội dung chính sách --}}
    <div style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 28px; line-height: 1.75; color: var(--text-muted); font-size: clamp(0.875rem, 3.6vw, 0.95rem);">
        
        <section>
            <h2 style="font-size: clamp(1.0625rem, 4.4vw, 1.25rem); font-weight: 800; color: var(--pl-purple); margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--pl-pink);"></span>
                1. Cam Kết Không Thu Thập Trái Phép Dữ Liệu
            </h2>
            <p>
                Website <strong>TySoNgoaiHang.site</strong> là cổng thông tin bóng đá mở. Bạn có thể tự do truy cập xem tỷ số, lịch thi đấu, bảng xếp hạng và các bài viết phân tích mà không bắt buộc phải đăng ký tài khoản hay cung cấp họ tên, số điện thoại, tài khoản ngân hàng hoặc địa chỉ nhà riêng.
            </p>
            <p>
                Chúng tôi <strong>tuyệt đối không</strong> sử dụng phần mềm gián điệp (spyware), không ghi lại phím bấm (keylogger), và không thực hiện bất kỳ hành vi thu thập trái phép dữ liệu người dùng nào vi phạm pháp luật Việt Nam và các tiêu chuẩn bảo mật quốc tế.
            </p>
        </section>

        <section>
            <h2 style="font-size: clamp(1.0625rem, 4.4vw, 1.25rem); font-weight: 800; color: var(--pl-purple); margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--pl-pink);"></span>
                2. Dữ Liệu Lưu Trữ Cục Bộ (LocalStorage & Cookie)
            </h2>
            <p>
                Để nâng cao trải nghiệm người dùng nhanh chóng và cá nhân hóa, hệ thống sử dụng bộ nhớ cục bộ trên trình duyệt thiết bị của bạn (HTML5 LocalStorage) cho các tính năng:
            </p>
            <ul style="padding-left: 24px; margin-top: 8px; margin-bottom: 8px;">
                <li><strong>Ghim đội bóng yêu thích:</strong> Ghi nhớ mã CLB bạn đã đánh dấu sao để tự động đẩy trận đấu đó lên đầu danh sách.</li>
                <li><strong>Tùy chọn âm thanh bàn thắng:</strong> Ghi nhớ trạng thái bật hoặc tắt chuông thông báo âm thanh khi có bàn thắng mới.</li>
                <li><strong>Bình chọn cộng đồng:</strong> Lưu tạm thời trạng thái bạn đã bình chọn cho trận đấu nào để tránh gửi trùng lặp.</li>
            </ul>
            <p>
                Các dữ liệu này <em>hoàn toàn nằm trên thiết bị cá nhân của bạn</em> và bạn có thể xóa bất cứ lúc nào thông qua tính năng Xóa dữ liệu duyệt web trên trình duyệt.
            </p>
        </section>

        <section>
            <h2 style="font-size: clamp(1.0625rem, 4.4vw, 1.25rem); font-weight: 800; color: var(--pl-purple); margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--pl-pink);"></span>
                3. Nhật Ký Kỹ Thuật Máy Chủ (Server Logs)
            </h2>
            <p>
                Như hầu hết các máy chủ web tiêu chuẩn trên thế giới, hệ thống của chúng tôi tự động ghi lại một số thông tin kỹ thuật cơ bản trong nhật ký máy chủ (Server Access Logs) bao gồm: địa chỉ IP truy cập, loại trình duyệt (User-Agent), thời gian truy cập và các đường dẫn yêu cầu (URL request).
            </p>
            <p>
                Mục đích duy nhất của việc lưu nhật ký này là để: giám sát an toàn hệ thống, ngăn chặn các cuộc tấn công từ chối dịch vụ (DDoS) và tối ưu hóa tốc độ tải trang thông qua hệ thống lưu đệm (Laravel Cache).
            </p>
        </section>

        <section>
            <h2 style="font-size: clamp(1.0625rem, 4.4vw, 1.25rem); font-weight: 800; color: var(--pl-purple); margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--pl-pink);"></span>
                4. Cam Kết Không Bán / Chia Sẻ Dữ Liệu Cho Bên Thứ Ba
            </h2>
            <p>
                Chúng tôi cam kết <strong>không bao giờ bán, cho thuê, thương mại hóa hoặc trao đổi</strong> thông tin liên hệ của người dùng cho bất kỳ bên thứ ba nào nhằm mục đích tiếp thị, quảng cáo rác (spam) hoặc gọi điện telesale làm phiền.
            </p>
        </section>

        <section>
            <h2 style="font-size: clamp(1.0625rem, 4.4vw, 1.25rem); font-weight: 800; color: var(--pl-purple); margin-bottom: 12px; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--pl-pink);"></span>
                5. Liên Hệ & Quyền Khiếu Nại
            </h2>
            <p>
                Nếu bạn có bất kỳ câu hỏi, thắc mắc hoặc yêu cầu nào liên quan đến chính sách bảo mật này, xin vui lòng gửi thư cho ban quản trị tại email: <a href="mailto:lienhe@tysongoaihang.site" style="color: var(--pl-pink); font-weight: 700;">lienhe@tysongoaihang.site</a>.
            </p>
        </section>

    </div>
</div>
@endsection
