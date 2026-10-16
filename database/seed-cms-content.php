<?php
/**
 * Idempotent CMS reference-data seed for the JobScout course project.
 * Run with WP-CLI: wp eval-file database/seed-cms-content.php
 * Or bootstrap WordPress first and include this file once.
 */
if (!defined('ABSPATH')) {
    $wp_root = getenv('CMS_WORDPRESS_ROOT') ?: dirname(__DIR__);
    define('WP_USE_THEMES', false);
    require_once rtrim($wp_root, '/\\') . DIRECTORY_SEPARATOR . 'wp-load.php';
}

if (!function_exists('wp_insert_post')) {
    throw new RuntimeException('WordPress must be loaded before the seed runs.');
}

// Do not send job notifications from a local development seed run.
add_filter('pre_wp_mail', static function () { return true; });

function cms_seed_require_term($taxonomy, $name) {
    if (!taxonomy_exists($taxonomy)) {
        throw new RuntimeException(sprintf('Required taxonomy "%s" is unavailable. Enable the taxonomy in WordPress/WP Job Manager first.', $taxonomy));
    }
    $term = term_exists($name, $taxonomy);
    if (!$term) {
        $term = wp_insert_term($name, $taxonomy);
    }
    if (is_wp_error($term)) {
        throw new RuntimeException($term->get_error_message());
    }
    return (int) (is_array($term) ? $term['term_id'] : $term);
}

function cms_seed_asset($key, $filename, $title, $alt) {
    if (!function_exists('wp_generate_attachment_metadata')) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }
    $matches = get_posts([
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'numberposts'    => 1,
        'fields'         => 'ids',
        'meta_key'       => '_cms_shared_seed_asset',
        'meta_value'     => $key,
        'suppress_filters' => true,
    ]);
    if ($matches) {
        $existing_id = (int) $matches[0];
        $existing_file = get_attached_file($existing_id);
        if ($existing_file && is_file($existing_file) && !wp_get_attachment_metadata($existing_id)) {
            $metadata = wp_generate_attachment_metadata($existing_id, $existing_file);
            if (is_array($metadata)) {
                wp_update_attachment_metadata($existing_id, $metadata);
            }
        }
        return $existing_id;
    }
    if (!function_exists('wp_generate_attachment_metadata')) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }
    $matches = get_posts([
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'numberposts'    => 1,
        'fields'         => 'ids',
        'meta_key'       => '_cms_shared_seed_asset',
        'meta_value'     => $key,
        'suppress_filters' => true,
    ]);
    if ($matches) {
        return (int) $matches[0];
    }

    $source = dirname(__DIR__) . '/wp-content/uploads/seed-cms-content/' . $filename;
    $uploads = wp_upload_dir();
    if (!empty($uploads['error'])) {
        throw new RuntimeException($uploads['error']);
    }
    $target_dir = trailingslashit($uploads['basedir']) . 'seed-cms-content';
    if (!wp_mkdir_p($target_dir)) {
        throw new RuntimeException('Could not create the WordPress uploads directory for seed media.');
    }
    $target = trailingslashit($target_dir) . $filename;

    if (!is_file($target)) {
        if (!is_file($source) || !copy($source, $target)) {
            throw new RuntimeException(sprintf('Seed image is missing or could not be copied: %s', $filename));
        }
    } elseif (is_file($source) && hash_file('sha256', $source) !== hash_file('sha256', $target)) {
        throw new RuntimeException(sprintf('A different file already uses the seed-media path: %s', $filename));
    }

    $attachment_id = wp_insert_attachment([
        'post_mime_type' => 'image/png',
        'post_title'     => $title,
        'post_content'   => '',
        'post_status'    => 'inherit',
    ], $target, 0, true);
    if (is_wp_error($attachment_id)) {
        throw new RuntimeException($attachment_id->get_error_message());
    }

    update_post_meta($attachment_id, '_cms_shared_seed_asset', $key);
    update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt);
    $metadata = wp_generate_attachment_metadata($attachment_id, $target);
    if (is_array($metadata)) {
        wp_update_attachment_metadata($attachment_id, $metadata);
    }
    return (int) $attachment_id;
}

