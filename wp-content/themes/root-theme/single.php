<?php
/**
 * The template for displaying all single posts
 * MODULE (6): Chi tiết bài viết (date nổi, nội dung, nguồn, cảm xúc)
 * MODULE (7): Prev - Next Post (phân số ngày/tháng 'yy)
 * MODULE (8/14): Full Comments với reply lồng nhau (comments.php)
 * MODULE (9): Categories (Cột trái)
 * MODULE (10): Recent post (Cột phải)
 * MODULE (13): Lịch thi đấu & kết quả (Cột phải)
 * MODULE (15): Latest News (Bootsnipp xrKXW)
 * Theme: Root Theme
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- READING PROGRESS BAR -->
<div class="tdc-reading-progress" aria-hidden="true">
    <span class="tdc-reading-progress__bar"></span>
</div>

<main class="single-page">
    <div class="single-page-wrapper">

        <!-- =========================================================
             HÀNG 3 CỘT (THEO ĐÚNG SƠ ĐỒ THIẾT KẾ ẢNH 1 - TRANG CHI TIẾT):
             Cột trái: Categories (9)
             Cột giữa: Detail (6)
             Cột phải: Recent post (10) + Lịch thi đấu (13)
        ========================================================== -->
        <div class="tdc-3cols-layout single-3cols-row">

            <!-- 1. CỘT TRÁI: CATEGORIES (MODULE 9) -->
            <aside class="tdc-col-left single-col-categories" role="complementary" aria-label="Chuyên mục bài viết">
                <?php root_theme_render_module_9_categories(); ?>
            </aside>

            <!-- 2. CỘT GIỮA: DETAIL (MODULE 6) -->
            <div class="tdc-col-center single-col-detail">
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

                            <!-- MODULE (6): POST HEADER với DATE BOX NỔI -->
                            <header class="single-post-header">
                                <div class="tdc-single-heading-content">
                                    <h1 class="single-post-title"><?php the_title(); ?></h1>
                                    <?php $reading_time = root_theme_get_reading_time(get_the_content()); ?>
                                    <div class="tdc-reading-meta" aria-label="Thời gian đọc dự kiến">
                                        <svg class="tdc-reading-meta__icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" focusable="false">
                                            <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2"></circle>
                                            <path d="M12 7v5l3 2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        <span>Đã đọc được <?php echo esc_html($reading_time); ?> phút</span>
                                    </div>
                                </div>

                                <!-- HÌNH TRÒN NGÀY THÁNG MÀU VÀNG (MODULE 6) -->
                                <div class="single-post-date-circle" aria-label="Ngày đăng bài">
                                    <div class="circle-date-left">
                                        <span class="circle-date-day"><?php echo get_the_date('d'); ?></span>
                                        <span class="circle-date-line"></span>
                                        <span class="circle-date-month"><?php echo get_the_date('m'); ?></span>
                                    </div>
                                    <div class="circle-date-year">'<?php echo get_the_date('y'); ?></div>
                                </div>
                            </header>

                            <!-- DIVIDER -->
                            <div class="single-divider"><span></span></div>

                            <!-- FEATURED IMAGE -->
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="single-post-thumbnail">
                                    <?php the_post_thumbnail('large'); ?>
                                </div>
                            <?php endif; ?>

                            <!-- POST CONTENT -->
                            <div class="single-post-content entry-content">
                                <?php the_content(); ?>
                            </div>

                            <!-- SOURCE -->
                            <div class="single-post-source">
                                <em data-i18n="source_label">(Theo Người Lao Động)</em>
                            </div>

                            <!-- BÌNH CHỌN CẢM XÚC -->
                            <?php root_theme_render_post_reactions(get_the_ID()); ?>

                        </article>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <!-- 3. CỘT PHẢI: RECENT POST (MODULE 10) + LỊCH THI ĐẤU (MODULE 13) -->
            <aside class="tdc-col-right single-col-recent" role="complementary" aria-label="Bài viết mới và tiện ích">
                <?php root_theme_render_module_10_recent_posts(5); ?>
                <?php root_theme_render_module_13_fixtures(); ?>
            </aside>

        </div><!-- /.single-3cols-row -->

        <!-- =========================================================
             HÀNG DƯỚI TOÀN CHIỀU RỘNG (THEO ĐÚNG SƠ ĐỒ THIẾT KẾ ẢNH 1):
             1. Prev - Next Post (7)
             2. Comments (8)
             3. Module 15 (Latest News timeline)
        ========================================================== -->
        <div class="single-bottom-fullwidth">

            <!-- MODULE (7): PREVIOUS / NEXT POST -->
            <?php
            global $wpdb;
            $current_post_date = get_the_date('Y-m-d H:i:s');
            $current_id        = get_the_ID();

            $prev_post_data = $wpdb->get_row($wpdb->prepare("
                SELECT ID, post_title, post_date
                FROM {$wpdb->posts}
                WHERE post_date < %s
                  AND post_type = 'post'
                  AND post_status = 'publish'
                ORDER BY post_date DESC
                LIMIT 1
            ", $current_post_date));

            $next_post_data = $wpdb->get_row($wpdb->prepare("
                SELECT ID, post_title, post_date
                FROM {$wpdb->posts}
                WHERE post_date > %s
                  AND post_type = 'post'
                  AND post_status = 'publish'
                ORDER BY post_date ASC
                LIMIT 1
            ", $current_post_date));

            $nav_posts = array();
            if ($prev_post_data) { $nav_posts[] = $prev_post_data; }
            if ($next_post_data) { $nav_posts[] = $next_post_data; }

            if (count($nav_posts) < 2) {
                $exclude_ids  = array_merge(array($current_id), array_column($nav_posts, 'ID'));
                $placeholders = implode(',', array_fill(0, count($exclude_ids), '%d'));
                $fallback_posts = $wpdb->get_results($wpdb->prepare("
                    SELECT ID, post_title, post_date
                    FROM {$wpdb->posts}
                    WHERE ID NOT IN ($placeholders)
                      AND post_type = 'post'
                      AND post_status = 'publish'
                    ORDER BY post_date DESC
                    LIMIT %d
                ", array_merge($exclude_ids, array(2 - count($nav_posts)))));

                if (!empty($fallback_posts)) {
                    foreach ($fallback_posts as $fb) {
                        $nav_posts[] = $fb;
                    }
                }
            }
            ?>

            <?php if (!empty($nav_posts)) : ?>
                <nav class="tdc-prev-next-container" aria-label="Bài viết trước và sau">
                    <?php foreach ($nav_posts as $p_item) :
                        $p_day   = date('d', strtotime($p_item->post_date));
                        $p_month = date('m', strtotime($p_item->post_date));
                        $p_year  = date('y', strtotime($p_item->post_date));
                    ?>
                        <div class="tdc-prev-next-row">
                            <div class="tdc-pn-date">
                                <div class="tdc-pn-fraction">
                                    <span class="tdc-pn-day"><?php echo esc_html($p_day); ?></span>
                                    <span class="tdc-pn-line"></span>
                                    <span class="tdc-pn-month"><?php echo esc_html($p_month); ?></span>
                                </div>
                                <span class="tdc-pn-year">'<?php echo esc_html($p_year); ?></span>
                            </div>
                            <div class="tdc-pn-content">
                                <a href="<?php echo esc_url(get_permalink($p_item->ID)); ?>" class="tdc-pn-title">
                                    <?php echo esc_html($p_item->post_title); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <!-- MODULE (15): LATEST NEWS TIMELINE -->
            <?php root_theme_render_module_15_latest_news(3, $current_id); ?>

            <!-- MODULE (8/14): COMMENTS -->
            <?php
            if (comments_open() || get_comments_number()) {
                comments_template();
            }
            ?>

        </div><!-- /.single-bottom-fullwidth -->

    </div><!-- /.single-page-wrapper -->
</main>

<!-- READING PROGRESS BAR SCRIPT -->
<script>
window.addEventListener('scroll', function () {
    var winScroll = document.documentElement.scrollTop || document.body.scrollTop;
    var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    var scrolled = height > 0 ? (winScroll / height) * 100 : 0;
    var progressBar = document.querySelector('.tdc-reading-progress__bar');
    if (progressBar) {
        progressBar.style.width = scrolled + '%';
    }
}, { passive: true });
</script>

<?php get_footer(); ?>
