<?php
/**
 * Custom job submitted confirmation template.
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_post_types;

do_action( 'job_manager_job_submitted_content_before', $job );

$job = get_post( $job->ID );

if ( ! $job ) {
	echo '<div class="job-manager-message">' . esc_html__( 'Job listing submitted successfully.', 'jobscout' ) . '</div>';
	return;
}

$job_title     = get_the_title( $job->ID );
$job_permalink = get_permalink( $job->ID );
$is_published  = 'publish' === $job->post_status;
?>
<div class="pds-submit-success-card">
	<div class="pds-submit-success-icon" aria-hidden="true">
		<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
			<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
			<polyline points="22 4 12 14.01 9 11.01"></polyline>
		</svg>
	</div>
	<div class="pds-submit-success-content">
		<h3 class="pds-submit-success-title">
			<?php echo $is_published ? esc_html__( 'Đăng tin tuyển dụng thành công!', 'jobscout' ) : esc_html__( 'Tin tuyển dụng đã được gửi thành công!', 'jobscout' ); ?>
		</h3>
		<p class="pds-submit-success-desc">
			<?php if ( $is_published ) : ?>
				<?php printf( esc_html__( 'Tin tuyển dụng "%s" đã được xuất bản trực tiếp và hiển thị công khai trên website.', 'jobscout' ), '<strong>' . esc_html( $job_title ) . '</strong>' ); ?>
			<?php else : ?>
				<?php printf( esc_html__( 'Tin tuyển dụng "%s" đang chờ xét duyệt.', 'jobscout' ), '<strong>' . esc_html( $job_title ) . '</strong>' ); ?>
			<?php endif; ?>
		</p>
		<div class="pds-submit-success-actions">
			<?php if ( $is_published ) : ?>
				<a href="<?php echo esc_url( $job_permalink ); ?>" class="btn pds-btn-view-job">
					<?php esc_html_e( 'Xem tin vừa đăng', 'jobscout' ); ?>
				</a>
			<?php endif; ?>
			<a href="<?php echo esc_url( home_url( '/jobs/' ) ); ?>" class="btn pds-btn-all-jobs">
				<?php esc_html_e( 'Xem danh sách việc làm', 'jobscout' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/submit-job/' ) ); ?>" class="btn pds-btn-submit-more">
				<?php esc_html_e( 'Đăng tin khác', 'jobscout' ); ?>
			</a>
		</div>
	</div>
</div>
<?php
do_action( 'job_manager_job_submitted_content_after', sanitize_title( $job->post_status ), $job );
