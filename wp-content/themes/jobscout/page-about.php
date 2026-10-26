<?php
/**
 * page-about.php
 * Hero + Newsletter giữ nguyên, phần giữa lấy từ editor (the_content).
 *
 * @package JobScout
 */

add_filter( 'body_class', function( $classes ) {
    $classes[] = 'pds-about-page-body';
    return $classes;
} );

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'pds-about-style',
        get_template_directory_uri() . '/assets/css/about.css',
        array( 'jobscout' ),
        JOBSCOUT_THEME_VERSION
    );
}, 20 );

remove_action( 'jobscout_content', 'jobscout_content_start' );
remove_action( 'jobscout_before_footer', 'jobscout_content_end', 20 );

get_header();
?>

<div class="pds-about-page">

        <!-- HERO BANNER -->
    <?php
    $about_id   = get_queried_object_id();
    $hero_img   = get_the_post_thumbnail_url( $about_id, 'full' );
    $hero_text  = has_excerpt( $about_id ) ? get_the_excerpt( $about_id ) : "SHARE “OMOTENASHI”\nWITH THE WORLD";
    ?>
    <section class="pds-about-hero" aria-labelledby="pds-about-hero-title"
        <?php if ( $hero_img ) : ?>style="background-image:url('<?php echo esc_url( $hero_img ); ?>');"<?php endif; ?>>
        <div class="pds-about-hero-overlay"></div>
        <div class="pds-about-hero-inner">
            <h1 id="pds-about-hero-title" class="pds-about-hero-title">
                <?php echo nl2br( esc_html( $hero_text ) ); ?>
            </h1>
        </div>
    </section>

       <!-- NỘI DUNG ADMIN CHỈNH TRONG EDITOR -->
    <section class="pds-about-editable">
        <div class="pds-about-container">
            <?php
            while ( have_posts() ) :
                the_post();
                ?>
                <h2 class="pds-about-main-title"><?php the_title(); ?></h2>
                <?php
            endwhile;
            ?>

            <div class="pds-about-content">
                <?php
                if ( is_active_sidebar( 'about-page-content' ) ) {
                    dynamic_sidebar( 'about-page-content' );
                }
                ?>
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