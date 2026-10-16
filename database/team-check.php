<?php
/** Read-only environment audit used by scripts/wamp64-team.ps1. */
if (!defined('ABSPATH')) {
    $wordpress_root = getenv('CMS_WORDPRESS_ROOT') ?: dirname(__DIR__);
    define('WP_USE_THEMES', false);
    require_once rtrim($wordpress_root, '/\\') . DIRECTORY_SEPARATOR . 'wp-load.php';
}

$theme = wp_get_theme();
$front_id = (int) get_option('page_on_front');
$posts_page_id = (int) get_option('page_for_posts');
$jobs_page_id = (int) get_option('job_manager_jobs_page_id');
$locations = get_nav_menu_locations();
$registered_locations = get_registered_nav_menus();
$menu_report = [];
foreach ($registered_locations as $location => $label) {
    $menu_id = isset($locations[$location]) ? (int) $locations[$location] : 0;
    $menu = $menu_id ? wp_get_nav_menu_object($menu_id) : false;
    $items = $menu ? wp_get_nav_menu_items($menu_id) : [];
    $menu_report[$location] = [
        'label' => $label,
        'menu' => $menu ? $menu->name : null,
        'item_count' => is_array($items) ? count($items) : 0,
    ];
}
$active_plugins = (array) get_option('active_plugins', []);
$home_url = home_url('/');
$published_jobs = new WP_Query([
    'post_type' => 'job_listing',
    'post_status' => 'publish',
    'posts_per_page' => 6,
    'fields' => 'ids',
    'no_found_rows' => true,
]);
$latest_posts = new WP_Query([
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => 4,
    'fields' => 'ids',
    'no_found_rows' => true,
]);
$home_is_page = get_option('show_on_front') === 'page'
    && $front_id > 0
    && get_post_status($front_id) === 'publish'
    && get_the_title($front_id) === 'Home';

$result = [
    'site_url' => $home_url,
    'theme' => $theme->get_stylesheet(),
    'header_file' => is_readable(get_template_directory() . '/header.php'),
    'footer_file' => is_readable(get_template_directory() . '/footer.php'),
    'home_is_front_page' => $home_is_page,
    'home_page_id' => $front_id,
    'news_page' => $posts_page_id ? get_the_title($posts_page_id) : null,
    'jobs_page' => $jobs_page_id ? get_the_title($jobs_page_id) : null,
    'menu_locations' => $menu_report,
    'wp_job_manager_active' => in_array('wp-job-manager/wp-job-manager.php', $active_plugins, true),
    'wpjm_extra_fields_active' => in_array('wpjm-extra-fields/wpjm-extra-fields.php', $active_plugins, true),
    'published_jobs' => count($published_jobs->posts),
    'latest_news' => array_map(static function ($post_id) {
        return get_the_title($post_id);
    }, $latest_posts->posts),
];

echo wp_json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
