<?php
/**
 * Front Page
 *
 * @package JobScout
 */

if ( 'posts' === get_option( 'show_on_front' ) ) {
    // Keep WordPress' configured posts index behavior intact.
    include( get_home_template() );
} else {
    get_header();
    get_template_part( 'template-parts/home', 'content' );
    get_footer();
}
