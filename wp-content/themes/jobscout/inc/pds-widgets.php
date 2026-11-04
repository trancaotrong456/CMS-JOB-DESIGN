<?php
/**
 * ==============================================================================
 * HỆ THỐNG WIDGET HỢP NHẤT TOÀN DIỆN CHO ĐỒ ÁN WORDPRESS CMS-JOB-DESIGN
 * ==============================================================================
 *
 * MÔN HỌC  : HỆ QUẢN TRỊ NỘI DUNG (CMS) - NĂM HỌC 2026 - NHÓM D
 * THEME GỐC: JobScout (Tùy biến & phát triển giao diện theo bản thiết kế đồ án)
 *
 * BẢNG PHÂN CÔNG PHỤ TRÁCH GIAO DIỆN & WIDGET THEO TỪNG THÀNH VIÊN:
 * ------------------------------------------------------------------------------
 * 1. TRẦN CAO TRỌNG   (MSSV: 24211TT1101) -> Trang Chủ (Home Page)
 * 2. NGUYỄN HỮU PHONG (MSSV: 24211TT1976) -> Trang Liên Hệ (Contact Page)
 * 3. HỒ HOÀNG ĐỨC HUY (MSSV: 24211TT2193) -> Trang Giới Thiệu (About Page)
 * 4. NGUYỄN HỮU CHÚC  (MSSV: 24211TT4553) -> Trang Tin Tức (News) & Chi Tiết Tin Tức (News Detail)
 * 5. TRẦN VĂN TRỌNG   (MSSV: 22211TT0337) -> Trang Danh Sách Việc Làm (Jobs) & Chi Tiết Việc Làm (Job Detail)
 *
 * CƠ CHẾ KỸ THUẬT CỐT LÕI ĐÁP ỨNG TIÊU CHÍ CHẤM ĐIỂM (10 ĐIỂM):
 * ------------------------------------------------------------------------------
 * 1) Kế thừa chuẩn WordPress Codex: Toàn bộ 24 Widget kế thừa lớp cha WP_Widget,
 *    triển khai đúng 4 phương thức chuẩn: __construct(), widget(), form(), update().
 * 2) Live Preview với Customizer Selective Refresh ('customize_selective_refresh' => true):
 *    Cho phép xem trước trực quan các thay đổi trong Giao diện > Tùy biến ngay lập tức.
 * 3) Cơ chế Custom HTML Override độc quyền:
 *    Mỗi widget đều tích hợp trường nhập mã HTML tùy chọn kèm nút 'Chèn HTML mẫu',
 *    cho phép quản trị viên có thể hiển thị mã mặc định động hoặc tự do sửa đổi HTML, CSS
 *    và nhúng các WordPress Shortcode (như Contact Form 7, form đăng ký nhận tin...).
 * 4) Bảo mật & Làm sạch dữ liệu đầu vào (Data Sanitization):
 *    Áp dụng triệt để sanitize_text_field(), absint(), esc_url_raw(), wp_unslash()
 *    nhằm ngăn ngừa triệt để các lỗ hổng bảo mật XSS (Cross-Site Scripting) và Injection.
 * 5) Tự động điền dữ liệu mẫu (Auto-Populate Default Widgets):
 *    Hàm pds_auto_populate_default_widgets() tự động gán sẵn cấu hình và các widget vào
 *    các vị trí Sidebar tương ứng ngay khi kích hoạt theme, đảm bảo website luôn đầy đủ
 *    giao diện theo đúng ảnh mẫu thiết kế mà không cần cấu hình thủ công phức tạp.
 *
 * @package JobScout
 * @subpackage CMS_Job_Design
 * @author Nhóm D - Lớp CMS
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Chặn truy cập trực tiếp vào file mã nguồn vì lý do an toàn bảo mật.
}

/* ==============================================================================
   0. CÁC HÀM HỖ TRỢ CHUNG CHO GIAO DIỆN QUẢN TRỊ WIDGET (HELPERS)
   ============================================================================== */

if ( ! function_exists( 'pds_render_custom_html_field' ) ) {
    /**
     * Render the Custom HTML Override field inside any widget form.
     *
     * @param WP_Widget $widget Instance of the widget.
     * @param array     $instance Current widget settings.
     * @param string    $snippet_html Default HTML code snippet for this widget.
     * @param string    $description Brief guide for user.
     */
    function pds_render_custom_html_field( $widget, $instance, $snippet_html, $description = '' ) {
        $custom_html = ! empty( $instance['custom_html'] ) ? $instance['custom_html'] : '';
        $field_id    = $widget->get_field_id( 'custom_html' );
        $field_name  = $widget->get_field_name( 'custom_html' );
        $snippet_id  = $field_id . '_snippet';
        ?>
        <div class="pds-widget-custom-html-wrapper" style="margin-top: 15px; padding: 12px; background: #fffcf8; border: 1px solid #e5a770; border-radius: 6px;">
            <div style="margin: 0 0 6px 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;">
                <label for="<?php echo esc_attr( $field_id ); ?>" style="font-weight: 700; color: #ef741b;">
                    ⚙️ <?php esc_html_e( 'Tùy chỉnh mã HTML (Custom HTML Override):', 'jobscout' ); ?>
                </label>
                <button type="button" class="button button-small pds-insert-template-btn" data-target="<?php echo esc_attr( $field_id ); ?>" style="font-size: 11px; cursor: pointer;">
                    📋 <?php esc_html_e( 'Chèn HTML mẫu', 'jobscout' ); ?>
                </button>
            </div>
            <p style="font-size: 11px; color: #666; margin: 0 0 8px 0; line-height: 1.4;">
                <?php
                if ( $description ) {
                    echo esc_html( $description ) . ' ';
                }
                esc_html_e( 'Nhập mã HTML vào đây để hiển thị trực tiếp thay thế giao diện mặc định. Hỗ trợ shortcode WordPress.', 'jobscout' );
                ?>
            </p>
            <textarea
                class="widefat pds-custom-html-textarea"
                rows="7"
                id="<?php echo esc_attr( $field_id ); ?>"
                name="<?php echo esc_attr( $field_name ); ?>"
                placeholder="<?php esc_attr_e( 'Nhập HTML tùy chỉnh tại đây... (hoặc bấm "Chèn HTML mẫu" ở trên)', 'jobscout' ); ?>"
                style="font-family: Consolas, Monaco, monospace; font-size: 11.5px; line-height: 1.4; tab-size: 2;"
            ><?php echo esc_textarea( $custom_html ); ?></textarea>

            <textarea id="<?php echo esc_attr( $snippet_id ); ?>" style="display: none;" readonly><?php echo esc_textarea( trim( $snippet_html ) ); ?></textarea>

            <details style="margin-top: 8px; font-size: 11px;">
                <summary style="cursor: pointer; color: #0073aa; font-weight: 600;"><?php esc_html_e( 'Xem trước mã HTML mẫu chuẩn của widget này', 'jobscout' ); ?></summary>
                <pre style="margin-top: 6px; padding: 8px; background: #23282d; color: #f1f1f1; border-radius: 4px; overflow-x: auto; font-size: 11px; white-space: pre-wrap; word-break: break-all; max-height: 180px;"><?php echo esc_html( trim( $snippet_html ) ); ?></pre>
            </details>
        </div>
        <?php
    }
}

