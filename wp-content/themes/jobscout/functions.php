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
});

/**
 * PDS News Widget Areas
 */
function pds_register_news_widgets() {

    $areas = array(
        'pds-news-hero' => 'PDS News - Hero Banner',
        'pds-news-before-grid' => 'PDS News - Before Grid',
        'pds-news-after-grid' => 'PDS News - After Grid',
        'pds-news-newsletter' => 'PDS News - Newsletter',
    );

    foreach ($areas as $id => $name) {
        register_sidebar(array(
            'name'          => __($name, 'jobscout'),
            'id'            => $id,
            'before_widget' => '<div class="pds-news-widget">',
            'after_widget'  => '</div>',
            'before_title'  => '<h2 class="pds-news-widget-title">',
            'after_title'   => '</h2>',
        ));
    }
}
add_action('widgets_init', 'pds_register_news_widgets');

require_once get_template_directory()
    . '/pds-detail-register-areas.php';

require_once get_template_directory()
    . '/pds-detail-summary-widget.php';

require_once get_template_directory()
    . '/pds-detail-section-widgets.php';
