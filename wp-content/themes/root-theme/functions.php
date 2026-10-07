<?php
/**
 * Root Theme Functions and definitions
 * Theme Name: Root Theme
 * Author: Group C & D
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Setup Theme Supports
 */
function root_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'root_theme_setup');

/**
 * 2. Nạp Stylesheet & Google Fonts & Font Awesome
 */
function root_theme_enqueue_scripts() {
    // Google Fonts: Inter
    wp_enqueue_style(
        'root-theme-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap',
        array(),
        null
    );

    // Font Awesome 4.7
    wp_enqueue_style(
        'font-awesome-4',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
        array(),
        '4.7.0'
    );

    // Theme Main Stylesheet
    wp_enqueue_style(
        'group-c-style',
        get_stylesheet_uri(),
        array('font-awesome-4', 'root-theme-fonts'),
        filemtime(get_template_directory() . '/style.css')
    );
}
add_action('wp_enqueue_scripts', 'root_theme_enqueue_scripts');

/**
 * 3. Helper: Tính thời gian đọc bài viết (Reading Time)
 */
if (!function_exists('root_theme_get_reading_time')) {
    function root_theme_get_reading_time($content = '') {
        if (empty($content)) {
            $content = get_post_field('post_content', get_the_ID());
        }
        $clean_content = strip_shortcodes(wp_strip_all_tags($content));
        // Đếm số từ (hỗ trợ cả tiếng Việt unicode)
        $words = preg_split('/[\s]+/', trim($clean_content));
        $word_count = count(array_filter($words));
        // Tốc độ đọc trung bình 200 từ/phút
        $minutes = ceil($word_count / 200);
        return max(1, (int) $minutes);
    }
}

// Module tự chọn: bình chọn cảm xúc cho bài viết.
function root_theme_get_reaction_options() {
    return array(
        'helpful'     => array('emoji' => '💡', 'label' => 'Hữu ích'),
        'interesting' => array('emoji' => '✨', 'label' => 'Thú vị'),
        'surprising'  => array('emoji' => '😮', 'label' => 'Bất ngờ'),
        'confusing'   => array('emoji' => '🤔', 'label' => 'Khó hiểu'),
        'needs_more'  => array('emoji' => '📝', 'label' => 'Cần bổ sung'),
    );
}

function root_theme_get_reaction_counts($post_id) {
    $counts = get_post_meta($post_id, '_tdc_reaction_counts', true);
    $counts = is_array($counts) ? $counts : array();

    foreach (root_theme_get_reaction_options() as $key => $option) {
        $counts[$key] = isset($counts[$key]) ? max(0, (int) $counts[$key]) : 0;
    }

    return $counts;
}

function root_theme_reaction_cookie_name($post_id) {
    return 'tdc_post_reaction_' . absint($post_id);
}

function root_theme_encode_reaction_cookie($post_id, $reaction) {
    return $reaction . '.' . wp_hash($post_id . '|' . $reaction, 'nonce');
}

function root_theme_get_selected_reaction($post_id) {
    $cookie_name = root_theme_reaction_cookie_name($post_id);

    if (empty($_COOKIE[$cookie_name])) {
        return '';
    }

    $cookie_value = sanitize_text_field(wp_unslash($_COOKIE[$cookie_name]));
    $parts = explode('.', $cookie_value, 2);
    $options = root_theme_get_reaction_options();

    if (count($parts) !== 2 || !isset($options[$parts[0]])) {
        return '';
    }

    $expected = wp_hash($post_id . '|' . $parts[0], 'nonce');
    return hash_equals($expected, $parts[1]) ? $parts[0] : '';
}

function root_theme_set_reaction_cookie($post_id, $reaction) {
    $cookie_name   = root_theme_reaction_cookie_name($post_id);
    $cookie_value  = root_theme_encode_reaction_cookie($post_id, $reaction);
    $cookie_path   = defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
    $cookie_domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';

    setcookie($cookie_name, $cookie_value, array(
        'expires'  => time() + YEAR_IN_SECONDS,
        'path'     => $cookie_path,
        'domain'   => $cookie_domain,
        'secure'   => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax',
    ));

    $_COOKIE[$cookie_name] = $cookie_value;
}

function root_theme_handle_post_reaction() {
    check_ajax_referer('tdc_post_reaction', 'nonce');

    $post_id  = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $reaction = isset($_POST['reaction']) ? sanitize_key(wp_unslash($_POST['reaction'])) : '';
    $options  = root_theme_get_reaction_options();

    if (!$post_id || get_post_status($post_id) !== 'publish' || !isset($options[$reaction])) {
        wp_send_json_error(array('message' => 'Không thể ghi nhận lựa chọn này.'), 400);
    }

    $selected = root_theme_get_selected_reaction($post_id);
    $counts   = root_theme_get_reaction_counts($post_id);

    if ($selected === $reaction) {
        wp_send_json_success(array(
            'counts'   => $counts,
            'selected' => $selected,
            'message'  => 'Bạn đã chọn cảm xúc này rồi.',
        ));
    }

    if ($selected && isset($counts[$selected])) {
        $counts[$selected] = max(0, $counts[$selected] - 1);
    }

    $counts[$reaction]++;
    update_post_meta($post_id, '_tdc_reaction_counts', $counts);
    root_theme_set_reaction_cookie($post_id, $reaction);

    wp_send_json_success(array(
        'counts'   => $counts,
        'selected' => $reaction,
        'message'  => $selected ? 'Đã cập nhật cảm xúc của bạn.' : 'Cảm ơn bạn đã chia sẻ cảm xúc!',
    ));
}
add_action('wp_ajax_tdc_post_reaction', 'root_theme_handle_post_reaction');
add_action('wp_ajax_nopriv_tdc_post_reaction', 'root_theme_handle_post_reaction');

