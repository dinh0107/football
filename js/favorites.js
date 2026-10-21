/**
 * EPL Favorites & Team Pinning Engine (Pure Vanilla JavaScript)
 * Phase 7: Ghim trận & Đội bóng yêu thích (Favorites)
 */
(function() {
    'use strict';

    const STORAGE_KEY = 'epl_favorite_teams';

    const FavoritesManager = {
        /**
         * Lấy danh sách ID các đội bóng yêu thích từ localStorage
         * @returns {number[]}
         */
        getFavorites: function() {
            try {
                const stored = localStorage.getItem(STORAGE_KEY);
                return stored ? JSON.parse(stored) : [];
            } catch (e) {
                console.warn('Lỗi đọc localStorage favorites:', e);
                return [];
            }
        },

        /**
         * Lưu danh sách đội bóng yêu thích vào localStorage
         * @param {number[]} list
         */
        saveFavorites: function(list) {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
            } catch (e) {
                console.warn('Lỗi ghi localStorage favorites:', e);
            }
        },

        /**
         * Kiểm tra xem một đội có trong danh sách yêu thích không
         * @param {number|string} teamId
         * @returns {boolean}
         */
        isFavorite: function(teamId) {
            const id = parseInt(teamId, 10);
            return this.getFavorites().includes(id);
        },

        /**
         * Thêm hoặc xóa một đội khỏi danh sách yêu thích
         * @param {number|string} teamId
         * @returns {boolean} true nếu đã thêm, false nếu đã gỡ
         */
        toggleFavorite: function(teamId) {
            const id = parseInt(teamId, 10);
            if (!id) return false;

            let favs = this.getFavorites();
            const index = favs.indexOf(id);
            let added = false;

            if (index > -1) {
                favs.splice(index, 1);
                added = false;
            } else {
                favs.push(id);
                added = true;
            }

            this.saveFavorites(favs);
            this.updateAllStarIcons();
            this.reorderMatchesOnHome();

            return added;
        },

        /**
         * Cập nhật trạng thái hiển thị của tất cả icon Ngôi sao trên trang
         */
        updateAllStarIcons: function() {
            const favs = this.getFavorites();
            const buttons = document.querySelectorAll('.btn-favorite-team[data-favorite-team]');

            buttons.forEach(btn => {
                const teamId = parseInt(btn.getAttribute('data-favorite-team'), 10);
                const isFav = favs.includes(teamId);

                if (isFav) {
                    btn.classList.add('is-favorite');
                    btn.setAttribute('aria-pressed', 'true');
                    btn.setAttribute('title', 'Bỏ ghim đội yêu thích');
                } else {
                    btn.classList.remove('is-favorite');
                    btn.setAttribute('aria-pressed', 'false');
                    btn.setAttribute('title', 'Ghim đội yêu thích lên đầu');
                }
            });
        },

        /**
         * Tự động đẩy các trận đấu có đội yêu thích lên vị trí đầu tiên của trang chủ
         */
        reorderMatchesOnHome: function() {
            const container = document.getElementById('matchesContainer');
            if (!container) return;

            const favs = this.getFavorites();
            const cards = Array.from(container.querySelectorAll('.match-card[data-fixture-id]'));
            if (cards.length === 0) return;

            // Đánh giá từng card
            const pinnedCards = [];
            const normalCards = [];

            cards.forEach(card => {
                const homeId = parseInt(card.getAttribute('data-home-id'), 10);
                const awayId = parseInt(card.getAttribute('data-away-id'), 10);

                const isFav = favs.includes(homeId) || favs.includes(awayId);

                // Gắn/gỡ badge ghim yêu thích
                let pinnedBadge = card.querySelector('.badge-favorite-pinned');
                if (isFav) {
                    card.classList.add('match-pinned-favorite');
                    if (!pinnedBadge) {
                        pinnedBadge = document.createElement('span');
                        pinnedBadge.className = 'badge-favorite-pinned';
                        pinnedBadge.innerHTML = '<svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg> Trận Yêu Thích';
                        const cardTop = card.querySelector('.match-card-top') || card.querySelector('.match-card-header');
                        if (cardTop) {
                            cardTop.prepend(pinnedBadge);
                        }
                    }
                    pinnedCards.push(card);
                } else {
                    card.classList.remove('match-pinned-favorite');
                    if (pinnedBadge) {
                        pinnedBadge.remove();
                    }
                    normalCards.push(card);
                }
            });

            // Nếu có trận yêu thích, xếp lên đầu
            if (pinnedCards.length > 0) {
                // Sắp xếp lại container mà không reload
                const fragment = document.createDocumentFragment();
                pinnedCards.forEach(c => fragment.appendChild(c));
                normalCards.forEach(c => fragment.appendChild(c));
                container.appendChild(fragment);
            }
        },

        /**
         * Khởi tạo sự kiện click
         */
        init: function() {
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-favorite-team');
                if (!btn) return;

                e.preventDefault();
                e.stopPropagation();

                const teamId = btn.getAttribute('data-favorite-team');
                if (teamId) {
                    FavoritesManager.toggleFavorite(teamId);
                }
            });

            // Khởi tạo ban đầu
            this.updateAllStarIcons();
            this.reorderMatchesOnHome();
        }
    };

    // Đưa vào window để các component khác có thể gọi nếu cần
    window.FavoritesManager = FavoritesManager;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => FavoritesManager.init());
    } else {
        FavoritesManager.init();
    }
})();
