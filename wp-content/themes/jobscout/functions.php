<?php
/**
 * JobScout functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package JobScout
 */

$jobscout_theme_data = wp_get_theme();
if( ! defined( 'JOBSCOUT_THEME_VERSION' ) ) define ( 'JOBSCOUT_THEME_VERSION', $jobscout_theme_data->get( 'Version' ) );
if( ! defined( 'JOBSCOUT_THEME_NAME' ) ) define( 'JOBSCOUT_THEME_NAME', $jobscout_theme_data->get( 'Name' ) );

/**
 * Implement Local Font Method functions.
 */
require get_template_directory() . '/inc/class-webfont-loader.php';

/**
 * Custom Functions.
 */
require get_template_directory() . '/inc/custom-functions.php';

/**
 * Standalone Functions.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Template Functions.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom functions for selective refresh.
 */
require get_template_directory() . '/inc/partials.php';

if( jobscout_is_rara_theme_companion_activated() ) :
	/**
	 * Modify filter hooks of RTC plugin.
	 */
	require get_template_directory() . '/inc/rtc-filters.php';
endif;

/**
 * Custom Controls
 */
require get_template_directory() . '/inc/custom-controls/custom-control.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer/customizer.php';

/**
 * Widgets
 */
require get_template_directory() . '/inc/widgets.php';
require get_template_directory() . '/inc/pds-widgets.php';

/**
 * Metabox
 */
require get_template_directory() . '/inc/metabox.php';

/**
 * Getting Started
*/
require get_template_directory() . '/inc/dashboard/dashboard.php';

/**
 * Plugin Recommendation
*/
require get_template_directory() . '/inc/tgmpa/recommended-plugins.php';

/**
 * Add theme compatibility function for woocommerce if active
*/
if( jobscout_is_woocommerce_activated() ){
    require get_template_directory() . '/inc/woocommerce-functions.php';    
}

/**
 * Modify filter hooks of WP Job Manager plugin.
 */
if( jobscout_is_wp_job_manager_activated() ) :
	require get_template_directory() . '/inc/wp-job-manager-filters.php';
endif;
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'pds-newsletter',
        get_template_directory_uri() . '/assets/css/pds-newsletter.css',
        [],
        filemtime(get_template_directory() . '/assets/css/pds-newsletter.css')
    );

    if ( is_front_page() ) {
        wp_enqueue_style(
            'pds-all-jobs-style',
            get_template_directory_uri() . '/assets/css/all-jobs.css',
            array( 'jobscout' ),
            defined( 'JOBSCOUT_THEME_VERSION' ) ? JOBSCOUT_THEME_VERSION : '1.0.0'
        );
    }
}, 99);

/**
 * Register custom semantic widget areas for CMS-JOB-DESIGN
 */
