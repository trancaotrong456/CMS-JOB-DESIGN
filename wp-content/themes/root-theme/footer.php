<?php
/**
 * The template for displaying the footer
 * MODULE (3) - FOOTER 3 CỘT: COMMENT / CATEGORIES / LAST POSTS
 * Theme: Root Theme
 */
if (!defined('ABSPATH')) {
    exit;
}

// --- Fetch data cho footer ---
$footer_comments = get_comments(array(
    'number'  => 5,
    'status'  => 'approve',
    'orderby' => 'comment_date',
    'order'   => 'DESC',
));
$footer_categories = get_categories(array(
    'number'     => 5,
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
));
$footer_recent_posts = get_posts(array(
    'numberposts' => 5,
    'post_status' => 'publish',
    'orderby'     => 'date',
    'order'       => 'DESC',
));
$site_name = get_bloginfo('name') ?: 'Root Theme';
?>

<!-- MODULE (3) - FOOTER CHUẨN BOOTSNIPP rIXdE (SUNLIMETECH) -->
<footer id="footer" class="site-footer" role="contentinfo">
    <div class="footer-container">
        <div class="footer-row">

            <!-- Cột 1: COMMENT -->
            <div class="footer-col">
                <h5>COMMENT</h5>
                <ul class="quick-links">
                    <?php if (!empty($footer_comments)) : ?>
                        <?php foreach ($footer_comments as $fc) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_comment_link($fc->comment_ID)); ?>">
                                    <i class="fa fa-angle-double-right"></i>
                                    <?php echo esc_html($fc->comment_author . ': ' . wp_trim_words($fc->comment_content, 5, '...')); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li><i class="fa fa-angle-double-right"></i> Chưa có bình luận.</li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Cột 2: CATEGORIES -->
            <div class="footer-col">
                <h5>CATEGORIES</h5>
                <ul class="quick-links">
                    <?php if (!empty($footer_categories)) : ?>
                        <?php foreach ($footer_categories as $fc_cat) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_category_link($fc_cat->term_id)); ?>">
                                    <i class="fa fa-angle-double-right"></i>
                                    <?php echo esc_html($fc_cat->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li><i class="fa fa-angle-double-right"></i> Chưa có danh mục.</li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Cột 3: LAST POSTS -->
            <div class="footer-col">
                <h5>LAST POSTS</h5>
                <ul class="quick-links">
                    <?php if (!empty($footer_recent_posts)) : ?>
                        <?php foreach ($footer_recent_posts as $fp) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_permalink($fp->ID)); ?>">
                                    <i class="fa fa-angle-double-right"></i>
                                    <?php echo esc_html(wp_trim_words($fp->post_title, 8, '...')); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li><i class="fa fa-angle-double-right"></i> Chưa có bài viết.</li>
                    <?php endif; ?>
                </ul>
            </div>

        </div><!-- /.footer-row -->

        <!-- Social row Bootsnipp rIXdE -->
        <div class="footer-social-row">
            <a href="#" class="footer-social-icon" title="Facebook" aria-label="Facebook"><i class="fa fa-facebook"></i></a>
            <a href="#" class="footer-social-icon" title="Twitter" aria-label="Twitter"><i class="fa fa-twitter"></i></a>
            <a href="#" class="footer-social-icon" title="Instagram" aria-label="Instagram"><i class="fa fa-instagram"></i></a>
            <a href="#" class="footer-social-icon" title="Google+" aria-label="Google+"><i class="fa fa-google-plus"></i></a>
            <a href="#" class="footer-social-icon" title="Email" aria-label="Email"><i class="fa fa-envelope"></i></a>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom-bar">
            <p class="footer-bottom-text">
                <a href="http://www.sunlimetech.com" title="Design by Sunlimetech" target="_blank">National Transaction Corporation</a>
                is a Registered MSP/ISO of Elavon, Inc. Georgia (a wholly owned subsidiary of U.S. Bancorp, Minneapolis, MN)
            </p>
            <p class="footer-copyright">
                &copy; All right Reserved. Sunlimetech
            </p>
        </div>

    </div><!-- /.footer-container -->
</footer>

<?php wp_footer(); ?>

</body>
</html>
