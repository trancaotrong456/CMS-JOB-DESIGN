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
 * -----------------------------------------------------------------------------
 * 1. CHỨC NĂNG HOẠT ĐỘNG (FUNCTIONAL OVERVIEW):
 * -----------------------------------------------------------------------------
 * - Tương tác Nút "Share":
 *   + Tự động sao chép URL của bài viết việc làm vào Clipboard bằng `navigator.clipboard`.
 *   + Hiển thị thông báo trạng thái "COPIED!" trong 2 giây rồi tự hoàn về chữ "SHARE".
 *   + Fallback hộp thoại `prompt` an toàn nếu trình duyệt không hỗ trợ Clipboard API.
 * - Tương tác Nút "APPLY THIS JOB":
 *   + Mở rộng khung/form ứng tuyển công việc bên dưới hoặc mở modal ứng tuyển trực tuyến.
 *   + Tự động cuộn trang êm ái (Smooth Scroll) đưa người dùng đến khu vực điền thông tin và đính kèm CV.
 *
 * -----------------------------------------------------------------------------
 * 2. GIAO DIỆN & TRẠNG THÁI UI (DESIGN SPECS):
 * -----------------------------------------------------------------------------
 * - Nút Share: Viền mỏng 1px #ccc, bo góc nhẹ 3px, font chữ in hoa thanh lịch, hiệu ứng phản hồi nhanh.
 * - Nút Apply: Nền cam #f2761b, chữ in hoa trắng đậm nét, hiệu ứng phóng nhẹ khi click.
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
        // 1. SOCIAL MEDIA SHARE POPUP MODAL
        // =====================================================================
        var shareButtons     = document.querySelectorAll('.pds-job-detail-btn-share, [data-open-share-modal]');
        var shareModal       = document.getElementById('pds-social-share-modal');
        var shareLinkInput   = document.getElementById('pds-share-link-input');
        var shareCopyBtn     = document.getElementById('pds-share-copy-btn');
        var platformsTrack   = shareModal ? shareModal.querySelector('.pds-share-platforms') : null;
        var carouselPrevBtn  = shareModal ? shareModal.querySelector('.pds-share-carousel-prev') : null;
        var carouselNextBtn  = shareModal ? shareModal.querySelector('.pds-share-carousel-next') : null;
        var lastShareTrigger = null;

        function updateCarouselNav() {
            if (!platformsTrack || !carouselPrevBtn || !carouselNextBtn) return;
            var scrollLeft = platformsTrack.scrollLeft;
            var maxScroll  = platformsTrack.scrollWidth - platformsTrack.clientWidth;

            if (maxScroll <= 4) {
                carouselPrevBtn.classList.add('is-hidden');
                carouselNextBtn.classList.add('is-hidden');
                return;
            }

            if (scrollLeft > 6) {
                carouselPrevBtn.classList.remove('is-hidden');
            } else {
                carouselPrevBtn.classList.add('is-hidden');
            }

            if (scrollLeft < maxScroll - 6) {
                carouselNextBtn.classList.remove('is-hidden');
            } else {
                carouselNextBtn.classList.add('is-hidden');
            }
        }

        function openShareModal(trigger) {
            if (!shareModal) return;
            lastShareTrigger = trigger || null;

            // Extract dynamic metadata from trigger or document
            var targetUrl   = (trigger && trigger.getAttribute('data-share-url')) || window.location.href;
            var targetTitle = (trigger && trigger.getAttribute('data-share-title')) || document.title;
            var targetComp  = (trigger && trigger.getAttribute('data-share-company')) || '';
            var targetImg   = (trigger && trigger.getAttribute('data-share-image')) || '';

            if (shareLinkInput) {
                shareLinkInput.value = targetUrl;
                if (targetTitle) shareLinkInput.setAttribute('data-share-title', targetTitle);
                if (targetComp)  shareLinkInput.setAttribute('data-share-company', targetComp);
                if (targetImg)   shareLinkInput.setAttribute('data-share-image', targetImg);
            }

            // Ensure modal lives at document.body for clean fixed stacking
            if (shareModal.parentNode !== document.body) {
                document.body.appendChild(shareModal);
            }

            shareModal.classList.add('is-active');
            shareModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('pds-share-modal-open');

            if (trigger) trigger.setAttribute('aria-expanded', 'true');

            // Reset copy button state
            if (shareCopyBtn) {
                shareCopyBtn.textContent = i18n.copy || 'Copy';
                shareCopyBtn.classList.remove('is-copied');
            }

            // Reset carousel scroll and update nav arrows
            if (platformsTrack) {
                platformsTrack.scrollLeft = 0;
                setTimeout(updateCarouselNav, 60);
            }

            // Focus management
            setTimeout(function () {
                if (shareCopyBtn) shareCopyBtn.focus();
            }, 80);
        }

        function closeShareModal() {
            if (!shareModal || !shareModal.classList.contains('is-active')) return;

            shareModal.classList.remove('is-active');
            shareModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('pds-share-modal-open');

            if (lastShareTrigger) {
                lastShareTrigger.setAttribute('aria-expanded', 'false');
                try { lastShareTrigger.focus(); } catch (e) {}
            }
        }

        // Attach trigger clicks to all SHARE buttons on page
        shareButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                openShareModal(btn);
            });
        });

        // Close triggers inside modal
        if (shareModal) {
            shareModal.addEventListener('click', function (e) {
                if (e.target.hasAttribute('data-pds-share-close') || e.target.closest('[data-pds-share-close]')) {
                    e.preventDefault();
                    closeShareModal();
                }
            });

            // Prevent clicks inside card from bubbling to backdrop
            var shareCard = shareModal.querySelector('.pds-share-card');
            if (shareCard) {
                shareCard.addEventListener('click', function (e) {
                    if (e.target.closest('[data-pds-share-close]')) return;
                    e.stopPropagation();
                });
            }

            // Social platform item clicks
            var platformButtons = shareModal.querySelectorAll('.pds-share-item[data-platform]');
            platformButtons.forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var platform = btn.getAttribute('data-platform');
                    var url      = (shareLinkInput && shareLinkInput.value) || window.location.href;
                    var title    = (shareLinkInput && shareLinkInput.getAttribute('data-share-title')) || document.title;
                    var comp     = (shareLinkInput && shareLinkInput.getAttribute('data-share-company')) || '';
                    var image    = (shareLinkInput && shareLinkInput.getAttribute('data-share-image')) || '';
                    var fullText = comp ? (title + ' at ' + comp) : title;

                    handleSocialShare(platform, url, fullText, image);
                });
            });

            // Carousel navigation listeners
            if (platformsTrack) {
                platformsTrack.addEventListener('scroll', updateCarouselNav, { passive: true });
                window.addEventListener('resize', updateCarouselNav);
            }
            if (carouselPrevBtn) {
                carouselPrevBtn.addEventListener('click', function () {
                    platformsTrack.scrollBy({ left: -160, behavior: 'smooth' });
                });
            }
            if (carouselNextBtn) {
                carouselNextBtn.addEventListener('click', function () {
                    platformsTrack.scrollBy({ left: 160, behavior: 'smooth' });
                });
            }

            // Copy Link button handler
            if (shareCopyBtn && shareLinkInput) {
                shareCopyBtn.addEventListener('click', function () {
                    var urlToCopy = shareLinkInput.value || window.location.href;
                    var copySuccess = function () {
                        shareCopyBtn.textContent = i18n.copied || 'Copied!';
                        shareCopyBtn.classList.add('is-copied');
                        setTimeout(function () {
                            shareCopyBtn.textContent = i18n.copy || 'Copy';
                            shareCopyBtn.classList.remove('is-copied');
                        }, 2000);
                    };

                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(urlToCopy).then(copySuccess).catch(function () {
                            shareLinkInput.select();
                            try {
                                document.execCommand('copy');
                                copySuccess();
                            } catch (err) {
                                prompt('Copy link:', urlToCopy);
                            }
                        });
                    } else {
                        shareLinkInput.select();
                        try {
                            document.execCommand('copy');
                            copySuccess();
                        } catch (err) {
                            prompt('Copy link:', urlToCopy);
                        }
                    }
                });
            }
        }

        // Global Escape listener for share modal
        document.addEventListener('keydown', function (e) {
            if ((e.key === 'Escape' || e.keyCode === 27) && shareModal && shareModal.classList.contains('is-active')) {
                closeShareModal();
            }
        });

        /**
         * Handle official social sharing per platform
         */
        function handleSocialShare(platform, url, title, image) {
            var shareUrl = '';
            var winWidth  = 620;
            var winHeight = 540;

            switch (platform) {
                case 'facebook':
                    // Official Facebook Share Dialog
                    // Facebook scrapes open graph tags from the actual URL.
                    // User manually prepares post and confirms by clicking "Post to Facebook".
                    shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
                    winWidth  = 620;
                    winHeight = 560;
                    break;

                case 'x':
                case 'twitter':
                    // Official X (Twitter) Web Intent
                    shareUrl = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent('Job opening: ' + title);
                    winWidth  = 600;
                    winHeight = 440;
                    break;

                case 'email':
                    // Native mail client
                    var emailSubject = encodeURIComponent('Job Opportunity: ' + title);
                    var emailBody    = encodeURIComponent('Check out this job opportunity:\n\n' + title + '\n' + url);
                    window.location.href = 'mailto:?subject=' + emailSubject + '&body=' + emailBody;
                    return;

                case 'reddit':
                    // Official Reddit submit
                    shareUrl = 'https://www.reddit.com/submit?url=' + encodeURIComponent(url) + '&title=' + encodeURIComponent(title);
                    winWidth  = 720;
                    winHeight = 620;
                    break;

                case 'pinterest':
                    // Official Pinterest pin builder
                    shareUrl = 'https://pinterest.com/pin/create/button/?url=' + encodeURIComponent(url) +
                               (image ? '&media=' + encodeURIComponent(image) : '') +
                               '&description=' + encodeURIComponent(title);
                    winWidth  = 750;
                    winHeight = 580;
                    break;

                case 'linkedin':
                    // Official LinkedIn offsite share
                    shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url);
                    winWidth  = 620;
                    winHeight = 600;
                    break;

                case 'whatsapp':
                    // Official WhatsApp web/mobile sharing
                    shareUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(title + ' - ' + url);
                    winWidth  = 650;
                    winHeight = 600;
                    break;

                default:
                    return;
            }

            if (shareUrl) {
                var left = Math.max(0, (screen.width - winWidth) / 2);
                var top  = Math.max(0, (screen.height - winHeight) / 2);
                var winOptions = 'width=' + winWidth + ',height=' + winHeight + ',top=' + top + ',left=' + left + ',toolbar=0,status=0,resizable=1,location=0,menubar=0';

                var newWin = window.open(shareUrl, 'pdsSocialShareWin', winOptions);
                if (!newWin || newWin.closed || typeof newWin.closed === 'undefined') {
                    // Fallback if popup blocker intercepted window.open()
                    window.open(shareUrl, '_blank', 'noopener,noreferrer');
                } else {
                    newWin.focus();
                }
            }
        }

        // =====================================================================
        // 2. APPLY JOB MODAL POPUP (MATCHING COMPANY PHOTOS LIGHTBOX)
        // =====================================================================
        var applyButtons        = document.querySelectorAll('.pds-job-detail-btn-apply, a[href="#pds-job-apply"]');
        var applyArea           = document.getElementById('pds-job-apply');
        var nameInput           = document.getElementById('pds_candidate_name');
        var appCard             = document.getElementById('pds-application-card');
        var lastFocusedApplyBtn = null;

        // Detach modal from article and attach directly to <body> to avoid inline rendering
        if (applyArea && applyArea.parentNode !== document.body) {
            document.body.appendChild(applyArea);
        }

        function getScrollbarWidth() {
            return window.innerWidth - document.documentElement.clientWidth;
        }

        function openApplicationModal(triggerBtn) {
            if (!applyArea) return;
            lastFocusedApplyBtn = triggerBtn || document.activeElement;

            // Lock background scrolling & prevent layout shift
            var sbWidth = getScrollbarWidth();
            if (sbWidth > 0) {
                document.body.style.paddingRight = sbWidth + 'px';
            }
            document.body.classList.add('pds-apply-modal-open');
            document.documentElement.classList.add('pds-apply-modal-open');

            // Open modal overlay
            applyArea.classList.add('is-active');
            applyArea.setAttribute('aria-hidden', 'false');
            applyButtons.forEach(function (btn) {
                btn.setAttribute('aria-expanded', 'true');
            });

            // Focus on first input for accessibility
            setTimeout(function () {
                if (nameInput) {
                    nameInput.focus();
                } else {
                    var closeBtn = applyArea.querySelector('.pds-apply-modal-close-btn');
                    if (closeBtn) closeBtn.focus();
                }
            }, 180);
        }

        function closeApplicationModal() {
            if (!applyArea || !applyArea.classList.contains('is-active')) return;

            // Hide modal overlay
            applyArea.classList.remove('is-active');
            applyArea.setAttribute('aria-hidden', 'true');
            applyButtons.forEach(function (btn) {
                btn.setAttribute('aria-expanded', 'false');
            });

            // Restore background scrolling
            document.body.classList.remove('pds-apply-modal-open');
            document.documentElement.classList.remove('pds-apply-modal-open');
            document.body.style.paddingRight = '';

            // Note: Candidate form data is preserved if closed and reopened!

            // Restore focus to trigger button
            if (lastFocusedApplyBtn && typeof lastFocusedApplyBtn.focus === 'function') {
                lastFocusedApplyBtn.focus();
            }
        }

        // Trigger buttons click & keyboard handlers
        applyButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                openApplicationModal(btn);
            });

            btn.addEventListener('keydown', function (e) {
                if (e.key === ' ' || e.keyCode === 32) {
                    e.preventDefault();
                    openApplicationModal(btn);
                }
            });
        });

        // Modal Close handlers
        var closeButtons = applyArea ? applyArea.querySelectorAll('[data-pds-apply-close], .pds-apply-modal-close-btn') : [];
        closeButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                closeApplicationModal();
            });
        });

        // Backdrop click to close
        var modalBackdrop = applyArea ? applyArea.querySelector('.pds-apply-modal-backdrop') : null;
        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', function (e) {
                e.preventDefault();
                closeApplicationModal();
            });
        }

        // Dialog background click outside card to close
        var modalDialog = applyArea ? applyArea.querySelector('.pds-apply-modal-dialog') : null;
        if (modalDialog) {
            modalDialog.addEventListener('click', function (e) {
                if (e.target === modalDialog) {
                    closeApplicationModal();
                }
            });
        }

        // Prevent clicks inside white form card from closing the modal (unless clicking close btn)
        if (appCard) {
            appCard.addEventListener('click', function (e) {
                if (!e.target.closest('[data-pds-apply-close]') && !e.target.closest('.pds-apply-modal-close-btn')) {
                    e.stopPropagation();
                }
            });
        }

        // Global Escape key handler for Apply modal
        document.addEventListener('keydown', function (e) {
            if (applyArea && applyArea.classList.contains('is-active')) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    e.preventDefault();
                    closeApplicationModal();
                }
            }
        });

        // =====================================================================
        // 3. APPLICATION FORM & CV UPLOAD HANDLING
        // =====================================================================
        var applyForm       = document.getElementById('pds-job-application-form');
        var cvInput         = document.getElementById('pds_candidate_cv');
        var cvDropzone      = document.getElementById('pds-cv-dropzone');
        var cvCard          = document.getElementById('pds-cv-selected-card');
        var cvFileNameEl    = document.getElementById('pds-cv-file-name');
        var cvFileSizeEl    = document.getElementById('pds-cv-file-size');
        var cvBadgeEl       = document.getElementById('pds-cv-ext-badge');
        var cvRemoveBtn     = document.getElementById('pds-cv-remove-btn');
        var submitBtn       = document.getElementById('pds-btn-submit-app');
        var alertError      = document.getElementById('pds-app-alert-error');
        var alertErrorMsg   = document.getElementById('pds-app-alert-error-msg');
        var alertSuccess    = document.getElementById('pds-app-alert-success');
        var alertSuccessMsg = document.getElementById('pds-app-alert-success-msg');

        var maxFileSize = 10 * 1024 * 1024; // 10MB
        var allowedExts = ['pdf', 'doc', 'docx'];

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function clearFieldErrors() {
            var errorMsgs = document.querySelectorAll('.pds-field-error');
            errorMsgs.forEach(function (el) {
                el.textContent = '';
                el.style.display = 'none';
            });
            var errorInputs = document.querySelectorAll('.has-error');
            errorInputs.forEach(function (el) {
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
                var nameInputEl  = document.getElementById('pds_candidate_name');
                var emailInputEl = document.getElementById('pds_candidate_email');
                var phoneInputEl = document.getElementById('pds_candidate_phone');

                var candidateNameVal  = nameInputEl ? nameInputEl.value.trim() : '';
                var candidateEmailVal = emailInputEl ? emailInputEl.value.trim() : '';
                var candidatePhoneVal = phoneInputEl ? phoneInputEl.value.trim() : '';
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
        // Company Photos Lightbox Modal
        // =====================================================================
        var lightbox = document.getElementById('pds-company-photos-lightbox');
        var photoWrappers = document.querySelectorAll('.pds-company-photo-wrap');

        if (photoWrappers.length > 0) {
            // Ensure lightbox modal markup exists
            if (!lightbox) {
                lightbox = document.createElement('div');
                lightbox.id = 'pds-company-photos-lightbox';
                lightbox.className = 'pds-lightbox-modal';
                lightbox.setAttribute('role', 'dialog');
                lightbox.setAttribute('aria-modal', 'true');
                lightbox.setAttribute('aria-hidden', 'true');
                lightbox.setAttribute('aria-label', 'Company Photo Lightbox');
                lightbox.innerHTML = [
                    '<div class="pds-lightbox-backdrop" data-pds-lightbox-close></div>',
                    '<div class="pds-lightbox-dialog">',
                    '  <button type="button" class="pds-lightbox-close-btn" aria-label="Close lightbox" data-pds-lightbox-close>',
                    '    <svg class="pds-lightbox-close-icon" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
                    '  </button>',
                    '  <button type="button" class="pds-lightbox-nav-btn pds-lightbox-prev-btn" aria-label="Previous photo" style="display: none;">',
                    '    <svg class="pds-lightbox-nav-icon" viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                    '  </button>',
                    '  <div class="pds-lightbox-content">',
                    '    <figure class="pds-lightbox-figure">',
                    '      <div class="pds-lightbox-image-wrap">',
                    '        <img class="pds-lightbox-img" src="" alt="Company photo preview">',
                    '      </div>',
                    '      <figcaption class="pds-lightbox-caption" style="display: none;">',
                    '        <span class="pds-lightbox-title"></span>',
                    '        <span class="pds-lightbox-counter"></span>',
                    '      </figcaption>',
                    '    </figure>',
                    '  </div>',
                    '  <button type="button" class="pds-lightbox-nav-btn pds-lightbox-next-btn" aria-label="Next photo" style="display: none;">',
                    '    <svg class="pds-lightbox-nav-icon" viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    '  </button>',
                    '</div>'
                ].join('');
                document.body.appendChild(lightbox);
            }

            var lightboxImg = lightbox.querySelector('.pds-lightbox-img');
            var lightboxFigure = lightbox.querySelector('.pds-lightbox-figure');
            var lightboxCaption = lightbox.querySelector('.pds-lightbox-caption');
            var lightboxTitle = lightbox.querySelector('.pds-lightbox-title');
            var lightboxCounter = lightbox.querySelector('.pds-lightbox-counter');
            var prevBtn = lightbox.querySelector('.pds-lightbox-prev-btn');
            var nextBtn = lightbox.querySelector('.pds-lightbox-next-btn');
            var closeBtn = lightbox.querySelector('.pds-lightbox-close-btn');

            var currentPhotos = [];
            var currentIndex = 0;
            var lastFocusedElement = null;

            function getScrollbarWidth() {
                return window.innerWidth - document.documentElement.clientWidth;
            }

            function openLightbox(photos, startIndex, triggerElement) {
                if (!photos || !photos.length) return;
                currentPhotos = photos;
                currentIndex = startIndex >= 0 && startIndex < photos.length ? startIndex : 0;
                lastFocusedElement = triggerElement || document.activeElement;

                // Disable background scrolling & prevent layout shift
                var scrollbarWidth = getScrollbarWidth();
                if (scrollbarWidth > 0) {
                    document.body.style.paddingRight = scrollbarWidth + 'px';
                }
                document.body.classList.add('pds-lightbox-open');
                document.documentElement.classList.add('pds-lightbox-open');

                lightbox.classList.add('is-active');
                lightbox.setAttribute('aria-hidden', 'false');

                renderPhoto();

                if (closeBtn) {
                    closeBtn.focus();
                }
            }

            function closeLightbox() {
                if (!lightbox.classList.contains('is-active')) return;

                lightbox.classList.remove('is-active');
                lightbox.setAttribute('aria-hidden', 'true');

                document.body.classList.remove('pds-lightbox-open');
                document.documentElement.classList.remove('pds-lightbox-open');
                document.body.style.paddingRight = '';

                if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                    lastFocusedElement.focus();
                }
            }

            function renderPhoto() {
                if (!currentPhotos.length) return;
                var item = currentPhotos[currentIndex];

                if (lightboxImg) {
                    lightboxImg.src = item.src;
                    lightboxImg.alt = item.caption || 'Company Photo';
                }

                // If multiple photos exist, display gallery navigation & counter
                if (currentPhotos.length > 1) {
                    if (prevBtn) prevBtn.style.display = 'flex';
                    if (nextBtn) nextBtn.style.display = 'flex';
                    if (lightboxCaption) lightboxCaption.style.display = 'flex';
                    if (lightboxTitle) lightboxTitle.textContent = item.caption || '';
                    if (lightboxCounter) {
                        lightboxCounter.textContent = (currentIndex + 1) + ' / ' + currentPhotos.length;
                    }
                } else {
                    // Gracefully hide navigation & counter for single-photo mode
                    if (prevBtn) prevBtn.style.display = 'none';
                    if (nextBtn) nextBtn.style.display = 'none';
                    if (lightboxCaption) {
                        if (item.caption && lightboxTitle) {
                            lightboxTitle.textContent = item.caption;
                            lightboxCounter.textContent = '';
                            lightboxCaption.style.display = 'flex';
                        } else {
                            lightboxCaption.style.display = 'none';
                        }
                    }
                }
            }

            function prevPhoto() {
                if (currentPhotos.length <= 1) return;
                currentIndex = (currentIndex - 1 + currentPhotos.length) % currentPhotos.length;
                renderPhoto();
            }

            function nextPhoto() {
                if (currentPhotos.length <= 1) return;
                currentIndex = (currentIndex + 1) % currentPhotos.length;
                renderPhoto();
            }

            // Bind click & keyboard event to all photo wrappers
            photoWrappers.forEach(function (wrap) {
                function triggerOpen(e) {
                    e.preventDefault();

                    var img = wrap.querySelector('.pds-company-photo-img') || wrap.querySelector('img');
                    var fullSrc = wrap.getAttribute('data-full-src');

                    if (!fullSrc && img) {
                        var rawSrc = img.currentSrc || img.getAttribute('src') || '';
                        // Strip thumbnail size suffix e.g. -600x450
                        fullSrc = rawSrc.replace(/-\d+x\d+(\.[a-zA-Z0-9]+)$/, '$1');
                    }

                    var caption = wrap.getAttribute('data-caption') || (img ? img.getAttribute('alt') : '') || 'Company Photo';
                    var galleryData = wrap.getAttribute('data-gallery');
                    var photos = [];

                    if (galleryData) {
                        try {
                            var parsed = JSON.parse(galleryData);
                            if (Array.isArray(parsed) && parsed.length > 0) {
                                photos = parsed.map(function (p) {
                                    if (typeof p === 'string') {
                                        return { src: p, caption: caption };
                                    }
                                    return {
                                        src: p.src || p.full || '',
                                        thumb: p.thumb || p.src || '',
                                        caption: p.caption || p.title || caption
                                    };
                                }).filter(function (p) {
                                    return !!p.src;
                                });
                            }
                        } catch (err) {
                            // Fallback to single photo
                        }
                    }

                    if (!photos.length && fullSrc) {
                        photos = [{ src: fullSrc, caption: caption }];
                    }

                    if (photos.length > 0) {
                        openLightbox(photos, 0, wrap);
                    }
                }

                wrap.addEventListener('click', triggerOpen);

                wrap.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ' || e.keyCode === 13 || e.keyCode === 32) {
                        triggerOpen(e);
                    }
                });
            });

            // Close button click
            if (closeBtn) {
                closeBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    closeLightbox();
                });
            }

            // Gallery navigation button clicks
            if (prevBtn) {
                prevBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    prevPhoto();
                });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    nextPhoto();
                });
            }

            // Click outside the image closes the modal
            lightbox.addEventListener('click', function (e) {
                // If clicked on close button or element with data-pds-lightbox-close
                if (e.target.closest('[data-pds-lightbox-close]')) {
                    closeLightbox();
                    return;
                }

                // If click is inside figure/image wrap or navigation buttons, do NOT close
                if (e.target.closest('.pds-lightbox-figure') || e.target.closest('.pds-lightbox-nav-btn')) {
                    return;
                }

                // Any other click outside the image closes modal
                closeLightbox();
            });

            // Prevent clicks inside figure from bubbling up to close modal
            if (lightboxFigure) {
                lightboxFigure.addEventListener('click', function (e) {
                    e.stopPropagation();
                });
            }

            // Keyboard events (Escape to close, Arrows for navigation)
            document.addEventListener('keydown', function (e) {
                if (!lightbox.classList.contains('is-active')) return;

                if (e.key === 'Escape' || e.keyCode === 27) {
                    e.preventDefault();
                    closeLightbox();
                } else if (e.key === 'ArrowLeft' || e.keyCode === 37) {
                    prevPhoto();
                } else if (e.key === 'ArrowRight' || e.keyCode === 39) {
                    nextPhoto();
                }
            });
        }
    });
})();
