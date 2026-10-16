<?php
/**
 * Template Name: About
 * Description: Purpose-built About Us page for JobScout.
 *
 * @package JobScout
 */

// Filter body classes to allow page-scoped styling and disable breadcrumbs
add_filter( 'body_class', function( $classes ) {
    $classes[] = 'pds-about-page-body';
    return $classes;
} );

// Enqueue About page scoped stylesheet
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'pds-about-style',
        get_template_directory_uri() . '/assets/css/about.css',
        array( 'jobscout' ),
        JOBSCOUT_THEME_VERSION
    );
}, 20 );

// Retrieve torii gate image from WordPress attachment or seed fallback
$torii_id = 0;
$torii_query = get_posts( array(
    'post_type'        => 'attachment',
    'meta_key'         => '_cms_shared_seed_asset',
    'meta_value'       => 'about-torii-lake',
    'posts_per_page'   => 1,
    'fields'           => 'ids',
    'suppress_filters' => true,
) );
if ( ! empty( $torii_query ) ) {
    $torii_id = (int) $torii_query[0];
}
$torii_url = $torii_id ? wp_get_attachment_image_url( $torii_id, 'large' ) : content_url( '/uploads/seed-cms-content/about-torii-lake.png' );
$torii_alt = $torii_id ? get_post_meta( $torii_id, '_wp_attachment_image_alt', true ) : __( 'Vermilion torii gate in a Japanese lake', 'jobscout' );

// Retrieve tokyo tower image from WordPress attachment or seed fallback
$tokyo_id = 0;
$tokyo_query = get_posts( array(
    'post_type'        => 'attachment',
    'meta_key'         => '_cms_shared_seed_asset',
    'meta_value'       => 'about-tokyo-tower',
    'posts_per_page'   => 1,
    'fields'           => 'ids',
    'suppress_filters' => true,
) );
if ( ! empty( $tokyo_query ) ) {
    $tokyo_id = (int) $tokyo_query[0];
}
$tokyo_url = $tokyo_id ? wp_get_attachment_image_url( $tokyo_id, 'large' ) : content_url( '/uploads/seed-cms-content/about-tokyo-tower.png' );
$tokyo_alt = $tokyo_id ? get_post_meta( $tokyo_id, '_wp_attachment_image_alt', true ) : __( 'Tokyo Tower above the city at blue hour', 'jobscout' );

get_header();
?>

