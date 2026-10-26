/**
 * Premier League LiveScore Engine (Vanilla JavaScript)
 * Phase 5: Cơ chế cập nhật tỷ số thời gian thực không tải lại trang và chuông báo bàn thắng
 *
 * Tính năng chính:
 * 1. Polling ngầm API GET /api/matches/live mỗi 30s (tự tạm dừng khi tab ẩn).
 * 2. DOM Diffing: So sánh tỷ số cũ và mới, cập nhật bàn thắng & phút thi đấu.
 * 3. Hiệu ứng nhấp nháy vàng (highlight flash) tại ô tỷ số khi có bàn thắng.
 * 4. Chuông báo bàn thắng: Phát file goal.mp3 (kèm Web Audio API fanfare fallback).
 * 5. Nút icon Loa 🔊/🔇 góc màn hình cho phép bật/tắt âm thanh, lưu vào localStorage.
 * 6. Đổi document.title thành "⚽ VÀO! [Đội nhà] [Tỷ số] [Đội khách]" trong 10 giây.
 */

(function () {
    'use strict';

    // Cấu hình
    const CONFIG = {
        apiEndpoint: '/api/matches/live',
        pollIntervalMs: 30000, // 30 giây
        titleDurationMs: 10000, // 10 giây
        soundStorageKey: 'epl_goal_alert_sound',
        soundFile: '/sounds/goal.mp3',
    };

    // State
    const originalDocumentTitle = document.title;
    let titleRestoreTimer = null;
    let pollTimer = null;
    let isFetching = false;
    let audioContext = null;

    /**
     * Khởi tạo Web Audio Context khi người dùng tương tác (đáp ứng Autoplay Policy)
     */
    function getAudioContext() {
        if (!audioContext) {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) {
                audioContext = new AudioCtx();
            }
        }
        if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume();
        }
        return audioContext;
    }

    /**
     * Kiểm tra trạng thái âm thanh từ localStorage (mặc định bật = true)
     */
    function isSoundEnabled() {
        const stored = localStorage.getItem(CONFIG.soundStorageKey);
        return stored === null ? true : (stored === 'true' || stored === '1');
    }

    /**
     * Bật/tắt âm thanh và lưu localStorage
     */
    function toggleSound() {
        const newState = !isSoundEnabled();
        localStorage.setItem(CONFIG.soundStorageKey, newState ? 'true' : 'false');
        updateSpeakerUI(newState);

        // Khởi động AudioContext khi người dùng click
        getAudioContext();

        if (newState) {
            // Phát âm thử nhẹ khi người dùng bật loa
            playGoalSound(true);
        }
    }

    /**
     * Cập nhật giao diện nút Loa (loại bỏ nếu còn tồn tại trong DOM)
     */
    function updateSpeakerUI(enabled) {
        const btn = document.getElementById('eplGoalSoundToggle');
        if (btn) {
            btn.remove();
        }
    }

    /**
     * Nút Loa âm thanh góc màn hình đã được loại bỏ để giao diện tối giản, thanh lịch
     */
    function ensureSpeakerButton() {
        const btn = document.getElementById('eplGoalSoundToggle');
        if (btn) {
            btn.remove();
        }
    }

    /**
     * Web Audio API Fanfare Fallback: Giả lập còi và hợp âm chiến thắng
     * Chạy khi audio mp3 gặp chính sách chặn autoplay hoặc lỗi tải file
     */
    function playWebAudioFanfare(isTest = false) {
        try {
            const ctx = getAudioContext();
            if (!ctx) return;

            const now = ctx.currentTime;
            const volume = isTest ? 0.2 : 0.4;

            // 1. Tiếng còi trọng tài (Whistle)
            const oscWhistle = ctx.createOscillator();
            const gainWhistle = ctx.createGain();
            oscWhistle.type = 'sine';
            oscWhistle.frequency.setValueAtTime(2600, now);
            oscWhistle.frequency.exponentialRampToValueAtTime(2900, now + 0.15);
            gainWhistle.gain.setValueAtTime(volume * 0.5, now);
            gainWhistle.gain.exponentialRampToValueAtTime(0.001, now + 0.25);
            oscWhistle.connect(gainWhistle);
            gainWhistle.connect(ctx.destination);
            oscWhistle.start(now);
            oscWhistle.stop(now + 0.25);

            // 2. Triumphant Stadium Fanfare (C5, E5, G5, C6)
            const notes = [523.25, 659.25, 783.99, 1046.50];
            notes.forEach((freq, index) => {
                const startTime = now + 0.25 + (index * 0.18);
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'triangle';
                osc.frequency.setValueAtTime(freq, startTime);

                gain.gain.setValueAtTime(volume, startTime);
                gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.4);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(startTime);
                osc.stop(startTime + 0.45);
            });
        } catch (err) {
            console.warn('[LiveScoreEngine] Web Audio fallback error:', err);
        }
    }

    /**
     * Phát chuông báo bàn thắng (goal.mp3 kèm fallback)
     */
    function playGoalSound(isTest = false) {
        if (!isSoundEnabled()) {
            return;
        }

        try {
            const audio = new Audio(CONFIG.soundFile);
            audio.volume = isTest ? 0.5 : 1.0;
            const playPromise = audio.play();

            if (playPromise !== undefined) {
                playPromise.catch(function (error) {
                    // Nếu trình duyệt chặn Audio play hoặc lỗi, fallback qua Web Audio
                    playWebAudioFanfare(isTest);
                });
            }
        } catch (e) {
            playWebAudioFanfare(isTest);
        }
    }

    /**
     * Đổi tiêu đề Tab trình duyệt trong 10 giây:
     * "⚽ VÀO! [Đội nhà] [Tỷ số] [Đội khách]"
     */
    function triggerGoalTabTitle(homeTeamName, scoreStr, awayTeamName) {
        if (titleRestoreTimer) {
            clearTimeout(titleRestoreTimer);
        }

        const goalTitle = `⚽ VÀO! ${homeTeamName} ${scoreStr} ${awayTeamName}`;
        document.title = goalTitle;

        titleRestoreTimer = setTimeout(function () {
            document.title = originalDocumentTitle;
            titleRestoreTimer = null;
        }, CONFIG.titleDurationMs);
    }

    /**
     * Hiển thị Goal Toast Banner nổi bật trên màn hình (Thiết kế Vector hiện đại, nhỏ gọn trên Mobile)
     */
     function showGoalBanner(homeTeam, awayTeam, scoreStr, scorer) {
        let toast = document.getElementById('eplGoalBannerToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'eplGoalBannerToast';
            toast.className = 'goal-toast-notification';
            document.body.appendChild(toast);
        }

        const ballSvg = `<svg class="toast-ball-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="12 7 15 9.5 14 13 10 13 9 9.5"></polygon><line x1="12" y1="2" x2="12" y2="7"></line><line x1="17" y1="5.5" x2="15" y2="9.5"></line><line x1="20" y1="12" x2="14" y2="13"></line><line x1="17" y1="18.5" x2="13" y2="17"></line><line x1="7" y1="18.5" x2="11" y2="17"></line><line x1="4" y1="12" x2="10" y2="13"></line><line x1="7" y1="5.5" x2="9" y2="9.5"></line></svg>`;

        const scorerText = scorer ? `<div class="toast-scorer">${ballSvg}<span>${scorer}</span></div>` : '';

        toast.innerHTML = `
            <div class="toast-icon">
                ${ballSvg}
            </div>
            <div class="toast-body">
                <div class="toast-heading">VÀO! BÀN THẮNG</div>
                <div class="toast-match">
                    <span class="toast-team">${homeTeam}</span>
                    <span class="toast-score">${scoreStr}</span>
                    <span class="toast-team">${awayTeam}</span>
                </div>
                ${scorerText}
            </div>
            <button class="toast-close" type="button" aria-label="Đóng">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        `;

        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                toast.classList.remove('show');
            });
        }

        // Kích hoạt animation hiện
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Tự ẩn sau 7 giây
        setTimeout(() => {
            if (toast) {
                toast.classList.remove('show');
            }
        }, 7000);
    }

    /**
     * Kích hoạt hiệu ứng nhấp nháy vàng (highlight flash) trên phần tử DOM
     */
    function triggerHighlightFlash(element) {
        if (!element) return;

        element.classList.remove('flash-highlight-goal');
        // Trigger DOM reflow để restart CSS animation
        void element.offsetWidth;
        element.classList.add('flash-highlight-goal');

        setTimeout(() => {
            element.classList.remove('flash-highlight-goal');
        }, 2600);
    }

    /**
     * Tìm tên cầu thủ ghi bàn mới nhất từ danh sách sự kiện
     */
    function extractLatestScorer(events) {
        if (!Array.isArray(events) || events.length === 0) return null;
        for (let i = 0; i < events.length; i++) {
            const ev = events[i];
            if (ev.type === 'G' || ev.type === 'OG' || ev.type === 'P') {
                return (ev.playerName || 'Cầu thủ') + (ev.clock ? ` (${ev.clock})` : '');
            }
        }
        return null;
    }

    /**
     * Thực hiện Diffing dữ liệu và cập nhật DOM
     * Trả về true nếu phát hiện bàn thắng mới
     */
    function diffAndUpdateMatch(match) {
        // Trận chưa đá: giữ giờ bóng lăn, không ghi đè thành "0 - 0"
        if (!match || !match.id || match.status === 'U') return false;

        const matchId = String(match.id);
        const matchElements = document.querySelectorAll(`[data-fixture-id="${matchId}"]`);
        if (matchElements.length === 0) return false;

        let hasNewGoal = false;

        matchElements.forEach(container => {
            const currentHome = parseInt(container.getAttribute('data-home-score') || '0', 10);
            const currentAway = parseInt(container.getAttribute('data-away-score') || '0', 10);
            const newHome = parseInt(match.home_score, 10);
            const newAway = parseInt(match.away_score, 10);

            const scoreIncreased = (newHome > currentHome) || (newAway > currentAway);

            if (scoreIncreased) {
                hasNewGoal = true;
            }

            // Cập nhật thuộc tính data trên container
            container.setAttribute('data-home-score', newHome);
            container.setAttribute('data-away-score', newAway);
            if (match.status) {
                container.setAttribute('data-status', match.status);
            }

            // 0. Nâng cấp giao diện nếu trận chuyển từ "Sắp diễn ra" (U) sang "Trực tiếp" (L)
            if (match.status === 'L') {
                // Trên trang chi tiết trận đấu (Hero Card):
                const upcomingPill = container.querySelector('.pill-upcoming');
                if (upcomingPill) {
                    upcomingPill.className = 'hero-status-pill pill-live';
                    upcomingPill.innerHTML = `<span class="live-dot-pulse"></span> <span data-fixture-clock>${match.minute || 'LIVE'}</span> &bull; TRỰC TIẾP`;
                }

                const heroVsEl = container.querySelector('.hero-score-vs');
                if (heroVsEl) {
                    heroVsEl.className = 'hero-score-display';
                    heroVsEl.innerHTML = `<span data-score-home>${newHome}</span> - <span data-score-away>${newAway}</span>`;
                    const heroCenter = container.querySelector('.hero-score-center');
                    if (heroCenter) {
                        const dateSpans = heroCenter.querySelectorAll('span');
                        dateSpans.forEach(s => {
                            if (!s.classList.contains('hero-status-pill') && !s.classList.contains('live-dot-pulse') && !s.hasAttribute('data-fixture-clock')) {
                                s.style.display = 'none';
                            }
                        });
                    }
                }

                // Trên danh sách thẻ trận đấu (Home / Fixtures):
                const upcomingBadge = container.querySelector('.badge-upcoming');
                if (upcomingBadge) {
                    upcomingBadge.className = 'status-badge badge-live';
                    upcomingBadge.innerHTML = `<span class="pulse-dot"></span> <span class="live-clock-flash" data-fixture-clock>${match.minute || 'LIVE'}</span>`;
                }
            }

            // 1. Cập nhật tỷ số vào các phần tử con
            const homeScoreEl = container.querySelector('[data-score-home]');
            const awayScoreEl = container.querySelector('[data-score-away]');
            const combinedScoreEl = container.querySelector('[data-score-display], .score-value, .hero-score-value');

            if (homeScoreEl) {
                if (parseInt(homeScoreEl.textContent.trim(), 10) !== newHome) {
                    homeScoreEl.textContent = newHome;
                    triggerHighlightFlash(homeScoreEl);
                }
            }

            if (awayScoreEl) {
                if (parseInt(awayScoreEl.textContent.trim(), 10) !== newAway) {
                    awayScoreEl.textContent = newAway;
                    triggerHighlightFlash(awayScoreEl);
                }
            }

            if (combinedScoreEl) {
                const newScoreText = `${newHome} - ${newAway}`;
                if (combinedScoreEl.textContent.trim() !== newScoreText && !homeScoreEl) {
                    combinedScoreEl.textContent = newScoreText;
                }
                if (scoreIncreased) {
                    triggerHighlightFlash(combinedScoreEl);
                }
            }

            // 2. Cập nhật số phút thi đấu mới
            if (match.minute) {
                const clockEl = container.querySelector('[data-fixture-clock], .live-clock-flash, .hero-clock');
                if (clockEl) {
                    clockEl.textContent = match.minute;
                }
            }

            // 3. Nếu là trang chi tiết trận đấu, cập nhật dòng thời gian sự kiện (Timeline) nếu có sự kiện mới
            if (match.latest_events && match.latest_events.length > 0) {
                updateMatchCenterEvents(container, match.latest_events);
            }
        });

        // Nếu có bàn thắng mới, kích hoạt cảnh báo âm thanh & đổi tiêu đề Tab
        if (hasNewGoal) {
            const homeName = match.home_team ? (match.home_team.shortName || match.home_team.name) : 'Đội nhà';
            const awayName = match.away_team ? (match.away_team.shortName || match.away_team.name) : 'Đội khách';
            const scoreStr = `${match.home_score} - ${match.away_score}`;
            const scorer = extractLatestScorer(match.latest_events);

            // Cảnh báo âm thanh
            playGoalSound();

            // Đổi tiêu đề Tab trình duyệt trong 10 giây
            triggerGoalTabTitle(homeName, scoreStr, awayName);

            // Hiện toast banner trực quan
            showGoalBanner(homeName, awayName, scoreStr, scorer);
        }

        return hasNewGoal;
    }

    /**
     * Cập nhật danh sách sự kiện trên trang Match Center nếu người dùng đang xem
     */
    function updateMatchCenterEvents(container, events) {
        const timelineList = document.querySelector('.timeline');
        if (!timelineList || !Array.isArray(events)) return;

        // Nếu có sự kiện mới chưa xuất hiện trong timeline, có thể bổ sung hoặc cập nhật thông báo
        const indicator = document.getElementById('livePollingIndicator');
        if (indicator) {
            indicator.textContent = `Cập nhật lúc ${new Date().toLocaleTimeString('vi-VN')}`;
        }
    }

    /**
     * Gọi ngầm API GET /api/matches/live
     */
    async function fetchLiveScores() {
        // Chỉ chạy khi tab trang web đang mở (Page Visibility API)
        if (document.visibilityState !== 'visible') {
            return;
        }

        if (isFetching) return;
        isFetching = true;

        try {
            // Kiểm tra xem trang hiện tại có đang theo dõi một fixture cụ thể không
            const matchCenterEl = document.querySelector('[data-fixture-id]');
            let url = CONFIG.apiEndpoint;
            if (matchCenterEl) {
                const fId = matchCenterEl.getAttribute('data-fixture-id');
                if (fId && fId !== '0') {
                    url += `?fixture_id=${encodeURIComponent(fId)}`;
                }
            }

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                cache: 'no-store'
            });

            if (!response.ok) {
                throw new Error(`HTTP error ${response.status}`);
            }

            const data = await response.json();
            const matches = Array.isArray(data) ? data : (data.data || []);

            // Tiến hành Diffing từng trận
            matches.forEach(match => {
                diffAndUpdateMatch(match);
            });

            // Cập nhật chỉ báo đồng bộ ở thanh ticker nếu có
            const indicator = document.getElementById('livePollingIndicator');
            if (indicator) {
                indicator.textContent = `Cập nhật lúc ${new Date().toLocaleTimeString('vi-VN')} • Chu kỳ 30s`;
            }
        } catch (error) {
            console.debug('[LiveScoreEngine] Fetch info:', error.message);
        } finally {
            isFetching = false;
        }
    }

    /**
     * Lập lịch chạy ngầm bằng setInterval
     */
    function startPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
        }

        pollTimer = setInterval(() => {
            if (document.visibilityState === 'visible') {
                fetchLiveScores();
            }
        }, CONFIG.pollIntervalMs);
    }

    /**
     * Xử lý sự kiện Visibility Change (người dùng chuyển tab)
     */
    function handleVisibilityChange() {
        if (document.visibilityState === 'visible') {
            // Khi người dùng quay lại tab, kiểm tra cập nhật ngay lập tức
            fetchLiveScores();
        }
    }

    /**
     * Lắng nghe người dùng tương tác để mở khóa Web Audio Policy
     */
    function unlockAudioOnInteraction() {
        const unlock = function () {
            getAudioContext();
            window.removeEventListener('click', unlock);
            window.removeEventListener('keydown', unlock);
            window.removeEventListener('touchstart', unlock);
        };
        window.addEventListener('click', unlock, { once: true, passive: true });
        window.addEventListener('keydown', unlock, { once: true, passive: true });
        window.addEventListener('touchstart', unlock, { once: true, passive: true });
    }

    /**
     * API công khai (đặt trên window để dễ dàng kiểm thử qua Console hoặc nút test)
     */
    window.EplLiveEngine = {
        fetchNow: fetchLiveScores,
        toggleSound: toggleSound,
        isSoundEnabled: isSoundEnabled,
        testGoalAlert: function (home = 'Arsenal', score = '2 - 1', away = 'Chelsea', scorer = 'Bukayo Saka') {
            playGoalSound();
            triggerGoalTabTitle(home, score, away);
            showGoalBanner(home, away, score, scorer);

            // Tìm và flash thử một trận bất kỳ đang có trên trang
            const firstMatch = document.querySelector('[data-fixture-id]');
            if (firstMatch) {
                const scoreBox = firstMatch.querySelector('[data-score-display], .score-value, .hero-score-value');
                if (scoreBox) triggerHighlightFlash(scoreBox);
            }
            return 'Goal Alert triggered successfully!';
        }
    };

    /**
     * Khởi chạy Engine khi DOM đã sẵn sàng
     */
    function init() {
        ensureSpeakerButton();
        unlockAudioOnInteraction();
        startPolling();
        document.addEventListener('visibilitychange', handleVisibilityChange);

        // Chạy lần đầu sau 2 giây tải trang để đồng bộ
        setTimeout(fetchLiveScores, 2000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
