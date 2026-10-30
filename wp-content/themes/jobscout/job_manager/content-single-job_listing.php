<?php
/**
 * Single job listing template override for JobScout
 *
 * Removes the inline application form below job description,
 * as the application form is presented exclusively via the modal popup.
 *
 * @package JobScout
 * @version 1.37.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

if ( ( ! post_password_required( $post ) || is_super_admin() ) && job_manager_user_can_view_job_listing( $post->ID ) ) : ?>
	<div class="single_job_listing">
		<?php if ( get_option( 'job_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
			<div class="job-manager-info"><?php _e( 'This listing has expired.', 'wp-job-manager' ); ?></div>
		<?php else : ?>
			<?php
				/**
				 * single_job_listing_start hook
				 *
				 * @hooked job_listing_meta_display - 20
				 * @hooked job_listing_company_display - 30
				 */
				do_action( 'single_job_listing_start' );
			?>

			<div class="job_description">
				<?php wpjm_the_job_description(); ?>
			</div>

			<?php
				/**
				 * single_job_listing_end hook
				 */
				do_action( 'single_job_listing_end' );
			?>
		<?php endif; ?>
	</div>
<?php else : ?>

	<?php get_job_manager_template_part( 'access-denied', 'single-job_listing' ); ?>

<?php endif; ?>

