<?php
/**
 * =============================================================================
 * TRANG CHI TIẾT TIN TỨC (NEWS DETAIL)
 * =============================================================================
 *
 * @package           JobScout
 * @subpackage        PDS / News Detail Single
 * @author            Nguyễn Hữu Chúc
 * @git_branch        nguyen-huu-chuc/news-detail
 * @ui_reference      docs/ui-reference/06-news-detail.png
 *
 * -----------------------------------------------------------------------------
 * 1. CHỨC NĂNG HOẠT ĐỘNG (FUNCTIONAL OVERVIEW):
 * -----------------------------------------------------------------------------
 * - Template Hierarchy: WordPress tự động sử dụng file này cho tất cả bài viết
 *   thuộc post type chuẩn `post` (single-post.php -> single.php).
 * - Bóc tách dữ liệu bài viết chi tiết:
 *   + Tiêu đề bài viết (get_the_title).
 *   + Ảnh bìa đại diện (Featured Image) cỡ lớn hoặc thumbnail chất lượng cao.
 *   + Thông tin tác giả, ngày đăng bài (định dạng ngày tháng chuẩn).
 *   + Danh mục (Categories) và thẻ (Tags) bài viết.
 *   + Nội dung đầy đủ của bài viết (the_content).
 * - Tính năng tương tác:
 *   + Nút "Share": Chia sẻ nhanh bài viết lên các mạng xã hội (Facebook, Twitter, LinkedIn,...).
 *   + Nút "Back to news": Điều hướng quay lại trang danh sách bài viết `/news`.
 *   + Sidebar bài viết mới nhất (Recent News / Related Posts): Truy vấn các bài viết liên quan
 *     hoặc bài viết mới nhất để tăng thời gian ở lại trang của độc giả.
 * - Widget Areas: Hỗ trợ 3 khu vực widget linh hoạt:
 *   + `pds-detail-breadcrumb` (thanh điều hướng breadcrumb tùy biến).
 *   + `pds-detail-summary` (khối tóm tắt bài viết trên cùng).
 *   + `pds-detail-content` (nội dung bổ trợ hoặc banner quảng cáo).
 *
 * -----------------------------------------------------------------------------
 * 2. GIAO DIỆN & BỐ CỤC UI (DESIGN SPECS):
 * -----------------------------------------------------------------------------
 * - Mẫu chuẩn: bám sát thiết kế tham chiếu docs/ui-reference/06-news-detail.png.
 * - Breadcrumb điều hướng: Home / News / News Detail với style tối giản, thanh lịch.
 * - Khối tóm tắt bài viết (Article Header):
 *   + Ảnh bìa đại diện bên trái hoặc trên cùng, bo góc nhẹ 4px, chất lượng sắc nét.
 *   + Khối tiêu đề bài viết: Font chữ lớn 26px - 32px, đậm nét, màu #111111.
 *   + Meta bài viết: Ngày đăng, tác giả, chuyên mục xếp theo hàng ngang tinh tế.
 *   + Cụm nút tương tác: Nút Share viền mỏng và nút Back to news tiện lợi.
 * - Bố cục nội dung 2 cột:
 *   + Cột chính (Nội dung bài viết): Typography rõ ràng, dễ đọc, căn lề chuẩn, khoảng cách
 *     giữa các đoạn văn (line-height: 1.8), ảnh chèn trong bài có caption đẹp mắt.
 *   + Cột phụ (Sidebar - nếu có): Danh sách "Recent News" với thumbnail nhỏ và tiêu đề ngắn gọn.
 * - Khối bài viết liên quan dưới cùng ("NEWEST BLOG ENTRIES"):
 *   + Lưới 4 card bài viết liên quan dạng thumbnail nhỏ kèm tiêu đề và ngày đăng.
 * - Cuối trang: Tích hợp Newsletter đăng ký email và Footer.
 * =============================================================================
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wp_enqueue_style(
    'pds-news-detail',
    get_template_directory_uri() . '/assets/css/news-detail.css',
    array(),
    '1.0.1'
);

get_header();
?>
<div class="pds-news-detail-page">
    <div class="pds-news-detail-container">
        <?php while ( have_posts() ) : the_post();
            $current_post_id = get_the_ID();
            $categories      = get_the_category( $current_post_id );
            $category_ids    = wp_list_pluck( $categories, 'term_id' );
            $news_page       = get_page_by_path( 'news' );
            $news_url        = $news_page ? get_permalink( $news_page ) : home_url( '/news/' );
            $share_url       = get_permalink( $current_post_id );
        ?>

        <?php if ( is_active_sidebar( 'pds-detail-breadcrumb' ) ) : ?>
            <div class="pds-news-detail-breadcrumb pds-detail-widget-area">
                <?php dynamic_sidebar( 'pds-detail-breadcrumb' ); ?>
            </div>
        <?php else : ?>
        <nav class="pds-news-detail-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
            <span aria-hidden="true">/</span>
            <a href="<?php echo esc_url( $news_url ); ?>">News</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">News Detail</span>
        </nav>

        <?php endif; ?>

        <article <?php post_class( 'pds-news-detail-article' ); ?>>
            <?php if ( is_active_sidebar( 'pds-detail-summary' ) ) : ?>
                <div class="pds-detail-summary-widget-area">
                    <?php dynamic_sidebar( 'pds-detail-summary' ); ?>
                </div>
            <?php else : ?>
            <header class="pds-news-detail-summary">
                <div class="pds-news-detail-cover">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'medium', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
                    <?php else : ?>
                        <span class="pds-news-detail-placeholder">PDS NEWS</span>
                    <?php endif; ?>
                </div>

                <div class="pds-news-detail-summary-text">
                    <h1><?php echo esc_html( get_the_title() ); ?></h1>
                    <p class="pds-news-detail-date">Published: <?php echo esc_html( get_the_date( 'M d, Y' ) ); ?></p>
                    <div class="pds-news-detail-tags">
                        <span><?php echo esc_html( get_the_author() ); ?></span>
                        <?php foreach ( array_slice( $categories, 0, 2 ) as $category ) : ?>
                            <span><?php echo esc_html( $category->name ); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pds-news-detail-actions">
                    <button type="button" class="pds-news-detail-share" data-share-url="<?php echo esc_url( $share_url ); ?>">SHARE</button>
                    <a class="pds-news-detail-back" href="<?php echo esc_url( $news_url ); ?>">BACK TO NEWS</a>
                </div>
            </header>
            <?php endif; ?>

            <div class="pds-news-detail-columns">
                <div class="pds-news-detail-content">
                    <?php if ( is_active_sidebar( 'pds-detail-before-article' ) ) : ?>
                        <div class="pds-detail-before-article pds-detail-widget-area">
                            <?php dynamic_sidebar( 'pds-detail-before-article' ); ?>
                        </div>
                    <?php else : ?>
                        <h2>Article Overview</h2>
                    <?php endif; ?>

                    <div class="pds-news-detail-entry">
                        <?php if ( is_active_sidebar( 'pds-detail-article-content' ) ) : ?>
                            <?php dynamic_sidebar( 'pds-detail-article-content' ); ?>
                        <?php else : ?>
                        <?php
                        the_content();
                        wp_link_pages( array(
                            'before' => '<div class="pds-news-detail-page-links">Pages: ',
                            'after'  => '</div>',
                        ) );
                        ?>
                        <?php endif; ?>
                    </div>

                    <?php if ( is_active_sidebar( 'pds-detail-after-article' ) ) : ?>
                        <div class="pds-detail-after-article pds-detail-widget-area">
                            <?php dynamic_sidebar( 'pds-detail-after-article' ); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="pds-news-detail-sidebar" aria-label="News sidebar">

                    <?php if ( is_active_sidebar( 'pds-detail-article-info' ) ) : ?>
                        <div class="pds-detail-widget-area pds-news-detail-side-card">
                            <?php dynamic_sidebar( 'pds-detail-article-info' ); ?>
                        </div>
                    <?php else : ?>
                    <section class="pds-news-detail-side-card">
                        <h2>Article Information</h2>
                        <dl>
                            <dt>Published</dt>
                            <dd><?php echo esc_html( get_the_date( 'M d, Y' ) ); ?></dd>
                            <dt>Author</dt>
                            <dd><?php echo esc_html( get_the_author() ); ?></dd>
                            <dt>Category</dt>
                            <dd><?php echo esc_html( $categories ? implode( ', ', wp_list_pluck( $categories, 'name' ) ) : 'News' ); ?></dd>
                        </dl>
                    </section>
                    <?php endif; ?>

                    <?php if ( is_active_sidebar( 'pds-detail-recent-news' ) ) : ?>
                        <div class="pds-detail-widget-area pds-news-detail-side-card">
                            <?php dynamic_sidebar( 'pds-detail-recent-news' ); ?>
                        </div>
                    <?php else : ?>
                    <section class="pds-news-detail-side-card">
                        <h2>Recent News</h2>
                        <?php
                        $recent_news = new WP_Query( array(
                            'post_type'           => 'post',
                            'post_status'         => 'publish',
                            'posts_per_page'      => 3,
                            'post__not_in'        => array( $current_post_id ),
                            'ignore_sticky_posts' => true,
                            'no_found_rows'       => true,
                        ) );
                        ?>
                        <?php if ( $recent_news->have_posts() ) : ?>
                            <div class="pds-news-detail-recent-list">
                                <?php while ( $recent_news->have_posts() ) : $recent_news->the_post(); ?>
                                    <a class="pds-news-detail-recent-item" href="<?php echo esc_url( get_permalink() ); ?>">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <?php the_post_thumbnail( 'thumbnail' ); ?>
                                        <?php else : ?>
                                            <span class="pds-news-detail-mini-placeholder">NEWS</span>
                                        <?php endif; ?>
                                        <span><?php echo esc_html( get_the_title() ); ?></span>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                        <?php else : ?>
                            <p>No recent articles.</p>
                        <?php endif; ?>
                        <?php wp_reset_postdata(); ?>
                    </section>
                    <?php endif; ?>
                </aside>
            </div>
        </article>

        <?php
        $related_args = array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => 6,
            'post__not_in'        => array( $current_post_id ),
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        );
        if ( $category_ids ) {
            $related_args['category__in'] = $category_ids;
        }
        $related       = new WP_Query( $related_args );
        $related_posts = $related->posts;
        $related_ids   = wp_list_pluck( $related_posts, 'ID' );

        if ( count( $related_posts ) < 6 ) {
            $extra = new WP_Query( array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => 6 - count( $related_posts ),
                'post__not_in'        => array_merge( array( $current_post_id ), $related_ids ),
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            ) );
            $related_posts = array_merge( $related_posts, $extra->posts );
        }
        ?>

        <?php if ( $related_posts || is_active_sidebar( 'pds-detail-related-content' ) ) : ?>
            <section class="pds-news-detail-related" aria-label="Related news">
                <?php if ( is_active_sidebar( 'pds-detail-before-related' ) ) : ?>
                    <div class="pds-detail-before-related pds-detail-widget-area">
                        <?php dynamic_sidebar( 'pds-detail-before-related' ); ?>
                    </div>
                <?php else : ?>
                    <h2 id="pds-news-detail-related-title">NEWEST BLOG ENTRIES</h2>
                <?php endif; ?>

                <?php if ( is_active_sidebar( 'pds-detail-related-content' ) ) : ?>
                    <div class="pds-detail-widget-area">
                        <?php dynamic_sidebar( 'pds-detail-related-content' ); ?>
                    </div>
                <?php else : ?>
                <div class="pds-news-detail-related-grid">
                    <?php foreach ( $related_posts as $related_post ) :
                        $related_id         = $related_post->ID;
                        $related_categories = get_the_category( $related_id );
                    ?>
                        <article class="pds-news-detail-related-card">
                            <a class="pds-news-detail-related-image" href="<?php echo esc_url( get_permalink( $related_id ) ); ?>">
                                <?php if ( has_post_thumbnail( $related_id ) ) : ?>
                                    <?php echo get_the_post_thumbnail( $related_id, 'thumbnail', array( 'alt' => esc_attr( get_the_title( $related_id ) ) ) ); ?>
                                <?php else : ?>
                                    <span class="pds-news-detail-placeholder">NEWS</span>
                                <?php endif; ?>
                            </a>
                            <div class="pds-news-detail-related-body">
                                <h3><a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>"><?php echo esc_html( get_the_title( $related_id ) ); ?></a></h3>
                                <p class="pds-news-detail-related-date">Published: <?php echo esc_html( get_the_date( 'M d, Y', $related_id ) ); ?></p>
                                <p class="pds-news-detail-related-meta"><?php echo esc_html( $related_categories ? $related_categories[0]->name : 'News' ); ?></p>
                                <p class="pds-news-detail-related-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $related_id ), 18, '…' ) ); ?></p>
                                <a class="pds-news-read-more" href="<?php echo esc_url( get_permalink( $related_id ) ); ?>">Read More</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
        <?php endwhile; ?>
    </div>
</div>

<!-- NEWSLETTER BANNER: unchanged -->
<?php get_template_part( 'template-parts/pds-newsletter' ); ?>

<script>
(function () {
    var btn = document.querySelector('.pds-news-detail-share');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var url = btn.getAttribute('data-share-url');
        if (navigator.share) {
            navigator.share({ title: document.title, url: url }).catch(function () {});
        } else if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(function () {
                btn.textContent = 'COPIED';
            }).catch(function () { window.prompt('Copy article URL:', url); });
        } else {
            window.prompt('Copy article URL:', url);
        }
    });
})();
</script>
<?php get_footer(); ?>