function root_theme_render_post_reactions($post_id) {
    $post_id  = absint($post_id);
    $options  = root_theme_get_reaction_options();
    $counts   = root_theme_get_reaction_counts($post_id);
    $selected = root_theme_get_selected_reaction($post_id);
    ?>
    <section class="tdc-reactions" data-post-id="<?php echo esc_attr($post_id); ?>" aria-labelledby="tdc-reactions-title-<?php echo esc_attr($post_id); ?>">
        <div class="tdc-reactions__intro">
            <span class="tdc-reactions__eyebrow">PHẢN HỒI NHANH</span>
            <h2 id="tdc-reactions-title-<?php echo esc_attr($post_id); ?>">Bài viết này mang lại cảm xúc gì cho bạn?</h2>
            <p>Chọn một cảm xúc phù hợp nhất. Bạn có thể đổi lựa chọn bất cứ lúc nào.</p>
        </div>
        <div class="tdc-reactions__grid" role="group" aria-label="Chọn cảm xúc">
            <?php foreach ($options as $key => $option) :
                $is_selected = $selected === $key;
                ?>
                <button class="tdc-reaction<?php echo $is_selected ? ' is-selected' : ''; ?>"
                        type="button"
                        data-reaction="<?php echo esc_attr($key); ?>"
                        aria-pressed="<?php echo $is_selected ? 'true' : 'false'; ?>">
                    <span class="tdc-reaction__emoji" aria-hidden="true"><?php echo esc_html($option['emoji']); ?></span>
                    <span class="tdc-reaction__label"><?php echo esc_html($option['label']); ?></span>
                    <span class="tdc-reaction__count" aria-label="<?php echo esc_attr($counts[$key] . ' lượt chọn'); ?>"><?php echo esc_html($counts[$key]); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
        <p class="tdc-reactions__status" role="status" aria-live="polite"></p>
    </section>
    <?php
}

function root_theme_enqueue_reaction_styles() {
    if (!is_single()) {
        return;
    }

    $reaction_css = <<<'CSS'
.tdc-reactions {
    position: relative;
    clear: both;
    margin: 38px 0 34px;
    padding: 28px;
    overflow: hidden;
    color: #334155;
    background: #f1f7fb;
    border: 1px solid #d9e8f2;
    border-left: 4px solid #1681c4;
    box-shadow: 0 12px 28px rgba(15, 53, 77, .08);
}
.tdc-reactions::after {
    content: '';
    position: absolute;
    right: -42px;
    top: -52px;
    width: 140px;
    height: 140px;
    border: 24px solid rgba(22, 129, 196, .06);
    border-radius: 50%;
    pointer-events: none;
}
.tdc-reactions__intro {
    position: relative;
    z-index: 1;
    max-width: 720px;
}
.tdc-reactions__eyebrow {
    display: block;
    margin-bottom: 7px;
    color: #1681c4;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .14em;
}
.tdc-reactions h2 {
    margin: 0 0 7px;
    color: #172b3a;
    font-size: clamp(20px, 2.4vw, 27px);
    line-height: 1.25;
}
.tdc-reactions__intro p {
    margin: 0;
    color: #5d7080;
    font-size: 14px;
    line-height: 1.55;
}
.tdc-reactions__grid {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 10px;
    margin-top: 22px;
}
.tdc-reaction {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 8px;
    min-width: 0;
    min-height: 58px;
    padding: 10px 11px;
    color: #334155;
    font: inherit;
    text-align: left;
    cursor: pointer;
    background: #fff;
    border: 1px solid #d5e1e9;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(15, 53, 77, .04);
    transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, color .18s ease;
}
.tdc-reaction:hover {
    color: #096aa6;
    border-color: #58a8d8;
    transform: translateY(-2px);
}
.tdc-reaction:focus-visible {
    outline: 3px solid rgba(14, 165, 233, .3);
    outline-offset: 2px;
}
.tdc-reaction.is-selected {
    color: #075f98;
    border-color: #1681c4;
    box-shadow: 0 0 0 2px #1681c4, 0 9px 18px rgba(22, 129, 196, .15);
    transform: translateY(-3px);
}
.tdc-reaction:disabled {
    cursor: wait;
    opacity: .65;
}
.tdc-reaction__emoji {
    font-size: 20px;
    line-height: 1;
}
.tdc-reaction__label {
    overflow: hidden;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.2;
    text-overflow: ellipsis;
}
.tdc-reaction__count {
    display: inline-grid;
    place-items: center;
    min-width: 25px;
    height: 25px;
    padding: 0 6px;
    color: #496273;
    font-size: 12px;
    font-weight: 800;
    background: #edf3f7;
    border-radius: 5px;
}
.tdc-reaction.is-selected .tdc-reaction__count {
    color: #fff;
    background: #1681c4;
}
.tdc-reactions__status {
    min-height: 20px;
    margin: 13px 0 -5px;
    color: #1373ad;
    font-size: 13px;
    font-weight: 600;
}
.tdc-reactions__status.is-error {
    color: #b42318;
}
@media (max-width: 900px) {
    .tdc-reactions__grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width: 480px) {
    .tdc-reactions {
        margin: 30px 0;
        padding: 22px 18px;
    }
    .tdc-reactions__grid {
        gap: 8px;
    }
    .tdc-reaction {
        min-height: 56px;
    }
    .tdc-reaction:last-child:nth-child(odd) {
        grid-column: 1 / -1;
    }
}
@media (max-width: 340px) {
    .tdc-reactions__grid {
        grid-template-columns: 1fr;
    }
    .tdc-reaction:last-child:nth-child(odd) {
        grid-column: auto;
    }
}
@media (prefers-reduced-motion: reduce) {
    .tdc-reaction {
        transition: none;
    }
}
CSS;

    wp_add_inline_style('group-c-style', $reaction_css);
}
add_action('wp_enqueue_scripts', 'root_theme_enqueue_reaction_styles', 40);

