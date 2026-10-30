<?php
/**
 * Extended CMS Widgets for JobScout
 *
 * Adds widgets for:
 * 1. News Hero Banner
 * 2. About Hero Banner
 * 3. About Management Callout
 * 4. Job Detail Hero Banner
 * 5. Job Detail Header Card
 * 6. Job Detail Main Content
 * 7. News Detail Article Header
 * 8. News Detail Article Overview
 * 9. News Detail Sidebar Info
 * 10. Contact Form Area
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ==========================================================================
   1. NEWS HERO BANNER WIDGET
   ========================================================================== */
class PDS_News_Hero_Banner_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'pds_news_hero_banner',
            __( '[News] Hero Banner', 'jobscout' ),
            array(
                'description'                 => __( 'Khối Banner PDS NEWS đầu trang News (đổi ảnh nền và tiêu đề).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
            echo $args['after_widget'];
            return;
        }

        $title        = ! empty( $instance['title'] ) ? $instance['title'] : __( 'PDS NEWS', 'jobscout' );
        $bg_image_url = ! empty( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : get_template_directory_uri() . '/assets/images/news-hero.png';

        echo $args['before_widget'];
        if ( ! empty( $bg_image_url ) ) : ?>
            <style>.pds-news-hero { background-image: url('<?php echo esc_url( $bg_image_url ); ?>') !important; }</style>
        <?php endif; ?>
        <section class="pds-news-hero" aria-labelledby="pds-news-hero-title">
            <div class="pds-news-hero-overlay"></div>
            <div class="pds-news-hero-inner" style="margin: 0 auto !important; text-align: center !important;">
                <h1 id="pds-news-hero-title" class="pds-news-hero-title" style="margin: 0 auto !important; text-align: center !important;">
                    <?php echo esc_html( $title ); ?>
                </h1>
            </div>
        </section>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title        = isset( $instance['title'] ) ? $instance['title'] : __( 'PDS NEWS', 'jobscout' );
        $bg_image_url = isset( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : get_template_directory_uri() . '/assets/images/news-hero.png';

        $snippet = <<<HTML
<section class="pds-news-hero" aria-labelledby="pds-news-hero-title">
    <div class="pds-news-hero-overlay"></div>
    <div class="pds-news-hero-inner">
        <h1 id="pds-news-hero-title" class="pds-news-hero-title">PDS NEWS</h1>
    </div>
</section>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề Banner:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
        pds_render_image_field( $this, 'bg_image_url', __( 'Ảnh nền Banner (Hero Background Image):', 'jobscout' ), $bg_image_url );
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    public function update( $new_instance, $old_instance ) {
        $instance                 = array();
        $instance['title']        = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['bg_image_url'] = esc_url_raw( $new_instance['bg_image_url'] ?? '' );
        $instance['custom_html']  = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==========================================================================
   2. ABOUT HERO BANNER WIDGET
   ========================================================================== */
class PDS_About_Hero_Banner_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'pds_about_hero_banner',
            __( '[About] Hero Banner', 'jobscout' ),
            array(
                'description'                 => __( 'Khối Banner SHARE OMOTENASHI đầu trang About (đổi ảnh nền và tiêu đề).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
            echo $args['after_widget'];
            return;
        }

        $title_line1  = ! empty( $instance['title_line1'] ) ? $instance['title_line1'] : __( 'SHARE “OMOTENASHI”', 'jobscout' );
        $title_line2  = ! empty( $instance['title_line2'] ) ? $instance['title_line2'] : __( 'WITH THE WORLD', 'jobscout' );
        $bg_image_url = ! empty( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : get_template_directory_uri() . '/assets/images/about-hero.png';

        echo $args['before_widget'];
        if ( ! empty( $bg_image_url ) ) : ?>
            <style>.pds-about-hero { background-image: url('<?php echo esc_url( $bg_image_url ); ?>') !important; }</style>
        <?php endif; ?>
        <section class="pds-about-hero" aria-labelledby="pds-about-hero-title">
            <div class="pds-about-hero-overlay"></div>
            <div class="pds-about-hero-inner" style="margin: 0 auto !important; text-align: center !important;">
                <h1 id="pds-about-hero-title" class="pds-about-hero-title" style="margin: 0 auto !important; text-align: center !important;">
                    <?php echo esc_html( $title_line1 ); ?><br>
                    <?php echo esc_html( $title_line2 ); ?>
                </h1>
            </div>
        </section>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title_line1  = isset( $instance['title_line1'] ) ? $instance['title_line1'] : __( 'SHARE “OMOTENASHI”', 'jobscout' );
        $title_line2  = isset( $instance['title_line2'] ) ? $instance['title_line2'] : __( 'WITH THE WORLD', 'jobscout' );
        $bg_image_url = isset( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : get_template_directory_uri() . '/assets/images/about-hero.png';

        $snippet = <<<HTML
<section class="pds-about-hero" aria-labelledby="pds-about-hero-title">
    <div class="pds-about-hero-overlay"></div>
    <div class="pds-about-hero-inner">
        <h1 id="pds-about-hero-title" class="pds-about-hero-title">
            SHARE “OMOTENASHI”<br>WITH THE WORLD
        </h1>
    </div>
</section>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title_line1' ) ); ?>"><?php esc_html_e( 'Tiêu đề dòng 1:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title_line1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title_line1' ) ); ?>" type="text" value="<?php echo esc_attr( $title_line1 ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title_line2' ) ); ?>"><?php esc_html_e( 'Tiêu đề dòng 2:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title_line2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title_line2' ) ); ?>" type="text" value="<?php echo esc_attr( $title_line2 ); ?>">
        </p>
        <?php
        pds_render_image_field( $this, 'bg_image_url', __( 'Ảnh nền Banner (Hero Background Image):', 'jobscout' ), $bg_image_url );
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    public function update( $new_instance, $old_instance ) {
        $instance                 = array();
        $instance['title_line1']  = sanitize_text_field( $new_instance['title_line1'] ?? '' );
        $instance['title_line2']  = sanitize_text_field( $new_instance['title_line2'] ?? '' );
        $instance['bg_image_url'] = esc_url_raw( $new_instance['bg_image_url'] ?? '' );
        $instance['custom_html']  = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==========================================================================
   3. ABOUT MANAGEMENT CALLOUT WIDGET
   ========================================================================== */
class PDS_About_Management_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'pds_about_management',
            __( '[About] Management Callout', 'jobscout' ),
            array(
                'description'                 => __( 'Khối giới thiệu quản lý khách sạn/nhà hàng ở giữa trang About.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
            echo $args['after_widget'];
            return;
        }

        $title  = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Hotels, restaurants, banquets/weddings management', 'jobscout' );
        $desc_1 = ! empty( $instance['desc_1'] ) ? $instance['desc_1'] : __( 'Plan Do See developed and operates 17 properties worldwide including 3 award-winning resorts in Japan; 17 restaurants of diverse cuisines in cities including New York, Miami and Los Angeles; and other countries including Japan, Indonesia, Malaysia and Bali.', 'jobscout' );
        $desc_2 = ! empty( $instance['desc_2'] ) ? $instance['desc_2'] : __( 'Each venue carries its own concept and design. Many of them are originally historical landmarks that were loved by the local people.', 'jobscout' );

        echo $args['before_widget'];
        ?>
        <section class="pds-about-management-section" aria-labelledby="pds-about-mgmt-title">
            <div class="pds-about-container">
                <div class="pds-about-management-inner">
                    <h2 id="pds-about-mgmt-title" class="pds-about-mgmt-title">
                        <?php echo esc_html( $title ); ?>
                    </h2>
                    <div class="pds-about-mgmt-text">
                        <?php if ( $desc_1 ) : ?><p><?php echo esc_html( $desc_1 ); ?></p><?php endif; ?>
                        <?php if ( $desc_2 ) : ?><p><?php echo esc_html( $desc_2 ); ?></p><?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title  = isset( $instance['title'] ) ? $instance['title'] : __( 'Hotels, restaurants, banquets/weddings management', 'jobscout' );
        $desc_1 = isset( $instance['desc_1'] ) ? $instance['desc_1'] : __( 'Plan Do See developed and operates 17 properties worldwide including 3 award-winning resorts in Japan; 17 restaurants of diverse cuisines in cities including New York, Miami and Los Angeles; and other countries including Japan, Indonesia, Malaysia and Bali.', 'jobscout' );
        $desc_2 = isset( $instance['desc_2'] ) ? $instance['desc_2'] : __( 'Each venue carries its own concept and design. Many of them are originally historical landmarks that were loved by the local people.', 'jobscout' );

        $snippet = <<<HTML
<section class="pds-about-management-section" aria-labelledby="pds-about-mgmt-title">
    <div class="pds-about-container">
        <div class="pds-about-management-inner">
            <h2 id="pds-about-mgmt-title" class="pds-about-mgmt-title">Hotels, restaurants, banquets/weddings management</h2>
            <div class="pds-about-mgmt-text">
                <p>Plan Do See developed and operates 17 properties worldwide including 3 award-winning resorts in Japan; 17 restaurants of diverse cuisines in cities including New York, Miami and Los Angeles; and other countries including Japan, Indonesia, Malaysia and Bali.</p>
                <p>Each venue carries its own concept and design. Many of them are originally historical landmarks that were loved by the local people.</p>
            </div>
        </div>
    </div>
</section>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề cam:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'desc_1' ) ); ?>"><?php esc_html_e( 'Đoạn văn 1:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'desc_1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'desc_1' ) ); ?>"><?php echo esc_textarea( $desc_1 ); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'desc_2' ) ); ?>"><?php esc_html_e( 'Đoạn văn 2:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'desc_2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'desc_2' ) ); ?>"><?php echo esc_textarea( $desc_2 ); ?></textarea>
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['desc_1']      = sanitize_textarea_field( $new_instance['desc_1'] ?? '' );
        $instance['desc_2']      = sanitize_textarea_field( $new_instance['desc_2'] ?? '' );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==========================================================================
   4. JOB DETAIL HERO BANNER WIDGET
   ========================================================================== */
class PDS_Job_Detail_Hero_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'pds_job_detail_hero',
            __( '[Job Detail] Hero Banner', 'jobscout' ),
            array(
                'description'                 => __( 'Khối Banner ảnh trên cùng trang Chi tiết việc làm.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
            echo $args['after_widget'];
            return;
        }

        $bg_image_url = ! empty( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : get_header_image();
        if ( empty( $bg_image_url ) ) {
            $bg_image_url = get_template_directory_uri() . '/assets/images/job-detail-hero.png';
        }

        echo $args['before_widget'];
        if ( ! empty( $bg_image_url ) ) : ?>
            <div class="pds-job-detail-hero-banner" style="width: 100%; height: 360px; background: #222 url('<?php echo esc_url( $bg_image_url ); ?>') center center / cover no-repeat; position: relative;">
                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.15);"></div>
            </div>
        <?php endif;
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $bg_image_url = isset( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : get_header_image();
        if ( empty( $bg_image_url ) ) {
            $bg_image_url = get_template_directory_uri() . '/assets/images/job-detail-hero.png';
        }

        $snippet = <<<HTML
<div class="pds-job-detail-hero-banner" style="width: 100%; height: 360px; background: #222 url('$bg_image_url') center center / cover no-repeat; position: relative;">
</div>
HTML;
        pds_render_image_field( $this, 'bg_image_url', __( 'Ảnh Banner Job Detail:', 'jobscout' ), $bg_image_url );
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    public function update( $new_instance, $old_instance ) {
        $instance                 = array();
        $instance['bg_image_url'] = esc_url_raw( $new_instance['bg_image_url'] ?? '' );
        $instance['custom_html']  = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==========================================================================
   5. JOB DETAIL HEADER CARD WIDGET
   ========================================================================== */
class PDS_Job_Detail_Header_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'pds_job_detail_header',
            __( '[Job Detail] Header Card', 'jobscout' ),
            array(
                'description'                 => __( 'Khối Header thông tin tuyển dụng (nút APPLY, nút SHARE) trang Job Detail.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
            echo $args['after_widget'];
            return;
        }

        $apply_text = ! empty( $instance['apply_text'] ) ? $instance['apply_text'] : __( 'APPLY JOB', 'jobscout' );
        $share_text = ! empty( $instance['share_text'] ) ? $instance['share_text'] : __( 'SHARE', 'jobscout' );

        $job_id    = get_the_ID();
        $title     = get_the_title();
        $date      = get_the_date( 'M j, Y' );
        $types     = wp_get_post_terms( $job_id, 'job_listing_type' );
        $type_name = ! empty( $types ) ? $types[0]->name : 'Fulltime';
        $cats      = wp_get_post_terms( $job_id, 'job_listing_category' );
        $cat_name  = ! empty( $cats ) ? $cats[0]->name : 'Category Name';
        $location  = get_post_meta( $job_id, '_job_location', true ) ?: 'Ho Chi Minh City';
        $company   = get_post_meta( $job_id, '_company_name', true ) ?: 'The SODOH';

        echo $args['before_widget'];
        ?>
        <header class="pds-job-detail-header-card">
            <div class="pds-job-detail-logo">
                <div class="pds-logo-sodoh">
                    <svg class="pds-logo-sodoh-icon" viewBox="0 0 40 40" width="36" height="36" fill="currentColor">
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
                    </div>
                </div>
            </div>
            <div class="pds-job-detail-title-col">
                <h1 class="pds-job-detail-title"><?php echo esc_html( $title ); ?></h1>
                <div class="pds-job-detail-date">Created: <?php echo esc_html( $date ); ?></div>
                <div class="pds-job-detail-pills">
                    <span class="pds-pill"><?php echo esc_html( $type_name ); ?></span>
                    <span class="pds-pill-sep">|</span>
                    <span class="pds-pill"><?php echo esc_html( $cat_name ); ?></span>
                    <span class="pds-pill-sep">|</span>
                    <span class="pds-pill"><?php echo esc_html( $location ); ?></span>
                </div>
            </div>
            <div class="pds-job-detail-actions">
                <button type="button" class="pds-job-detail-btn-share" data-share-url="<?php echo esc_url( get_permalink() ); ?>">
                    <?php echo esc_html( $share_text ); ?>
                </button>
                <a href="#pds-job-apply" class="pds-job-detail-btn-apply">
                    <?php echo esc_html( $apply_text ); ?>
                </a>
            </div>
        </header>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $apply_text = isset( $instance['apply_text'] ) ? $instance['apply_text'] : __( 'APPLY JOB', 'jobscout' );
        $share_text = isset( $instance['share_text'] ) ? $instance['share_text'] : __( 'SHARE', 'jobscout' );

        $snippet = <<<HTML
<header class="pds-job-detail-header-card">
    <div class="pds-job-detail-title-col">
        <h1 class="pds-job-detail-title">HOTEL MANAGER</h1>
        <div class="pds-job-detail-date">Created: Oct 20, 2022</div>
    </div>
    <div class="pds-job-detail-actions">
        <button type="button" class="pds-job-detail-btn-share">SHARE</button>
        <a href="#apply" class="pds-job-detail-btn-apply">APPLY JOB</a>
    </div>
</header>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'apply_text' ) ); ?>"><?php esc_html_e( 'Chữ nút Apply Job:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'apply_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'apply_text' ) ); ?>" type="text" value="<?php echo esc_attr( $apply_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'share_text' ) ); ?>"><?php esc_html_e( 'Chữ nút Share:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'share_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'share_text' ) ); ?>" type="text" value="<?php echo esc_attr( $share_text ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['apply_text']  = sanitize_text_field( $new_instance['apply_text'] ?? '' );
        $instance['share_text']  = sanitize_text_field( $new_instance['share_text'] ?? '' );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==========================================================================
   5.2 JOB DETAIL MAIN CONTENT WIDGET
   ========================================================================== */
class PDS_Job_Detail_Content_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'pds_job_detail_content',
            __( '[Job Detail] Main Content & Application', 'jobscout' ),
            array(
                'description'                 => __( 'Khối nội dung chi tiết bài tuyển dụng và form nộp hồ sơ trang Job Detail.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
            echo $args['after_widget'];
            return;
        }

        $apply_title = ! empty( $instance['apply_title'] ) ? $instance['apply_title'] : __( 'Apply for this position', 'jobscout' );

        $raw_content = get_the_content();
        if ( preg_match( '/^\s*<ul>.*?<\/ul>\s*(<h2>.*)$/is', $raw_content, $matches ) ) {
            $clean_content = $matches[1];
        } else {
            $clean_content = $raw_content;
        }

        echo $args['before_widget'];
        ?>
        <article class="pds-job-detail-content-card">
            <div class="pds-job-detail-entry">
                <?php echo apply_filters( 'the_content', $clean_content ); ?>
            </div>

            <!-- Application Section -->
            <div id="pds-job-apply" class="pds-job-detail-application-area">
                <?php if ( $apply_title ) : ?>
                    <h3 class="pds-job-apply-title" style="font-size: 18px; font-weight: 700; color: #211916; margin-bottom: 16px;">
                        <?php echo esc_html( $apply_title ); ?>
                    </h3>
                <?php endif; ?>
                <?php
                if ( function_exists( 'candidates_can_apply' ) && candidates_can_apply() ) {
                    get_job_manager_template( 'job-application.php' );
                }
                ?>
            </div>
        </article>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $apply_title = isset( $instance['apply_title'] ) ? $instance['apply_title'] : __( 'Apply for this position', 'jobscout' );

        $snippet = <<<HTML
<article class="pds-job-detail-content-card">
    <div class="pds-job-detail-entry">
        <h2>Job Description</h2>
        <p>Job content details...</p>
    </div>
</article>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'apply_title' ) ); ?>"><?php esc_html_e( 'Tiêu đề phần nộp đơn ứng tuyển:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'apply_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'apply_title' ) ); ?>" type="text" value="<?php echo esc_attr( $apply_title ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['apply_title'] = sanitize_text_field( $new_instance['apply_title'] ?? '' );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}


/* ==========================================================================
   6. NEWS DETAIL HEADER WIDGET
   ========================================================================== */
class PDS_News_Detail_Header_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'pds_news_detail_header',
            __( '[News Detail] Article Header', 'jobscout' ),
            array(
                'description'                 => __( 'Khối tiêu đề bài viết, nút SHARE và BACK TO NEWS trang News Detail.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
            echo $args['after_widget'];
            return;
        }

        $share_text = ! empty( $instance['share_text'] ) ? $instance['share_text'] : __( 'SHARE', 'jobscout' );
        $back_text  = ! empty( $instance['back_text'] ) ? $instance['back_text'] : __( 'BACK TO NEWS', 'jobscout' );

        $news_page = get_page_by_path( 'news' );
        $news_url  = $news_page ? get_permalink( $news_page ) : home_url( '/news/' );

        echo $args['before_widget'];
        ?>
        <header class="pds-news-detail-summary">
            <div class="pds-news-detail-cover">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'medium', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
                <?php else : ?>
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/news-restaurant.png' ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php endif; ?>
            </div>
            <div class="pds-news-detail-summary-text">
                <h1><?php echo esc_html( get_the_title() ); ?></h1>
                <p class="pds-news-detail-date">Published: <?php echo esc_html( get_the_date( 'M d, Y' ) ); ?></p>
                <div class="pds-news-detail-tags">
                    <span><?php echo esc_html( get_the_author() ?: 'Admin' ); ?></span>
                    <?php
                    $categories = get_the_category();
                    foreach ( array_slice( $categories, 0, 2 ) as $cat ) {
                        echo '<span>' . esc_html( $cat->name ) . '</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="pds-news-detail-actions">
                <button type="button" class="pds-news-detail-share" data-share-url="<?php echo esc_url( get_permalink() ); ?>">
                    <?php echo esc_html( $share_text ); ?>
                </button>
                <a class="pds-news-detail-back" href="<?php echo esc_url( $news_url ); ?>">
                    <?php echo esc_html( $back_text ); ?>
                </a>
            </div>
        </header>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $share_text = isset( $instance['share_text'] ) ? $instance['share_text'] : __( 'SHARE', 'jobscout' );
        $back_text  = isset( $instance['back_text'] ) ? $instance['back_text'] : __( 'BACK TO NEWS', 'jobscout' );

        $snippet = <<<HTML
<header class="pds-news-detail-summary">
    <div class="pds-news-detail-summary-text">
        <h1>PROJECT DEVELOPMENT</h1>
        <p class="pds-news-detail-date">Published: Oct 09, 2026</p>
    </div>
    <div class="pds-news-detail-actions">
        <button type="button" class="pds-news-detail-share">SHARE</button>
        <a class="pds-news-detail-back" href="/news/">BACK TO NEWS</a>
    </div>
</header>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'back_text' ) ); ?>"><?php esc_html_e( 'Chữ nút quay lại News:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'back_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'back_text' ) ); ?>" type="text" value="<?php echo esc_attr( $back_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'share_text' ) ); ?>"><?php esc_html_e( 'Chữ nút Share:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'share_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'share_text' ) ); ?>" type="text" value="<?php echo esc_attr( $share_text ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['back_text']   = sanitize_text_field( $new_instance['back_text'] ?? '' );
        $instance['share_text']  = sanitize_text_field( $new_instance['share_text'] ?? '' );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==========================================================================
   6.2 NEWS DETAIL ARTICLE OVERVIEW WIDGET
   ========================================================================== */
class PDS_News_Detail_Content_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'pds_news_detail_content',
            __( '[News Detail] Article Overview Content', 'jobscout' ),
            array(
                'description'                 => __( 'Khối nội dung chi tiết bài viết (Article Overview) trang News Detail.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
            echo $args['after_widget'];
            return;
        }

        $heading = ! empty( $instance['heading'] ) ? $instance['heading'] : __( 'Article Overview', 'jobscout' );

        echo $args['before_widget'];
        ?>
        <div class="pds-news-detail-content">
            <?php if ( $heading ) : ?>
                <h2><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>
            <div class="pds-news-detail-entry">
                <?php
                the_content();
                wp_link_pages( array(
                    'before' => '<div class="pds-news-detail-page-links">' . esc_html__( 'Pages:', 'jobscout' ),
                    'after'  => '</div>',
                ) );
                ?>
            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $heading = isset( $instance['heading'] ) ? $instance['heading'] : __( 'Article Overview', 'jobscout' );

        $snippet = <<<HTML
<div class="pds-news-detail-content">
    <h2>Article Overview</h2>
    <div class="pds-news-detail-entry">
        <p>Article content goes here...</p>
    </div>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'heading' ) ); ?>"><?php esc_html_e( 'Tiêu đề khối nội dung:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'heading' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'heading' ) ); ?>" type="text" value="<?php echo esc_attr( $heading ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['heading']     = sanitize_text_field( $new_instance['heading'] ?? '' );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}


/* ==========================================================================
   7. NEWS DETAIL SIDEBAR WIDGET
   ========================================================================== */

class PDS_News_Detail_Sidebar_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'pds_news_detail_sidebar',
            __( '[News Detail] Sidebar Info & Recent', 'jobscout' ),
            array(
                'description'                 => __( 'Cột Article Information và Recent News trang News Detail.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
            echo $args['after_widget'];
            return;
        }

        $info_title   = ! empty( $instance['info_title'] ) ? $instance['info_title'] : __( 'Article Information', 'jobscout' );
        $recent_title = ! empty( $instance['recent_title'] ) ? $instance['recent_title'] : __( 'Recent News', 'jobscout' );
        $recent_count = ! empty( $instance['recent_count'] ) ? absint( $instance['recent_count'] ) : 3;

        $post_id    = get_the_ID();
        $date       = get_the_date( 'M d, Y' );
        $author     = get_the_author() ?: 'Admin';
        $categories = get_the_category();
        $cat_names  = $categories ? implode( ', ', wp_list_pluck( $categories, 'name' ) ) : 'Category Name';

        echo $args['before_widget'];
        ?>
        <aside class="pds-news-detail-sidebar" aria-label="<?php esc_attr_e( 'News sidebar', 'jobscout' ); ?>">
            <section class="pds-news-detail-side-card">
                <h2><?php echo esc_html( $info_title ); ?></h2>
                <dl>
                    <dt><?php esc_html_e( 'Published', 'jobscout' ); ?></dt>
                    <dd><?php echo esc_html( $date ); ?></dd>
                    <dt><?php esc_html_e( 'Author', 'jobscout' ); ?></dt>
                    <dd><?php echo esc_html( $author ); ?></dd>
                    <dt><?php esc_html_e( 'Category', 'jobscout' ); ?></dt>
                    <dd><?php echo esc_html( $cat_names ); ?></dd>
                </dl>
            </section>
            <section class="pds-news-detail-side-card">
                <h2><?php echo esc_html( $recent_title ); ?></h2>
                <?php
                $recent_news = new WP_Query( array(
                    'post_type'           => 'post',
                    'post_status'         => 'publish',
                    'posts_per_page'      => $recent_count,
                    'post__not_in'        => array( $post_id ),
                    'ignore_sticky_posts' => true,
                    'no_found_rows'       => true,
                ) );
                if ( $recent_news->have_posts() ) : ?>
                    <div class="pds-news-detail-recent-list">
                        <?php while ( $recent_news->have_posts() ) : $recent_news->the_post(); ?>
                            <a class="pds-news-detail-recent-item" href="<?php the_permalink(); ?>">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'thumbnail' ); ?>
                                <?php else : ?>
                                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/news-hotel.png' ); ?>" alt="" style="width: 58px; height: 58px; object-fit: cover; border-radius: 2px;">
                                <?php endif; ?>
                                <span><?php the_title(); ?></span>
                            </a>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <p><?php esc_html_e( 'No recent articles.', 'jobscout' ); ?></p>
                <?php endif; ?>
            </section>
        </aside>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $info_title   = isset( $instance['info_title'] ) ? $instance['info_title'] : __( 'Article Information', 'jobscout' );
        $recent_title = isset( $instance['recent_title'] ) ? $instance['recent_title'] : __( 'Recent News', 'jobscout' );
        $recent_count = isset( $instance['recent_count'] ) ? absint( $instance['recent_count'] ) : 3;

        $snippet = <<<HTML
<section class="pds-news-detail-side-card">
    <h2>Article Information</h2>
    <dl>
        <dt>Published</dt><dd>Oct 09, 2026</dd>
        <dt>Author</dt><dd>Admin</dd>
        <dt>Category</dt><dd>Category Name</dd>
    </dl>
</section>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'info_title' ) ); ?>"><?php esc_html_e( 'Tiêu đề thông tin bài viết:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'info_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'info_title' ) ); ?>" type="text" value="<?php echo esc_attr( $info_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'recent_title' ) ); ?>"><?php esc_html_e( 'Tiêu đề tin tức gần đây:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'recent_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'recent_title' ) ); ?>" type="text" value="<?php echo esc_attr( $recent_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'recent_count' ) ); ?>"><?php esc_html_e( 'Số bài viết gần đây:', 'jobscout' ); ?></label>
            <input class="small-text" id="<?php echo esc_attr( $this->get_field_id( 'recent_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'recent_count' ) ); ?>" type="number" value="<?php echo esc_attr( $recent_count ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    public function update( $new_instance, $old_instance ) {
        $instance                 = array();
        $instance['info_title']   = sanitize_text_field( $new_instance['info_title'] ?? '' );
        $instance['recent_title'] = sanitize_text_field( $new_instance['recent_title'] ?? '' );
        $instance['recent_count'] = absint( $new_instance['recent_count'] ?? 3 );
        $instance['custom_html']  = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==========================================================================
   8. CONTACT FORM AREA WIDGET
   ========================================================================== */