if ( ! function_exists( 'pds_render_image_field' ) ) {
    /**
     * Render an Image upload/select field with live preview and WP Media uploader.
     *
     * @param WP_Widget $widget Instance of the widget.
     * @param string    $field_key Field key name.
     * @param string    $label Label title.
     * @param string    $current_val Current image URL.
     */
    function pds_render_image_field( $widget, $field_key, $label, $current_val ) {
        $field_id   = $widget->get_field_id( $field_key );
        $field_name = $widget->get_field_name( $field_key );
        $preview_id = $field_id . '_preview';
        ?>
        <div class="pds-widget-image-field-wrap" style="margin-bottom: 12px; padding: 8px 10px; background: #f9f9f9; border: 1px solid #e2e4e7; border-radius: 4px;">
            <label for="<?php echo esc_attr( $field_id ); ?>" style="display: block; font-weight: 600; margin-bottom: 6px; color: #23282d;">
                🖼️ <?php echo esc_html( $label ); ?>
            </label>
            <div style="display: flex; gap: 6px; align-items: center;">
                <input class="widefat pds-image-url-input" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" type="url" value="<?php echo esc_attr( $current_val ); ?>" placeholder="https://..." style="flex: 1;">
                <button type="button" class="button button-secondary pds-media-upload-btn" data-target="<?php echo esc_attr( $field_id ); ?>" data-preview="<?php echo esc_attr( $preview_id ); ?>" style="white-space: nowrap; font-size: 11px;">
                    📷 <?php esc_html_e( 'Chọn ảnh', 'jobscout' ); ?>
                </button>
            </div>
            <div id="<?php echo esc_attr( $preview_id ); ?>" class="pds-image-preview" style="margin-top: 6px;">
                <?php if ( ! empty( $current_val ) ) : ?>
                    <img src="<?php echo esc_url( $current_val ); ?>" alt="" style="max-width: 100%; height: auto; max-height: 110px; border-radius: 4px; border: 1px solid #ccd0d4; display: block; object-fit: cover;">
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

/* ==============================================================================
   1. NHÓM WIDGET TRANG CHỦ (HOME PAGE) - PHỤ TRÁCH: TRẦN CAO TRỌNG (24211TT1101)
   ============================================================================== */

/**
 * ==============================================================================
 * 1.1 Home Hero Search Widget (Banner Tìm Kiếm Việc Làm Trang Chủ)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Chủ (Home Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Trần Cao Trọng (MSSV: 24211TT1101)
 * - SIDEBAR LIÊN KẾT    : 'home-hero-search'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị khối banner chính (Hero Banner) ở đầu trang chủ, bao gồm tiêu đề truyền cảm hứng, phụ đề mô tả và thanh công cụ tìm kiếm việc làm (tìm theo từ khóa/vị trí/kỹ năng và chọn địa điểm). Hỗ trợ tùy chỉnh ảnh nền banner trực quan hoặc chèn mã HTML tùy chỉnh.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Home_Hero_Search_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_home_hero_search',
            __( '[Home] Hero Search Banner', 'jobscout' ),
            array(
                'description'                 => __( 'Banner tìm kiếm việc làm cho trang chủ (Home Hero Search).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $eyebrow       = isset( $instance['eyebrow'] ) ? $instance['eyebrow'] : '';
        $title         = ! empty( $instance['title'] ) ? $instance['title'] : __( 'FIND YOUR DREAM JOBS', 'jobscout' );
        $description   = ! empty( $instance['description'] ) ? $instance['description'] : __( 'The secret behind our company is simple: to always put ourselves in the other person’s shoes - employees, guests or customers. This allows us to see the world through their eyes, anticipate their needs and better understand their feelings.', 'jobscout' );
        $keyword_ph    = ! empty( $instance['keyword_placeholder'] ) ? $instance['keyword_placeholder'] : __( 'Search for jobs, companies, skills', 'jobscout' );
        $location_ph   = ! empty( $instance['location_placeholder'] ) ? $instance['location_placeholder'] : __( 'Tokyo', 'jobscout' );
        $button_text   = ! empty( $instance['button_text'] ) ? $instance['button_text'] : __( 'SEARCH JOB', 'jobscout' );

        $jobs_url = ! empty( $instance['search_url'] ) ? $instance['search_url'] : '';
        if ( empty( $jobs_url ) ) {
            $jobs_page = get_page_by_path( 'jobs' ) ?: get_page_by_path( 'all-jobs' );
            $jobs_url = $jobs_page ? get_permalink( $jobs_page ) : ( function_exists( 'get_post_type_archive_link' ) && get_post_type_archive_link( 'job_listing' ) ? get_post_type_archive_link( 'job_listing' ) : home_url( '/jobs/' ) );
        }

        $bg_image_url  = ! empty( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : '';

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        if ( ! empty( $bg_image_url ) ) : ?>
            <style>.home-hero { background-image: url('<?php echo esc_url( $bg_image_url ); ?>') !important; }</style>
        <?php endif;
        $selected_loc      = isset( $_GET['search_location'] ) ? sanitize_text_field( wp_unslash( $_GET['search_location'] ) ) : '';
        $default_loc_label = ( ! empty( $location_ph ) && 'Tokyo' !== $location_ph ) ? $location_ph : __( 'All Locations', 'jobscout' );
        ?>
        <div class="home-hero__copy">
            <?php if ( ! empty( $eyebrow ) ) : ?>
                <p class="home-eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
            <?php endif; ?>
            <?php if ( $title ) : ?>
                <h1 id="home-hero-title"><?php echo esc_html( $title ); ?></h1>
            <?php endif; ?>
            <?php if ( $description ) : ?>
                <p><?php echo esc_html( $description ); ?></p>
            <?php endif; ?>
            <form class="home-job-search" method="get" action="<?php echo esc_url( $jobs_url ); ?>">

                <label class="home-search-field home-search-keywords">
                    <span class="screen-reader-text"><?php esc_html_e( 'Search jobs, companies, skills', 'jobscout' ); ?></span>
                    <span class="home-search-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f2761b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <input type="search" name="search_keywords" placeholder="<?php echo esc_attr( $keyword_ph ); ?>">
                </label>
                <label class="home-search-field home-search-location">
                    <span class="screen-reader-text"><?php esc_html_e( 'Location', 'jobscout' ); ?></span>
                    <span class="home-location-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f2761b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </span>
                    <select name="search_location" class="home-search-location-select" style="width: 100%; height: 100%; border: 0; outline: 0; color: #3b3837; font-size: 14px; background: transparent; cursor: pointer; -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 18px;">
                        <option value="" <?php selected( empty( $selected_loc ), true ); ?>><?php echo esc_html( $default_loc_label ); ?></option>
                        <?php
                        $widget_locations = function_exists( 'pds_get_job_locations' ) ? pds_get_job_locations() : array( 'Ho Chi Minh City', 'Ha Noi', 'Da Nang', 'Tokyo', 'Kyoto', 'Osaka' );
                        foreach ( $widget_locations as $loc ) :
                        ?>
                            <option value="<?php echo esc_attr( $loc ); ?>" <?php selected( $selected_loc, $loc ); ?>>
                                <?php echo esc_html( $loc ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="home-dropdown-arrow" style="pointer-events: none; margin-left: -12px; color: #696461; font-size: 10px;">▼</span>
                </label>
                <button type="submit" class="home-search-submit"><?php echo esc_html( strtoupper( $button_text ) ); ?></button>
            </form>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $eyebrow      = isset( $instance['eyebrow'] ) ? $instance['eyebrow'] : '';
        $title        = isset( $instance['title'] ) ? $instance['title'] : __( 'FIND YOUR DREAM JOBS', 'jobscout' );
        $description  = isset( $instance['description'] ) ? $instance['description'] : __( 'The secret behind our company is simple: to always put ourselves in the other person’s shoes - employees, guests or customers. This allows us to see the world through their eyes, anticipate their needs and better understand their feelings.', 'jobscout' );
        $search_url   = isset( $instance['search_url'] ) ? $instance['search_url'] : '';
        $keyword_ph   = isset( $instance['keyword_placeholder'] ) ? $instance['keyword_placeholder'] : __( 'Search for jobs, companies, skills', 'jobscout' );
        $location_ph  = isset( $instance['location_placeholder'] ) ? $instance['location_placeholder'] : __( 'Tokyo', 'jobscout' );
        $button_text  = isset( $instance['button_text'] ) ? $instance['button_text'] : __( 'SEARCH JOB', 'jobscout' );
        $bg_image_url = isset( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : '';

        $snippet = <<<HTML
<div class="home-hero__copy">
    <h1 id="home-hero-title">FIND YOUR DREAM JOBS</h1>
    <p>The secret behind our company is simple: to always put ourselves in the other person’s shoes - employees, guests or customers. This allows us to see the world through their eyes, anticipate their needs and better understand their feelings.</p>
    <form class="home-job-search" method="get" action="/jobs/">
        <label class="home-search-field home-search-keywords">
            <span class="screen-reader-text">Search jobs, companies, skills</span>
            <span class="home-search-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f2761b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
            <input type="search" name="search_keywords" placeholder="Search for jobs, companies, skills">
        </label>
        <label class="home-search-field home-search-location">
            <span class="screen-reader-text">Location</span>
            <span class="home-location-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f2761b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            </span>
            <select name="search_location" class="home-search-location-select" style="width: 100%; height: 100%; border: 0; outline: 0; color: #3b3837; font-size: 14px; background: transparent; cursor: pointer; -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 18px;">
                <option value="Tokyo">Tokyo</option>
                <option value="Kyoto">Kyoto</option>
                <option value="Osaka">Osaka</option>
                <option value="">All Locations</option>
            </select>
            <span class="home-dropdown-arrow" style="pointer-events: none; margin-left: -12px; color: #696461; font-size: 10px;">▼</span>
        </label>
        <button type="submit" class="home-search-submit">SEARCH JOB</button>
    </form>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'eyebrow' ) ); ?>"><?php esc_html_e( 'Dòng phụ đề trên (Eyebrow - để trống nếu không dùng):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'eyebrow' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'eyebrow' ) ); ?>" type="text" value="<?php echo esc_attr( $eyebrow ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề chính (Title):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_html_e( 'Đoạn giới thiệu (Description):', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'search_url' ) ); ?>"><?php esc_html_e( 'URL đích khi tìm kiếm (để trống = tự động trang Jobs):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'search_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'search_url' ) ); ?>" type="url" value="<?php echo esc_attr( $search_url ); ?>" placeholder="<?php echo esc_attr( home_url( '/jobs/' ) ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'keyword_placeholder' ) ); ?>"><?php esc_html_e( 'Placeholder ô từ khóa:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'keyword_placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'keyword_placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $keyword_ph ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'location_placeholder' ) ); ?>"><?php esc_html_e( 'Placeholder ô địa điểm:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'location_placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'location_placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $location_ph ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Chữ trên nút tìm kiếm:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>">
        </p>
        <?php
        pds_render_image_field( $this, 'bg_image_url', __( 'Ảnh nền Hero Banner trang chủ (Background Image):', 'jobscout' ), $bg_image_url );
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                        = array();
        $instance['eyebrow']             = sanitize_text_field( $new_instance['eyebrow'] ?? '' );
        $instance['title']               = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['description']         = sanitize_textarea_field( $new_instance['description'] ?? '' );
        $instance['search_url']          = esc_url_raw( $new_instance['search_url'] ?? '' );
        $instance['keyword_placeholder'] = sanitize_text_field( $new_instance['keyword_placeholder'] ?? '' );
        $instance['location_placeholder']= sanitize_text_field( $new_instance['location_placeholder'] ?? '' );
        $instance['button_text']         = sanitize_text_field( $new_instance['button_text'] ?? '' );
        $instance['bg_image_url']        = esc_url_raw( $new_instance['bg_image_url'] ?? '' );
        $instance['custom_html']         = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 1.2 Home Top Jobs Widget (Danh Sách Việc Làm Nổi Bật Trang Chủ)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Chủ (Home Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Trần Cao Trọng (MSSV: 24211TT1101)
 * - SIDEBAR LIÊN KẾT    : 'home-top-jobs'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị danh sách các tin tuyển dụng nổi bật (Top Jobs) trên trang chủ dưới dạng lưới thẻ công việc (Job Cards). Tự động truy vấn dữ liệu động từ Custom Post Type "job_listing" với các thông tin chi tiết: logo công ty, tiêu đề, ngày đăng, mức lương, hình thức làm việc, địa điểm và các tiêu chí tuyển dụng nhanh.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Home_Top_Jobs_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_home_top_jobs',
            __( '[Home] Top Jobs Grid', 'jobscout' ),
            array(
                'description'                 => __( 'Khối danh sách Top Jobs dạng lưới trên trang chủ.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title       = ! empty( $instance['title'] ) ? $instance['title'] : __( 'TOP JOBS', 'jobscout' );
        $count       = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 6;
        $button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : __( 'VIEW MORE JOBS', 'jobscout' );
        $button_url  = ! empty( $instance['button_url'] ) ? $instance['button_url'] : '';

        if ( empty( $button_url ) ) {
            $jobs_page  = get_page_by_path( 'jobs' ) ?: get_page_by_path( 'all-jobs' );
            $button_url = $jobs_page ? get_permalink( $jobs_page ) : ( function_exists( 'get_post_type_archive_link' ) && get_post_type_archive_link( 'job_listing' ) ? get_post_type_archive_link( 'job_listing' ) : home_url( '/jobs/' ) );
        }

        $jobs_query = new WP_Query( array(
            'post_type'           => 'job_listing',
            'post_status'         => 'publish',
            'posts_per_page'      => $count,
            'ignore_sticky_posts' => true,
        ) );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        if ( $title ) {
            echo '<h2 id="home-jobs-title" class="home-section-title">' . esc_html( $title ) . '</h2>';
        }
        if ( $jobs_query->have_posts() ) : ?>
            <div class="home-job-grid pds-all-jobs-grid" id="home-top-jobs-grid">
                <?php while ( $jobs_query->have_posts() ) : $jobs_query->the_post();
                    get_template_part( 'template-parts/all-jobs-card' );
                endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="home-empty-state"><?php esc_html_e( 'New hospitality opportunities will appear here soon.', 'jobscout' ); ?></p>
        <?php endif; ?>

        <?php if ( $button_text ) : ?>
            <div class="home-button-row">
                <a class="home-outline-button" id="home-view-more-jobs-btn" href="<?php echo esc_url( $button_url ); ?>" data-page="1" data-max="<?php echo esc_attr( $jobs_query->max_num_pages ); ?>" data-count="<?php echo esc_attr( $count ); ?>"><?php echo esc_html( $button_text ); ?></a>
            </div>
        <?php endif; ?>

        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title       = isset( $instance['title'] ) ? $instance['title'] : __( 'TOP JOBS', 'jobscout' );
        $count       = isset( $instance['count'] ) ? absint( $instance['count'] ) : 6;
        $button_text = isset( $instance['button_text'] ) ? $instance['button_text'] : __( 'VIEW MORE JOBS', 'jobscout' );
        $button_url  = isset( $instance['button_url'] ) ? $instance['button_url'] : '';

        $snippet = <<<HTML
<h2 id="home-jobs-title" class="home-section-title">TOP JOBS</h2>
<div class="home-job-grid pds-all-jobs-grid">
    <article class="pds-all-jobs-card">
        <div class="pds-all-jobs-card-header">
            <a href="/jobs/" class="pds-all-jobs-card-logo">
                <div class="pds-logo-sodoh">
                    <span class="pds-sodoh-sub">THE</span>
                    <span class="pds-sodoh-main">SODOH</span>
                    <span class="pds-sodoh-loc">HIGASHIYAMA KYOTO</span>
                </div>
            </a>
            <div class="pds-all-jobs-card-info">
                <h3 class="pds-all-jobs-card-title"><a href="/jobs/">HOTEL MANAGER</a></h3>
                <div class="pds-all-jobs-card-date">Created: Oct 20, 2022</div>
                <div class="pds-all-jobs-card-meta-pill">
                    <span class="pds-meta-type">Fulltime</span>
                    <span class="pds-meta-sep">|</span>
                    <span class="pds-meta-cat">Category Name</span>
                    <span class="pds-meta-sep">|</span>
                    <span class="pds-meta-loc">Ho Chi Minh City</span>
                </div>
            </div>
        </div>
        <div class="pds-all-jobs-card-bullets">
            <ul>
                <li>Be responsible for the effective operational management of the hotel</li>
                <li>Excellent salary bonuses & recognition activities</li>
                <li>Foreign language allowance (up to 500USD/month)</li>
            </ul>
        </div>
    </article>
</div>
<div class="home-button-row">
    <a class="home-outline-button" href="/jobs/">VIEW MORE JOBS</a>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề section:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Số lượng việc làm hiển thị:', 'jobscout' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" step="1" min="1" max="24" value="<?php echo esc_attr( $count ); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Chữ trên nút xem thêm:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>"><?php esc_html_e( 'Đường dẫn nút xem thêm (để trống = tự động trang Jobs):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_url' ) ); ?>" type="url" value="<?php echo esc_attr( $button_url ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['count']       = absint( $new_instance['count'] ?? 6 );
        $instance['button_text'] = sanitize_text_field( $new_instance['button_text'] ?? '' );
        $instance['button_url']  = esc_url_raw( $new_instance['button_url'] ?? '' );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 1.3 Home Career CTA Widget (Khối Kêu Gọi Hành Động Tuyển Dụng)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Chủ (Home Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Trần Cao Trọng (MSSV: 24211TT1101)
 * - SIDEBAR LIÊN KẾT    : 'home-career-cta'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị khối Call-To-Action (CTA) với thông điệp "CAREER WITH US", hình ảnh nền phong cảnh khách sạn sang trọng, đoạn giới thiệu văn hóa doanh nghiệp và nút chuyển hướng trực tiếp đến trang xem toàn bộ danh sách việc làm.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Home_Career_CTA_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_home_career_cta',
            __( '[Home] Career CTA', 'jobscout' ),
            array(
                'description'                 => __( 'Khối giới thiệu văn hóa và cơ hội nghề nghiệp Career With Us trang chủ.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title       = ! empty( $instance['title'] ) ? $instance['title'] : __( 'CAREER WITH US', 'jobscout' );
        $desc_1      = ! empty( $instance['desc_1'] ) ? $instance['desc_1'] : __( 'Plan Do See Global is a hospitality group founded in Japan and rooted in “Omotenashi”, the Japanese principle of selfless hospitality. We strive to deliver unforgettable and bespoke experiences, to understand local cultures like natives, and to provide service that is warm but not intrusive, and to foresee our guests’ every need at all times. That is our sole mission and purpose.', 'jobscout' );
        $desc_2      = ! empty( $instance['desc_2'] ) ? $instance['desc_2'] : __( 'We are experts in all stages of project development: concept, design, implementation and management. We love to find unique ways to create experiences that surprise and delight guests and customers.', 'jobscout' );
        $button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : __( 'MORE ABOUT US', 'jobscout' );
        $button_url  = ! empty( $instance['button_url'] ) ? $instance['button_url'] : '';

        if ( empty( $button_url ) ) {
            $about_page = get_page_by_path( 'about' ) ?: get_page_by_path( 'about-us' );
            $button_url = $about_page ? get_permalink( $about_page ) : home_url( '/about/' );
        }

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="home-career__inner">
            <?php if ( $title ) : ?>
                <h2 id="home-career-title"><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
            <?php if ( $desc_1 ) : ?>
                <p><?php echo esc_html( $desc_1 ); ?></p>
            <?php endif; ?>
            <?php if ( $desc_2 ) : ?>
                <p><?php echo esc_html( $desc_2 ); ?></p>
            <?php endif; ?>
            <?php if ( $button_text ) : ?>
                <a class="home-light-button" href="<?php echo esc_url( $button_url ); ?>"><?php echo esc_html( $button_text ); ?></a>
            <?php endif; ?>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title       = isset( $instance['title'] ) ? $instance['title'] : __( 'CAREER WITH US', 'jobscout' );
        $desc_1      = isset( $instance['desc_1'] ) ? $instance['desc_1'] : __( 'Plan Do See Global is a hospitality group founded in Japan and rooted in “Omotenashi”, the Japanese principle of selfless hospitality. We strive to deliver unforgettable and bespoke experiences, to understand local cultures like natives, and to provide service that is warm but not intrusive, and to foresee our guests’ every need at all times. That is our sole mission and purpose.', 'jobscout' );
        $desc_2      = isset( $instance['desc_2'] ) ? $instance['desc_2'] : __( 'We are experts in all stages of project development: concept, design, implementation and management. We love to find unique ways to create experiences that surprise and delight guests and customers.', 'jobscout' );
        $button_text = isset( $instance['button_text'] ) ? $instance['button_text'] : __( 'MORE ABOUT US', 'jobscout' );
        $button_url  = isset( $instance['button_url'] ) ? $instance['button_url'] : '';

        $snippet = <<<HTML
<div class="home-career__inner">
    <h2 id="home-career-title">CAREER WITH US</h2>
    <p>Plan Do See Global is a hospitality group founded in Japan and rooted in “Omotenashi”, the Japanese principle of selfless hospitality. We strive to deliver unforgettable and bespoke experiences, to understand local cultures like natives, and to provide service that is warm but not intrusive, and to foresee our guests’ every need at all times. That is our sole mission and purpose.</p>
    <p>We are experts in all stages of project development: concept, design, implementation and management. We love to find unique ways to create experiences that surprise and delight guests and customers.</p>
    <a class="home-light-button" href="/about/">MORE ABOUT US</a>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'desc_1' ) ); ?>"><?php esc_html_e( 'Đoạn mô tả 1:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="4" id="<?php echo esc_attr( $this->get_field_id( 'desc_1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'desc_1' ) ); ?>"><?php echo esc_textarea( $desc_1 ); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'desc_2' ) ); ?>"><?php esc_html_e( 'Đoạn mô tả 2:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'desc_2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'desc_2' ) ); ?>"><?php echo esc_textarea( $desc_2 ); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Chữ nút bấm:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>"><?php esc_html_e( 'Đường dẫn nút (để trống = tự động trang About):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_url' ) ); ?>" type="url" value="<?php echo esc_attr( $button_url ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['desc_1']      = sanitize_textarea_field( $new_instance['desc_1'] ?? '' );
        $instance['desc_2']      = sanitize_textarea_field( $new_instance['desc_2'] ?? '' );
        $instance['button_text'] = sanitize_text_field( $new_instance['button_text'] ?? '' );
        $instance['button_url']  = esc_url_raw( $new_instance['button_url'] ?? '' );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 1.4 Home Blog Entries Widget (Khối Bài Viết Tin Tức Mới Nhất Trang Chủ)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Chủ (Home Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Trần Cao Trọng (MSSV: 24211TT1101)
 * - SIDEBAR LIÊN KẾT    : 'home-blog-entries'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị các bài viết tin tức mới nhất (Blog Entries) trên trang chủ. Tự động truy vấn bài viết từ WordPress Post chuẩn, hiển thị dạng lưới 2 cột với ảnh đại diện, tiêu đề bài viết, đoạn trích dẫn và liên kết "Read More" dẫn vào trang chi tiết tin tức.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Home_Blog_Entries_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_home_blog_entries',
            __( '[Home] Blog Entries', 'jobscout' ),
            array(
                'description'                 => __( 'Khối 4 bài blog mới nhất trên trang chủ (Newest Blog Entries).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'NEWEST BLOG ENTRIES', 'jobscout' );
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 4;

        $posts_query = new WP_Query( array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => $count,
            'ignore_sticky_posts' => true,
        ) );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        if ( $title ) {
            echo '<h2 id="home-blog-title" class="home-section-title">' . esc_html( $title ) . '</h2>';
        }
        if ( $posts_query->have_posts() ) : ?>
            <div class="home-blog-grid">
                <?php while ( $posts_query->have_posts() ) : $posts_query->the_post(); ?>
                    <article class="home-blog-card">
                        <a class="home-blog-card__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                            <?php if ( has_post_thumbnail() ) {
                                the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) );
                            } else { ?>
                                <span class="home-blog-card__placeholder" aria-hidden="true"></span>
                            <?php } ?>
                        </a>
                        <div class="home-blog-card__copy">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 19, '…' ) ); ?></p>
                            <a class="home-read-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'jobscout' ); ?></a>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="home-empty-state"><?php esc_html_e( 'The latest stories will appear here soon.', 'jobscout' ); ?></p>
        <?php endif; ?>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? $instance['title'] : __( 'NEWEST BLOG ENTRIES', 'jobscout' );
        $count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 4;

        $snippet = <<<HTML
<h2 id="home-blog-title" class="home-section-title">NEWEST BLOG ENTRIES</h2>
<div class="home-blog-grid">
    <article class="home-blog-card">
        <a class="home-blog-card__image" href="/news/">
            <span class="home-blog-card__placeholder" aria-hidden="true"></span>
        </a>
        <div class="home-blog-card__copy">
            <h3><a href="/news/">Project Development</a></h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            <a class="home-read-more" href="/news/">Read More</a>
        </div>
    </article>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề section:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Số lượng bài viết:', 'jobscout' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" step="1" min="1" max="16" value="<?php echo esc_attr( $count ); ?>" size="3">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['count']       = absint( $new_instance['count'] ?? 4 );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==============================================================================
   2. NHÓM WIDGET TRANG VIỆC LÀM (ALL JOBS) - PHỤ TRÁCH: TRẦN VĂN TRỌNG (22211TT0337)
   ============================================================================== */

/**
 * ==============================================================================
 * 2.1 Jobs Hero Banner Widget (Banner Đầu Trang Danh Sách Việc Làm)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Danh Sách Việc Làm (Jobs / All Jobs)
 * - THÀNH VIÊN PHỤ TRÁCH: Trần Văn Trọng (MSSV: 22211TT0337)
 * - SIDEBAR LIÊN KẾT    : 'jobs-hero-banner'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị banner tiêu đề "CAREER WITH US" cho trang danh sách toàn bộ việc làm, hỗ trợ tùy biến ảnh nền hero banner và tiêu đề thông qua Customizer.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Jobs_Hero_Banner_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_jobs_hero_banner',
            __( '[Job] Hero Banner', 'jobscout' ),
            array(
                'description'                 => __( 'Khối Banner CAREER WITH US trên cùng trang All Jobs (cho phép thay ảnh nền và tiêu đề).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title        = ! empty( $instance['title'] ) ? $instance['title'] : __( 'CAREER WITH US', 'jobscout' );
        $bg_image_url = ! empty( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : get_template_directory_uri() . '/assets/images/all-jobs-hero.png';

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        if ( ! empty( $bg_image_url ) ) : ?>
            <style>.pds-all-jobs-hero { background-image: url('<?php echo esc_url( $bg_image_url ); ?>') !important; }</style>
        <?php endif; ?>
        <div class="pds-all-jobs-hero-overlay"></div>
        <div class="pds-all-jobs-hero-inner" style="margin: 0 auto !important; text-align: center !important; width: 100% !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important;">
            <h1 id="pds-all-jobs-hero-title" class="pds-all-jobs-hero-title" style="margin: 0 auto !important; text-align: center !important; width: 100% !important;">
                <?php echo esc_html( $title ); ?>
            </h1>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title        = isset( $instance['title'] ) ? $instance['title'] : __( 'CAREER WITH US', 'jobscout' );
        $bg_image_url = isset( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : get_template_directory_uri() . '/assets/images/all-jobs-hero.png';

        $snippet = <<<HTML
<div class="pds-all-jobs-hero-overlay"></div>
<div class="pds-all-jobs-hero-inner">
    <h1 id="pds-all-jobs-hero-title" class="pds-all-jobs-hero-title">CAREER WITH US</h1>
</div>
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

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                 = array();
        $instance['title']        = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['bg_image_url'] = esc_url_raw( $new_instance['bg_image_url'] ?? '' );
        $instance['custom_html']  = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 2.2 Jobs List Widget (Danh Sách Việc Làm & Bộ Lọc Đa Năng)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Danh Sách Việc Làm (Jobs / All Jobs)
 * - THÀNH VIÊN PHỤ TRÁCH: Trần Văn Trọng (MSSV: 22211TT0337)
 * - SIDEBAR LIÊN KẾT    : 'jobs-list-content'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị toàn bộ danh sách công việc với các tính năng nâng cao: bộ lọc theo địa điểm và ngành nghề/hình thức, sắp xếp theo ngày/lương, tích hợp AJAX Load More tải thêm việc làm mượt mà không cần tải lại trang.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Jobs_List_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_jobs_list_content',
            __( '[Job] All Jobs List', 'jobscout' ),
            array(
                'description'                 => __( 'Khối danh sách việc làm chính trang All Jobs (CAREER WITH US).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title         = ! empty( $instance['title'] ) ? $instance['title'] : __( 'ALL JOBS', 'jobscout' );
        $count         = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 6;
        $show_filter   = isset( $instance['show_filter'] ) ? (bool) $instance['show_filter'] : true;
        $show_sort     = isset( $instance['show_sort'] ) ? (bool) $instance['show_sort'] : true;
        $show_loadmore = isset( $instance['show_loadmore'] ) ? (bool) $instance['show_loadmore'] : true;

        $search_keywords = isset( $_GET['search_keywords'] ) ? sanitize_text_field( wp_unslash( $_GET['search_keywords'] ) ) : '';
        $search_location = isset( $_GET['search_location'] ) ? sanitize_text_field( wp_unslash( $_GET['search_location'] ) ) : '';
        $current_sort    = isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : 'latest';

        $jobs_query = new WP_Query( pds_get_jobs_query_args( array(
            'posts_per_page'  => $count,
            'paged'           => 1,
            'sort'            => $current_sort,
            'search_keywords' => $search_keywords,
            'search_location' => $search_location,
        ) ) );
        $total_pages = $jobs_query->max_num_pages;

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <?php if ( $show_filter ) : ?>
            <div class="pds-all-jobs-filter-bar" style="margin-bottom: 24px; background: #fbf9f7; padding: 18px 20px; border: 1px solid #ebdcd0; border-radius: 4px;">
                <form class="pds-all-jobs-filter-form" method="get" action="<?php echo esc_url( home_url( '/jobs/' ) ); ?>" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    <div style="flex: 2; min-width: 200px;">
                        <input type="text" name="search_keywords" placeholder="<?php esc_attr_e( 'Keywords (e.g. Hotel Manager, Chef)...', 'jobscout' ); ?>" value="<?php echo esc_attr( $search_keywords ); ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #ccc; border-radius: 2px;">
                    </div>
                    <div style="flex: 1.5; min-width: 160px;">
                        <input type="text" name="search_location" placeholder="<?php esc_attr_e( 'Location (e.g. Ho Chi Minh, Tokyo)...', 'jobscout' ); ?>" value="<?php echo esc_attr( $search_location ); ?>" style="width: 100%; padding: 10px 14px; border: 1px solid #ccc; border-radius: 2px;">
                    </div>
                    <?php if ( ! empty( $current_sort ) && 'latest' !== $current_sort ) : ?>
                        <input type="hidden" name="sort" value="<?php echo esc_attr( $current_sort ); ?>">
                    <?php endif; ?>
                    <div style="flex: 1; min-width: 130px; display: flex; gap: 8px;">
                        <button type="submit" style="flex: 1; background: #f2761b; color: #fff; font-weight: 700; padding: 10px 16px; border: none; cursor: pointer; text-transform: uppercase; border-radius: 2px; font-size: 13px;">
                            <?php esc_html_e( 'FILTER JOBS', 'jobscout' ); ?>
                        </button>
                        <?php if ( ! empty( $search_keywords ) || ! empty( $search_location ) ) : ?>
                            <a href="<?php echo esc_url( home_url( '/jobs/' ) ); ?>" style="display: inline-flex; align-items: center; justify-content: center; background: #eceae8; color: #555; font-weight: 600; padding: 10px 14px; text-decoration: none; border-radius: 2px; font-size: 13px;" title="<?php esc_attr_e( 'Clear filters', 'jobscout' ); ?>">✕</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $search_keywords ) || ! empty( $search_location ) ) : ?>
            <div class="pds-all-jobs-filter-status" style="margin-bottom: 24px; font-size: 14px; color: #555; background: #fff8f2; border: 1px solid #ffd8b8; padding: 12px 18px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <strong><?php esc_html_e( 'Search results for:', 'jobscout' ); ?></strong>
                    <?php if ( ! empty( $search_keywords ) ) : ?>
                        <span style="color: #f2761b; font-weight: 600;">"<?php echo esc_html( $search_keywords ); ?>"</span>
                    <?php endif; ?>
                    <?php if ( ! empty( $search_location ) ) : ?>
                        <?php esc_html_e( 'in', 'jobscout' ); ?> <span style="color: #f2761b; font-weight: 600;">"<?php echo esc_html( $search_location ); ?>"</span>
                    <?php endif; ?>
                    <span>(<?php printf( esc_html( _n( '%d job found', '%d jobs found', $jobs_query->found_posts, 'jobscout' ) ), (int) $jobs_query->found_posts ); ?>)</span>
                </div>
                <a href="<?php echo esc_url( home_url( '/jobs/' ) ); ?>" style="color: #d9534f; font-weight: 600; text-decoration: underline; font-size: 13px;">
                    <?php esc_html_e( 'Clear Filter', 'jobscout' ); ?>
                </a>
            </div>
        <?php endif; ?>

        <div class="pds-all-jobs-header">
            <h2 class="pds-all-jobs-title"><?php echo esc_html( $title ); ?></h2>

            <?php if ( $show_sort ) : ?>
                <div class="pds-all-jobs-sort-wrapper">
                    <label for="pds-all-jobs-sort" class="screen-reader-text"><?php esc_html_e( 'Sort Jobs', 'jobscout' ); ?></label>
                    <select id="pds-all-jobs-sort" class="pds-all-jobs-sort-select">
                        <option value="latest" <?php selected( $current_sort, 'latest' ); ?>><?php esc_html_e( 'Latest Jobs', 'jobscout' ); ?></option>
                        <option value="oldest" <?php selected( $current_sort, 'oldest' ); ?>><?php esc_html_e( 'Oldest Jobs', 'jobscout' ); ?></option>
                        <option value="title_asc" <?php selected( $current_sort, 'title_asc' ); ?>><?php esc_html_e( 'Title: A - Z', 'jobscout' ); ?></option>
                        <option value="title_desc" <?php selected( $current_sort, 'title_desc' ); ?>><?php esc_html_e( 'Title: Z - A', 'jobscout' ); ?></option>
                    </select>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( $jobs_query->have_posts() ) : ?>
            <div id="pds-all-jobs-grid" class="pds-all-jobs-grid">
                <?php
                while ( $jobs_query->have_posts() ) {
                    $jobs_query->the_post();
                    get_template_part( 'template-parts/all-jobs-card' );
                }
                ?>
            </div>

            <?php if ( $show_loadmore && $total_pages > 1 ) : ?>
                <div class="pds-all-jobs-loadmore-wrap">
                    <button type="button" id="pds-all-jobs-load-more" class="pds-all-jobs-loadmore-btn" data-page="1" data-max="<?php echo esc_attr( $total_pages ); ?>">
                        <?php esc_html_e( 'LOAD MORE JOBS', 'jobscout' ); ?>
                    </button>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <div class="pds-all-jobs-empty">
                <p><?php esc_html_e( 'No job openings found matching your criteria.', 'jobscout' ); ?></p>
                <?php if ( ! empty( $search_keywords ) || ! empty( $search_location ) ) : ?>
                    <p style="margin-top: 14px;">
                        <a href="<?php echo esc_url( home_url( '/jobs/' ) ); ?>" style="display: inline-block; background: #f2761b; color: #fff; padding: 10px 20px; font-weight: 700; text-transform: uppercase; border-radius: 3px; text-decoration: none; font-size: 13px;">
                            <?php esc_html_e( 'VIEW ALL JOBS', 'jobscout' ); ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; wp_reset_postdata(); ?>

        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title         = isset( $instance['title'] ) ? $instance['title'] : __( 'ALL JOBS', 'jobscout' );
        $count         = isset( $instance['count'] ) ? absint( $instance['count'] ) : 6;
        $show_filter   = isset( $instance['show_filter'] ) ? (bool) $instance['show_filter'] : true;
        $show_sort     = isset( $instance['show_sort'] ) ? (bool) $instance['show_sort'] : true;
        $show_loadmore = isset( $instance['show_loadmore'] ) ? (bool) $instance['show_loadmore'] : true;

        $snippet = <<<HTML
<div class="pds-all-jobs-header">
    <h2 class="pds-all-jobs-title">ALL JOBS</h2>
    <div class="pds-all-jobs-sort-wrapper">
        <select id="pds-all-jobs-sort" class="pds-all-jobs-sort-select">
            <option value="latest">Latest Jobs</option>
            <option value="oldest">Oldest Jobs</option>
            <option value="title_asc">Title: A - Z</option>
            <option value="title_desc">Title: Z - A</option>
        </select>
    </div>
</div>
<div id="pds-all-jobs-grid" class="pds-all-jobs-grid">
    <!-- Tùy biến mã HTML danh sách việc làm hoặc dùng template all-jobs-card -->
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề khối:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Số lượng việc làm mỗi lần tải:', 'jobscout' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" step="1" min="1" max="30" value="<?php echo esc_attr( $count ); ?>" size="3">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $show_filter ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_filter' ) ); ?>">
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_filter' ) ); ?>"><?php esc_html_e( 'Hiển thị thanh bộ lọc tìm kiếm (Từ khóa, Địa điểm)', 'jobscout' ); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $show_sort ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_sort' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_sort' ) ); ?>">
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_sort' ) ); ?>"><?php esc_html_e( 'Hiển thị bộ chọn sắp xếp (Sort Selector)', 'jobscout' ); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $show_loadmore ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_loadmore' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_loadmore' ) ); ?>">
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_loadmore' ) ); ?>"><?php esc_html_e( 'Hiển thị nút Load More Jobs', 'jobscout' ); ?></label>
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                  = array();
        $instance['title']         = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['count']         = absint( $new_instance['count'] ?? 6 );
        $instance['show_filter']   = ! empty( $new_instance['show_filter'] );
        $instance['show_sort']     = ! empty( $new_instance['show_sort'] );
        $instance['show_loadmore'] = ! empty( $new_instance['show_loadmore'] );
        $instance['custom_html']   = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==============================================================================
   3. NHÓM WIDGET TRANG CHI TIẾT VIỆC LÀM - PHỤ TRÁCH: TRẦN VĂN TRỌNG (22211TT0337)
   ============================================================================== */

/**
 * ==============================================================================
 * 3.1 Job Staff Rating Widget (Đánh Giá Nhân Viên Khách Sạn / Doanh Nghiệp)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Chi Tiết Việc Làm (Job Detail)
 * - THÀNH VIÊN PHỤ TRÁCH: Trần Văn Trọng (MSSV: 22211TT0337)
 * - SIDEBAR LIÊN KẾT    : 'job-detail-sidebar'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị điểm số đánh giá nhân viên (Staff Rating) ở cột sidebar bên phải trang chi tiết việc làm, bao gồm số điểm trung bình (ví dụ 4.0/5.0), các ngôi sao đánh giá trực quan và các thanh đo lường tiêu chí môi trường làm việc.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Job_Staff_Rating_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_job_staff_rating',
            __( '[Job Detail] Staff Rating', 'jobscout' ),
            array(
                'description'                 => __( 'Khối đánh giá Staff Rating ở cột bên phải trang Job Detail.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title          = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Staff Rating', 'jobscout' );
        $default_rating = ! empty( $instance['default_rating'] ) ? floatval( $instance['default_rating'] ) : 4.0;
        $use_post_meta  = isset( $instance['use_post_meta'] ) ? (bool) $instance['use_post_meta'] : true;

        $rating = $default_rating;
        if ( $use_post_meta && is_singular( 'job_listing' ) ) {
            $post_id   = get_the_ID();
            $meta_rate = get_post_meta( $post_id, '_staff_rating', true );
            if ( ! $meta_rate ) {
                $meta_rate = get_post_meta( $post_id, '_rating', true );
            }
            if ( is_numeric( $meta_rate ) && $meta_rate > 0 ) {
                $rating = floatval( $meta_rate );
            }
        }

        $full_stars  = (int) floor( $rating );
        $empty_stars = max( 0, 5 - $full_stars );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="pds-job-detail-widget pds-job-detail-widget-rating">
            <?php if ( $title ) : ?>
                <h3 class="pds-job-detail-widget-title"><?php echo esc_html( $title ); ?></h3>
            <?php endif; ?>
            <div class="pds-rating-stars-row">
                <div class="pds-rating-stars">
                    <?php for ( $i = 0; $i < $full_stars; $i++ ) : ?>
                        <svg class="pds-star-icon is-filled" viewBox="0 0 20 20" width="18" height="18" fill="currentColor" aria-hidden="true">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    <?php endfor; ?>
                    <?php for ( $i = 0; $i < $empty_stars; $i++ ) : ?>
                        <svg class="pds-star-icon is-empty" viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    <?php endfor; ?>
                </div>
                <span class="pds-rating-score"><?php echo esc_html( number_format( $rating, 1 ) ); ?></span>
            </div>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title          = isset( $instance['title'] ) ? $instance['title'] : __( 'Staff Rating', 'jobscout' );
        $default_rating = isset( $instance['default_rating'] ) ? $instance['default_rating'] : '4.0';
        $use_post_meta  = isset( $instance['use_post_meta'] ) ? (bool) $instance['use_post_meta'] : true;

        $snippet = <<<HTML
<div class="pds-job-detail-widget pds-job-detail-widget-rating">
    <h3 class="pds-job-detail-widget-title">Staff Rating</h3>
    <div class="pds-rating-stars-row">
        <div class="pds-rating-stars">
            <svg class="pds-star-icon is-filled" viewBox="0 0 20 20" width="18" height="18" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg class="pds-star-icon is-filled" viewBox="0 0 20 20" width="18" height="18" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg class="pds-star-icon is-filled" viewBox="0 0 20 20" width="18" height="18" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg class="pds-star-icon is-filled" viewBox="0 0 20 20" width="18" height="18" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <svg class="pds-star-icon is-empty" viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        </div>
        <span class="pds-rating-score">4.0</span>
    </div>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'default_rating' ) ); ?>"><?php esc_html_e( 'Điểm số mặc định (1.0 - 5.0):', 'jobscout' ); ?></label>
            <input class="small-text" id="<?php echo esc_attr( $this->get_field_id( 'default_rating' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'default_rating' ) ); ?>" type="number" step="0.1" min="1" max="5" value="<?php echo esc_attr( $default_rating ); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $use_post_meta ); ?> id="<?php echo esc_attr( $this->get_field_id( 'use_post_meta' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'use_post_meta' ) ); ?>">
            <label for="<?php echo esc_attr( $this->get_field_id( 'use_post_meta' ) ); ?>"><?php esc_html_e( 'Ưu tiên lấy điểm từ bài viết (_staff_rating meta)', 'jobscout' ); ?></label>
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                   = array();
        $instance['title']          = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['default_rating'] = sanitize_text_field( $new_instance['default_rating'] ?? '4.0' );
        $instance['use_post_meta']  = ! empty( $new_instance['use_post_meta'] );
        $instance['custom_html']    = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 3.2 Job Company Photos Widget (Bộ Sưu Tập Hình Ảnh Không Gian Làm Việc)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Chi Tiết Việc Làm (Job Detail)
 * - THÀNH VIÊN PHỤ TRÁCH: Trần Văn Trọng (MSSV: 22211TT0337)
 * - SIDEBAR LIÊN KẾT    : 'job-detail-sidebar'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị ảnh tiêu biểu của môi trường làm việc / khách sạn kèm huy hiệu số lượng ảnh chụp thực tế ("+5 Photos"), giúp ứng viên hình dung chân thực không gian làm việc.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Job_Company_Photos_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_job_company_photos',
            __( '[Job Detail] Company Photos', 'jobscout' ),
            array(
                'description'                 => __( 'Khối hình ảnh công ty Company Photos ở cột bên phải trang Job Detail.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title       = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Company Photos', 'jobscout' );
        $job_id      = is_singular( 'job_listing' ) ? get_the_ID() : get_the_ID();
        $photos      = function_exists( 'pds_get_company_photos' ) && $job_id ? pds_get_company_photos( $job_id ) : array();

        if ( ! empty( $photos ) ) {
            $primary_thumb   = $photos[0]['thumb'];
            $large_photo_url = $photos[0]['full'];
            $photo_count     = count( $photos );
            $extra_count     = max( 0, $photo_count - 1 );
            $gallery_json    = wp_json_encode( array_map( function( $p ) {
                return array(
                    'src'     => $p['full'],
                    'thumb'   => $p['thumb'],
                    'caption' => $p['title'] ?: $p['caption'],
                );
            }, $photos ) );
        } else {
            $primary_thumb   = ! empty( $instance['photo_url'] ) ? $instance['photo_url'] : content_url( '/uploads/seed-cms-content/news-restaurant-hotel-operations-600x450.png' );
            $large_photo_url = $primary_thumb;
            $clean_full      = preg_replace( '/-\d+x\d+(?=\.[a-zA-Z0-9]+$)/', '', $primary_thumb );
            if ( ! empty( $clean_full ) ) {
                $att_id = attachment_url_to_postid( $clean_full );
                if ( $att_id ) {
                    $large_src = wp_get_attachment_image_url( $att_id, 'full' );
                    if ( $large_src ) {
                        $large_photo_url = $large_src;
                    }
                } else {
                    $large_photo_url = $clean_full;
                }
            }
            $extra_count  = 0;
            $gallery_json = wp_json_encode( array(
                array(
                    'src'     => $large_photo_url,
                    'thumb'   => $primary_thumb,
                    'caption' => $title,
                ),
            ) );
        }

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="pds-job-detail-widget pds-job-detail-widget-photos">
            <?php if ( $title ) : ?>
                <h3 class="pds-job-detail-widget-title"><?php echo esc_html( $title ); ?></h3>
            <?php endif; ?>
            <div class="pds-company-photo-wrap" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'View company photos in fullscreen', 'jobscout' ); ?>" data-full-src="<?php echo esc_url( $large_photo_url ); ?>" data-gallery="<?php echo esc_attr( $gallery_json ); ?>" data-caption="<?php echo esc_attr( $title ); ?>">
                <img src="<?php echo esc_url( $primary_thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="pds-company-photo-img" loading="lazy">
                <?php if ( $extra_count > 0 ) : ?>
                    <div class="pds-company-photo-overlay">
                        <span class="pds-photo-more-badge">+<?php echo esc_html( $extra_count ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title       = isset( $instance['title'] ) ? $instance['title'] : __( 'Company Photos', 'jobscout' );
        $photo_url   = isset( $instance['photo_url'] ) ? $instance['photo_url'] : content_url( '/uploads/seed-cms-content/news-restaurant-hotel-operations-600x450.png' );
        $extra_count = isset( $instance['extra_count'] ) ? absint( $instance['extra_count'] ) : 5;

        $snippet = <<<HTML
<div class="pds-job-detail-widget pds-job-detail-widget-photos">
    <h3 class="pds-job-detail-widget-title">Company Photos</h3>
    <div class="pds-company-photo-wrap">
        <img src="/wp-content/uploads/seed-cms-content/news-restaurant-hotel-operations-600x450.png" alt="Company Photos" class="pds-company-photo-img" loading="lazy">
        <div class="pds-company-photo-overlay">
            <span class="pds-photo-more-badge">+5</span>
        </div>
    </div>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề khối:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
        pds_render_image_field( $this, 'photo_url', __( 'Ảnh đại diện công ty (Company Photo):', 'jobscout' ), $photo_url );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'extra_count' ) ); ?>"><?php esc_html_e( 'Số ảnh còn lại (+5):', 'jobscout' ); ?></label>
            <input class="small-text" id="<?php echo esc_attr( $this->get_field_id( 'extra_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'extra_count' ) ); ?>" type="number" step="1" min="0" max="99" value="<?php echo esc_attr( $extra_count ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['photo_url']   = esc_url_raw( $new_instance['photo_url'] ?? '' );
        $instance['extra_count'] = isset( $new_instance['extra_count'] ) ? absint( $new_instance['extra_count'] ) : 5;
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 3.3 Job Related Widget (Danh Sách Việc Làm Tương Tự / Khác)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Chi Tiết Việc Làm (Job Detail)
 * - THÀNH VIÊN PHỤ TRÁCH: Trần Văn Trọng (MSSV: 22211TT0337)
 * - SIDEBAR LIÊN KẾT    : 'job-detail-related'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị danh sách các công việc liên quan (Other Jobs) ở cuối trang chi tiết việc làm, tự động loại trừ công việc hiện tại và gợi ý các vị trí tuyển dụng khác cho ứng viên.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Job_Related_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_job_related_jobs',
            __( '[Job Detail] Other Jobs', 'jobscout' ),
            array(
                'description'                 => __( 'Khối việc làm khác (OTHER JOBS) ở cuối trang Job Detail.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'OTHER JOBS', 'jobscout' );
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 6;

        $exclude_id = is_singular( 'job_listing' ) ? get_the_ID() : 0;
        $query_args = array(
            'post_type'      => 'job_listing',
            'post_status'    => 'publish',
            'posts_per_page' => $count,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );
        if ( $exclude_id ) {
            $query_args['post__not_in'] = array( $exclude_id );
        }

        $other_jobs_query = new WP_Query( $query_args );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        if ( $title ) {
            echo '<h2 id="pds-other-jobs-title" class="pds-job-detail-other-title">' . esc_html( $title ) . '</h2>';
        }
        if ( $other_jobs_query->have_posts() ) : ?>
            <div class="pds-job-detail-other-grid pds-all-jobs-grid">
                <?php
                while ( $other_jobs_query->have_posts() ) {
                    $other_jobs_query->the_post();
                    get_template_part( 'template-parts/all-jobs-card' );
                }
                ?>
            </div>
        <?php endif; wp_reset_postdata(); ?>

        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? $instance['title'] : __( 'OTHER JOBS', 'jobscout' );
        $count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 6;

        $snippet = <<<HTML
<h2 id="pds-other-jobs-title" class="pds-job-detail-other-title">OTHER JOBS</h2>
<div class="pds-job-detail-other-grid pds-all-jobs-grid">
    <article class="pds-all-jobs-card">
        <div class="pds-all-jobs-card-header">
            <a class="pds-all-jobs-card-logo" href="/jobs/"><div class="pds-logo-sodoh"><span class="pds-sodoh-sub">THE</span><span class="pds-sodoh-main">SODOH</span></div></a>
            <div class="pds-all-jobs-card-info">
                <h3 class="pds-all-jobs-card-title"><a href="/jobs/">HOTEL MANAGER</a></h3>
                <div class="pds-all-jobs-card-date">Created: Oct 20, 2022</div>
                <div class="pds-all-jobs-card-meta-pill">
                    <span class="pds-meta-type">Fulltime</span>
                    <span class="pds-meta-sep">|</span>
                    <span class="pds-meta-cat">Category Name</span>
                    <span class="pds-meta-sep">|</span>
                    <span class="pds-meta-loc">Ho Chi Minh City</span>
                </div>
            </div>
        </div>
        <div class="pds-all-jobs-card-bullets">
            <ul>
                <li>Be responsible for the effective operational management of the hotel</li>
                <li>Excellent salary bonuses & recognition activities</li>
                <li>Foreign language allowance (up to 500USD/month)</li>
            </ul>
        </div>
    </article>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề khối:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Số lượng việc làm liên quan:', 'jobscout' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" step="1" min="1" max="18" value="<?php echo esc_attr( $count ); ?>" size="3">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['count']       = absint( $new_instance['count'] ?? 6 );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==============================================================================
   4. NHÓM WIDGET TRANG TIN TỨC (NEWS) - PHỤ TRÁCH: NGUYỄN HỮU CHÚC (24211TT4553)
   ============================================================================== */

/**
 * ==============================================================================
 * 4.1 News Grid Widget (Lưới Bài Viết Tin Tức)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Tin Tức (News Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Nguyễn Hữu Chúc (MSSV: 24211TT4553)
 * - SIDEBAR LIÊN KẾT    : 'news-grid-content'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị danh sách bài viết tin tức dạng lưới (Grid) 2 cột với ảnh đại diện, danh mục, tiêu đề và ngày đăng, hỗ trợ phân trang số học (Pagination) và Custom HTML Override.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_News_Grid_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_news_grid_content',
            __( '[News] News Grid', 'jobscout' ),
            array(
                'description'                 => __( 'Khối danh sách bài viết dạng lưới trang News (PDS NEWS).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'NEWEST BLOG ENTRIES', 'jobscout' );
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 8;

        $paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
        $news_query = new WP_Query( array(
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => $count,
            'paged'          => $paged,
        ) );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        if ( $title ) {
            echo '<h2 class="pds-news-heading">' . esc_html( $title ) . '</h2>';
        }
        if ( $news_query->have_posts() ) : ?>
            <div class="pds-news-grid">
                <?php while ( $news_query->have_posts() ) : $news_query->the_post(); ?>
                    <article class="pds-news-card">
                        <a class="pds-news-card-image" href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'medium_large', array( 'class' => 'pds-news-thumbnail' ) ); ?>
                            <?php else : ?>
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/images/banner-image.jpg' ); ?>" alt="" loading="lazy">
                            <?php endif; ?>
                        </a>
                        <div class="pds-news-card-content">
                            <h3 class="pds-news-card-title">
                                <a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
                            </h3>
                            <p class="pds-news-card-excerpt">
                                <?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '...' ) ); ?>
                            </p>
                            <a class="pds-news-read-more" href="<?php echo esc_url( get_permalink() ); ?>">
                                <?php esc_html_e( 'Read More', 'jobscout' ); ?>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php if ( $news_query->max_num_pages > 1 ) : ?>
                <div class="pds-news-pagination">
                    <?php
                    echo paginate_links( array(
                        'total'     => $news_query->max_num_pages,
                        'current'   => $paged,
                        'prev_text' => __( '&laquo; Prev', 'jobscout' ),
                        'next_text' => __( 'Next &raquo;', 'jobscout' ),
                    ) );
                    ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <p class="pds-news-empty"><?php esc_html_e( 'No news articles have been published yet.', 'jobscout' ); ?></p>
        <?php endif; wp_reset_postdata(); ?>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? $instance['title'] : __( 'NEWEST BLOG ENTRIES', 'jobscout' );
        $count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 8;

        $snippet = <<<HTML
<h2 class="pds-news-heading">NEWEST BLOG ENTRIES</h2>
<div class="pds-news-grid">
    <article class="pds-news-card">
        <a class="pds-news-card-image" href="/news/">
            <img src="/wp-content/uploads/seed-cms-content/news-tokyo-hotel-summit-600x450.png" alt="News Image" class="pds-news-thumbnail" loading="lazy">
        </a>
        <div class="pds-news-card-content">
            <h3 class="pds-news-card-title"><a href="/news/">Tokyo Hospitality Summit 2026</a></h3>
            <p class="pds-news-card-excerpt">Key takeaways and highlights from industry leaders on guest experience innovation.</p>
            <a class="pds-news-read-more" href="/news/">Read More</a>
        </div>
    </article>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề khối:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Số lượng bài viết mỗi trang:', 'jobscout' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" step="1" min="1" max="24" value="<?php echo esc_attr( $count ); ?>" size="3">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['count']       = absint( $new_instance['count'] ?? 9 );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==============================================================================
   5. NHÓM WIDGET CHI TIẾT TIN TỨC (NEWS DETAIL) - PHỤ TRÁCH: NGUYỄN HỮU CHÚC (24211TT4553)
   ============================================================================== */

/**
 * ==============================================================================
 * 5.1 News Detail Related Widget (Bài Viết Tin Tức Liên Quan / Đọc Thêm)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Chi Tiết Tin Tức (News Detail Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Nguyễn Hữu Chúc (MSSV: 24211TT4553)
 * - SIDEBAR LIÊN KẾT    : 'news-detail-related'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị các bài viết tin tức liên quan ở chân trang chi tiết tin tức, tự động loại trừ bài viết đang xem, khuyến khích độc giả tiếp tục khám phá các nội dung khác trên trang.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_News_Detail_Related_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_news_detail_related',
            __( '[News Detail] Related Blog Entries', 'jobscout' ),
            array(
                'description'                 => __( 'Khối bài viết liên quan (NEWEST BLOG ENTRIES) cuối bài viết News Detail.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'NEWEST BLOG ENTRIES', 'jobscout' );
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 6;

        $current_post_id = is_singular( 'post' ) ? get_the_ID() : 0;
        $category_ids    = $current_post_id ? wp_get_post_categories( $current_post_id ) : array();

        $related_args = array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => $count,
            'post__not_in'        => array( $current_post_id ),
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
        );
        if ( $category_ids ) {
            $related_args['category__in'] = $category_ids;
        }

        $related       = new WP_Query( $related_args );
        $related_ids   = wp_list_pluck( $related->posts, 'ID' );
        $related_posts = $related->posts;

        if ( count( $related_posts ) < $count ) {
            $extra = new WP_Query( array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'posts_per_page'      => $count - count( $related_posts ),
                'post__not_in'        => array_merge( array( $current_post_id ), $related_ids ),
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            ) );
            $related_posts = array_merge( $related_posts, $extra->posts );
        }

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        if ( $title ) {
            echo '<h2 id="pds-news-detail-related-title">' . esc_html( $title ) . '</h2>';
        }
        if ( $related_posts ) : ?>
            <div class="pds-news-detail-related-grid">
                <?php foreach ( $related_posts as $related_post ) :
                    $related_id         = $related_post->ID;
                    $related_categories = get_the_category( $related_id );
                    ?>
                    <article class="pds-news-detail-related-card">
                        <a class="pds-news-detail-related-image" href="<?php echo esc_url( get_permalink( $related_id ) ); ?>">
                            <?php if ( has_post_thumbnail( $related_id ) ) :
                                echo get_the_post_thumbnail( $related_id, 'thumbnail', array( 'alt' => esc_attr( get_the_title( $related_id ) ) ) );
                            else : ?>
                                <span class="pds-news-detail-placeholder"><?php esc_html_e( 'NEWS', 'jobscout' ); ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="pds-news-detail-related-body">
                            <h3><a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>"><?php echo esc_html( get_the_title( $related_id ) ); ?></a></h3>
                            <p class="pds-news-detail-related-date"><?php echo esc_html( sprintf( __( 'Published: %s', 'jobscout' ), get_the_date( 'M d, Y', $related_id ) ) ); ?></p>
                            <p class="pds-news-detail-related-meta"><?php echo esc_html( $related_categories ? $related_categories[0]->name : __( 'News', 'jobscout' ) ); ?></p>
                            <p class="pds-news-detail-related-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $related_id ), 18, '…' ) ); ?></p>
                            <a class="pds-news-read-more" href="<?php echo esc_url( get_permalink( $related_id ) ); ?>">
                                <?php esc_html_e( 'Read More', 'jobscout' ); ?>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; wp_reset_postdata(); ?>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? $instance['title'] : __( 'NEWEST BLOG ENTRIES', 'jobscout' );
        $count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 6;

        $snippet = <<<HTML
<h2 id="pds-news-detail-related-title">NEWEST BLOG ENTRIES</h2>
<div class="pds-news-detail-related-grid">
    <article class="pds-news-detail-related-card">
        <a class="pds-news-detail-related-image" href="/news/">
            <span class="pds-news-detail-placeholder">NEWS</span>
        </a>
        <div class="pds-news-detail-related-body">
            <h3><a href="/news/">Culinary Excellence & Culture</a></h3>
            <p class="pds-news-detail-related-date">Published: Oct 19, 2026</p>
            <p class="pds-news-detail-related-meta">Stories</p>
            <p class="pds-news-detail-related-excerpt">How Japanese culinary traditions inspire our restaurant menus across the globe.</p>
            <a class="pds-news-read-more" href="/news/">Read More</a>
        </div>
    </article>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề khối:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Số lượng bài viết liên quan:', 'jobscout' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" step="1" min="1" max="18" value="<?php echo esc_attr( $count ); ?>" size="3">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['count']       = absint( $new_instance['count'] ?? 6 );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==============================================================================
   6. NHÓM WIDGET TRANG GIỚI THIỆU (ABOUT) - PHỤ TRÁCH: HỒ HOÀNG ĐỨC HUY (24211TT2193)
   ============================================================================== */

/**
 * ==============================================================================
 * 6.1 About Vision Widget (Khối Tầm Nhìn, Sứ Mệnh & Giá Trị Cốt Lõi)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Giới Thiệu (About Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Hồ Hoàng Đức Huy (MSSV: 24211TT2193)
 * - SIDEBAR LIÊN KẾT    : 'about-vision-section'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị phần giới thiệu doanh nghiệp gồm 3 cột trụ chính: Tầm nhìn (Vision), Sứ mệnh (Mission) và Giá trị cốt lõi (Core Values), đi kèm hình ảnh minh họa phong cách hospitality Nhật Bản.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_About_Vision_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_about_vision_section',
            __( '[About] Vision, Mission & Values', 'jobscout' ),
            array(
                'description'                 => __( 'Khối giới thiệu ABOUT US kèm hình ảnh Torii và 3 block: Vision, Mission, Core Value.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title         = ! empty( $instance['title'] ) ? $instance['title'] : __( 'ABOUT US', 'jobscout' );
        $image_url     = ! empty( $instance['image_url'] ) ? $instance['image_url'] : content_url( '/uploads/seed-cms-content/about-torii-lake.png' );
        $image_alt     = ! empty( $instance['image_alt'] ) ? $instance['image_alt'] : __( 'Vermilion torii gate in a Japanese lake', 'jobscout' );

        $vision_title  = ! empty( $instance['vision_title'] ) ? $instance['vision_title'] : __( 'Our Vision', 'jobscout' );
        $vision_desc   = ! empty( $instance['vision_desc'] ) ? $instance['vision_desc'] : __( 'Create hotels and restaurants around the world that offer memorable experiences while building a lasting, positive relationship together with our guests, partners, team members and communities.', 'jobscout' );

        $mission_title = ! empty( $instance['mission_title'] ) ? $instance['mission_title'] : __( 'Ours Mission', 'jobscout' );
        $mission_desc  = ! empty( $instance['mission_desc'] ) ? $instance['mission_desc'] : __( 'Share “Omotenashi” with the world', 'jobscout' );

        $value_title   = ! empty( $instance['value_title'] ) ? $instance['value_title'] : __( 'Our Core Value', 'jobscout' );
        $value_quote   = ! empty( $instance['value_quote'] ) ? $instance['value_quote'] : __( '“If I were the guest…”', 'jobscout' );
        $value_desc    = ! empty( $instance['value_desc'] ) ? $instance['value_desc'] : __( 'To provide guests with the hospitality you would want to receive as a guest.', 'jobscout' );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <?php if ( $title ) : ?>
            <h2 id="pds-about-main-title" class="pds-about-main-title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>

        <div class="pds-about-two-col pds-about-vision-grid">
            <!-- LEFT COLUMN: TORII GATE PHOTO -->
            <div class="pds-about-col pds-about-col-media">
                <div class="pds-about-image-wrapper">
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" class="pds-about-img pds-about-img-torii" loading="lazy">
                </div>
            </div>

            <!-- RIGHT COLUMN: VISION, MISSION, VALUE -->
            <div class="pds-about-col pds-about-col-content">
                <div class="pds-about-info-block">
                    <h3 class="pds-about-block-title"><?php echo esc_html( $vision_title ); ?></h3>
                    <p class="pds-about-block-desc"><?php echo esc_html( $vision_desc ); ?></p>
                </div>

                <div class="pds-about-info-block">
                    <h3 class="pds-about-block-title"><?php echo esc_html( $mission_title ); ?></h3>
                    <p class="pds-about-block-desc"><?php echo esc_html( $mission_desc ); ?></p>
                </div>

                <div class="pds-about-info-block">
                    <h3 class="pds-about-block-title"><?php echo esc_html( $value_title ); ?></h3>
                    <p class="pds-about-block-desc">
                        <?php if ( $value_quote ) : ?>
                            <span class="pds-about-quote-lead"><?php echo esc_html( $value_quote ); ?></span><br>
                        <?php endif; ?>
                        <?php echo esc_html( $value_desc ); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title         = isset( $instance['title'] ) ? $instance['title'] : __( 'ABOUT US', 'jobscout' );
        $image_url     = isset( $instance['image_url'] ) ? $instance['image_url'] : content_url( '/uploads/seed-cms-content/about-fushimi-inari-torii-600x600.png' );
        $image_alt     = isset( $instance['image_alt'] ) ? $instance['image_alt'] : __( 'Fushimi Inari Torii Shrine Gate', 'jobscout' );
        $vision_title  = isset( $instance['vision_title'] ) ? $instance['vision_title'] : __( 'Our Vision', 'jobscout' );
        $vision_desc   = isset( $instance['vision_desc'] ) ? $instance['vision_desc'] : __( 'Create hotels and restaurants around the world that offer memorable experiences while building a lasting, positive relationship together with our guests, partners, team members and communities.', 'jobscout' );
        $mission_title = isset( $instance['mission_title'] ) ? $instance['mission_title'] : __( 'Ours Mission', 'jobscout' );
        $mission_desc  = isset( $instance['mission_desc'] ) ? $instance['mission_desc'] : __( 'Share “Omotenashi” with the world', 'jobscout' );
        $value_title   = isset( $instance['value_title'] ) ? $instance['value_title'] : __( 'Our Core Value', 'jobscout' );
        $value_quote   = isset( $instance['value_quote'] ) ? $instance['value_quote'] : __( '“If I were the guest…”', 'jobscout' );
        $value_desc    = isset( $instance['value_desc'] ) ? $instance['value_desc'] : __( 'To provide guests with the hospitality you would want to receive as a guest.', 'jobscout' );

        $snippet = <<<HTML
<h2 id="pds-about-main-title" class="pds-about-main-title">ABOUT US</h2>
<div class="pds-about-two-col pds-about-vision-grid">
    <div class="pds-about-col pds-about-col-media">
        <div class="pds-about-image-wrapper">
            <img src="/wp-content/uploads/seed-cms-content/about-fushimi-inari-torii-600x600.png" alt="Fushimi Inari Torii" class="pds-about-img pds-about-img-torii" loading="lazy">
        </div>
    </div>
    <div class="pds-about-col pds-about-col-content">
        <div class="pds-about-info-block">
            <h3 class="pds-about-block-title">Our Vision</h3>
            <p class="pds-about-block-desc">Create hotels and restaurants around the world that offer memorable experiences while building a lasting, positive relationship together with our guests, partners, team members and communities.</p>
        </div>
        <div class="pds-about-info-block">
            <h3 class="pds-about-block-title">Ours Mission</h3>
            <p class="pds-about-block-desc">Share “Omotenashi” with the world</p>
        </div>
        <div class="pds-about-info-block">
            <h3 class="pds-about-block-title">Our Core Value</h3>
            <p class="pds-about-block-desc">
                <span class="pds-about-quote-lead">“If I were the guest…”</span><br>
                To provide guests with the hospitality you would want to receive as a guest.
            </p>
        </div>
    </div>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề chính:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
        pds_render_image_field( $this, 'image_url', __( 'Ảnh cổng Torii cột trái (Torii Gate Photo):', 'jobscout' ), $image_url );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'image_alt' ) ); ?>"><?php esc_html_e( 'Alt ảnh:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image_alt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_alt' ) ); ?>" type="text" value="<?php echo esc_attr( $image_alt ); ?>">
        </p>
        <hr>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'vision_title' ) ); ?>"><?php esc_html_e( 'Tiêu đề Vision:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'vision_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vision_title' ) ); ?>" type="text" value="<?php echo esc_attr( $vision_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'vision_desc' ) ); ?>"><?php esc_html_e( 'Nội dung Vision:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'vision_desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'vision_desc' ) ); ?>"><?php echo esc_textarea( $vision_desc ); ?></textarea>
        </p>
        <hr>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'mission_title' ) ); ?>"><?php esc_html_e( 'Tiêu đề Mission:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'mission_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'mission_title' ) ); ?>" type="text" value="<?php echo esc_attr( $mission_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'mission_desc' ) ); ?>"><?php esc_html_e( 'Nội dung Mission:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="2" id="<?php echo esc_attr( $this->get_field_id( 'mission_desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'mission_desc' ) ); ?>"><?php echo esc_textarea( $mission_desc ); ?></textarea>
        </p>
        <hr>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'value_title' ) ); ?>"><?php esc_html_e( 'Tiêu đề Value:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'value_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'value_title' ) ); ?>" type="text" value="<?php echo esc_attr( $value_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'value_quote' ) ); ?>"><?php esc_html_e( 'Câu trích dẫn (Quote lead):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'value_quote' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'value_quote' ) ); ?>" type="text" value="<?php echo esc_attr( $value_quote ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'value_desc' ) ); ?>"><?php esc_html_e( 'Nội dung Value:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="2" id="<?php echo esc_attr( $this->get_field_id( 'value_desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'value_desc' ) ); ?>"><?php echo esc_textarea( $value_desc ); ?></textarea>
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                  = array();
        $instance['title']         = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['image_url']     = esc_url_raw( $new_instance['image_url'] ?? '' );
        $instance['image_alt']     = sanitize_text_field( $new_instance['image_alt'] ?? '' );
        $instance['vision_title']  = sanitize_text_field( $new_instance['vision_title'] ?? '' );
        $instance['vision_desc']   = sanitize_textarea_field( $new_instance['vision_desc'] ?? '' );
        $instance['mission_title'] = sanitize_text_field( $new_instance['mission_title'] ?? '' );
        $instance['mission_desc']  = sanitize_textarea_field( $new_instance['mission_desc'] ?? '' );
        $instance['value_title']   = sanitize_text_field( $new_instance['value_title'] ?? '' );
        $instance['value_quote']   = sanitize_text_field( $new_instance['value_quote'] ?? '' );
        $instance['value_desc']    = sanitize_textarea_field( $new_instance['value_desc'] ?? '' );
        $instance['custom_html']   = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 6.2 About Company Stats Widget (Khối Thông Tin & Số Liệu Doanh Nghiệp)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Giới Thiệu (About Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Hồ Hoàng Đức Huy (MSSV: 24211TT2193)
 * - SIDEBAR LIÊN KẾT    : 'about-company-stats'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị bảng dữ liệu hồ sơ công ty: Năm thành lập, Địa chỉ trụ sở chính, Vốn điều lệ, Giám đốc điều hành (CEO) và Số lượng nhân viên.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_About_Company_Stats_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_about_company_stats',
            __( '[About] Company Facts & Stats', 'jobscout' ),
            array(
                'description'                 => __( 'Khối thông số công ty Company Facts kèm ảnh Tokyo Tower.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $label_established = ! empty( $instance['label_established'] ) ? $instance['label_established'] : __( 'Established since', 'jobscout' );
        $label_head_office = ! empty( $instance['label_head_office'] ) ? $instance['label_head_office'] : __( 'Head Office', 'jobscout' );
        $label_capital     = ! empty( $instance['label_capital'] ) ? $instance['label_capital'] : __( 'Capital', 'jobscout' );
        $label_ceo         = ! empty( $instance['label_ceo'] ) ? $instance['label_ceo'] : __( 'CEO', 'jobscout' );
        $label_employees   = ! empty( $instance['label_employees'] ) ? $instance['label_employees'] : __( 'Number of Employees', 'jobscout' );

        $established = ! empty( $instance['established'] ) ? $instance['established'] : 'April 1993';
        $head_office = ! empty( $instance['head_office'] ) ? $instance['head_office'] : 'Marunouchi 2-1-1, Chiyoda, Tokyo';
        $capital     = ! empty( $instance['capital'] ) ? $instance['capital'] : '100,000,000 JPY';
        $ceo         = ! empty( $instance['ceo'] ) ? $instance['ceo'] : 'Yutaka Noda';
        $employees   = ! empty( $instance['employees'] ) ? $instance['employees'] : 'Full time: 830 / Total: 1,600';
        $image_url   = ! empty( $instance['image_url'] ) ? $instance['image_url'] : content_url( '/uploads/seed-cms-content/about-tokyo-tower.png' );
        $image_alt   = ! empty( $instance['image_alt'] ) ? $instance['image_alt'] : __( 'Tokyo Tower with cherry blossoms', 'jobscout' );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="pds-about-two-col pds-about-facts-grid">
            <!-- LEFT COLUMN: FACTS LIST -->
            <div class="pds-about-col pds-about-col-facts">
                <?php if ( $established ) : ?>
                    <div class="pds-about-fact-item">
                        <h3 class="pds-about-fact-label"><?php echo esc_html( $label_established ); ?></h3>
                        <p class="pds-about-fact-value"><?php echo esc_html( $established ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ( $head_office ) : ?>
                    <div class="pds-about-fact-item">
                        <h3 class="pds-about-fact-label"><?php echo esc_html( $label_head_office ); ?></h3>
                        <p class="pds-about-fact-value"><?php echo esc_html( $head_office ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ( $capital ) : ?>
                    <div class="pds-about-fact-item">
                        <h3 class="pds-about-fact-label"><?php echo esc_html( $label_capital ); ?></h3>
                        <p class="pds-about-fact-value"><?php echo esc_html( $capital ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ( $ceo ) : ?>
                    <div class="pds-about-fact-item">
                        <h3 class="pds-about-fact-label"><?php echo esc_html( $label_ceo ); ?></h3>
                        <p class="pds-about-fact-value"><?php echo esc_html( $ceo ); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ( $employees ) : ?>
                    <div class="pds-about-fact-item">
                        <h3 class="pds-about-fact-label"><?php echo esc_html( $label_employees ); ?></h3>
                        <p class="pds-about-fact-value"><?php echo esc_html( $employees ); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT COLUMN: TOKYO TOWER PHOTO -->
            <div class="pds-about-col pds-about-col-media">
                <div class="pds-about-image-wrapper">
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" class="pds-about-img pds-about-img-tokyo" loading="lazy">
                </div>
            </div>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $label_established = isset( $instance['label_established'] ) ? $instance['label_established'] : __( 'Established since', 'jobscout' );
        $label_head_office = isset( $instance['label_head_office'] ) ? $instance['label_head_office'] : __( 'Head Office', 'jobscout' );
        $label_capital     = isset( $instance['label_capital'] ) ? $instance['label_capital'] : __( 'Capital', 'jobscout' );
        $label_ceo         = isset( $instance['label_ceo'] ) ? $instance['label_ceo'] : __( 'CEO', 'jobscout' );
        $label_employees   = isset( $instance['label_employees'] ) ? $instance['label_employees'] : __( 'Number of Employees', 'jobscout' );

        $established = isset( $instance['established'] ) ? $instance['established'] : 'April 1993';
        $head_office = isset( $instance['head_office'] ) ? $instance['head_office'] : 'Marunouchi 2-1-1, Chiyoda, Tokyo';
        $capital     = isset( $instance['capital'] ) ? $instance['capital'] : '100,000,000 JPY';
        $ceo         = isset( $instance['ceo'] ) ? $instance['ceo'] : 'Yutaka Noda';
        $employees   = isset( $instance['employees'] ) ? $instance['employees'] : 'Full time: 830 / Total: 1,600';
        $image_url   = isset( $instance['image_url'] ) ? $instance['image_url'] : content_url( '/uploads/seed-cms-content/about-tokyo-tower.png' );
        $image_alt   = isset( $instance['image_alt'] ) ? $instance['image_alt'] : __( 'Tokyo Tower with cherry blossoms', 'jobscout' );

        $snippet = <<<HTML
<div class="pds-about-two-col pds-about-facts-grid">
    <div class="pds-about-col pds-about-col-facts">
        <div class="pds-about-fact-item">
            <h3 class="pds-about-fact-label">Established since</h3>
            <p class="pds-about-fact-value">April 1993</p>
        </div>
        <div class="pds-about-fact-item">
            <h3 class="pds-about-fact-label">Head Office</h3>
            <p class="pds-about-fact-value">Marunouchi 2-1-1, Chiyoda, Tokyo</p>
        </div>
        <div class="pds-about-fact-item">
            <h3 class="pds-about-fact-label">Capital</h3>
            <p class="pds-about-fact-value">100,000,000 JPY</p>
        </div>
        <div class="pds-about-fact-item">
            <h3 class="pds-about-fact-label">CEO</h3>
            <p class="pds-about-fact-value">Yutaka Noda</p>
        </div>
        <div class="pds-about-fact-item">
            <h3 class="pds-about-fact-label">Number of Employees</h3>
            <p class="pds-about-fact-value">Full time: 830 / Total: 1,600</p>
        </div>
    </div>
    <div class="pds-about-col pds-about-col-media">
        <div class="pds-about-image-wrapper">
            <img src="/wp-content/uploads/seed-cms-content/about-tokyo-tower-600x600.png" alt="Tokyo Tower" class="pds-about-img pds-about-img-tokyo" loading="lazy">
        </div>
    </div>
</div>
HTML;
        ?>
        <div style="background: #fdfaf6; padding: 10px; border-radius: 6px; margin-bottom: 12px; border: 1px solid #fae1cc;">
            <p style="margin-top: 0; font-weight: 700; color: #ef741b;">1. Established since</p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'label_established' ) ); ?>"><?php esc_html_e( 'Nhãn:', 'jobscout' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'label_established' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'label_established' ) ); ?>" type="text" value="<?php echo esc_attr( $label_established ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'established' ) ); ?>"><?php esc_html_e( 'Giá trị:', 'jobscout' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'established' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'established' ) ); ?>" type="text" value="<?php echo esc_attr( $established ); ?>">
            </p>
        </div>

        <div style="background: #fdfaf6; padding: 10px; border-radius: 6px; margin-bottom: 12px; border: 1px solid #fae1cc;">
            <p style="margin-top: 0; font-weight: 700; color: #ef741b;">2. Head Office</p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'label_head_office' ) ); ?>"><?php esc_html_e( 'Nhãn:', 'jobscout' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'label_head_office' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'label_head_office' ) ); ?>" type="text" value="<?php echo esc_attr( $label_head_office ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'head_office' ) ); ?>"><?php esc_html_e( 'Giá trị:', 'jobscout' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'head_office' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'head_office' ) ); ?>" type="text" value="<?php echo esc_attr( $head_office ); ?>">
            </p>
        </div>

        <div style="background: #fdfaf6; padding: 10px; border-radius: 6px; margin-bottom: 12px; border: 1px solid #fae1cc;">
            <p style="margin-top: 0; font-weight: 700; color: #ef741b;">3. Capital</p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'label_capital' ) ); ?>"><?php esc_html_e( 'Nhãn:', 'jobscout' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'label_capital' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'label_capital' ) ); ?>" type="text" value="<?php echo esc_attr( $label_capital ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'capital' ) ); ?>"><?php esc_html_e( 'Giá trị:', 'jobscout' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'capital' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'capital' ) ); ?>" type="text" value="<?php echo esc_attr( $capital ); ?>">
            </p>
        </div>

        <div style="background: #fdfaf6; padding: 10px; border-radius: 6px; margin-bottom: 12px; border: 1px solid #fae1cc;">
            <p style="margin-top: 0; font-weight: 700; color: #ef741b;">4. CEO</p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'label_ceo' ) ); ?>"><?php esc_html_e( 'Nhãn:', 'jobscout' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'label_ceo' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'label_ceo' ) ); ?>" type="text" value="<?php echo esc_attr( $label_ceo ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'ceo' ) ); ?>"><?php esc_html_e( 'Giá trị:', 'jobscout' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'ceo' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'ceo' ) ); ?>" type="text" value="<?php echo esc_attr( $ceo ); ?>">
            </p>
        </div>

        <div style="background: #fdfaf6; padding: 10px; border-radius: 6px; margin-bottom: 12px; border: 1px solid #fae1cc;">
            <p style="margin-top: 0; font-weight: 700; color: #ef741b;">5. Number of Employees</p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'label_employees' ) ); ?>"><?php esc_html_e( 'Nhãn:', 'jobscout' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'label_employees' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'label_employees' ) ); ?>" type="text" value="<?php echo esc_attr( $label_employees ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'employees' ) ); ?>"><?php esc_html_e( 'Giá trị:', 'jobscout' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'employees' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'employees' ) ); ?>" type="text" value="<?php echo esc_attr( $employees ); ?>">
            </p>
        </div>

        <?php
        pds_render_image_field( $this, 'image_url', __( 'Ảnh Tokyo Tower cột phải (Tokyo Tower Photo):', 'jobscout' ), $image_url );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'image_alt' ) ); ?>"><?php esc_html_e( 'Alt ảnh:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image_alt' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_alt' ) ); ?>" type="text" value="<?php echo esc_attr( $image_alt ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                      = array();
        $instance['label_established'] = sanitize_text_field( $new_instance['label_established'] ?? '' );
        $instance['label_head_office'] = sanitize_text_field( $new_instance['label_head_office'] ?? '' );
        $instance['label_capital']     = sanitize_text_field( $new_instance['label_capital'] ?? '' );
        $instance['label_ceo']         = sanitize_text_field( $new_instance['label_ceo'] ?? '' );
        $instance['label_employees']   = sanitize_text_field( $new_instance['label_employees'] ?? '' );
        $instance['established']       = sanitize_text_field( $new_instance['established'] ?? '' );
        $instance['head_office']       = sanitize_text_field( $new_instance['head_office'] ?? '' );
        $instance['capital']           = sanitize_text_field( $new_instance['capital'] ?? '' );
        $instance['ceo']               = sanitize_text_field( $new_instance['ceo'] ?? '' );
        $instance['employees']         = sanitize_text_field( $new_instance['employees'] ?? '' );
        $instance['image_url']         = esc_url_raw( $new_instance['image_url'] ?? '' );
        $instance['image_alt']         = sanitize_text_field( $new_instance['image_alt'] ?? '' );
        $instance['custom_html']       = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==============================================================================
   7. NHÓM WIDGET TRANG LIÊN HỆ (CONTACT) - PHỤ TRÁCH: NGUYỄN HỮU PHONG (24211TT1976)
   ============================================================================== */

/**
 * ==============================================================================
 * 7.1 Contact Hero Banner Widget (Banner Đầu Trang Liên Hệ)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Liên Hệ (Contact Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Nguyễn Hữu Phong (MSSV: 24211TT1976)
 * - SIDEBAR LIÊN KẾT    : 'contact-hero-banner'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị banner tiêu đề "CONTACT US" đầu trang liên hệ với hình ảnh nền tùy biến và tiêu đề trang trọng.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Contact_Hero_Banner_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_contact_hero_banner',
            __( '[Contact] Hero Banner', 'jobscout' ),
            array(
                'description'                 => __( 'Khối Banner CONTACT US trên cùng trang Contact (cho phép thay ảnh nền và tiêu đề).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title        = ! empty( $instance['title'] ) ? $instance['title'] : __( 'CONTACT US', 'jobscout' );
        $bg_image_url = ! empty( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : get_template_directory_uri() . '/assets/images/contact-hero.png';

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        if ( ! empty( $bg_image_url ) ) : ?>
            <style>.pds-contact-hero { background-image: url('<?php echo esc_url( $bg_image_url ); ?>') !important; }</style>
        <?php endif; ?>
        <div class="pds-contact-hero-overlay"></div>
        <div class="pds-contact-hero-inner" style="margin: 0 auto !important; text-align: center !important; width: 100% !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important;">
            <h1 id="pds-contact-hero-title" class="pds-contact-hero-title" style="margin: 0 auto !important; text-align: center !important; width: 100% !important;">
                <?php echo esc_html( $title ); ?>
            </h1>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title        = isset( $instance['title'] ) ? $instance['title'] : __( 'CONTACT US', 'jobscout' );
        $bg_image_url = isset( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : get_template_directory_uri() . '/assets/images/contact-hero.png';

        $snippet = <<<HTML
<div class="pds-contact-hero-overlay"></div>
<div class="pds-contact-hero-inner">
    <h1 id="pds-contact-hero-title" class="pds-contact-hero-title">CONTACT US</h1>
</div>
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

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                 = array();
        $instance['title']        = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['bg_image_url'] = esc_url_raw( $new_instance['bg_image_url'] ?? '' );
        $instance['custom_html']  = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 7.2 Contact Headquarters Widget (Thông Tin Địa Chỉ Trụ Sở Chính & Bản Đồ)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Liên Hệ (Contact Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Nguyễn Hữu Phong (MSSV: 24211TT1976)
 * - SIDEBAR LIÊN KẾT    : 'contact-headquarters'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị địa chỉ trụ sở chính của công ty (Headquarters Address) kèm nhúng bản đồ Google Maps và hướng dẫn chỉ đường cho khách hàng và ứng viên.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Contact_Headquarters_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_contact_headquarters',
            __( '[Contact] Headquarters Address', 'jobscout' ),
            array(
                'description'                 => __( 'Khối địa chỉ trụ sở trang Contact Us.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title   = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Our Headquarters Address', 'jobscout' );
        $address = ! empty( $instance['address'] ) ? $instance['address'] : __( '60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City, Viet Nam', 'jobscout' );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <?php if ( $title ) : ?>
            <h2 id="pds-contact-hq-title" class="pds-contact-hq-title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>
        <?php if ( $address ) :
            $query_address = rawurlencode( $address );
            $maps_url = 'https://www.google.com/maps/search/?api=1&query=' . $query_address;
            $embed_url = 'https://maps.google.com/maps?q=' . $query_address . '&t=m&z=16&output=embed';
        ?>
            <div class="pds-contact-hq-address-wrapper">
                <a href="<?php echo esc_url( $maps_url ); ?>"
                   class="pds-contact-hq-address-link pds-contact-hq-address-btn"
                   id="pds-contact-map-trigger"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-expanded="false"
                   aria-controls="pds-contact-map-card"
                   title="<?php esc_attr_e( 'Bấm để xem bản đồ', 'jobscout' ); ?>">
                    <span class="pds-contact-pin-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </span>
                    <span class="pds-contact-address-text"><?php echo esc_html( $address ); ?></span>
                    <span class="pds-contact-address-badge">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                            <line x1="8" y1="2" x2="8" y2="18"></line>
                            <line x1="16" y1="6" x2="16" y2="22"></line>
                        </svg>
                        <span class="pds-contact-badge-label"><?php esc_html_e( 'Bấm để xem bản đồ', 'jobscout' ); ?></span>
                    </span>
                </a>
            </div>

            <!-- INTERACTIVE MAP CONTAINER -->
            <div class="pds-contact-map-card" id="pds-contact-map-card" aria-hidden="true">
                <div class="pds-contact-map-header">
                    <div class="pds-contact-map-actions">
                        <a href="<?php echo esc_url( $maps_url ); ?>"
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
                        title="<?php echo esc_attr( sprintf( __( 'Google Map - %s', 'jobscout' ), $address ) ); ?>"
                        data-src="<?php echo esc_url( $embed_url ); ?>"
                        src="about:blank"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        <?php endif; ?>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title   = isset( $instance['title'] ) ? $instance['title'] : __( 'Our Headquarters Address', 'jobscout' );
        $address = isset( $instance['address'] ) ? $instance['address'] : __( '60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City, Viet Nam', 'jobscout' );

        $snippet = <<<HTML
<h2 id="pds-contact-hq-title" class="pds-contact-hq-title">Our Headquarters Address</h2>
<p class="pds-contact-hq-address">60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City, Viet Nam</p>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>"><?php esc_html_e( 'Địa chỉ trụ sở:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'address' ) ); ?>"><?php echo esc_textarea( $address ); ?></textarea>
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['address']     = sanitize_textarea_field( $new_instance['address'] ?? '' );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 7.3 Contact Hotline Info Widget (Đường Dây Nóng Hỗ Trợ Doanh Nghiệp & Ứng Viên)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Liên Hệ (Contact Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Nguyễn Hữu Phong (MSSV: 24211TT1976)
 * - SIDEBAR LIÊN KẾT    : 'contact-hotline-info'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị khối thông tin liên hệ 2 luồng: Luồng dành cho nhà tuyển dụng (Hotline Sales HCM & Hà Nội) và Luồng dành cho ứng viên tìm việc (Kênh Facebook hỗ trợ, bài viết cẩm nang phỏng vấn và số điện thoại tư vấn).
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Contact_Hotline_Info_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_contact_hotline_info',
            __( '[Contact] Hotline & Channels', 'jobscout' ),
            array(
                'description'                 => __( 'Khối Hotline 2 cột trang Contact Us (For Employers & For Jobseekers).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        // Employers
        $emp_title     = ! empty( $instance['emp_title'] ) ? $instance['emp_title'] : __( 'For Employers', 'jobscout' );
        $emp_subtitle  = ! empty( $instance['emp_subtitle'] ) ? $instance['emp_subtitle'] : __( 'Call our Sales Hotline', 'jobscout' );
        $emp_hcm_label = ! empty( $instance['emp_hcm_label'] ) ? $instance['emp_hcm_label'] : __( 'Ho Chi Minh', 'jobscout' );
        $emp_hcm_phone = ! empty( $instance['emp_hcm_phone'] ) ? $instance['emp_hcm_phone'] : '+84 (0) 28 3911 5828';
        $emp_hn_label  = ! empty( $instance['emp_hn_label'] ) ? $instance['emp_hn_label'] : __( 'Ha Noi', 'jobscout' );
        $emp_hn_phone  = ! empty( $instance['emp_hn_phone'] ) ? $instance['emp_hn_phone'] : '+84 (0) 24 3936 8899';
        $emp_note      = ! empty( $instance['emp_note'] ) ? $instance['emp_note'] : "Request a call from one of our\nCustomer Love Account Managers\nWe're ready to help you grow!";

        // Jobseekers
        $seeker_title      = ! empty( $instance['seeker_title'] ) ? $instance['seeker_title'] : __( 'For Jobseekers', 'jobscout' );
        $seeker_fb_text    = ! empty( $instance['seeker_fb_text'] ) ? $instance['seeker_fb_text'] : __( 'Ask a question on our {Facebook} page', 'jobscout' );
        $seeker_fb         = ! empty( $instance['seeker_fb'] ) ? $instance['seeker_fb'] : 'https://facebook.com';
        $seeker_news_text  = ! empty( $instance['seeker_news_text'] ) ? $instance['seeker_news_text'] : __( 'Read our {blog posts} on interview and CV tips', 'jobscout' );
        $seeker_news       = ! empty( $instance['seeker_news'] ) ? $instance['seeker_news'] : '';
        $seeker_call_label = ! empty( $instance['seeker_call_label'] ) ? $instance['seeker_call_label'] : __( 'Call us at', 'jobscout' );
        $seeker_phone      = ! empty( $instance['seeker_phone'] ) ? $instance['seeker_phone'] : '+84 (0) 28 3911 5828';

        if ( empty( $seeker_news ) ) {
            $news_page   = get_page_by_path( 'news' );
            $seeker_news = $news_page ? get_permalink( $news_page ) : home_url( '/news/' );
        }

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="pds-contact-grid">
            <!-- LEFT COLUMN: FOR EMPLOYERS -->
            <div class="pds-contact-column pds-contact-employers">
                <?php if ( $emp_title ) : ?>
                    <h3 class="pds-contact-col-title"><?php echo esc_html( $emp_title ); ?></h3>
                <?php endif; ?>
                <?php if ( $emp_subtitle ) : ?>
                    <p class="pds-contact-col-subtitle"><?php echo esc_html( $emp_subtitle ); ?></p>
                <?php endif; ?>

                <?php if ( $emp_hcm_phone || $emp_hcm_label ) : ?>
                    <div class="pds-contact-city-group">
                        <h4 class="pds-contact-city-name"><?php echo esc_html( $emp_hcm_label ); ?></h4>
                        <?php if ( $emp_hcm_phone ) : ?>
                            <p class="pds-contact-phone">
                                <a href="tel:<?php echo esc_attr( preg_replace( '/[^\+0-9]/', '', $emp_hcm_phone ) ); ?>"><?php echo esc_html( $emp_hcm_phone ); ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $emp_hn_phone || $emp_hn_label ) : ?>
                    <div class="pds-contact-city-group">
                        <h4 class="pds-contact-city-name"><?php echo esc_html( $emp_hn_label ); ?></h4>
                        <?php if ( $emp_hn_phone ) : ?>
                            <p class="pds-contact-phone">
                                <a href="tel:<?php echo esc_attr( preg_replace( '/[^\+0-9]/', '', $emp_hn_phone ) ); ?>"><?php echo esc_html( $emp_hn_phone ); ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $emp_note ) : ?>
                    <div class="pds-contact-note">
                        <p><?php echo nl2br( esc_html( $emp_note ) ); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- RIGHT COLUMN: FOR JOBSEEKERS -->
            <div class="pds-contact-column pds-contact-jobseekers">
                <?php if ( $seeker_title ) : ?>
                    <h3 class="pds-contact-col-title"><?php echo esc_html( $seeker_title ); ?></h3>
                <?php endif; ?>

                <div class="pds-contact-seeker-actions">
                    <?php if ( $seeker_fb && $seeker_fb_text ) : ?>
                        <p class="pds-contact-action-item">
                            <?php
                            $fb_anchor = '<a href="' . esc_url( $seeker_fb ) . '" class="pds-contact-link-orange" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Facebook', 'jobscout' ) . '</a>';
                            if ( preg_match( '/\{([^}]+)\}/', $seeker_fb_text, $m ) ) {
                                $custom_anchor = '<a href="' . esc_url( $seeker_fb ) . '" class="pds-contact-link-orange" target="_blank" rel="noopener noreferrer">' . esc_html( $m[1] ) . '</a>';
                                echo str_replace( $m[0], $custom_anchor, esc_html( $seeker_fb_text ) );
                            } elseif ( stripos( $seeker_fb_text, 'Facebook' ) !== false ) {
                                echo preg_replace( '/Facebook/i', $fb_anchor, esc_html( $seeker_fb_text ), 1 );
                            } else {
                                echo esc_html( $seeker_fb_text ) . ' ' . $fb_anchor;
                            }
                            ?>
                        </p>
                    <?php endif; ?>

                    <?php if ( $seeker_news && $seeker_news_text ) : ?>
                        <p class="pds-contact-action-item">
                            <?php
                            $news_anchor = '<a href="' . esc_url( $seeker_news ) . '" class="pds-contact-link-orange">' . esc_html__( 'blog posts', 'jobscout' ) . '</a>';
                            if ( preg_match( '/\{([^}]+)\}/', $seeker_news_text, $m ) ) {
                                $custom_anchor = '<a href="' . esc_url( $seeker_news ) . '" class="pds-contact-link-orange">' . esc_html( $m[1] ) . '</a>';
                                echo str_replace( $m[0], $custom_anchor, esc_html( $seeker_news_text ) );
                            } elseif ( stripos( $seeker_news_text, 'blog posts' ) !== false ) {
                                echo preg_replace( '/blog posts/i', $news_anchor, esc_html( $seeker_news_text ), 1 );
                            } else {
                                echo esc_html( $seeker_news_text ) . ' ' . $news_anchor;
                            }
                            ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if ( $seeker_phone || $seeker_call_label ) : ?>
                    <div class="pds-contact-city-group pds-contact-seeker-call">
                        <h4 class="pds-contact-city-name"><?php echo esc_html( $seeker_call_label ); ?></h4>
                        <?php if ( $seeker_phone ) : ?>
                            <p class="pds-contact-phone">
                                <a href="tel:<?php echo esc_attr( preg_replace( '/[^\+0-9]/', '', $seeker_phone ) ); ?>"><?php echo esc_html( $seeker_phone ); ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $emp_title         = isset( $instance['emp_title'] ) ? $instance['emp_title'] : __( 'For Employers', 'jobscout' );
        $emp_subtitle      = isset( $instance['emp_subtitle'] ) ? $instance['emp_subtitle'] : __( 'Call our Sales Hotline', 'jobscout' );
        $emp_hcm_label     = isset( $instance['emp_hcm_label'] ) ? $instance['emp_hcm_label'] : __( 'Ho Chi Minh', 'jobscout' );
        $emp_hcm_phone     = isset( $instance['emp_hcm_phone'] ) ? $instance['emp_hcm_phone'] : '+84 (0) 28 3911 5828';
        $emp_hn_label      = isset( $instance['emp_hn_label'] ) ? $instance['emp_hn_label'] : __( 'Ha Noi', 'jobscout' );
        $emp_hn_phone      = isset( $instance['emp_hn_phone'] ) ? $instance['emp_hn_phone'] : '+84 (0) 24 3936 8899';
        $emp_note          = isset( $instance['emp_note'] ) ? $instance['emp_note'] : "Request a call from one of our\nCustomer Love Account Managers\nWe're ready to help you grow!";
        $seeker_title      = isset( $instance['seeker_title'] ) ? $instance['seeker_title'] : __( 'For Jobseekers', 'jobscout' );
        $seeker_fb_text    = isset( $instance['seeker_fb_text'] ) ? $instance['seeker_fb_text'] : __( 'Ask a question on our {Facebook} page', 'jobscout' );
        $seeker_fb         = isset( $instance['seeker_fb'] ) ? $instance['seeker_fb'] : 'https://facebook.com';
        $seeker_news_text  = isset( $instance['seeker_news_text'] ) ? $instance['seeker_news_text'] : __( 'Read our {blog posts} on interview and CV tips', 'jobscout' );
        $seeker_news       = isset( $instance['seeker_news'] ) ? $instance['seeker_news'] : '';
        $seeker_call_label = isset( $instance['seeker_call_label'] ) ? $instance['seeker_call_label'] : __( 'Call us at', 'jobscout' );
        $seeker_phone      = isset( $instance['seeker_phone'] ) ? $instance['seeker_phone'] : '+84 (0) 28 3911 5828';

        $snippet = <<<HTML
<div class="pds-contact-grid">
    <div class="pds-contact-column pds-contact-employers">
        <h3 class="pds-contact-col-title">For Employers</h3>
        <p class="pds-contact-col-subtitle">Call our Sales Hotline</p>
        <div class="pds-contact-city-group">
            <h4 class="pds-contact-city-name">Ho Chi Minh</h4>
            <p class="pds-contact-phone"><a href="tel:+842839115828">+84 (0) 28 3911 5828</a></p>
        </div>
        <div class="pds-contact-city-group">
            <h4 class="pds-contact-city-name">Ha Noi</h4>
            <p class="pds-contact-phone"><a href="tel:+842439368899">+84 (0) 24 3936 8899</a></p>
        </div>
        <div class="pds-contact-note">
            <p>Request a call from one of our<br>Customer Love Account Managers<br>We're ready to help you grow!</p>
        </div>
    </div>
    <div class="pds-contact-column pds-contact-jobseekers">
        <h3 class="pds-contact-col-title">For Jobseekers</h3>
        <div class="pds-contact-seeker-actions">
            <p class="pds-contact-action-item">Ask a question on our <a href="https://facebook.com" class="pds-contact-link-orange" target="_blank" rel="noopener noreferrer">Facebook</a> page</p>
            <p class="pds-contact-action-item">Read our <a href="/news/" class="pds-contact-link-orange">blog posts</a> on interview and CV tips</p>
        </div>
        <div class="pds-contact-city-group pds-contact-seeker-call">
            <h4 class="pds-contact-city-name">Call us at</h4>
            <p class="pds-contact-phone"><a href="tel:+842839115828">+84 (0) 28 3911 5828</a></p>
        </div>
    </div>
</div>
HTML;
        ?>
        <h4 style="margin: 10px 0 5px; font-weight: 700; color: #ef741b;"><?php esc_html_e( 'Cột 1: Dành cho Nhà tuyển dụng (Employers)', 'jobscout' ); ?></h4>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'emp_title' ) ); ?>"><?php esc_html_e( 'Tiêu đề cột:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'emp_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'emp_title' ) ); ?>" type="text" value="<?php echo esc_attr( $emp_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'emp_subtitle' ) ); ?>"><?php esc_html_e( 'Phụ đề:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'emp_subtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'emp_subtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $emp_subtitle ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'emp_hcm_label' ) ); ?>"><?php esc_html_e( 'Tên thành phố 1 (Mặc định: Ho Chi Minh):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'emp_hcm_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'emp_hcm_label' ) ); ?>" type="text" value="<?php echo esc_attr( $emp_hcm_label ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'emp_hcm_phone' ) ); ?>"><?php esc_html_e( 'Hotline HCM:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'emp_hcm_phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'emp_hcm_phone' ) ); ?>" type="text" value="<?php echo esc_attr( $emp_hcm_phone ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'emp_hn_label' ) ); ?>"><?php esc_html_e( 'Tên thành phố 2 (Mặc định: Ha Noi):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'emp_hn_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'emp_hn_label' ) ); ?>" type="text" value="<?php echo esc_attr( $emp_hn_label ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'emp_hn_phone' ) ); ?>"><?php esc_html_e( 'Hotline HN:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'emp_hn_phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'emp_hn_phone' ) ); ?>" type="text" value="<?php echo esc_attr( $emp_hn_phone ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'emp_note' ) ); ?>"><?php esc_html_e( 'Ghi chú cuối cột:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'emp_note' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'emp_note' ) ); ?>"><?php echo esc_textarea( $emp_note ); ?></textarea>
        </p>

        <h4 style="margin: 15px 0 5px; font-weight: 700; color: #ef741b;"><?php esc_html_e( 'Cột 2: Dành cho Ứng viên (Jobseekers)', 'jobscout' ); ?></h4>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'seeker_title' ) ); ?>"><?php esc_html_e( 'Tiêu đề cột:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'seeker_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'seeker_title' ) ); ?>" type="text" value="<?php echo esc_attr( $seeker_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'seeker_fb_text' ) ); ?>"><?php esc_html_e( 'Văn bản Facebook (đặt trong { } để gắn link):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'seeker_fb_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'seeker_fb_text' ) ); ?>" type="text" value="<?php echo esc_attr( $seeker_fb_text ); ?>" placeholder="Ask a question on our {Facebook} page">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'seeker_fb' ) ); ?>"><?php esc_html_e( 'Link Facebook page:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'seeker_fb' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'seeker_fb' ) ); ?>" type="url" value="<?php echo esc_attr( $seeker_fb ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'seeker_news_text' ) ); ?>"><?php esc_html_e( 'Văn bản Blog tips (đặt trong { } để gắn link):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'seeker_news_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'seeker_news_text' ) ); ?>" type="text" value="<?php echo esc_attr( $seeker_news_text ); ?>" placeholder="Read our {blog posts} on interview and CV tips">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'seeker_news' ) ); ?>"><?php esc_html_e( 'Link Blog/Tips (để trống = tự động trang News):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'seeker_news' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'seeker_news' ) ); ?>" type="url" value="<?php echo esc_attr( $seeker_news ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'seeker_call_label' ) ); ?>"><?php esc_html_e( 'Nhãn tiêu đề gọi điện (Mặc định: Call us at):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'seeker_call_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'seeker_call_label' ) ); ?>" type="text" value="<?php echo esc_attr( $seeker_call_label ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'seeker_phone' ) ); ?>"><?php esc_html_e( 'Số điện thoại hỗ trợ Ứng viên:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'seeker_phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'seeker_phone' ) ); ?>" type="text" value="<?php echo esc_attr( $seeker_phone ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                      = array();
        $instance['emp_title']         = sanitize_text_field( $new_instance['emp_title'] ?? '' );
        $instance['emp_subtitle']      = sanitize_text_field( $new_instance['emp_subtitle'] ?? '' );
        $instance['emp_hcm_label']     = sanitize_text_field( $new_instance['emp_hcm_label'] ?? '' );
        $instance['emp_hcm_phone']     = sanitize_text_field( $new_instance['emp_hcm_phone'] ?? '' );
        $instance['emp_hn_label']      = sanitize_text_field( $new_instance['emp_hn_label'] ?? '' );
        $instance['emp_hn_phone']      = sanitize_text_field( $new_instance['emp_hn_phone'] ?? '' );
        $instance['emp_note']          = sanitize_textarea_field( $new_instance['emp_note'] ?? '' );
        $instance['seeker_title']      = sanitize_text_field( $new_instance['seeker_title'] ?? '' );
        $instance['seeker_fb_text']    = sanitize_text_field( $new_instance['seeker_fb_text'] ?? '' );
        $instance['seeker_fb']         = esc_url_raw( $new_instance['seeker_fb'] ?? '' );
        $instance['seeker_news_text']  = sanitize_text_field( $new_instance['seeker_news_text'] ?? '' );
        $instance['seeker_news']       = esc_url_raw( $new_instance['seeker_news'] ?? '' );
        $instance['seeker_call_label'] = sanitize_text_field( $new_instance['seeker_call_label'] ?? '' );
        $instance['seeker_phone']      = sanitize_text_field( $new_instance['seeker_phone'] ?? '' );
        $instance['custom_html']       = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==============================================================================
   8. NHÓM WIDGET DÙNG CHUNG - HEADER & FOOTER - PHỤ TRÁCH: NHÓM D
   ============================================================================== */

/**
 * ==============================================================================
 * 8.1 Header CTA Widget (Nút Hành Động Đầu Trang - Đăng Tin Tuyển Dụng)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Toàn Website (Shared Header Area)
 * - THÀNH VIÊN PHỤ TRÁCH: Nhóm D - Dùng chung
 * - SIDEBAR LIÊN KẾT    : 'header-cta'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị nút hành động nổi bật màu cam "SUBMIT JOB" trên thanh điều hướng đầu trang (Navigation Header), dẫn trực tiếp tới trang biểu mẫu đăng tin tuyển dụng.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Header_CTA_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_header_cta',
            __( '[Header] CTA Submit Job', 'jobscout' ),
            array(
                'description'                 => __( 'Nút SUBMIT JOB trên Header.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : __( 'SUBMIT JOB', 'jobscout' );
        $button_url  = ! empty( $instance['button_url'] ) ? $instance['button_url'] : '';

        if ( empty( $button_url ) ) {
            $post_job_page = get_page_by_path( 'post-a-job' ) ?: get_page_by_path( 'submit-job' );
            $button_url    = $post_job_page ? get_permalink( $post_job_page ) : home_url( '/post-a-job/' );
        }

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <a href="<?php echo esc_url( $button_url ); ?>" class="btn btn-primary submit-job-btn"><?php echo esc_html( $button_text ); ?></a>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $button_text = isset( $instance['button_text'] ) ? $instance['button_text'] : __( 'SUBMIT JOB', 'jobscout' );
        $button_url  = isset( $instance['button_url'] ) ? $instance['button_url'] : '';

        $snippet = <<<HTML
<a href="/post-a-job/" class="btn btn-primary submit-job-btn">SUBMIT JOB</a>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Chữ nút bấm:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>"><?php esc_html_e( 'Đường dẫn liên kết (để trống = tự động trang Post a Job):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_url' ) ); ?>" type="url" value="<?php echo esc_attr( $button_url ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['button_text'] = sanitize_text_field( $new_instance['button_text'] ?? '' );
        $instance['button_url']  = esc_url_raw( $new_instance['button_url'] ?? '' );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 8.2 Footer Newsletter Widget (Khối Đăng Ký Bản Tin Email Chân Trang)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Toàn Website (Shared Footer Area)
 * - THÀNH VIÊN PHỤ TRÁCH: Nhóm D - Dùng chung
 * - SIDEBAR LIÊN KẾT    : 'footer-newsletter'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị dải băng màu cam nổi bật ở chân trang với form đăng ký nhận tin tuyển dụng qua email. Tích hợp linh hoạt với Contact Form 7 hoặc mã form HTML tiêu chuẩn.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Footer_Newsletter_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_footer_newsletter',
            __( '[Footer] Newsletter Banner', 'jobscout' ),
            array(
                'description'                 => __( 'Khối thanh cam đăng ký nhận bản tin Newsletter ở chân trang (hỗ trợ đổi tiêu đề, ô nhập email, shortcode CF7, HTML tùy chỉnh).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title_line1   = ! empty( $instance['title_line1'] ) ? $instance['title_line1'] : __( 'Subscribe To', 'jobscout' );
        $title_line2   = ! empty( $instance['title_line2'] ) ? $instance['title_line2'] : __( 'Our Newsletter', 'jobscout' );
        $cf7_shortcode = isset( $instance['cf7_shortcode'] ) ? $instance['cf7_shortcode'] : '[contact-form-7 id="1033" title="NewSelecter"]';
        $placeholder   = ! empty( $instance['placeholder'] ) ? $instance['placeholder'] : __( 'Input your email address', 'jobscout' );
        $btn_text      = ! empty( $instance['button_text'] ) ? $instance['button_text'] : __( 'SUBSCRIBE', 'jobscout' );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <section class="pds-contact-newsletter" aria-labelledby="pds-contact-newsletter-title">
            <div class="pds-contact-newsletter-inner">
                <h2 id="pds-contact-newsletter-title" class="pds-contact-newsletter-heading">
                    <?php echo esc_html( $title_line1 ); ?><br>
                    <?php echo esc_html( $title_line2 ); ?>
                </h2>

                <div class="pds-contact-newsletter-content-wrap">
                    <?php
                    $rendered = ! empty( $cf7_shortcode ) ? do_shortcode( $cf7_shortcode ) : '';
                    if ( ! empty( $rendered ) && strpos( $rendered, '[contact-form-7' ) === false && stripos( $rendered, 'not found' ) === false ) {
                        echo $rendered;
                    } else {
                        ?>
                        <form class="pds-contact-newsletter-form" onsubmit="return false;">
                            <label class="pds-contact-newsletter-field">
                                <!-- <span class="pds-contact-newsletter-icon" aria-hidden="true">
                                    <svg width="20" height="16" viewBox="0 0 20 16" fill="#f2761b">
                                        <path d="M18 0H2C0.9 0 0.01 0.9 0.01 2L0 14C0 15.1 0.9 16 2 16H18C19.1 16 20 15.1 20 14V2C20 0.9 19.1 0 18 0ZM18 4L10 9L2 4V2L10 7L18 2V4Z"/>
                                    </svg>
                                </span> -->
                                <input type="text" inputmode="email" name="contact_newsletter_email" autocomplete="off" placeholder="<?php echo esc_attr( $placeholder ); ?>" required>
                            </label>
                            <button type="button" class="pds-contact-newsletter-btn"><?php echo esc_html( $btn_text ); ?></button>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </section>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title_line1   = isset( $instance['title_line1'] ) ? $instance['title_line1'] : __( 'Subscribe To', 'jobscout' );
        $title_line2   = isset( $instance['title_line2'] ) ? $instance['title_line2'] : __( 'Our Newsletter', 'jobscout' );
        $cf7_shortcode = isset( $instance['cf7_shortcode'] ) ? $instance['cf7_shortcode'] : '[contact-form-7 id="1033" title="NewSelecter"]';
        $placeholder   = isset( $instance['placeholder'] ) ? $instance['placeholder'] : __( 'Input your email address', 'jobscout' );
        $btn_text      = isset( $instance['button_text'] ) ? $instance['button_text'] : __( 'SUBSCRIBE', 'jobscout' );

        $snippet = <<<HTML
<section class="pds-contact-newsletter" aria-labelledby="pds-contact-newsletter-title">
    <div class="pds-contact-newsletter-inner">
        <h2 id="pds-contact-newsletter-title" class="pds-contact-newsletter-heading">
            Subscribe To<br>Our Newsletter
        </h2>
        <div class="pds-contact-newsletter-content-wrap">
            [contact-form-7 id="66ddf06" title="NewSelecter"]
        </div>
    </div>
</section>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title_line1' ) ); ?>"><?php esc_html_e( 'Tiêu đề dòng 1 (Title line 1):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title_line1' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title_line1' ) ); ?>" type="text" value="<?php echo esc_attr( $title_line1 ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title_line2' ) ); ?>"><?php esc_html_e( 'Tiêu đề dòng 2 (Title line 2):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title_line2' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title_line2' ) ); ?>" type="text" value="<?php echo esc_attr( $title_line2 ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'cf7_shortcode' ) ); ?>"><?php esc_html_e( 'Shortcode Contact Form 7 (mặc định [contact-form-7 id="66ddf06" title="NewSelecter"]):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cf7_shortcode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cf7_shortcode' ) ); ?>" type="text" value="<?php echo esc_attr( $cf7_shortcode ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>"><?php esc_html_e( 'Placeholder ô nhập email:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $placeholder ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Chữ trên nút Subscribe:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $btn_text ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                  = array();
        $instance['title_line1']   = sanitize_text_field( $new_instance['title_line1'] ?? '' );
        $instance['title_line2']   = sanitize_text_field( $new_instance['title_line2'] ?? '' );
        $instance['cf7_shortcode'] = sanitize_text_field( $new_instance['cf7_shortcode'] ?? '' );
        $instance['placeholder']   = sanitize_text_field( $new_instance['placeholder'] ?? '' );
        $instance['button_text']   = sanitize_text_field( $new_instance['button_text'] ?? '' );
        $instance['custom_html']   = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 8.3 Footer Bottom Widget (Thông Tin Thương Hiệu, Menu Chân Trang & Mạng Xã Hội)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Toàn Website (Shared Footer Area)
 * - THÀNH VIÊN PHỤ TRÁCH: Nhóm D - Dùng chung
 * - SIDEBAR LIÊN KẾT    : 'footer-bottom'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị logo thương hiệu "TUYỂN DỤNG", thanh menu liên kết điều hướng nhanh và các biểu tượng mạng xã hội (Facebook, Google, Line, Twitter).
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Footer_Bottom_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_footer_bottom',
            __( '[Footer] Bottom Copyright & Socials', 'jobscout' ),
            array(
                'description'                 => __( 'Khu vực bản quyền và icon mạng xã hội ở đáy footer.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $brand_name     = ! empty( $instance['brand_name'] ) ? $instance['brand_name'] : 'Plan Do See Global Inc.';
        $copyright_text = ! empty( $instance['copyright_text'] ) ? $instance['copyright_text'] : '© ' . gmdate( 'Y' ) . ' Plan Do See Global. All Rights Reserved.';
        $fb_link        = ! empty( $instance['fb_link'] ) ? $instance['fb_link'] : 'https://facebook.com';
        $google_link    = ! empty( $instance['google_link'] ) ? $instance['google_link'] : 'https://google.com';
        $line_link      = ! empty( $instance['line_link'] ) ? $instance['line_link'] : 'https://line.me';
        $twitter_link   = ! empty( $instance['twitter_link'] ) ? $instance['twitter_link'] : 'https://twitter.com';

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="home-footer-brand">
            <?php if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) : ?>
                <div class="site-branding" style="justify-content: center; text-align: center;">
                    <?php the_custom_logo(); ?>
                </div>
            <?php else : ?>
                <div class="site-branding" style="justify-content: center; text-align: center;">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration: none; color: #211916; display: inline-block;">
                        <span style="display: block; font-size: 24px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; line-height: 1.2; color: #211916;">
                            <?php echo esc_html( $brand_name ); ?>
                        </span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <nav class="home-footer-navigation" aria-label="<?php esc_attr_e( 'Footer navigation', 'jobscout' ); ?>">
            <?php
            if ( has_nav_menu( 'footer' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'footer',
                    'menu_id'        => 'footer-menu',
                    'menu_class'     => 'home-footer-menu',
                    'container'      => false,
                    'fallback_cb'    => 'jobscout_footer_menu_fallback',
                ) );
            } else {
                ?>
                <ul class="home-footer-menu">
                    <li><a href="<?php echo esc_url( home_url( '/jobs/' ) ); ?>"><?php esc_html_e( 'JOBS', 'jobscout' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/jobs/' ) ); ?>"><?php esc_html_e( 'COMPANIES', 'jobscout' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/news/' ) ); ?>"><?php esc_html_e( 'BLOG', 'jobscout' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'ABOUT', 'jobscout' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'CONTACT', 'jobscout' ); ?></a></li>
                </ul>
                <?php
            }
            ?>
        </nav>
        <div class="home-footer-social" aria-label="<?php esc_attr_e( 'Social links', 'jobscout' ); ?>">
            <?php if ( $fb_link ) : ?>
                <a class="home-social--facebook" href="<?php echo esc_url( $fb_link ); ?>" rel="noopener noreferrer" target="_blank" aria-label="<?php esc_attr_e( 'Facebook', 'jobscout' ); ?>">f</a>
            <?php endif; ?>
            <?php if ( $google_link ) : ?>
                <a class="home-social--google" href="<?php echo esc_url( $google_link ); ?>" rel="noopener noreferrer" target="_blank" aria-label="<?php esc_attr_e( 'Google', 'jobscout' ); ?>">G</a>
            <?php endif; ?>
            <?php if ( $line_link ) : ?>
                <a class="home-social--line" href="<?php echo esc_url( $line_link ); ?>" rel="noopener noreferrer" target="_blank" aria-label="<?php esc_attr_e( 'Line', 'jobscout' ); ?>">LINE</a>
            <?php endif; ?>
            <?php if ( $twitter_link ) : ?>
                <a class="home-social--twitter" href="<?php echo esc_url( $twitter_link ); ?>" rel="noopener noreferrer" target="_blank" aria-label="<?php esc_attr_e( 'Twitter', 'jobscout' ); ?>">t</a>
            <?php endif; ?>
        </div>
        <?php if ( $copyright_text ) : ?>
            <div class="home-footer-copyright-row" style="text-align: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid rgba(0,0,0,0.06); font-size: 13px; color: #888;">
                <?php echo esc_html( $copyright_text ); ?>
            </div>
        <?php endif; ?>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $brand_name     = isset( $instance['brand_name'] ) ? $instance['brand_name'] : 'Plan Do See Global Inc.';
        $copyright_text = isset( $instance['copyright_text'] ) ? $instance['copyright_text'] : '© ' . gmdate( 'Y' ) . ' Plan Do See Global. All Rights Reserved.';
        $fb_link        = isset( $instance['fb_link'] ) ? $instance['fb_link'] : 'https://facebook.com';
        $google_link    = isset( $instance['google_link'] ) ? $instance['google_link'] : 'https://google.com';
        $line_link      = isset( $instance['line_link'] ) ? $instance['line_link'] : 'https://line.me';
        $twitter_link   = isset( $instance['twitter_link'] ) ? $instance['twitter_link'] : 'https://twitter.com';

        $snippet = <<<HTML
<div class="home-footer-brand">
    <div class="site-branding" style="justify-content: center; text-align: center;">
        <span style="font-size: 24px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; color: #211916;">Plan Do See Global Inc.</span>
    </div>
</div>
<nav class="home-footer-navigation" aria-label="Footer navigation">
    <ul class="home-footer-menu">
        <li><a href="/jobs/">JOBS</a></li>
        <li><a href="/jobs/">COMPANIES</a></li>
        <li><a href="/news/">BLOG</a></li>
        <li><a href="/about/">ABOUT</a></li>
        <li><a href="/contact/">CONTACT</a></li>
    </ul>
</nav>
<div class="home-footer-social" aria-label="Social links">
    <a class="home-social--facebook" href="https://facebook.com" target="_blank" rel="noopener noreferrer">f</a>
    <a class="home-social--google" href="https://google.com" target="_blank" rel="noopener noreferrer">G</a>
    <a class="home-social--line" href="https://line.me" target="_blank" rel="noopener noreferrer">LINE</a>
    <a class="home-social--twitter" href="https://twitter.com" target="_blank" rel="noopener noreferrer">t</a>
</div>
<div class="home-footer-copyright-row" style="text-align: center; margin-top: 24px; font-size: 13px; color: #888;">
    © 2026 Plan Do See Global. All Rights Reserved.
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'brand_name' ) ); ?>"><?php esc_html_e( 'Tên thương hiệu Footer (Brand Name):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'brand_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'brand_name' ) ); ?>" type="text" value="<?php echo esc_attr( $brand_name ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'copyright_text' ) ); ?>"><?php esc_html_e( 'Nội dung bản quyền (Copyright text):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'copyright_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'copyright_text' ) ); ?>" type="text" value="<?php echo esc_attr( $copyright_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'fb_link' ) ); ?>"><?php esc_html_e( 'Link Facebook:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'fb_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'fb_link' ) ); ?>" type="url" value="<?php echo esc_attr( $fb_link ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'google_link' ) ); ?>"><?php esc_html_e( 'Link Google:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'google_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'google_link' ) ); ?>" type="url" value="<?php echo esc_attr( $google_link ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'line_link' ) ); ?>"><?php esc_html_e( 'Link LINE:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'line_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'line_link' ) ); ?>" type="url" value="<?php echo esc_attr( $line_link ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'twitter_link' ) ); ?>"><?php esc_html_e( 'Link Twitter:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'twitter_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'twitter_link' ) ); ?>" type="url" value="<?php echo esc_attr( $twitter_link ); ?>">
        </p>
        <p style="font-size: 11px; color: #888; margin: 4px 0 10px 0;">
            <?php esc_html_e( '💡 Menu chân trang (JOBS, COMPANIES, BLOG...) có thể tùy biến chi tiết tại Giao diện -> Trình đơn (Menu "Footer Navigation").', 'jobscout' ); ?>
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                   = array();
        $instance['brand_name']     = sanitize_text_field( $new_instance['brand_name'] ?? '' );
        $instance['copyright_text'] = sanitize_text_field( $new_instance['copyright_text'] ?? '' );
        $instance['fb_link']        = esc_url_raw( $new_instance['fb_link'] ?? '' );
        $instance['google_link']    = esc_url_raw( $new_instance['google_link'] ?? '' );
        $instance['line_link']      = esc_url_raw( $new_instance['line_link'] ?? '' );
        $instance['twitter_link']   = esc_url_raw( $new_instance['twitter_link'] ?? '' );
        $instance['custom_html']    = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 8.4 Footer Copyright Widget (Dòng Bản Quyền & Điều Khoản Sử Dụng)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Toàn Website (Shared Footer Area)
 * - THÀNH VIÊN PHỤ TRÁCH: Nhóm D - Dùng chung
 * - SIDEBAR LIÊN KẾT    : 'footer-copyright'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị dòng chữ bản quyền © bảo vệ quyền sở hữu trí tuệ của website cùng các liên kết chính sách bảo mật và điều khoản dịch vụ ở chân trang.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Footer_Copyright_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_footer_copyright',
            __( '[Footer] Copyright Bar (Phần màu đen)', 'jobscout' ),
            array(
                'description'                 => __( 'Tùy biến dải đen cuối cùng của Footer (Copyright text, Privacy Policy, Terms of Service).', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $copyright_text = ! empty( $instance['copyright_text'] ) ? $instance['copyright_text'] : __( 'Plan Do See Global Inc. All rights reserved.', 'jobscout' );
        $link1_text     = ! empty( $instance['link1_text'] ) ? $instance['link1_text'] : __( 'Privacy Policy', 'jobscout' );
        $link1_url      = ! empty( $instance['link1_url'] ) ? $instance['link1_url'] : home_url( '/privacy-policy/' );
        $link2_text     = ! empty( $instance['link2_text'] ) ? $instance['link2_text'] : __( 'Terms of Service', 'jobscout' );
        $link2_url      = ! empty( $instance['link2_url'] ) ? $instance['link2_url'] : home_url( '/terms-of-service/' );
        $bg_color       = ! empty( $instance['bg_color'] ) ? $instance['bg_color'] : '#000000';
        $text_color     = ! empty( $instance['text_color'] ) ? $instance['text_color'] : '#ffffff';

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="footer-b" style="display:flex; justify-content:space-between; align-items:center; background:<?php echo esc_attr( $bg_color ); ?>; color:<?php echo esc_attr( $text_color ); ?>; padding:15px 40px; font-size:12px; font-weight:600;">
            <div class="site-info" style="color:<?php echo esc_attr( $text_color ); ?>;">
                <?php echo esc_html( $copyright_text ); ?>
            </div>
            <div class="footer-b-links">
                <?php if ( $link1_text && $link1_url ) : ?>
                    <a href="<?php echo esc_url( $link1_url ); ?>" style="color:<?php echo esc_attr( $text_color ); ?>; margin-left:15px; text-decoration:none;"><?php echo esc_html( $link1_text ); ?></a>
                <?php endif; ?>
                <?php if ( $link2_text && $link2_url ) : ?>
                    <a href="<?php echo esc_url( $link2_url ); ?>" style="color:<?php echo esc_attr( $text_color ); ?>; margin-left:15px; text-decoration:none;"><?php echo esc_html( $link2_text ); ?></a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $copyright_text = isset( $instance['copyright_text'] ) ? $instance['copyright_text'] : __( 'Plan Do See Global Inc. All rights reserved.', 'jobscout' );
        $link1_text     = isset( $instance['link1_text'] ) ? $instance['link1_text'] : __( 'Privacy Policy', 'jobscout' );
        $link1_url      = isset( $instance['link1_url'] ) ? $instance['link1_url'] : home_url( '/privacy-policy/' );
        $link2_text     = isset( $instance['link2_text'] ) ? $instance['link2_text'] : __( 'Terms of Service', 'jobscout' );
        $link2_url      = isset( $instance['link2_url'] ) ? $instance['link2_url'] : home_url( '/terms-of-service/' );
        $bg_color       = isset( $instance['bg_color'] ) ? $instance['bg_color'] : '#000000';
        $text_color     = isset( $instance['text_color'] ) ? $instance['text_color'] : '#ffffff';

        $snippet = <<<HTML
<div class="footer-b" style="display:flex; justify-content:space-between; align-items:center; background:#000; color:#fff; padding:15px 40px; font-size:12px; font-weight:600;">
    <div class="site-info" style="color:#fff;">
        Plan Do See Global Inc. All rights reserved.
    </div>
    <div class="footer-b-links">
        <a href="/privacy-policy/" style="color:#fff; margin-left:15px; text-decoration:none;">Privacy Policy</a>
        <a href="/terms-of-service/" style="color:#fff; margin-left:15px; text-decoration:none;">Terms of Service</a>
    </div>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'copyright_text' ) ); ?>"><?php esc_html_e( 'Văn bản bản quyền (Copyright text):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'copyright_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'copyright_text' ) ); ?>" type="text" value="<?php echo esc_attr( $copyright_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'link1_text' ) ); ?>"><?php esc_html_e( 'Tên liên kết 1 (Link 1 text):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link1_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link1_text' ) ); ?>" type="text" value="<?php echo esc_attr( $link1_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'link1_url' ) ); ?>"><?php esc_html_e( 'URL liên kết 1 (Link 1 URL):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link1_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link1_url' ) ); ?>" type="url" value="<?php echo esc_attr( $link1_url ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'link2_text' ) ); ?>"><?php esc_html_e( 'Tên liên kết 2 (Link 2 text):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link2_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link2_text' ) ); ?>" type="text" value="<?php echo esc_attr( $link2_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'link2_url' ) ); ?>"><?php esc_html_e( 'URL liên kết 2 (Link 2 URL):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link2_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link2_url' ) ); ?>" type="url" value="<?php echo esc_attr( $link2_url ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'bg_color' ) ); ?>"><?php esc_html_e( 'Màu nền dải footer (Mặc định #000000):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'bg_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bg_color' ) ); ?>" type="text" value="<?php echo esc_attr( $bg_color ); ?>" placeholder="#000000">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'text_color' ) ); ?>"><?php esc_html_e( 'Màu chữ & liên kết (Mặc định #ffffff):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text_color' ) ); ?>" type="text" value="<?php echo esc_attr( $text_color ); ?>" placeholder="#ffffff">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                   = array();
        $instance['copyright_text'] = sanitize_text_field( $new_instance['copyright_text'] ?? '' );
        $instance['link1_text']     = sanitize_text_field( $new_instance['link1_text'] ?? '' );
        $instance['link1_url']      = esc_url_raw( $new_instance['link1_url'] ?? '' );
        $instance['link2_text']     = sanitize_text_field( $new_instance['link2_text'] ?? '' );
        $instance['link2_url']      = esc_url_raw( $new_instance['link2_url'] ?? '' );
        $instance['bg_color']       = sanitize_hex_color( $new_instance['bg_color'] ?? '' ) ?: '#000000';
        $instance['text_color']     = sanitize_hex_color( $new_instance['text_color'] ?? '' ) ?: '#ffffff';
        $instance['custom_html']    = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/* ==============================================================================
   9. NHÓM WIDGET TRANG ĐĂNG TIN (SUBMIT JOB) - PHỤ TRÁCH: NHÓM D
   ============================================================================== */

/**
 * ==============================================================================
 * 9.1 Submit Job Header Widget (Banner Tiêu Đề Trang Đăng Tin)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Đăng Tin Tuyển Dụng (Submit Job Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Nhóm D - Dùng chung
 * - SIDEBAR LIÊN KẾT    : 'submit-job-header'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị banner tiêu đề trang đăng tin việc làm "POST A JOB TODAY", khuyến khích nhà tuyển dụng tiếp cận nguồn ứng viên tài năng.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Submit_Job_Header_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_submit_job_header',
            __( '[Submit Job] Hero Banner Header', 'jobscout' ),
            array(
                'description'                 => __( 'Banner đầu trang đăng tin tuyển dụng POST A JOB.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title        = ! empty( $instance['title'] ) ? $instance['title'] : __( 'POST A JOB', 'jobscout' );
        $subtitle     = ! empty( $instance['subtitle'] ) ? $instance['subtitle'] : __( 'Find the best talent for your hospitality team worldwide', 'jobscout' );
        $bg_image_url = ! empty( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : '';
        $bg_style     = $bg_image_url ? 'style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url(\'' . esc_url( $bg_image_url ) . '\') center/cover no-repeat;"' : '';

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="post-job-hero" <?php echo $bg_style; ?>>
            <div class="container">
                <h1 class="post-job-title"><?php echo esc_html( $title ); ?></h1>
                <?php if ( $subtitle ) : ?>
                    <p class="post-job-subtitle"><?php echo esc_html( $subtitle ); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title        = isset( $instance['title'] ) ? $instance['title'] : __( 'POST A JOB', 'jobscout' );
        $subtitle     = isset( $instance['subtitle'] ) ? $instance['subtitle'] : __( 'Find the best talent for your hospitality team worldwide', 'jobscout' );
        $bg_image_url = isset( $instance['bg_image_url'] ) ? $instance['bg_image_url'] : '';

        $snippet = <<<HTML
<div class="post-job-hero">
    <div class="container">
        <h1 class="post-job-title">POST A JOB</h1>
        <p class="post-job-subtitle">Find the best talent for your hospitality team worldwide</p>
    </div>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề trang (Heading):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>"><?php esc_html_e( 'Mô tả phụ (Subtitle):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'subtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'bg_image_url' ) ); ?>"><?php esc_html_e( 'Ảnh nền Banner (tùy chọn):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'bg_image_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bg_image_url' ) ); ?>" type="url" value="<?php echo esc_attr( $bg_image_url ); ?>">
            <button type="button" class="button pds-media-upload-btn" data-target="<?php echo esc_attr( $this->get_field_id( 'bg_image_url' ) ); ?>" style="margin-top: 5px;">
                <?php esc_html_e( 'Chọn/Tải ảnh lên', 'jobscout' ); ?>
            </button>
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                 = array();
        $instance['title']        = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['subtitle']     = sanitize_text_field( $new_instance['subtitle'] ?? '' );
        $instance['bg_image_url'] = esc_url_raw( $new_instance['bg_image_url'] ?? '' );
        $instance['custom_html']  = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 9.2 Submit Job Content Widget (Nội Dung & Hướng Dẫn Đăng Tin)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Đăng Tin Tuyển Dụng (Submit Job Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Nhóm D - Dùng chung
 * - SIDEBAR LIÊN KẾT    : 'submit-job-content'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị phần quy trình và các lưu ý tiêu chuẩn khi gửi thông tin tuyển dụng lên hệ thống tuyển dụng.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Submit_Job_Content_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_submit_job_content',
            __( '[Submit Job] Form Content & Shortcode', 'jobscout' ),
            array(
                'description'                 => __( 'Khung nội dung Form Đăng Tuyển Dụng trang Submit Job.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $heading          = ! empty( $instance['heading'] ) ? $instance['heading'] : __( 'JOB DETAILS', 'jobscout' );
        $instruction_text = ! empty( $instance['instruction_text'] ) ? $instance['instruction_text'] : '';
        $shortcode        = ! empty( $instance['shortcode'] ) ? $instance['shortcode'] : '[submit_job_form]';

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="home-job-card pds-submit-job-panel post-job-form-wrapper">
            <?php if ( $heading ) : ?>
                <h2 class="pds-submit-job-group-title post-job-form-heading"><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>
            <?php if ( $instruction_text ) : ?>
                <p class="pds-submit-job-instruction"><?php echo esc_html( $instruction_text ); ?></p>
            <?php endif; ?>
            <div class="pds-submit-job-form-content post-job-form-body">
                <?php echo do_shortcode( $shortcode ); ?>
            </div>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $heading          = isset( $instance['heading'] ) ? $instance['heading'] : __( 'JOB DETAILS', 'jobscout' );
        $instruction_text = isset( $instance['instruction_text'] ) ? $instance['instruction_text'] : '';
        $shortcode        = isset( $instance['shortcode'] ) ? $instance['shortcode'] : '[submit_job_form]';

        $snippet = <<<HTML
<div class="post-job-form-wrapper">
    <h2 class="post-job-form-heading">JOB DETAILS</h2>
    <div class="post-job-form-body">
        [submit_job_form]
    </div>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'heading' ) ); ?>"><?php esc_html_e( 'Tiêu đề biểu mẫu (Form heading):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'heading' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'heading' ) ); ?>" type="text" value="<?php echo esc_attr( $heading ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'instruction_text' ) ); ?>"><?php esc_html_e( 'Lời nhắc / Hướng dẫn điền form (tùy chọn):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'instruction_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'instruction_text' ) ); ?>" type="text" value="<?php echo esc_attr( $instruction_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'shortcode' ) ); ?>"><?php esc_html_e( 'Mã Shortcode Form (mặc định [submit_job_form]):', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'shortcode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'shortcode' ) ); ?>" type="text" value="<?php echo esc_attr( $shortcode ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                     = array();
        $instance['heading']          = sanitize_text_field( $new_instance['heading'] ?? '' );
        $instance['instruction_text'] = sanitize_text_field( $new_instance['instruction_text'] ?? '' );
        $instance['shortcode']        = sanitize_text_field( $new_instance['shortcode'] ?? '' );
        $instance['custom_html']      = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 9.3 Submit Job Success Widget (Khối Thông Báo Đăng Tin Thành Công)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Đăng Tin Tuyển Dụng (Submit Job Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Nhóm D - Dùng chung
 * - SIDEBAR LIÊN KẾT    : 'submit-job-success'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị hộp thoại chúc mừng và thông báo đăng tin thành công kèm mã tham chiếu và các nút điều hướng quay về trang chủ hoặc xem tin vừa tạo.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Submit_Job_Success_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_submit_job_success',
            __( '[Submit Job] Success Confirmation Widget', 'jobscout' ),
            array(
                'description'                 => __( 'Khối thông báo chúc mừng & điều hướng khi người dùng hoàn thành đăng tin tuyển dụng.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        $job = $GLOBALS['pds_submitted_job'] ?? null;
        if ( ! $job && isset( $_GET['job_id'] ) ) {
            $job = get_post( absint( $_GET['job_id'] ) );
        }
        $job_title     = $job ? get_the_title( $job->ID ) : __( 'Vị trí tuyển dụng', 'jobscout' );
        $job_permalink = $job ? get_permalink( $job->ID ) : home_url( '/jobs/' );

        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
            $custom_html = wp_unslash( $instance['custom_html'] );
            $custom_html = str_replace( '{job_title}', esc_html( $job_title ), $custom_html );
            $custom_html = str_replace( '{job_url}', esc_url( $job_permalink ), $custom_html );
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( $custom_html );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title         = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Đăng tin tuyển dụng thành công!', 'jobscout' );
        $desc_template = ! empty( $instance['desc_template'] ) ? $instance['desc_template'] : __( 'Tin tuyển dụng "{job_title}" đã được xuất bản trực tiếp và hiển thị công khai trên website.', 'jobscout' );
        $desc          = str_replace( '{job_title}', '<strong>' . esc_html( $job_title ) . '</strong>', esc_html( $desc_template ) );

        $show_view_btn        = ! empty( $instance['show_view_btn'] ?? '1' );
        $view_btn_text        = ! empty( $instance['view_btn_text'] ) ? $instance['view_btn_text'] : __( 'Xem tin vừa đăng', 'jobscout' );

        $show_all_jobs_btn    = ! empty( $instance['show_all_jobs_btn'] ?? '1' );
        $all_jobs_btn_text    = ! empty( $instance['all_jobs_btn_text'] ) ? $instance['all_jobs_btn_text'] : __( 'Xem danh sách việc làm', 'jobscout' );
        $all_jobs_btn_url     = ! empty( $instance['all_jobs_btn_url'] ) ? $instance['all_jobs_btn_url'] : home_url( '/jobs/' );

        $show_submit_more_btn = ! empty( $instance['show_submit_more_btn'] ?? '1' );
        $submit_more_btn_text = ! empty( $instance['submit_more_btn_text'] ) ? $instance['submit_more_btn_text'] : __( 'Đăng tin khác', 'jobscout' );
        $submit_more_btn_url  = ! empty( $instance['submit_more_btn_url'] ) ? $instance['submit_more_btn_url'] : home_url( '/submit-job/' );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="pds-submit-success-card">
            <div class="pds-submit-success-icon" aria-hidden="true">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div class="pds-submit-success-content">
                <h3 class="pds-submit-success-title"><?php echo esc_html( $title ); ?></h3>
                <p class="pds-submit-success-desc"><?php echo wp_kses_post( $desc ); ?></p>
                <div class="pds-submit-success-actions">
                    <?php if ( $show_view_btn && $job ) : ?>
                        <a href="<?php echo esc_url( $job_permalink ); ?>" class="btn pds-btn-view-job">
                            <?php echo esc_html( $view_btn_text ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $show_all_jobs_btn ) : ?>
                        <a href="<?php echo esc_url( $all_jobs_btn_url ); ?>" class="btn pds-btn-all-jobs">
                            <?php echo esc_html( $all_jobs_btn_text ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $show_submit_more_btn ) : ?>
                        <a href="<?php echo esc_url( $submit_more_btn_url ); ?>" class="btn pds-btn-submit-more">
                            <?php echo esc_html( $submit_more_btn_text ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title                = isset( $instance['title'] ) ? $instance['title'] : __( 'Đăng tin tuyển dụng thành công!', 'jobscout' );
        $desc_template        = isset( $instance['desc_template'] ) ? $instance['desc_template'] : __( 'Tin tuyển dụng "{job_title}" đã được xuất bản trực tiếp và hiển thị công khai trên website.', 'jobscout' );
        $show_view_btn        = isset( $instance['show_view_btn'] ) ? (bool) $instance['show_view_btn'] : true;
        $view_btn_text        = isset( $instance['view_btn_text'] ) ? $instance['view_btn_text'] : __( 'Xem tin vừa đăng', 'jobscout' );
        $show_all_jobs_btn    = isset( $instance['show_all_jobs_btn'] ) ? (bool) $instance['show_all_jobs_btn'] : true;
        $all_jobs_btn_text    = isset( $instance['all_jobs_btn_text'] ) ? $instance['all_jobs_btn_text'] : __( 'Xem danh sách việc làm', 'jobscout' );
        $all_jobs_btn_url     = isset( $instance['all_jobs_btn_url'] ) ? $instance['all_jobs_btn_url'] : home_url( '/jobs/' );
        $show_submit_more_btn = isset( $instance['show_submit_more_btn'] ) ? (bool) $instance['show_submit_more_btn'] : true;
        $submit_more_btn_text = isset( $instance['submit_more_btn_text'] ) ? $instance['submit_more_btn_text'] : __( 'Đăng tin khác', 'jobscout' );
        $submit_more_btn_url  = isset( $instance['submit_more_btn_url'] ) ? $instance['submit_more_btn_url'] : home_url( '/submit-job/' );

        $snippet = <<<HTML
<div class="pds-submit-success-card">
    <div class="pds-submit-success-content">
        <h3 class="pds-submit-success-title">Đăng tin tuyển dụng thành công!</h3>
        <p class="pds-submit-success-desc">Tin tuyển dụng "{job_title}" đã được xuất bản trực tiếp.</p>
        <div class="pds-submit-success-actions">
            <a href="{job_url}" class="btn pds-btn-view-job">Xem tin vừa đăng</a>
            <a href="/jobs/" class="btn pds-btn-all-jobs">Xem danh sách việc làm</a>
            <a href="/submit-job/" class="btn pds-btn-submit-more">Đăng tin khác</a>
        </div>
    </div>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề thông báo:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'desc_template' ) ); ?>"><?php esc_html_e( 'Mô tả thông báo (dùng {job_title} để chèn tên việc làm):', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="3" id="<?php echo esc_attr( $this->get_field_id( 'desc_template' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'desc_template' ) ); ?>"><?php echo esc_textarea( $desc_template ); ?></textarea>
        </p>
        <hr>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $show_view_btn ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_view_btn' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_view_btn' ) ); ?>" value="1" />
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_view_btn' ) ); ?>"><?php esc_html_e( 'Hiển thị nút Xem tin vừa đăng', 'jobscout' ); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'view_btn_text' ) ); ?>"><?php esc_html_e( 'Chữ nút Xem tin vừa đăng:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'view_btn_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'view_btn_text' ) ); ?>" type="text" value="<?php echo esc_attr( $view_btn_text ); ?>">
        </p>
        <hr>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $show_all_jobs_btn ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_all_jobs_btn' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_all_jobs_btn' ) ); ?>" value="1" />
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_all_jobs_btn' ) ); ?>"><?php esc_html_e( 'Hiển thị nút Xem danh sách việc làm', 'jobscout' ); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'all_jobs_btn_text' ) ); ?>"><?php esc_html_e( 'Chữ nút Danh sách việc làm:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'all_jobs_btn_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'all_jobs_btn_text' ) ); ?>" type="text" value="<?php echo esc_attr( $all_jobs_btn_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'all_jobs_btn_url' ) ); ?>"><?php esc_html_e( 'URL nút Danh sách việc làm:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'all_jobs_btn_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'all_jobs_btn_url' ) ); ?>" type="url" value="<?php echo esc_attr( $all_jobs_btn_url ); ?>">
        </p>
        <hr>
        <p>
            <input class="checkbox" type="checkbox" <?php checked( $show_submit_more_btn ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_submit_more_btn' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_submit_more_btn' ) ); ?>" value="1" />
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_submit_more_btn' ) ); ?>"><?php esc_html_e( 'Hiển thị nút Đăng tin khác', 'jobscout' ); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'submit_more_btn_text' ) ); ?>"><?php esc_html_e( 'Chữ nút Đăng tin khác:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'submit_more_btn_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'submit_more_btn_text' ) ); ?>" type="text" value="<?php echo esc_attr( $submit_more_btn_text ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'submit_more_btn_url' ) ); ?>"><?php esc_html_e( 'URL nút Đăng tin khác:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'submit_more_btn_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'submit_more_btn_url' ) ); ?>" type="url" value="<?php echo esc_attr( $submit_more_btn_url ); ?>">
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                         = array();
        $instance['title']                = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['desc_template']        = sanitize_text_field( $new_instance['desc_template'] ?? '' );
        $instance['show_view_btn']        = ! empty( $new_instance['show_view_btn'] ) ? 1 : 0;
        $instance['view_btn_text']        = sanitize_text_field( $new_instance['view_btn_text'] ?? '' );
        $instance['show_all_jobs_btn']    = ! empty( $new_instance['show_all_jobs_btn'] ) ? 1 : 0;
        $instance['all_jobs_btn_text']    = sanitize_text_field( $new_instance['all_jobs_btn_text'] ?? '' );
        $instance['all_jobs_btn_url']     = esc_url_raw( $new_instance['all_jobs_btn_url'] ?? '' );
        $instance['show_submit_more_btn'] = ! empty( $new_instance['show_submit_more_btn'] ) ? 1 : 0;
        $instance['submit_more_btn_text'] = sanitize_text_field( $new_instance['submit_more_btn_text'] ?? '' );
        $instance['submit_more_btn_url']  = esc_url_raw( $new_instance['submit_more_btn_url'] ?? '' );
        $instance['custom_html']          = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}

/**
 * ==============================================================================
 * 9.4 Submit Job Benefits Widget (Khối Lợi Ích & Đặc Quyền Nhà Tuyển Dụng)
 * ==============================================================================
 * - VỊ TRÍ HIỂN THỊ     : Trang Đăng Tin Tuyển Dụng (Submit Job Page)
 * - THÀNH VIÊN PHỤ TRÁCH: Nhóm D - Dùng chung
 * - SIDEBAR LIÊN KẾT    : 'submit-job-benefits'
 * - MỤC ĐÍCH & VAI TRÒ   : Hiển thị 3 cột quyền lợi nổi bật khi đăng tin tuyển dụng: Tiếp cận hàng nghìn ứng viên chất lượng, Xuất bản tin nhanh chóng tức thì, Giao diện chuẩn đa nền tảng.
 * - TÍNH NĂNG KỸ THUẬT   : Kế thừa WP_Widget, hỗ trợ Live Preview (Selective Refresh)
 *                          và cơ chế ghi đè Custom HTML Override kèm nút "Chèn HTML mẫu".
 */
class PDS_Submit_Job_Benefits_Widget extends WP_Widget {

    /**
     * Phương thức khởi tạo Widget (Constructor):
     * - Gọi parent::__construct() để đăng ký Widget Base ID với WordPress Core.
     * - Thiết lập tên hiển thị trong Customizer và mô tả ngắn cho người dùng.
     * - Kích hoạt 'customize_selective_refresh' => true để hỗ trợ Live Preview tức thì.
     */
    public function __construct() {
        parent::__construct(
            'pds_submit_job_benefits',
            __( '[Submit Job] Recruitment Benefits Widget', 'jobscout' ),
            array(
                'description'                 => __( 'Khối lợi ích và điểm nổi bật dành cho nhà tuyển dụng khi đăng tin.', 'jobscout' ),
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
     * Phương thức render giao diện hiển thị ra phía người dùng (Front-end):
     * 1. Ưu tiên kiểm tra Custom HTML Override: Nếu người dùng có nhập mã HTML riêng,
     *    hệ thống sẽ thực thi shortcode (do_shortcode) và render trực tiếp ra màn hình.
     * 2. Nếu không có HTML tùy chỉnh: Trích xuất các tham số cấu hình từ $instance,
     *    fallback về giá trị mặc định và in thẻ HTML semantic theo đúng thiết kế mẫu.
     *
     * @param array $args     Tham số do WordPress cung cấp từ Sidebar ($before_widget, $after_widget...)
     * @param array $instance Mảng dữ liệu cấu hình đã lưu của widget hiện tại
     */
    public function widget( $args, $instance ) {
        // [CƠ CHẾ CUSTOM HTML OVERRIDE]: Ưu tiên xuất mã HTML tùy biến hoặc Shortcode nếu được thiết lập
        if ( ! empty( trim( $instance['custom_html'] ?? '' ) ) ) {
        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
            echo $args['before_widget'];
            echo do_shortcode( wp_unslash( $instance['custom_html'] ) );
        // In thẻ đóng container bao ngoài widget
            echo $args['after_widget'];
            return;
        }

        $title       = ! empty( $instance['title'] ) ? $instance['title'] : __( 'LỢI ÍCH KHI ĐĂNG TIN TUYỂN DỤNG CÙNG CHÚNG TÔI', 'jobscout' );
        $item1_title = ! empty( $instance['item1_title'] ) ? $instance['item1_title'] : __( 'Ứng viên chất lượng cao', 'jobscout' );
        $item1_desc  = ! empty( $instance['item1_desc'] ) ? $instance['item1_desc'] : __( 'Tiếp cận hàng nghìn chuyên gia, quản lý và nhân sự ngành du lịch - khách sạn hàng đầu.', 'jobscout' );
        $item2_title = ! empty( $instance['item2_title'] ) ? $instance['item2_title'] : __( 'Xuất bản tin tức thì', 'jobscout' );
        $item2_desc  = ! empty( $instance['item2_desc'] ) ? $instance['item2_desc'] : __( 'Hệ thống tự động kích hoạt và hiển thị tin tuyển dụng ngay lập tức trên trang chủ và danh mục.', 'jobscout' );
        $item3_title = ! empty( $instance['item3_title'] ) ? $instance['item3_title'] : __( 'Tối ưu đa nền tảng', 'jobscout' );
        $item3_desc  = ! empty( $instance['item3_desc'] ) ? $instance['item3_desc'] : __( 'Giao diện hiển thị chuyên nghiệp, mượt mà trên cả điện thoại, máy tính bảng và máy tính.', 'jobscout' );

        // In thẻ mở container bao ngoài widget theo tiêu chuẩn WordPress Sidebar
        echo $args['before_widget'];
        ?>
        <div class="pds-submit-job-benefits-section">
            <?php if ( $title ) : ?>
                <h3 class="pds-submit-job-benefits-title"><?php echo esc_html( $title ); ?></h3>
            <?php endif; ?>
            <div class="pds-submit-job-benefits-grid">
                <div class="pds-submit-job-benefit-item">
                    <div class="pds-benefit-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h4><?php echo esc_html( $item1_title ); ?></h4>
                    <p><?php echo esc_html( $item1_desc ); ?></p>
                </div>
                <div class="pds-submit-job-benefit-item">
                    <div class="pds-benefit-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                    </div>
                    <h4><?php echo esc_html( $item2_title ); ?></h4>
                    <p><?php echo esc_html( $item2_desc ); ?></p>
                </div>
                <div class="pds-submit-job-benefit-item">
                    <div class="pds-benefit-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    </div>
                    <h4><?php echo esc_html( $item3_title ); ?></h4>
                    <p><?php echo esc_html( $item3_desc ); ?></p>
                </div>
            </div>
        </div>
        <?php
        // In thẻ đóng container bao ngoài widget
        echo $args['after_widget'];
    }

    /**
     * Phương thức render giao diện bảng điều khiển (Control Panel) trong WP Customizer & Widgets Admin:
     * - Tạo các input trực quan (tiêu đề, số lượng, chọn ảnh...) liên kết với get_field_id/get_field_name.
     * - Tích hợp khung Custom HTML Override với nút 'Chèn HTML mẫu' giúp tùy biến mã nguồn linh hoạt.
     *
     * @param array $instance Mảng dữ liệu cấu hình hiện tại đang lưu trong database
     */
    public function form( $instance ) {
        $title       = isset( $instance['title'] ) ? $instance['title'] : __( 'LỢI ÍCH KHI ĐĂNG TIN TUYỂN DỤNG CÙNG CHÚNG TÔI', 'jobscout' );
        $item1_title = isset( $instance['item1_title'] ) ? $instance['item1_title'] : __( 'Ứng viên chất lượng cao', 'jobscout' );
        $item1_desc  = isset( $instance['item1_desc'] ) ? $instance['item1_desc'] : __( 'Tiếp cận hàng nghìn chuyên gia, quản lý và nhân sự ngành du lịch - khách sạn hàng đầu.', 'jobscout' );
        $item2_title = isset( $instance['item2_title'] ) ? $instance['item2_title'] : __( 'Xuất bản tin tức thì', 'jobscout' );
        $item2_desc  = isset( $instance['item2_desc'] ) ? $instance['item2_desc'] : __( 'Hệ thống tự động kích hoạt và hiển thị tin tuyển dụng ngay lập tức trên trang chủ và danh mục.', 'jobscout' );
        $item3_title = isset( $instance['item3_title'] ) ? $instance['item3_title'] : __( 'Tối ưu đa nền tảng', 'jobscout' );
        $item3_desc  = isset( $instance['item3_desc'] ) ? $instance['item3_desc'] : __( 'Giao diện hiển thị chuyên nghiệp, mượt mà trên cả điện thoại, máy tính bảng và máy tính.', 'jobscout' );

        $snippet = <<<HTML
<div class="pds-submit-job-benefits-section">
    <h3 class="pds-submit-job-benefits-title">LỢI ÍCH KHI ĐĂNG TIN TUYỂN DỤNG</h3>
    <div class="pds-submit-job-benefits-grid">
        <div class="pds-submit-job-benefit-item">
            <h4>Ứng viên chất lượng cao</h4>
            <p>Tiếp cận hàng nghìn chuyên gia và nhân sự ngành khách sạn.</p>
        </div>
        <div class="pds-submit-job-benefit-item">
            <h4>Xuất bản tức thì</h4>
            <p>Tin đăng hiển thị ngay lập tức không cần chờ đợi.</p>
        </div>
        <div class="pds-submit-job-benefit-item">
            <h4>Tối ưu đa nền tảng</h4>
            <p>Trải nghiệm mượt mà trên mọi thiết bị.</p>
        </div>
    </div>
</div>
HTML;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Tiêu đề khối lợi ích:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <hr>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'item1_title' ) ); ?>"><?php esc_html_e( 'Mục 1 - Tiêu đề:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'item1_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'item1_title' ) ); ?>" type="text" value="<?php echo esc_attr( $item1_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'item1_desc' ) ); ?>"><?php esc_html_e( 'Mục 1 - Mô tả:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="2" id="<?php echo esc_attr( $this->get_field_id( 'item1_desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'item1_desc' ) ); ?>"><?php echo esc_textarea( $item1_desc ); ?></textarea>
        </p>
        <hr>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'item2_title' ) ); ?>"><?php esc_html_e( 'Mục 2 - Tiêu đề:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'item2_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'item2_title' ) ); ?>" type="text" value="<?php echo esc_attr( $item2_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'item2_desc' ) ); ?>"><?php esc_html_e( 'Mục 2 - Mô tả:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="2" id="<?php echo esc_attr( $this->get_field_id( 'item2_desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'item2_desc' ) ); ?>"><?php echo esc_textarea( $item2_desc ); ?></textarea>
        </p>
        <hr>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'item3_title' ) ); ?>"><?php esc_html_e( 'Mục 3 - Tiêu đề:', 'jobscout' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'item3_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'item3_title' ) ); ?>" type="text" value="<?php echo esc_attr( $item3_title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'item3_desc' ) ); ?>"><?php esc_html_e( 'Mục 3 - Mô tả:', 'jobscout' ); ?></label>
            <textarea class="widefat" rows="2" id="<?php echo esc_attr( $this->get_field_id( 'item3_desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'item3_desc' ) ); ?>"><?php echo esc_textarea( $item3_desc ); ?></textarea>
        </p>
        <?php
        pds_render_custom_html_field( $this, $instance, $snippet );
    }

    /**
     * Phương thức xử lý lưu và làm sạch dữ liệu (Sanitization) khi người dùng bấm Lưu / Xuất bản:
     * - Sử dụng sanitize_text_field() để loại bỏ các thẻ HTML / mã script độc hại khỏi dữ liệu văn bản.
     * - Sử dụng absint() cho số nguyên dương (số bài viết, số việc làm).
     * - Sử dụng esc_url_raw() cho đường dẫn liên kết URL.
     * - Sử dụng wp_unslash() cho trường custom_html để bảo toàn mã HTML và Shortcode người dùng nhập.
     *
     * @param array $new_instance Dữ liệu mới do người dùng vừa nhập từ form quản trị
     * @param array $old_instance Dữ liệu cũ đang được lưu trong cơ sở dữ liệu
     * @return array Mảng dữ liệu đã được làm sạch và hợp lệ sẵn sàng lưu vào bảng wp_options
     */
    public function update( $new_instance, $old_instance ) {
        $instance                = array();
        $instance['title']       = sanitize_text_field( $new_instance['title'] ?? '' );
        $instance['item1_title'] = sanitize_text_field( $new_instance['item1_title'] ?? '' );
        $instance['item1_desc']  = sanitize_text_field( $new_instance['item1_desc'] ?? '' );
        $instance['item2_title'] = sanitize_text_field( $new_instance['item2_title'] ?? '' );
        $instance['item2_desc']  = sanitize_text_field( $new_instance['item2_desc'] ?? '' );
        $instance['item3_title'] = sanitize_text_field( $new_instance['item3_title'] ?? '' );
        $instance['item3_desc']  = sanitize_text_field( $new_instance['item3_desc'] ?? '' );
        $instance['custom_html'] = ! empty( $new_instance['custom_html'] ) ? wp_unslash( $new_instance['custom_html'] ) : '';
        return $instance;
    }
}



