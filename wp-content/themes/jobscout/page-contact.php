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

            <div class="pds-contact-hq-address-wrapper">
                <button type="button"
                        class="pds-contact-hq-address-btn"
                        id="pds-contact-map-trigger"
                        aria-expanded="false"
                        aria-controls="pds-contact-map-card"
                        title="<?php esc_attr_e( 'Click to view map', 'jobscout' ); ?>">
                    <span class="pds-contact-pin-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5-2.5 2.5-2.5 2.5z"/>
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

<script>
(function () {
    function initContactMap() {
        var hqSection = document.querySelector('.pds-contact-hq');
        if (!hqSection) return;

        var existingTrigger = document.getElementById('pds-contact-map-trigger');
        var existingCard = document.getElementById('pds-contact-map-card');

        // If widget is active, dynamically enhance .pds-contact-hq-address
        if (!existingTrigger) {
            var addressEl = hqSection.querySelector('.pds-contact-hq-address');
            if (addressEl) {
                var addressText = addressEl.textContent.trim();
                if (!addressText) {
                    addressText = '60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City, Viet Nam';
                }
                var encodedQuery = encodeURIComponent(addressText);
                var mapEmbedUrl = 'https://maps.google.com/maps?q=' + encodedQuery + '&t=m&z=16&output=embed';
                var mapExternalUrl = 'https://www.google.com/maps/search/?api=1&query=' + encodedQuery;

                var wrapper = document.createElement('div');
                wrapper.className = 'pds-contact-hq-address-wrapper';
                wrapper.innerHTML =
                    '<button type="button" class="pds-contact-hq-address-btn" id="pds-contact-map-trigger" aria-expanded="false" aria-controls="pds-contact-map-card" title="<?php echo esc_js( __( 'Click to view map', 'jobscout' ) ); ?>">' +
                        '<span class="pds-contact-pin-icon" aria-hidden="true">' +
                            '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">' +
                                '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>' +
                            '</svg>' +
                        '</span>' +
                        '<span class="pds-contact-address-text">' + addressText + '</span>' +
                        '<span class="pds-contact-address-badge">' +
                            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
                                '<polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>' +
                                '<line x1="8" y1="2" x2="8" y2="18"></line>' +
                                '<line x1="16" y1="6" x2="16" y2="22"></line>' +
                            '</svg>' +
                            '<span class="pds-contact-badge-label"><?php echo esc_js( __( 'Bấm để xem bản đồ', 'jobscout' ) ); ?></span>' +
                        '</span>' +
                    '</button>';

                var mapCard = document.createElement('div');
                mapCard.className = 'pds-contact-map-card';
                mapCard.id = 'pds-contact-map-card';
                mapCard.setAttribute('aria-hidden', 'true');
                mapCard.innerHTML =
                    '<div class="pds-contact-map-header">' +
                        '<div class="pds-contact-map-actions">' +
                            '<a href="' + mapExternalUrl + '" class="pds-contact-map-external-link" target="_blank" rel="noopener noreferrer">' +
                                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
                                    '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>' +
                                    '<polyline points="15 3 21 3 21 9"></polyline>' +
                                    '<line x1="10" y1="14" x2="21" y2="3"></line>' +
                                '</svg>' +
                                '<span><?php echo esc_js( __( 'Mở Google Maps', 'jobscout' ) ); ?></span>' +
                            '</a>' +
                            '<button type="button" class="pds-contact-map-close-btn" id="pds-contact-map-close" aria-label="<?php echo esc_js( __( 'Đóng bản đồ', 'jobscout' ) ); ?>">' +
                                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
                                    '<line x1="18" y1="6" x2="6" y2="18"></line>' +
                                    '<line x1="6" y1="6" x2="18" y2="18"></line>' +
                                '</svg>' +
                                '<span><?php echo esc_js( __( 'Đóng bản đồ', 'jobscout' ) ); ?></span>' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="pds-contact-map-frame-wrapper">' +
                        '<iframe id="pds-contact-map-iframe" title="<?php echo esc_js( __( 'Google Map', 'jobscout' ) ); ?>" data-src="' + mapEmbedUrl + '" src="about:blank" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>' +
                    '</div>';

                addressEl.parentNode.replaceChild(wrapper, addressEl);
                wrapper.parentNode.insertBefore(mapCard, wrapper.nextSibling);
            }
        }

        var triggerBtn = document.getElementById('pds-contact-map-trigger');
        var card = document.getElementById('pds-contact-map-card');
        var iframe = document.getElementById('pds-contact-map-iframe');
        var closeBtn = document.getElementById('pds-contact-map-close');

        if (!triggerBtn || !card) return;

        window.pdsOpenContactMap = function (e) {
            if (e && e.preventDefault) e.preventDefault();
            if (iframe && iframe.getAttribute('data-src')) {
                if (!iframe.src || iframe.src === 'about:blank' || iframe.src.indexOf('about:blank') !== -1) {
                    iframe.src = iframe.getAttribute('data-src');
                }
            }
            card.classList.add('is-active');
            card.style.display = 'block';
            card.setAttribute('aria-hidden', 'false');
            triggerBtn.setAttribute('aria-expanded', 'true');
            triggerBtn.classList.add('is-active');
            var badgeLabel = triggerBtn.querySelector('.pds-contact-badge-label');
            if (badgeLabel) {
                badgeLabel.textContent = '<?php echo esc_js( __( 'Bấm để ẩn bản đồ', 'jobscout' ) ); ?>';
            }
        };

        window.pdsCloseContactMap = function (e) {
            if (e && e.preventDefault) e.preventDefault();
            card.classList.remove('is-active');
            card.style.display = 'none';
            card.setAttribute('aria-hidden', 'true');
            triggerBtn.setAttribute('aria-expanded', 'false');
            triggerBtn.classList.remove('is-active');
            var badgeLabel = triggerBtn.querySelector('.pds-contact-badge-label');
            if (badgeLabel) {
                badgeLabel.textContent = '<?php echo esc_js( __( 'Bấm để xem bản đồ', 'jobscout' ) ); ?>';
            }
            triggerBtn.focus();
        };

        window.pdsToggleContactMap = function (e) {
            if (card.classList.contains('is-active') || card.style.display === 'block') {
                window.pdsCloseContactMap(e);
            } else {
                window.pdsOpenContactMap(e);
            }
        };

        triggerBtn.onclick = function (e) {
            window.pdsToggleContactMap(e);
        };

        if (closeBtn) {
            closeBtn.onclick = function (e) {
                window.pdsCloseContactMap(e);
            };
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && (card.classList.contains('is-active') || card.style.display === 'block')) {
                window.pdsCloseContactMap(e);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initContactMap);
    } else {
        initContactMap();
    }
})();
</script>

<?php
get_footer();
