# Quy tắc Thiết kế Giao diện (UI Guidelines)

## Tuyệt đối KHÔNG sử dụng Emoji thô làm icon
- KHÔNG bao giờ sử dụng các emoji hệ điều hành thô/kém chuyên nghiệp như: ⚽, 🟨, 🟥, 🔄, ⏱️, 🏁, 👟, 🤖, 🗳️, 📊, 📋, ⚡, ⭐, 🔔, 🏆, 🔥, 👍, 👥 trong giao diện web.
- LUÔN LUÔN sử dụng **Vector SVG tinh gọn, sắc nét** (inline SVG với `stroke`, `fill`, `viewBox` chuẩn tỉ lệ 16x16, 20x20 hoặc 24x24) hoặc icon CSS vector hiện đại.
- Đối với thẻ phạt (Thẻ vàng / Thẻ đỏ): Dùng thẻ CSS hình chữ nhật bo góc tinh tế (`width: 14px; height: 18px; border-radius: 3px; background: ...`) thay vì emoji `🟨` / `🟥`.
- Đối với bóng đá / bàn thắng: Dùng SVG soccer ball sắc sảo thay vì emoji `⚽`.
- Đối với thay người: Dùng SVG mũi tên lên/xuống (Up/Down arrow) vector hoặc badge CSS hiện đại thay vì `🔄`, `▲`, `▼`.
- Giữ phong cách thiết kế tối giản, cao cấp, thanh lịch chuẩn các ứng dụng thể thao hàng đầu (Sofascore, FotMob, Premier League Official).