function root_theme_render_reaction_script() {
    if (!is_single()) {
        return;
    }
    ?>
    <script>
    (function () {
        'use strict';

        var config = <?php echo wp_json_encode(array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('tdc_post_reaction'),
        )); ?>;

        document.querySelectorAll('.tdc-reactions').forEach(function (module) {
            var buttons = Array.prototype.slice.call(module.querySelectorAll('.tdc-reaction'));
            var status = module.querySelector('.tdc-reactions__status');

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var previous = module.querySelector('.tdc-reaction.is-selected');
                    var reaction = button.getAttribute('data-reaction');
                    var formData = new FormData();
                    var originalCounts = {};

                    if (button.getAttribute('aria-pressed') === 'true') {
                        status.textContent = 'Bạn đã chọn cảm xúc này rồi.';
                        return;
                    }

                    buttons.forEach(function (item) { item.disabled = true; });
                    buttons.forEach(function (item) {
                        originalCounts[item.getAttribute('data-reaction')] = parseInt(item.querySelector('.tdc-reaction__count').textContent, 10) || 0;
                    });
                    module.setAttribute('aria-busy', 'true');
                    status.classList.remove('is-error');
                    status.textContent = 'Đang ghi nhận lựa chọn…';

                    if (previous) {
                        var previousCount = previous.querySelector('.tdc-reaction__count');
                        previousCount.textContent = Math.max(0, parseInt(previousCount.textContent, 10) - 1);
                        previous.classList.remove('is-selected');
                        previous.setAttribute('aria-pressed', 'false');
                    }

                    var currentCount = button.querySelector('.tdc-reaction__count');
                    currentCount.textContent = parseInt(currentCount.textContent, 10) + 1;
                    button.classList.add('is-selected');
                    button.setAttribute('aria-pressed', 'true');

                    formData.append('action', 'tdc_post_reaction');
                    formData.append('nonce', config.nonce);
                    formData.append('post_id', module.getAttribute('data-post-id'));
                    formData.append('reaction', reaction);

                    fetch(config.ajaxUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: formData
                    })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('request_failed');
                        }
                        return response.json();
                    })
                    .then(function (response) {
                        if (!response.success) {
                            throw new Error(response.data && response.data.message ? response.data.message : 'request_failed');
                        }

                        buttons.forEach(function (item) {
                            var key = item.getAttribute('data-reaction');
                            var count = response.data.counts[key] || 0;
                            var countNode = item.querySelector('.tdc-reaction__count');
                            var selected = key === response.data.selected;
                            countNode.textContent = count;
                            countNode.setAttribute('aria-label', count + ' lượt chọn');
                            item.classList.toggle('is-selected', selected);
                            item.setAttribute('aria-pressed', selected ? 'true' : 'false');
                        });
                        status.textContent = response.data.message;
                    })
                    .catch(function (error) {
                        buttons.forEach(function (item) {
                            var key = item.getAttribute('data-reaction');
                            var countNode = item.querySelector('.tdc-reaction__count');
                            var wasSelected = item === previous;
                            countNode.textContent = originalCounts[key];
                            countNode.setAttribute('aria-label', originalCounts[key] + ' lượt chọn');
                            item.classList.toggle('is-selected', wasSelected);
                            item.setAttribute('aria-pressed', wasSelected ? 'true' : 'false');
                        });
                        status.classList.add('is-error');
                        status.textContent = error.message !== 'request_failed' ? error.message : 'Chưa thể ghi nhận. Vui lòng thử lại.';
                    })
                    .then(function () {
                        buttons.forEach(function (item) { item.disabled = false; });
                        module.removeAttribute('aria-busy');
                    });
                });
            });
        });
    }());
    </script>
    <?php
}
add_action('wp_footer', 'root_theme_render_reaction_script', 40);

/**
 * 4. Đăng ký Sidebar
 */
