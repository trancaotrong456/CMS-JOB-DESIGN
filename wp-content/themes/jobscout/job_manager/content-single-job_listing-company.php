<?php
/**
 * Single view Company information box
 *
 * Hooked into single_job_listing_start priority 30
 *
 * @version 1.32.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! get_the_company_name() ) {
	return;
}

$company_name = get_the_company_name();
$logo_url     = get_the_company_logo();
if ( empty( $logo_url ) && has_post_thumbnail() ) {
    $logo_url = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
}
if ( empty( $logo_url ) ) {
    $logo_url = apply_filters( 'job_manager_default_company_logo', JOB_MANAGER_PLUGIN_URL . '/assets/images/company.png' );
}
?>
<div class="company pds-single-job-company" style="display: flex !important; align-items: center !important; gap: 24px !important; padding: 18px 24px !important; margin: 24px 0 !important; background: #ffffff !important; border: 1px solid #e5e7eb !important; border-radius: 8px !important; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important; position: relative !important; clear: both !important; box-sizing: border-box !important; width: 100% !important;">
    <div class="pds-company-logo-wrap" style="flex: 0 0 64px !important; width: 64px !important; height: 64px !important; min-width: 64px !important; max-width: 64px !important; margin: 0 24px 0 0 !important; display: flex !important; align-items: center !important; justify-content: center !important; background: #f8fafc !important; border: 1px solid #e2e8f0 !important; border-radius: 8px !important; padding: 6px !important; box-sizing: border-box !important;">
        <img class="company_logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $company_name ); ?>" style="position: static !important; float: none !important; margin: 0 !important; padding: 0 !important; width: 52px !important; height: 52px !important; max-width: 52px !important; max-height: 52px !important; object-fit: contain !important; display: block !important; border: none !important; background: transparent !important; box-shadow: none !important;" />
    </div>

    <div class="company_header" style="flex: 1 1 auto !important; margin: 0 !important; padding: 0 !important; min-height: auto !important;">
        <p class="name" style="margin: 0 !important; padding: 0 !important; font-size: 20px !important; font-weight: 700 !important; line-height: 1.3 !important; color: #1e293b !important; width: auto !important;">
            <strong style="font-size: 20px !important; font-weight: 700 !important; color: #1e293b !important; display: inline-block !important; margin-right: 12px !important;"><?php echo esc_html( $company_name ); ?></strong>
            <?php if ( $website = get_the_company_website() ) : ?>
                <a class="website" href="<?php echo esc_url( $website ); ?>" rel="nofollow" style="display: inline-block !important; font-size: 13px !important; font-weight: 600 !important; color: #f2761b !important; text-decoration: none !important;"><?php esc_html_e( 'Website', 'jobscout' ); ?></a>
            <?php endif; ?>
            <?php the_company_twitter(); ?>
        </p>
        <?php the_company_tagline( '<p class="tagline" style="margin: 6px 0 0 !important; padding: 0 !important; font-size: 14px !important; color: #64748b !important; line-height: 1.4 !important; width: auto !important;">', '</p>' ); ?>
    </div>

    <?php the_company_video(); ?>
</div>
