<?php
/**
 * Module 3: Footer chuẩn Bootsnipp rIXdE (Sunlimetech)
 * Vị trí: Chân trang toàn bộ website
 */
if (!defined('ABSPATH')) {
    exit;
}

$site_name = get_bloginfo('name') ?: 'Root Theme';

// Fetch data
$comments = get_comments(array('number' => 5, 'status' => 'approve'));
$categories = get_categories(array('number' => 5, 'hide_empty' => false));
$recent_posts = get_posts(array('numberposts' => 5, 'post_status' => 'publish'));
?>

<footer id="footer" class="site-footer" role="contentinfo">
    <div class="footer-container">
        <div class="footer-row">
            <!-- Cột 1: COMMENTS -->
            <div class="footer-col">
                <h5>COMMENT</h5>
                <ul class="quick-links">
                    <?php if (!empty($comments)) : ?>
                        <?php foreach ($comments as $comment) : ?>
                            <li><a href="<?php echo esc_url(get_comment_link($comment)); ?>"><i class="fa fa-angle-double-right"></i><?php echo esc_html($comment->comment_author . ': ' . wp_trim_words($comment->comment_content, 5)); ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a href="#"><i class="fa fa-angle-double-right"></i>No comments</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Cột 2: CATEGORIES -->
            <div class="footer-col">
                <h5>CATEGORIES</h5>
                <ul class="quick-links">
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $cat) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                                    <i class="fa fa-angle-double-right"></i><?php echo esc_html($cat->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li><a href="#"><i class="fa fa-angle-double-right"></i>No categories</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Cột 3: LAST POSTS -->
            <div class="footer-col">
                <h5>LAST POSTS</h5>
                <ul class="quick-links">
                    <?php if (!empty($recent_posts)) : ?>
                        <?php foreach ($recent_posts as $p) : ?>
                            <li><a href="<?php echo esc_url(get_permalink($p->ID)); ?>"><i class="fa fa-angle-double-right"></i><?php echo esc_html(wp_trim_words($p->post_title, 8)); ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a href="#"><i class="fa fa-angle-double-right"></i>No posts</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Social row Bootsnipp rIXdE -->
        <div class="footer-social-row">
            <ul class="social">
                <li><a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa fa-facebook"></i></a></li>
                <li><a href="https://twitter.com" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fa fa-twitter"></i></a></li>
                <li><a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa fa-instagram"></i></a></li>
                <li><a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fa fa-youtube-play"></i></a></li>
                <li><a href="mailto:<?php echo esc_attr(get_option('admin_email')); ?>" aria-label="Email"><i class="fa fa-envelope"></i></a></li>
            </ul>
        </div>

        <hr class="footer-divider" />

        <!-- Copyright row -->
        <div class="footer-copyright">
            <p>© <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($site_name); ?>. <span data-i18n="footer_rights">Tất cả quyền được bảo lưu.</span></p>
            <p class="copyright-author"><span data-i18n="footer_design_by">Thiết kế chuẩn</span> <a href="https://bootsnipp.com/snippets/rIXdE" target="_blank" rel="noopener">Bootsnipp rIXdE</a> <span data-i18n="by">bởi</span> <a href="<?php echo esc_url(home_url('/')); ?>">Group C & D</a></p>
        </div>
    </div>
</footer>
