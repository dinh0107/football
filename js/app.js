/**
 * EPL Live Tracker - Pure Vanilla JavaScript Engine
 * Không phụ thuộc framework nặng, siêu nhẹ và tối ưu hiệu năng
 */

document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initTableSearch();
    initLiveScoresPolling();
});

/**
 * Quản lý chuyển Tab trên trang Chi tiết trận đấu
 */
function initTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    if (!tabButtons.length) return;

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-tab');

            // Bỏ active tất cả tab buttons
            tabButtons.forEach(b => b.classList.remove('active'));
            // Ẩn tất cả tab content
            tabContents.forEach(c => c.classList.remove('active'));

            // Kích hoạt tab được chọn
            btn.classList.add('active');
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

/**
 * Bộ lọc tìm kiếm nhanh tên CLB trên Bảng Xếp Hạng (Real-time Filter)
 */
function initTableSearch() {
    const searchInput = document.getElementById('tableSearchInput');
    const tableBody = document.getElementById('standingsTableBody');

    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const teamName = row.getAttribute('data-team-name')?.toLowerCase() || '';
            if (teamName.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

/**
 * Cơ chế Tự động Polling Live Scores mỗi 30 giây (Không cần F5 / Reload trang)
 */
function initLiveScoresPolling() {
    const liveIndicator = document.getElementById('livePollingIndicator');
    
    // Chỉ kích hoạt nếu có phần tử live ticker hoặc trên trang /truc-tiep
    const shouldPoll = document.querySelector('.live-section') || document.querySelector('.ticker-bar');
    if (!shouldPoll) return;

    const POLL_INTERVAL = 30000; // 30 giây theo chuẩn PulseLive cache TTL

    setInterval(async () => {
        try {
            if (liveIndicator) {
                liveIndicator.style.opacity = '0.4';
            }

            const response = await fetch('/api/live-scores', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) return;
            const data = await response.json();

            if (data.success && Array.isArray(data.matches)) {
                updateLiveUI(data.matches);
            }

            if (liveIndicator) {
                liveIndicator.style.opacity = '1';
            }
        } catch (err) {
            console.warn('Lỗi cập nhật Live Scores tự động:', err);
        }
    }, POLL_INTERVAL);
}

/**
 * Cập nhật DOM của các trận live mà không cần tải lại trang
 */
function updateLiveUI(matches) {
    if (!matches) return;

    matches.forEach(match => {
        // Cập nhật thẻ trận đấu trên trang /truc-tiep nếu có
        const card = document.querySelector(`[data-fixture-id="${match.id}"]`);
        if (card) {
            const homeScoreEl = card.querySelector('.home-score');
            const awayScoreEl = card.querySelector('.away-score');
            const clockEl = card.querySelector('.match-clock');

            if (homeScoreEl && match.home.score !== null) {
                homeScoreEl.textContent = match.home.score;
            }
            if (awayScoreEl && match.away.score !== null) {
                awayScoreEl.textContent = match.away.score;
            }
            if (clockEl && match.clock) {
                clockEl.textContent = match.clock;
            }
        }

        // Cập nhật hero score trên trang chi tiết trận đấu
        const heroScore = document.querySelector(`.hero-match-detail[data-fixture-id="${match.id}"]`);
        if (heroScore) {
            const scoreDisplay = heroScore.querySelector('.hero-score-value');
            const heroClock = heroScore.querySelector('.hero-clock');

            if (scoreDisplay && match.home.score !== null && match.away.score !== null) {
                scoreDisplay.textContent = `${match.home.score} - ${match.away.score}`;
            }
            if (heroClock && match.clock) {
                heroClock.textContent = match.clock;
            }
        }
    });
}