function cms_seed_create_once($post_type, $key, array $post_data, $fallback_slug = '') {
    global $cms_seed_created_posts, $cms_seed_conflicts;

    $cms_seed_created_posts = isset($cms_seed_created_posts) ? $cms_seed_created_posts : [];
    $cms_seed_conflicts = isset($cms_seed_conflicts) ? $cms_seed_conflicts : [];

    $seeded = get_posts([
        'post_type'        => $post_type,
        'post_status'      => ['publish', 'draft', 'pending', 'expired', 'private', 'future'],
        'numberposts'      => 1,
        'fields'           => 'ids',
        'meta_key'         => '_cms_shared_seed_key',
        'meta_value'       => $key,
        'suppress_filters' => true,
    ]);
    if ($seeded) {
        $cms_seed_created_posts[(int) $seeded[0]] = false;
        return (int) $seeded[0];
    }

    $conflict = null;
    if ($fallback_slug) {
        $slug_matches = get_posts([
            'post_type'        => $post_type,
            'post_status'      => ['publish', 'draft', 'pending', 'expired', 'private', 'future'],
            'numberposts'      => 1,
            'name'             => $fallback_slug,
            'fields'           => 'ids',
            'suppress_filters' => true,
        ]);
        $conflict = $slug_matches ? get_post((int) $slug_matches[0]) : null;
    }
    if (!$conflict && !empty($post_data['post_title'])) {
        $same_title = get_posts([
            'post_type'        => $post_type,
            'post_status'      => ['publish', 'draft', 'pending', 'expired', 'private', 'future'],
            'numberposts'      => 1,
            'title'            => $post_data['post_title'],
            'fields'           => 'ids',
            'suppress_filters' => true,
        ]);
        $conflict = $same_title ? get_post((int) $same_title[0]) : null;
    }
    if ($conflict) {
        $cms_seed_conflicts[] = [
            'type'  => $post_type,
            'title' => $post_data['post_title'],
            'id'    => (int) $conflict->ID,
        ];
        return 0;
    }

    $post_id = wp_insert_post(wp_slash($post_data), true);
    if (is_wp_error($post_id)) {
        throw new RuntimeException($post_id->get_error_message());
    }
    if (!$post_id) {
        return 0;
    }

    add_post_meta($post_id, '_cms_shared_seed_key', $key, true);
    $cms_seed_created_posts[(int) $post_id] = true;
    return (int) $post_id;
}

function cms_seed_is_new_post($post_id) {
    global $cms_seed_created_posts;
    return !empty($cms_seed_created_posts[(int) $post_id]);
}
function cms_seed_image_html($attachment_id, $alt, $class = '') {
    $url = wp_get_attachment_image_url($attachment_id, 'full');
    if (!$url) {
        return '';
    }
    return sprintf(
        '<figure class="wp-block-image"><img src="%s" alt="%s"%s></figure>',
        esc_url($url),
        esc_attr($alt),
        $class ? ' class="' . esc_attr($class) . '"' : ''
    );
}

