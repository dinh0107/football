# HƯỚNG DẪN THIẾT LẬP BẢO MẬT & CHỐNG BOT/DDOS VỚI CLOUDFLARE
## Hệ Thống Theo Dõi Bóng Đá Ngoại Hạng Anh (Premier League Tracker)

Tài liệu này cung cấp các bước cấu hình Cloudflare tối ưu nhằm chống Bot cào dữ liệu trái phép, ngăn chặn tấn công từ chối dịch vụ (DDoS Layer 7), bảo vệ trang quản trị Admin và tối ưu phân phối CDN cho hàng chục nghìn lượt truy cập đồng thời vào các khung giờ thi đấu Ngoại Hạng Anh cuối tuần.

---

### 1. Cấu Hình Cơ Bản & SSL/TLS

1. **DNS Settings**:
   - Trỏ `A record` hoặc `CNAME` về IP máy chủ VPS/Production.
   - Bật trạng thái **Proxy Status: Proxied** (Đám mây màu cam 🟠) cho các bản ghi `@` (root domain) và `www`.
2. **SSL/TLS Encryption Mode**:
   - Chọn chế độ **Full (Strict)** để mã hóa 2 chiều: Trình duyệt ↔ Cloudflare ↔ Nginx VPS.
   - Bật **Always Use HTTPS** và **Automatic HTTPS Rewrites**.
   - Bật **Minimum TLS Version: TLS 1.2**.
   - Bật **HTTP/2** & **HTTP/3 (with QUIC)** để tăng tốc độ kết nối di động.

---

### 2. Thiết Lập Chống Bot & Tấn Công (Bot Fight Mode & Security Level)

1. **Bật Bot Fight Mode**:
   - Điều hướng: `Security` > `Bots` > Bật **Bot Fight Mode**.
   - Tính năng này sẽ tự động nhận diện các scraper, headless browsers (Puppeteer, Selenium không hợp lệ), phát hiện và đưa ra JS Challenge mà không ảnh hưởng tới người dùng bình thường.
2. **Security Level**:
   - Ngày thường: Đặt ở mức **Medium**.
   - Khung giờ cao điểm có bão truy cập hoặc có dấu hiệu tấn công: Chuyển sang **High**.
   - Khi bị tấn công DDoS nặng: Bật chế độ **"I'm Under Attack!" Mode** (Cloudflare sẽ hiển thị màn hình kiểm tra 5 giây trước khi cho vào website).

---

### 3. Quy Tắc Tường Lửa Tùy Chỉnh (WAF Custom Rules)

Điều hướng vào `Security` > `WAF` > `Custom rules` và tạo các quy tắc sau:

#### Quy tắc 1: Bảo vệ Trang Đăng Nhập Quản Trị (`/admin*`)
- **Tên**: `Protect Admin Endpoints`
- **Biểu thức (Expression)**:
  ```text
  (http.request.uri.path contains "/admin" and not ip.src in {1.2.3.4 5.6.7.8})
  ```
  *(Thay `1.2.3.4` bằng địa chỉ IP tĩnh của quản trị viên nếu có)*
- **Hành động**: **Managed Challenge** (hoặc **Block** nếu bạn chỉ truy cập từ IP cố định).

#### Quy tắc 2: Chống Spam Bot Bỏ Phiếu Dự Đoán (`/api/matches/{id}/vote`)
- **Tên**: `Rate Limit Voting API`
- **Biểu thức (Expression)**:
  ```text
  (http.request.uri.path contains "/api/matches/" and http.request.uri.path contains "/vote" and http.request.method eq "POST")
  ```
- **Hành động**: **Rate Limiting**:
  * Giới hạn: **5 requests / 1 phút** trên mỗi IP.
  * Khi vượt ngưỡng: **Block** trong vòng 10 phút.

#### Quy tắc 3: Chặn Công Cụ Cào Dữ Liệu Tự Động (Bad User-Agents & Tools)
- **Tên**: `Block Scrapers and Exploits`
- **Biểu thức (Expression)**:
  ```text
  (http.user_agent contains "curl" or 
   http.user_agent contains "python-requests" or 
   http.user_agent contains "Go-http-client" or 
   http.user_agent contains "Wget" or 
   http.user_agent contains "Scrapy")
  ```
- **Hành động**: **Block** (hoặc **JS Challenge**).

---

### 4. Tối Ưu Hóa Bộ Nhớ Đệm Edge Cache (Cache Rules / Page Rules)

Điều hướng vào `Caching` > `Cache Rules`:

#### Quy tắc 1: Cache Tài Nguyên Tĩnh 30 Ngày (Edge & Browser)
- **Tên**: `Cache Static Assets 30 Days`
- **Biểu thức**:
  ```text
  (http.request.uri.path matches "\.(css|js|jpg|jpeg|png|webp|svg|ico|woff|woff2|mp3)$")
  ```
- **Cấu hình Cache**:
  * **Cache Eligibility**: Eligible for cache
  * **Edge TTL**: Override origin -> `1 month` (30 days)
  * **Browser TTL**: Override origin -> `1 month` (30 days)

#### Quy tắc 2: Bỏ Qua Cache Với Admin & API Vote
- **Tên**: `Bypass Cache For Dynamic & Admin Routes`
- **Biểu thức**:
  ```text
  (http.request.uri.path contains "/admin" or http.request.uri.path contains "/api/matches/")
  ```
- **Cấu hình**: **Bypass Cache**.

---

### 5. Khôi Phục IP Thật Của Khách Hàng (Real IP) Trong Laravel

Khi sử dụng Cloudflare, IP mà Nginx nhận được từ Socket TCP ban đầu sẽ là IP của máy chủ Cloudflare. Để Laravel nhận đúng IP người dùng thực (dùng cho tính năng 1 IP chỉ vote 1 lần và ghi log bảo mật):

1. File `deploy/nginx.conf` đã được thiết lập sẵn toàn bộ dải IP CIDR của Cloudflare cùng chỉ thị:
   ```nginx
   real_ip_header CF-Connecting-IP;
   ```
2. Trong Laravel `bootstrap/app.php`, tin cậy tất cả Proxy từ Cloudflare:
   ```php
   ->withMiddleware(function (Middleware $middleware) {
       $middleware->trustProxies(at: '*');
   })
   ```
   Điều này đảm bảo `$request->ip()` luôn trả về chính xác IP của người dùng thực tế.
