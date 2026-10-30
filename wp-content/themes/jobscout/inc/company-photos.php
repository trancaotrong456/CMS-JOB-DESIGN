<?php
/**
 * Company Photos Gallery Functionality for Job Listings
 *
 * @package JobScout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Retrieve company gallery photos for a given job listing.
 *
 * @param int $job_id Optional. Post ID of the job listing. Defaults to current post ID.
 * @return array Array of photo items with 'id', 'full', 'large', 'thumb', 'title', 'caption', 'alt'.
 */
function pds_get_company_photos( $job_id = 0 ) {
    if ( ! $job_id ) {
        $job_id = get_the_ID();
    }
    if ( ! $job_id ) {
        return array();
    }

    $raw_meta = get_post_meta( $job_id, '_company_photos', true );

    $attachment_ids = array();
    if ( is_array( $raw_meta ) ) {
        $attachment_ids = array_map( 'absint', $raw_meta );
    } elseif ( is_string( $raw_meta ) && '' !== trim( $raw_meta ) ) {
        $attachment_ids = array_map( 'absint', explode( ',', $raw_meta ) );
    }

    $attachment_ids = array_values( array_unique( array_filter( $attachment_ids ) ) );

    $photos = array();
    foreach ( $attachment_ids as $att_id ) {
        $full_url = wp_get_attachment_image_url( $att_id, 'full' );
        if ( ! $full_url ) {
            continue;
        }

        $large_url = wp_get_attachment_image_url( $att_id, 'large' ) ?: $full_url;
        $thumb_url = wp_get_attachment_image_url( $att_id, 'jobscout-blog' ) ?: ( wp_get_attachment_image_url( $att_id, 'medium' ) ?: $full_url );
        $title     = get_the_title( $att_id ) ?: get_post_meta( $att_id, '_wp_attachment_image_alt', true );
        $caption   = wp_get_attachment_caption( $att_id );
        $alt       = get_post_meta( $att_id, '_wp_attachment_image_alt', true ) ?: $title;

        $photos[] = array(
            'id'      => $att_id,
            'full'    => $full_url,
            'large'   => $large_url,
            'thumb'   => $thumb_url,
            'title'   => $title ?: __( 'Company Photo', 'jobscout' ),
            'caption' => $caption ?: '',
            'alt'     => $alt ?: __( 'Company Photo', 'jobscout' ),
        );
    }

    return $photos;
}

/**
 * Register Company Photos Gallery meta box on job_listing editing screen.
 */
function pds_register_company_photos_metabox() {
    add_meta_box(
        'pds_company_photos_metabox',
        __( 'Company Photos Gallery', 'jobscout' ),
        'pds_render_company_photos_metabox',
        'job_listing',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'pds_register_company_photos_metabox' );

/**
 * Render Company Photos Gallery meta box.
 *
 * @param WP_Post $post Current post object.
 */
function pds_render_company_photos_metabox( $post ) {
    $raw_meta  = get_post_meta( $post->ID, '_company_photos', true );
    $photo_ids = array();
    if ( is_array( $raw_meta ) ) {
        $photo_ids = array_map( 'absint', $raw_meta );
    } elseif ( is_string( $raw_meta ) && '' !== trim( $raw_meta ) ) {
        $photo_ids = array_map( 'absint', explode( ',', $raw_meta ) );
    }
    $photo_ids = array_values( array_unique( array_filter( $photo_ids ) ) );

    wp_nonce_field( 'pds_save_company_photos_action', 'pds_company_photos_nonce' );
    ?>
    <div id="pds-company-photos-metabox-wrapper">
        <p class="description" style="margin-bottom: 12px;">
            <?php esc_html_e( 'Manage the real photos for this company. The first image will be shown as the primary thumbnail on the job detail page, and additional images will be presented in the interactive Lightbox gallery.', 'jobscout' ); ?>
        </p>

        <div id="pds-gallery-preview-container" class="pds-gallery-preview-container">
            <?php foreach ( $photo_ids as $id ) :
                $thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
                if ( ! $thumb ) continue;
            ?>
                <div class="pds-gallery-preview-item" data-id="<?php echo esc_attr( $id ); ?>">
                    <img src="<?php echo esc_url( $thumb ); ?>" alt="" />
                    <button type="button" class="pds-gallery-preview-remove" title="<?php esc_attr_e( 'Remove image', 'jobscout' ); ?>" aria-label="<?php esc_attr_e( 'Remove image', 'jobscout' ); ?>">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>

        <input type="hidden" name="_company_photos" id="pds_company_photos_input" value="<?php echo esc_attr( implode( ',', $photo_ids ) ); ?>">

        <div style="margin-top: 14px; display: flex; gap: 10px; align-items: center;">
            <button type="button" class="button button-primary" id="pds-add-gallery-photos-btn">
                <span class="dashicons dashicons-images-alt2" style="line-height: 1.3; vertical-align: middle;"></span>
                <?php esc_html_e( 'Add / Select Photos from Media Library', 'jobscout' ); ?>
            </button>
            <button type="button" class="button" id="pds-clear-gallery-photos-btn" style="<?php echo empty( $photo_ids ) ? 'display: none;' : ''; ?>">
                <?php esc_html_e( 'Clear All', 'jobscout' ); ?>
            </button>
        </div>
    </div>
    <?php
}

/**
 * Save company photos gallery metadata when saving job_listing post.
 *
 * @param int $post_id Post ID being saved.
 */
function pds_save_company_photos_metabox( $post_id ) {
    if ( ! isset( $_POST['pds_company_photos_nonce'] ) || ! wp_verify_nonce( $_POST['pds_company_photos_nonce'], 'pds_save_company_photos_action' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['_company_photos'] ) ) {
        $raw = sanitize_text_field( wp_unslash( $_POST['_company_photos'] ) );
        if ( '' === trim( $raw ) ) {
            delete_post_meta( $post_id, '_company_photos' );
            delete_post_meta( $post_id, '_cms_reference_company_photo_count' );
        } else {
            $ids = array_values( array_unique( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) ) );
            update_post_meta( $post_id, '_company_photos', $ids );
            update_post_meta( $post_id, '_cms_reference_company_photo_count', count( $ids ) );
        }
    }
}
add_action( 'save_post_job_listing', 'pds_save_company_photos_metabox' );

