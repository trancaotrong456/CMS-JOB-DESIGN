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

        // Apply Job Button
        var applyBtn = document.querySelector('.pds-job-detail-btn-apply');
        var applyArea = document.getElementById('pds-job-apply');
        if (applyBtn && applyArea) {
            applyBtn.addEventListener('click', function (e) {
                var applicationToggle = applyArea.querySelector('.application_button');
                if (applicationToggle) {
                    e.preventDefault();
                    if (!applicationToggle.classList.contains('active')) {
                        applicationToggle.click();
                    }
                    applyArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
    });
})();
