<?php
/**
 * Template part: Single Job Card for All Jobs listing page
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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

// Custom Company Logo (uploaded via WP Job Manager or featured image)
$logo_url = '';
if ( function_exists( 'get_the_company_logo' ) ) {
    $logo_url = get_the_company_logo( $job_id );
}
if ( empty( $logo_url ) && has_post_thumbnail( $job_id ) ) {
    $logo_url = get_the_post_thumbnail_url( $job_id, 'thumbnail' );
}

// Excerpt / Bullets
$excerpt_raw = get_the_excerpt();
$bullet_items = array();

if ( ! empty( $excerpt_raw ) ) {
    // Check if excerpt contains <li> tags
    if ( preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $excerpt_raw, $matches ) ) {
        $bullet_items = array_map( 'wp_strip_all_tags', $matches[1] );
    } else {
        // Plain text lines or sentences
        $lines = preg_split( '/[\r\n]+/', wp_strip_all_tags( $excerpt_raw ) );
        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( ! empty( $line ) ) {
                $bullet_items[] = $line;
            }
        }
    }
}

// Fallback to post_content if excerpt is empty
if ( empty( $bullet_items ) ) {
    $content_raw = get_the_content();
    if ( preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $content_raw, $matches ) ) {
        $bullet_items = array_slice( array_map( 'wp_strip_all_tags', $matches[1] ), 0, 3 );
    }
}

// Fallback reference bullet lines
if ( empty( $bullet_items ) ) {
    $bullet_items = array(
        __( 'Be responsible for the effective operational management of the hotel', 'jobscout' ),
        __( 'Excellent salary bonuses & recognition activities', 'jobscout' ),
        __( 'Foreign language allowance (up to 500USD/month)', 'jobscout' ),
    );
}
?>

<article class="pds-all-jobs-card" id="job-card-<?php echo esc_attr( $job_id ); ?>">

    <div class="pds-all-jobs-card-header">

        <!-- Logo Box -->
        <a href="<?php echo esc_url( $permalink ); ?>" class="pds-all-jobs-card-logo" aria-label="<?php echo esc_attr( $company ?: $title ); ?>">
            <?php if ( ! empty( $logo_url ) ) : ?>
                <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $company ); ?>" class="pds-all-jobs-logo-img" loading="lazy">
            <?php else : ?>
                <?php
                // Crisp brand logo vector based on company name
                $c_lower = strtolower( (string) $company );
                if ( str_contains( $c_lower, 'sodoh' ) ) :
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
        </a>

        <!-- Info Column -->
        <div class="pds-all-jobs-card-info">
            <h3 class="pds-all-jobs-card-title">
                <a href="<?php echo esc_url( $permalink ); ?>">
                    <?php echo esc_html( $title ); ?>
                </a>
            </h3>

            <div class="pds-all-jobs-card-date">
                <?php echo esc_html( sprintf( __( 'Created: %s', 'jobscout' ), $date_format ) ); ?>
            </div>

            <div class="pds-all-jobs-card-meta-pill">
                <span class="pds-meta-type"><?php echo esc_html( $job_type ); ?></span>
                <span class="pds-meta-sep" aria-hidden="true">|</span>
                <span class="pds-meta-cat"><?php echo esc_html( $job_category ); ?></span>
                <span class="pds-meta-sep" aria-hidden="true">|</span>
                <span class="pds-meta-loc"><?php echo esc_html( $location ); ?></span>
            </div>
        </div>

    </div>

    <!-- Bullets -->
    <?php if ( ! empty( $bullet_items ) ) : ?>
        <div class="pds-all-jobs-card-bullets">
            <ul>
                <?php foreach ( array_slice( $bullet_items, 0, 3 ) as $bullet ) : ?>
                    <li><?php echo esc_html( $bullet ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

</article>
