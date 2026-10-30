/**
 * =============================================================================
 * TÍNH NĂNG TÌM KIẾM THÔNG MINH & GỢI Ý LIVE SEARCH TRANG CHỦ (HOME SEARCH)
 * =============================================================================
 *
 * @package           JobScout
 * @subpackage        PDS / JavaScript Live Search
 * @author            Trần Cao Trọng (Lead)
 * @git_branch        tran-cao-trong/home
 * @ui_reference      docs/ui-reference/01-home.png
 *
 * -----------------------------------------------------------------------------
 * 1. CHỨC NĂNG HOẠT ĐỘNG (FUNCTIONAL OVERVIEW):
 * -----------------------------------------------------------------------------
 * - Lắng nghe sự kiện gõ phím (`input`) trong ô tìm kiếm việc làm tại Trang Chủ.
 * - Cơ chế Debounce (250ms): Giảm tải tần suất gửi request về server khi người dùng gõ liên tục.
 * - Ngưỡng kích hoạt: Bắt đầu tìm kiếm tự động khi từ khóa có từ 2 ký tự trở lên.
 * - Kết nối Endpoint AJAX gợi ý: Gửi request ngầm qua tham số `pds_ajax_search_suggest=1`.
 * - Điều hướng thông minh:
 *   + Khi người dùng click vào một công việc trong dropdown -> Điều hướng thẳng đến trang Job Detail của job đó.
 *   + Khi click "Xem tất cả việc làm" hoặc nhấn Enter / nút SEARCH JOB -> Điều hướng sang trang All Jobs (`/jobs/?search_keywords=...&search_location=...`).
 * - Tự động đóng dropdown khi click chuột ra ngoài vùng tìm kiếm hoặc ấn ESC.
 *
 * -----------------------------------------------------------------------------
 * 2. GIAO DIỆN & TƯƠNG TÁC UI (DESIGN SPECS):
 * -----------------------------------------------------------------------------
 * - Bảng gợi ý dạng Dropdown (`pds-search-suggest-dropdown`):
 *   + Nền trắng tinh khiết (#ffffff), bo góc 6px, đổ bóng mềm mại (box-shadow: 0 12px 30px rgba(0,0,0,0.18)).
 *   + Mỗi mục gợi ý gồm: Tiêu đề công việc in đậm #111, huy hiệu badge Loại công việc (Fulltime) màu cam nhẹ,
 *     dòng phụ chứa tên công ty và địa điểm làm việc màu xám #666.
 *   + Hiệu ứng Hover: Nền chuyển sang màu xám sáng (#f9f7f5), tiêu đề chuyển màu cam #f2761b.
 *   + Chân bảng gợi ý: Dòng chữ "Xem tất cả kết quả..." nổi bật màu cam kèm icon mũi tên.
 * =============================================================================
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var searchForms = document.querySelectorAll('form.home-job-search');

        searchForms.forEach(function (form) {
            var input = form.querySelector('input[name="search_keywords"]');
            var locSelect = form.querySelector('select[name="search_location"]');

            if (!input) {
                return;
            }

            // Ensure parent wrapper is relative for absolute positioning of dropdown
            var parentWrapper = input.closest('.home-search-keywords') || input.parentElement;
            if (parentWrapper) {
                parentWrapper.style.position = 'relative';
            }

            // Create suggestion dropdown container
            var dropdown = document.createElement('div');
            dropdown.className = 'pds-search-suggest-dropdown';
            dropdown.style.display = 'none';
            dropdown.style.position = 'absolute';
            dropdown.style.top = '100%';
            dropdown.style.left = '0';
            dropdown.style.right = '0';
            dropdown.style.background = '#ffffff';
            dropdown.style.borderRadius = '6px';
            dropdown.style.boxShadow = '0 12px 30px rgba(0, 0, 0, 0.18)';
            dropdown.style.border = '1px solid #e8e2dc';
            dropdown.style.marginTop = '6px';
            dropdown.style.zIndex = '99999';
            dropdown.style.overflow = 'hidden';
            dropdown.style.textAlign = 'left';
            dropdown.style.fontFamily = 'inherit';

            if (parentWrapper) {
                parentWrapper.appendChild(dropdown);
            }

            var debounceTimer = null;
            var currentKeyword = '';

            function hideDropdown() {
                dropdown.style.display = 'none';
                dropdown.innerHTML = '';
            }

            function fetchSuggestions(keyword) {
                var loc = locSelect ? locSelect.value : '';
                var url = new URL(window.location.origin + '/');
                url.searchParams.set('pds_ajax_search_suggest', '1');
                url.searchParams.set('keyword', keyword);
                if (loc) {
                    url.searchParams.set('location', loc);
                }

                fetch(url.toString(), {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (res) { return res.json(); })
                .then(function (res) {
                    if (!res || !res.success || !res.data) {
                        hideDropdown();
                        return;
                    }

                    var data = res.data;
                    var jobs = data.jobs || [];
                    var total = data.total || 0;

                    if (jobs.length === 0) {
                        dropdown.innerHTML = '<div style="padding: 14px 18px; color: #777; font-size: 13px;">' +
                            'Không tìm thấy công việc nào khớp với "<strong>' + escapeHtml(keyword) + '</strong>".' +
                        '</div>';
                        dropdown.style.display = 'block';
                        return;
                    }

                    var html = '<div style="padding: 8px 0; max-height: 340px; overflow-y: auto;">';

                    jobs.forEach(function (job) {
                        html += '<a href="' + escapeHtml(job.url) + '" class="pds-suggest-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 16px; text-decoration: none; border-bottom: 1px solid #f2ede8; transition: background 0.15s ease;">' +
                            '<div style="flex: 1; padding-right: 12px;">' +
                                '<div style="color: #2b2827; font-size: 14px; font-weight: 600; line-height: 1.3;">' + highlightMatch(job.title, keyword) + '</div>' +
                                '<div style="color: #7a736e; font-size: 12px; margin-top: 3px;">' + escapeHtml(job.company) + ' &bull; ' + escapeHtml(job.location) + '</div>' +
                            '</div>' +
                            '<span style="background: #fff0e6; color: #f2761b; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 3px; white-space: nowrap;">' + escapeHtml(job.type) + '</span>' +
                        '</a>';
                    });

                    // Footer "Xem tất cả kết quả trên trang Jobs"
                    var targetJobsUrl = data.jobs_page_url || (window.location.origin + '/jobs/?search_keywords=' + encodeURIComponent(keyword));
                    html += '<a href="' + escapeHtml(targetJobsUrl) + '" class="pds-suggest-all" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #faf8f5; color: #f2761b; font-size: 13px; font-weight: 700; text-decoration: none; border-top: 1px solid #ebdcd0;">' +
                        '<span>Xem tất cả <strong>' + total + '</strong> việc làm cho "' + escapeHtml(keyword) + '"</span>' +
                        '<span>&rarr;</span>' +
                    '</a>';

                    html += '</div>';

                    dropdown.innerHTML = html;
                    dropdown.style.display = 'block';

                    // Hover effects for items
                    var items = dropdown.querySelectorAll('.pds-suggest-item');
                    items.forEach(function (item) {
                        item.addEventListener('mouseenter', function () {
                            item.style.background = '#fff8f2';
                        });
                        item.addEventListener('mouseleave', function () {
                            item.style.background = '#ffffff';
                        });
                    });

                    var allBtn = dropdown.querySelector('.pds-suggest-all');
                    if (allBtn) {
                        allBtn.addEventListener('mouseenter', function () {
                            allBtn.style.background = '#f5efe8';
                        });
                        allBtn.addEventListener('mouseleave', function () {
                            allBtn.style.background = '#faf8f5';
                        });
                    }
                })
                .catch(function (err) {
                    console.error('Failed to fetch suggestions:', err);
                    hideDropdown();
                });
            }

            input.addEventListener('input', function () {
                var val = input.value.trim();
                currentKeyword = val;

                clearTimeout(debounceTimer);
                if (val.length < 2) {
                    hideDropdown();
                    return;
                }

                debounceTimer = setTimeout(function () {
                    fetchSuggestions(val);
                }, 220);
            });

            input.addEventListener('focus', function () {
                if (input.value.trim().length >= 2) {
                    fetchSuggestions(input.value.trim());
                }
            });

            // Close on click outside
            document.addEventListener('click', function (e) {
                if (!form.contains(e.target)) {
                    hideDropdown();
                }
            });

            // Close on Escape key
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    hideDropdown();
                }
            });
        });

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function highlightMatch(text, query) {
            if (!text || !query) return escapeHtml(text);
            var escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var regex = new RegExp('(' + escapedQuery + ')', 'gi');
            return escapeHtml(text).replace(regex, '<span style="color: #f2761b; background: #fff2e6; border-radius: 2px;">$1</span>');
        }
    });
})();
