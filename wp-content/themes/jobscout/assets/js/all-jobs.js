/**
 * All Jobs interactive sorting and AJAX Load More
 *
 * Scoped specifically to .pds-all-jobs-page
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
