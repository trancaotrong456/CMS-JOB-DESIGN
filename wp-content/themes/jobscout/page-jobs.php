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
    $paged = isset( $_REQUEST['page'] ) ? max( 1, (int) $_REQUEST['page'] ) : 1;
    $sort  = isset( $_REQUEST['sort'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['sort'] ) ) : 'latest';

    $args = array(
        'post_type'      => 'job_listing',
        'post_status'    => 'publish',
        'posts_per_page' => 6,
        'paged'          => $paged,
    );

    switch ( $sort ) {
        case 'oldest':
            $args['orderby'] = 'date';
            $args['order']   = 'ASC';
            break;
        case 'title_asc':
            $args['orderby'] = 'title';
            $args['order']   = 'ASC';
            break;
        case 'title_desc':
            $args['orderby'] = 'title';
            $args['order']   = 'DESC';
            break;
        case 'latest':
        default:
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
    }

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
$current_sort = isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : 'latest';
$current_page = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

$query_args = array(
    'post_type'      => 'job_listing',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'paged'          => $current_page,
);

switch ( $current_sort ) {
    case 'oldest':
        $query_args['orderby'] = 'date';
        $query_args['order']   = 'ASC';
        break;
    case 'title_asc':
        $query_args['orderby'] = 'title';
        $query_args['order']   = 'ASC';
        break;
    case 'title_desc':
        $query_args['orderby'] = 'title';
        $query_args['order']   = 'DESC';
        break;
    case 'latest':
    default:
        $query_args['orderby'] = 'date';
        $query_args['order']   = 'DESC';
        break;
}

$jobs_query = new WP_Query( $query_args );
$total_pages = $jobs_query->max_num_pages;

// -----------------------------------------------------------------------------
// 3. Page Enqueue & Body Class
// -----------------------------------------------------------------------------
add_filter( 'body_class', function( $classes ) {
    $classes[] = 'pds-all-jobs-page-body';
    return $classes;
} );

add_action( 'wp_enqueue_scripts', function() use ( $total_pages, $current_sort ) {
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
    <section class="pds-all-jobs-hero" aria-labelledby="pds-all-jobs-hero-title">
        <div class="pds-all-jobs-hero-overlay"></div>
        <div class="pds-all-jobs-hero-inner">
            <h1 id="pds-all-jobs-hero-title" class="pds-all-jobs-hero-title">
                <?php esc_html_e( 'CAREER WITH US', 'jobscout' ); ?>
            </h1>
        </div>
    </section>

    <!-- MAIN LISTING SECTION -->
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
                </div>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        </div>
    </main>

    <!-- NEWSLETTER BANNER -->
    <?php get_template_part( 'template-parts/pds-newsletter' ); ?>

</div>

<?php
get_footer();
