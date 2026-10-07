<?php
/**
 * MODULE (8/14): BÌNH LUẬN - Danh sách comment với avatar + Nested Reply + Form Make a Post
 * TÍCH HỢP SQL TRỰC TIẾP QUA $wpdb
 * Hỗ trợ Trả lời (Reply) bình luận lồng nhau theo cấp
 * Vị trí: Cuối bài viết single.php
 * Theme: Root Theme
 */
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

if (post_password_required()) {
    return;
}

$current_post_id = get_the_ID();

// SQL: Lấy danh sách bình luận đã duyệt kèm comment_parent
$raw_comments = $wpdb->get_results($wpdb->prepare("
    SELECT comment_ID, comment_author, comment_author_email, comment_date, comment_content, comment_parent
    FROM {$wpdb->comments}
    WHERE comment_post_ID = %d
      AND comment_approved = '1'
    ORDER BY comment_date ASC
", $current_post_id));

$comments_count = count($raw_comments);

// Phân cấp cây bình luận (Cha và Con)
$parent_comments = array();
$child_comments  = array();

foreach ($raw_comments as $c) {
    $pid = (int) $c->comment_parent;
    if ($pid === 0) {
        $parent_comments[] = $c;
    } else {
        if (!isset($child_comments[$pid])) {
            $child_comments[$pid] = array();
        }
        $child_comments[$pid][] = $c;
    }
}
?>

<section id="comments" class="comments-area tdc-module-8-container">

    <!-- 1. DANH SÁCH BÌNH LUẬN (HIỂN THỊ PHÍA TRÊN) -->
    <?php if ($comments_count > 0) : ?>
        <div class="existing-comments tdc-existing-comments">
            <h3 class="comments-title">
                <i class="fa fa-comments"></i> <span data-i18n="comments_title">Comments</span>
                <span class="tdc-comments-badge"><?php echo esc_html($comments_count); ?></span>
            </h3>

            <ol class="comment-list tdc-threaded-comment-list">
                <?php foreach ($parent_comments as $cmt) : ?>
                    <li class="comment tdc-comment-item" id="comment-<?php echo esc_attr($cmt->comment_ID); ?>">
                        <article class="comment-body tdc-comment-card">
                            <footer class="comment-meta">
                                <div class="comment-author vcard">
                                    <?php echo get_avatar($cmt->comment_author_email, 48, '', '', array('class' => 'avatar-circle')); ?>
                                    <div class="tdc-comment-meta-info">
                                        <b class="fn tdc-comment-author-name"><?php echo esc_html($cmt->comment_author); ?></b>
                                        <time class="tdc-comment-time" datetime="<?php echo esc_attr(date('c', strtotime($cmt->comment_date))); ?>">
                                            <i class="fa fa-clock-o"></i> <?php echo esc_html(date_i18n('d/m/Y H:i', strtotime($cmt->comment_date))); ?>
                                        </time>
                                    </div>
                                </div>
                            </footer>

                            <div class="comment-content tdc-comment-text">
                                <?php echo wpautop(esc_html($cmt->comment_content)); ?>
                            </div>

                            <div class="reply tdc-comment-action-bar">
                                <button type="button" class="tdc-comment-reply-btn" onclick="tdcStartReply(<?php echo (int) $cmt->comment_ID; ?>, '<?php echo esc_js($cmt->comment_author); ?>')">
                                    <i class="fa fa-reply"></i> <span data-i18n="reply_btn">Trả lời</span>
                                </button>
                            </div>
                        </article>

                        <!-- DANH SÁCH BÌNH LUẬN CON (REPLIES) -->
                        <?php if (!empty($child_comments[$cmt->comment_ID])) : ?>
                            <ol class="children tdc-comment-children">
                                <?php foreach ($child_comments[$cmt->comment_ID] as $reply) : ?>
                                    <li class="comment tdc-comment-item tdc-comment-reply-item" id="comment-<?php echo esc_attr($reply->comment_ID); ?>">
                                        <article class="comment-body tdc-comment-card tdc-reply-card">
                                            <footer class="comment-meta">
                                                <div class="comment-author vcard">
                                                    <?php echo get_avatar($reply->comment_author_email, 40, '', '', array('class' => 'avatar-circle')); ?>
                                                    <div class="tdc-comment-meta-info">
                                                        <b class="fn tdc-comment-author-name"><?php echo esc_html($reply->comment_author); ?></b>
                                                        <time class="tdc-comment-time" datetime="<?php echo esc_attr(date('c', strtotime($reply->comment_date))); ?>">
                                                            <i class="fa fa-clock-o"></i> <?php echo esc_html(date_i18n('d/m/Y H:i', strtotime($reply->comment_date))); ?>
                                                        </time>
                                                    </div>
                                                </div>
                                            </footer>

                                            <div class="comment-content tdc-comment-text">
                                                <?php echo wpautop(esc_html($reply->comment_content)); ?>
                                            </div>

                                            <div class="reply tdc-comment-action-bar">
                                                <button type="button" class="tdc-comment-reply-btn" onclick="tdcStartReply(<?php echo (int) $cmt->comment_ID; ?>, '<?php echo esc_js($reply->comment_author); ?>')">
                                                    <i class="fa fa-reply"></i> <span data-i18n="reply_btn">Trả lời</span>
                                                </button>
                                            </div>
                                        </article>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    <?php endif; ?>

    <!-- 2. FORM MAKE A POST (HIỂN THỊ PHÍA DƯỚI) -->
    <div class="tdc-make-post-card-wrapper" id="respond">
        <div class="tdc-make-a-post-card">
            <div class="tdc-make-a-post-tab">
                <i class="fa fa-pencil"></i> <span data-i18n="make_a_post">Make a Post</span>
            </div>

            <?php if (comments_open()) : ?>
                <form action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" method="post" id="commentform" class="tdc-make-post-form">

                    <?php comment_id_fields(); ?>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url(get_permalink()); ?>">

                    <!-- THANH CHỈ BÁO ĐANG TRẢ LỜI COMMENT -->
                    <div id="tdc-replying-indicator" class="tdc-replying-indicator" style="display:none;">
                        <span class="tdc-replying-text">
                            <i class="fa fa-reply"></i> <span data-i18n="replying_to">Đang trả lời:</span>
                            <strong id="tdc-replying-author">@Người dùng</strong>
                        </span>
                        <button type="button" class="tdc-cancel-reply-btn" onclick="tdcCancelReply()" title="Hủy trả lời">
                            &times; <span data-i18n="cancel">Hủy</span>
                        </button>
                    </div>

                    <div class="tdc-make-a-post-body">
                        <textarea
                            id="comment"
                            name="comment"
                            class="tdc-make-a-post-textarea"
                            placeholder="What are you thinking..."
                            data-i18n-placeholder="comment_placeholder"
                            required="required"
                            rows="4"
                        ></textarea>
                    </div>

                    <?php if (!is_user_logged_in()) : ?>
                        <div class="tdc-comment-guest-fields">
                            <input type="text"  name="author" id="author" placeholder="Họ và tên *"          required="required" class="tdc-guest-input">
                            <input type="email" name="email"  id="email"  placeholder="Email *"              required="required" class="tdc-guest-input">
                            <input type="url"   name="url"    id="url"    placeholder="Website (tùy chọn)"   class="tdc-guest-input">
                        </div>
                    <?php else : ?>
                        <div class="tdc-logged-in-info">
                            <?php
                            $current_user = wp_get_current_user();
                            echo '<p><span data-i18n="logged_in_as">Đăng bình luận với tư cách</span> <strong>' . esc_html($current_user->display_name ?: $current_user->user_login) . '</strong>. ';
                            echo '<a href="' . esc_url(wp_logout_url(get_permalink())) . '" data-i18n="logout_question">Đăng xuất?</a></p>';
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="tdc-make-a-post-footer">
                        <button name="submit" type="submit" id="submit" class="tdc-make-post-share-btn">
                            <i class="fa fa-paper-plane-o"></i> <span data-i18n="share_btn">Share</span>
                        </button>
                    </div>

                </form>
            <?php else : ?>
                <p class="tdc-comments-closed" data-i18n="comments_closed">Bình luận đã được đóng cho bài viết này.</p>
            <?php endif; ?>
        </div>
    </div>

</section>

<!-- JAVASCRIPT ĐIỀU KHIỂN REPLY BÌNH LUẬN -->
<script>
function tdcStartReply(commentId, authorName) {
    var parentInput = document.getElementById('comment_parent');
    var indicator   = document.getElementById('tdc-replying-indicator');
    var authorSpan  = document.getElementById('tdc-replying-author');
    var textarea    = document.getElementById('comment');
    var respondBox  = document.getElementById('respond');

    if (parentInput) {
        parentInput.value = commentId;
    }
    if (authorSpan) {
        authorSpan.textContent = '@' + authorName;
    }
    if (indicator) {
        indicator.style.display = 'flex';
    }

    if (respondBox) {
        respondBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    if (textarea) {
        setTimeout(function () {
            textarea.focus();
            textarea.setAttribute('placeholder', 'Trả lời @' + authorName + '...');
        }, 300);
    }
}

function tdcCancelReply() {
    var parentInput = document.getElementById('comment_parent');
    var indicator   = document.getElementById('tdc-replying-indicator');
    var textarea    = document.getElementById('comment');

    if (parentInput) {
        parentInput.value = '0';
    }
    if (indicator) {
        indicator.style.display = 'none';
    }
    if (textarea) {
        textarea.setAttribute('placeholder', 'What are you thinking...');
    }
}
</script>
