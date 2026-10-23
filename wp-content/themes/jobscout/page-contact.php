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

            <div class="pds-contact-hq-address-wrapper">
                <button type="button"
                        class="pds-contact-hq-address-btn"
                        id="pds-contact-map-trigger"
                        aria-expanded="false"
                        aria-controls="pds-contact-map-card"
                        title="<?php esc_attr_e( 'Click to view map', 'jobscout' ); ?>">
                    <span class="pds-contact-pin-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </span>
                    <span class="pds-contact-address-text">
                        <?php esc_html_e( '60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City, Viet Nam', 'jobscout' ); ?>
                    </span>
                    <span class="pds-contact-address-badge">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                            <line x1="8" y1="2" x2="8" y2="18"></line>
                            <line x1="16" y1="6" x2="16" y2="22"></line>
                        </svg>
                        <span class="pds-contact-badge-label"><?php esc_html_e( 'Bấm để xem bản đồ', 'jobscout' ); ?></span>
                    </span>
                </button>
            </div>

            <!-- INTERACTIVE MAP CONTAINER -->
            <div class="pds-contact-map-card" id="pds-contact-map-card" aria-hidden="true">
                <div class="pds-contact-map-header">
                    <div class="pds-contact-map-actions">
                        <a href="https://www.google.com/maps/search/?api=1&amp;query=60+Nguyen+Van+Thu%2C+Ward+Da+Kao%2C+District+1%2C+Ho+Chi+Minh+City%2C+Viet+Nam"
                           class="pds-contact-map-external-link"
                           target="_blank"
                           rel="noopener noreferrer">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <line x1="10" y1="14" x2="21" y2="3"></line>
                            </svg>
                            <span><?php esc_html_e( 'Mở Google Maps', 'jobscout' ); ?></span>
                        </a>
                        <button type="button"
                                class="pds-contact-map-close-btn"
                                id="pds-contact-map-close"
                                onclick="if(window.pdsCloseContactMap){window.pdsCloseContactMap(event);}"
                                aria-label="<?php esc_attr_e( 'Đóng bản đồ', 'jobscout' ); ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                            <span><?php esc_html_e( 'Đóng bản đồ', 'jobscout' ); ?></span>
                        </button>
                    </div>
                </div>

                <div class="pds-contact-map-frame-wrapper">
                    <iframe
                        id="pds-contact-map-iframe"
                        title="<?php esc_attr_e( 'Google Map - 60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City', 'jobscout' ); ?>"
                        data-src="https://maps.google.com/maps?q=60+Nguyen+Van+Thu,+Ward+Da+Kao,+District+1,+Ho+Chi+Minh+City,+Viet+Nam&amp;t=m&amp;z=16&amp;output=embed"
                        src="about:blank"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
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

<script>
(function () {
    function getEls() {
        return {
            triggerBtn: document.getElementById('pds-contact-map-trigger'),
            mapCard: document.getElementById('pds-contact-map-card'),
            mapIframe: document.getElementById('pds-contact-map-iframe')
        };
    }

    window.pdsOpenContactMap = function (e) {
        if (e && e.preventDefault) e.preventDefault();
        var els = getEls();
        if (!els.mapCard) return;

        if (els.mapIframe && els.mapIframe.getAttribute('data-src')) {
            if (!els.mapIframe.src || els.mapIframe.src === 'about:blank' || els.mapIframe.src.indexOf('about:blank') !== -1) {
                els.mapIframe.src = els.mapIframe.getAttribute('data-src');
            }
        }

        els.mapCard.classList.add('is-active');
        els.mapCard.style.display = 'block';
        els.mapCard.setAttribute('aria-hidden', 'false');

        if (els.triggerBtn) {
            els.triggerBtn.setAttribute('aria-expanded', 'true');
            els.triggerBtn.classList.add('is-active');
            var badgeLabel = els.triggerBtn.querySelector('.pds-contact-badge-label');
            if (badgeLabel) {
                badgeLabel.textContent = 'Bấm để ẩn bản đồ';
            }
        }
    };

    window.pdsCloseContactMap = function (e) {
        if (e && e.preventDefault) e.preventDefault();
        var els = getEls();
        if (!els.mapCard) return;

        els.mapCard.classList.remove('is-active');
        els.mapCard.style.display = 'none';
        els.mapCard.setAttribute('aria-hidden', 'true');

        if (els.triggerBtn) {
            els.triggerBtn.setAttribute('aria-expanded', 'false');
            els.triggerBtn.classList.remove('is-active');
            var badgeLabel = els.triggerBtn.querySelector('.pds-contact-badge-label');
            if (badgeLabel) {
                badgeLabel.textContent = 'Bấm để xem bản đồ';
            }
            els.triggerBtn.focus();
        }
    };

    window.pdsToggleContactMap = function (e) {
        var els = getEls();
        if (els.mapCard && (els.mapCard.classList.contains('is-active') || els.mapCard.style.display === 'block')) {
            window.pdsCloseContactMap(e);
        } else {
            window.pdsOpenContactMap(e);
        }
    };

    function attachListeners() {
        var triggerBtn = document.getElementById('pds-contact-map-trigger');
        var closeBtn = document.getElementById('pds-contact-map-close');

        if (triggerBtn) {
            triggerBtn.onclick = function (e) {
                window.pdsToggleContactMap(e);
            };
        }

        if (closeBtn) {
            closeBtn.onclick = function (e) {
                window.pdsCloseContactMap(e);
            };
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                var mapCard = document.getElementById('pds-contact-map-card');
                if (mapCard && (mapCard.classList.contains('is-active') || mapCard.style.display === 'block')) {
                    window.pdsCloseContactMap(e);
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attachListeners);
    } else {
        attachListeners();
    }
})();
</script>

<?php
get_footer();
