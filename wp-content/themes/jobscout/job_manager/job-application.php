<?php
/**
 * Job Application Template Override for JobScout
 *
 * Implements the online application form with personal details,
 * CV upload (.pdf, .doc, .docx), client & server validation,
 * and recruiter email notification with CV attachment.
 *
 * @package    JobScout
 * @subpackage PDS / Application Form
 * @author     Trần Văn Trọng
 * @branch     tran-van-trong/job-detail
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$job_id = get_the_ID();
$apply  = get_the_job_application_method();
?>
<div class="job_application application pds-job-application-wrapper">
    <?php if ( $apply ) : ?>
        <?php do_action( 'job_application_start', $apply ); ?>
    <?php endif; ?>

    <div class="pds-application-card" id="pds-application-card">
        <div class="pds-application-header">
            <h3 class="pds-application-title">
                <?php esc_html_e( 'Apply For This Position', 'jobscout' ); ?>
            </h3>
            <p class="pds-application-subtitle">
                <?php esc_html_e( 'Please complete the form below and attach your CV to apply.', 'jobscout' ); ?>
            </p>
        </div>

        <!-- Alert messages container -->
        <div class="pds-app-alert pds-app-alert--error" id="pds-app-alert-error" style="display: none;" role="alert">
            <svg class="pds-app-alert-icon" viewBox="0 0 20 20" width="20" height="20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div class="pds-app-alert-message" id="pds-app-alert-error-msg"></div>
        </div>

        <div class="pds-app-alert pds-app-alert--success" id="pds-app-alert-success" style="display: none;" role="status">
            <svg class="pds-app-alert-icon" viewBox="0 0 20 20" width="20" height="20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="pds-app-alert-message" id="pds-app-alert-success-msg"></div>
        </div>

        <!-- Application Form -->
        <form id="pds-job-application-form" class="pds-job-application-form" method="post" enctype="multipart/form-data" novalidate>

            <?php wp_nonce_field( 'pds_job_apply_nonce', 'pds_job_apply_security' ); ?>
            <input type="hidden" name="action" value="pds_submit_job_application">
            <input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>">
            <!-- Honeypot -->
            <input type="text" name="pds_hp" class="pds-hp-field" style="display: none !important;" tabindex="-1" autocomplete="off">

            <div class="pds-form-grid">
                <!-- Full Name -->
                <div class="pds-form-group pds-form-group--half">
                    <label for="pds_candidate_name" class="pds-form-label">
                        <?php esc_html_e( 'Full Name', 'jobscout' ); ?> <span class="pds-req" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="text"
                        name="candidate_name"
                        id="pds_candidate_name"
                        class="pds-form-input"
                        placeholder="<?php esc_attr_e( 'e.g. Tran Van Trong', 'jobscout' ); ?>"
                        required
                        aria-required="true"
                    >
                    <span class="pds-field-error" id="err-candidate_name"></span>
                </div>

                <!-- Email -->
                <div class="pds-form-group pds-form-group--half">
                    <label for="pds_candidate_email" class="pds-form-label">
                        <?php esc_html_e( 'Email Address', 'jobscout' ); ?> <span class="pds-req" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="email"
                        name="candidate_email"
                        id="pds_candidate_email"
                        class="pds-form-input"
                        placeholder="<?php esc_attr_e( 'e.g. trong@example.com', 'jobscout' ); ?>"
                        required
                        aria-required="true"
                    >
                    <span class="pds-field-error" id="err-candidate_email"></span>
                </div>
            </div>

            <!-- Phone Number -->
            <div class="pds-form-group">
                <label for="pds_candidate_phone" class="pds-form-label">
                    <?php esc_html_e( 'Phone Number', 'jobscout' ); ?> <span class="pds-req" aria-hidden="true">*</span>
                </label>
                <input
                    type="tel"
                    name="candidate_phone"
                    id="pds_candidate_phone"
                    class="pds-form-input"
                    placeholder="<?php esc_attr_e( 'e.g. +84 901 234 567', 'jobscout' ); ?>"
                    required
                    aria-required="true"
                >
                <span class="pds-field-error" id="err-candidate_phone"></span>
            </div>

            <!-- Cover Letter / Message -->
            <div class="pds-form-group">
                <label for="pds_candidate_message" class="pds-form-label">
                    <?php esc_html_e( 'Cover Letter / Message', 'jobscout' ); ?>
                    <span class="pds-optional">(<?php esc_html_e( 'Optional', 'jobscout' ); ?>)</span>
                </label>
                <textarea
                    name="candidate_message"
                    id="pds_candidate_message"
                    class="pds-form-textarea"
                    rows="4"
                    placeholder="<?php esc_attr_e( 'Introduce yourself, your experience, and why you are the best fit for this role...', 'jobscout' ); ?>"
                ></textarea>
            </div>

            <!-- CV Upload -->
            <div class="pds-form-group pds-upload-group">
                <label class="pds-form-label" id="pds-cv-label">
                    <?php esc_html_e( 'Upload CV / Resume', 'jobscout' ); ?> <span class="pds-req" aria-hidden="true">*</span>
                </label>

                <!-- Hidden file input -->
                <input
                    type="file"
                    name="candidate_cv"
                    id="pds_candidate_cv"
                    class="pds-file-input-hidden"
                    accept=".pdf,.doc,.docx"
                    required
                    aria-required="true"
                    aria-labelledby="pds-cv-label"
                >

                <!-- Dropzone Box -->
                <div class="pds-cv-dropzone" id="pds-cv-dropzone" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Upload CV file', 'jobscout' ); ?>">
                    <div class="pds-cv-dropzone-content">
                        <svg class="pds-cv-upload-icon" viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <p class="pds-cv-dropzone-text">
                            <strong><?php esc_html_e( 'Click to browse', 'jobscout' ); ?></strong>
                            <span><?php esc_html_e( 'or drag and drop your resume here', 'jobscout' ); ?></span>
                        </p>
                        <span class="pds-cv-dropzone-hint">
                            <?php esc_html_e( 'Supported formats: PDF, DOC, DOCX (Max 10MB)', 'jobscout' ); ?>
                        </span>
                    </div>
                </div>

                <!-- Selected File Card (Hidden by default) -->
                <div class="pds-cv-selected-card" id="pds-cv-selected-card" style="display: none;">
                    <div class="pds-cv-file-info">
                        <span class="pds-cv-file-icon" id="pds-cv-ext-badge">PDF</span>
                        <div class="pds-cv-file-meta">
                            <span class="pds-cv-file-name" id="pds-cv-file-name">filename.pdf</span>
                            <span class="pds-cv-file-size" id="pds-cv-file-size">1.2 MB</span>
                        </div>
                    </div>
                    <button type="button" class="pds-cv-remove-btn" id="pds-cv-remove-btn" aria-label="<?php esc_attr_e( 'Remove file', 'jobscout' ); ?>">
                        &times;
                    </button>
                </div>

                <span class="pds-field-error" id="err-candidate_cv"></span>
            </div>

            <!-- Submit Button Area -->
            <div class="pds-form-actions">
                <button type="submit" class="pds-btn-submit-app" id="pds-btn-submit-app">
                    <span class="pds-btn-text"><?php esc_html_e( 'SUBMIT APPLICATION', 'jobscout' ); ?></span>
                    <span class="pds-btn-spinner" aria-hidden="true" style="display: none;">
                        <svg class="pds-spinner-svg" viewBox="0 0 24 24" width="18" height="18">
                            <circle class="pds-spinner-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"/>
                            <path class="pds-spinner-head" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                    </span>
                </button>
            </div>

        </form>
    </div>

    <?php if ( $apply ) : ?>
        <div class="application_details" style="display: none !important;">
            <?php do_action( 'job_manager_application_details_' . $apply->type, $apply ); ?>
        </div>
        <?php do_action( 'job_application_end', $apply ); ?>
    <?php endif; ?>
</div>