<div class="pds-about-page">

    <!-- HERO BANNER -->
    <section class="pds-about-hero" aria-labelledby="pds-about-hero-title">
        <div class="pds-about-hero-overlay"></div>
        <div class="pds-about-hero-inner">
            <h1 id="pds-about-hero-title" class="pds-about-hero-title">
                <?php esc_html_e( 'SHARE “OMOTENASHI”', 'jobscout' ); ?><br>
                <?php esc_html_e( 'WITH THE WORLD', 'jobscout' ); ?>
            </h1>
        </div>
    </section>

    <!-- ABOUT US INTRO + VISION / MISSION / VALUE -->
    <section class="pds-about-vision-section" aria-labelledby="pds-about-main-title">
        <div class="pds-about-container">
            <h2 id="pds-about-main-title" class="pds-about-main-title">
                <?php esc_html_e( 'ABOUT US', 'jobscout' ); ?>
            </h2>

            <div class="pds-about-two-col pds-about-vision-grid">
                <!-- LEFT COLUMN: TORII GATE PHOTO -->
                <div class="pds-about-col pds-about-col-media">
                    <div class="pds-about-image-wrapper">
                        <img src="<?php echo esc_url( $torii_url ); ?>" alt="<?php echo esc_attr( $torii_alt ); ?>" class="pds-about-img" loading="lazy">
                    </div>
                </div>

                <!-- RIGHT COLUMN: VISION, MISSION, VALUE -->
                <div class="pds-about-col pds-about-col-content">
                    <div class="pds-about-info-block">
                        <h3 class="pds-about-block-title"><?php esc_html_e( 'Our Vision', 'jobscout' ); ?></h3>
                        <p class="pds-about-block-desc">
                            <?php esc_html_e( 'Create hotels and restaurants around the world that offer memorable experiences while building a lasting, positive relationship together with our guests, partners, team members and communities.', 'jobscout' ); ?>
                        </p>
                    </div>

                    <div class="pds-about-info-block">
                        <h3 class="pds-about-block-title"><?php esc_html_e( 'Ours Mission', 'jobscout' ); ?></h3>
                        <p class="pds-about-block-desc">
                            <?php esc_html_e( 'Share “Omotenashi” with the world', 'jobscout' ); ?>
                        </p>
                    </div>

                    <div class="pds-about-info-block">
                        <h3 class="pds-about-block-title"><?php esc_html_e( 'Our Core Value', 'jobscout' ); ?></h3>
                        <p class="pds-about-block-desc">
                            <span class="pds-about-quote-lead"><?php esc_html_e( '“If I were the guest…”', 'jobscout' ); ?></span><br>
                            <?php esc_html_e( 'To provide guests with the hospitality you would want to receive as a guest.', 'jobscout' ); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MANAGEMENT OVERVIEW (CENTERED HIGHLIGHT) -->
    <section class="pds-about-management-section" aria-labelledby="pds-about-mgmt-title">
        <div class="pds-about-container">
            <div class="pds-about-management-inner">
                <h2 id="pds-about-mgmt-title" class="pds-about-mgmt-title">
                    <?php esc_html_e( 'Hotels, restaurants, banquets/weddings management', 'jobscout' ); ?>
                </h2>
                <div class="pds-about-mgmt-text">
                    <p>
                        <?php esc_html_e( 'Plan Do See developed and operates 17 properties worldwide including 3 award-winning resorts in Japan; 17 restaurants of diverse cuisines in cities including New York, Miami and Los Angeles; and other countries including Japan, Indonesia, Malaysia and Bali.', 'jobscout' ); ?>
                    </p>
                    <p>
                        <?php esc_html_e( 'Each venue carries its own concept and design. Many of them are originally historical landmarks that were loved by the local people.', 'jobscout' ); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- COMPANY FACTS + TOKYO TOWER PHOTO -->
    <section class="pds-about-facts-section" aria-label="<?php esc_attr_e( 'Company Facts', 'jobscout' ); ?>">
        <div class="pds-about-container">
            <div class="pds-about-two-col pds-about-facts-grid">
                <!-- LEFT COLUMN: FACTS LIST -->
                <div class="pds-about-col pds-about-col-facts">
                    <div class="pds-about-fact-item">
                        <h3 class="pds-about-fact-label"><?php esc_html_e( 'Established since', 'jobscout' ); ?></h3>
                        <p class="pds-about-fact-value"><?php esc_html_e( 'April 1993', 'jobscout' ); ?></p>
                    </div>

                    <div class="pds-about-fact-item">
                        <h3 class="pds-about-fact-label"><?php esc_html_e( 'Head Office', 'jobscout' ); ?></h3>
                        <p class="pds-about-fact-value"><?php esc_html_e( 'Marunouchi 2-1-1, Chiyoda, Tokyo', 'jobscout' ); ?></p>
                    </div>

                    <div class="pds-about-fact-item">
                        <h3 class="pds-about-fact-label"><?php esc_html_e( 'Capital', 'jobscout' ); ?></h3>
                        <p class="pds-about-fact-value"><?php esc_html_e( '100,000,000 JPY', 'jobscout' ); ?></p>
                    </div>

                    <div class="pds-about-fact-item">
                        <h3 class="pds-about-fact-label"><?php esc_html_e( 'CEO', 'jobscout' ); ?></h3>
                        <p class="pds-about-fact-value"><?php esc_html_e( 'Yutaka Noda', 'jobscout' ); ?></p>
                    </div>

                    <div class="pds-about-fact-item">
                        <h3 class="pds-about-fact-label"><?php esc_html_e( 'Number of Employees', 'jobscout' ); ?></h3>
                        <p class="pds-about-fact-value"><?php esc_html_e( 'Full time: 830 / Total: 1,600', 'jobscout' ); ?></p>
                    </div>
                </div>

                <!-- RIGHT COLUMN: TOKYO TOWER PHOTO -->
                <div class="pds-about-col pds-about-col-media">
                    <div class="pds-about-image-wrapper">
                        <img src="<?php echo esc_url( $tokyo_url ); ?>" alt="<?php echo esc_attr( $tokyo_alt ); ?>" class="pds-about-img" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEWSLETTER BANNER -->
    <section class="pds-about-newsletter" aria-labelledby="pds-about-newsletter-title">
        <div class="pds-about-newsletter-inner">
            <h2 id="pds-about-newsletter-title" class="pds-about-newsletter-heading">
                <?php esc_html_e( 'Subscribe To', 'jobscout' ); ?><br>
                <?php esc_html_e( 'Our Newsletter', 'jobscout' ); ?>
            </h2>
            <form class="pds-about-newsletter-form" onsubmit="return false;">
                <label class="pds-about-newsletter-field">
                    <span class="pds-about-newsletter-icon" aria-hidden="true">
                        <svg width="20" height="16" viewBox="0 0 20 16" fill="currentColor">
                            <path d="M18 0H2C0.9 0 0.01 0.9 0.01 2L0 14C0 15.1 0.9 16 2 16H18C19.1 16 20 15.1 20 14V2C20 0.9 19.1 0 18 0ZM18 4L10 9L2 4V2L10 7L18 2V4Z"/>
                        </svg>
                    </span>
                    <input type="email" name="about_newsletter_email" placeholder="<?php esc_attr_e( 'Input your email address', 'jobscout' ); ?>" required>
                </label>
                <button type="button" class="pds-about-newsletter-btn"><?php esc_html_e( 'SUBSCRIBE', 'jobscout' ); ?></button>
            </form>
        </div>
    </section>

</div>

<?php
get_footer();