$job_category_id = cms_seed_require_term('job_listing_category', 'Category Name');
$job_type_id     = cms_seed_require_term('job_listing_type', 'Fulltime');
$post_category_id = cms_seed_require_term('category', 'Category Name');
$bullet_lines = [
    'Be responsible for the effective operational management of the hotel',
    'Excellent salary bonuses &amp; recognition activities',
    'Foreign language allowance (up to 500USD/month)',
];
$bullet_html = '<ul><li>' . implode('</li><li>', $bullet_lines) . '</li></ul>';
$detail_placeholder = '<h2>Overview about Company</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, at. Vivamus volutpat in nunc, porttitor dui. Ut placerat aenean accumsan, aenean lacus eu. Aliquet urna, habitasse elit lorem id enim quam. Eu varius nulla nullam dignissim massa tempor, massa tortor. Eget auctor nulla maecenas ac tortor.</p>'
    . '<h2>Our Key Skills</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, at. Vivamus volutpat in nunc, porttitor dui.</p>'
    . '<h2>Why You\'ll Love Working Here</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, at. Vivamus volutpat in nunc, porttitor dui. Ut placerat aenean accumsan, aenean lacus eu. Aliquet urna, habitasse elit lorem id enim quam.</p>'
    . '<h2>Location</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, at, porttitor dui.</p>';

$job_rows = [
    ['HOTEL MANAGER', 'The SODOH', 'hotel-manager', '12:00:00'],
    ['GENERAL MANAGER - LEADING HOTEL CHAIN', 'Fortune Garden', 'general-manager-leading-hotel-chain', '11:00:00'],
    ['BANQUET MANAGER', 'Shikaku', 'banquet-manager', '10:00:00'],
    ['BELLMAN', 'The Sayan House', 'bellman', '09:00:00'],
    ['CHIEF OPERATING OFFICER HOTEL / RESORT CHAIN', 'Phở Thìn', 'chief-operating-officer-hotel-resort-chain', '08:00:00'],
    ['LOSS PREVENTION OFFICER', 'From Where I Stand', 'loss-prevention-officer', '07:00:00'],
];
$job_ids = [];
foreach ($job_rows as $row) {
    [$title, $company, $slug, $time] = $row;
    $body = $bullet_html;
    if ($slug === 'chief-operating-officer-hotel-resort-chain') {
        $body .= $detail_placeholder;
    }
    $job_id = cms_seed_create_once('job_listing', 'job:' . $slug, [
        'post_type'      => 'job_listing',
        'post_status'    => 'publish',
        'post_title'     => $title,
        'post_name'      => $slug,
        'post_content'   => $body,
        'post_excerpt'   => $bullet_html,
        'post_date'      => '2022-10-20 ' . $time,
        'post_date_gmt'  => get_gmt_from_date('2022-10-20 ' . $time),
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
    ], $slug);
    if (!$job_id) {
        continue;
    }
    $job_ids[] = $job_id;
    if (!cms_seed_is_new_post($job_id)) {
        continue;
    }

    update_post_meta($job_id, '_company_name', $company);
    update_post_meta($job_id, '_job_location', 'Ho Chi Minh City');
    wp_set_object_terms($job_id, [$job_type_id], 'job_listing_type', false);
    wp_set_object_terms($job_id, [$job_category_id], 'job_listing_category', false);
    if ($slug === 'chief-operating-officer-hotel-resort-chain') {
        update_post_meta($job_id, '_cms_reference_staff_rating', '4.0');
        update_post_meta($job_id, '_cms_reference_company_photo_count', '6');
    }
}
$assets = [
    'project' => cms_seed_asset('news-project-development', 'news-project-development.png', 'Project Development — Kyoto restaurant exterior', 'Traditional Kyoto restaurant exterior'),
    'restaurant' => cms_seed_asset('news-restaurant-operations', 'news-restaurant-hotel-operations.png', 'Restaurant and hotel operations — Kyoto', 'Japanese restaurant entrance and guest'),
    'consulting' => cms_seed_asset('news-hospitality-consulting', 'news-hospitality-consulting.png', 'Hospitality consulting — sakura evening', 'Cherry blossoms and lantern-lit path in Japan'),
    'venue' => cms_seed_asset('news-venue-interior', 'news-venue-interior.png', 'Venue and interior design — koi pond', 'Japanese koi pond with lily pads'),
    'torii' => cms_seed_asset('about-torii-lake', 'about-torii-lake.png', 'About — torii gate by the lake', 'Vermilion torii gate in a Japanese lake'),
    'tokyo' => cms_seed_asset('about-tokyo-tower', 'about-tokyo-tower.png', 'About — Tokyo Tower at blue hour', 'Tokyo Tower above the city at blue hour'),
    'contact' => cms_seed_asset('contact-japanese-street', 'contact-japanese-street.png', 'Contact — Japanese street', 'Japanese street with a red paper parasol'),
];

