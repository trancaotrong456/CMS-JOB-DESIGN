<?php
/**
 * Plugin Name: TDC Custom Modules (Khoa CNTT - TDC)
 * Description: Tùy biến 4 module giao diện: (8) Form Comment Make a Post, (9) Widget Categories FIT TDC, (10) Widget 10 Recent Posts FIT TDC, (11) Widget Archive/Top bài viết VnExpress 2 cột, và hỗ trợ Footer #3, Footer #4.
 * Version: 1.0.0
 * Author: Tran Cao Trong
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// -----------------------------------------------------------------------------
// 0. KHÔI PHỤC CLASSIC WIDGETS & ĐĂNG KÝ FOOTER #3, FOOTER #4
// -----------------------------------------------------------------------------

// Khôi phục giao diện Widget cổ điển (nếu dùng giao diện hỗ trợ widget cổ điển)
add_filter( 'use_widgets_block_editor', '__return_false' );

// Đăng ký Footer #3 và Footer #4
function tdc_register_custom_sidebars() {
    $default_args = array(
        'before_widget' => '<div id="%1$s" class="widget tdc-footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    );

    register_sidebar( array_merge( $default_args, array(
        'name'          => 'Footer #3',
        'id'            => 'footer-3',
        'description'   => 'Khu vực hiển thị Widget tại Footer #3',
    ) ) );

    register_sidebar( array_merge( $default_args, array(
        'name'          => 'Footer #4',
        'id'            => 'footer-4',
        'description'   => 'Khu vực hiển thị Widget tại Footer #4',
    ) ) );
}
add_action( 'widgets_init', 'tdc_register_custom_sidebars', 20 );

// Hiển thị Footer #1, #2, #3, #4 ra ngoài giao diện (tự động móc vào footer của Astra / themes)
function tdc_render_custom_footer_grid() {
    if ( is_active_sidebar( 'footer-widget-1' ) || is_active_sidebar( 'footer-widget-2' ) || is_active_sidebar( 'footer-3' ) || is_active_sidebar( 'footer-4' ) ) {
        echo '<div class="tdc-custom-footer-container"><div class="tdc-footer-grid">';
        
        if ( is_active_sidebar( 'footer-widget-1' ) ) {
            echo '<div class="tdc-footer-col tdc-footer-col-1">';
            dynamic_sidebar( 'footer-widget-1' );
            echo '</div>';
        }
        if ( is_active_sidebar( 'footer-widget-2' ) ) {
            echo '<div class="tdc-footer-col tdc-footer-col-2">';
            dynamic_sidebar( 'footer-widget-2' );
            echo '</div>';
        }
        if ( is_active_sidebar( 'footer-3' ) ) {
            echo '<div class="tdc-footer-col tdc-footer-col-3">';
            dynamic_sidebar( 'footer-3' );
            echo '</div>';
        }
        if ( is_active_sidebar( 'footer-4' ) ) {
            echo '<div class="tdc-footer-col tdc-footer-col-4">';
            dynamic_sidebar( 'footer-4' );
            echo '</div>';
        }
        
        echo '</div></div>';
    }
}
// Không tự động render footer grid để tránh hiển thị trùng lặp với root-theme
// add_action( 'astra_footer_before', 'tdc_render_custom_footer_grid' );
// add_action( 'wp_footer', 'tdc_render_custom_footer_grid_fallback', 5 );


// -----------------------------------------------------------------------------
// 1. MODULE (8) - TÙY BIẾN FORM COMMENT KHI ĐÃ ĐĂNG NHẬP (MAKE A POST - BOOTSNIPP)
// -----------------------------------------------------------------------------
add_filter( 'comment_form_defaults', 'tdc_custom_comment_form_defaults', 9999 );
add_filter( 'astra_comment_form_default_markup', 'tdc_custom_comment_form_defaults', 9999 );
function tdc_custom_comment_form_defaults( $defaults ) {
    if ( is_user_logged_in() ) {
        $defaults['title_reply']          = ''; // Ẩn "Leave a comment"
        $defaults['title_reply_before']   = '<span style="display:none">';
        $defaults['title_reply_after']    = '</span>';
        $defaults['title_reply_to']       = '';
        $defaults['logged_in_as']         = ''; // Ẩn "Logged in as..."
        $defaults['comment_notes_before'] = '';
        $defaults['comment_notes_after']  = '';
        $defaults['class_form']          .= ' tdc-make-post-form';
        $defaults['class_submit']         = 'tdc-make-post-share-btn';
        $defaults['label_submit']         = 'share';
        
        // Tùy biến khung textarea dạng "Make a Post"
        $defaults['comment_field'] = '
        <div class="tdc-make-a-post-card">
            <div class="tdc-make-a-post-tab">Make a Post</div>
            <div class="tdc-make-a-post-body">
                <textarea id="comment" name="comment" class="tdc-make-a-post-textarea" placeholder="What are you thinking..." required="required"></textarea>
            </div>
        </div>';
        
        // Chỉnh sửa wrapper nút submit thành căn phải
        $defaults['submit_field'] = '<div class="tdc-make-a-post-footer">%1$s %2$s</div>';
    }
    return $defaults;
}

add_filter( 'comment_form_field_comment', function( $field ) {
    if ( is_user_logged_in() ) {
        return '
        <div class="tdc-make-a-post-card">
            <div class="tdc-make-a-post-tab">Make a Post</div>
            <div class="tdc-make-a-post-body">
                <textarea id="comment" name="comment" class="tdc-make-a-post-textarea" placeholder="What are you thinking..." required="required"></textarea>
            </div>
        </div>';
    }
    return $field;
}, 9999 );

add_filter( 'comment_form_submit_button', function( $submit_button, $args ) {
    if ( is_user_logged_in() ) {
        return '<button name="submit" type="submit" id="submit" class="tdc-make-post-share-btn">share</button>';
    }
    return $submit_button;
}, 9999, 2 );



// -----------------------------------------------------------------------------
// 2. MODULE (9) - CATEGORIES PHONG CÁCH KHOA CNTT - TDC (FIT TDC)
// -----------------------------------------------------------------------------
class TDC_FIT_Categories_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'tdc_fit_categories',
            'TDC (9) - Categories (Phong cách FIT TDC)',
            array( 'description' => 'Hiển thị danh mục phong cách FIT TDC với bullet vàng và đường kẻ mờ' )
        );
    }

    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'Categories';
        $title = apply_filters( 'widget_title', $title );

        echo $args['before_widget'];
        ?>
        <div class="tdc-fit-categories-box">
            <h3 class="tdc-fit-categories-title"><?php echo esc_html( $title ); ?></h3>
            <div class="tdc-fit-title-stripe"></div>
            <ul class="tdc-fit-categories-list">
                <?php
                $categories = get_categories( array(
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                    'hide_empty' => false,
                ) );

                if ( ! empty( $categories ) ) {
                    foreach ( $categories as $cat ) {
                        $cat_link = esc_url( get_category_link( $cat->term_id ) );
                        echo '<li>';
                        echo '<span class="tdc-fit-bullet">&#8226;</span> ';
                        echo '<a href="' . $cat_link . '" class="tdc-fit-cat-link">' . esc_html( $cat->name ) . '</a>';
                        echo '</li>';
                    }
                } else {
                    echo '<li>Chưa có chuyên mục</li>';
                }
                ?>
            </ul>
        </div>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'Categories';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Tiêu đề:</label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php 
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        return $instance;
    }
}


// -----------------------------------------------------------------------------
// 3. MODULE (10) - 10 RECENT POSTS PHONG CÁCH KHOA CNTT - TDC (FIT TDC)
// -----------------------------------------------------------------------------
class TDC_FIT_Recent_Posts_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'tdc_fit_recent_posts',
            'TDC (10) - Recent Posts (Phong cách FIT TDC)',
            array( 'description' => 'Hiển thị 10 bài viết mới nhất với nền xanh ngọc, hiển thị ngày/tháng/năm và nút xem tất cả' )
        );
    }

    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'Bài viết mới nhất';
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 10;
        $more_link = ! empty( $instance['more_link'] ) ? esc_url( $instance['more_link'] ) : home_url( '/' );

        echo $args['before_widget'];
        ?>
        <div class="tdc-fit-recent-box">
            <?php if ( ! empty( $title ) ) : ?>
                <div class="tdc-fit-recent-header"><?php echo esc_html( $title ); ?></div>
            <?php endif; ?>

            <div class="tdc-fit-recent-list">
                <?php
                $recent_query = new WP_Query( array(
                    'posts_per_page'      => $count,
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => 1,
                ) );

                if ( $recent_query->have_posts() ) :
                    while ( $recent_query->have_posts() ) : $recent_query->the_post();
                        $day   = get_the_date( 'd' );
                        $month = get_the_date( 'm' );
                        $year  = get_the_date( 'y' );
                        ?>
                        <div class="tdc-fit-post-item">
                            <div class="tdc-fit-date-box">
                                <div class="tdc-fit-date-top">
                                    <span class="tdc-fit-day"><?php echo esc_html( $day ); ?></span>
                                    <span class="tdc-fit-dash">—</span>
                                    <span class="tdc-fit-year"><?php echo esc_html( $year ); ?></span>
                                </div>
                                <div class="tdc-fit-date-bottom">
                                    <span class="tdc-fit-month"><?php echo esc_html( $month ); ?></span>
                                </div>
                            </div>
                            <div class="tdc-fit-title-wrap">
                                <a href="<?php the_permalink(); ?>" class="tdc-fit-post-title" title="<?php the_title_attribute(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </div>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p style="color:#fff; padding:10px;">Chưa có bài viết nào.</p>';
                endif;
                ?>
            </div>

            <div class="tdc-fit-recent-footer">
                <a href="<?php echo $more_link; ?>" class="tdc-fit-all-news-btn">XEM TẤT CẢ TIN TỨC</a>
            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title     = ! empty( $instance['title'] ) ? $instance['title'] : 'Bài viết mới nhất';
        $count     = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 10;
        $more_link = ! empty( $instance['more_link'] ) ? $instance['more_link'] : home_url( '/' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Tiêu đề:</label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">Số lượng bài viết (mặc định 10):</label> 
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $count ); ?>" size="3">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'more_link' ) ); ?>">Đường dẫn "Xem tất cả tin tức":</label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'more_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'more_link' ) ); ?>" type="text" value="<?php echo esc_attr( $more_link ); ?>">
        </p>
        <?php 
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']     = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['count']     = ( ! empty( $new_instance['count'] ) ) ? absint( $new_instance['count'] ) : 10;
        $instance['more_link'] = ( ! empty( $new_instance['more_link'] ) ) ? esc_url_raw( $new_instance['more_link'] ) : '';
        return $instance;
    }
}


// -----------------------------------------------------------------------------
// 4. MODULE (11) - ARCHIVE / TOP BÀI VIẾT 2 CỘT (PHONG CÁCH VNEXPRESS XEM NHIỀU)
// -----------------------------------------------------------------------------
class TDC_VnExpress_Archive_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'tdc_vnexpress_archive',
            'TDC (11) - Archive/Top bài viết 2 cột (VnExpress)',
            array( 'description' => 'Hiển thị bài viết lưu trữ dạng 2 cột chia đôi (Cột 1: 1-4, Cột 2: 5-8) với số thứ tự to đậm' )
        );
    }

    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'Xem nhiều';
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 8;

        echo $args['before_widget'];
        ?>
        <div class="tdc-vnexpress-wrap">
            <?php if ( ! empty( $title ) ) : ?>
                <div class="tdc-vne-header">
                    <h3 class="tdc-vne-title"><?php echo esc_html( $title ); ?></h3>
                </div>
            <?php endif; ?>

            <div class="tdc-vne-grid">
                <?php
                $vne_query = new WP_Query( array(
                    'posts_per_page'      => $count,
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => 1,
                ) );

                $all_posts = $vne_query->posts;
                $half = ceil( count( $all_posts ) / 2 );
                $col1 = array_slice( $all_posts, 0, $half );
                $col2 = array_slice( $all_posts, $half );

                // Cột 1 (Số 1 -> 4)
                echo '<div class="tdc-vne-col">';
                $idx = 1;
                foreach ( $col1 as $p ) {
                    $permalink = esc_url( get_permalink( $p->ID ) );
                    $comment_count = get_comments_number( $p->ID );
                    echo '<div class="tdc-vne-item">';
                    echo '  <div class="tdc-vne-number">' . $idx . '</div>';
                    echo '  <div class="tdc-vne-title-wrap">';
                    echo '    <a href="' . $permalink . '" class="tdc-vne-item-title">' . esc_html( get_the_title( $p->ID ) ) . '</a>';
                    if ( $comment_count > 0 ) {
                        echo ' <span class="tdc-vne-comments-count">&#128172; ' . $comment_count . '</span>';
                    }
                    echo '  </div>';
                    echo '</div>';
                    $idx++;
                }
                echo '</div>';

                // Cột 2 (Số 5 -> 8)
                echo '<div class="tdc-vne-col">';
                foreach ( $col2 as $p ) {
                    $permalink = esc_url( get_permalink( $p->ID ) );
                    $comment_count = get_comments_number( $p->ID );
                    echo '<div class="tdc-vne-item">';
                    echo '  <div class="tdc-vne-number">' . $idx . '</div>';
                    echo '  <div class="tdc-vne-title-wrap">';
                    echo '    <a href="' . $permalink . '" class="tdc-vne-item-title">' . esc_html( get_the_title( $p->ID ) ) . '</a>';
                    if ( $comment_count > 0 ) {
                        echo ' <span class="tdc-vne-comments-count">&#128172; ' . $comment_count . '</span>';
                    }
                    echo '  </div>';
                    echo '</div>';
                    $idx++;
                }
                echo '</div>';
                ?>
            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : 'Xem nhiều';
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 8;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Tiêu đề (mặc định: Xem nhiều):</label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">Số lượng bài viết (mặc định 8 bài - 2 cột):</label> 
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" step="1" min="2" max="20" value="<?php echo esc_attr( $count ); ?>" size="3">
        </p>
        <?php 
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['count'] = ( ! empty( $new_instance['count'] ) ) ? absint( $new_instance['count'] ) : 8;
        return $instance;
    }
}

// Đăng ký cả 3 Custom Widgets
function tdc_register_custom_widgets() {
    register_widget( 'TDC_FIT_Categories_Widget' );
    register_widget( 'TDC_FIT_Recent_Posts_Widget' );
    register_widget( 'TDC_VnExpress_Archive_Widget' );
}
add_action( 'widgets_init', 'tdc_register_custom_widgets', 30 );


// -----------------------------------------------------------------------------
// 5. CUNG CẤP SHORTCODES ĐỂ DỄ DÀNG CHÈN VÀO TRANG, BÀI VIẾT HOẶC FOOTER
// -----------------------------------------------------------------------------

// [tdc_categories title="Categories"]
add_shortcode( 'tdc_categories', function( $atts ) {
    $atts = shortcode_atts( array( 'title' => 'Categories' ), $atts );
    ob_start();
    the_widget( 'TDC_FIT_Categories_Widget', $atts );
    return ob_get_clean();
} );

// [tdc_recent_posts count="10" title="Bài viết mới nhất"]
add_shortcode( 'tdc_recent_posts', function( $atts ) {
    $atts = shortcode_atts( array( 'count' => 10, 'title' => 'Bài viết mới nhất', 'more_link' => home_url('/') ), $atts );
    ob_start();
    the_widget( 'TDC_FIT_Recent_Posts_Widget', $atts );
    return ob_get_clean();
} );

// [tdc_vnexpress_archive count="8" title="Xem nhiều"]
add_shortcode( 'tdc_vnexpress_archive', function( $atts ) {
    $atts = shortcode_atts( array( 'count' => 8, 'title' => 'Xem nhiều' ), $atts );
    ob_start();
    the_widget( 'TDC_VnExpress_Archive_Widget', $atts );
    return ob_get_clean();
} );


// -----------------------------------------------------------------------------
// 6. CSS STYLING ĐỒNG BỘ 100% THEO ĐÚNG MẪU THIẾT KẾ TRONG ĐỀ
// -----------------------------------------------------------------------------
add_action( 'wp_head', 'tdc_custom_modules_inline_css', 99 );
function tdc_custom_modules_inline_css() {
    ?>
    <style id="tdc-custom-modules-css">
    /* =========================================================
       0. CSS CHO FOOTER GRID 4 CỘT
       ========================================================= */
    .tdc-custom-footer-container {
        width: 100%;
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 40px 20px;
        margin-top: 40px;
    }
    .tdc-footer-grid {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
    }
    .tdc-footer-col {
        min-width: 0;
    }
    .tdc-footer-col .widget-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 18px;
        color: #2c3e50;
        border-bottom: 2px solid #3498db;
        padding-bottom: 8px;
        display: inline-block;
    }

    /* =========================================================
       1. CSS MODULE (8) - MAKE A POST (BOOTSNIPP)
       ========================================================= */
    .tdc-make-post-form {
        margin-top: 30px;
    }
    .tdc-make-a-post-card {
        position: relative;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 0;
        margin-top: 35px;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .tdc-make-a-post-tab {
        position: absolute;
        top: -33px;
        left: 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-bottom: 1px solid #ffffff;
        padding: 6px 20px;
        border-radius: 6px 6px 0 0;
        font-weight: 600;
        color: #4a5568;
        font-size: 15px;
        z-index: 2;
    }
    .tdc-make-a-post-body {
        padding: 16px;
    }
    .tdc-make-a-post-textarea {
        width: 100% !important;
        border: 1px solid #d1d5db !important;
        border-radius: 6px !important;
        padding: 12px 14px !important;
        font-size: 15px !important;
        color: #374151 !important;
        min-height: 95px !important;
        background: #ffffff !important;
        box-sizing: border-box !important;
        outline: none !important;
        resize: vertical !important;
        transition: border-color 0.2s;
    }
    .tdc-make-a-post-textarea:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.15) !important;
    }
    .tdc-make-a-post-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
        padding: 0 4px;
    }
    .tdc-make-post-share-btn {
        background-color: #007bff !important;
        color: #ffffff !important;
        border: none !important;
        padding: 7px 26px !important;
        border-radius: 5px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        text-transform: lowercase !important;
        transition: background-color 0.2s;
        display: inline-block;
    }
    .tdc-make-post-share-btn:hover {
        background-color: #0056b3 !important;
    }

    /* =========================================================
       2. CSS MODULE (9) - CATEGORIES PHONG CÁCH FIT TDC
       ========================================================= */
    .tdc-fit-categories-box,
    .widget_categories,
    .wp-block-categories {
        background: #ffffff;
        border: 1px solid #e9ecef;
        padding: 20px 24px;
        border-radius: 4px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    }
    .tdc-fit-categories-title,
    .widget_categories .widget-title,
    .wp-block-categories h2 {
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #222222 !important;
        margin: 0 0 10px 0 !important;
        text-transform: none;
        letter-spacing: -0.2px;
        position: relative;
    }
    .tdc-fit-title-stripe,
    .widget_categories .widget-title::after,
    .wp-block-categories h2::after {
        content: "";
        display: block;
        height: 8px;
        width: 100%;
        background: repeating-linear-gradient(
            -45deg,
            #d5d5d5,
            #d5d5d5 2px,
            transparent 2px,
            transparent 6px
        );
        margin-top: 8px;
        margin-bottom: 14px;
    }
    .tdc-fit-categories-list,
    .widget_categories ul,
    .wp-block-categories-list {
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .tdc-fit-categories-list li,
    .widget_categories ul li,
    .wp-block-categories-list li {
        padding: 12px 0 !important;
        border-bottom: 1px solid #efefef !important;
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
    }
    .tdc-fit-categories-list li:last-child,
    .widget_categories ul li:last-child,
    .wp-block-categories-list li:last-child {
        border-bottom: none !important;
    }
    .tdc-fit-bullet,
    .widget_categories ul li::before,
    .wp-block-categories-list li::before {
        content: "•";
        color: #f5af02 !important;
        font-size: 22px !important;
        line-height: 1 !important;
        margin-right: 12px !important;
        display: inline-block !important;
    }
    .tdc-fit-cat-link,
    .widget_categories ul li a,
    .wp-block-categories-list li a {
        color: #337ab7 !important;
        text-decoration: none !important;
        font-size: 14.5px !important;
        font-weight: 500 !important;
        transition: color 0.2s !important;
    }
    .tdc-fit-cat-link:hover,
    .widget_categories ul li a:hover,
    .wp-block-categories-list li a:hover {
        color: #23527c !important;
        text-decoration: underline !important;
    }


    /* =========================================================
       3. CSS MODULE (10) - 10 BÀI VIẾT MỚI NHẤT PHONG CÁCH FIT TDC
       ========================================================= */
    .tdc-fit-recent-box {
        background-color: #42b4b7;
        border-radius: 4px;
        overflow: hidden;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .tdc-fit-recent-header {
        font-size: 17px;
        font-weight: 700;
        color: #205c5e;
        background-color: #d1efef;
        padding: 12px 18px;
    }
    .tdc-fit-recent-list {
        padding: 16px 20px;
    }
    .tdc-fit-post-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed rgba(255, 255, 255, 0.25);
    }
    .tdc-fit-post-item:last-child {
        border-bottom: none;
    }
    .tdc-fit-date-box {
        flex: 0 0 52px;
        text-align: center;
        margin-right: 18px;
        font-weight: 700;
        line-height: 1.1;
        color: #ffffff;
    }
    .tdc-fit-date-top {
        font-size: 13px;
        border-bottom: 1px solid rgba(255,255,255,0.7);
        padding-bottom: 2px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2px;
    }
    .tdc-fit-day {
        font-size: 14px;
    }
    .tdc-fit-dash {
        font-size: 10px;
        opacity: 0.8;
    }
    .tdc-fit-year {
        font-size: 11px;
        opacity: 0.9;
    }
    .tdc-fit-date-bottom {
        padding-top: 2px;
    }
    .tdc-fit-month {
        font-size: 13px;
    }
    .tdc-fit-title-wrap {
        flex: 1;
        min-width: 0;
    }
    .tdc-fit-post-title {
        color: #ffffff !important;
        text-decoration: none !important;
        font-size: 14.5px;
        line-height: 1.4;
        display: block;
        transition: opacity 0.2s;
    }
    .tdc-fit-post-title:hover {
        opacity: 0.85;
        text-decoration: underline !important;
    }
    .tdc-fit-recent-footer {
        background-color: #3aa6a9;
        text-align: center;
        padding: 12px 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
    }
    .tdc-fit-all-news-btn {
        color: #ffffff !important;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-decoration: none !important;
        display: block;
        transition: opacity 0.2s;
    }
    .tdc-fit-all-news-btn:hover {
        opacity: 0.85;
    }

    /* =========================================================
       4. CSS MODULE (11) - ARCHIVE / XEM NHIỀU 2 CỘT VNEXPRESS
       ========================================================= */
    .tdc-vnexpress-wrap {
        background: #ffffff;
        padding: 20px 24px;
        border-radius: 4px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .tdc-vne-header {
        border-bottom: 1px solid #e5e5e5;
        margin-bottom: 20px;
        padding-bottom: 8px;
        position: relative;
    }
    .tdc-vne-title {
        font-size: 20px;
        font-weight: 700;
        color: #222222;
        margin: 0;
        display: inline-block;
        position: relative;
    }
    .tdc-vne-title::after {
        content: "";
        position: absolute;
        bottom: -9px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #b52a1d;
    }
    .tdc-vne-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    @media (max-width: 768px) {
        .tdc-vne-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
    }
    .tdc-vne-col {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .tdc-vne-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f2f2f2;
    }
    .tdc-vne-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .tdc-vne-number {
        font-size: 32px;
        font-weight: 700;
        font-family: Georgia, 'Times New Roman', Times, serif;
        color: #222222;
        line-height: 1;
        flex: 0 0 28px;
    }
    .tdc-vne-title-wrap {
        flex: 1;
    }
    .tdc-vne-item-title {
        font-size: 14.5px;
        line-height: 1.45;
        color: #222222 !important;
        text-decoration: none !important;
        font-weight: 500;
        display: inline;
        transition: color 0.2s;
    }
    .tdc-vne-item-title:hover {
        color: #076db6 !important;
    }
    .tdc-vne-comments-count {
        font-size: 12px;
        color: #888888;
        margin-left: 6px;
        white-space: nowrap;
    }
    </style>
    <?php
}
