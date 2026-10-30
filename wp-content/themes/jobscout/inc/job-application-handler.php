<?php
/**
 * Job Application Handler & Email Framework
 *
 * Handles job application submission, CV file validation & upload,
 * application data persistence, recruiter email notification via wp_mail(),
 * and SMTP integration.
 *
 * @package JobScout
 * @subpackage PDS / Application System
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -----------------------------------------------------------------------------
// 1. Register Custom Post Type for Job Applications
// -----------------------------------------------------------------------------
function pds_register_job_application_cpt() {
    $labels = array(
        'name'               => __( 'Job Applications', 'jobscout' ),
        'singular_name'      => __( 'Job Application', 'jobscout' ),
        'menu_name'          => __( 'Applications', 'jobscout' ),
        'all_items'          => __( 'All Applications', 'jobscout' ),
        'view_item'          => __( 'View Application', 'jobscout' ),
        'edit_item'          => __( 'Edit Application', 'jobscout' ),
        'search_items'       => __( 'Search Applications', 'jobscout' ),
        'not_found'          => __( 'No applications found', 'jobscout' ),
        'not_found_in_trash' => __( 'No applications found in trash', 'jobscout' ),
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => 'edit.php?post_type=job_listing',
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'supports'            => array( 'title', 'custom-fields' ),
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
    );

    register_post_type( 'job_application', $args );
}
add_action( 'init', 'pds_register_job_application_cpt' );

// Custom columns in wp-admin for job_application
add_filter( 'manage_job_application_posts_columns', function( $columns ) {
    $new_cols = array(
        'cb'               => $columns['cb'],
        'title'            => __( 'Candidate', 'jobscout' ),
        'app_job'          => __( 'Applied Job', 'jobscout' ),
        'app_email'        => __( 'Email', 'jobscout' ),
        'app_phone'        => __( 'Phone', 'jobscout' ),
        'app_cv'           => __( 'CV File', 'jobscout' ),
        'date'             => __( 'Date', 'jobscout' ),
    );
    return $new_cols;
} );

add_action( 'manage_job_application_posts_custom_column', function( $column, $post_id ) {
    switch ( $column ) {
        case 'app_job':
            $job_id = get_post_meta( $post_id, '_job_id', true );
            if ( $job_id ) {
                echo '<a href="' . esc_url( get_edit_post_link( $job_id ) ) . '">' . esc_html( get_the_title( $job_id ) ) . '</a>';
            } else {
                echo '—';
            }
            break;
        case 'app_email':
            $email = get_post_meta( $post_id, '_candidate_email', true );
            echo $email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '—';
            break;
        case 'app_phone':
            $phone = get_post_meta( $post_id, '_candidate_phone', true );
            echo $phone ? esc_html( $phone ) : '—';
            break;
        case 'app_cv':
            $cv_url = get_post_meta( $post_id, '_cv_file_url', true );
            $cv_name = get_post_meta( $post_id, '_cv_file_name', true );
            if ( $cv_url ) {
                echo '<a href="' . esc_url( $cv_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $cv_name ?: __( 'Download CV', 'jobscout' ) ) . '</a>';
            } else {
                echo '—';
            }
            break;
    }
}, 10, 2 );

// -----------------------------------------------------------------------------
// 2. SMTP Integration via phpmailer_init
// -----------------------------------------------------------------------------
/**
 * Configures PHPMailer with SMTP settings if defined via environment variables,
 * wp-config constants, or WordPress options.
 */