$excerpt = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
$paragraph = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Faucibus lectus tristique massa gravida vel elementum, mi. Sit scelerisque at amet leo. In volutpat turpis dolor, at. Vivamus volutpat in nunc, porttitor dui. Ut placerat aenean accumsan, aenean lacus eu.</p>';
$news_rows = [
    ['Project Development', 'project-development', $assets['project'], 'A Japanese restaurant entrance and streetscape.'],
    ['Restaurant & Hotel Management And Operations', 'restaurant-hotel-management-and-operations', $assets['restaurant'], 'A Japanese restaurant ready to welcome guests.'],
    ['Hospitality Consulting', 'hospitality-consulting', $assets['consulting'], 'A lantern-lit path beneath cherry blossoms.'],
    ['Venue And Interior Design', 'venue-and-interior-design', $assets['venue'], 'A koi pond as an example of Japanese landscape design.'],
];
$news_ids = [];
$news_category = [$post_category_id];
foreach ($news_rows as $index => $row) {
    [$title, $slug, $image_id, $alt] = $row;
    $timestamp = current_time('timestamp') - ($index * 60);
    $date = wp_date('Y-m-d H:i:s', $timestamp);
    $content = $paragraph . $paragraph . $paragraph;
    $post_id = cms_seed_create_once('post', 'news:' . $slug, [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'post_title'     => $title,
        'post_name'      => $slug,
        'post_excerpt'   => $excerpt,
        'post_content'   => $content,
        'post_date'      => $date,
        'post_date_gmt'  => get_gmt_from_date($date),
        'post_category'  => $news_category,
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
    ], $slug);
    if (!$post_id) {
        continue;
    }
    $news_ids[] = $post_id;
    if (cms_seed_is_new_post($post_id)) {
        wp_set_post_categories($post_id, $news_category, false);
        set_post_thumbnail($post_id, $image_id);
    }
}
$detail_slug = 'chief-operating-officer-hotel-resort-chain-news';
$detail_content = $paragraph . cms_seed_image_html($assets['project'], 'Japanese restaurant exterior')
    . $paragraph . cms_seed_image_html($assets['consulting'], 'Cherry blossoms and lanterns in Japan') . $paragraph;
$detail_post_id = cms_seed_create_once('post', 'news:reference-detail', [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'post_title'     => 'CHIEF OPERATING OFFICER HOTEL/ RESORT CHAIN',
    'post_name'      => $detail_slug,
    'post_excerpt'   => $excerpt,
    'post_content'   => $detail_content,
    'post_date'      => '2022-10-20 12:00:00',
    'post_date_gmt'  => get_gmt_from_date('2022-10-20 12:00:00'),
    'post_category'  => $news_category,
    'comment_status' => 'closed',
    'ping_status'    => 'closed',
], $detail_slug);
if ($detail_post_id && cms_seed_is_new_post($detail_post_id)) {
    wp_set_post_categories($detail_post_id, $news_category, false);
    set_post_thumbnail($detail_post_id, $assets['project']);
    update_post_meta($detail_post_id, '_cms_reference_location', 'Ho Chi Minh City');
}
$about_content = cms_seed_image_html($assets['torii'], 'Vermilion torii gate in a Japanese lake')
    . '<h2>Our Vision</h2><p>Create hotels and restaurants around the world that offer memorable experiences while building a lasting, positive relationship together with our guests, partners, team members and communities.</p>'
    . '<h2>Our Mission</h2><p>Share “Omotenashi” with the world.</p>'
    . '<h2>Our Core Value</h2><p>“If I were the guest”<br>To provide guests with the hospitality you would want to receive as a guest.</p>'
    . '<h2>Hotels, restaurants, banquets/weddings management</h2><p>Plan Do See developed and operates 17 properties worldwide including 3 award-winning resorts in Japan; 17 restaurants of diverse cuisines in cities including New York, Miami and Los Angeles; and other countries including Japan, Indonesia, Malaysia and Bali.</p>'
    . '<p>Each venue carries its own concept and design. Many of them are originally historical landmarks that were loved by the local people.</p>'
    . cms_seed_image_html($assets['tokyo'], 'Tokyo Tower above the city at blue hour')
    . '<h2>Established since</h2><p>April 1993</p><h2>Head Office</h2><p>Marunouchi 2-1-1, Chiyoda, Tokyo</p>'
    . '<h2>Capital</h2><p>100,000,000 JPY</p><h2>CEO</h2><p>Yutaka Noda</p>'
    . '<h2>Number of Employees</h2><p>Full time: 830 / Total: 1,600</p>';
