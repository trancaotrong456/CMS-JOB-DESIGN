<?php
/**
 * Module 11: Archive / Xem nhiều 2 cột phong cách VNExpress
 * Vị trí: Sidebar / Shortcode [tdc_vnexpress_archive]
 */
if (!defined('ABSPATH')) {
    exit;
}

$count = isset($args['count']) ? (int) $args['count'] : 8;
$title = isset($args['title']) ? $args['title'] : 'Xem nhiều';

$popular_query = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $count,
    'orderby'        => 'comment_count date',
    'order'          => 'DESC',
));

$posts_array = $popular_query->posts;
$total = count($posts_array);
$half = ceil($total / 2);
$col1 = array_slice($posts_array, 0, $half);
$col2 = array_slice($posts_array, $half);
?>

<section class="widget tdc-module-11-widget">
    <div class="tdc-vnexpress-wrap">
        <div class="tdc-vne-header">
            <h3 class="tdc-vne-title" data-i18n="most_viewed_title"><?php echo esc_html($title); ?></h3>
        </div>

        <?php if (!empty($posts_array)) : ?>
            <div class="tdc-vne-grid">
                <!-- Cột 1 -->
                <div class="tdc-vne-col">
                    <?php foreach ($col1 as $idx => $p) : 
                        $num = $idx + 1;
                        $link = get_permalink($p->ID);
                        $p_title = get_the_title($p->ID);
                    ?>
                        <article class="tdc-vne-item">
                            <span class="tdc-vne-number"><?php echo esc_html($num); ?></span>
                            <div class="tdc-vne-content">
                                <a href="<?php echo esc_url($link); ?>" class="tdc-vne-link">
                                    <?php echo esc_html($p_title); ?>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Cột 2 -->
                <div class="tdc-vne-col">
                    <?php foreach ($col2 as $idx => $p) : 
                        $num = $half + $idx + 1;
                        $link = get_permalink($p->ID);
                        $p_title = get_the_title($p->ID);
                    ?>
                        <article class="tdc-vne-item">
                            <span class="tdc-vne-number"><?php echo esc_html($num); ?></span>
                            <div class="tdc-vne-content">
                                <a href="<?php echo esc_url($link); ?>" class="tdc-vne-link">
                                    <?php echo esc_html($p_title); ?>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else : ?>
            <p style="padding: 10px 0; margin: 0; font-size: 13px; color: #888;" data-i18n="no_archive_posts">Chưa có bài viết nào.</p>
        <?php endif; ?>
    </div>
</section>