function pds_configure_smtp( $phpmailer ) {
    // 1. Check constants / env vars first
    $smtp_host = defined( 'SMTP_HOST' ) ? SMTP_HOST : getenv( 'SMTP_HOST' );
    $smtp_port = defined( 'SMTP_PORT' ) ? SMTP_PORT : getenv( 'SMTP_PORT' );
    $smtp_user = defined( 'SMTP_USER' ) ? SMTP_USER : getenv( 'SMTP_USER' );
    $smtp_pass = defined( 'SMTP_PASS' ) ? SMTP_PASS : getenv( 'SMTP_PASS' );
    $smtp_secure = defined( 'SMTP_SECURE' ) ? SMTP_SECURE : getenv( 'SMTP_SECURE' );
    $from_email  = defined( 'SMTP_FROM' ) ? SMTP_FROM : getenv( 'SMTP_FROM' );
    $from_name   = defined( 'SMTP_FROM_NAME' ) ? SMTP_FROM_NAME : getenv( 'SMTP_FROM_NAME' );

    // 2. Fallback to WordPress options if constants are not set
    if ( empty( $smtp_host ) ) {
        $enabled = get_option( 'pds_smtp_enabled', '0' );
        if ( '1' === (string) $enabled ) {
            $smtp_host   = get_option( 'pds_smtp_host', '' );
            $smtp_port   = get_option( 'pds_smtp_port', '587' );
            $smtp_user   = get_option( 'pds_smtp_username', '' );
            $smtp_pass   = get_option( 'pds_smtp_password', '' );
            $smtp_secure = get_option( 'pds_smtp_encryption', 'tls' );
            $from_email  = get_option( 'pds_smtp_from_email', '' );
            $from_name   = get_option( 'pds_smtp_from_name', '' );
        }
    }

    if ( ! empty( $smtp_host ) ) {
        $phpmailer->isSMTP();
        $phpmailer->Host = $smtp_host;
        $phpmailer->Port = ! empty( $smtp_port ) ? (int) $smtp_port : 587;

        if ( ! empty( $smtp_user ) ) {
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = $smtp_user;
            $phpmailer->Password = $smtp_pass;
        } else {
            $phpmailer->SMTPAuth = false;
        }

        if ( 'ssl' === strtolower( (string) $smtp_secure ) ) {
            $phpmailer->SMTPSecure = 'ssl';
        } elseif ( 'tls' === strtolower( (string) $smtp_secure ) ) {
            $phpmailer->SMTPSecure = 'tls';
        } else {
            $phpmailer->SMTPSecure = '';
            $phpmailer->SMTPAutoTLS = false;
        }

        if ( ! empty( $from_email ) && is_email( $from_email ) ) {
            $phpmailer->From = $from_email;
            $phpmailer->FromName = ! empty( $from_name ) ? $from_name : get_bloginfo( 'name' );
        }
    }
}
add_action( 'phpmailer_init', 'pds_configure_smtp' );

// Log mail failures
add_action( 'wp_mail_failed', function( $error ) {
    if ( is_wp_error( $error ) ) {
        $msg = sprintf(
            '[%s] wp_mail failed: %s | Data: %s',
            current_time( 'mysql' ),
            $error->get_error_message(),
            wp_json_encode( $error->get_error_data() )
        );
        error_log( $msg );

        // Also save to a local log file inside uploads for debugging
        $upload_dir = wp_upload_dir();
        $log_dir = trailingslashit( $upload_dir['basedir'] ) . 'pds-logs';
        if ( ! file_exists( $log_dir ) ) {
            wp_mkdir_p( $log_dir );
            file_put_contents( $log_dir . '/.htaccess', "Deny from all\n" );
            file_put_contents( $log_dir . '/index.php', "<?php // Silence\n" );
        }
        @file_put_contents( $log_dir . '/mail-errors.log', $msg . PHP_EOL, FILE_APPEND );
    }
} );

// -----------------------------------------------------------------------------
// 3. Helper: Get Recruiter Email for a Job
// -----------------------------------------------------------------------------
function pds_get_job_recruiter_email( $job_id ) {
    // 1. Meta _application
    $app_meta = get_post_meta( $job_id, '_application', true );
    if ( ! empty( $app_meta ) && is_email( $app_meta ) ) {
        return sanitize_email( $app_meta );
    }

    // 2. Author email
    $post = get_post( $job_id );
    if ( $post && $post->post_author ) {
        $author_email = get_the_author_meta( 'user_email', $post->post_author );
        if ( ! empty( $author_email ) && is_email( $author_email ) ) {
            return sanitize_email( $author_email );
        }
    }

    // 3. Customizer / Option recruiter email
    $hr_email = get_option( 'pds_recruiter_email', '' );
    if ( ! empty( $hr_email ) && is_email( $hr_email ) ) {
        return sanitize_email( $hr_email );
    }

    // 4. Fallback to site admin_email
    return sanitize_email( get_option( 'admin_email' ) );
}

