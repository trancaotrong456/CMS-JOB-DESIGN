<?php
/**
 * Template for Home / Blog Index (Module 2: Latest Posts phong cách FIT TDC)
 * THIẾT KẾ THEO SƠ ĐỒ ẢNH 2 (TRANG CHỦ - 3 CỘT):
 * Cột trái: Archive (11) (nhóm 6 sv)
 * Cột giữa: Content (2) (nhóm 6 sv - danh sách bài viết)
 * Cột phải: Comments (12) (nhóm 6 sv) + Lịch thi đấu (13)
 * Chân trang: Footer (3)
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();

global $wpdb;

// Thiết lập phân trang
$paged = max(1, get_query_var('paged'), get_query_var('page'));
$posts_per_page = (int) get_option('posts_per_page', 6);
$offset = ($paged - 1) * $posts_per_page;

// 1. SQL: Đếm tổng số bài viết đã xuất bản
$total_posts = (int) $wpdb->get_var("
    SELECT COUNT(*)
    FROM {$wpdb->posts}
    WHERE post_type = 'post'
      AND post_status = 'publish'
");

// 2. SQL: Truy vấn danh sách bài viết theo phân trang
$latest_posts = $wpdb->get_results($wpdb->prepare("
    SELECT ID, post_title, post_date, post_content, post_excerpt
    FROM {$wpdb->posts}
    WHERE post_type = 'post'
      AND post_status = 'publish'
    ORDER BY post_date DESC
    LIMIT %d OFFSET %d
", $posts_per_page, $offset));

$max_pages = ceil($total_posts / $posts_per_page);
?>

<main class="home-page" role="main">
    <div class="home-page-wrapper">

        <!-- =========================================================
             MODULE MEDIA TABS: ẢNH | MEGASTORY | INFOGRAPHIC
             (Phong cách VnExpress - 4 ảnh mỗi tab, carousel)
        ========================================================== -->
        <?php root_theme_render_media_tabs(); ?>

        <!-- =========================================================
             HÀNG 3 CỘT (THEO ĐÚNG SƠ ĐỒ THIẾT KẾ ẢNH 2 - TRANG CHỦ):
             Cột trái: Archive (11)
             Cột giữa: Content (2)
             Cột phải: Comments (12) + Lịch thi đấu (13)
        ========================================================== -->
        <div class="tdc-3cols-layout home-3cols-row">


            <!-- 1. CỘT TRÁI: ARCHIVE (MODULE 11) (nhóm 6 sv) -->
            <aside class="tdc-col-left home-col-archive" role="complementary" aria-label="Archive và chuyên mục">
                <?php root_theme_render_module_11_archive(true); ?>
                <?php root_theme_render_module_9_categories(); ?>
            </aside>

            <!-- 2. CỘT GIỮA: CONTENT (MODULE 2) (nhóm 6 sv - danh sách bài viết) -->
            <section class="tdc-col-center home-col-content">

                <!-- MODULE TIÊU ĐIỂM THỂ THAO & VIDEO HIGHLIGHTS (NẾU Ở TRANG 1) -->
                <?php
                if ($paged <= 1) :
                    $featured_sports_query = "
                        SELECT p.ID, p.post_title, p.post_date, p.post_excerpt, p.post_content
                        FROM {$wpdb->posts} p
                        WHERE p.post_type IN ('the_thao', 'post')
                          AND p.post_status = 'publish'
                        ORDER BY p.post_date DESC
                        LIMIT 4
                    ";
                    $featured_sports = $wpdb->get_results($featured_sports_query);
                ?>
                <div class="custom-module-12-featured-sports" aria-label="Tiêu điểm thể thao">
                    <div class="module-12-header">
                        <div class="module-12-title-wrap">
                            <span class="module-12-badge"><i class="fa fa-bolt"></i> <span data-i18n="hot_trending">HOT</span></span>
                            <h3 class="module-12-title" data-i18n="featured_sports_title">Tiêu Điểm Thể Thao</h3>
                        </div>
                    </div>

                    <div class="module-12-grid">
                        <?php if (!empty($featured_sports)) : ?>
                            <?php foreach ($featured_sports as $index => $item) :
                                $item_id   = $item->ID;
                                $permalink = esc_url(get_permalink($item_id));
                                $title     = esc_html($item->post_title);
                                $date      = esc_html(date_i18n('d/m/Y', strtotime($item->post_date)));
                                $thumb_url = has_post_thumbnail($item_id)
                                    ? get_the_post_thumbnail_url($item_id, 'medium_large')
                                    : 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=600&q=80';
                                $terms = get_the_terms($item_id, 'category');
                                $cat_name = (!empty($terms) && !is_wp_error($terms)) ? $terms[0]->name : 'Thể thao';
                            ?>
                                <article class="module-12-card <?php echo $index === 0 ? 'is-spotlight' : ''; ?>">
                                    <div class="module-12-media">
                                        <a href="<?php echo $permalink; ?>">
                                            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo $title; ?>" loading="lazy" />
                                        </a>
                                        <span class="module-12-cat-tag"><?php echo esc_html($cat_name); ?></span>
                                    </div>
                                    <div class="module-12-info">
                                        <div class="module-12-meta">
                                            <span class="module-12-date"><i class="fa fa-clock-o"></i> <?php echo $date; ?></span>
                                        </div>
                                        <h4 class="module-12-post-title">
                                            <a href="<?php echo $permalink; ?>"><?php echo $title; ?></a>
                                        </h4>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- SECTION HEADING: LATEST POSTS -->
                <div class="section-heading tdc-section-heading">
                    <h2 class="section-heading-title">
                        <i class="fa fa-newspaper-o"></i> <span data-i18n="latest_posts">Latest Posts</span>
                    </h2>
                    <div class="section-heading-line"></div>
                </div>

                <?php if (!empty($latest_posts)) : ?>
                    <div class="news-list tdc-news-list">
                        <?php foreach ($latest_posts as $post_item) :
                            $post_obj   = get_post($post_item->ID);
                            $date       = $post_obj->post_date;
                            $post_day   = date("j", strtotime($date));
                            $post_month = date("m", strtotime($date));
                            $permalink  = esc_url(get_permalink($post_item->ID));
                            $excerpt    = !empty($post_item->post_excerpt) ? $post_item->post_excerpt : $post_item->post_content;
                        ?>
                            <article class="news-item tdc-content-post-card">
                                <!-- DATE (Module 2 FIT TDC) -->
                                <div class="news-date tdc-post-date-col">
                                    <span class="date-day"><?php echo esc_html($post_day); ?></span>
                                    <span class="date-month">THÁNG <?php echo esc_html($post_month); ?></span>
                                </div>

                                <!-- CONTENT (Module 2 FIT TDC) -->
                                <div class="news-content tdc-post-content-col">
                                    <h3 class="tdc-post-card-title">
                                        <a href="<?php echo $permalink; ?>">
                                            <?php echo esc_html($post_item->post_title); ?>
                                        </a>
                                    </h3>

                                    <p class="tdc-post-card-excerpt">
                                        <?php
                                        echo esc_html(
                                            wp_trim_words(
                                                wp_strip_all_tags($excerpt),
                                                30,
                                                '[...]'
                                            )
                                        );
                                        ?>
                                    </p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- PAGINATION -->
                    <?php if ($max_pages > 1) : ?>
                        <nav class="home-pagination tdc-pagination" aria-label="Phân trang bài viết">
                            <?php
                            echo paginate_links(array(
                                'total'     => $max_pages,
                                'current'   => $paged,
                                'mid_size'  => 2,
                                'prev_text' => '<i class="fa fa-angle-left"></i> Previous',
                                'next_text' => 'Next <i class="fa fa-angle-right"></i>',
                            ));
                            ?>
                        </nav>
                    <?php endif; ?>

                <?php else : ?>
                    <div class="no-posts tdc-empty-box">
                        <h2 data-i18n="no_posts_title">Chưa có bài viết nào.</h2>
                        <p data-i18n="no_posts_desc">Hiện tại chưa có bài viết nào được đăng tải trên hệ thống.</p>
                    </div>
                <?php endif; ?>

            </section>

            <!-- 3. CỘT PHẢI: COMMENTS (MODULE 12) + LỊCH THI ĐẤU (MODULE 13) (nhóm 6 sv) -->
            <aside class="tdc-col-right home-col-comments" role="complementary" aria-label="Bình luận và lịch thi đấu">
                <?php root_theme_render_module_12_comments(5); ?>
                <?php root_theme_render_module_13_fixtures(); ?>
                <?php root_theme_render_module_10_recent_posts(4); ?>
            </aside>

        </div><!-- /.home-3cols-row -->

    </div><!-- /.home-page-wrapper -->
</main>

<?php get_footer(); ?>