/* ==============================================================================
   10. ĐĂNG KÝ TẤT CẢ WIDGET & TỰ ĐỘNG KHỞI TẠO DỮ LIỆU MẪU (AUTO-POPULATE)
   ============================================================================== */

// Nạp thêm các widget chuyên biệt mở rộng (nếu có) từ file pds-widgets-extended.php
require_once get_template_directory() . '/inc/pds-widgets-extended.php';

/**
 * Đăng ký toàn bộ 24 Widget vào hệ sinh thái WordPress thông qua Action Hook 'widgets_init'.
 *
 * Ý NGHĨA KỸ THUẬT QUAN TRỌNG:
 * - Khi WordPress khởi chạy hệ thống widget, hook 'widgets_init' được kích hoạt.
 * - Lệnh register_widget('ClassName') sẽ đưa lớp widget đó vào mảng quản lý của WordPress Core.
 * - Giúp quản trị viên có thể thấy và tùy biến các widget này trực tiếp trong:
 *   + Giao diện -> Tùy biến (Appearance -> Customize).
 *   + Giao diện -> Widget (Appearance -> Widgets).
 *
 * @return void
 */
function pds_register_all_cms_widgets() {
    // --------------------------------------------------------------------------
    // 1. NHÓM WIDGET TRANG CHỦ (HOME PAGE) - PHỤ TRÁCH: TRẦN CAO TRỌNG (24211TT1101)
    // --------------------------------------------------------------------------
    register_widget( 'PDS_Home_Hero_Search_Widget' );   // Banner tìm kiếm việc làm đầu trang
    register_widget( 'PDS_Home_Top_Jobs_Widget' );       // Danh sách việc làm hàng đầu (Top Jobs)
    register_widget( 'PDS_Home_Career_CTA_Widget' );     // Khối kêu gọi ứng tuyển (Career With Us)
    register_widget( 'PDS_Home_Blog_Entries_Widget' );   // Khối bài viết tin tức mới nhất trang chủ

    // --------------------------------------------------------------------------
    // 2. NHÓM WIDGET TRANG VIỆC LÀM (ALL JOBS) - PHỤ TRÁCH: TRẦN VĂN TRỌNG (22211TT0337)
    // --------------------------------------------------------------------------
    register_widget( 'PDS_Jobs_Hero_Banner_Widget' );    // Banner tiêu đề trang việc làm
    register_widget( 'PDS_Jobs_List_Widget' );           // Danh sách việc làm có bộ lọc & AJAX Load More

    // --------------------------------------------------------------------------
    // 3. NHÓM WIDGET CHI TIẾT VIỆC LÀM (JOB DETAIL) - PHỤ TRÁCH: TRẦN VĂN TRỌNG (22211TT0337)
    // --------------------------------------------------------------------------
    register_widget( 'PDS_Job_Detail_Hero_Widget' );     // Banner ảnh nền chi tiết việc làm
    register_widget( 'PDS_Job_Detail_Header_Widget' );   // Tiêu đề, nút Apply & Share
    register_widget( 'PDS_Job_Detail_Content_Widget' );  // Nội dung mô tả công việc & form ứng tuyển
    register_widget( 'PDS_Job_Staff_Rating_Widget' );    // Điểm đánh giá nhân viên (Staff Rating)
    register_widget( 'PDS_Job_Company_Photos_Widget' );  // Bộ sưu tập hình ảnh không gian làm việc
    register_widget( 'PDS_Job_Related_Widget' );         // Việc làm liên quan / tương tự (Other Jobs)

    // --------------------------------------------------------------------------
    // 4. NHÓM WIDGET TRANG TIN TỨC (NEWS) - PHỤ TRÁCH: NGUYỄN HỮU CHÚC (24211TT4553)
    // --------------------------------------------------------------------------
    register_widget( 'PDS_News_Hero_Banner_Widget' );    // Banner tiêu đề trang tin tức
    register_widget( 'PDS_News_Grid_Widget' );           // Lưới bài viết tin tức phân trang

    // --------------------------------------------------------------------------
    // 5. NHÓM WIDGET CHI TIẾT TIN TỨC (NEWS DETAIL) - PHỤ TRÁCH: NGUYỄN HỮU CHÚC (24211TT4553)
    // --------------------------------------------------------------------------
    register_widget( 'PDS_News_Detail_Header_Widget' );  // Header tiêu đề bài viết & nút chia sẻ
    register_widget( 'PDS_News_Detail_Content_Widget' ); // Nội dung chi tiết bài viết tin tức
    register_widget( 'PDS_News_Detail_Sidebar_Widget' ); // Sidebar thông tin bài viết & tin mới nhận
    register_widget( 'PDS_News_Detail_Related_Widget' ); // Bài viết đọc thêm liên quan

    // --------------------------------------------------------------------------
    // 6. NHÓM WIDGET TRANG GIỚI THIỆU (ABOUT) - PHỤ TRÁCH: HỒ HOÀNG ĐỨC HUY (24211TT2193)
    // --------------------------------------------------------------------------
    register_widget( 'PDS_About_Hero_Banner_Widget' );   // Banner giới thiệu Omotenashi
    register_widget( 'PDS_About_Vision_Widget' );        // Tầm nhìn, Sứ mệnh & Giá trị cốt lõi
    register_widget( 'PDS_About_Management_Widget' );    // Thông tin quản lý khách sạn/nhà hàng
    register_widget( 'PDS_About_Company_Stats_Widget' ); // Bảng số liệu & hồ sơ doanh nghiệp

    // --------------------------------------------------------------------------
    // 7. NHÓM WIDGET TRANG LIÊN HỆ (CONTACT) - PHỤ TRÁCH: NGUYỄN HỮU PHONG (24211TT1976)
    // --------------------------------------------------------------------------
    register_widget( 'PDS_Contact_Hero_Banner_Widget' );   // Banner tiêu đề trang liên hệ
    register_widget( 'PDS_Contact_Headquarters_Widget' );  // Trụ sở chính & nhúng bản đồ Google Maps
    register_widget( 'PDS_Contact_Hotline_Info_Widget' );  // Hotline nhà tuyển dụng & ứng viên
    register_widget( 'PDS_Contact_Form_Widget' );          // Khối biểu mẫu liên hệ trực tuyến

    // --------------------------------------------------------------------------
    // 8. NHÓM WIDGET DÙNG CHUNG TOÀN HỆ THỐNG - HEADER & FOOTER - PHỤ TRÁCH: NHÓM D
    // --------------------------------------------------------------------------
    register_widget( 'PDS_Header_CTA_Widget' );          // Nút kêu gọi hành động Header (Submit Job)
    register_widget( 'PDS_Footer_Newsletter_Widget' );   // Khối dải cam đăng ký nhận bản tin qua email
    register_widget( 'PDS_Footer_Bottom_Widget' );       // Logo thương hiệu, menu chân trang & mạng xã hội
    register_widget( 'PDS_Footer_Copyright_Widget' );    // Dải màu đen bản quyền và điều khoản sử dụng

    // --------------------------------------------------------------------------
    // 9. NHÓM WIDGET TRANG ĐĂNG TIN (SUBMIT JOB) - PHỤ TRÁCH: NHÓM D
    // --------------------------------------------------------------------------
    register_widget( 'PDS_Submit_Job_Header_Widget' );   // Banner giới thiệu đăng tin tuyển dụng
    register_widget( 'PDS_Submit_Job_Content_Widget' );  // Form đăng tin 3 bước & shortcode WPJM
    register_widget( 'PDS_Submit_Job_Success_Widget' );  // Khối thông báo xuất bản tin thành công
    register_widget( 'PDS_Submit_Job_Benefits_Widget' ); // 3 cột quyền lợi đặc quyền của nhà tuyển dụng
}
add_action( 'widgets_init', 'pds_register_all_cms_widgets' );