// -----------------------------------------------------------------------------
// 4. Secure File Upload Handler
// -----------------------------------------------------------------------------
function pds_handle_cv_upload( $file, $job_id ) {
    if ( empty( $file ) || ! isset( $file['error'] ) || UPLOAD_ERR_OK !== $file['error'] ) {
        return new WP_Error( 'upload_error', __( 'File upload failed. Please try again.', 'jobscout' ) );
    }

    // Size limit: 10MB
    $max_size = 10 * 1024 * 1024;
    if ( $file['size'] > $max_size ) {
        return new WP_Error( 'file_too_large', __( 'CV file size must be less than 10MB.', 'jobscout' ) );
    }

    // File extension check
    $file_name = sanitize_file_name( $file['name'] );
    $ext = strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) );
    $allowed_exts = array( 'pdf', 'doc', 'docx' );

    if ( ! in_array( $ext, $allowed_exts, true ) ) {
        return new WP_Error( 'invalid_extension', __( 'Invalid file format. Only .pdf, .doc, and .docx files are accepted.', 'jobscout' ) );
    }

    // Real MIME type check using finfo
    $tmp_path = $file['tmp_name'];
    $real_mime = '';
    if ( function_exists( 'finfo_open' ) ) {
        $finfo = finfo_open( FILEINFO_MIME_TYPE );
        if ( $finfo ) {
            $real_mime = finfo_file( $finfo, $tmp_path );
            finfo_close( $finfo );
        }
    } elseif ( function_exists( 'mime_content_type' ) ) {
        $real_mime = mime_content_type( $tmp_path );
    }

    $allowed_mimes = array(
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/octet-stream', // Some Windows systems send octet-stream for doc/docx
        'application/x-zip',
    );

    // Double check that it is not PHP or executable in disguise
    $content_sample = file_get_contents( $tmp_path, false, null, 0, 512 );
    if ( false !== stripos( $content_sample, '<?php' ) || false !== stripos( $content_sample, '<script' ) ) {
        return new WP_Error( 'security_violation', __( 'The uploaded file contains unsafe content.', 'jobscout' ) );
    }

    if ( ! empty( $real_mime ) && ! in_array( $real_mime, $allowed_mimes, true ) ) {
        return new WP_Error( 'invalid_mime', __( 'The file content does not match an allowed document type.', 'jobscout' ) );
    }

    // Target upload directory: uploads/job-applications/{year}/{month}
    $upload_dir = wp_upload_dir();
    $year_month = date( 'Y/m' );
    $target_dir = trailingslashit( $upload_dir['basedir'] ) . 'job-applications/' . $year_month;
    $target_url = trailingslashit( $upload_dir['baseurl'] ) . 'job-applications/' . $year_month;

    if ( ! file_exists( $target_dir ) ) {
        wp_mkdir_p( $target_dir );
    }

    // Secure the parent job-applications directory with .htaccess and index.php
    $base_job_app_dir = trailingslashit( $upload_dir['basedir'] ) . 'job-applications';
    $htaccess_path = $base_job_app_dir . '/.htaccess';
    if ( ! file_exists( $htaccess_path ) ) {
        $htaccess_rules = "# Protect CV uploads\n<FilesMatch \"\.(php|phtml|php3|php4|php5|php7|phps|pl|py|cgi|sh)$\">\n    Deny from all\n</FilesMatch>\nOptions -Indexes\n";
        @file_put_contents( $htaccess_path, $htaccess_rules );
    }
    $index_path = $base_job_app_dir . '/index.php';
    if ( ! file_exists( $index_path ) ) {
        @file_put_contents( $index_path, "<?php // Silence is golden.\n" );
    }

    // Generate a unique, safe filename
    $unique_prefix = 'cv-job' . absint( $job_id ) . '-' . date( 'Ymd-His' ) . '-' . wp_generate_password( 8, false );
    $saved_filename = $unique_prefix . '.' . $ext;
    $destination = trailingslashit( $target_dir ) . $saved_filename;

    $moved = @move_uploaded_file( $tmp_path, $destination );
    if ( ! $moved && ( php_sapi_name() === 'cli' || defined( 'WP_CLI' ) ) ) {
        $moved = @copy( $tmp_path, $destination );
    }
    if ( ! $moved ) {
        return new WP_Error( 'save_error', __( 'Failed to save the uploaded file to server storage.', 'jobscout' ) );
    }

    return array(
        'file_name' => $file_name,
        'file_path' => $destination,
        'file_url'  => trailingslashit( $target_url ) . $saved_filename,
    );
}

