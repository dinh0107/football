{{-- Smart App Banner: Gợi ý thêm vào màn hình chính (Chuẩn phong cách thể thao cao cấp) --}}
<aside id="pwa-pin-banner" class="pwa-pin-banner hidden" aria-label="Cài đặt ứng dụng">
    <div class="pwa-banner-card">
        <button type="button" id="pwa-dismiss-btn" class="pwa-btn-close" aria-label="Đóng thông báo">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="pwa-app-icon">
            <img src="{{ asset('images/logo.png') }}" alt="Tỷ Số Ngoại Hạng Anh" width="38" height="38" onerror="this.onerror=null; this.src='/favicon-32x32.png';">
        </div>

        <div class="pwa-banner-info">
            <div class="pwa-app-title">Tỷ Số Ngoại Hạng Anh</div>
            <div class="pwa-app-desc">Thêm vào màn hình để mở xem nhanh</div>
        </div>

        <button type="button" id="pwa-pin-btn" class="pwa-btn-primary">
            <span>Thêm</span>
        </button>
    </div>
</aside>

{{-- Modal Hướng Dẫn Thêm Màn Hình Chính (iOS Safari) --}}
<div id="pwa-ios-modal" class="pwa-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="pwa-modal-title">
    <div class="pwa-modal-box">
        <div class="pwa-modal-header">
            <h3 id="pwa-modal-title" class="pwa-modal-title">Thêm vào màn hình chính</h3>
            <button type="button" id="pwa-modal-close" class="pwa-modal-close-btn" aria-label="Đóng">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="pwa-modal-body">
            <div class="pwa-step-item">
                <span class="pwa-step-badge">1</span>
                <div class="pwa-step-text">
                    <span>Nhấn vào biểu tượng <strong>Chia sẻ</strong></span>
                    <span class="pwa-icon-inline">
                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                            <polyline points="16 6 12 2 8 6"></polyline>
                            <line x1="12" y1="2" x2="12" y2="15"></line>
                        </svg>
                    </span>
                    <span>ở thanh dưới Safari.</span>
                </div>
            </div>

            <div class="pwa-step-item">
                <span class="pwa-step-badge">2</span>
                <div class="pwa-step-text">
                    <span>Chọn <strong>Thêm vào MH chính</strong> (Add to Home Screen).</span>
                </div>
            </div>
        </div>

        <div class="pwa-modal-footer">
            <button type="button" id="pwa-modal-confirm" class="pwa-btn-done">Đã hiểu</button>
        </div>
    </div>
</div>

<style>
/* Smart App Banner - Phong Cách Tối Giản Thanh Lịch */
.pwa-pin-banner {
    position: fixed;
    bottom: 20px;
    left: 16px;
    right: 16px;
    max-width: 380px;
    margin: 0 auto;
    z-index: 9990;
    pointer-events: none;
    animation: pwaSlideIn 0.25s ease-out forwards;
}

@media (max-width: 767px) {
    .pwa-pin-banner {
        bottom: calc(78px + env(safe-area-inset-bottom, 0px));
        left: 12px;
        right: 12px;
        max-width: 360px;
    }
}

@media (min-width: 768px) {
    .pwa-pin-banner {
        left: auto;
        right: 20px;
        bottom: 20px;
        margin: 0;
    }
}

.pwa-pin-banner.hidden,
.pwa-modal-backdrop.hidden {
    display: none !important;
}

@keyframes pwaSlideIn {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pwa-banner-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.16), 0 4px 6px -2px rgba(15, 23, 42, 0.05);
    border-radius: 12px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    pointer-events: auto;
}

.pwa-btn-close {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: color 0.15s ease;
}

.pwa-btn-close:hover {
    color: #0f172a;
}

