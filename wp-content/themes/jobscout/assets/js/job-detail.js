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