$about_id = cms_seed_create_once('page', 'page:about', [
    'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'About', 'post_name' => 'about',
    'post_content' => $about_content, 'comment_status' => 'closed', 'ping_status' => 'closed',
], 'about');

$contact_content = cms_seed_image_html($assets['contact'], 'Japanese street with a red paper parasol')
    . '<h2>Our Headquarters Address</h2><p>60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City, Viet Nam</p>'
    . '<h2>For Employers</h2><p>Call our Sales Hotline</p><h3>Ho Chi Minh</h3><h3>Ha Noi</h3>'
    . '<h2>For Jobseekers</h2><p>Ask a question on our Facebook page.<br>Read our blog posts on interview and CV tips.</p>'
    . '<h2>Call us at</h2><p>Request a call from one of our Customer Love Account Managers. We’re ready to help you grow!</p>';
$contact_id = cms_seed_create_once('page', 'page:contact', [
    'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Contact', 'post_name' => 'contact',
    'post_content' => $contact_content, 'comment_status' => 'closed', 'ping_status' => 'closed',
], 'contact');

$news_page_id = cms_seed_create_once('page', 'page:news-index', [
    'post_type'      => 'page',
    'post_status'    => 'publish',
    'post_title'     => 'News',
    'post_name'      => 'news',
    'post_content'   => '',
    'comment_status' => 'closed',
    'ping_status'    => 'closed',
], 'news');
if ($news_page_id && !get_option('page_for_posts')) {
    update_option('page_for_posts', $news_page_id);
}

$jobs_page = get_page_by_path('jobs', OBJECT, 'page');
if ($jobs_page && !get_option('job_manager_jobs_page_id')) {
    update_option('job_manager_jobs_page_id', (int) $jobs_page->ID);
}
if (get_option('job_manager_enable_categories', false) === false) {
    add_option('job_manager_enable_categories', '1');
}
if (get_option('posts_per_page', false) === false) {
    add_option('posts_per_page', 4);
}
$expired_count = (int) (new WP_Query([
    'post_type' => 'job_listing', 'post_status' => 'expired', 'posts_per_page' => 1,
    'fields' => 'ids', 'no_found_rows' => false,
]))->found_posts;

$result = [
    'jobs' => $job_ids,
    'news_posts' => $news_ids,
    'news_detail_post' => $detail_post_id,
    'about_page' => $about_id,
    'contact_page' => $contact_id,
    'news_index_page' => isset($news_page_id) ? (int) $news_page_id : null,
    'seed_assets' => $assets,
    'expired_job_count_preserved' => $expired_count,
    'created_post_ids' => array_keys(array_filter($cms_seed_created_posts)),
    'conflicts_skipped' => $cms_seed_conflicts,
];
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::log(wp_json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
} else {
    echo wp_json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
}