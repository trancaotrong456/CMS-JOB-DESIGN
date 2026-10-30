/**
 * =============================================================================
 * TƯƠNG TÁC NGƯỜI DÙNG TRANG CHI TIẾT VIỆC LÀM (JOB DETAIL SCRIPTS)
 * =============================================================================
 *
 * @package           JobScout
 * @subpackage        PDS / JavaScript Job Detail
 * @author            Trần Văn Trọng
 * @git_branch        tran-van-trong/job-detail
 * @ui_reference      docs/ui-reference/05-job-detail.png
 *
 * Includes:
 * 1. Share button URL copy & feedback
 * 2. Apply Job header button smooth scroll & focus
 * 3. Application Form with CV drag & drop upload, validation & AJAX submission
 * 4. Company Photos Lightbox Modal with full gallery navigation & keyboard access
 * =============================================================================
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var appData = window.pdsJobDetailData || {};
        var ajaxUrl = appData.ajax_url || '/wp-admin/admin-ajax.php';
        var appNonce = appData.nonce || '';
        var i18n = appData.i18n || {};

        // =====================================================================
        // 1. SHARE BUTTON
        // =====================================================================
        var shareBtn = document.querySelector('.pds-job-detail-btn-share');
        if (shareBtn) {
            shareBtn.addEventListener('click', function () {
                var url = shareBtn.getAttribute('data-share-url') || window.location.href;
                var originalText = shareBtn.textContent;

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function () {
                        shareBtn.textContent = i18n.copied || 'COPIED!';
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
                    try {
                        document.execCommand('copy');
                        shareBtn.textContent = i18n.copied || 'COPIED!';
                        setTimeout(function () {
                            shareBtn.textContent = originalText;
                        }, 2000);
                    } catch (e) {
                        prompt('Copy this link:', url);
                    }
                    document.body.removeChild(input);
                }
            });
        }

        // =====================================================================
        // 2. APPLY JOB HEADER BUTTON & SCROLL
        // =====================================================================
        var headerApplyBtn = document.querySelector('.pds-job-detail-btn-apply');
        var applyArea      = document.getElementById('pds-job-apply');
        var nameInput      = document.getElementById('pds_candidate_name');

        if (headerApplyBtn && applyArea) {
            headerApplyBtn.addEventListener('click', function (e) {
                e.preventDefault();
                applyArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
                setTimeout(function () {
                    if (nameInput) {
                        nameInput.focus();
                    }
                }, 500);
            });
        }

        // =====================================================================
        // 3. APPLICATION FORM & CV UPLOAD HANDLING
        // =====================================================================
        var applyForm     = document.getElementById('pds-job-application-form');
        var cvInput       = document.getElementById('pds_candidate_cv');
        var cvDropzone    = document.getElementById('pds-cv-dropzone');
        var cvCard        = document.getElementById('pds-cv-selected-card');
        var cvFileNameEl  = document.getElementById('pds-cv-file-name');
        var cvFileSizeEl  = document.getElementById('pds-cv-file-size');
        var cvBadgeEl     = document.getElementById('pds-cv-ext-badge');
        var cvRemoveBtn   = document.getElementById('pds-cv-remove-btn');
        var submitBtn     = document.getElementById('pds-btn-submit-app');
        var alertError    = document.getElementById('pds-app-alert-error');
        var alertErrorMsg = document.getElementById('pds-app-alert-error-msg');
        var alertSuccess  = document.getElementById('pds-app-alert-success');
        var alertSuccessMsg = document.getElementById('pds-app-alert-success-msg');

        var maxFileSize = 10 * 1024 * 1024; // 10MB
        var allowedExts = ['pdf', 'doc', 'docx'];

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function clearFieldErrors() {
            var errorEls = document.querySelectorAll('.pds-field-error');
            errorEls.forEach(function (el) {
                el.textContent = '';
                el.style.display = 'none';
            });
            var inputEls = document.querySelectorAll('.pds-form-input, .pds-form-textarea, .pds-cv-dropzone');
            inputEls.forEach(function (el) {
                el.classList.remove('has-error');
            });
            if (alertError) alertError.style.display = 'none';
            if (alertSuccess) alertSuccess.style.display = 'none';
        }

        function showFieldError(fieldId, errorText) {
            var errEl = document.getElementById('err-' + fieldId);
            if (errEl) {
                errEl.textContent = errorText;
                errEl.style.display = 'block';
            }
            var inputEl = document.getElementById('pds_' + fieldId);
            if (inputEl) {
                inputEl.classList.add('has-error');
            } else if ('candidate_cv' === fieldId && cvDropzone) {
                cvDropzone.classList.add('has-error');
            }
        }

        function handleFileSelection(file) {
            if (!file) return;

            var ext = file.name.split('.').pop().toLowerCase();
            if (allowedExts.indexOf(ext) === -1) {
                showFieldError('candidate_cv', i18n.invalid_file || 'Invalid file format. Only .pdf, .doc, and .docx are supported.');
                if (cvInput) cvInput.value = '';
                return;
            }

            if (file.size > maxFileSize) {
                showFieldError('candidate_cv', i18n.file_too_large || 'File size exceeds the 10MB limit.');
                if (cvInput) cvInput.value = '';
                return;
            }

            // Clear any previous error
            var errEl = document.getElementById('err-candidate_cv');
            if (errEl) {
                errEl.textContent = '';
                errEl.style.display = 'none';
            }
            if (cvDropzone) cvDropzone.classList.remove('has-error');

            // Update UI card
            if (cvFileNameEl) cvFileNameEl.textContent = file.name;
            if (cvFileSizeEl) cvFileSizeEl.textContent = formatBytes(file.size);
            if (cvBadgeEl) cvBadgeEl.textContent = ext.toUpperCase();

            if (cvDropzone) cvDropzone.style.display = 'none';
            if (cvCard) cvCard.style.display = 'flex';
        }

        if (cvDropzone && cvInput) {
            // Click dropzone to browse
            cvDropzone.addEventListener('click', function () {
                cvInput.click();
            });

            // Keyboard access
            cvDropzone.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    cvInput.click();
                }
            });

            // Drag and drop events
            ['dragenter', 'dragover'].forEach(function (eventName) {
                cvDropzone.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    cvDropzone.classList.add('is-dragover');
                });
            });

            ['dragleave', 'drop'].forEach(function (eventName) {
                cvDropzone.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    cvDropzone.classList.remove('is-dragover');
                });
            });

            cvDropzone.addEventListener('drop', function (e) {
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    var droppedFile = e.dataTransfer.files[0];
                    try {
                        // Assign file to input if browser supports DataTransfer assignment
                        var dt = new DataTransfer();
                        dt.items.add(droppedFile);
                        cvInput.files = dt.files;
                    } catch (err) {}
                    handleFileSelection(droppedFile);
                }
            });

            // File input change
            cvInput.addEventListener('change', function () {
                if (cvInput.files && cvInput.files.length > 0) {
                    handleFileSelection(cvInput.files[0]);
                }
            });
        }

        // Remove selected file
        if (cvRemoveBtn && cvInput) {
            cvRemoveBtn.addEventListener('click', function () {
                cvInput.value = '';
                if (cvCard) cvCard.style.display = 'none';
                if (cvDropzone) cvDropzone.style.display = 'block';
                var errEl = document.getElementById('err-candidate_cv');
                if (errEl) {
                    errEl.textContent = '';
                    errEl.style.display = 'none';
                }
            });
        }

        // Form Submit Handler
        if (applyForm) {
            applyForm.addEventListener('submit', function (e) {
                e.preventDefault();
                clearFieldErrors();

                var hasError = false;
                var candidateNameVal  = document.getElementById('pds_candidate_name').value.trim();
                var candidateEmailVal = document.getElementById('pds_candidate_email').value.trim();
                var candidatePhoneVal = document.getElementById('pds_candidate_phone').value.trim();
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!candidateNameVal) {
                    showFieldError('candidate_name', 'Please enter your full name.');
                    hasError = true;
                }

                if (!candidateEmailVal || !emailRegex.test(candidateEmailVal)) {
                    showFieldError('candidate_email', 'Please enter a valid email address.');
                    hasError = true;
                }

                if (!candidatePhoneVal) {
                    showFieldError('candidate_phone', 'Please enter your phone number.');
                    hasError = true;
                }

                if (!cvInput || !cvInput.files || cvInput.files.length === 0) {
                    showFieldError('candidate_cv', 'Please upload your CV (.pdf, .doc, or .docx).');
                    hasError = true;
                }

                if (hasError) {
                    if (alertError && alertErrorMsg) {
                        alertErrorMsg.textContent = 'Please check the highlighted fields above.';
                        alertError.style.display = 'flex';
                        alertError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }

                // Prepare FormData
                var formData = new FormData(applyForm);
                formData.append('action', 'pds_submit_job_application');
                formData.append('security', appNonce);

                // Set submitting state
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('is-submitting');
                    var btnText = submitBtn.querySelector('.pds-btn-text');
                    var btnSpinner = submitBtn.querySelector('.pds-btn-spinner');
                    if (btnText) btnText.textContent = i18n.submitting || 'Submitting...';
                    if (btnSpinner) btnSpinner.style.display = 'inline-block';
                }

                fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function (response) {
                    return response.json();
                })
                .then(function (res) {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('is-submitting');
                        var btnText = submitBtn.querySelector('.pds-btn-text');
                        var btnSpinner = submitBtn.querySelector('.pds-btn-spinner');
                        if (btnText) btnText.textContent = i18n.submit || 'SUBMIT APPLICATION';
                        if (btnSpinner) btnSpinner.style.display = 'none';
                    }

                    if (res && res.success) {
                        // Success
                        applyForm.reset();
                        if (cvCard) cvCard.style.display = 'none';
                        if (cvDropzone) cvDropzone.style.display = 'block';

                        if (alertSuccess && alertSuccessMsg) {
                            alertSuccessMsg.textContent = res.data.message || 'Application submitted successfully!';
                            alertSuccess.style.display = 'flex';
                            alertSuccess.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } else {
                        // Error
                        var errorMsg = (res && res.data && res.data.message)
                            ? res.data.message
                            : 'Failed to submit application. Please check your information and try again.';

                        if (alertError && alertErrorMsg) {
                            alertErrorMsg.textContent = errorMsg;
                            alertError.style.display = 'flex';
                            alertError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }

                        if (res && res.data && res.data.errors) {
                            for (var field in res.data.errors) {
                                if (res.data.errors.hasOwnProperty(field)) {
                                    showFieldError(field, res.data.errors[field]);
                                }
                            }
                        }
                    }
                })
                .catch(function (err) {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('is-submitting');
                        var btnText = submitBtn.querySelector('.pds-btn-text');
                        var btnSpinner = submitBtn.querySelector('.pds-btn-spinner');
                        if (btnText) btnText.textContent = i18n.submit || 'SUBMIT APPLICATION';
                        if (btnSpinner) btnSpinner.style.display = 'none';
                    }

                    if (alertError && alertErrorMsg) {
                        alertErrorMsg.textContent = 'A network error occurred while submitting your application. Please try again.';
                        alertError.style.display = 'flex';
                    }
                });
            });
        }

        // =====================================================================
        // 4. COMPANY PHOTOS LIGHTBOX MODAL
        // =====================================================================
        var photoTriggers = document.querySelectorAll('.pds-company-photo-wrap');

        if (photoTriggers.length > 0) {
            // Build Lightbox Modal DOM Element once
            var lightbox = document.createElement('div');
            lightbox.id = 'pds-lightbox-modal';
            lightbox.className = 'pds-lightbox-modal';
            lightbox.setAttribute('aria-hidden', 'true');
            lightbox.setAttribute('role', 'dialog');
            lightbox.setAttribute('aria-modal', 'true');

            lightbox.innerHTML = [
                '<div class="pds-lightbox-overlay" id="pds-lightbox-overlay"></div>',
                '<div class="pds-lightbox-dialog" role="document">',
                '    <button type="button" class="pds-lightbox-btn-close" id="pds-lightbox-btn-close" aria-label="Close Lightbox">&times;</button>',
                '    <div class="pds-lightbox-stage">',
                '        <button type="button" class="pds-lightbox-nav pds-lightbox-prev" id="pds-lightbox-prev" aria-label="Previous image">&#10094;</button>',
                '        <div class="pds-lightbox-viewport">',
                '            <img src="" alt="" class="pds-lightbox-image" id="pds-lightbox-image">',
                '            <div class="pds-lightbox-caption">',
                '                <span class="pds-lightbox-counter" id="pds-lightbox-counter">1 / 1</span>',
                '                <span class="pds-lightbox-title" id="pds-lightbox-title"></span>',
                '            </div>',
                '        </div>',
                '        <button type="button" class="pds-lightbox-nav pds-lightbox-next" id="pds-lightbox-next" aria-label="Next image">&#10095;</button>',
                '    </div>',
                '    <div class="pds-lightbox-thumbs" id="pds-lightbox-thumbs"></div>',
                '</div>'
            ].join('\n');

            document.body.appendChild(lightbox);

            var lbImage     = document.getElementById('pds-lightbox-image');
            var lbCounter   = document.getElementById('pds-lightbox-counter');
            var lbTitle     = document.getElementById('pds-lightbox-title');
            var lbPrev      = document.getElementById('pds-lightbox-prev');
            var lbNext      = document.getElementById('pds-lightbox-next');
            var lbClose     = document.getElementById('pds-lightbox-btn-close');
            var lbOverlay   = document.getElementById('pds-lightbox-overlay');
            var lbThumbs    = document.getElementById('pds-lightbox-thumbs');

            var currentGallery = [];
            var currentIndex   = 0;
            var lastActiveElement = null;

            function updateLightboxView() {
                if (!currentGallery || currentGallery.length === 0) return;

                var item = currentGallery[currentIndex];
                lbImage.classList.remove('is-loaded');
                lbImage.src = item.full || item.thumb;
                lbImage.alt = item.title || 'Company Photo';

                lbImage.onload = function () {
                    lbImage.classList.add('is-loaded');
                };

                if (lbCounter) {
                    lbCounter.textContent = (currentIndex + 1) + ' / ' + currentGallery.length;
                }

                if (lbTitle) {
                    lbTitle.textContent = item.title || '';
                }

                // If only 1 image, hide navigation buttons and thumbs
                if (currentGallery.length <= 1) {
                    if (lbPrev) lbPrev.style.display = 'none';
                    if (lbNext) lbNext.style.display = 'none';
                    if (lbCounter) lbCounter.style.display = 'none';
                    if (lbThumbs) lbThumbs.style.display = 'none';
                } else {
                    if (lbPrev) lbPrev.style.display = 'flex';
                    if (lbNext) lbNext.style.display = 'flex';
                    if (lbCounter) lbCounter.style.display = 'inline-block';
                    if (lbThumbs) lbThumbs.style.display = 'flex';

                    // Update active thumbnail
                    var thumbBtns = lbThumbs.querySelectorAll('.pds-lightbox-thumb-btn');
                    thumbBtns.forEach(function (btn, idx) {
                        if (idx === currentIndex) {
                            btn.classList.add('is-active');
                            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                        } else {
                            btn.classList.remove('is-active');
                        }
                    });
                }
            }

            function openLightbox(gallery, startIndex) {
                if (!gallery || gallery.length === 0) return;
                currentGallery = gallery;
                currentIndex   = startIndex || 0;
                lastActiveElement = document.activeElement;

                // Build thumbnails
                lbThumbs.innerHTML = '';
                if (currentGallery.length > 1) {
                    currentGallery.forEach(function (photo, idx) {
                        var thumbBtn = document.createElement('button');
                        thumbBtn.type = 'button';
                        thumbBtn.className = 'pds-lightbox-thumb-btn' + (idx === currentIndex ? ' is-active' : '');
                        thumbBtn.setAttribute('aria-label', 'View photo ' + (idx + 1));
                        var thumbImg = document.createElement('img');
                        thumbImg.src = photo.thumb || photo.full;
                        thumbImg.alt = photo.title || '';
                        thumbBtn.appendChild(thumbImg);

                        thumbBtn.addEventListener('click', function () {
                            currentIndex = idx;
                            updateLightboxView();
                        });

                        lbThumbs.appendChild(thumbBtn);
                    });
                }

                updateLightboxView();

                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.classList.add('pds-lightbox-active');

                // Focus close button for accessibility
                if (lbClose) lbClose.focus();
            }

            function closeLightbox() {
                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('pds-lightbox-active');
                if (lastActiveElement && typeof lastActiveElement.focus === 'function') {
                    lastActiveElement.focus();
                }
            }

            function nextPhoto() {
                if (!currentGallery || currentGallery.length <= 1) return;
                currentIndex = (currentIndex + 1) % currentGallery.length;
                updateLightboxView();
            }

            function prevPhoto() {
                if (!currentGallery || currentGallery.length <= 1) return;
                currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
                updateLightboxView();
            }

            // Bind triggers
            photoTriggers.forEach(function (trigger) {
                function triggerOpen() {
                    var galleryData = [];
                    var rawGallery = trigger.getAttribute('data-gallery');
                    if (rawGallery) {
                        try {
                            galleryData = JSON.parse(rawGallery);
                        } catch (e) {}
                    }

                    // Fallback if data-gallery was empty: use image inside trigger
                    if (!galleryData || galleryData.length === 0) {
                        var img = trigger.querySelector('.pds-company-photo-img');
                        if (img && img.src) {
                            galleryData = [{
                                full: img.src,
                                thumb: img.src,
                                title: img.alt || 'Company Photo'
                            }];
                        }
                    }

                    openLightbox(galleryData, 0);
                }

                trigger.addEventListener('click', function (e) {
                    e.preventDefault();
                    triggerOpen();
                });

                trigger.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        triggerOpen();
                    }
                });
            });

            // Close button click
            if (lbClose) {
                lbClose.addEventListener('click', function (e) {
                    e.preventDefault();
                    closeLightbox();
                });
            }

            // Click outside (overlay)
            if (lbOverlay) {
                lbOverlay.addEventListener('click', function (e) {
                    e.preventDefault();
                    closeLightbox();
                });
            }

            // Next / Prev buttons
            if (lbNext) {
                lbNext.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    nextPhoto();
                });
            }

            if (lbPrev) {
                lbPrev.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    prevPhoto();
                });
            }

            // Keyboard navigation (ESC, ArrowLeft, ArrowRight)
            document.addEventListener('keydown', function (e) {
                if (!lightbox.classList.contains('is-open')) return;

                if (e.key === 'Escape' || e.keyCode === 27) {
                    e.preventDefault();
                    closeLightbox();
                } else if (e.key === 'ArrowRight' || e.keyCode === 39) {
                    e.preventDefault();
                    nextPhoto();
                } else if (e.key === 'ArrowLeft' || e.keyCode === 37) {
                    e.preventDefault();
                    prevPhoto();
                }
            });
        }
    });
})();
