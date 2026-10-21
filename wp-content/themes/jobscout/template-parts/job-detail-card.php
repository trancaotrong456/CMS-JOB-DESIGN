<?php
/**
 * Template part: Job Card for "OTHER JOBS" section on Job Detail page
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$card_job_id  = get_the_ID();
$card_title   = get_the_title();
$card_url     = get_permalink();
$card_date    = get_the_date( 'M j, Y' );
$card_company = get_post_meta( $card_job_id, '_company_name', true );
$card_loc     = get_post_meta( $card_job_id, '_job_location', true );
if ( empty( $card_loc ) ) {
    $card_loc = __( 'Ho Chi Minh City', 'jobscout' );
}

// Job Type
$types     = wp_get_post_terms( $card_job_id, 'job_listing_type' );
$card_type = ! empty( $types ) ? $types[0]->name : __( 'Fulltime', 'jobscout' );

// Job Category
$cats     = wp_get_post_terms( $card_job_id, 'job_listing_category' );
$card_cat = ! empty( $cats ) ? $cats[0]->name : __( 'Category Name', 'jobscout' );

// Logo
$logo_url = '';
if ( function_exists( 'get_the_company_logo' ) ) {
    $logo_url = get_the_company_logo( $card_job_id );
}
if ( empty( $logo_url ) && has_post_thumbnail( $card_job_id ) ) {
    $logo_url = get_the_post_thumbnail_url( $card_job_id, 'thumbnail' );
}

// Bullets
$excerpt_raw = get_the_excerpt();
$bullet_items = array();

if ( ! empty( $excerpt_raw ) ) {
    if ( preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $excerpt_raw, $matches ) ) {
        $bullet_items = array_map( 'wp_strip_all_tags', $matches[1] );
    } else {
        $lines = preg_split( '/[\r\n]+/', wp_strip_all_tags( $excerpt_raw ) );
        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( ! empty( $line ) ) {
                $bullet_items[] = $line;
            }
        }
    }
}

if ( empty( $bullet_items ) ) {
    $content_raw = get_the_content();
    if ( preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $content_raw, $matches ) ) {
        $bullet_items = array_slice( array_map( 'wp_strip_all_tags', $matches[1] ), 0, 3 );
    }
}

if ( empty( $bullet_items ) ) {
    $bullet_items = array(
        __( 'Be responsible for the effective operational management of the hotel', 'jobscout' ),
        __( 'Excellent salary bonuses & recognition activities', 'jobscout' ),
        __( 'Foreign language allowance (up to 500USD/month)', 'jobscout' ),
    );
}
?>

<article class="pds-job-detail-other-card" id="job-card-<?php echo esc_attr( $card_job_id ); ?>">

    <div class="pds-job-detail-card-header">

        <!-- Logo Box -->
        <a href="<?php echo esc_url( $card_url ); ?>" class="pds-job-detail-card-logo" aria-label="<?php echo esc_attr( $card_company ?: $card_title ); ?>">
            <?php if ( ! empty( $logo_url ) ) : ?>
                <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $card_company ); ?>" class="pds-job-detail-logo-img" loading="lazy">
            <?php else : ?>
                <?php
                $c_lower = strtolower( (string) $card_company );
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
                        <span class="pds-generic-initial"><?php echo esc_html( mb_substr( $card_company ?: $card_title, 0, 1 ) ); ?></span>
                        <span class="pds-generic-name"><?php echo esc_html( wp_trim_words( $card_company ?: $card_title, 2, '' ) ); ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </a>

        <!-- Info Column -->
        <div class="pds-job-detail-card-info">
            <h3 class="pds-job-detail-card-title">
                <a href="<?php echo esc_url( $card_url ); ?>">
                    <?php echo esc_html( $card_title ); ?>
                </a>
            </h3>

            <div class="pds-job-detail-card-date">
                <?php echo esc_html( sprintf( __( 'Created: %s', 'jobscout' ), $card_date ) ); ?>
            </div>

            <div class="pds-job-detail-card-meta-pill">
                <span class="pds-meta-type"><?php echo esc_html( $card_type ); ?></span>
                <span class="pds-meta-sep" aria-hidden="true">|</span>
                <span class="pds-meta-cat"><?php echo esc_html( $card_cat ); ?></span>
                <span class="pds-meta-sep" aria-hidden="true">|</span>
                <span class="pds-meta-loc"><?php echo esc_html( $card_loc ); ?></span>
            </div>
        </div>

    </div>

    <!-- Bullets -->
    <?php if ( ! empty( $bullet_items ) ) : ?>
        <div class="pds-job-detail-card-bullets">
            <ul>
                <?php foreach ( array_slice( $bullet_items, 0, 3 ) as $bullet ) : ?>
                    <li><?php echo esc_html( $bullet ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

</article>
