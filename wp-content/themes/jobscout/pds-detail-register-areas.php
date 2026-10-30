<?php
/** Register all News Detail widget areas. Add this code to functions.php once. */
if ( ! function_exists( 'pds_register_news_detail_all_widgets' ) ) {
    function pds_register_news_detail_all_widgets() {
        $areas = array(
            'pds-detail-breadcrumb'      => 'PDS News Detail - Breadcrumb',
            'pds-detail-header'          => 'PDS News Detail - Header',
            'pds-detail-summary'         => 'PDS News Detail - Post Summary',
            'pds-detail-before-article'  => 'PDS News Detail - Before Article',
            'pds-detail-article-content' => 'PDS News Detail - Article Content',
            'pds-detail-after-article'   => 'PDS News Detail - After Article',
            'pds-detail-sidebar'         => 'PDS News Detail - Sidebar',
            'pds-detail-article-info'    => 'PDS News Detail - Article Information',
            'pds-detail-recent-news'     => 'PDS News Detail - Recent News',
            'pds-detail-before-related'  => 'PDS News Detail - Before Related',
            'pds-detail-related-content' => 'PDS News Detail - Related Cards',
        );
        foreach ( $areas as $id => $name ) {
            register_sidebar( array(
                'name'          => $name,
                'id'            => $id,
                'description'   => 'Manage this News Detail area from Appearance > Widgets. Empty areas use default content where available.',
                'before_widget' => '<div id="%1$s" class="pds-detail-widget widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h2 class="pds-detail-widget-title">',
                'after_title'   => '</h2>',
            ) );
        }
    }
    add_action( 'widgets_init', 'pds_register_news_detail_all_widgets' );
}
