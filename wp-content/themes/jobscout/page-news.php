<?php
/**
 * =============================================================================
 * TRANG TIN TỨC (PDS NEWS / BLOG LISTING)
 * =============================================================================
 *
 * @package           JobScout
 * @subpackage        PDS / News & Blog
 * @author            Nguyễn Hữu Chúc
 * @git_branch        nguyen-huu-chuc/news
 * @ui_reference      docs/ui-reference/03-news.png
 *
 * -----------------------------------------------------------------------------
 * 1. CHỨC NĂNG HOẠT ĐỘNG (FUNCTIONAL OVERVIEW):
 * -----------------------------------------------------------------------------
 * - Template Name: News (gán cho trang có slug "/news" hoặc trang chỉ định mẫu News).
 * - Cơ chế truy vấn (WP_Query):
 *   + Lấy 8 bài viết mới nhất thuộc post type `post` ở trạng thái đã xuất bản (`publish`).
 *   + Sắp xếp giảm dần theo thời gian đăng (`orderby => date`, `order => DESC`).
 *   + Bỏ qua sticky post (`ignore_sticky_posts => true`) và tối ưu hiệu năng với `no_found_rows => true`.
 * - Hỗ trợ Sidebar Widget:
 *   + Khối Hero Banner có hook kiểm tra `is_active_sidebar('news-hero-banner')`.
 *   + Khối Grid bài viết có hook kiểm tra `is_active_sidebar('news-grid-content')`.
 *   + Nếu không có widget, fallback hiển thị trực tiếp danh sách bài viết từ database.
 * - Điều hướng bài viết: Mỗi thẻ bài viết trỏ trực tiếp đến URL chi tiết `single-post.php`.
 *
 * -----------------------------------------------------------------------------
 * 2. GIAO DIỆN & BỐ CỤC UI (DESIGN SPECS):
 * -----------------------------------------------------------------------------
 * - Mẫu chuẩn: bám sát thiết kế tham chiếu docs/ui-reference/03-news.png.
 * - Hero Banner:
 *   + Khung nền xám tối hoặc ảnh chụp, chữ in hoa "PDS NEWS" màu trắng nổi bật, căn giữa,
 *     font chữ đậm nét, chiều cao banner chuẩn 240px - 280px.
 * - Tiêu đề chính mục tin tức:
 *   + Dòng chữ "NEWEST BLOG ENTRIES", font in hoa thanh lịch, màu #111111, căn giữa trên cùng.
 * - Lưới bài viết (pds-news-grid):
 *   + Bố cục 2 cột (2 columns layout) cân đối trên màn hình Desktop, chuyển về 1 cột trên Mobile.
 *   + Mỗi bài viết (pds-news-card):
 *     * Ảnh đại diện (Featured Image): Tỷ lệ khung hình chữ nhật ngang, hiệu ứng zoom nhẹ khi hover.
 *     * Ngày đăng bài: Font nhỏ, màu chữ xám, căn chỉnh phía trên tiêu đề.
 *     * Tiêu đề bài viết: Màu đen #111, hover sang màu cam #f2761b, font size 18px.
 *     * Đoạn tóm tắt (Excerpt): Giới hạn độ dài, câu từ súc tích, màu chữ #666.
 *     * Liên kết xem tiếp: Nút hoặc dòng "READ MORE" màu cam kèm mũi tên điều hướng.
 * - Form Newsletter: Tích hợp cuối trang trước khi kết thúc bằng Footer.
 * =============================================================================
 */

get_header();

$paged = max(
    1,
    (int) get_query_var('paged'),
    (int) get_query_var('page')
);

$news_query = new WP_Query([
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 8,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
]);
?>

<div class="pds-news-page">

    <!-- NEWS HERO -->
    <?php if ( is_active_sidebar( 'news-hero-banner' ) ) : ?>
        <?php dynamic_sidebar( 'news-hero-banner' ); ?>
    <?php else : ?>
        <section class="pds-news-hero" aria-labelledby="pds-news-hero-title">
            <h1 id="pds-news-hero-title">PDS NEWS</h1>
        </section>
    <?php endif; ?>

    <!-- NEWS CONTENT -->
    <?php if ( is_active_sidebar( 'news-grid-content' ) ) : ?>
        <main class="pds-news-main pds-widget-area-section">
            <div class="pds-news-container">
                <?php dynamic_sidebar( 'news-grid-content' ); ?>
            </div>
        </main>
    <?php else : ?>
    <main class="pds-news-main">

        <div class="pds-news-container">

            <h2 class="pds-news-heading">
                NEWEST BLOG ENTRIES
            </h2>

            <?php if ($news_query->have_posts()) : ?>

                <div class="pds-news-grid">

                    <?php while ($news_query->have_posts()) :
                        $news_query->the_post();
                    ?>

                        <article class="pds-news-card">

                            <!-- Featured Image -->
                            <a
                                class="pds-news-card-image"
                                href="<?php echo esc_url(get_permalink()); ?>"
                                aria-label="<?php echo esc_attr(get_the_title()); ?>"
                            >

                                <?php if (has_post_thumbnail()) : ?>

                                    <?php the_post_thumbnail(
                                        'medium_large',
                                        ['class' => 'pds-news-thumbnail']
                                    ); ?>

                                <?php else : ?>

                                    <img
                                        src="<?php echo esc_url(
                                            get_template_directory_uri()
                                            . '/images/banner-image.jpg'
                                        ); ?>"
                                        alt=""
                                        loading="lazy"
                                    >

                                <?php endif; ?>

                            </a>

                            <!-- Card Content -->
                            <div class="pds-news-card-content">

                                <h3 class="pds-news-card-title">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                        <?php echo esc_html(get_the_title()); ?>
                                    </a>
                                </h3>

                                <p class="pds-news-card-excerpt">
                                    <?php
                                    echo esc_html(
                                        wp_trim_words(
                                            get_the_excerpt(),
                                            24,
                                            '...'
                                        )
                                    );
                                    ?>
                                </p>

                                <a
                                    class="pds-news-read-more"
                                    href="<?php echo esc_url(get_permalink()); ?>"
                                >
                                    <?php esc_html_e('Read More', 'jobscout'); ?>
                                </a>

                            </div>

                        </article>

                    <?php endwhile; ?>

                </div>


            <?php else : ?>

                <p class="pds-news-empty">
                    <?php esc_html_e(
                        'No news articles have been published yet.',
                        'jobscout'
                    ); ?>
                </p>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        </div>

    </main>
    <?php endif; ?>

    <!-- NEWSLETTER BANNER -->
    <?php get_template_part('template-parts/pds-newsletter'); ?>

</div>

<?php get_footer(); ?>
