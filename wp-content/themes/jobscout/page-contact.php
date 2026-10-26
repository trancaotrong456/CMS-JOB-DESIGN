<?php
/**
 * Template Name: Contact
 * Description: Purpose-built Contact Us page for JobScout.
 *
 * @package JobScout
 */

// Filter body classes to allow page-scoped styling and disable breadcrumbs
add_filter( 'body_class', function( $classes ) {
    $classes[] = 'pds-contact-page-body';
    return $classes;
} );

// Enqueue contact page scoped stylesheet
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'pds-contact-style',
        get_template_directory_uri() . '/assets/css/contact.css',
        array( 'jobscout' ),
        JOBSCOUT_THEME_VERSION
    );
}, 20 );

get_header();
?>

<div class="pds-contact-page">

    <!-- HERO BANNER -->
    <?php if ( is_active_sidebar( 'contact-hero-banner' ) ) : ?>
        <section class="pds-contact-hero pds-contact-hero--widget" aria-labelledby="pds-contact-hero-title">
            <?php dynamic_sidebar( 'contact-hero-banner' ); ?>
        </section>
    <?php else : ?>
        <?php
        $contact_featured_img = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : '';
        if ( ! empty( $contact_featured_img ) ) : ?>
            <style>.pds-contact-hero { background-image: url('<?php echo esc_url( $contact_featured_img ); ?>') !important; }</style>
        <?php endif; ?>
        <section class="pds-contact-hero" aria-labelledby="pds-contact-hero-title">
            <div class="pds-contact-hero-overlay"></div>
            <div class="pds-contact-hero-inner" style="margin: 0 auto !important; text-align: center !important;">
                <h1 id="pds-contact-hero-title" class="pds-contact-hero-title" style="margin: 0 auto !important; text-align: center !important;">
                    <?php esc_html_e( 'CONTACT US', 'jobscout' ); ?>
                </h1>
            </div>
        </section>
    <?php endif; ?>

    <!-- HEADQUARTERS ADDRESS SECTION -->
    <?php if ( is_active_sidebar( 'contact-headquarters' ) ) : ?>
        <section class="pds-contact-hq pds-widget-area-section" aria-labelledby="pds-contact-hq-title">
            <div class="pds-contact-container">
                <?php dynamic_sidebar( 'contact-headquarters' ); ?>
            </div>
        </section>
    <?php else : ?>
    <section class="pds-contact-hq" aria-labelledby="pds-contact-hq-title">
        <div class="pds-contact-container">
            <h2 id="pds-contact-hq-title" class="pds-contact-hq-title">
                <?php esc_html_e( 'Our Headquarters Address', 'jobscout' ); ?>
            </h2>
            <p class="pds-contact-hq-address">
                <?php esc_html_e( '60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City, Viet Nam', 'jobscout' ); ?>
            </p>
        </div>
    </section>
    <?php endif; ?>

    <!-- TWO COLUMNS CONTACT CHANNELS -->
    <?php if ( is_active_sidebar( 'contact-hotline-info' ) ) : ?>
        <section class="pds-contact-channels pds-widget-area-section" aria-label="<?php esc_attr_e( 'Contact Channels', 'jobscout' ); ?>">
            <div class="pds-contact-container">
                <?php dynamic_sidebar( 'contact-hotline-info' ); ?>
            </div>
        </section>
    <?php else : ?>
    <section class="pds-contact-channels" aria-label="<?php esc_attr_e( 'Contact Channels', 'jobscout' ); ?>">
        <div class="pds-contact-container">
            <div class="pds-contact-grid">

                <!-- LEFT COLUMN: FOR EMPLOYERS -->
                <div class="pds-contact-column pds-contact-employers">
                    <h3 class="pds-contact-col-title"><?php esc_html_e( 'For Employers', 'jobscout' ); ?></h3>
                    <p class="pds-contact-col-subtitle"><?php esc_html_e( 'Call our Sales Hotline', 'jobscout' ); ?></p>

                    <div class="pds-contact-city-group">
                        <h4 class="pds-contact-city-name"><?php esc_html_e( 'Ho Chi Minh', 'jobscout' ); ?></h4>
                        <p class="pds-contact-phone">
                            <a href="tel:+842839115828"><?php esc_html_e( '+84 (0) 28 3911 5828', 'jobscout' ); ?></a>
                        </p>
                    </div>

                    <div class="pds-contact-city-group">
                        <h4 class="pds-contact-city-name"><?php esc_html_e( 'Ha Noi', 'jobscout' ); ?></h4>
                        <p class="pds-contact-phone">
                            <a href="tel:+842439368899"><?php esc_html_e( '+84 (0) 24 3936 8899', 'jobscout' ); ?></a>
                        </p>
                    </div>

                    <div class="pds-contact-note">
                        <p>
                            <?php esc_html_e( 'Request a call from one of our', 'jobscout' ); ?><br>
                            <?php esc_html_e( 'Customer Love Account Managers', 'jobscout' ); ?><br>
                            <?php esc_html_e( "We're ready to help you grow!", 'jobscout' ); ?>
                        </p>
                    </div>
                </div>

                <!-- RIGHT COLUMN: FOR JOBSEEKERS -->
                <div class="pds-contact-column pds-contact-jobseekers">
                    <h3 class="pds-contact-col-title"><?php esc_html_e( 'For Jobseekers', 'jobscout' ); ?></h3>

                    <div class="pds-contact-seeker-actions">
                        <p class="pds-contact-action-item">
                            <?php
                            printf(
                                /* translators: %s: Facebook page link */
                                esc_html__( 'Ask a question on our %s page', 'jobscout' ),
                                '<a href="https://facebook.com" class="pds-contact-link-orange" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Facebook', 'jobscout' ) . '</a>'
                            );
                            ?>
                        </p>
                        <p class="pds-contact-action-item">
                            <?php
                            $news_page = get_page_by_path( 'news' );
                            $news_url  = $news_page ? get_permalink( $news_page ) : home_url( '/news/' );
                            printf(
                                /* translators: %s: blog posts link */
                                esc_html__( 'Read our %s on interview and CV tips', 'jobscout' ),
                                '<a href="' . esc_url( $news_url ) . '" class="pds-contact-link-orange">' . esc_html__( 'blog posts', 'jobscout' ) . '</a>'
                            );
                            ?>
                        </p>
                    </div>

                    <div class="pds-contact-city-group pds-contact-seeker-call">
                        <h4 class="pds-contact-city-name"><?php esc_html_e( 'Call us at', 'jobscout' ); ?></h4>
                        <p class="pds-contact-phone">
                            <a href="tel:+842839115828"><?php esc_html_e( '+84 (0) 28 3911 5828', 'jobscout' ); ?></a>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <?php endif; ?>


    <!-- NEWSLETTER BANNER -->
    <?php get_template_part( 'template-parts/pds-newsletter' ); ?>

</div>

<?php
get_footer();
