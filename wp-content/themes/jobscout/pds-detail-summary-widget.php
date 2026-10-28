<?php
/** Editable post summary for Appearance > Customize > Widgets. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class PDS_Detail_Summary_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct( 'pds_detail_summary', 'PDS Detail - Editable Post Summary', array( 'description' => 'Edit the cover, title, metadata and action buttons in Customize.' ) );
    }
    private function defaults() {
        return array( 'image_id' => 0, 'image_url' => '', 'title' => '', 'date' => '', 'author' => '', 'category' => '', 'share_label' => 'SHARE', 'back_label' => 'BACK TO NEWS', 'back_url' => '', 'show_date' => 1, 'show_author' => 1, 'show_category' => 1, 'show_share' => 1, 'show_back' => 1 );
    }
    public function widget( $args, $instance ) {
        if ( ! is_singular( 'post' ) ) { return; }
        $v = wp_parse_args( $instance, $this->defaults() );
        $post_id = get_queried_object_id();
        $cats = get_the_category( $post_id );
        $news_page = get_page_by_path( 'news' );
        $news_url = $news_page ? get_permalink( $news_page ) : home_url( '/news/' );
        $title = $v['title'] !== '' ? $v['title'] : get_the_title( $post_id );
        $date = $v['date'] !== '' ? $v['date'] : get_the_date( 'M d, Y', $post_id );
        $author = $v['author'] !== '' ? $v['author'] : get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) );
        $category = $v['category'] !== '' ? $v['category'] : implode( ', ', array_slice( wp_list_pluck( $cats, 'name' ), 0, 2 ) );
        $back_url = $v['back_url'] !== '' ? $v['back_url'] : $news_url;
        echo $args['before_widget'];
        echo '<header class="pds-news-detail-summary">';
        echo '<div class="pds-news-detail-cover">';
        if ( (int) $v['image_id'] ) {
            echo wp_get_attachment_image( (int) $v['image_id'], 'medium', false, array( 'alt' => esc_attr( $title ) ) );
        } elseif ( $v['image_url'] !== '' ) {
            echo '<img src="' . esc_url( $v['image_url'] ) . '" alt="' . esc_attr( $title ) . '">';
        } elseif ( has_post_thumbnail( $post_id ) ) {
            echo get_the_post_thumbnail( $post_id, 'medium', array( 'alt' => esc_attr( $title ) ) );
        } else {
            echo '<span class="pds-news-detail-placeholder">PDS NEWS</span>';
        }
        echo '</div><div class="pds-news-detail-summary-text"><h1>' . esc_html( $title ) . '</h1>';
        if ( ! empty( $v['show_date'] ) ) { echo '<p class="pds-news-detail-date">Published: ' . esc_html( $date ) . '</p>'; }
        echo '<div class="pds-news-detail-tags">';
        if ( ! empty( $v['show_author'] ) ) { echo '<span>' . esc_html( $author ) . '</span>'; }
        if ( ! empty( $v['show_category'] ) && $category !== '' ) { echo '<span>' . esc_html( $category ) . '</span>'; }
        echo '</div></div><div class="pds-news-detail-actions">';
        if ( ! empty( $v['show_share'] ) ) { echo '<button type="button" class="pds-news-detail-share" data-share-url="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $v['share_label'] ) . '</button>'; }
        if ( ! empty( $v['show_back'] ) ) { echo '<a class="pds-news-detail-back" href="' . esc_url( $back_url ) . '">' . esc_html( $v['back_label'] ) . '</a>'; }
        echo '</div></header>';
        echo $args['after_widget'];
    }
    public function form( $instance ) {
        $v = wp_parse_args( $instance, $this->defaults() );
        $fields = array( 'title' => 'Post title (blank = automatic)', 'date' => 'Published date (blank = automatic)', 'author' => 'Author (blank = automatic)', 'category' => 'Category (blank = automatic)', 'share_label' => 'Share button text', 'back_label' => 'Back button text', 'back_url' => 'Back URL (blank = News page)' );
        ?>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'image_url' ) ); ?>">Cover image URL (blank = featured image)</label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_url' ) ); ?>" value="<?php echo esc_attr( $v['image_url'] ); ?>" type="url"></p>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'image_id' ) ); ?>">Or Media Library attachment ID (blank/0 = use URL or featured image)</label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_id' ) ); ?>" value="<?php echo esc_attr( $v['image_id'] ); ?>" type="number" min="0"></p>
        <p><button type="button" class="button pds-summary-select-image">Choose image from Media Library</button></p>
        <?php foreach ( $fields as $key => $label ) : ?>
            <p><label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo esc_html( $label ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" value="<?php echo esc_attr( $v[$key] ); ?>" type="text"></p>
        <?php endforeach; ?>
        <?php foreach ( array( 'show_date' => 'Show date', 'show_author' => 'Show author', 'show_category' => 'Show category', 'show_share' => 'Show Share', 'show_back' => 'Show Back to News' ) as $key => $label ) : ?>
            <p><input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" value="1" <?php checked( ! empty( $v[$key] ) ); ?>> <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo esc_html( $label ); ?></label></p>
        <?php endforeach;
    }
    public function update( $new, $old ) {
        $v = $this->defaults();
        foreach ( array( 'title', 'date', 'author', 'category', 'share_label', 'back_label' ) as $key ) { $v[$key] = sanitize_text_field( $new[$key] ?? '' ); }
        foreach ( array( 'image_url', 'back_url' ) as $key ) { $v[$key] = esc_url_raw( $new[$key] ?? '' ); }
        $v['image_id'] = absint( $new['image_id'] ?? 0 );
        foreach ( array( 'show_date', 'show_author', 'show_category', 'show_share', 'show_back' ) as $key ) { $v[$key] = ! empty( $new[$key] ) ? 1 : 0; }
        return $v;
    }
}
add_action( 'widgets_init', function () { register_widget( 'PDS_Detail_Summary_Widget' ); } );

// Seed only once, and never overwrite a sidebar that already has widgets.
add_action( 'admin_init', function () {
    if ( get_option( 'pds_detail_summary_seeded' ) ) { return; }
    $areas = wp_get_sidebars_widgets();
    if ( ! empty( $areas['pds-detail-summary'] ) ) { update_option( 'pds_detail_summary_seeded', 1 ); return; }
    $instances = get_option( 'widget_pds_detail_summary', array() );
    $ids = array_filter( array_keys( $instances ), 'is_numeric' );
    $next = $ids ? max( array_map( 'intval', $ids ) ) + 1 : 2;
    $instances[$next] = array( 'share_label' => 'SHARE', 'back_label' => 'BACK TO NEWS', 'show_date' => 1, 'show_author' => 1, 'show_category' => 1, 'show_share' => 1, 'show_back' => 1 );
    update_option( 'widget_pds_detail_summary', $instances );
    $areas['pds-detail-summary'] = array( 'pds_detail_summary-' . $next );
    wp_set_sidebars_widgets( $areas );
    update_option( 'pds_detail_summary_seeded', 1 );
}, 30 );

add_action( 'customize_controls_enqueue_scripts', 'pds_summary_media_assets' );
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( $hook === 'widgets.php' ) { pds_summary_media_assets(); }
} );
function pds_summary_media_assets() {
    wp_enqueue_media();
    wp_register_script( 'pds-summary-media', false, array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'pds-summary-media' );
    wp_add_inline_script( 'pds-summary-media', <<<'JS'
jQuery(function($) {
    $(document).on('click', '.pds-summary-select-image', function(e) {
        e.preventDefault();
        var wrap = $(this).closest('.widget-content, .widget, .customize-control-widget_form');
        var id = wrap.find('input[name$="[image_id]"]').first();
        var url = wrap.find('input[name$="[image_url]"]').first();
        var frame = wp.media({ title: 'Choose News Detail cover', button: { text: 'Use this image' }, multiple: false, library: { type: 'image' } });
        frame.on('select', function() {
            var selected = frame.state().get('selection').first().toJSON();
            id.val(selected.id).trigger('change');
            url.val('').trigger('change');
        });
        frame.open();
    });
});
JS
    );
}