function jobscout_register_cms_widget_areas() {
    $widget_areas = array(
        array(
            'id'          => 'header-cta',
            'name'        => __( 'Header CTA (Submit Job)', 'jobscout' ),
            'description' => __( 'Nút SUBMIT JOB trên Header.', 'jobscout' ),
        ),
        array(
            'id'          => 'footer-newsletter',
            'name'        => __( 'Footer Newsletter', 'jobscout' ),
            'description' => __( 'Thanh cam Newsletter dùng chung ở chân trang.', 'jobscout' ),
        ),
        array(
            'id'          => 'footer-bottom',
            'name'        => __( 'Footer Bottom', 'jobscout' ),
            'description' => __( 'Khu vực bản quyền và icon mạng xã hội ở đáy footer.', 'jobscout' ),
        ),
        array(
            'id'          => 'home-hero-search',
            'name'        => __( 'Home Hero Search', 'jobscout' ),
            'description' => __( 'Banner tìm kiếm trang chủ.', 'jobscout' ),
        ),
        array(
            'id'          => 'home-top-jobs',
            'name'        => __( 'Home Top Jobs', 'jobscout' ),
            'description' => __( 'Khối Top Jobs trang chủ.', 'jobscout' ),
        ),
        array(
            'id'          => 'home-career-cta',
            'name'        => __( 'Home Career CTA', 'jobscout' ),
            'description' => __( 'Khối giới thiệu Career With Us trang chủ.', 'jobscout' ),
        ),
        array(
            'id'          => 'home-blog-entries',
            'name'        => __( 'Home Blog Entries', 'jobscout' ),
            'description' => __( 'Khối 4 bài blog trang chủ.', 'jobscout' ),
        ),
        array(
            'id'          => 'about-hero-banner',
            'name'        => __( 'About Hero Banner', 'jobscout' ),
            'description' => __( 'Banner SHARE OMOTENASHI đầu trang About Us (đổi ảnh và tiêu đề).', 'jobscout' ),
        ),
        array(
            'id'          => 'about-vision-section',
            'name'        => __( 'About Vision Section', 'jobscout' ),
            'description' => __( 'Khối Vision / Mission / Value trang About Us.', 'jobscout' ),
        ),
        array(
            'id'          => 'about-management-section',
            'name'        => __( 'About Management Callout', 'jobscout' ),
            'description' => __( 'Khối giới thiệu quản lý khách sạn/nhà hàng (Hotels, restaurants management) trang About.', 'jobscout' ),
        ),
        array(
            'id'          => 'about-company-stats',
            'name'        => __( 'About Company Stats', 'jobscout' ),
            'description' => __( 'Khối thông số công ty trang About Us.', 'jobscout' ),
        ),
        array(
            'id'          => 'jobs-hero-banner',
            'name'        => __( 'Jobs Hero Banner', 'jobscout' ),
            'description' => __( 'Banner CAREER WITH US đầu trang All Jobs (đổi ảnh và tiêu đề).', 'jobscout' ),
        ),
        array(
            'id'          => 'jobs-list-content',
            'name'        => __( 'Jobs List Content', 'jobscout' ),
            'description' => __( 'Khu vực hiển thị tin việc làm trang All Jobs.', 'jobscout' ),
        ),
        array(
            'id'          => 'job-detail-hero',
            'name'        => __( 'Job Detail Hero Banner', 'jobscout' ),
            'description' => __( 'Banner đầu trang Job Detail (ảnh và tiêu đề).', 'jobscout' ),
        ),
        array(
            'id'          => 'job-detail-header',
            'name'        => __( 'Job Detail Header Card', 'jobscout' ),
            'description' => __( 'Khối tiêu đề việc làm, logo, nút APPLY và SHARE trang Job Detail.', 'jobscout' ),
        ),
        array(
            'id'          => 'job-detail-content',
            'name'        => __( 'Job Detail Main Content', 'jobscout' ),
            'description' => __( 'Khối nội dung chi tiết mô tả công việc trang Job Detail.', 'jobscout' ),
        ),
        array(
            'id'          => 'job-detail-sidebar',
            'name'        => __( 'Job Detail Sidebar', 'jobscout' ),
            'description' => __( 'Cột phải trang Job Detail (Staff Rating & Photos).', 'jobscout' ),
        ),
        array(
            'id'          => 'job-detail-related',
            'name'        => __( 'Job Detail Related', 'jobscout' ),
            'description' => __( 'Khối Other Jobs cuối trang Job Detail.', 'jobscout' ),
        ),
        array(
            'id'          => 'news-hero-banner',
            'name'        => __( 'News Hero Banner', 'jobscout' ),
            'description' => __( 'Banner PDS NEWS đầu trang News (đổi ảnh và tiêu đề).', 'jobscout' ),
        ),
        array(
            'id'          => 'news-grid-content',
            'name'        => __( 'News Grid Content', 'jobscout' ),
            'description' => __( 'Khối bài viết trang News.', 'jobscout' ),
        ),
        array(
            'id'          => 'news-detail-header',
            'name'        => __( 'News Detail Header', 'jobscout' ),
            'description' => __( 'Khối tiêu đề bài viết, nút SHARE và BACK TO NEWS trang News Detail.', 'jobscout' ),
        ),
        array(
            'id'          => 'news-detail-content',
            'name'        => __( 'News Detail Article Overview', 'jobscout' ),
            'description' => __( 'Khối nội dung Article Overview trang News Detail.', 'jobscout' ),
        ),
        array(
            'id'          => 'news-detail-sidebar',
            'name'        => __( 'News Detail Sidebar', 'jobscout' ),
            'description' => __( 'Cột Article Information và Recent News trang News Detail.', 'jobscout' ),
        ),
        array(
            'id'          => 'news-detail-related',
            'name'        => __( 'News Detail Related', 'jobscout' ),
            'description' => __( 'Khối Newest Blog Entries cuối bài News Detail.', 'jobscout' ),
        ),
        array(
            'id'          => 'contact-hero-banner',
            'name'        => __( 'Contact Hero Banner', 'jobscout' ),
            'description' => __( 'Banner CONTACT US đầu trang Contact (đổi ảnh nền và tiêu đề).', 'jobscout' ),
        ),
        array(
            'id'          => 'contact-headquarters',
            'name'        => __( 'Contact Headquarters', 'jobscout' ),
            'description' => __( 'Khối địa chỉ trụ sở trang Contact Us.', 'jobscout' ),
        ),
        array(
            'id'          => 'contact-hotline-info',
            'name'        => __( 'Contact Hotline Info', 'jobscout' ),
            'description' => __( 'Khối Hotline 2 cột trang Contact Us.', 'jobscout' ),
        ),
    );

    foreach ( $widget_areas as $area ) {
        register_sidebar( array(
            'id'            => $area['id'],
            'name'          => $area['name'],
            'description'   => $area['description'],
            'before_widget' => '<div id="%1$s" class="custom-widget-box widget-area-' . esc_attr( $area['id'] ) . ' ' . esc_attr( $area['id'] ) . '-widget %2$s" data-widget-area="' . esc_attr( $area['id'] ) . '">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ) );
    }
}
add_action( 'widgets_init', 'jobscout_register_cms_widget_areas', 20 );

// Enable shortcode execution inside Custom HTML and Text widgets
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'widget_custom_html_custom_html_value', 'do_shortcode' );

// Luôn kích hoạt hiển thị tất cả các widget areas trong WordPress Customizer
add_filter( 'customize_section_active', function( $active, $section ) {
    if ( isset( $section->id ) && str_starts_with( $section->id, 'sidebar-widgets-' ) ) {
        return true;
    }
    return $active;
}, 20, 2 );

add_action( 'customize_register', function( $wp_customize ) {
    foreach ( $wp_customize->sections() as $section_id => $section ) {
        if ( str_starts_with( $section_id, 'sidebar-widgets-' ) ) {
            $section->active_callback = '__return_true';
        }
    }
}, 999 );

// Spacer cho menu chính để icon bút chì Customizer không đè lên chữ CONTACT
add_filter( 'wp_nav_menu_items', function( $items, $args ) {
    if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
        if ( is_customize_preview() ) {
            $items .= '<li class="menu-item-pds-spacer" style="width: 48px; min-width: 48px; display: inline-block; pointer-events: none;" aria-hidden="true"></li>';
        }
    }
    return $items;
}, 99, 2 );