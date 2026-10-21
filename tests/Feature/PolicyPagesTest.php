<?php

namespace Tests\Feature;

use Tests\TestCase;

class PolicyPagesTest extends TestCase
{
    /**
     * Kiểm tra trang Giới thiệu (About Us)
     */
    public function test_about_page_loads_successfully(): void
    {
        $response = $this->get('/gioi-thieu');

        $response->assertStatus(200);
        $response->assertSee('Giới Thiệu TySoNgoaiHang.site');
        $response->assertSee('Chuyên trang phi lợi nhuận cập nhật lịch thi đấu, tỷ số và phân tích số liệu Ngoại Hạng Anh dành cho cộng đồng hâm mộ bóng đá Việt Nam');
        $response->assertSee('PulseLive');
    }

    /**
     * Kiểm tra trang Liên hệ (Contact)
     */
    public function test_contact_page_loads_and_displays_email(): void
    {
        $response = $this->get('/lien-he');

        $response->assertStatus(200);
        $response->assertSee('lienhe@tysongoaihang.site');
        $response->assertSee('Gửi Tin Nhắn Cho Chúng Tôi');
        $response->assertSee('name="name"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="subject"', false);
        $response->assertSee('name="message"', false);
    }

    /**
     * Kiểm tra gửi Form Liên hệ thành công
     */
    public function test_send_contact_form_validates_and_redirects(): void
    {
        $response = $this->post('/lien-he', [
            'name' => 'Nguyễn Văn Test',
            'email' => 'nguyenvantest@gmail.com',
            'subject' => 'Góp ý giao diện di động',
            'message' => 'Giao diện di động rất mượt mà và dễ sử dụng.',
        ]);

        $response->assertRedirect('/lien-he');
        $response->assertSessionHas('success');
    }

    /**
     * Kiểm tra validation lỗi khi form liên hệ thiếu trường bắt buộc
     */
    public function test_send_contact_form_validation_errors(): void
    {
        $response = $this->post('/lien-he', [
            'name' => '',
            'email' => 'email-khong-hop-le',
            'subject' => '',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    /**
     * Kiểm tra trang Chính sách bảo mật (Privacy Policy)
     */
    public function test_privacy_policy_page_loads(): void
    {
        $response = $this->get('/chinh-sach-bao-mat');

        $response->assertStatus(200);
        $response->assertSee('Chính Sách Bảo Mật');
        $response->assertSee('không thu thập trái phép');
        $response->assertSee('LocalStorage');
        $response->assertSee('quyền riêng tư');
    }

    /**
     * Kiểm tra trang Tuyên bố bản quyền & Miễn trừ trách nhiệm (Disclaimer)
     */
    public function test_disclaimer_page_loads(): void
    {
        $response = $this->get('/mien-tru-trach-nhiem');

        $response->assertStatus(200);
        $response->assertSee('Tuyên Bố Bản Quyền &amp; Miễn Trừ Trách Nhiệm', false);
        $response->assertSee('dữ liệu thống kê thể thao công khai');
        $response->assertSee('không tổ chức cá độ');
        $response->assertSee('không lưu trữ luồng phát sóng lậu');
    }

    /**
     * Kiểm tra chuyển hướng 301 cho các đường dẫn tiếng Anh
     */
    public function test_english_aliases_redirect_301(): void
    {
        $this->get('/about')->assertRedirect('/gioi-thieu');
        $this->get('/contact')->assertRedirect('/lien-he');
        $this->get('/privacy')->assertRedirect('/chinh-sach-bao-mat');
        $this->get('/disclaimer')->assertRedirect('/mien-tru-trach-nhiem');
    }
}
