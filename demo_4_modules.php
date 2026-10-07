<?php
/**
 * Trang xem trước và kiểm tra trực tiếp 4 module:
 * (8) Comments: Form Make a Post (Bootsnipp)
 * (9) Categories: Danh mục phong cách FIT TDC
 * (10) Recent Post: 10 bài viết mới nhất phong cách FIT TDC
 * (11) Archive: Bài viết lưu trữ 2 cột phong cách VnExpress
 * 
 * Xem tại: http://localhost/TranCaoTrong_CMS/demo_4_modules.php
 */

require_once __DIR__ . '/wp-load.php';

// Tự động giả lập đăng nhập user trancaotrong hoặc user1 nếu chưa đăng nhập để test Module (8)
if ( ! is_user_logged_in() ) {
    $admin = get_user_by( 'login', 'trancaotrong' );
    if ( ! $admin ) {
        $admin = get_user_by( 'login', 'user1' );
    }
    if ( $admin ) {
        wp_set_current_user( $admin->ID, $admin->user_login );
        wp_set_auth_cookie( $admin->ID );
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo 4 Modules Giao Diện - Khoa CNTT TDC</title>
    <?php wp_head(); ?>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 30px 15px;
            color: #333;
        }
        .demo-container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .demo-header {
            text-align: center;
            margin-bottom: 35px;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .demo-header h1 {
            margin: 0 0 10px 0;
            color: #1e3a8a;
            font-size: 26px;
        }
        .demo-header p {
            margin: 0;
            color: #64748b;
            font-size: 15px;
        }
        .demo-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }
        @media (max-width: 768px) {
            .demo-grid-2 {
                grid-template-columns: 1fr;
            }
        }
        .demo-section {
            background: #ffffff;
            border-radius: 8px;
            padding: 22px;
            margin-bottom: 25px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .demo-section-title {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 18px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .badge-done {
            background-color: #10b981;
            color: #fff;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="demo-container">
    <div class="demo-header">
        <h1>BÁO CÁO THỰC HIỆN 4 MODULES THEO YÊU CẦU</h1>
        <p>Hệ thống quản trị nội dung (TranCaoTrong_CMS) - Khoa CNTT TDC</p>
    </div>

    <!-- MODULE 8 -->
    <div class="demo-section">
        <div class="demo-section-title">
            <span>(8) Module Comments - Form "Make a Post" (Bootsnipp) khi đã Login</span>
            <span class="badge-done">Hoàn thành</span>
        </div>
        <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">
            Đang hiển thị ở trạng thái đã đăng nhập (User: <strong><?php echo wp_get_current_user()->user_login; ?></strong>).
        </p>
        
        <form action="<?php echo site_url('/wp-comments-post.php'); ?>" method="post" class="tdc-make-post-form">
            <div class="tdc-make-a-post-card">
                <div class="tdc-make-a-post-tab">Make a Post</div>
                <div class="tdc-make-a-post-body">
                    <textarea name="comment" class="tdc-make-a-post-textarea" placeholder="What are you thinking..."></textarea>
                </div>
            </div>
            <div class="tdc-make-a-post-footer">
                <button type="submit" class="tdc-make-post-share-btn">share</button>
            </div>
        </form>
    </div>

    <!-- HÀNG 2 CỘT: MODULE 9 VÀ MODULE 10 -->
    <div class="demo-grid-2">
        <!-- MODULE 9 -->
        <div class="demo-section" style="margin-bottom: 0;">
            <div class="demo-section-title">
                <span>(9) Module Categories (Phong cách FIT TDC)</span>
                <span class="badge-done">Hoàn thành</span>
            </div>
            <?php the_widget( 'TDC_FIT_Categories_Widget' ); ?>
        </div>

        <!-- MODULE 10 -->
        <div class="demo-section" style="margin-bottom: 0;">
            <div class="demo-section-title">
                <span>(10) Module Recent Post 10 bài (FIT TDC)</span>
                <span class="badge-done">Hoàn thành</span>
            </div>
            <?php the_widget( 'TDC_FIT_Recent_Posts_Widget', array( 'count' => 10, 'title' => 'Bài viết mới nhất' ) ); ?>
        </div>
    </div>

    <!-- MODULE 11 -->
    <div class="demo-section">
        <div class="demo-section-title">
            <span>(11) Module Archive / Xem nhiều 2 cột (Phong cách VnExpress)</span>
            <span class="badge-done">Hoàn thành</span>
        </div>
        <p style="font-size: 14px; color: #64748b; margin-bottom: 15px;">
            Hiển thị 8 bài viết mới nhất chia thành 2 cột đều nhau (Cột 1: 1 - 4, Cột 2: 5 - 8) với số thứ tự phóng to đậm nét.
        </p>
        <?php the_widget( 'TDC_VnExpress_Archive_Widget', array( 'count' => 8, 'title' => 'Xem nhiều' ) ); ?>
    </div>

    <!-- THÔNG TIN VỀ FOOTER #3 VÀ FOOTER #4 -->
    <div class="demo-section">
        <div class="demo-section-title">
            <span>Footer #1, #2, #3, #4 & Giải pháp Widget WP 7.*</span>
            <span class="badge-done">Đã thiết lập</span>
        </div>
        <ul style="line-height: 1.8; color: #334155;">
            <li><strong>Giải pháp khi WP bỏ Widget truyền thống:</strong> Đã tích hợp bộ lọc <code>add_filter('use_widgets_block_editor', '__return_false');</code> để khôi phục trình quản lý Widget trực quan và đầy đủ, đồng thời cung cấp các <strong>Shortcode</strong> và <strong>Custom Widgets</strong> để sử dụng linh hoạt trong cả FSE Block Editor lẫn Classic.</li>
            <li><strong>Footer #1, Footer #2:</strong> Đã sẵn có trong theme Astra (<code>footer-widget-1</code>, <code>footer-widget-2</code>).</li>
            <li><strong>Footer #3, Footer #4:</strong> Đã đăng ký thành công qua hàm <code>register_sidebar()</code> với ID <code>footer-3</code> và <code>footer-4</code>.</li>
            <li><strong>Hiển thị ra ngoài giao diện:</strong> Đã gắn tự động qua hook giao diện chân trang (<code>astra_footer_before</code>) dạng lưới 4 cột đáp ứng (Grid layout).</li>
        </ul>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
