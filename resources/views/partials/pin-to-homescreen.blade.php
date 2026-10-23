{{-- Component Gợi Ý Ghim Ra Màn Hình Chính (PWA / Add to Home Screen Banner & Guide) --}}
<div id="pwa-pin-banner" class="pwa-pin-banner hidden" role="dialog" aria-labelledby="pwa-pin-title">
    <div class="pwa-banner-card">
        <div class="pwa-banner-left">
            <div class="pwa-app-icon">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tỷ Số Ngoại Hạng Anh" width="44" height="44" onerror="this.src='/favicon-32x32.png'">
            </div>
            <div class="pwa-banner-info">
                <div class="pwa-badge">
                    <svg class="pwa-badge-icon" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    <span>Trải Nghiệm Tốc Độ Cao</span>
                </div>
                <h4 id="pwa-pin-title" class="pwa-app-title">Ghim Tỷ Số Ngoại Hạng Ra Màn Hình</h4>
                <p class="pwa-app-desc">Mở nhanh như App, xem tỷ số & bảng xếp hạng trực tiếp không quảng cáo rác</p>
            </div>
        </div>
        <div class="pwa-banner-actions">
            <button type="button" id="pwa-pin-btn" class="pwa-btn-primary">
                <svg class="pwa-btn-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <span>Ghim Ngay</span>
            </button>
            <button type="button" id="pwa-dismiss-btn" class="pwa-btn-close" aria-label="Để sau">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- Modal Hướng Dẫn Chi Tiết Cho iOS Safari & Desktop --}}
<div id="pwa-ios-modal" class="pwa-modal-backdrop hidden" role="dialog" aria-modal="true">
    <div class="pwa-modal-box">
        <div class="pwa-modal-header">
            <div class="pwa-modal-title-group">
                <div class="pwa-modal-icon-badge">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                        <line x1="12" y1="18" x2="12.01" y2="18"></line>
                    </svg>
                </div>
                <div>
                    <h3 class="pwa-modal-title">Ghim Ứng Dụng Ra Màn Hình Chính</h3>
                    <p class="pwa-modal-subtitle">Thao tác đơn giản trong 2 bước để xem bóng đá nhanh nhất</p>
                </div>
            </div>
            <button type="button" id="pwa-modal-close" class="pwa-modal-close-btn" aria-label="Đóng">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="pwa-modal-body">
            <div class="pwa-step-item">
                <div class="pwa-step-num">1</div>
                <div class="pwa-step-content">
                    <div class="pwa-step-title">
                        <span>Bấm vào nút <strong>Chia sẻ</strong></span>
                        <span class="pwa-step-chip">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                <polyline points="16 6 12 2 8 6"></polyline>
                                <line x1="12" y1="2" x2="12" y2="15"></line>
                            </svg>
                            <span>Chia sẻ</span>
                        </span>
                    </div>
                    <p class="pwa-step-desc">Nút biểu tượng hình vuông có mũi tên hướng lên ở thanh công cụ dưới đáy Safari (iPhone / iPad).</p>
                </div>
            </div>
            <div class="pwa-step-item">
                <div class="pwa-step-num">2</div>
                <div class="pwa-step-content">
                    <div class="pwa-step-title">
                        <span>Chọn <strong>Thêm vào MH chính</strong></span>
                        <span class="pwa-step-chip">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="3" ry="3"></rect>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            <span>Thêm vào MH chính</span>
                        </span>
                    </div>
                    <p class="pwa-step-desc">Cuộn danh sách chọn <strong>Add to Home Screen</strong>, sau đó nhấn <strong>Thêm</strong> ở góc trên bên phải.</p>
                </div>
            </div>
            <div class="pwa-modal-desktop-tip">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Nếu trên máy tính: Nhấn tổ hợp phím <strong>Ctrl + D</strong> (hoặc <strong>Cmd + D</strong> trên Mac) để lưu dấu trang truy cập nhanh.</span>
            </div>
        </div>
        <div class="pwa-modal-footer">
            <button type="button" id="pwa-modal-confirm" class="pwa-modal-confirm-btn">
                <span>Đã Hiểu, Tôi Sẽ Thao Tác</span>
            </button>
        </div>
    </div>
</div>