/**
 * Enqueue scripts and styles for the admin company photos gallery metabox.
 *
 * @param string $hook_suffix Admin page hook.
 */
function pds_enqueue_company_photos_admin_scripts( $hook_suffix ) {
    global $post_type;
    if ( ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) && 'job_listing' === $post_type ) {
        wp_enqueue_media();
        add_action( 'admin_footer', 'pds_render_company_photos_admin_js_css' );
    }
}
add_action( 'admin_enqueue_scripts', 'pds_enqueue_company_photos_admin_scripts' );

/**
 * Output admin script and styles for the gallery metabox.
 */
function pds_render_company_photos_admin_js_css() {
    ?>
    <style>
        .pds-gallery-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            min-height: 86px;
            padding: 12px;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 4px;
            align-items: center;
        }
        .pds-gallery-preview-item {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid #d1d5db;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            flex-shrink: 0;
        }
        .pds-gallery-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .pds-gallery-preview-remove {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(0,0,0,0.65);
            color: #ffffff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            line-height: 1;
            padding: 0;
            transition: background 0.15s ease;
        }
        .pds-gallery-preview-remove:hover {
            background: #ef4444;
        }
    </style>
    <script>
    (function($) {
        $(document).ready(function() {
            var mediaFrame;
            var $container = $('#pds-gallery-preview-container');
            var $input = $('#pds_company_photos_input');
            var $clearBtn = $('#pds-clear-gallery-photos-btn');

            $('#pds-add-gallery-photos-btn').on('click', function(e) {
                e.preventDefault();

                if (mediaFrame) {
                    mediaFrame.open();
                    return;
                }

                mediaFrame = wp.media({
                    title: '<?php echo esc_js( __( 'Select Company Photos', 'jobscout' ) ); ?>',
                    button: {
                        text: '<?php echo esc_js( __( 'Add to Company Gallery', 'jobscout' ) ); ?>'
                    },
                    multiple: true,
                    library: {
                        type: 'image'
                    }
                });

                mediaFrame.on('select', function() {
                    var selection = mediaFrame.state().get('selection');
                    var currentVal = $input.val();
                    var ids = currentVal ? currentVal.split(',').map(function(id) { return parseInt(id.trim(), 10); }).filter(Boolean) : [];

                    selection.each(function(attachment) {
                        var att = attachment.toJSON();
                        if (ids.indexOf(att.id) === -1) {
                            ids.push(att.id);
                            var thumbUrl = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
                            var $item = $('<div class="pds-gallery-preview-item" data-id="' + att.id + '">' +
                                '<img src="' + thumbUrl + '" alt="" />' +
                                '<button type="button" class="pds-gallery-preview-remove" title="<?php echo esc_js( __( 'Remove image', 'jobscout' ) ); ?>">&times;</button>' +
                                '</div>');
                            $container.append($item);
                        }
                    });

                    $input.val(ids.join(','));
                    if (ids.length > 0) {
                        $clearBtn.show();
                    }
                });

                mediaFrame.open();
            });

            // Remove single photo
            $container.on('click', '.pds-gallery-preview-remove', function(e) {
                e.preventDefault();
                var $item = $(this).closest('.pds-gallery-preview-item');
                var idToRemove = parseInt($item.attr('data-id'), 10);
                $item.remove();

                var currentVal = $input.val();
                var ids = currentVal ? currentVal.split(',').map(function(id) { return parseInt(id.trim(), 10); }).filter(Boolean) : [];
                ids = ids.filter(function(id) { return id !== idToRemove; });
                $input.val(ids.join(','));

                if (ids.length === 0) {
                    $clearBtn.hide();
                }
            });

            // Clear all photos
            $clearBtn.on('click', function(e) {
                e.preventDefault();
                if (confirm('<?php echo esc_js( __( 'Remove all photos from this company gallery?', 'jobscout' ) ); ?>')) {
                    $container.empty();
                    $input.val('');
                    $clearBtn.hide();
                }
            });
        });
    })(jQuery);
    </script>
    <?php
}

