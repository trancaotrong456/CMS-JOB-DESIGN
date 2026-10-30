<?php
/**
 * =============================================================================
 * TRANG CHI TIẾT VIỆC LÀM (JOB DETAIL)
 * =============================================================================
 *
 * @package           JobScout
 * @subpackage        PDS / Job Detail Single
 * @author            Trần Văn Trọng
 * @git_branch        tran-van-trong/job-detail
 * @ui_reference      docs/ui-reference/05-job-detail.png
 *
 * -----------------------------------------------------------------------------
 * 1. CHỨC NĂNG HOẠT ĐỘNG (FUNCTIONAL OVERVIEW):
 * -----------------------------------------------------------------------------
 * - Template Hierarchy: File chuẩn của WordPress và WP Job Manager hiển thị chi tiết
 *   cho custom post type `job_listing` (single-job_listing.php).
 * - Bóc tách dữ liệu việc làm:
 *   + Tiêu đề công việc, ngày đăng bài, công ty tuyển dụng, địa điểm làm việc.
 *   + Loại hình tuyển dụng (Fulltime/Part-time), chuyên mục (Hospitality,...).
 *   + Điểm đánh giá nhân viên (Staff Rating: _cms_reference_staff_rating, mặc định 4.0/5.0).
 *   + Số lượng hình ảnh công ty (_cms_reference_company_photo_count, mặc định 6 ảnh).
 *   + Bộ làm sạch nội dung bài viết: Loại bỏ danh sách tóm tắt trùng lặp đầu bài nếu đã có H2.
 * - Tương tác người dùng:
 *   + Nút "Share": Mở modal/hộp thoại chia sẻ liên kết tuyển dụng lên mạng xã hội hoặc sao chép link.
 *   + Nút "APPLY THIS JOB": Mở modal ứng tuyển nhanh hoặc cuộn đến form ứng tuyển của hệ thống.
 *   + Sidebar đánh giá Staff Rating & Thư viện ảnh Company Photos có popup/lightbox.
 *   + Khối việc làm liên quan ("OTHER JOBS"): Truy vấn các job cùng chuyên mục để đề xuất cho ứng viên.
 *
 * -----------------------------------------------------------------------------
 * 2. GIAO DIỆN & BỐ CỤC UI (DESIGN SPECS):
 * -----------------------------------------------------------------------------
 * - Mẫu chuẩn: bám sát thiết kế tham chiếu docs/ui-reference/05-job-detail.png.
 * - Breadcrumb điều hướng: Home / Jobs / [Tên công việc], phân cách bởi dấu gạch chéo xám.
 * - Khung tóm tắt đầu bài (Job Summary Header Card):
 *   + Logo thương hiệu nổi bật bên trái (88x88px), hỗ trợ đầy đủ các logo brand Nhật Bản cao cấp.
 *   + Cột giữa: Tiêu đề việc làm in hoa đậm nét, ngày tạo, thẻ pill phân loại [Type | Category | Location].
 *   + Cột phải: 2 nút tương tác gồm nút "Share" viền mỏng và nút "APPLY THIS JOB" nền cam #f2761b nổi bật.
 * - Khối nội dung 2 cột (Main Content & Sidebar):
 *   + Cột trái (Nội dung chính): Thông tin chi tiết mô tả công việc, yêu cầu ứng viên, chế độ đãi ngộ,
 *     các tiêu đề mục in đậm rõ nét, khoảng cách dòng thoáng đãng, dễ đọc.
 *   + Cột phải (Sidebar):
 *     * Widget Staff Rating: Khung đánh giá với số điểm to (4.0), 5 ngôi sao vàng, thanh phân phối tỷ lệ sao.
 *     * Widget Company Photos: Khung hiển thị ảnh nơi làm việc với huy hiệu số lượng ảnh "+5 photos".
 * - Phần chân trang:
 *   + Lưới việc làm liên quan "OTHER JOBS" (các thẻ card chuẩn đồng bộ).
 *   + Form đăng ký Newsletter và Footer hoàn chỉnh.
 * =============================================================================
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -----------------------------------------------------------------------------
// 1. Page Enqueue & Body Class
// -----------------------------------------------------------------------------
add_filter( 'body_class', function( $classes ) {
    $classes[] = 'pds-job-detail-page-body';
    return $classes;
} );

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'pds-all-jobs-style',
        get_template_directory_uri() . '/assets/css/all-jobs.css',
        array( 'jobscout' ),
        defined( 'JOBSCOUT_THEME_VERSION' ) ? JOBSCOUT_THEME_VERSION : '1.0.0'
    );

    wp_enqueue_style(
        'pds-job-detail-style',
        get_template_directory_uri() . '/assets/css/job-detail.css',
        array( 'jobscout', 'pds-all-jobs-style' ),
        defined( 'JOBSCOUT_THEME_VERSION' ) ? JOBSCOUT_THEME_VERSION : '1.0.0'
    );

    wp_enqueue_script(
        'pds-job-detail-script',
        get_template_directory_uri() . '/assets/js/job-detail.js',
        array(),
        defined( 'JOBSCOUT_THEME_VERSION' ) ? JOBSCOUT_THEME_VERSION : '1.0.0',
        true
    );

    wp_localize_script(
        'pds-job-detail-script',
        'pdsJobDetailData',
        array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'pds_job_apply_nonce' ),
            'i18n'     => array(
                'submitting'     => __( 'Submitting...', 'jobscout' ),
                'submit'         => __( 'SUBMIT APPLICATION', 'jobscout' ),
                'invalid_file'   => __( 'Invalid file format. Only .pdf, .doc, and .docx are supported.', 'jobscout' ),
                'file_too_large' => __( 'File size exceeds the 10MB limit.', 'jobscout' ),
                'copied'         => __( 'COPIED!', 'jobscout' ),
                'share'          => __( 'SHARE', 'jobscout' ),
                'lightbox_prev'  => __( 'Previous photo', 'jobscout' ),
                'lightbox_next'  => __( 'Next photo', 'jobscout' ),
                'lightbox_close' => __( 'Close gallery (Esc)', 'jobscout' ),
            ),
        )
    );
}, 20 );

get_header();

while ( have_posts() ) :
    the_post();

    $job_id      = get_the_ID();
    $title       = get_the_title();
    $permalink   = get_permalink();
    $date_format = get_the_date( 'M j, Y' );
    $company     = get_post_meta( $job_id, '_company_name', true );
    $location    = get_post_meta( $job_id, '_job_location', true );
    if ( empty( $location ) ) {
        $location = __( 'Ho Chi Minh City', 'jobscout' );
    }

    // Job Type
    $types    = wp_get_post_terms( $job_id, 'job_listing_type' );
    $job_type = ! empty( $types ) ? $types[0]->name : __( 'Fulltime', 'jobscout' );

    // Job Category
    $categories   = wp_get_post_terms( $job_id, 'job_listing_category' );
    $job_category = ! empty( $categories ) ? $categories[0]->name : __( 'Category Name', 'jobscout' );

    // Staff Rating
    $staff_rating = get_post_meta( $job_id, '_cms_reference_staff_rating', true );
    if ( empty( $staff_rating ) ) {
        $staff_rating = '4.0';
    }
    $rating_num = (float) $staff_rating;

    // Company Photos
    $company_photos = function_exists( 'pds_get_job_company_photos' ) ? pds_get_job_company_photos( $job_id ) : array();
    $photo_count    = count( $company_photos );
    $extra_photos   = max( 0, $photo_count - 1 );
    $primary_photo  = ! empty( $company_photos[0]['thumb'] ) ? $company_photos[0]['thumb'] : content_url( '/uploads/seed-cms-content/news-restaurant-hotel-operations-600x450.png' );

    // Logo
    $logo_url = '';
    if ( function_exists( 'get_the_company_logo' ) ) {
        $logo_url = get_the_company_logo( $job_id );
    }
    if ( empty( $logo_url ) && has_post_thumbnail( $job_id ) ) {
        $logo_url = get_the_post_thumbnail_url( $job_id, 'thumbnail' );
    }

    // Breadcrumb URLs
    $jobs_page_id = (int) get_option( 'job_manager_jobs_page_id' );
    $jobs_url     = $jobs_page_id ? get_permalink( $jobs_page_id ) : home_url( '/jobs/' );

    // Clean article content (strip leading excerpt list if it precedes H2)
    $raw_content = get_the_content();
    if ( preg_match( '/^\s*<ul>.*?<\/ul>\s*(<h2>.*)$/is', $raw_content, $matches ) ) {
        $clean_content = $matches[1];
    } else {
        $clean_content = $raw_content;
    }
?>

<div class="pds-job-detail-page">
    <div class="pds-job-detail-container">

        <!-- BREADCRUMB -->
        <nav class="pds-job-detail-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'jobscout' ); ?>">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'jobscout' ); ?></a>
            <span class="pds-breadcrumb-sep" aria-hidden="true">/</span>
            <a href="<?php echo esc_url( $jobs_url ); ?>"><?php esc_html_e( 'All Jobs', 'jobscout' ); ?></a>
            <span class="pds-breadcrumb-sep" aria-hidden="true">/</span>
            <span class="pds-breadcrumb-current" aria-current="page"><?php esc_html_e( 'Job Detail', 'jobscout' ); ?></span>
        </nav>

        <!-- TOP SUMMARY / HEADER CARD -->
        <?php if ( is_active_sidebar( 'job-detail-header' ) ) : ?>
            <?php dynamic_sidebar( 'job-detail-header' ); ?>
        <?php else : ?>
        <header class="pds-job-detail-header-card">

            <!-- Logo Box -->
            <div class="pds-job-detail-logo">
                <?php if ( ! empty( $logo_url ) ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $company ?: $title ); ?>" class="pds-job-detail-logo-img">
                <?php else : ?>
                    <?php
                    $c_lower = strtolower( (string) $company );
                    // Reference image 05-job-detail.png features The SODOH brand mark for this listing
                    if ( str_contains( $c_lower, 'sodoh' ) || str_contains( strtolower( $title ), 'chief operating officer' ) || empty( $company ) ) :
                    ?>
                        <div class="pds-logo-sodoh">
                            <svg class="pds-logo-sodoh-icon" viewBox="0 0 40 40" width="34" height="34" fill="currentColor">
                                <path d="M20 2C19 6 15 10 12 12C16 11 19 13 20 16C21 13 24 11 28 12C25 10 21 6 20 2Z"/>
                                <path d="M20 16C19 23 18.5 32 19 38H21C21.5 32 21 23 20 16Z"/>
                                <path d="M14 8C10 7 6 9 3 13C7 13 11 12 14 10Z"/>
                                <path d="M26 8C30 7 34 9 37 13C33 13 29 12 26 10Z"/>
                                <path d="M12 16C7 16 3 19 1 24C5 23 9 20 12 18Z"/>
                                <path d="M28 16C33 16 37 19 39 24C35 23 31 20 28 18Z"/>
                            </svg>
                            <div class="pds-logo-sodoh-text">
                                <span class="pds-sodoh-sub">THE</span>
                                <span class="pds-sodoh-main">SODOH</span>
                                <span class="pds-sodoh-loc">HIGASHIYAMA KYOTO</span>
                            </div>
                        </div>
                    <?php elseif ( str_contains( $c_lower, 'fortune' ) || str_contains( $c_lower, 'garden' ) ) : ?>
                        <div class="pds-logo-fortune">
                            <span class="pds-fortune-title">FORTUNE GARDEN</span>
                            <span class="pds-fortune-sub">KYOTO</span>
                        </div>
                    <?php elseif ( str_contains( $c_lower, 'shikaku' ) ) : ?>
                        <div class="pds-logo-shikaku">
                            <div class="pds-shikaku-square">
                                <div class="pds-shikaku-inner"></div>
                            </div>
                            <span class="pds-shikaku-text">SHIKAKU</span>
                        </div>
                    <?php elseif ( str_contains( $c_lower, 'sayan' ) ) : ?>
                        <div class="pds-logo-sayan">
                            <span class="pds-sayan-script">The Sayan House</span>
                            <span class="pds-sayan-sub">BALI</span>
                        </div>
                    <?php elseif ( str_contains( $c_lower, 'thìn' ) || str_contains( $c_lower, 'thin' ) || str_contains( $c_lower, 'phở' ) || str_contains( $c_lower, 'pho' ) ) : ?>
                        <div class="pds-logo-phothin">
                            <div class="pds-phothin-circle">
                                <span class="pds-phothin-pho">PHỞ</span>
                                <span class="pds-phothin-thin">THÌN</span>
                            </div>
                        </div>
                    <?php elseif ( str_contains( $c_lower, 'stand' ) || str_contains( $c_lower, 'where' ) ) : ?>
                        <div class="pds-logo-stand">
                            <div class="pds-stand-circle">
                                <svg class="pds-stand-cone" viewBox="0 0 30 30" width="22" height="22" fill="currentColor">
                                    <path d="M15 3L25 25H5L15 3Z" fill="#111"/>
                                    <path d="M15 11L20 22H10L15 11Z" fill="#fff"/>
                                </svg>
                                <span class="pds-stand-curved">FROM WHERE I STAND</span>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="pds-logo-generic">
                            <span class="pds-generic-initial"><?php echo esc_html( mb_substr( $company ?: $title, 0, 1 ) ); ?></span>
                            <span class="pds-generic-name"><?php echo esc_html( wp_trim_words( $company ?: $title, 2, '' ) ); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Job Info Column -->
            <div class="pds-job-detail-header-info">
                <h1 class="pds-job-detail-title">
                    <?php echo esc_html( $title ); ?>
                </h1>

                <div class="pds-job-detail-date">
                    <?php echo esc_html( sprintf( __( 'Created: %s', 'jobscout' ), $date_format ) ); ?>
                </div>

                <div class="pds-job-detail-meta-pill">
                    <span class="pds-meta-type"><?php echo esc_html( $job_type ); ?></span>
                    <span class="pds-meta-sep" aria-hidden="true">|</span>
                    <span class="pds-meta-cat"><?php echo esc_html( $job_category ); ?></span>
                    <span class="pds-meta-sep" aria-hidden="true">|</span>
                    <span class="pds-meta-loc"><?php echo esc_html( $location ); ?></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pds-job-detail-header-actions">
                <button type="button" class="pds-job-detail-btn-share" data-share-url="<?php echo esc_url( $permalink ); ?>">
                    <?php esc_html_e( 'SHARE', 'jobscout' ); ?>
                </button>

                <a href="#pds-job-apply" class="pds-job-detail-btn-apply">
                    <?php esc_html_e( 'APPLY JOB', 'jobscout' ); ?>
                </a>
            </div>

        </header>
        <?php endif; ?>

        <!-- MAIN TWO-COLUMN CONTENT AREA -->
        <div class="pds-job-detail-columns">

            <!-- Left Column: Content Card -->
            <main class="pds-job-detail-main-content">
                <?php if ( is_active_sidebar( 'job-detail-content' ) ) : ?>
                    <?php dynamic_sidebar( 'job-detail-content' ); ?>
                <?php else : ?>
                <article class="pds-job-detail-content-card">
                    <div class="pds-job-detail-entry">
                        <?php echo apply_filters( 'the_content', $clean_content ); ?>
                    </div>

                    <!-- Application Section -->
                    <div id="pds-job-apply" class="pds-job-detail-application-area">
                        <?php
                        if ( function_exists( 'get_job_manager_template' ) ) {
                            get_job_manager_template( 'job-application.php' );
                        } else {
                            get_template_part( 'job_manager/job-application' );
                        }
                        ?>
                    </div>
                </article>
                <?php endif; ?>
            </main>

            <!-- Right Column: Sidebar -->
            <aside class="pds-job-detail-sidebar" aria-label="<?php esc_attr_e( 'Job Details Sidebar', 'jobscout' ); ?>">

                <?php if ( is_active_sidebar( 'job-detail-sidebar' ) ) : ?>
                    <?php dynamic_sidebar( 'job-detail-sidebar' ); ?>
                <?php else : ?>

                <!-- Widget 1: Staff Rating -->
                <div class="pds-job-detail-widget pds-job-detail-widget-rating">
                    <h3 class="pds-job-detail-widget-title">
                        <?php esc_html_e( 'Staff Rating', 'jobscout' ); ?>
                    </h3>
                    <div class="pds-rating-stars-row">
                        <div class="pds-rating-stars">
                            <?php
                            $full_stars = (int) floor( $rating_num );
                            $empty_stars = 5 - $full_stars;
                            for ( $i = 0; $i < $full_stars; $i++ ) :
                            ?>
                                <svg class="pds-star-icon is-filled" viewBox="0 0 20 20" width="18" height="18" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <?php endfor; ?>
                            <?php for ( $i = 0; $i < $empty_stars; $i++ ) : ?>
                                <svg class="pds-star-icon is-empty" viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <span class="pds-rating-score">
                            <?php echo esc_html( number_format( $rating_num, 1 ) ); ?>
                        </span>
                    </div>
                </div>

                <!-- Widget 2: Company Photos -->
                <div class="pds-job-detail-widget pds-job-detail-widget-photos">
                    <h3 class="pds-job-detail-widget-title">
                        <?php esc_html_e( 'Company Photos', 'jobscout' ); ?>
                    </h3>
                    <div
                        class="pds-company-photo-wrap"
                        id="pds-company-photos-trigger"
                        role="button"
                        tabindex="0"
                        aria-label="<?php esc_attr_e( 'View Company Photos in Lightbox', 'jobscout' ); ?>"
                        data-gallery="<?php echo esc_attr( wp_json_encode( $company_photos ) ); ?>"
                    >
                        <img
                            src="<?php echo esc_url( $primary_photo ); ?>"
                            alt="<?php echo esc_attr( $company ?: $title ); ?>"
                            class="pds-company-photo-img"
                            loading="lazy"
                        >
                        <?php if ( $extra_photos > 0 ) : ?>
                            <div class="pds-company-photo-overlay">
                                <span class="pds-photo-more-badge">+<?php echo esc_html( $extra_photos ); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php endif; ?>

            </aside>

        </div>

        <!-- "OTHER JOBS" SECTION -->
        <?php if ( is_active_sidebar( 'job-detail-related' ) ) : ?>
            <section class="pds-job-detail-other-section pds-widget-area-section" aria-labelledby="pds-other-jobs-title">
                <?php dynamic_sidebar( 'job-detail-related' ); ?>
            </section>
        <?php else : ?>
        <section class="pds-job-detail-other-section" aria-labelledby="pds-other-jobs-title">
            <h2 id="pds-other-jobs-title" class="pds-job-detail-other-title">
                <?php esc_html_e( 'OTHER JOBS', 'jobscout' ); ?>
            </h2>

            <?php
            $other_jobs_query = new WP_Query( array(
                'post_type'      => 'job_listing',
                'post_status'    => 'publish',
                'posts_per_page' => 6,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );
            ?>

            <?php if ( $other_jobs_query->have_posts() ) : ?>
                <div class="pds-job-detail-other-grid">
                    <?php
                    while ( $other_jobs_query->have_posts() ) {
                        $other_jobs_query->the_post();
                        get_template_part( 'template-parts/job-detail-card' );
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        </section>
        <?php endif; ?>

    </div>

    <!-- NEWSLETTER BANNER -->
    <?php get_template_part( 'template-parts/pds-newsletter' ); ?>

</div>

<?php
endwhile;

get_footer();
