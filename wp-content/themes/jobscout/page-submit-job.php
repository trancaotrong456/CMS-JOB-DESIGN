<?php
/**
 * Purpose-built presentation for the WP Job Manager Submit Job page.
 *
 * WordPress selects this file for the "submit-job" page by slug. The page
 * content remains responsible for rendering the [submit_job_form] shortcode.
 *
 * @package JobScout
 */

add_filter( 'body_class', function( $classes ) {
	$classes[] = 'pds-submit-job-body';
	return $classes;
} );

add_action( 'wp_enqueue_scripts', function() {
	$style_path = get_template_directory() . '/assets/css/submit-job.css';

	wp_enqueue_style(
		'pds-submit-job-style',
		get_template_directory_uri() . '/assets/css/submit-job.css',
		array( 'jobscout-home' ),
		file_exists( $style_path ) ? (string) filemtime( $style_path ) : JOBSCOUT_THEME_VERSION
	);
}, 20 );

// Preserve the shared header and footer while removing the generic sidebar layout.
remove_action( 'jobscout_content', 'jobscout_content_start' );
remove_action( 'jobscout_before_footer', 'jobscout_content_end', 20 );

get_header();
?>

<main id="submit-job-content" class="home-page pds-submit-job-page">
	<section class="home-section pds-submit-job-section" aria-labelledby="submit-job-title">
		<div class="home-container">
			<?php if ( is_active_sidebar( 'submit-job-header' ) ) : ?>
				<?php dynamic_sidebar( 'submit-job-header' ); ?>
			<?php else : ?>
				<h1 id="submit-job-title" class="home-section-title"><?php esc_html_e( 'POST A JOB', 'jobscout' ); ?></h1>
				<p class="pds-submit-job-description">
					<?php esc_html_e( 'Find the right talent for your hospitality business.', 'jobscout' ); ?>
				</p>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'submit-job-content' ) ) : ?>
				<?php dynamic_sidebar( 'submit-job-content' ); ?>
			<?php else : ?>
				<section class="home-job-card pds-submit-job-panel" aria-labelledby="submit-job-details-title">
					<h2 id="submit-job-details-title" class="pds-submit-job-group-title"><?php esc_html_e( 'JOB DETAILS', 'jobscout' ); ?></h2>
					<div class="pds-submit-job-form-content">
						<?php
						while ( have_posts() ) :
							the_post();
							the_content();
						endwhile;
						?>
					</div>
				</section>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/pds-newsletter' ); ?>

<?php get_footer(); ?>