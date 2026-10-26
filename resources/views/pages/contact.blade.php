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
            <li aria-current="page" style="color: var(--pl-pink); font-weight: 700;">Liên Hệ</li>
        </ol>
    </nav>

    {{-- Hero Header --}}
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: var(--radius-lg); padding: 36px 32px; color: #ffffff; margin-bottom: 32px; box-shadow: var(--shadow-md); position: relative; overflow: hidden;">
        <div style="position: absolute; right: -15px; bottom: -20px; opacity: 0.06; pointer-events: none;">
            <svg viewBox="0 0 24 24" style="width: 240px; height: 240px; fill: #ffffff;"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
        </div>
        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.35); padding: 4px 14px; border-radius: var(--radius-full); margin-bottom: 14px;">
            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #38bdf8;"></span>
            <span style="font-size: 0.75rem; font-weight: 800; color: #38bdf8; text-transform: uppercase; letter-spacing: 0.05em;">Kênh Phản Hồi Trực Tiếp</span>
        </div>
        <h1 style="font-size: clamp(1.375rem, 5.6vw, 2rem); font-weight: 900; line-height: 1.25; margin-bottom: 12px; letter-spacing: -0.02em;">
            Liên Hệ Ban Quản Trị
        </h1>
        <p style="font-size: clamp(0.9375rem, 3.85vw, 1.05rem); color: #cbd5e1; max-width: 680px; line-height: 1.6; margin: 0;">
            Mọi ý kiến đóng góp, phản ánh lỗi kỹ thuật hoặc thắc mắc về dữ liệu trận đấu, vui lòng gửi qua email hoặc mẫu liên hệ dưới đây.
        </p>
    </div>

    {{-- Thông báo gửi thành công --}}
    @if(session('success'))
        <div style="background: #ecfdf5; border: 1.5px solid #10b981; border-radius: var(--radius-md); padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; color: #065f46;">
            <svg class="svg-icon svg-icon-lg" viewBox="0 0 24 24" style="color: #059669; flex-shrink: 0;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            <div>
                <strong style="font-size: clamp(0.875rem, 3.6vw, 0.95rem);">Gửi liên hệ thành công!</strong>
                <p style="font-size: 0.85rem; margin-top: 2px; margin-bottom: 0;">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- Lỗi Validation nếu có --}}
    @if($errors->any())
        <div style="background: #fef2f2; border: 1.5px solid #ef4444; border-radius: var(--radius-md); padding: 16px 20px; margin-bottom: 24px; color: #991b1b;">
            <strong style="font-size: clamp(0.875rem, 3.6vw, 0.95rem); display: block; margin-bottom: 6px;">Vui lòng kiểm tra lại thông tin:</strong>
            <ul style="margin: 0; padding-left: 20px; font-size: 0.85rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 24px;">
        {{-- Cột trái: Thông tin liên hệ trực tiếp --}}
        <div style="display: flex; flex-direction: column; gap: 18px;">
            <div style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
                <h2 style="font-size: clamp(1rem, 4.1vw, 1.15rem); font-weight: 800; color: var(--pl-purple); margin-bottom: 16px;">
                    Thông Tin Liên Hệ
                </h2>

                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; gap: 12px; align-items: flex-start;">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: var(--pl-pink); flex-shrink: 0;">
                            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        </div>
                        <div>
                            <span style="font-size: 0.775rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700; display: block;">Email Chính Thức</span>
                            <a href="mailto:lienhe@tysongoaihang.site" style="font-size: clamp(0.875rem, 3.6vw, 0.95rem); font-weight: 800; color: var(--pl-pink); word-break: break-all;">
                                lienhe@tysongoaihang.site
                            </a>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; align-items: flex-start;">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #059669; flex-shrink: 0;">
                            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        </div>
                        <div>
                            <span style="font-size: 0.775rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700; display: block;">Địa Chỉ Dự Án</span>
                            <span style="font-size: 0.9rem; font-weight: 600; color: var(--text-main);">
                                Hà Nội & TP. Hồ Chí Minh, Việt Nam
                            </span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; align-items: flex-start;">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #d97706; flex-shrink: 0;">
                            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                        </div>
                        <div>
                            <span style="font-size: 0.775rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700; display: block;">Thời Gian Phản Hồi</span>
                            <span style="font-size: 0.9rem; font-weight: 600; color: var(--text-main);">
                                Thông thường trong vòng 24 giờ làm việc
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px dashed var(--border-light); border-radius: var(--radius-md); padding: 18px; font-size: 0.825rem; color: var(--text-muted); line-height: 1.6;">
                <strong>Lưu ý:</strong> Chúng tôi là website tin tức thể thao phi lợi nhuận. Ban biên tập không cung cấp dịch vụ cá độ, không bán tips bóng đá và không phát sóng trực tiếp lậu. Vui lòng không gửi các yêu cầu liên quan đến các vấn đề này.
            </div>
        </div>

        {{-- Cột phải: Form liên hệ --}}
        <div style="background: #ffffff; border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-sm);">
            <h2 style="font-size: clamp(1.0625rem, 4.4vw, 1.25rem); font-weight: 800; color: var(--pl-purple); margin-bottom: 8px;">
                Gửi Tin Nhắn Cho Chúng Tôi
            </h2>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 20px;">
                Vui lòng điền thông tin bên dưới, chúng tôi sẽ tiếp nhận và xử lý sớm nhất.
            </p>

            <form action="{{ route('contact.send') }}" method="POST">
                @csrf

                <div style="margin-bottom: 16px;">
                    <label for="name" style="display: block; font-size: 0.825rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        Họ và tên của bạn <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Ví dụ: Nguyễn Văn A"
                           style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1.5px solid var(--border-light); font-family: inherit; font-size: 0.9rem; outline: none; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--pl-pink)'" onblur="this.style.borderColor='var(--border-light)'">
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="email" style="display: block; font-size: 0.825rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        Địa chỉ Email của bạn <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           placeholder="Ví dụ: emailcuaban@gmail.com"
                           style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1.5px solid var(--border-light); font-family: inherit; font-size: 0.9rem; outline: none; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--pl-pink)'" onblur="this.style.borderColor='var(--border-light)'">
                </div>

                <div style="margin-bottom: 16px;">
                    <label for="subject" style="display: block; font-size: 0.825rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        Chủ đề liên hệ <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                           placeholder="Ví dụ: Báo lỗi hiển thị tỷ số trận đấu..."
                           style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1.5px solid var(--border-light); font-family: inherit; font-size: 0.9rem; outline: none; transition: border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--pl-pink)'" onblur="this.style.borderColor='var(--border-light)'">
                </div>

                <div style="margin-bottom: 22px;">
                    <label for="message" style="display: block; font-size: 0.825rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        Nội dung tin nhắn <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea id="message" name="message" rows="5" required
                              placeholder="Chi tiết phản hồi hoặc nội dung bạn cần hỗ trợ..."
                              style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1.5px solid var(--border-light); font-family: inherit; font-size: 0.9rem; outline: none; resize: vertical; transition: border-color 0.2s;"
                              onfocus="this.style.borderColor='var(--pl-pink)'" onblur="this.style.borderColor='var(--border-light)'">{{ old('message') }}</textarea>
                </div>

                <button type="submit" style="width: 100%; padding: 12px 24px; border-radius: var(--radius-full); background: var(--pl-purple); color: #ffffff; font-weight: 800; font-size: clamp(0.875rem, 3.6vw, 0.95rem); border: none; cursor: pointer; transition: background 0.2s; box-shadow: var(--shadow-sm);"
                        onmouseover="this.style.background='var(--pl-purple-light)'" onmouseout="this.style.background='var(--pl-purple)'">
                    Gửi Tin Nhắn Liên Hệ &rarr;
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
