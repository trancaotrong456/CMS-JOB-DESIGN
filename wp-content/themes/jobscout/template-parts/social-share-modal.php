<?php
/**
 * Component: Social Media Share Modal Popup
 *
 * Fullscreen overlay modal displaying social media sharing platforms
 * (Facebook, X, Email, Reddit, Pinterest, LinkedIn, WhatsApp)
 * and an interactive Copy Link URL bar with visual feedback.
 *
 * @package    JobScout
 * @subpackage PDS / Social Sharing
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$job_id      = get_the_ID();
$share_url   = get_permalink( $job_id );
$share_title = get_the_title( $job_id );
$company     = get_post_meta( $job_id, '_company_name', true );
$location    = get_post_meta( $job_id, '_job_location', true );

// Representative image for platforms supporting media sharing (Pinterest, etc.)
$share_image = '';
if ( has_post_thumbnail( $job_id ) ) {
    $share_image = get_the_post_thumbnail_url( $job_id, 'large' );
} elseif ( function_exists( 'pds_get_company_photos' ) ) {
    $photos = pds_get_company_photos( $job_id );
    if ( ! empty( $photos[0]['full'] ) ) {
        $share_image = $photos[0]['full'];
    }
}
if ( empty( $share_image ) && function_exists( 'get_the_company_logo' ) ) {
    $logo = get_the_company_logo( $job_id, 'full' );
    if ( ! empty( $logo ) && ! str_contains( $logo, 'assets/images/company.png' ) ) {
        $share_image = $logo;
    }
}
?>

<div id="pds-social-share-modal" class="pds-share-modal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="pds-share-modal-title" tabindex="-1">
    <div class="pds-share-modal-backdrop" data-pds-share-close></div>

    <div class="pds-share-modal-dialog" role="document">
        <div class="pds-share-card">

            <!-- Modal Header -->
            <div class="pds-share-header">
                <h3 id="pds-share-modal-title" class="pds-share-title">
                    <?php esc_html_e( 'Share', 'jobscout' ); ?>
                </h3>
                <button type="button" class="pds-share-close-btn" aria-label="<?php esc_attr_e( 'Close share dialog', 'jobscout' ); ?>" data-pds-share-close>
                    <svg class="pds-share-close-icon" viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Social Media Platforms Carousel -->
            <div class="pds-share-carousel-wrapper">
                <!-- Left Scroll Arrow -->
                <button type="button" class="pds-share-carousel-nav pds-share-carousel-prev is-hidden" aria-label="<?php esc_attr_e( 'Scroll platforms left', 'jobscout' ); ?>">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <!-- Scrollable Track -->
                <div class="pds-share-platforms" role="list">

                    <!-- 1. Facebook -->
                    <button type="button" class="pds-share-item" data-platform="facebook" role="listitem" aria-label="<?php esc_attr_e( 'Share on Facebook', 'jobscout' ); ?>">
                        <span class="pds-share-icon pds-share-icon--facebook">
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </span>
                        <span class="pds-share-label"><?php esc_html_e( 'Facebook', 'jobscout' ); ?></span>
                    </button>

                    <!-- 2. X (Twitter) -->
                    <button type="button" class="pds-share-item" data-platform="x" role="listitem" aria-label="<?php esc_attr_e( 'Share on X', 'jobscout' ); ?>">
                        <span class="pds-share-icon pds-share-icon--x">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </span>
                        <span class="pds-share-label"><?php esc_html_e( 'X', 'jobscout' ); ?></span>
                    </button>

                    <!-- 3. Email -->
                    <button type="button" class="pds-share-item" data-platform="email" role="listitem" aria-label="<?php esc_attr_e( 'Share via Email', 'jobscout' ); ?>">
                        <span class="pds-share-icon pds-share-icon--email">
                            <svg viewBox="0 0 24 24" width="26" height="26" fill="currentColor" aria-hidden="true">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </span>
                        <span class="pds-share-label"><?php esc_html_e( 'Email', 'jobscout' ); ?></span>
                    </button>

                    <!-- 4. Reddit -->
                    <button type="button" class="pds-share-item" data-platform="reddit" role="listitem" aria-label="<?php esc_attr_e( 'Share on Reddit', 'jobscout' ); ?>">
                        <span class="pds-share-icon pds-share-icon--reddit">
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true">
                                <path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.702zM9.25 12C8.56 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.197-2.512-.73a.326.326 0 0 0-.232-.095z"/>
                            </svg>
                        </span>
                        <span class="pds-share-label"><?php esc_html_e( 'Reddit', 'jobscout' ); ?></span>
                    </button>

                    <!-- 5. Pinterest -->
                    <button type="button" class="pds-share-item" data-platform="pinterest" role="listitem" aria-label="<?php esc_attr_e( 'Share on Pinterest', 'jobscout' ); ?>">
                        <span class="pds-share-icon pds-share-icon--pinterest">
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true">
                                <path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/>
                            </svg>
                        </span>
                        <span class="pds-share-label"><?php esc_html_e( 'Pinterest', 'jobscout' ); ?></span>
                    </button>

                    <!-- 6. LinkedIn -->
                    <button type="button" class="pds-share-item" data-platform="linkedin" role="listitem" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'jobscout' ); ?>">
                        <span class="pds-share-icon pds-share-icon--linkedin">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" aria-hidden="true">
                                <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9h2.79v8.37H6.46v-8.37M7.86 6.54a1.62 1.62 0 1 0 0 3.24 1.62 1.62 0 0 0 0-3.24z"/>
                            </svg>
                        </span>
                        <span class="pds-share-label"><?php esc_html_e( 'LinkedIn', 'jobscout' ); ?></span>
                    </button>

                    <!-- 7. WhatsApp -->
                    <button type="button" class="pds-share-item" data-platform="whatsapp" role="listitem" aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'jobscout' ); ?>">
                        <span class="pds-share-icon pds-share-icon--whatsapp">
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true">
                                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.19 8.19 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25.7-.72 1.29-1.37 1.58-.52.23-1.2.33-1.95.12-.51-.15-1.16-.38-2.02-.76-3.56-1.55-5.88-5.15-6.06-5.39-.18-.24-1.44-1.92-1.44-3.66 0-1.74.91-2.6 1.24-2.95.32-.35.7-.44.93-.44.24 0 .47.01.68.02.22.01.52-.08.81.62.3.71 1.02 2.49 1.11 2.67.09.18.15.39.03.63-.12.24-.18.39-.36.6-.18.21-.38.47-.54.63-.18.18-.37.37-.16.73.21.36.93 1.54 2 2.49 1.38 1.23 2.54 1.61 2.9 1.79.36.18.57.15.78-.09.21-.24.9-1.05 1.14-1.41.24-.36.48-.3.81-.18.33.12 2.1 1 2.46 1.17.36.18.6.27.69.42.09.15.09.87-.16 1.57z"/>
                            </svg>
                        </span>
                        <span class="pds-share-label"><?php esc_html_e( 'WhatsApp', 'jobscout' ); ?></span>
                    </button>

                </div>

                <!-- Right Scroll Arrow -->
                <button type="button" class="pds-share-carousel-nav pds-share-carousel-next" aria-label="<?php esc_attr_e( 'Scroll platforms right', 'jobscout' ); ?>">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>

            <!-- URL Box & Copy Button -->
            <div class="pds-share-link-box">
                <input
                    type="text"
                    readonly
                    class="pds-share-link-input"
                    id="pds-share-link-input"
                    value="<?php echo esc_url( $share_url ); ?>"
                    aria-label="<?php esc_attr_e( 'Job URL', 'jobscout' ); ?>"
                    data-share-title="<?php echo esc_attr( $share_title ); ?>"
                    data-share-company="<?php echo esc_attr( $company ); ?>"
                    data-share-image="<?php echo esc_url( $share_image ); ?>"
                />
                <button type="button" class="pds-share-copy-btn" id="pds-share-copy-btn" aria-label="<?php esc_attr_e( 'Copy job link to clipboard', 'jobscout' ); ?>">
                    <?php esc_html_e( 'Copy', 'jobscout' ); ?>
                </button>
            </div>

        </div>
    </div>
</div>