<style>
/* CSS Giao Diện Ghim Ra Màn Hình Chính */
.pwa-pin-banner {
    position: fixed;
    bottom: 20px;
    left: 20px;
    right: 20px;
    max-width: 520px;
    margin: 0 auto;
    z-index: 99999;
    animation: pwaSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@media (min-width: 768px) {
    .pwa-pin-banner {
        left: auto;
        right: 24px;
        bottom: 24px;
        margin: 0;
    }
}
.pwa-pin-banner.hidden,
.pwa-modal-backdrop.hidden {
    display: none !important;
}
@keyframes pwaSlideUp {
    from {
        opacity: 0;
        transform: translateY(24px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
.pwa-banner-card {
    background: linear-gradient(135deg, rgba(26, 11, 46, 0.94) 0%, rgba(15, 23, 42, 0.96) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(0, 255, 135, 0.28);
    box-shadow: 0 16px 40px -8px rgba(0, 0, 0, 0.6), 0 0 24px rgba(0, 255, 135, 0.12);
    border-radius: 16px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
}
.pwa-banner-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    flex: 1;
}
.pwa-app-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    overflow: hidden;
    background: #0f172a;
    border: 1px solid rgba(255, 255, 255, 0.15);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
.pwa-app-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.pwa-banner-info {
    min-width: 0;
    flex: 1;
}
.pwa-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(0, 255, 135, 0.12);
    border: 1px solid rgba(0, 255, 135, 0.3);
    color: #00ff87;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 999px;
    margin-bottom: 3px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}
.pwa-app-title {
    margin: 0;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.pwa-app-desc {
    margin: 2px 0 0;
    color: #94a3b8;
    font-size: 11.5px;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.pwa-banner-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.pwa-btn-primary {
    background: linear-gradient(135deg, #00ff87 0%, #02b875 100%);
    color: #0d1117;
    border: none;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    box-shadow: 0 4px 14px rgba(0, 255, 135, 0.35);
    white-space: nowrap;
}
.pwa-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 255, 135, 0.5);
}
.pwa-btn-close {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #94a3b8;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
}
.pwa-btn-close:hover {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
}

/* Modal Backdrop */
.pwa-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(5, 7, 12, 0.78);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: pwaFadeIn 0.2s ease forwards;
}
@keyframes pwaFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.pwa-modal-box {
    background: #111827;
    border: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.75);
    border-radius: 20px;
    max-width: 480px;
    width: 100%;
    overflow: hidden;
    color: #f1f5f9;
}
.pwa-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.pwa-modal-title-group {
    display: flex;
    align-items: center;
    gap: 12px;
}
.pwa-modal-icon-badge {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(0, 255, 135, 0.12);
    color: #00ff87;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.pwa-modal-title {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
}
.pwa-modal-subtitle {
    margin: 2px 0 0;
    font-size: 12px;
    color: #94a3b8;
}
.pwa-modal-close-btn {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 6px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.pwa-modal-close-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}
.pwa-modal-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.pwa-step-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}
.pwa-step-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #38003c;
    border: 1px solid rgba(0, 255, 135, 0.4);
    color: #00ff87;
    font-weight: 800;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
}
.pwa-step-content {
    flex: 1;
}
.pwa-step-title {
    font-size: 14px;
    color: #e2e8f0;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}
.pwa-step-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    padding: 3px 8px;
    border-radius: 6px;
    color: #38bdf8;
    font-size: 12px;
    font-weight: 600;
}
.pwa-step-desc {
    margin: 4px 0 0;
    font-size: 12.5px;
    color: #94a3b8;
    line-height: 1.4;
}
.pwa-modal-desktop-tip {
    background: rgba(56, 189, 248, 0.08);
    border: 1px solid rgba(56, 189, 248, 0.2);
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 12px;
    color: #bae6fd;
    display: flex;
    align-items: center;
    gap: 8px;
    line-height: 1.4;
}
.pwa-modal-footer {
    padding: 14px 20px 20px;
}
.pwa-modal-confirm-btn {
    width: 100%;
    background: linear-gradient(135deg, #38003c 0%, #1e1b4b 100%);
    border: 1px solid rgba(0, 255, 135, 0.4);
    color: #00ff87;
    border-radius: 10px;
    padding: 11px 16px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}
.pwa-modal-confirm-btn:hover {
    background: linear-gradient(135deg, #4a0050 0%, #2e266d 100%);
    border-color: #00ff87;
}
</style>

<script>
(function() {
    'use strict';
    
    // Kiểm tra xem ứng dụng đã chạy ở chế độ Standalone (đã cài đặt) chưa
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    if (isStandalone) {
        return; // Đã cài đặt ra màn hình chính, không hiển thị banner
    }

    // Kiểm tra lưu trạng thái người dùng đã từ chối gần đây chưa (7 ngày)
    var storageKey = 'pwa_pin_dismissed_until';
    var dismissedUntil = localStorage.getItem(storageKey);
    if (dismissedUntil && Date.now() < parseInt(dismissedUntil, 10)) {
        return;
    }

    var banner = document.getElementById('pwa-pin-banner');
    var pinBtn = document.getElementById('pwa-pin-btn');
    var dismissBtn = document.getElementById('pwa-dismiss-btn');
    var modal = document.getElementById('pwa-ios-modal');
    var modalClose = document.getElementById('pwa-modal-close');
    var modalConfirm = document.getElementById('pwa-modal-confirm');

    var deferredPrompt = null;
    var isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent.toLowerCase());

    // Bắt sự kiện beforeinstallprompt (Android Chrome / Edge / Desktop Chrome)
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        showBannerAfterDelay();
    });

    // Nếu là iOS Safari hoặc các trình duyệt khác không có beforeinstallprompt
    function showBannerAfterDelay() {
        setTimeout(function() {
            if (banner) {
                banner.classList.remove('hidden');
            }
        }, 2500); // Xuất hiện nhẹ nhàng sau 2.5s khi vào trang
    }

    // Tự động kích hoạt hiển thị cho iOS hoặc các thiết bị di động
    showBannerAfterDelay();

    // Xử lý khi nhấn nút "Ghim Ngay"
    if (pinBtn) {
        pinBtn.addEventListener('click', function() {
            if (deferredPrompt) {
                // Trình duyệt hỗ trợ cài đặt trực tiếp (Chrome / Android / Edge)
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function(choiceResult) {
                    if (choiceResult.outcome === 'accepted') {
                        if (banner) banner.classList.add('hidden');
                    }
                    deferredPrompt = null;
                });
            } else {
                // Trình duyệt iOS Safari hoặc Desktop: mở modal hướng dẫn trực quan
                if (modal) {
                    modal.classList.remove('hidden');
                }
            }
        });
    }

    // Xử lý đóng banner (Nhớ 7 ngày)
    function dismissBanner() {
        if (banner) banner.classList.add('hidden');
        var nextWeek = Date.now() + 7 * 24 * 60 * 60 * 1000;
        localStorage.setItem(storageKey, nextWeek.toString());
    }

    if (dismissBtn) {
        dismissBtn.addEventListener('click', dismissBanner);
    }

    // Đóng Modal
    function closeModal() {
        if (modal) modal.classList.add('hidden');
    }

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalConfirm) modalConfirm.addEventListener('click', closeModal);

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
})();
</script>
