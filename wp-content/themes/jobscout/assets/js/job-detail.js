/**
 * Job Detail Page Scripts
 * Handles Share button clipboard copy and Apply button smooth scroll.
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // Share Button
        var shareBtn = document.querySelector('.pds-job-detail-btn-share');
        if (shareBtn) {
            shareBtn.addEventListener('click', function () {
                var url = shareBtn.getAttribute('data-share-url') || window.location.href;
                var originalText = shareBtn.textContent;

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        shareBtn.textContent = 'COPIED!';
                        setTimeout(function () {
                            shareBtn.textContent = originalText;
                        }, 2000);
                    }).catch(function () {
                        prompt('Copy this link:', url);
                    });
                } else {
                    var input = document.createElement('input');
                    input.value = url;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                    shareBtn.textContent = 'COPIED!';
                    setTimeout(function () {
                        shareBtn.textContent = originalText;
                    }, 2000);
                }
            });
        }

        // Apply Job Button & Application Toggle
        var applyBtn = document.querySelector('.pds-job-detail-btn-apply');
        var applyArea = document.getElementById('pds-job-apply');

        function toggleApplication(openOnly) {
            if (!applyArea) return;
            var applicationToggle = applyArea.querySelector('.application_button');
            var details = applyArea.querySelector('.application_details');
            if (applicationToggle && details) {
                var isOpen = applicationToggle.classList.contains('active') || window.getComputedStyle(details).display !== 'none';
                if (!openOnly && isOpen) {
                    details.style.display = 'none';
                    applicationToggle.classList.remove('active');
                } else {
                    details.style.display = 'block';
                    applicationToggle.classList.add('active');
                }
            }
        }

        if (applyArea) {
            applyArea.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('application_button')) {
                    e.preventDefault();
                    toggleApplication(false);
                }
            });
        }

        if (applyBtn && applyArea) {
            applyBtn.addEventListener('click', function (e) {
                e.preventDefault();
                toggleApplication(true);
                applyArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }
    });
})();
