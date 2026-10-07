<?php
/**
 * Module 10: 10 Bài viết mới nhất phong cách FIT TDC
 * Vị trí: Sidebar / Shortcode [tdc_recent_posts]
 */
if (!defined('ABSPATH')) {
    exit;
}

$count     = isset($args['count']) ? (int) $args['count'] : 5;
$title     = isset($args['title']) ? $args['title'] : 'Bài viết mới nhất';
$more_link = isset($args['more_link']) ? $args['more_link'] : home_url('/');

$recent_query = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $count,
    'orderby'        => 'date',
    'order'          => 'DESC',
));
?>

<section class="widget tdc-module-10-widget">
    <div class="tdc-fit-recent-box">
        <div class="tdc-fit-recent-header" data-i18n="recent_posts_title">
            <?php echo esc_html($title); ?>
        </div>

        <div class="tdc-fit-recent-list">
            <?php if ($recent_query->have_posts()) : ?>
                <?php while ($recent_query->have_posts()) : $recent_query->the_post();
                    $day   = get_the_date('d');
                    $month = get_the_date('m');
                    $year  = get_the_date('y');
                ?>
                    <article class="tdc-fit-post-item">
                        <div class="tdc-fit-date-box">
                            <div class="tdc-fit-date-top">
                                <span class="tdc-fit-day"><?php echo esc_html($day); ?></span>
                                <span class="tdc-fit-dash">-</span>
                                <span class="tdc-fit-year"><?php echo esc_html($year); ?></span>
                            </div>
                            <div class="tdc-fit-date-bottom">
                                <span class="tdc-fit-month">T<?php echo esc_html($month); ?></span>
                            </div>
                        </div>
                        <div class="tdc-fit-title-wrap">
                            <a href="<?php the_permalink(); ?>" class="tdc-fit-post-title">
                                <?php the_title(); ?>
                            </a>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <p style="padding: 10px; margin: 0; font-size: 13px;" data-i18n="no_recent_posts">Chưa có bài viết nào.</p>
            <?php endif; ?>
        </div>

        <div class="tdc-fit-recent-footer">
            <a href="<?php echo esc_url($more_link); ?>" class="tdc-fit-all-news-btn">
                <span data-i18n="view_all_news">XEM TẤT CẢ TIN TỨC</span> <i class="fa fa-angle-right"></i>
            </a>
        </div>
    </div>
</section>
