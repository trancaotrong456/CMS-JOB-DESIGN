<?php
/**
 * MODULE - LATEST NEWS (TIMELINE STYLE)
 * Hiển thị các bài viết mới nhất theo dòng sự kiện / timeline.
 * Vị trí: Single Post / Shortcode [tdc_latest_news]
 */
if (!defined('ABSPATH')) {
    exit;
}

$current_post_id = is_single() ? get_the_ID() : 0;

$latest_news_query = new WP_Query(array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 3,
    'post__not_in'        => $current_post_id ? array($current_post_id) : array(),
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
));

if ($latest_news_query->have_posts()) :
?>

<section class="latest-news tdc-latest-news-section" aria-label="Dòng sự kiện bài viết mới">
    <div class="latest-news-container">
        <h3 class="latest-news-title">
            <i class="fa fa-history"></i> <span data-i18n="latest_news_title">Dòng sự kiện mới nhất</span>
        </h3>

        <div class="latest-news-timeline">
            <?php while ($latest_news_query->have_posts()) : $latest_news_query->the_post();
                $excerpt = get_the_excerpt();
                if (empty($excerpt)) {
                    $excerpt = wp_strip_all_tags(get_the_content());
                }
                $excerpt = wp_trim_words($excerpt, 22, '...');
            ?>
                <article class="latest-news-item">
                    <!-- Timeline dot -->
                    <span class="latest-news-dot" aria-hidden="true"></span>

                    <div class="latest-news-content">
                        <div class="latest-news-header">
                            <h4 class="latest-news-post-heading">
                                <a class="latest-news-post-title" href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h4>
                            <time class="latest-news-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                <i class="fa fa-clock-o"></i> <?php echo esc_html(get_the_date('d/m/Y')); ?>
                            </time>
                        </div>

                        <?php if (!empty($excerpt)) : ?>
                            <div class="latest-news-excerpt">
                                <?php echo esc_html($excerpt); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php
endif;
wp_reset_postdata();
?>
