<?php
/**
 * =============================================================================
 * TRANG CHỦ (HOME PAGE) - ROUTER CHÍNH
 * =============================================================================
 * - Người phụ trách: Trần Cao Trọng (Project Lead)
 * - Nhánh Git:       tran-cao-trong/home
 * - Phụ trách:       Trang chủ (Home) + Header / Footer dùng chung / Design Tokens
 * - Mẫu tham chiếu:  docs/ui-reference/01-home.png
 *
 * [CHỨC NĂNG HOẠT ĐỘNG]:
 * - Là tệp đích hàng đầu theo WordPress Template Hierarchy khi người dùng truy cập domain gốc.
 * - Kiểm tra cấu hình 'show_on_front' trong Settings -> Reading:
 *   + Nếu đặt là 'posts': Tự động gọi get_home_template() để hiển thị danh sách bài viết blog.
 *   + Nếu đặt là static page (mặc định): Tải khung Header chung, gọi template-parts/home-content.php
 *     chứa toàn bộ nội dung Trang chủ (Hero search, Top Jobs, Career CTA, Blog entries) và kết thúc bằng Footer.
 *
 * [GIAO DIỆN & BỐ CỤC (UI AESTHETICS)]:
 * - Khung hiển thị chuẩn 1440px căn giữa, nền xám nhạt cao cấp (#f7f5f2 / #fbf9f7).
 * - Bao bọc bởi thanh Header trắng cố định ở trên và Newsletter cam cùng Footer bản quyền ở chân trang.
 * =============================================================================
 *
 * @package JobScout
 */

if ( 'posts' === get_option( 'show_on_front' ) ) {
    // Giữ nguyên luồng xử lý posts index của WordPress nếu cấu hình đọc tin tức
    include( get_home_template() );
} else {
    get_header();
    get_template_part( 'template-parts/home', 'content' );
    get_footer();
}