class PDS_Contact_Form_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'pds_contact_form_area',
            __( '[Contact] Message Form Area', 'jobscout' ),
            array(
                'description'                 => __( 'Khối Form liên hệ trực tuyến trang Contact Us (hỗ trợ shortcode Contact Form 7).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    public function widget( $args, $instance ) {
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
            echo $args['after_widget'];
            return;
        }

        $title         = ! empty( $instance['title'] ) ? $instance['title'] : __( 'SEND US A MESSAGE', 'jobscout' );
        $subtitle      = ! empty( $instance['subtitle'] ) ? $instance['subtitle'] : __( 'Fill out the form below and we will get back to you shortly.', 'jobscout' );
        $cf7_shortcode = ! empty( $instance['cf7_shortcode'] ) ? $instance['cf7_shortcode'] : '[contact-form-7 id="66ddf06" title="NewSelecter"]';

        echo $args['before_widget'];
        ?>
        <section class="pds-contact-form-section" aria-labelledby="pds-contact-form-title" style="padding: 50px 20px; background: #ffffff;">
            <div class="pds-contact-container" style="max-width: 820px; margin: 0 auto; text-align: center;">
                <h2 id="pds-contact-form-title" style="font-size: 24px; font-weight: 700; color: #211916; margin-bottom: 8px; letter-spacing: 0.5px;">
                    <?php echo esc_html( $title ); ?>
                </h2>
                <?php if ( $subtitle ) : ?>
                    <p style="color: #696461; font-size: 14px; margin-bottom: 28px;"><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>

                <div class="pds-contact-form-wrapper" style="text-align: left;">
                    <?php
                    $rendered = ! empty( $cf7_shortcode ) ? do_shortcode( $cf7_shortcode ) : '';
                    if ( ! empty( $rendered ) && strpos( $rendered, '[contact-form-7' ) === false && stripos( $rendered, 'not found' ) === false ) {
                        echo $rendered;
                    } else {
                        ?>
                        <form class="pds-contact-custom-form" onsubmit="return false;" style="display: flex; flex-direction: column; gap: 14px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                                <input type="text" placeholder="<?php esc_attr_e( 'Your Name *', 'jobscout' ); ?>" required style="padding: 12px 16px; border: 1px solid #ddd; border-radius: 2px;">
                                <input type="email" placeholder="<?php esc_attr_e( 'Your Email *', 'jobscout' ); ?>" required style="padding: 12px 16px; border: 1px solid #ddd; border-radius: 2px;">
                            </div>
                            <input type="text" placeholder="<?php esc_attr_e( 'Subject', 'jobscout' ); ?>" style="padding: 12px 16px; border: 1px solid #ddd; border-radius: 2px;">
                            <textarea rows="5" placeholder="<?php esc_attr_e( 'Your Message *', 'jobscout' ); ?>" required style="padding: 12px 16px; border: 1px solid #ddd; border-radius: 2px;"></textarea>
                            <button type="submit" style="align-self: center; background: #f2761b; color: #fff; font-weight: 700; padding: 12px 36px; border: 0; cursor: pointer; text-transform: uppercase; border-radius: 2px;">
                                <?php esc_html_e( 'SUBMIT MESSAGE', 'jobscout' ); ?>
                            </button>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </section>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title         = isset( $instance['title'] ) ? $instance['title'] : __( 'SEND US A MESSAGE', 'jobscout' );
        $subtitle      = isset( $instance['subtitle'] ) ? $instance['subtitle'] : __( 'Fill out the form below and we will get back to you shortly.', 'jobscout' );
        $cf7_shortcode = isset( $instance['cf7_shortcode'] ) ? $instance['cf7_shortcode'] : '[contact-form-7 id="66ddf06" title="NewSelecter"]';

        $snippet = <<<HTML
<section class="pds-contact-form-section">
    <h2>SEND US A MESSAGE</h2>
    [contact-form-7 id="66ddf06" title="NewSelecter"]
</section>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề Form:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>"><?php esc_html_e( 'Mô tả ngắn dưới tiêu đề:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'subtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'cf7_shortcode' ) ); ?>"><?php esc_html_e( 'Shortcode Contact Form 7:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cf7_shortcode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cf7_shortcode' ) ); ?>" type="text" value="<?php echo esc_attr( $cf7_shortcode ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    public function update( $new_instance, $old_instance ) {
        $instance                  = array();
        $instance['title']         = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['subtitle']      = sanitize_text_field( $new_instance['subtitle'] ?? '' );
        $instance['cf7_shortcode'] = sanitize_text_field( $new_instance['cf7_shortcode'] ?? '' );
        $instance['custom_html']   = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}
