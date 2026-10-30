/**
 * =============================================================================
 * TƯƠNG TÁC AJAX LOAD MORE & SẮP XẾP ĐỘNG TRANG ALL JOBS (ALL JOBS SCRIPTS)
 * =============================================================================
 *
 * @package           JobScout
 * @subpackage        PDS / JavaScript All Jobs
 * @author            Trần Văn Trọng
 * @git_branch        tran-van-trong/all-jobs
 * @ui_reference      docs/ui-reference/04-all-jobs.png
 *
 * -----------------------------------------------------------------------------
 * 1. CHỨC NĂNG HOẠT ĐỘNG (FUNCTIONAL OVERVIEW):
 * -----------------------------------------------------------------------------
 * - Cơ chế AJAX Phân trang (Load More Jobs):
 *   + Khi người dùng bấm nút "LOAD MORE JOBS", gửi request ngầm `pds_ajax_jobs=1` kèm số trang `page=N`.
 *   + Nối thêm danh sách thẻ việc làm mới vào cuối `#pds-all-jobs-grid` mượt mà không load lại trang.
 *   + Cập nhật lại thuộc tính `data-page` và vô hiệu hóa nút khi đã tải hết toàn bộ việc làm.
 * - Sắp xếp tức thời (Dynamic Sort):
 *   + Khi thay đổi lựa chọn trong dropdown "Sort Jobs" (Latest, Oldest, Title A-Z, Title Z-A),
 *     tự động reset về trang 1 và gửi request AJAX cập nhật lại danh sách ngay lập tức.
 * - Duy trì bộ lọc tìm kiếm (Keywords & Location):
 *   + Tự động đọc tham số từ đối tượng `window.pdsAllJobsData` hoặc URL query string để đảm bảo
 *     Load More và Sort luôn tôn trọng tiêu chí tìm kiếm hiện tại.
 *
 * -----------------------------------------------------------------------------
 * 2. GIAO DIỆN & TRẠNG THÁI UI (DESIGN SPECS):
 * -----------------------------------------------------------------------------
 * - Trạng thái Loading của nút: Thay đổi văn bản thành "LOADING..." kèm hiệu ứng mờ nhẹ (opacity: 0.7).
 * - Trạng thái hoàn thành: Đổi văn bản nút thành "NO MORE JOBS" với màu xám nhạt khi không còn bài đăng.
 * - Hiệu ứng chuyển động mượt mà (Fade In) cho các card mới được chèn vào lưới.
 * =============================================================================
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var loadMoreBtn = document.getElementById('pds-all-jobs-load-more');
        var sortSelect  = document.getElementById('pds-all-jobs-sort');
        var grid        = document.getElementById('pds-all-jobs-grid');

        if (!grid) {
            return;
        }

        var config = window.pdsAllJobsData || {};
        var endpoint = config.endpoint || window.location.pathname;
        var currentPage = parseInt(loadMoreBtn ? loadMoreBtn.getAttribute('data-page') : '1', 10) || 1;
        var maxPages = parseInt(loadMoreBtn ? loadMoreBtn.getAttribute('data-max') : '1', 10) || 1;
        var currentSort = sortSelect ? sortSelect.value : (config.sort || 'latest');
        var isLoading = false;

        var currentUrlParams = new URLSearchParams(window.location.search);
        var searchKeywords = (config.keywords !== undefined && config.keywords !== null && config.keywords !== '')
            ? config.keywords
            : (currentUrlParams.get('search_keywords') || '');
        var searchLocation = (config.location !== undefined && config.location !== null && config.location !== '')
            ? config.location
            : (currentUrlParams.get('search_location') || '');
        var searchCategory = (config.category !== undefined && config.category !== null && config.category !== '')
            ? config.category
            : (currentUrlParams.get('search_category') || '');

        // If initial load already has all posts loaded
        if (loadMoreBtn && maxPages <= 1) {
            loadMoreBtn.setAttribute('data-all-loaded', 'true');
        }

        // ---------------------------------------------------------------------
        // Handle Load More
        // ---------------------------------------------------------------------
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function (e) {
                e.preventDefault();

                if (isLoading) {
                    return;
                }

                if (loadMoreBtn.getAttribute('data-all-loaded') === 'true') {
                    return;
                }

                var nextPage = currentPage + 1;
                isLoading = true;
                var originalText = loadMoreBtn.textContent;
                loadMoreBtn.textContent = config.loading || 'LOADING...';
                loadMoreBtn.classList.add('is-loading');

                var url = new URL(endpoint, window.location.origin);
                url.searchParams.set('pds_ajax_jobs', '1');
                url.searchParams.set('page', nextPage);
                url.searchParams.set('sort', currentSort);
                if (searchKeywords) {
                    url.searchParams.set('search_keywords', searchKeywords);
                }
                if (searchLocation) {
                    url.searchParams.set('search_location', searchLocation);
                }
                if (searchCategory) {
                    url.searchParams.set('search_category', searchCategory);
                }

                fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function (response) {
                    return response.json();
                })
                .then(function (res) {
                    isLoading = false;
                    loadMoreBtn.classList.remove('is-loading');

                    if (res && res.success && res.data) {
                        var temp = document.createElement('div');
                        temp.innerHTML = res.data.html;

                        while (temp.firstChild) {
                            grid.appendChild(temp.firstChild);
                        }

                        currentPage = nextPage;
                        loadMoreBtn.setAttribute('data-page', currentPage);

                        if (!res.data.has_more || currentPage >= res.data.max_pages) {
                            loadMoreBtn.textContent = config.noMore || 'NO MORE JOBS';
                            loadMoreBtn.setAttribute('data-all-loaded', 'true');
                            loadMoreBtn.classList.add('is-finished');
                        } else {
                            loadMoreBtn.textContent = config.loadMore || 'LOAD MORE JOBS';
                        }
                    } else {
                        loadMoreBtn.textContent = config.noMore || 'NO MORE JOBS';
                        loadMoreBtn.setAttribute('data-all-loaded', 'true');
                    }
                })
                .catch(function (err) {
                    console.error('Failed to load more jobs:', err);
                    isLoading = false;
                    loadMoreBtn.classList.remove('is-loading');
                    loadMoreBtn.textContent = originalText;
                });
            });
        }

        // ---------------------------------------------------------------------
        // Handle Sorting
        // ---------------------------------------------------------------------
        if (sortSelect) {
            sortSelect.addEventListener('change', function () {
                currentSort = sortSelect.value;
                currentPage = 1;
                isLoading = true;
                grid.classList.add('is-updating');

                var url = new URL(endpoint, window.location.origin);
                url.searchParams.set('pds_ajax_jobs', '1');
                url.searchParams.set('page', '1');
                url.searchParams.set('sort', currentSort);
                if (searchKeywords) {
                    url.searchParams.set('search_keywords', searchKeywords);
                }
                if (searchLocation) {
                    url.searchParams.set('search_location', searchLocation);
                }
                if (searchCategory) {
                    url.searchParams.set('search_category', searchCategory);
                }

                fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function (response) {
                    return response.json();
                })
                .then(function (res) {
                    isLoading = false;
                    grid.classList.remove('is-updating');

                    if (res && res.success && res.data) {
                        grid.innerHTML = res.data.html;

                        if (loadMoreBtn) {
                            loadMoreBtn.setAttribute('data-page', '1');
                            maxPages = res.data.max_pages || 1;
                            loadMoreBtn.setAttribute('data-max', maxPages);

                            if (maxPages > 1) {
                                loadMoreBtn.removeAttribute('data-all-loaded');
                                loadMoreBtn.classList.remove('is-finished');
                                loadMoreBtn.textContent = config.loadMore || 'LOAD MORE JOBS';
                            } else {
                                loadMoreBtn.setAttribute('data-all-loaded', 'true');
                                loadMoreBtn.textContent = config.loadMore || 'LOAD MORE JOBS';
                            }
                        }

                        // Update browser URL query param without full page refresh
                        if (window.history && window.history.pushState) {
                            var stateUrl = new URL(window.location.href);
                            stateUrl.searchParams.set('sort', currentSort);
                            if (searchKeywords) {
                                stateUrl.searchParams.set('search_keywords', searchKeywords);
                            }
                            if (searchLocation) {
                                stateUrl.searchParams.set('search_location', searchLocation);
                            }
                            if (searchCategory) {
                                stateUrl.searchParams.set('search_category', searchCategory);
                            }
                            window.history.pushState({ sort: currentSort }, '', stateUrl.toString());
                        }
                    }
                })
                .catch(function (err) {
                    console.error('Failed to sort jobs:', err);
                    isLoading = false;
                    grid.classList.remove('is-updating');
                });
            });
        }
    });
})();