// -----------------------------------------------------------------------------
// 5. AJAX Application Submission Endpoint
// -----------------------------------------------------------------------------
function pds_ajax_submit_job_application() {
    // 1. Nonce validation
    if ( ! check_ajax_referer( 'pds_job_apply_nonce', 'security', false ) ) {
        wp_send_json_error( array(
            'message' => __( 'Security verification failed. Please refresh the page and try again.', 'jobscout' ),
        ), 403 );
        exit;
    }

    // 2. Anti-spam honeypot
    if ( ! empty( $_POST['pds_hp'] ) ) {
        wp_send_json_error( array(
            'message' => __( 'Spam detected.', 'jobscout' ),
        ), 400 );
        exit;
    }

    // 3. Sanitize & validate required fields
    $candidate_name  = isset( $_POST['candidate_name'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_name'] ) ) : '';
    $candidate_email = isset( $_POST['candidate_email'] ) ? sanitize_email( wp_unslash( $_POST['candidate_email'] ) ) : '';
    $candidate_phone = isset( $_POST['candidate_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_phone'] ) ) : '';
    $cover_letter    = isset( $_POST['candidate_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['candidate_message'] ) ) : '';
    $job_id          = isset( $_POST['job_id'] ) ? absint( $_POST['job_id'] ) : 0;

    $errors = array();

    if ( empty( $candidate_name ) ) {
        $errors['candidate_name'] = __( 'Please enter your full name.', 'jobscout' );
    }

    if ( empty( $candidate_email ) || ! is_email( $candidate_email ) ) {
        $errors['candidate_email'] = __( 'Please enter a valid email address.', 'jobscout' );
    }

    if ( empty( $candidate_phone ) ) {
        $errors['candidate_phone'] = __( 'Please enter your phone number.', 'jobscout' );
    }

    // Validate Job ID
    if ( ! $job_id || 'job_listing' !== get_post_type( $job_id ) ) {
        $errors['job_id'] = __( 'Invalid job listing specified.', 'jobscout' );
    }

    // Validate CV upload presence
    if ( empty( $_FILES['candidate_cv'] ) || UPLOAD_ERR_NO_FILE === $_FILES['candidate_cv']['error'] ) {
        $errors['candidate_cv'] = __( 'Please upload your CV (.pdf, .doc, or .docx).', 'jobscout' );
    }

    if ( ! empty( $errors ) ) {
        wp_send_json_error( array(
            'message' => __( 'Please fill in all required fields correctly.', 'jobscout' ),
            'errors'  => $errors,
        ), 422 );
        exit;
    }

    // 4. Process CV upload
    $upload_result = pds_handle_cv_upload( $_FILES['candidate_cv'], $job_id );
    if ( is_wp_error( $upload_result ) ) {
        wp_send_json_error( array(
            'message' => $upload_result->get_error_message(),
            'errors'  => array( 'candidate_cv' => $upload_result->get_error_message() ),
        ), 400 );
        exit;
    }

    $cv_name = $upload_result['file_name'];
    $cv_path = $upload_result['file_path'];
    $cv_url  = $upload_result['file_url'];

    // 5. Retrieve Job Details & Recruiter Email
    $job_title = get_the_title( $job_id );
    $job_url   = get_permalink( $job_id );
    $company   = get_post_meta( $job_id, '_company_name', true );
    $recruiter_email = pds_get_job_recruiter_email( $job_id );
    $app_date = current_time( 'mysql' );
    $formatted_date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $app_date ) );

    // 6. Save Application to Database
    $app_post_id = wp_insert_post( array(
        'post_title'   => sprintf( '%s - %s', $candidate_name, $job_title ),
        'post_type'    => 'job_application',
        'post_status'  => 'publish',
        'post_parent'  => $job_id,
        'post_date'    => $app_date,
    ), true );

    if ( ! is_wp_error( $app_post_id ) ) {
        update_post_meta( $app_post_id, '_job_id', $job_id );
        update_post_meta( $app_post_id, '_candidate_name', $candidate_name );
        update_post_meta( $app_post_id, '_candidate_email', $candidate_email );
        update_post_meta( $app_post_id, '_candidate_phone', $candidate_phone );
        update_post_meta( $app_post_id, '_cover_letter', $cover_letter );
        update_post_meta( $app_post_id, '_cv_file_name', $cv_name );
        update_post_meta( $app_post_id, '_cv_file_path', $cv_path );
        update_post_meta( $app_post_id, '_cv_file_url', $cv_url );
        update_post_meta( $app_post_id, '_application_date', $app_date );
    }

    // 7. Compose Email Notification
    $subject = sprintf( __( '[Application] New Candidate for %s: %s', 'jobscout' ), $job_title, $candidate_name );

    $site_name = get_bloginfo( 'name' );
    $admin_email = get_option( 'admin_email' );

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        sprintf( 'From: %s <%s>', $site_name, $admin_email ),
        sprintf( 'Reply-To: %s <%s>', $candidate_name, $candidate_email ),
    );

    $attachments = array();
    if ( file_exists( $cv_path ) ) {
        $attachments[] = $cv_path;
    }

    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo esc_html( $subject ); ?></title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f7f8f9; }
            .email-wrapper { max-width: 600px; margin: 24px auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 4px; overflow: hidden; }
            .email-header { background: #f2761b; color: #ffffff; padding: 24px 30px; }
            .email-header h1 { margin: 0; font-size: 20px; font-weight: 700; text-transform: uppercase; }
            .email-body { padding: 30px; }
            .info-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
            .info-table th { text-align: left; padding: 10px 12px; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-size: 13px; color: #6b7280; width: 35%; }
            .info-table td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 14px; color: #111827; }
            .cover-letter-box { background: #f9fafb; border-left: 4px solid #f2761b; padding: 16px 20px; margin-top: 10px; font-size: 14px; color: #374151; white-space: pre-wrap; }
            .email-footer { background: #f3f4f6; padding: 16px 30px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; }
            .cv-badge { display: inline-block; background: #e0f2fe; color: #0284c7; padding: 6px 12px; border-radius: 3px; font-weight: 600; font-size: 13px; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class="email-wrapper">
            <div class="email-header">
                <h1><?php esc_html_e( 'New Job Application Received', 'jobscout' ); ?></h1>
            </div>
            <div class="email-body">
                <p><?php printf( esc_html__( 'A new application has been submitted on %s. Details are below:', 'jobscout' ), '<strong>' . esc_html( $site_name ) . '</strong>' ); ?></p>

                <table class="info-table">
                    <tr>
                        <th><?php esc_html_e( 'Job Title', 'jobscout' ); ?></th>
                        <td><strong><a href="<?php echo esc_url( $job_url ); ?>"><?php echo esc_html( $job_title ); ?></a></strong> (ID: #<?php echo esc_html( $job_id ); ?>)</td>
                    </tr>
                    <?php if ( ! empty( $company ) ) : ?>
                    <tr>
                        <th><?php esc_html_e( 'Company', 'jobscout' ); ?></th>
                        <td><?php echo esc_html( $company ); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?php esc_html_e( 'Candidate Name', 'jobscout' ); ?></th>
                        <td><strong><?php echo esc_html( $candidate_name ); ?></strong></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Candidate Email', 'jobscout' ); ?></th>
                        <td><a href="mailto:<?php echo esc_attr( $candidate_email ); ?>"><?php echo esc_html( $candidate_email ); ?></a></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Candidate Phone', 'jobscout' ); ?></th>
                        <td><a href="tel:<?php echo esc_attr( $candidate_phone ); ?>"><?php echo esc_html( $candidate_phone ); ?></a></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Application Date', 'jobscout' ); ?></th>
                        <td><?php echo esc_html( $formatted_date ); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'CV Document', 'jobscout' ); ?></th>
                        <td>
                            <a href="<?php echo esc_url( $cv_url ); ?>" class="cv-badge" target="_blank" rel="noopener noreferrer">
                                &#128196; <?php echo esc_html( $cv_name ); ?>
                            </a>
                            <br><small style="color:#6b7280;"><?php esc_html_e( '(The CV is also attached to this email)', 'jobscout' ); ?></small>
                        </td>
                    </tr>
                </table>

                <?php if ( ! empty( $cover_letter ) ) : ?>
                    <h3 style="font-size: 15px; margin-bottom: 6px; color: #111827;"><?php esc_html_e( 'Cover Letter / Message:', 'jobscout' ); ?></h3>
                    <div class="cover-letter-box"><?php echo esc_html( $cover_letter ); ?></div>
                <?php endif; ?>
            </div>
            <div class="email-footer">
                <?php printf( esc_html__( 'Notification sent to recruiter email: %s', 'jobscout' ), esc_html( $recruiter_email ) ); ?>
            </div>
        </div>
    </body>
    </html>
    <?php
    $message = ob_get_clean();

    // Track mail failure
    $mail_error_captured = '';
    $mail_failure_listener = function( $wp_error ) use ( &$mail_error_captured ) {
        if ( is_wp_error( $wp_error ) ) {
            $mail_error_captured = $wp_error->get_error_message();
        }
    };
    add_action( 'wp_mail_failed', $mail_failure_listener );

    $mail_sent = wp_mail( $recruiter_email, $subject, $message, $headers, $attachments );

    remove_action( 'wp_mail_failed', $mail_failure_listener );

    // Requirement:
    // "Kiểm tra lỗi gửi email và ghi log khi cần thiết.
    //  Không hiển thị thông báo gửi thành công nếu hệ thống gửi email thất bại."
    if ( ! $mail_sent ) {
        $error_desc = ! empty( $mail_error_captured ) ? $mail_error_captured : __( 'Could not deliver notification email through the mail server.', 'jobscout' );
        
        // Log detailed failure
        error_log( sprintf( '[PDS Application] Email delivery failure for Job #%d to %s: %s', $job_id, $recruiter_email, $error_desc ) );

        wp_send_json_error( array(
            'message' => sprintf(
                __( 'Your application details and CV were saved, but email notification to the recruiter failed (%s). Please verify your SMTP settings or contact the administrator.', 'jobscout' ),
                esc_html( $error_desc )
            ),
            'mail_sent' => false,
            'application_id' => ! is_wp_error( $app_post_id ) ? $app_post_id : 0,
        ), 500 );
        exit;
    }

    // 8. Success Response
    wp_send_json_success( array(
        'message'        => __( 'Your application has been submitted successfully! The recruitment team has received your information and CV.', 'jobscout' ),
        'application_id' => ! is_wp_error( $app_post_id ) ? $app_post_id : 0,
        'candidate_name' => $candidate_name,
        'job_title'      => $job_title,
    ) );
    exit;
}
add_action( 'wp_ajax_pds_submit_job_application', 'pds_ajax_submit_job_application' );
add_action( 'wp_ajax_nopriv_pds_submit_job_application', 'pds_ajax_submit_job_application' );
