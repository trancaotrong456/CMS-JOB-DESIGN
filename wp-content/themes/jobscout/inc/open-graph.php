<?php
/**
 * Open Graph & Social Meta Tags Handler
 *
 * Emits standard Open Graph (og:*) and Twitter Card (twitter:*) meta tags
 * for Job Listings so Facebook, LinkedIn, X, Pinterest, and Reddit
 * scrapers retrieve rich previews (title, company, description, image).
 *
 * @package JobScout
 * @subpackage PDS / Social Sharing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Output Open Graph meta tags in wp_head for Job Listings
 */
function pds_job_social_meta_tags() {
    if ( ! is_singular( 'job_listing' ) ) {
        return;
    }

    // Guard against duplicate tags if a third-party SEO plugin is already active
    if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || function_exists( 'aioseo' ) || defined( 'SEOPRESS_VERSION' ) ) {
        return;
    }

    $job_id    = get_the_ID();
    $title     = get_the_title( $job_id );
    $company   = get_post_meta( $job_id, '_company_name', true );
    $location  = get_post_meta( $job_id, '_job_location', true );
    $permalink = get_permalink( $job_id );

    // Rich Title: "Position Title - Company Name"
    $og_title = $company ? sprintf( '%s - %s', $title, $company ) : $title;

    // Clean excerpt / description
    $raw_desc = get_post_meta( $job_id, '_job_description', true );
    if ( empty( $raw_desc ) ) {
        $post = get_post( $job_id );
        $raw_desc = $post ? ( $post->post_excerpt ?: $post->post_content ) : '';
    }
    $clean_desc = wp_trim_words( wp_strip_all_tags( $raw_desc ), 35, '...' );
    if ( empty( $clean_desc ) ) {
        $loc_text   = $location ? sprintf( ' in %s', $location ) : '';
        $clean_desc = sprintf( __( 'Job opportunity for %s at %s%s. Apply today!', 'jobscout' ), $title, $company ?: get_bloginfo( 'name' ), $loc_text );
    }

    // Determine representative image
    $og_image = '';
    if ( has_post_thumbnail( $job_id ) ) {
        $og_image = get_the_post_thumbnail_url( $job_id, 'large' );
    }

    // Fallback 1: Company photos gallery
    if ( empty( $og_image ) && function_exists( 'pds_get_company_photos' ) ) {
        $photos = pds_get_company_photos( $job_id );
        if ( ! empty( $photos[0]['full'] ) ) {
            $og_image = $photos[0]['full'];
        }
    }

    // Fallback 2: Company Logo (full size, ignore generic placeholder if possible)
    if ( empty( $og_image ) && function_exists( 'get_the_company_logo' ) ) {
        $logo = get_the_company_logo( $job_id, 'full' );
        if ( ! empty( $logo ) && ! str_contains( $logo, 'assets/images/company.png' ) ) {
            $og_image = $logo;
        }
    }

    // Fallback 3: Theme default seed banner if no image attached
    if ( empty( $og_image ) ) {
        $seed_banner = content_url( '/uploads/seed-cms-content/news-restaurant-hotel-operations.png' );
        if ( file_exists( ABSPATH . 'wp-content/uploads/seed-cms-content/news-restaurant-hotel-operations.png' ) ) {
            $og_image = $seed_banner;
        }
    }

    $site_name = get_bloginfo( 'name' );

    echo "\n<!-- PDS Social Open Graph & Twitter Meta Tags -->\n";
    echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '" />' . "\n";
    echo '<meta property="og:type" content="article" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $clean_desc ) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $permalink ) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
    if ( ! empty( $og_image ) ) {
        echo '<meta property="og:image" content="' . esc_url( $og_image ) . '" />' . "\n";
        echo '<meta property="og:image:alt" content="' . esc_attr( $og_title ) . '" />' . "\n";
    }

    // Twitter Cards (Summary with large image)
    echo '<meta name="twitter:card" content="' . ( ! empty( $og_image ) ? 'summary_large_image' : 'summary' ) . '" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $clean_desc ) . '" />' . "\n";
    if ( ! empty( $og_image ) ) {
        echo '<meta name="twitter:image" content="' . esc_url( $og_image ) . '" />' . "\n";
    }
    echo "<!-- / PDS Social Meta Tags -->\n\n";
}
add_action( 'wp_head', 'pds_job_social_meta_tags', 4 );

