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
    <section class="pds-contact-hero" aria-labelledby="pds-contact-hero-title">
        <div class="pds-contact-hero-overlay"></div>
        <div class="pds-contact-hero-inner">
            <h1 id="pds-contact-hero-title" class="pds-contact-hero-title">
                <?php esc_html_e( 'CONTACT US', 'jobscout' ); ?>
            </h1>
        </div>
    </section>

    <!-- HEADQUARTERS ADDRESS SECTION -->
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

    <!-- TWO COLUMNS CONTACT CHANNELS -->
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

    <!-- NEWSLETTER BANNER -->
    <section class="pds-contact-newsletter" aria-labelledby="pds-contact-newsletter-title">
        <div class="pds-contact-newsletter-inner">
            <h2 id="pds-contact-newsletter-title" class="pds-contact-newsletter-heading">
                <?php esc_html_e( 'Subscribe To', 'jobscout' ); ?><br>
                <?php esc_html_e( 'Our Newsletter', 'jobscout' ); ?>
            </h2>
            <form class="pds-contact-newsletter-form" onsubmit="return false;">
                <label class="pds-contact-newsletter-field">
                    <span class="pds-contact-newsletter-icon" aria-hidden="true">
                        <svg width="20" height="16" viewBox="0 0 20 16" fill="currentColor">
                            <path d="M18 0H2C0.9 0 0.01 0.9 0.01 2L0 14C0 15.1 0.9 16 2 16H18C19.1 16 20 15.1 20 14V2C20 0.9 19.1 0 18 0ZM18 4L10 9L2 4V2L10 7L18 2V4Z"/>
                        </svg>
                    </span>
                    <input type="email" name="contact_newsletter_email" placeholder="<?php esc_attr_e( 'Input your email address', 'jobscout' ); ?>" required>
                </label>
                <button type="button" class="pds-contact-newsletter-btn"><?php esc_html_e( 'SUBSCRIBE', 'jobscout' ); ?></button>
            </form>
        </div>
    </section>

</div>

<?php
get_footer();