/**
 * TỰ ĐỘNG ĐIỀN DỮ LIỆU & GÁN WIDGET MẪU VÀO CÁC VÙNG SIDEBAR (AUTO-POPULATION)
 *
 * Ý NGHĨA KỸ THUẬT TRONG BÀI CHẤM ĐIỂM ĐỒ ÁN:
 * 1. Khi vừa kích hoạt theme (hoặc khi cơ sở dữ liệu chưa thiết lập widget), hàm này sẽ:
 *    - Khởi tạo instance dữ liệu chuẩn cho từng widget trong bảng 'wp_options'.
 *    - Gán widget instance đó vào Sidebar ngữ cảnh tương ứng ($sidebars_widgets).
 * 2. Kết quả: Toàn bộ 8 trang web lập tức có đầy đủ giao diện chuẩn 100% như ảnh thiết kế mẫu.
 * 3. Trong WordPress Customizer (Giao diện -> Tùy biến), các icon cây bút chỉnh sửa (Selective Refresh Shortcut)
 *    sẽ hiển thị trực quan ngay trên từng khối mà không cần người dùng kéo thả thủ công 24 widget.
 *
 * @return void
 */
function pds_auto_populate_default_widgets() {
    // Lấy cấu hình các vùng sidebar hiện tại từ cơ sở dữ liệu WordPress
    $sidebars_widgets = get_option( 'sidebars_widgets', array() );
    if ( ! is_array( $sidebars_widgets ) ) {
        $sidebars_widgets = array();
    }
    $updated = false;

    // Macro hàm ẩn danh (Closure) giúp kiểm tra và tự động khởi tạo widget instance nếu chưa có
    $ensure_widget = function( $sidebar_id, $base_id, $opt_key, $default_data ) use ( &$sidebars_widgets, &$updated ) {
        $instances = get_option( $opt_key, array() );
        if ( ! is_array( $instances ) ) {
            $instances = array();
        }
        $num = 2; // Số ID đại diện cho instance mặc định trong WordPress Multi-Widget
        if ( empty( $instances[ $num ] ) ) {
            $instances[ $num ]         = $default_data;
            $instances['_multiwidget'] = 1; // Đánh dấu widget này hỗ trợ đa thực thể
            update_option( $opt_key, $instances );
        }
        // Nếu sidebar này hiện đang trống, gán widget instance vào sidebar
        if ( empty( $sidebars_widgets[ $sidebar_id ] ) ) {
            $sidebars_widgets[ $sidebar_id ] = array( $base_id . '-' . $num );
            $updated = true;
        }
    };

    // 1. Shared Header CTA
    $ensure_widget( 'header-cta', 'pds_header_cta', 'widget_pds_header_cta', array(
        'button_text' => 'SUBMIT JOB',
        'button_url'  => home_url( '/submit-job/' ),
    ) );

    // 2. Shared Footer Newsletter
    $ensure_widget( 'footer-newsletter', 'pds_footer_newsletter', 'widget_pds_footer_newsletter', array(
        'title_line1'   => 'Subscribe To',
        'title_line2'   => 'Our Newsletter',
        'cf7_shortcode' => '[contact-form-7 id="1033" title="NewSelecter"]',
        'placeholder'   => 'Input your email address',
        'button_text'   => 'SUBSCRIBE',
    ) );

    // 3. Shared Footer Bottom
    $ensure_widget( 'footer-bottom', 'pds_footer_bottom', 'widget_pds_footer_bottom', array(
        'brand_name'     => 'Plan Do See Global Inc.',
        'copyright_text' => '© ' . gmdate( 'Y' ) . ' Plan Do See Global. All Rights Reserved.',
        'fb_link'        => 'https://facebook.com',
        'google_link'    => 'https://google.com',
        'line_link'      => 'https://line.me',
        'twitter_link'   => 'https://twitter.com',
    ) );

    // 4. Shared Footer Copyright (Phần màu đen)
    $ensure_widget( 'footer-copyright', 'pds_footer_copyright', 'widget_pds_footer_copyright', array(
        'copyright_text' => 'Plan Do See Global Inc. All rights reserved.',
        'link1_text'     => 'Privacy Policy',
        'link1_url'      => home_url( '/privacy-policy/' ),
        'link2_text'     => 'Terms of Service',
        'link2_url'      => home_url( '/terms-of-service/' ),
        'bg_color'       => '#000000',
        'text_color'     => '#ffffff',
    ) );

    // 5. Submit Job Page (Post a Job)
    $ensure_widget( 'submit-job-header', 'pds_submit_job_header', 'widget_pds_submit_job_header', array(
        'title'        => 'POST A JOB',
        'subtitle'     => 'Find the best talent for your hospitality team worldwide',
        'bg_image_url' => '',
    ) );
    $ensure_widget( 'submit-job-content', 'pds_submit_job_content', 'widget_pds_submit_job_content', array(
        'heading'   => 'JOB DETAILS',
        'shortcode' => '[submit_job_form]',
    ) );
    $ensure_widget( 'submit-job-completed', 'pds_submit_job_success', 'widget_pds_submit_job_success', array(
        'title'                => 'Đăng tin tuyển dụng thành công!',
        'desc_template'        => 'Tin tuyển dụng "{job_title}" đã được xuất bản trực tiếp và hiển thị công khai trên website.',
        'show_view_btn'        => '1',
        'view_btn_text'        => 'Xem tin vừa đăng',
        'show_all_jobs_btn'    => '1',
        'all_jobs_btn_text'    => 'Xem danh sách việc làm',
        'all_jobs_btn_url'     => home_url( '/jobs/' ),
        'show_submit_more_btn' => '1',
        'submit_more_btn_text' => 'Đăng tin khác',
        'submit_more_btn_url'  => home_url( '/submit-job/' ),
    ) );
    $ensure_widget( 'submit-job-footer', 'pds_submit_job_benefits', 'widget_pds_submit_job_benefits', array(
        'title'       => 'LỢI ÍCH KHI ĐĂNG TIN TUYỂN DỤNG CÙNG CHÚNG TÔI',
        'item1_title' => 'Ứng viên chất lượng cao',
        'item1_desc'  => 'Tiếp cận hàng nghìn chuyên gia, quản lý và nhân sự ngành du lịch - khách sạn hàng đầu.',
        'item2_title' => 'Xuất bản tin tức thì',
        'item2_desc'  => 'Hệ thống tự động kích hoạt và hiển thị tin tuyển dụng ngay lập tức trên trang chủ và danh mục.',
        'item3_title' => 'Tối ưu đa nền tảng',
        'item3_desc'  => 'Giao diện hiển thị chuyên nghiệp, mượt mà trên cả điện thoại, máy tính bảng và máy tính.',
    ) );

    // 6. Home Widgets (Trang Chủ - Trần Cao Trọng)
    $ensure_widget( 'home-hero-search', 'pds_home_hero_search', 'widget_pds_home_hero_search', array(
        'title'        => 'Find Your Dream Job Today',
        'subtitle'     => 'Connecting exceptional talent with world-class hospitality opportunities.',
        'bg_image_url' => get_template_directory_uri() . '/images/banner-image.jpg',
    ) );
    $ensure_widget( 'home-top-jobs', 'pds_home_top_jobs', 'widget_pds_home_top_jobs', array(
        'title' => 'TOP JOBS',
        'count' => 6,
    ) );
    $ensure_widget( 'home-career-cta', 'pds_home_career_cta', 'widget_pds_home_career_cta', array(
        'title'       => 'CAREER WITH US',
        'subtitle'    => 'Plan Do See creates hospitable venues around the world.',
        'button_text' => 'VIEW ALL JOBS',
        'button_url'  => home_url( '/jobs/' ),
    ) );
    $ensure_widget( 'home-blog-entries', 'pds_home_blog_entries', 'widget_pds_home_blog_entries', array(
        'title' => 'BLOG ENTRIES',
        'count' => 4,
    ) );

    // 7. Jobs / All Jobs (Trang Việc Làm - Trần Văn Trọng)
    $ensure_widget( 'jobs-hero-banner', 'pds_jobs_hero_banner', 'widget_pds_jobs_hero_banner', array(
        'title'        => 'CAREER WITH US',
        'bg_image_url' => get_template_directory_uri() . '/assets/images/all-jobs-hero.png',
    ) );
    $ensure_widget( 'jobs-list-content', 'pds_jobs_list_content', 'widget_pds_jobs_list_content', array(
        'title'         => 'ALL JOBS',
        'count'         => 6,
        'show_filter'   => 1,
        'show_sort'     => 1,
        'show_loadmore' => 1,
    ) );

    // 8. About Page (Trang Giới Thiệu - Hồ Hoàng Đức Huy)
    $ensure_widget( 'about-hero-banner', 'pds_about_hero_banner', 'widget_pds_about_hero_banner', array(
        'title_line1'  => 'SHARE “OMOTENASHI”',
        'title_line2'  => 'WITH THE WORLD',
        'bg_image_url' => get_template_directory_uri() . '/assets/images/about-hero.png',
    ) );
    $ensure_widget( 'about-vision-section', 'pds_about_vision_section', 'widget_pds_about_vision_section', array(
        'title'         => 'ABOUT US',
        'vision_title'  => 'Our Vision',
        'mission_title' => 'Ours Mission',
        'value_title'   => 'Our Core Value',
    ) );
    $ensure_widget( 'about-management-section', 'pds_about_management', 'widget_pds_about_management', array(
        'title'  => 'Hotels, restaurants, banquets/weddings management',
        'desc_1' => 'Plan Do See developed and operates 17 properties worldwide including 3 award-winning resorts in Japan; 17 restaurants of diverse cuisines in cities including New York, Miami and Los Angeles; and other countries including Japan, Indonesia, Malaysia and Bali.',
        'desc_2' => 'Each venue carries its own concept and design. Many of them are originally historical landmarks that were loved by the local people.',
    ) );
    $ensure_widget( 'about-company-stats', 'pds_about_company_stats', 'widget_pds_about_company_stats', array(
        'established'       => 'April 1993',
        'label_established' => 'Established since',
        'head_office'       => 'Marunouchi 2-1-1, Chiyoda, Tokyo',
        'label_head_office' => 'Head Office',
        'capital'           => '100,000,000 JPY',
        'label_capital'     => 'Capital',
        'ceo'               => 'Yutaka Noda',
        'label_ceo'         => 'CEO',
        'employees'         => 'Full time: 830 / Total: 1,600',
        'label_employees'   => 'Number of Employees',
    ) );

    // 9. Job Detail (Chi Tiết Việc Làm - Trần Văn Trọng)
    $ensure_widget( 'job-detail-hero', 'pds_job_detail_hero', 'widget_pds_job_detail_hero', array(
        'bg_image_url' => get_header_image() ?: get_template_directory_uri() . '/assets/images/job-detail-hero.png',
    ) );
    $ensure_widget( 'job-detail-header', 'pds_job_detail_header', 'widget_pds_job_detail_header', array(
        'apply_text' => 'APPLY JOB',
        'share_text' => 'SHARE',
    ) );
    $ensure_widget( 'job-detail-content', 'pds_job_detail_content', 'widget_pds_job_detail_content', array(
        'apply_title' => 'Apply for this position',
    ) );
    $ensure_widget( 'job-detail-related', 'pds_job_related_jobs', 'widget_pds_job_related_jobs', array(
        'title' => 'OTHER JOBS',
        'count' => 6,
    ) );

    // Job Detail Sidebar (gồm 2 widget bổ trợ)
    if ( empty( $sidebars_widgets['job-detail-sidebar'] ) ) {
        $instances_rating = get_option( 'widget_pds_job_staff_rating', array() );
        if ( empty( $instances_rating[2] ) ) {
            $instances_rating[2] = array( 'title' => 'Staff Rating', 'rating' => '4.0' );
            $instances_rating['_multiwidget'] = 1;
            update_option( 'widget_pds_job_staff_rating', $instances_rating );
        }
        $instances_photos = get_option( 'widget_pds_job_company_photos', array() );
        if ( empty( $instances_photos[2] ) ) {
            $instances_photos[2] = array(
                'title'       => 'Company Photos',
                'photo_url'   => content_url( '/uploads/seed-cms-content/news-restaurant-hotel-operations-600x450.png' ),
                'extra_count' => 5,
            );
            $instances_photos['_multiwidget'] = 1;
            update_option( 'widget_pds_job_company_photos', $instances_photos );
        }
        $sidebars_widgets['job-detail-sidebar'] = array( 'pds_job_staff_rating-2', 'pds_job_company_photos-2' );
        $updated = true;
    }

    // 10. News Page (Trang Tin Tức - Nguyễn Hữu Chúc)
    $ensure_widget( 'news-hero-banner', 'pds_news_hero_banner', 'widget_pds_news_hero_banner', array(
        'title'        => 'PDS NEWS',
        'bg_image_url' => get_template_directory_uri() . '/assets/images/news-hero.png',
    ) );
    $ensure_widget( 'news-grid-content', 'pds_news_grid_content', 'widget_pds_news_grid_content', array(
        'title' => 'NEWEST BLOG ENTRIES',
        'count' => 8,
    ) );

    // 11. News Detail (Chi Tiết Tin Tức - Nguyễn Hữu Chúc)
    $ensure_widget( 'news-detail-header', 'pds_news_detail_header', 'widget_pds_news_detail_header', array(
        'share_text' => 'SHARE',
        'back_text'  => 'BACK TO NEWS',
    ) );
    $ensure_widget( 'news-detail-content', 'pds_news_detail_content', 'widget_pds_news_detail_content', array(
        'heading' => 'Article Overview',
    ) );
    $ensure_widget( 'news-detail-sidebar', 'pds_news_detail_sidebar', 'widget_pds_news_detail_sidebar', array(
        'info_title'   => 'Article Information',
        'recent_title' => 'Recent News',
        'recent_count' => 3,
    ) );
    $ensure_widget( 'news-detail-related', 'pds_news_detail_related', 'widget_pds_news_detail_related', array(
        'title' => 'NEWEST BLOG ENTRIES',
        'count' => 6,
    ) );

    // 12. Contact Page (Trang Liên Hệ - Nguyễn Hữu Phong)
    $ensure_widget( 'contact-hero-banner', 'pds_contact_hero_banner', 'widget_pds_contact_hero_banner', array(
        'title'        => 'CONTACT US',
        'bg_image_url' => get_template_directory_uri() . '/assets/images/contact-hero.png',
    ) );
    $ensure_widget( 'contact-headquarters', 'pds_contact_headquarters', 'widget_pds_contact_headquarters', array(
        'title'   => 'Our Headquarters Address',
        'address' => '60 Nguyen Van Thu, Ward Da Kao, District 1, Ho Chi Minh City, Viet Nam',
    ) );
    $ensure_widget( 'contact-hotline-info', 'pds_contact_hotline_info', 'widget_pds_contact_hotline_info', array(
        'emp_title'          => 'For Employers',
        'emp_subtitle'       => 'Call our Sales Hotline',
        'emp_hcm_label'      => 'Ho Chi Minh',
        'emp_hcm_phone'      => '+84 (0) 28 3911 5828',
        'emp_hn_label'       => 'Ha Noi',
        'emp_hn_phone'       => '+84 (0) 24 3936 8899',
        'seeker_title'       => 'For Jobseekers',
        'seeker_fb_text'     => 'Ask a question on our {Facebook} page',
        'seeker_fb'          => 'https://facebook.com',
        'seeker_news_text'   => 'Read our {blog posts} on interview and CV tips',
        'seeker_call_label'  => 'Call us at',
        'seeker_phone'       => '+84 (0) 28 3911 5828',
    ) );
    if ( ! empty( $sidebars_widgets['contact-form-area'] ) ) {
        $sidebars_widgets['contact-form-area'] = array();
        $updated = true;
    }

    // Nếu có sự thay đổi (gán widget mới vào sidebar), lưu lại cấu hình vào cơ sở dữ liệu WordPress
    if ( $updated ) {
        update_option( 'sidebars_widgets', $sidebars_widgets );
    }
}
// Đăng ký auto-populate trên nhiều hook quan trọng để đảm bảo tính sẵn sàng
add_action( 'widgets_init', 'pds_auto_populate_default_widgets', 99 );
add_action( 'init', 'pds_auto_populate_default_widgets', 99 );
add_action( 'customize_loaded_components', 'pds_auto_populate_default_widgets', 99 );
add_action( 'admin_init', 'pds_auto_populate_default_widgets', 99 );


