<?php
/**
 * Template Name: All Jobs
 * Description: Purpose-built All Jobs listing page for JobScout matching reference design.
 *
 * WordPress selects this template for the page with slug "jobs"
 * via template hierarchy: page-jobs.php -> page.php.
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -----------------------------------------------------------------------------
// 1. Handle Self-Contained AJAX Requests for Load More & Dynamic Sort
// -----------------------------------------------------------------------------
if ( isset( $_REQUEST['pds_ajax_jobs'] ) ) {
    $paged    = isset( $_REQUEST['page'] ) ? max( 1, (int) $_REQUEST['page'] ) : 1;
    $sort     = isset( $_REQUEST['sort'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['sort'] ) ) : 'latest';
    $keywords = isset( $_REQUEST['search_keywords'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search_keywords'] ) ) : '';
    $location = isset( $_REQUEST['search_location'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search_location'] ) ) : '';

    $args = pds_get_jobs_query_args( array(
        'posts_per_page'  => 6,
        'paged'           => $paged,
        'sort'            => $sort,
        'search_keywords' => $keywords,
        'search_location' => $location,
    ) );

    $ajax_query = new WP_Query( $args );

    ob_start();
    if ( $ajax_query->have_posts() ) {
        while ( $ajax_query->have_posts() ) {
            $ajax_query->the_post();
            get_template_part( 'template-parts/all-jobs-card' );
        }
    }
    $rendered_html = ob_get_clean();
    $max_num_pages = $ajax_query->max_num_pages;
    wp_reset_postdata();

    wp_send_json_success( array(
        'html'        => $rendered_html,
        'has_more'    => ( $paged < $max_num_pages ),
        'max_pages'   => $max_num_pages,
        'current_page'=> $paged,
        'total'       => $ajax_query->found_posts,
    ) );
    exit;
}

// -----------------------------------------------------------------------------
// 2. Initial Page Query
// -----------------------------------------------------------------------------
$current_sort    = isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : 'latest';
$current_page    = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$search_keywords = isset( $_GET['search_keywords'] ) ? sanitize_text_field( wp_unslash( $_GET['search_keywords'] ) ) : '';
$search_location = isset( $_GET['search_location'] ) ? sanitize_text_field( wp_unslash( $_GET['search_location'] ) ) : '';

$query_args = pds_get_jobs_query_args( array(
    'posts_per_page'  => 6,
    'paged'           => $current_page,
    'sort'            => $current_sort,
    'search_keywords' => $search_keywords,
    'search_location' => $search_location,
) );

$jobs_query  = new WP_Query( $query_args );
$total_pages = $jobs_query->max_num_pages;

// -----------------------------------------------------------------------------
// 3. Page Enqueue & Body Class
// -----------------------------------------------------------------------------
add_filter( 'body_class', function( $classes ) {
    $classes[] = 'pds-all-jobs-page-body';
    return $classes;
} );

add_action( 'wp_enqueue_scripts', function() use ( $total_pages, $current_sort, $search_keywords, $search_location ) {
    wp_enqueue_style(
        'pds-all-jobs-style',
        get_template_directory_uri() . '/assets/css/all-jobs.css',
        array( 'jobscout' ),
        defined( 'JOBSCOUT_THEME_VERSION' ) ? JOBSCOUT_THEME_VERSION : '1.0.0'
    );

    wp_enqueue_script(
        'pds-all-jobs-script',
        get_template_directory_uri() . '/assets/js/all-jobs.js',
        array(),
        defined( 'JOBSCOUT_THEME_VERSION' ) ? JOBSCOUT_THEME_VERSION : '1.0.0',
        true
    );

    wp_localize_script(
        'pds-all-jobs-script',
        'pdsAllJobsData',
        array(
            'endpoint' => esc_url( get_permalink() ),
            'maxPages' => $total_pages,
            'sort'     => $current_sort,
            'keywords' => $search_keywords,
            'location' => $search_location,
            'noMore'   => __( 'NO MORE JOBS', 'jobscout' ),
            'loading'  => __( 'LOADING...', 'jobscout' ),
            'loadMore' => __( 'LOAD MORE JOBS', 'jobscout' ),
        )
    );
}, 20 );

get_header();
?>

<div class="pds-all-jobs-page">

    <!-- HERO SECTION -->
    <?php if ( is_active_sidebar( 'jobs-hero-banner' ) ) : ?>
        <section class="pds-all-jobs-hero pds-all-jobs-hero--widget" aria-labelledby="pds-all-jobs-hero-title">
            <?php dynamic_sidebar( 'jobs-hero-banner' ); ?>
        </section>
    <?php else : ?>
        <?php
        $jobs_featured_img = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : '';
        if ( ! empty( $jobs_featured_img ) ) : ?>
            <style>.pds-all-jobs-hero { background-image: url('<?php echo esc_url( $jobs_featured_img ); ?>') !important; }</style>
        <?php endif; ?>
        <section class="pds-all-jobs-hero" aria-labelledby="pds-all-jobs-hero-title">
            <div class="pds-all-jobs-hero-overlay"></div>
            <div class="pds-all-jobs-hero-inner" style="margin: 0 auto !important; text-align: center !important; width: 100% !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important;">
                <h1 id="pds-all-jobs-hero-title" class="pds-all-jobs-hero-title" style="margin: 0 auto !important; text-align: center !important; width: 100% !important;">
                    <?php esc_html_e( 'CAREER WITH US', 'jobscout' ); ?>
                </h1>
            </div>
        </section>
    <?php endif; ?>

    <!-- MAIN LISTING SECTION -->
    <?php if ( is_active_sidebar( 'jobs-list-content' ) ) : ?>
        <main class="pds-all-jobs-main pds-widget-area-section">
            <div class="pds-all-jobs-container">
                <?php dynamic_sidebar( 'jobs-list-content' ); ?>
            </div>
        </main>
    <?php else : ?>
    <main class="pds-all-jobs-main">
        <div class="pds-all-jobs-container">

            <!-- HEADER BAR: TITLE & SORT SELECTOR -->
            <div class="pds-all-jobs-header">
                <h2 class="pds-all-jobs-title">
                    <?php esc_html_e( 'ALL JOBS', 'jobscout' ); ?>
                </h2>

                <div class="pds-all-jobs-sort-wrapper">
                    <label for="pds-all-jobs-sort" class="screen-reader-text">
                        <?php esc_html_e( 'Sort Jobs', 'jobscout' ); ?>
                    </label>
                    <select id="pds-all-jobs-sort" class="pds-all-jobs-sort-select">
                        <option value="latest" <?php selected( $current_sort, 'latest' ); ?>>
                            <?php esc_html_e( 'Latest Jobs', 'jobscout' ); ?>
                        </option>
                        <option value="oldest" <?php selected( $current_sort, 'oldest' ); ?>>
                            <?php esc_html_e( 'Oldest Jobs', 'jobscout' ); ?>
                        </option>
                        <option value="title_asc" <?php selected( $current_sort, 'title_asc' ); ?>>
                            <?php esc_html_e( 'Title: A - Z', 'jobscout' ); ?>
                        </option>
                        <option value="title_desc" <?php selected( $current_sort, 'title_desc' ); ?>>
                            <?php esc_html_e( 'Title: Z - A', 'jobscout' ); ?>
                        </option>
                    </select>
                </div>
            </div>

            <?php if ( ! empty( $search_keywords ) || ! empty( $search_location ) ) : ?>
                <div class="pds-all-jobs-filter-status" style="margin-bottom: 24px; font-size: 14px; color: #555; background: #fff8f2; border: 1px solid #ffd8b8; padding: 12px 18px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <strong><?php esc_html_e( 'Search results for:', 'jobscout' ); ?></strong>
                        <?php if ( ! empty( $search_keywords ) ) : ?>
                            <span style="color: #f2761b; font-weight: 600;">"<?php echo esc_html( $search_keywords ); ?>"</span>
                        <?php endif; ?>
                        <?php if ( ! empty( $search_location ) ) : ?>
                            <?php esc_html_e( 'in', 'jobscout' ); ?> <span style="color: #f2761b; font-weight: 600;">"<?php echo esc_html( $search_location ); ?>"</span>
                        <?php endif; ?>
                        <span>(<?php printf( esc_html( _n( '%d job found', '%d jobs found', $jobs_query->found_posts, 'jobscout' ) ), (int) $jobs_query->found_posts ); ?>)</span>
                    </div>
                    <a href="<?php echo esc_url( get_permalink() ); ?>" style="color: #d9534f; font-weight: 600; text-decoration: underline; font-size: 13px;">
                        <?php esc_html_e( 'Clear Filter', 'jobscout' ); ?>
                    </a>
                </div>
            <?php endif; ?>

            <!-- JOBS GRID -->
            <?php if ( $jobs_query->have_posts() ) : ?>

                <div id="pds-all-jobs-grid" class="pds-all-jobs-grid">
                    <?php
                    while ( $jobs_query->have_posts() ) {
                        $jobs_query->the_post();
                        get_template_part( 'template-parts/all-jobs-card' );
                    }
                    ?>
                </div>

                <!-- LOAD MORE BUTTON -->
                <div class="pds-all-jobs-loadmore-wrap">
                    <button
                        type="button"
                        id="pds-all-jobs-load-more"
                        class="pds-all-jobs-loadmore-btn"
                        data-page="1"
                        data-max="<?php echo esc_attr( $total_pages ); ?>"
                    >
                        <?php esc_html_e( 'LOAD MORE JOBS', 'jobscout' ); ?>
                    </button>
                </div>

            <?php else : ?>

                <div class="pds-all-jobs-empty">
                    <p><?php esc_html_e( 'No job openings found matching your criteria.', 'jobscout' ); ?></p>
                    <?php if ( ! empty( $search_keywords ) || ! empty( $search_location ) ) : ?>
                        <p style="margin-top: 14px;">
                            <a href="<?php echo esc_url( get_permalink() ); ?>" style="display: inline-block; background: #f2761b; color: #fff; padding: 10px 20px; font-weight: 700; text-transform: uppercase; border-radius: 3px; text-decoration: none; font-size: 13px;">
                                <?php esc_html_e( 'VIEW ALL JOBS', 'jobscout' ); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        </div>
    </main>
    <?php endif; ?>

    <!-- NEWSLETTER BANNER -->
    <?php get_template_part( 'template-parts/pds-newsletter' ); ?>

</div>

<?php
get_footer();
