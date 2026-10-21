<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    /**
     * Trang Giới thiệu (About Us)
     */
    public function about(): View
    {
        $seo = [
            'title' => 'Giới Thiệu Về Chúng Tôi | Chuyên Trang Ngoại Hạng Anh tysongoaihang.site',
            'description' => 'Chuyên trang phi lợi nhuận cập nhật lịch thi đấu, tỷ số và phân tích số liệu Ngoại Hạng Anh dành cho cộng đồng hâm mộ bóng đá Việt Nam.',
            'canonical' => route('about'),
            'type' => 'website',
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => 'Giới thiệu Premier League Live Tracker',
                'url' => route('about'),
                'description' => 'Chuyên trang phi lợi nhuận cập nhật lịch thi đấu, tỷ số và phân tích số liệu Ngoại Hạng Anh dành cho cộng đồng hâm mộ bóng đá Việt Nam.',
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'tysongoaihang.site',
                    'url' => url('/'),
                    'logo' => asset('images/logo.png'),
                ],
            ],
        ];

        return view('pages.about', compact('seo'));
    }

    /**
     * Trang Liên hệ (Contact)
     */
    public function contact(): View
    {
        $seo = [
            'title' => 'Liên Hệ Với Ban Biên Tập | tysongoaihang.site',
            'description' => 'Thông tin liên hệ, gửi phản hồi, đóng góp ý kiến hoặc báo lỗi dữ liệu cho ban quản trị chuyên trang tysongoaihang.site qua email lienhe@tysongoaihang.site.',
            'canonical' => route('contact'),
            'type' => 'website',
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'ContactPage',
                'name' => 'Liên hệ tysongoaihang.site',
                'url' => route('contact'),
                'description' => 'Liên hệ ban biên tập và quản trị viên website theo dõi bóng đá Ngoại Hạng Anh.',
                'mainEntity' => [
                    '@type' => 'Organization',
                    'name' => 'tysongoaihang.site',
                    'email' => 'lienhe@tysongoaihang.site',
                    'url' => url('/'),
                ],
            ],
        ];

        return view('pages.contact', compact('seo'));
    }

    /**
     * Xử lý gửi Form Liên hệ
     */
    public function sendContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'email.required' => 'Vui lòng cung cấp địa chỉ email hợp lệ.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'subject.required' => 'Vui lòng nhập chủ đề liên hệ.',
            'message.required' => 'Vui lòng nhập nội dung tin nhắn liên hệ.',
            'message.max' => 'Nội dung tin nhắn không được vượt quá 2000 ký tự.',
        ]);

        Log::info('Liên hệ mới từ người dùng:', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'ip' => $request->ip(),
            'message_snippet' => mb_substr($validated['message'], 0, 100),
        ]);

        return redirect()->route('contact')->with('success', 'Cảm ơn bạn đã liên hệ! Ban biên tập tysongoaihang.site đã nhận được thông tin và sẽ phản hồi sớm nhất qua email của bạn.');
    }

    /**
     * Trang Chính sách bảo mật (Privacy Policy)
     */
    public function privacy(): View
    {
        $seo = [
            'title' => 'Chính Sách Bảo Mật Thông Tin | tysongoaihang.site',
            'description' => 'Chính sách bảo mật thông tin người dùng tại tysongoaihang.site. Cam kết không thu thập trái phép dữ liệu cá nhân, minh bạch về cookie và lưu trữ trình duyệt.',
            'canonical' => route('privacy'),
            'type' => 'website',
        ];

        return view('pages.privacy', compact('seo'));
    }

    /**
     * Trang Tuyên bố bản quyền & Miễn trừ trách nhiệm (Disclaimer)
     */
    public function disclaimer(): View
    {
        $seo = [
            'title' => 'Tuyên Bố Bản Quyền & Miễn Trừ Trách Nhiệm | tysongoaihang.site',
            'description' => 'Tuyên bố bản quyền và miễn trừ trách nhiệm pháp lý. Website chỉ tổng hợp dữ liệu thống kê thể thao công khai, không tổ chức cá độ và không lưu trữ luồng phát sóng lậu.',
            'canonical' => route('disclaimer'),
            'type' => 'website',
        ];

        return view('pages.disclaimer', compact('seo'));
    }
}