function root_theme_widgets_init() {
    register_sidebar(array(
        'name'          => 'Main Sidebar',
        'id'            => 'sidebar-1',
        'description'   => 'Khu vực Sidebar chính (hiển thị cùng Modules 9, 10, 11, 13)',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'root_theme_widgets_init');

/**
 * 6. Shortcodes cho các Modules
 */

// Module 9: Chuyên mục (FIT TDC)
if (!shortcode_exists('tdc_categories')) {
    add_shortcode('tdc_categories', function($atts) {
        ob_start();
        root_theme_render_module_9();
        return ob_get_clean();
    });
}

// Module 10: Bài viết mới nhất (FIT TDC)
if (!shortcode_exists('tdc_recent_posts')) {
    add_shortcode('tdc_recent_posts', function($atts) {
        $args = shortcode_atts(array(
            'count'     => 10,
            'title'     => 'Bài viết mới nhất',
            'more_link' => home_url('/'),
        ), $atts);
        ob_start();
        root_theme_render_module_10($args);
        return ob_get_clean();
    });
}

// Module 11: Xem nhiều 2 cột (VNExpress)
if (!shortcode_exists('tdc_vnexpress_archive')) {
    add_shortcode('tdc_vnexpress_archive', function($atts) {
        $args = shortcode_atts(array(
            'count' => 8,
            'title' => 'Xem nhiều',
        ), $atts);
        ob_start();
        root_theme_render_module_11($args);
        return ob_get_clean();
    });
}

// Module 12: Tiêu Điểm Thể Thao & Highlights
if (!shortcode_exists('tdc_featured_sports')) {
    add_shortcode('tdc_featured_sports', function($atts) {
        ob_start();
        $file = get_template_directory() . '/modules/module-12-featured-sports.php';
        if (file_exists($file)) {
            include $file;
        }
        return ob_get_clean();
    });
}

// Module 13: Lịch Thi Đấu & Kết Quả Thể Thao
if (!shortcode_exists('tdc_sports_fixtures')) {
    add_shortcode('tdc_sports_fixtures', function($atts) {
        ob_start();
        root_theme_render_module_13();
        return ob_get_clean();
    });
}

// Module Latest News Timeline
if (!shortcode_exists('tdc_latest_news')) {
    add_shortcode('tdc_latest_news', function($atts) {
        ob_start();
        $file = get_template_directory() . '/modules/module-latest-news.php';
        if (file_exists($file)) {
            include $file;
        }
        return ob_get_clean();
    });
}


/**
 * Inline renderers for restored modules. The standalone module files remain deleted.
 */
function root_theme_render_module_9($args = array()) {
/**
 * Module 9: Categories phong cách FIT TDC
 * Vị trí: Sidebar / Shortcode [tdc_categories]
 * Tích hợp SQL trực tiếp qua $wpdb
 */
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$tdc_categories = $wpdb->get_results("
    SELECT t.term_id, t.name, t.slug, tt.count
    FROM {$wpdb->terms} t
    INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
    WHERE tt.taxonomy = 'category'
    ORDER BY t.name ASC
");
?>

<section class="widget tdc-fit-categories-widget">
    <div class="tdc-fit-categories-box">
        <h3 class="tdc-fit-categories-title" data-i18n="categories_title">Categories</h3>
        <div class="tdc-fit-title-stripe"></div>
        <ul class="tdc-fit-categories-list">
            <?php if (!empty($tdc_categories)) : ?>
                <?php foreach ($tdc_categories as $tdc_cat) : ?>
                    <li>
                        <span class="tdc-fit-bullet">&#8226;</span>
                        <a href="<?php echo esc_url(get_category_link($tdc_cat->term_id)); ?>" class="tdc-fit-cat-link">
                            <?php echo esc_html($tdc_cat->name); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else : ?>
                <li><span class="tdc-fit-bullet">&#8226;</span> <span data-i18n="no_categories">Chưa có chuyên mục nào.</span></li>
            <?php endif; ?>
        </ul>
    </div>
</section>
<?php
}

function root_theme_render_module_10($args = array()) {
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
<?php
}

function root_theme_render_module_11($args = array()) {
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
<?php
}

function root_theme_render_module_13($args = array()) {
/**
 * Module 13: Lịch Thi Đấu & Kết Quả Thể Thao (Tự chọn 2)
 * Vị trí: Sidebar / Shortcode [tdc_sports_fixtures]
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="custom-module-13-fixtures-widget">
    <div class="module-13-header">
        <div class="module-13-title-wrap">
            <i class="fa fa-futbol-o module-13-icon"></i>
            <h4 class="module-13-title" data-i18n="fixtures_title">Lịch Thi Đấu & Kết Quả</h4>
        </div>
        <div class="module-13-live-indicator">
            <span class="pulse-dot"></span> LIVE
        </div>
    </div>

    <!-- TABS -->
    <div class="module-13-tabs">
        <button type="button" class="module-13-tab-btn active" onclick="switchModule13Tab(event, 'tab-today')" data-i18n="tab_today">
            Hôm nay
        </button>
        <button type="button" class="module-13-tab-btn" onclick="switchModule13Tab(event, 'tab-fixtures')" data-i18n="tab_fixtures">
            Lịch thi đấu
        </button>
        <button type="button" class="module-13-tab-btn" onclick="switchModule13Tab(event, 'tab-results')" data-i18n="tab_results">
            Kết quả
        </button>
    </div>

    <!-- TAB 1: HÔM NAY (ĐANG DIỄN RA / SẮP ĐÁ) -->
    <div class="module-13-panel active" id="tab-today">
        <div class="module-13-match-row is-live-match">
            <div class="module-13-tournament-tag">
                <span>Premier League</span>
                <span class="match-time-tag"><i class="fa fa-clock-o"></i> 78'</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Arsenal</span>
                    <span class="team-score">2</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-live">LIVE 78'</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">1</span>
                    <span>Chelsea</span>
                </div>
            </div>
        </div>

        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>La Liga</span>
                <span class="match-time-tag"><i class="fa fa-clock-o"></i> 22:30</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Real Madrid</span>
                    <span class="team-score">-</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">22:30</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">-</span>
                    <span>Barcelona</span>
                </div>
            </div>
        </div>

        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>V-League 1</span>
                <span class="match-time-tag"><i class="fa fa-clock-o"></i> 19:15</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Hà Nội FC</span>
                    <span class="team-score">-</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">19:15</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">-</span>
                    <span>HAGL</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: LỊCH THI ĐẤU -->
    <div class="module-13-panel" id="tab-fixtures">
        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>Champions League</span>
                <span class="match-time-tag"><i class="fa fa-calendar"></i> 02:00</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Man City</span>
                    <span class="team-score">-</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">02:00</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">-</span>
                    <span>Bayern Munich</span>
                </div>
            </div>
        </div>

        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>Premier League</span>
                <span class="match-time-tag"><i class="fa fa-calendar"></i> 18:30</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Liverpool</span>
                    <span class="team-score">-</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">18:30</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">-</span>
                    <span>Man United</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: KẾT QUẢ -->
    <div class="module-13-panel" id="tab-results">
        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>Premier League</span>
                <span class="match-time-tag">FT</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Tottenham</span>
                    <span class="team-score">3</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">FT</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">1</span>
                    <span>Aston Villa</span>
                </div>
            </div>
        </div>

        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>Serie A</span>
                <span class="match-time-tag">FT</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Inter Milan</span>
                    <span class="team-score">2</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">FT</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">0</span>
                    <span>Juventus</span>
                </div>
            </div>
        </div>
    </div>

    <div class="module-13-footer">
        <a href="<?php echo esc_url(home_url('/?s=the-thao')); ?>" class="module-13-all-link">
            <span data-i18n="view_all_fixtures">Xem toàn bộ bảng xếp hạng & lịch thi đấu</span> <i class="fa fa-angle-right"></i>
        </a>
    </div>
</div>

<script>
function switchModule13Tab(evt, tabId) {
    var parent = evt.currentTarget.closest('.custom-module-13-fixtures-widget');
    if (!parent) return;
    var buttons = parent.querySelectorAll('.module-13-tab-btn');
    var panels = parent.querySelectorAll('.module-13-panel');
    buttons.forEach(function(btn) { btn.classList.remove('active'); });
    panels.forEach(function(p) { p.classList.remove('active'); });
    evt.currentTarget.classList.add('active');
    var target = parent.querySelector('#' + tabId);
    if (target) {
        target.classList.add('active');
    }
}
</script>
<?php
}

function root_theme_render_module_3_footer($args = array()) {
/**
 * Module 3: Footer chuẩn Bootsnipp rIXdE (Sunlimetech)
 * Vị trí: Chân trang toàn bộ website
 */
if (!defined('ABSPATH')) {
    exit;
}

$site_name = get_bloginfo('name') ?: 'Root Theme';
$categories = get_categories(array('number' => 12, 'hide_empty' => false));
?>

<footer id="footer" class="site-footer" role="contentinfo">
    <div class="footer-container">
        <div class="footer-row">
            <!-- Cột 1: Quick links -->
            <div class="footer-col">
                <h5 data-i18n="footer_quick_links">Khám phá nhanh</h5>
                <ul class="quick-links">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><i class="fa fa-angle-double-right"></i><span data-i18n="home">Trang chủ</span></a></li>
                    <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
                        <?php foreach (array_slice($categories, 0, 4) as $cat) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                                    <i class="fa fa-angle-double-right"></i><?php echo esc_html($cat->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li><a href="<?php echo esc_url(home_url('/?s=tin-tuc')); ?>"><i class="fa fa-angle-double-right"></i><span data-i18n="footer_general_news">Tin tức tổng hợp</span></a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Cột 2: Chuyên mục nổi bật -->
            <div class="footer-col">
                <h5 data-i18n="footer_categories">Chuyên mục</h5>
                <ul class="quick-links">
                    <?php if (!empty($categories) && count($categories) > 4) : ?>
                        <?php foreach (array_slice($categories, 4, 5) as $cat) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                                    <i class="fa fa-angle-double-right"></i><?php echo esc_html($cat->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li><a href="<?php echo esc_url(home_url('/?s=the-thao')); ?>"><i class="fa fa-angle-double-right"></i><span data-i18n="nav_sports">Thể thao</span></a></li>
                        <li><a href="<?php echo esc_url(home_url('/?s=khoa-hoc')); ?>"><i class="fa fa-angle-double-right"></i><span data-i18n="nav_science">Khoa học</span></a></li>
                        <li><a href="<?php echo esc_url(home_url('/?s=doi-song')); ?>"><i class="fa fa-angle-double-right"></i><span data-i18n="nav_news">Tin tức</span></a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Cột 3: Về chúng tôi & Hỗ trợ -->
            <div class="footer-col">
                <h5 data-i18n="footer_about_us">Về chúng tôi</h5>
                <ul class="quick-links">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><i class="fa fa-angle-double-right"></i><span data-i18n="footer_about_editorial">Giới thiệu tòa soạn</span></a></li>
                    <li><a href="<?php echo esc_url(admin_url()); ?>"><i class="fa fa-angle-double-right"></i><span data-i18n="footer_admin_sys">Quản trị hệ thống</span></a></li>
                    <li><a href="<?php echo esc_url(home_url('/?s=lien-he')); ?>"><i class="fa fa-angle-double-right"></i><span data-i18n="footer_contact_ads">Liên hệ & Quảng cáo</span></a></li>
                    <li><a href="<?php echo esc_url(home_url('/?s=dieu-khoan')); ?>"><i class="fa fa-angle-double-right"></i><span data-i18n="footer_terms">Điều khoản & Chính sách</span></a></li>
                </ul>
            </div>
        </div>

        <!-- Social row Bootsnipp rIXdE -->
        <div class="footer-social-row">
            <ul class="social">
                <li><a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa fa-facebook"></i></a></li>
                <li><a href="https://twitter.com" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fa fa-twitter"></i></a></li>
                <li><a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa fa-instagram"></i></a></li>
                <li><a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa fa-youtube-play"></i></a></li>
                <li><a href="mailto:<?php echo esc_attr(get_option('admin_email')); ?>" aria-label="Email"><i class="fa fa-envelope"></i></a></li>
            </ul>
        </div>

        <hr class="footer-divider" />

        <!-- Copyright row -->
        <div class="footer-copyright">
            <p>© <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($site_name); ?>. <span data-i18n="footer_rights">Tất cả quyền được bảo lưu.</span></p>
            <p class="copyright-author"><span data-i18n="footer_design_by">Thiết kế chuẩn</span> <a href="https://bootsnipp.com/snippets/rIXdE" target="_blank" rel="noopener">Bootsnipp rIXdE</a> <span data-i18n="by">bởi</span> <a href="<?php echo esc_url(home_url('/')); ?>">Group C & D</a></p>
        </div>
    </div>
</footer>
<?php
}

// Bootstrap 3 enqueue
if (!function_exists('enqueue_bootstrap')) {
    function enqueue_bootstrap() {
        wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), '3.3.7');
        wp_enqueue_script('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '3.3.7', true);
    }
    add_action('wp_enqueue_scripts', 'enqueue_bootstrap');
}

/**
 * =========================================================================
 * REUSABLE RENDER FUNCTIONS CHO CÁC MODULE (DÙNG ĐƯỢC Ở MỌI TEMPLATE)
 * =========================================================================
 */

// MODULE (9): CATEGORIES
if (!function_exists('root_theme_render_module_9_categories')) {
    function root_theme_render_module_9_categories() {
        global $wpdb;
        $tdc_categories = $wpdb->get_results("
            SELECT t.term_id, t.name, t.slug, tt.count
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'category'
            ORDER BY t.name ASC
        ");
        ?>
        <section class="widget tdc-fit-categories-widget">
            <div class="tdc-fit-categories-box">
                <h3 class="tdc-fit-categories-title" data-i18n="categories_title">Categories</h3>
                <div class="tdc-fit-title-stripe"></div>
                <ul class="tdc-fit-categories-list">
                    <?php if (!empty($tdc_categories)) : ?>
                        <?php foreach ($tdc_categories as $tdc_cat) : ?>
                            <li>
                                <span class="tdc-fit-bullet">&#8226;</span>
                                <a href="<?php echo esc_url(get_category_link($tdc_cat->term_id)); ?>" class="tdc-fit-cat-link">
                                    <?php echo esc_html($tdc_cat->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li><span class="tdc-fit-bullet">&#8226;</span> <span data-i18n="no_categories">Chưa có chuyên mục nào.</span></li>
                    <?php endif; ?>
                </ul>
            </div>
        </section>
        <?php
    }
}

// MODULE (10): BÀI VIẾT MỚI NHẤT
if (!function_exists('root_theme_render_module_10_recent_posts')) {
    function root_theme_render_module_10_recent_posts($limit = 5) {
        global $wpdb;
        $mod10_posts = $wpdb->get_results($wpdb->prepare("
            SELECT ID, post_title, post_date
            FROM {$wpdb->posts}
            WHERE post_type = 'post' AND post_status = 'publish'
            ORDER BY post_date DESC
            LIMIT %d
        ", $limit));
        ?>
        <section class="widget tdc-module-10-widget">
            <div class="tdc-fit-recent-box">
                <div class="tdc-fit-recent-header" data-i18n="recent_posts_title">Bài viết mới nhất</div>
                <div class="tdc-fit-recent-list">
                    <?php if (!empty($mod10_posts)) : ?>
                        <?php foreach ($mod10_posts as $rp) :
                            $rp_day   = date('d', strtotime($rp->post_date));
                            $rp_month = date('m', strtotime($rp->post_date));
                            $rp_year  = date('y', strtotime($rp->post_date));
                        ?>
                            <article class="tdc-fit-post-item">
                                <div class="tdc-fit-date-box">
                                    <div class="tdc-fit-date-top">
                                        <span class="tdc-fit-day"><?php echo esc_html($rp_day); ?></span>
                                        <span class="tdc-fit-dash">-</span>
                                        <span class="tdc-fit-year"><?php echo esc_html($rp_year); ?></span>
                                    </div>
                                    <div class="tdc-fit-date-bottom">
                                        <span class="tdc-fit-month">T<?php echo esc_html($rp_month); ?></span>
                                    </div>
                                </div>
                                <div class="tdc-fit-title-wrap">
                                    <a href="<?php echo esc_url(get_permalink($rp->ID)); ?>" class="tdc-fit-post-title">
                                        <?php echo esc_html($rp->post_title); ?>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p style="padding:10px;font-size:13px;" data-i18n="no_recent_posts">Chưa có bài viết nào.</p>
                    <?php endif; ?>
                </div>
                <div class="tdc-fit-recent-footer">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="tdc-fit-all-news-btn">
                        <span data-i18n="view_all_news">XEM TẤT CẢ TIN TỨC</span> <i class="fa fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </section>
        <?php
    }
}

// MODULE (11): ARCHIVE / XEM NHIỀU (VNEXPRESS STYLE)
if (!function_exists('root_theme_render_module_11_archive')) {
    function root_theme_render_module_11_archive($single_col = false) {
        global $wpdb;
        $mod11_posts = $wpdb->get_results("
            SELECT p.ID, p.post_title, COUNT(c.comment_ID) AS comment_count
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->comments} c ON c.comment_post_ID = p.ID AND c.comment_approved = '1'
            WHERE p.post_type = 'post' AND p.post_status = 'publish'
            GROUP BY p.ID
            ORDER BY comment_count DESC, p.post_date DESC
            LIMIT 8
        ");
        $mod11_total = count($mod11_posts);
        $mod11_half  = (int) ceil($mod11_total / 2);
        $mod11_col1  = array_slice($mod11_posts, 0, $mod11_half);
        $mod11_col2  = array_slice($mod11_posts, $mod11_half);
        ?>
        <section class="widget tdc-module-11-widget">
            <div class="tdc-vnexpress-wrap">
                <div class="tdc-vne-header">
                    <h3 class="tdc-vne-title" data-i18n="most_viewed_title">Archive / Xem nhiều</h3>
                </div>
                <?php if (!empty($mod11_posts)) : ?>
                    <?php if ($single_col) : ?>
                        <div class="tdc-vne-single-list">
                            <?php foreach ($mod11_posts as $idx => $vp) : ?>
                                <article class="tdc-vne-item">
                                    <span class="tdc-vne-number"><?php echo esc_html($idx + 1); ?></span>
                                    <div class="tdc-vne-content">
                                        <a href="<?php echo esc_url(get_permalink($vp->ID)); ?>" class="tdc-vne-link">
                                            <?php echo esc_html($vp->post_title); ?>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="tdc-vne-grid">
                            <div class="tdc-vne-col">
                                <?php foreach ($mod11_col1 as $idx => $vp) : ?>
                                    <article class="tdc-vne-item">
                                        <span class="tdc-vne-number"><?php echo esc_html($idx + 1); ?></span>
                                        <div class="tdc-vne-content">
                                            <a href="<?php echo esc_url(get_permalink($vp->ID)); ?>" class="tdc-vne-link">
                                                <?php echo esc_html($vp->post_title); ?>
                                            </a>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                            <div class="tdc-vne-col">
                                <?php foreach ($mod11_col2 as $idx => $vp) : ?>
                                    <article class="tdc-vne-item">
                                        <span class="tdc-vne-number"><?php echo esc_html($mod11_half + $idx + 1); ?></span>
                                        <div class="tdc-vne-content">
                                            <a href="<?php echo esc_url(get_permalink($vp->ID)); ?>" class="tdc-vne-link">
                                                <?php echo esc_html($vp->post_title); ?>
                                            </a>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <p style="padding:10px 0;font-size:13px;color:#888;" data-i18n="no_archive_posts">Chưa có bài viết nào.</p>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}

// MODULE (12): COMMENTS GẦN ĐÂY
if (!function_exists('root_theme_render_module_12_comments')) {
    function root_theme_render_module_12_comments($limit = 4) {
        global $wpdb;
        $mod12_comments = $wpdb->get_results($wpdb->prepare("
            SELECT c.comment_ID, c.comment_author, c.comment_content, c.comment_post_ID
            FROM {$wpdb->comments} c
            WHERE c.comment_approved = '1'
            ORDER BY c.comment_date DESC
            LIMIT %d
        ", $limit));
        ?>
        <section class="widget tdc-module-comments-widget">
            <div class="tdc-sidebar-comments-box">
                <div class="tdc-sidebar-comments-header">
                    <h3 class="tdc-sidebar-comments-title">
                        <i class="fa fa-comments-o"></i> <span data-i18n="recent_comments">Comments</span>
                    </h3>
                    <div class="tdc-sidebar-stripe"></div>
                </div>
                <?php if (!empty($mod12_comments)) : ?>
                    <div class="tdc-sidebar-comments-list">
                        <?php foreach ($mod12_comments as $mc) : ?>
                            <article class="tdc-sidebar-comment-item">
                                <a href="<?php echo esc_url(get_comment_link($mc->comment_ID)); ?>" class="tdc-sidebar-comment-link">
                                    <span class="comment-author-name"><strong><?php echo esc_html($mc->comment_author); ?></strong>:</span>
                                    <?php echo esc_html(wp_trim_words($mc->comment_content, 12, '...')); ?>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="tdc-sidebar-comments-empty" data-i18n="no_comments">Chưa có bình luận nào.</p>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}

// MODULE (13): LỊCH THI ĐẤU & KẾT QUẢ THỂ THAO (Ảnh 4)
if (!function_exists('root_theme_render_module_13_fixtures')) {
    function root_theme_render_module_13_fixtures($vertical = false) {
        static $instance_id = 0;
        $instance_id++;
        $tab_today_id    = 'tab-today-' . $instance_id;
        $tab_fixtures_id = 'tab-fixtures-' . $instance_id;
        $tab_results_id  = 'tab-results-' . $instance_id;
        ?>
        <div class="custom-module-13-fixtures-widget <?php echo $vertical ? 'is-vertical-item' : ''; ?>">
            <div class="module-13-header">
                <div class="module-13-title-wrap">
                    <i class="fa fa-futbol-o module-13-icon"></i>
                    <h4 class="module-13-title" data-i18n="fixtures_title">Lịch Thi Đấu & Kết Quả</h4>
                </div>
                <div class="module-13-live-indicator">
                    <span class="pulse-dot"></span> LIVE
                </div>
            </div>

            <!-- TABS -->
            <div class="module-13-tabs">
                <button type="button" class="module-13-tab-btn active" onclick="switchTdcFixtureTab(this,'<?php echo esc_attr($tab_today_id); ?>')" data-i18n="tab_today">Hôm nay</button>
                <button type="button" class="module-13-tab-btn" onclick="switchTdcFixtureTab(this,'<?php echo esc_attr($tab_fixtures_id); ?>')" data-i18n="tab_fixtures">Lịch thi đấu</button>
                <button type="button" class="module-13-tab-btn" onclick="switchTdcFixtureTab(this,'<?php echo esc_attr($tab_results_id); ?>')" data-i18n="tab_results">Kết quả</button>
            </div>

            <!-- TAB 1: HÔM NAY -->
            <div class="module-13-panel active" id="<?php echo esc_attr($tab_today_id); ?>">
                <div class="module-13-match-row is-live-match">
                    <div class="module-13-tournament-tag">
                        <span>Premier League</span>
                        <span class="match-time-tag"><i class="fa fa-clock-o"></i> 78'</span>
                    </div>
                    <div class="module-13-match-teams">
                        <div class="module-13-team team-home"><span>Arsenal</span><span class="team-score">2</span></div>
                        <div class="module-13-status-center"><span class="badge-live">LIVE 78'</span></div>
                        <div class="module-13-team team-away"><span class="team-score">1</span><span>Chelsea</span></div>
                    </div>
                </div>
                <div class="module-13-match-row">
                    <div class="module-13-tournament-tag">
                        <span>UEFA Champions League</span>
                        <span class="match-time-tag"><i class="fa fa-clock-o"></i> 21:00</span>
                    </div>
                    <div class="module-13-match-teams">
                        <div class="module-13-team team-home"><span>Real Madrid</span><span class="team-score">-</span></div>
                        <div class="module-13-status-center"><span class="badge-upcoming">21:00</span></div>
                        <div class="module-13-team team-away"><span class="team-score">-</span><span>Man City</span></div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: LỊCH THI ĐẤU -->
            <div class="module-13-panel" id="<?php echo esc_attr($tab_fixtures_id); ?>">
                <div class="module-13-match-row">
                    <div class="module-13-tournament-tag"><span>La Liga</span><span class="match-time-tag">T7 • 22:00</span></div>
                    <div class="module-13-match-teams">
                        <div class="module-13-team team-home"><span>Barcelona</span><span class="team-score">-</span></div>
                        <div class="module-13-status-center"><span class="badge-upcoming">22:00</span></div>
                        <div class="module-13-team team-away"><span class="team-score">-</span><span>Atletico</span></div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: KẾT QUẢ -->
            <div class="module-13-panel" id="<?php echo esc_attr($tab_results_id); ?>">
                <div class="module-13-match-row">
                    <div class="module-13-tournament-tag"><span>Serie A</span><span class="match-time-tag">FT</span></div>
                    <div class="module-13-match-teams">
                        <div class="module-13-team team-home"><span>Juventus</span><span class="team-score">3</span></div>
                        <div class="module-13-status-center"><span class="badge-finished">FT</span></div>
                        <div class="module-13-team team-away"><span class="team-score">1</span><span>AC Milan</span></div>
                    </div>
                </div>
            </div>

            <div class="module-13-footer">
                <a href="<?php echo esc_url(home_url('/?s=the-thao')); ?>" class="module-13-all-fixtures-link" data-i18n="all_fixtures">
                    <i class="fa fa-calendar"></i> Xem tất cả lịch thi đấu
                </a>
            </div>
        </div>
        <script>
        if (typeof window.switchTdcFixtureTab !== 'function') {
            window.switchTdcFixtureTab = function(btn, panelId) {
                var widget = btn.closest('.custom-module-13-fixtures-widget');
                if (!widget) return;
                var btns   = widget.querySelectorAll('.module-13-tab-btn');
                var panels = widget.querySelectorAll('.module-13-panel');
                btns.forEach(function(b){ b.classList.remove('active'); });
                panels.forEach(function(p){ p.classList.remove('active'); });
                btn.classList.add('active');
                var target = widget.querySelector('#' + panelId);
                if (target) target.classList.add('active');
            };
        }
        </script>
        <?php
    }
}

// MODULE (15): LATEST NEWS TIMELINE (Bootsnipp xrKXW)
if (!function_exists('root_theme_render_module_15_latest_news')) {
    function root_theme_render_module_15_latest_news($limit = 3, $exclude_id = 0) {
        global $wpdb;
        $sql = "
            SELECT ID, post_title, post_date, post_content, post_excerpt
            FROM {$wpdb->posts}
            WHERE post_type = 'post' AND post_status = 'publish'
        ";
        if ($exclude_id > 0) {
            $sql .= $wpdb->prepare(" AND ID != %d", $exclude_id);
        }
        $sql .= $wpdb->prepare(" ORDER BY post_date DESC LIMIT %d", $limit);
        $mod15_posts = $wpdb->get_results($sql);
        ?>
        <?php if (!empty($mod15_posts)) : ?>
            <section class="tdc-module-15-timeline" aria-label="Latest News Timeline">
                <h3 class="tdc-timeline-main-title" data-i18n="latest_news">Latest News</h3>
                <div class="tdc-timeline-list">
                    <?php foreach ($mod15_posts as $t_post) :
                        $t_date_str = date('j F, Y', strtotime($t_post->post_date));
                        $t_excerpt  = !empty($t_post->post_excerpt) ? $t_post->post_excerpt : $t_post->post_content;
                        $t_excerpt  = wp_trim_words(wp_strip_all_tags($t_excerpt), 24, '...');
                    ?>
                        <article class="tdc-timeline-item">
                            <span class="tdc-timeline-badge" aria-hidden="true"></span>
                            <div class="tdc-timeline-panel">
                                <div class="tdc-timeline-header">
                                    <h4 class="tdc-timeline-title">
                                        <a href="<?php echo esc_url(get_permalink($t_post->ID)); ?>">
                                            <?php echo esc_html($t_post->post_title); ?>
                                        </a>
                                    </h4>
                                    <time class="tdc-timeline-date" datetime="<?php echo esc_attr(date('c', strtotime($t_post->post_date))); ?>">
                                        <?php echo esc_html($t_date_str); ?>
                                    </time>
                                </div>
                                <div class="tdc-timeline-body">
                                    <p><?php echo esc_html($t_excerpt); ?></p>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <?php
    }
}

// MODULE MEDIA TABS: ẢNH | MEGASTORY | INFOGRAPHIC (Phong cách VnExpress)
if (!function_exists('root_theme_render_media_tabs')) {
    function root_theme_render_media_tabs($per_tab = 4) {
        global $wpdb;

        $posts_with_thumb = $wpdb->get_results($wpdb->prepare("
            SELECT p.ID, p.post_title, p.post_date
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = '_thumbnail_id'
            WHERE p.post_type = 'post' AND p.post_status = 'publish'
            ORDER BY p.post_date DESC
            LIMIT %d
        ", $per_tab * 3));

        if (count($posts_with_thumb) < $per_tab) {
            $posts_with_thumb = $wpdb->get_results($wpdb->prepare("
                SELECT ID, post_title, post_date
                FROM {$wpdb->posts}
                WHERE post_type = 'post' AND post_status = 'publish'
                ORDER BY post_date DESC
                LIMIT %d
            ", $per_tab * 3));
        }

        $placeholders = array(
            'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1504711434969-e33886168f5c?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=700&q=80',
            'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?auto=format&fit=crop&w=700&q=80',
        );

        $tab_data = array(
            array('id' => 'tdc-media-anh',        'label' => 'Ảnh',        'icon' => 'fa-camera',    'posts' => array_slice($posts_with_thumb, 0,         $per_tab)),
            array('id' => 'tdc-media-megastory',   'label' => 'Megastory',  'icon' => 'fa-star',      'posts' => array_slice($posts_with_thumb, $per_tab,  $per_tab)),
            array('id' => 'tdc-media-infographic', 'label' => 'Infographic','icon' => 'fa-bar-chart', 'posts' => array_slice($posts_with_thumb, $per_tab*2,$per_tab)),
        );
        ?>
        <section class="tdc-media-tabs-widget" aria-label="Ảnh, Megastory, Infographic">
            <div class="tdc-media-tabs-nav">
                <div class="tdc-media-tabs-nav-left" role="tablist">
                    <?php foreach ($tab_data as $i => $tab) : ?>
                        <button type="button"
                            class="tdc-media-tab-btn <?php echo $i === 0 ? 'active' : ''; ?>"
                            onclick="tdcSwitchMediaTab(this,'<?php echo esc_attr($tab['id']); ?>')"
                            role="tab"
                            aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                        ><?php echo esc_html($tab['label']); ?></button>
                    <?php endforeach; ?>
                </div>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="tdc-media-tabs-more">Xem thêm</a>
            </div>

            <?php foreach ($tab_data as $i => $tab) : ?>
                <div class="tdc-media-tab-panel <?php echo $i === 0 ? 'active' : ''; ?>"
                    id="<?php echo esc_attr($tab['id']); ?>" role="tabpanel">
                    <?php if (!empty($tab['posts'])) : ?>
                        <div class="tdc-media-cards-grid">
                            <?php foreach ($tab['posts'] as $pi => $post) :
                                $thumb = has_post_thumbnail($post->ID)
                                    ? get_the_post_thumbnail_url($post->ID, 'medium_large')
                                    : $placeholders[$pi % count($placeholders)];
                                $comment_count = (int) get_comments_number($post->ID);
                            ?>
                                <article class="tdc-media-card">
                                    <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"
                                        class="tdc-media-card-link"
                                        aria-label="<?php echo esc_attr($post->post_title); ?>">
                                        <?php if ($comment_count > 0) : ?>
                                            <span class="tdc-media-card-badge"><?php echo esc_html($comment_count); ?></span>
                                        <?php endif; ?>
                                        <span class="tdc-media-card-type-icon" aria-hidden="true">
                                            <i class="fa <?php echo esc_attr($tab['icon']); ?>"></i>
                                        </span>
                                        <div class="tdc-media-card-img-wrap">
                                            <img src="<?php echo esc_url($thumb); ?>"
                                                alt="<?php echo esc_attr($post->post_title); ?>"
                                                loading="lazy" class="tdc-media-card-img">
                                        </div>
                                        <div class="tdc-media-card-overlay">
                                            <h3 class="tdc-media-card-title"><?php echo esc_html($post->post_title); ?></h3>
                                        </div>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                        <div class="tdc-media-dots" aria-hidden="true">
                            <span class="tdc-media-dot active"></span>
                            <span class="tdc-media-dot"></span>
                            <span class="tdc-media-dot"></span>
                        </div>
                    <?php else : ?>
                        <p class="tdc-media-empty">Chưa có nội dung.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
        <script>
        if (typeof window.tdcSwitchMediaTab !== 'function') {
            window.tdcSwitchMediaTab = function(btn, panelId) {
                var w = btn.closest('.tdc-media-tabs-widget');
                if (!w) return;
                w.querySelectorAll('.tdc-media-tab-btn').forEach(function(b){ b.classList.remove('active'); b.setAttribute('aria-selected','false'); });
                w.querySelectorAll('.tdc-media-tab-panel').forEach(function(p){ p.classList.remove('active'); });
                btn.classList.add('active'); btn.setAttribute('aria-selected','true');
                var panel = w.querySelector('#'+panelId);
                if (panel) panel.classList.add('active');
            };
        }
        </script>
        <?php
    }
}