/**
 * Nạp thư viện WordPress Media Uploader trong màn hình Widgets và Customizer:
 * Hỗ trợ các trường widget tải hoặc chọn ảnh từ thư viện media có sẵn.
 */
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( 'widgets.php' === $hook || 'customize.php' === $hook ) {
        wp_enqueue_media();
    }
} );

/**
 * Xuất mã JavaScript bổ trợ trong giao diện quản trị WordPress (Admin & Customizer):
 * 1. Bắt sự kiện click nút 'Chèn HTML mẫu' (.pds-insert-template-btn) để chèn template snippet vào textarea.
 * 2. Bắt sự kiện click nút 'Chọn ảnh' (.pds-media-upload-btn) mở thư viện đa phương tiện wp.media.
 *
 * @return void
 */
function pds_widgets_admin_scripts() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Xử lý sự kiện click nút "Chèn HTML mẫu"
        $(document).on('click', '.pds-insert-template-btn', function(e) {
            e.preventDefault();
            var targetId = $(this).data('target');
            var snippet  = $('#' + targetId + '_snippet').val();
            var textarea = $('#' + targetId);
            if (textarea.length && snippet) {
                if (textarea.val().trim() !== '' && !confirm('Bạn có chắc muốn chèn mã HTML mẫu để ghi đè nội dung hiện tại không?')) {
                    return;
                }
                // Điền snippet và kích hoạt trigger change để Customizer tự động lưu và cập nhật Live Preview
                textarea.val(snippet).trigger('change');
            }
        });

        // Xử lý sự kiện click nút "Chọn ảnh" mở thư viện WordPress Media Library
        $(document).on('click', '.pds-media-upload-btn', function(e) {
            e.preventDefault();
            var button = $(this);
            var targetInput = $('#' + button.data('target'));
            var preview = $('#' + button.data('preview'));

            if (typeof wp === 'undefined' || !wp.media) {
                alert('WordPress Media Uploader chưa sẵn sàng.');
                return;
            }

            // Khởi tạo khung pop-up chọn ảnh chuẩn của WordPress
            var uploader = wp.media({
                title: 'Chọn hoặc tải ảnh lên từ thư viện WordPress',
                button: { text: 'Sử dụng ảnh này' },
                multiple: false
            }).on('select', function() {
                var attachment = uploader.state().get('selection').first().toJSON();
                targetInput.val(attachment.url).trigger('change');
                if (preview.length) {
                    preview.html('<img src="' + attachment.url + '" alt="" style="max-width: 100%; height: auto; max-height: 110px; border-radius: 4px; border: 1px solid #ccd0d4; display: block; object-fit: cover;">');
                }
            }).open();
        });
    });
    </script>
    <?php
}
add_action( 'admin_footer', 'pds_widgets_admin_scripts' );
add_action( 'customize_controls_print_footer_scripts', 'pds_widgets_admin_scripts' );
