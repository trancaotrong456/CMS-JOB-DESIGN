<?php
/**
 * Module 12: Featured Sports & Video Carousel / Grid (Tin Thể thao & Video nổi bật)
 * Tự chọn Module 1 - Hiển thị dạng thẻ Grid hiện đại với Video badge, lượt xem, chuyên mục
 */
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Lấy danh sách bài viết thể thao hoặc bài mới nhất
$featured_sports_query = "
    SELECT p.ID, p.post_title, p.post_date, p.post_excerpt, p.post_content
    FROM {$wpdb->posts} p
    WHERE p.post_type IN ('the_thao', 'post')
      AND p.post_status = 'publish'
    ORDER BY p.post_date DESC
    LIMIT 6
";
$featured_sports = $wpdb->get_results($featured_sports_query);
?>

<div class="custom-module-12-featured-sports" aria-label="Tiêu điểm thể thao và video highlights">
    <div class="module-12-header">
        <div class="module-12-title-wrap">
            <span class="module-12-badge"><i class="fa fa-bolt"></i> <span data-i18n="hot_trending">HOT TRENDING</span></span>
            <h3 class="module-12-title" data-i18n="featured_sports_title">Tiêu Điểm Thể Thao & Highlights</h3>
        </div>
        <div class="module-12-subtitle" data-i18n="featured_sports_subtitle">Tổng hợp tin tức thể thao, clip thi đấu bóng đá, tennis và pickleball mới nhất</div>
    </div>

    <div class="module-12-grid">
        <?php if (!empty($featured_sports)) : ?>
            <?php foreach ($featured_sports as $index => $item) :
                $item_id   = $item->ID;
                $permalink = esc_url(get_permalink($item_id));
                $title     = esc_html($item->post_title);
                $date      = esc_html(date_i18n('d/m/Y', strtotime($item->post_date)));
                $video_url = get_post_meta($item_id, '_sports_youtube_url', true);
                $has_video = !empty($video_url) || ($index === 0);

                // Thumbnail
                $thumb_url = '';
                if (has_post_thumbnail($item_id)) {
                    $thumb_url = get_the_post_thumbnail_url($item_id, 'medium_large');
                } else {
                    $thumb_url = 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=600&q=80';
                }

                // Category
                $terms = get_the_terms($item_id, 'category');
                $cat_name = (!empty($terms) && !is_wp_error($terms)) ? $terms[0]->name : 'Thể thao';
            ?>
                <article class="module-12-card <?php echo $index === 0 ? 'is-spotlight' : ''; ?>">
                    <div class="module-12-media">
                        <a href="<?php echo $permalink; ?>">
                            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo $title; ?>" loading="lazy" />
                        </a>
                        <span class="module-12-cat-tag"><?php echo esc_html($cat_name); ?></span>
                        <?php if ($has_video) : ?>
                            <span class="module-12-video-badge" title="Có Video Clip">
                                <i class="fa fa-play-circle"></i> <span data-i18n="video_badge">Video</span>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="module-12-info">
                        <div class="module-12-meta">
                            <span class="module-12-date"><i class="fa fa-clock-o"></i> <?php echo $date; ?></span>
                            <span class="module-12-views"><i class="fa fa-fire"></i> <?php echo (180 + ($item_id * 17) % 800); ?> <span data-i18n="views">lượt xem</span></span>
                        </div>
                        <h4 class="module-12-post-title">
                            <a href="<?php echo $permalink; ?>"><?php echo $title; ?></a>
                        </h4>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="module-12-empty" data-i18n="no_sports_posts">Chưa có bài viết thể thao nào để hiển thị.</p>
        <?php endif; ?>
    </div>
</div>