.pwa-app-icon {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    overflow: hidden;
    background: #0f172a;
    border: 1px solid rgba(0, 0, 0, 0.06);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pwa-app-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pwa-banner-info {
    flex: 1;
    min-width: 0;
}

.pwa-app-title {
    color: #0f172a;
    font-size: 13.5px;
    font-weight: 700;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pwa-app-desc {
    color: #64748b;
    font-size: 11.5px;
    line-height: 1.3;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pwa-btn-primary {
    background: #38003c;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    flex-shrink: 0;
    transition: background-color 0.15s ease;
    letter-spacing: 0.2px;
}

.pwa-btn-primary:hover {
    background: #250029;
}

/* Modal iOS Safari */
.pwa-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.pwa-modal-box {
    background: #ffffff;
    border-radius: 16px;
    max-width: 360px;
    width: 100%;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    color: #0f172a;
}

.pwa-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
}

.pwa-modal-title {
    margin: 0;
    font-size: 14.5px;
    font-weight: 700;
    color: #0f172a;
}

.pwa-modal-close-btn {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pwa-modal-close-btn:hover {
    color: #0f172a;
}

.pwa-modal-body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.pwa-step-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.pwa-step-badge {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #0f172a;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.pwa-step-text {
    font-size: 13px;
    color: #334155;
    line-height: 1.4;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
}

.pwa-icon-inline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 4px;
    background: #f1f5f9;
    color: #0284c7;
    margin: 0 2px;
}

.pwa-modal-footer {
    padding: 10px 16px 14px;
}

.pwa-btn-done {
    width: 100%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #0f172a;
    border-radius: 8px;
    padding: 9px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s ease;
}

.pwa-btn-done:hover {
    background: #f1f5f9;
}
</style>

<script>
(function() {
    'use strict';
    try {
        var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        if (isStandalone) return;

        var storageKey = 'pwa_pin_dismissed_until';
        var dismissedUntil = localStorage.getItem(storageKey);
        if (dismissedUntil && Date.now() < parseInt(dismissedUntil, 10)) {
            return;
        }
    } catch(e) {}

    var banner = document.getElementById('pwa-pin-banner');
    var pinBtn = document.getElementById('pwa-pin-btn');
    var dismissBtn = document.getElementById('pwa-dismiss-btn');
    var modal = document.getElementById('pwa-ios-modal');
    var modalClose = document.getElementById('pwa-modal-close');
    var modalConfirm = document.getElementById('pwa-modal-confirm');

    var deferredPrompt = null;
    var userAgent = (window.navigator.userAgent || '').toLowerCase();
    var isMobile = /iphone|ipad|ipod|android|mobile/i.test(userAgent);

    var hasShown = false;
    function triggerBanner() {
        if (hasShown || !banner) return;
        hasShown = true;
        showBanner();
    }

    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        setTimeout(triggerBanner, 8000);
    });

    if (isMobile) {
        // Chỉ hiện thông báo ghim khi người dùng đã cuộn trang (thực sự tương tác) hoặc sau 15 giây,
        // tuyệt đối KHÔNG nhảy ra ngay khi vừa load trang che mất thanh điều hướng (nav)
        var scrollHandler = function() {
            if (window.scrollY > 200) {
                triggerBanner();
                window.removeEventListener('scroll', scrollHandler);
            }
        };
        window.addEventListener('scroll', scrollHandler, { passive: true });
        setTimeout(triggerBanner, 15000);
    }

    if (pinBtn) {
        pinBtn.addEventListener('click', function() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function(choiceResult) {
                    if (choiceResult.outcome === 'accepted' && banner) {
                        banner.classList.add('hidden');
                    }
                    deferredPrompt = null;
                });
            } else if (modal) {
                modal.classList.remove('hidden');
            }
        });
    }

    function dismissBanner() {
        if (banner) banner.classList.add('hidden');
        try {
            var nextPeriod = Date.now() + 14 * 24 * 60 * 60 * 1000; // 14 ngày
            localStorage.setItem(storageKey, nextPeriod.toString());
        } catch(e) {}
    }

    if (dismissBtn) dismissBtn.addEventListener('click', dismissBanner);

    function closeModal() {
        if (modal) modal.classList.add('hidden');
    }

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalConfirm) modalConfirm.addEventListener('click', closeModal);
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    }
})();
</script>
