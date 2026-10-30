<?php
/** News Detail editable widgets; dynamic values remain tied to each post. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class PDS_Detail_Section_Widget extends WP_Widget {
    private static $sections = array(
        'breadcrumb' => 'Breadcrumb', 'header' => 'Header banner',
        'before-article' => 'Article heading', 'article-content' => 'Article body',
        'after-article' => 'After article', 'sidebar' => 'Sidebar introduction',
        'article-info' => 'Article information', 'recent-news' => 'Recent news',
        'before-related' => 'Related heading', 'related-content' => 'Related cards',
    );
    public function __construct() {
        parent::__construct( 'pds_detail_section', 'PDS Detail - Editable Section', array( 'description' => 'Customize News Detail sections while retaining per-post data.' ) );
    }
    private function defaults() {
        return array( 'section' => 'header', 'heading' => '', 'description' => '', 'html' => '', 'image_id' => 0, 'image_url' => '', 'count' => 3, 'button_label' => 'VIEW ALL NEWS', 'button_url' => '', 'show_image' => 1, 'show_date' => 1, 'show_author' => 1, 'show_category' => 1, 'show_excerpt' => 1, 'show_button' => 1 );
    }
    private function image( $v ) {
        if ( (int) $v['image_id'] ) { return wp_get_attachment_image( (int) $v['image_id'], 'large' ); }
        if ( $v['image_url'] ) { return '<img src="' . esc_url( $v['image_url'] ) . '" alt="">'; }
        return '';
    }
    public function widget( $args, $instance ) {
        if ( ! is_singular( 'post' ) ) { return; }
        $v = wp_parse_args( $instance, $this->defaults() );
        $section = $v['section'];
        $id = get_queried_object_id();
        $news_page = get_page_by_path( 'news' );
        $news_url = $news_page ? get_permalink( $news_page ) : home_url( '/news/' );
        $heading = $v['heading'];
        $cats = get_the_category( $id );
        echo $args['before_widget'];
        switch ( $section ) {
            case 'breadcrumb':
                echo '<nav aria-label="Breadcrumb"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a> <span aria-hidden="true">/</span> <a href="' . esc_url( $news_url ) . '">News</a> <span aria-hidden="true">/</span> <span aria-current="page">' . esc_html( $heading ?: 'News Detail' ) . '</span></nav>';
                break;
            case 'header':
                $img = $this->image( $v );
                if ( $img || $heading || $v['description'] || $v['html'] ) {
                    echo '<div class="pds-detail-custom-banner">' . $img;
                    if ( $heading ) { echo '<h2>' . esc_html( $heading ) . '</h2>'; }
                    if ( $v['description'] ) { echo '<p>' . esc_html( $v['description'] ) . '</p>'; }
                    echo wp_kses_post( $v['html'] ) . '</div>';
                }
                break;
            case 'before-article':
                echo '<h2>' . esc_html( $heading ?: 'Article Overview' ) . '</h2>' . wp_kses_post( $v['html'] );
                break;
            case 'article-content':
                // Always render current post content, never a global static article.
                echo apply_filters( 'the_content', get_post_field( 'post_content', $id ) );
                if ( $v['html'] ) { echo wp_kses_post( $v['html'] ); }
                break;
            case 'after-article':
                echo wp_kses_post( $v['html'] );
                if ( ! empty( $v['show_button'] ) ) {
                    echo '<a class="pds-news-read-more" href="' . esc_url( $v['button_url'] ?: $news_url ) . '">' . esc_html( $v['button_label'] ?: 'VIEW ALL NEWS' ) . '</a>';
                }
                break;
            case 'sidebar':
                if ( $heading || $v['description'] || $v['html'] || $v['image_id'] || $v['image_url'] ) {
                    echo '<section class="pds-news-detail-side-card">' . $this->image( $v );
                    if ( $heading ) { echo '<h2>' . esc_html( $heading ) . '</h2>'; }
                    if ( $v['description'] ) { echo '<p>' . esc_html( $v['description'] ) . '</p>'; }
                    echo wp_kses_post( $v['html'] ) . '</section>';
                }
                break;
            case 'article-info':
                echo '<h2>' . esc_html( $heading ?: 'Article Information' ) . '</h2><dl>';
                if ( ! empty( $v['show_date'] ) ) { echo '<dt>Published</dt><dd>' . esc_html( get_the_date( 'M d, Y', $id ) ) . '</dd>'; }
                if ( ! empty( $v['show_author'] ) ) { echo '<dt>Author</dt><dd>' . esc_html( get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $id ) ) ) . '</dd>'; }
                if ( ! empty( $v['show_category'] ) ) { echo '<dt>Category</dt><dd>' . esc_html( $cats ? implode( ', ', wp_list_pluck( $cats, 'name' ) ) : 'News' ) . '</dd>'; }
                echo '</dl>' . wp_kses_post( $v['html'] );
                break;
            case 'recent-news':
                echo '<h2>' . esc_html( $heading ?: 'Recent News' ) . '</h2>';
                $recent = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => max( 1, min( 12, absint( $v['count'] ) ) ), 'exclude' => array( $id ), 'ignore_sticky_posts' => true ) );
                if ( $recent ) {
                    echo '<div class="pds-news-detail-recent-list">';
                    foreach ( $recent as $post ) {
                        echo '<a class="pds-news-detail-recent-item" href="' . esc_url( get_permalink( $post->ID ) ) . '">';
                        if ( ! empty( $v['show_image'] ) ) { echo has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail( $post->ID, 'thumbnail' ) : '<span class="pds-news-detail-mini-placeholder">NEWS</span>'; }
                        echo '<span>' . esc_html( get_the_title( $post->ID ) ) . '</span></a>';
                    }
                    echo '</div>';
                } else { echo '<p>No recent articles.</p>'; }
                break;
            case 'before-related':
                echo '<h2 id="pds-news-detail-related-title">' . esc_html( $heading ?: 'NEWEST BLOG ENTRIES' ) . '</h2>' . wp_kses_post( $v['html'] );
                break;
            case 'related-content':
                $count = max( 1, min( 12, absint( $v['count'] ) ) );
                $cat_ids = wp_list_pluck( $cats, 'term_id' );
                $query = array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $count, 'exclude' => array( $id ), 'ignore_sticky_posts' => true );
                if ( $cat_ids ) { $query['category__in'] = $cat_ids; }
                $posts = get_posts( $query );
                if ( count( $posts ) < $count ) {
                    $posts = array_merge( $posts, get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $count - count( $posts ), 'exclude' => array_merge( array( $id ), wp_list_pluck( $posts, 'ID' ) ), 'ignore_sticky_posts' => true ) ) );
                }
                echo '<div class="pds-news-detail-related-grid">';
                foreach ( $posts as $post ) {
                    $pid = $post->ID;
                    $pcats = get_the_category( $pid );
                    $url = get_permalink( $pid );
                    echo '<article class="pds-news-detail-related-card">';
                    if ( ! empty( $v['show_image'] ) ) {
                        echo '<a class="pds-news-detail-related-image" href="' . esc_url( $url ) . '">';
                        echo has_post_thumbnail( $pid ) ? get_the_post_thumbnail( $pid, 'thumbnail' ) : '<span class="pds-news-detail-placeholder">NEWS</span>';
                        echo '</a>';
                    }
                    echo '<div class="pds-news-detail-related-body"><h3><a href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $pid ) ) . '</a></h3>';
                    if ( ! empty( $v['show_date'] ) ) { echo '<p class="pds-news-detail-related-date">Published: ' . esc_html( get_the_date( 'M d, Y', $pid ) ) . '</p>'; }
                    if ( ! empty( $v['show_category'] ) ) { echo '<p class="pds-news-detail-related-meta">' . esc_html( $pcats ? $pcats[0]->name : 'News' ) . '</p>'; }
                    if ( ! empty( $v['show_excerpt'] ) ) { echo '<p class="pds-news-detail-related-excerpt">' . esc_html( wp_trim_words( get_the_excerpt( $pid ), 18, '…' ) ) . '</p>'; }
                    if ( ! empty( $v['show_button'] ) ) { echo '<a class="pds-news-read-more" href="' . esc_url( $url ) . '">' . esc_html( $v['button_label'] ?: 'Read More' ) . '</a>'; }
                    echo '</div></article>';
                }
                echo '</div>';
                break;
        }
        echo $args['after_widget'];
    }
    public function form( $instance ) {
        $v = wp_parse_args( $instance, $this->defaults() );
        echo '<p><label>Section</label><select class="widefat" name="' . esc_attr( $this->get_field_name( 'section' ) ) . '">';
        foreach ( self::$sections as $key => $label ) { echo '<option value="' . esc_attr( $key ) . '" ' . selected( $v['section'], $key, false ) . '>' . esc_html( $label ) . '</option>'; }
        echo '</select></p>';
        $fields = array( 'heading' => 'Heading / title (blank = automatic)', 'description' => 'Description', 'button_label' => 'Button label', 'button_url' => 'Button URL', 'image_url' => 'Image URL (or choose Media Library)', 'image_id' => 'Media Library image ID', 'count' => 'Number of posts (1–12)' );
        foreach ( $fields as $key => $label ) {
            echo '<p><label for="' . esc_attr( $this->get_field_id( $key ) ) . '">' . esc_html( $label ) . '</label><input class="widefat" id="' . esc_attr( $this->get_field_id( $key ) ) . '" name="' . esc_attr( $this->get_field_name( $key ) ) . '" value="' . esc_attr( $v[$key] ) . '" type="' . ( $key === 'count' || $key === 'image_id' ? 'number' : 'text' ) . '"></p>';
        }
        echo '<p><button type="button" class="button pds-section-select-image">Choose image from Media Library</button></p>';
        echo '<p><label>Extra HTML (for static content)</label><textarea class="widefat" rows="4" name="' . esc_attr( $this->get_field_name( 'html' ) ) . '">' . esc_textarea( $v['html'] ) . '</textarea></p>';
        foreach ( array( 'show_image' => 'Show post images', 'show_date' => 'Show dates', 'show_author' => 'Show author', 'show_category' => 'Show categories', 'show_excerpt' => 'Show excerpts', 'show_button' => 'Show button' ) as $key => $label ) {
            echo '<p><label><input type="checkbox" name="' . esc_attr( $this->get_field_name( $key ) ) . '" value="1" ' . checked( ! empty( $v[$key] ), true, false ) . '> ' . esc_html( $label ) . '</label></p>';
        }
        echo '<p><small>Only fields relevant to the selected section affect its output. Post data stays dynamic.</small></p>';
    }
    public function update( $new, $old ) {
        $v = $this->defaults();
        $section = sanitize_key( $new['section'] ?? 'header' );
        $v['section'] = isset( self::$sections[$section] ) ? $section : 'header';
        foreach ( array( 'heading', 'description', 'button_label' ) as $key ) { $v[$key] = sanitize_text_field( $new[$key] ?? '' ); }
        $v['html'] = wp_kses_post( $new['html'] ?? '' );
        foreach ( array( 'button_url', 'image_url' ) as $key ) { $v[$key] = esc_url_raw( $new[$key] ?? '' ); }
        $v['image_id'] = absint( $new['image_id'] ?? 0 );
        $v['count'] = max( 1, min( 12, absint( $new['count'] ?? 3 ) ) );
        foreach ( array( 'show_image', 'show_date', 'show_author', 'show_category', 'show_excerpt', 'show_button' ) as $key ) { $v[$key] = empty( $new[$key] ) ? 0 : 1; }
        return $v;
    }
}
add_action( 'widgets_init', function () { register_widget( 'PDS_Detail_Section_Widget' ); } );

// One-time setup; preserve existing customer widgets and settings.
add_action( 'admin_init', function () {
    if ( get_option( 'pds_detail_sections_seeded_v1' ) ) { return; }
    $map = array(
        'pds-detail-breadcrumb' => 'breadcrumb', 'pds-detail-header' => 'header',
        'pds-detail-before-article' => 'before-article', 'pds-detail-article-content' => 'article-content',
        'pds-detail-after-article' => 'after-article', 'pds-detail-sidebar' => 'sidebar',
        'pds-detail-article-info' => 'article-info', 'pds-detail-recent-news' => 'recent-news',
        'pds-detail-before-related' => 'before-related', 'pds-detail-related-content' => 'related-content',
    );
    $areas = wp_get_sidebars_widgets();
    $instances = get_option( 'widget_pds_detail_section', array() );
    $ids = array_filter( array_keys( $instances ), 'is_numeric' );
    $next = $ids ? max( array_map( 'intval', $ids ) ) + 1 : 2;
    foreach ( $map as $area => $section ) {
        if ( ! empty( $areas[$area] ) ) { continue; }
        $instances[$next] = array( 'section' => $section, 'count' => $section === 'related-content' ? 6 : 3, 'button_label' => $section === 'related-content' ? 'Read More' : 'VIEW ALL NEWS', 'show_image' => 1, 'show_date' => 1, 'show_author' => 1, 'show_category' => 1, 'show_excerpt' => 1, 'show_button' => $section === 'after-article' ? 0 : 1 );
        $areas[$area] = array( 'pds_detail_section-' . $next );
        $next++;
    }
    update_option( 'widget_pds_detail_section', $instances );
    wp_set_sidebars_widgets( $areas );
    update_option( 'pds_detail_sections_seeded_v1', 1 );
}, 35 );

add_action( 'customize_controls_enqueue_scripts', 'pds_section_media_assets' );
add_action( 'admin_enqueue_scripts', function ( $hook ) { if ( $hook === 'widgets.php' ) { pds_section_media_assets(); } } );
function pds_section_media_assets() {
    wp_enqueue_media();
    wp_register_script( 'pds-section-media', false, array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'pds-section-media' );
    wp_add_inline_script( 'pds-section-media', <<<'JS'
jQuery(function($) {
    $(document).on('click', '.pds-section-select-image', function(e) {
        e.preventDefault();
        var wrap = $(this).closest('.widget-content, .widget, .customize-control-widget_form');
        var id = wrap.find('input[name$="[image_id]"]').first();
        var url = wrap.find('input[name$="[image_url]"]').first();
        var frame = wp.media({ title: 'Choose section image', button: { text: 'Use image' }, multiple: false, library: { type: 'image' } });
        frame.on('select', function() {
            var item = frame.state().get('selection').first().toJSON();
            id.val(item.id).trigger('change'); url.val('').trigger('change');
        });
        frame.open();
    });
});
JS
    );
}
