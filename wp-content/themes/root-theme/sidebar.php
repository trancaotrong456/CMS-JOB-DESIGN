<?php
/**
 * The sidebar template
 * Chứa MODULE 9 (Categories), 10 (Recent Posts), 11 (Archive/Xem nhiều), 13 (Fixtures)
 * Tích hợp trực tiếp - không dùng include từ /modules/
 * Theme: Root Theme
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<aside id="secondary" class="site-sidebar home-sidebar tdc-sidebar-area" role="complementary">

    <!-- ================================================
         MODULE (9): CATEGORIES
    ================================================= -->
    <?php
    global $wpdb;
    $tdc_categories = $wpdb->get_results("
        SELECT t.term_id, t.name, t.slug, tt.count
        FROM {$wpdb->terms} t
        INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
        WHERE tt.taxonomy = 'category'
        ORDER BY t.name ASC
    ");
    ?>
    <section class="widget tdc-fit-categories-widget">
        <div class="tdc-fit-categories-box">
            <h3 class="tdc-fit-categories-title" data-i18n="categories_title">Categories</h3>
            <div class="tdc-fit-title-stripe"></div>
            <ul class="tdc-fit-categories-list">
                <?php if (!empty($tdc_categories)) : ?>
                    <?php foreach ($tdc_categories as $tdc_cat) : ?>
                        <li>
                            <span class="tdc-fit-bullet">&#8226;</span>
                            <a href="<?php echo esc_url(get_category_link($tdc_cat->term_id)); ?>" class="tdc-fit-cat-link">
                                <?php echo esc_html($tdc_cat->name); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else : ?>
                    <li><span class="tdc-fit-bullet">&#8226;</span> <span data-i18n="no_categories">Chưa có chuyên mục nào.</span></li>
                <?php endif; ?>
            </ul>
        </div>
    </section>

    <!-- ================================================
         MODULE (10): BÀI VIẾT MỚI NHẤT
    ================================================= -->
    <?php
    $mod10_posts = $wpdb->get_results("
        SELECT ID, post_title, post_date
        FROM {$wpdb->posts}
        WHERE post_type = 'post' AND post_status = 'publish'
        ORDER BY post_date DESC
        LIMIT 5
    ");
    ?>
    <section class="widget tdc-module-10-widget">
        <div class="tdc-fit-recent-box">
            <div class="tdc-fit-recent-header" data-i18n="recent_posts_title">Bài viết mới nhất</div>
            <div class="tdc-fit-recent-list">
                <?php if (!empty($mod10_posts)) : ?>
                    <?php foreach ($mod10_posts as $rp) :
                        $rp_day   = date('d', strtotime($rp->post_date));
                        $rp_month = date('m', strtotime($rp->post_date));
                        $rp_year  = date('y', strtotime($rp->post_date));
                    ?>
                        <article class="tdc-fit-post-item">
                            <div class="tdc-fit-date-box">
                                <div class="tdc-fit-date-top">
                                    <span class="tdc-fit-day"><?php echo esc_html($rp_day); ?></span>
                                    <span class="tdc-fit-dash">-</span>
                                    <span class="tdc-fit-year"><?php echo esc_html($rp_year); ?></span>
                                </div>
                                <div class="tdc-fit-date-bottom">
                                    <span class="tdc-fit-month">T<?php echo esc_html($rp_month); ?></span>
                                </div>
                            </div>
                            <div class="tdc-fit-title-wrap">
                                <a href="<?php echo esc_url(get_permalink($rp->ID)); ?>" class="tdc-fit-post-title">
                                    <?php echo esc_html($rp->post_title); ?>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p style="padding:10px;font-size:13px;" data-i18n="no_recent_posts">Chưa có bài viết nào.</p>
                <?php endif; ?>
            </div>
            <div class="tdc-fit-recent-footer">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="tdc-fit-all-news-btn">
                    <span data-i18n="view_all_news">XEM TẤT CẢ TIN TỨC</span> <i class="fa fa-angle-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ================================================
         MODULE (15): LAST POSTS - LATEST NEWS (BOOTSNIPP xrKXW)
    ================================================= -->
    <?php
    $mod15_sidebar = $wpdb->get_results("
        SELECT ID, post_title, post_date, post_content, post_excerpt
        FROM {$wpdb->posts}
        WHERE post_type = 'post' AND post_status = 'publish'
        ORDER BY post_date DESC
        LIMIT 3
    ");
    ?>
    <section class="widget tdc-module-15-widget">
        <div class="tdc-module-15-box">
            <h3 class="tdc-timeline-main-title" data-i18n="latest_news">Latest News</h3>
            <div class="tdc-timeline-list">
                <?php if (!empty($mod15_sidebar)) : ?>
                    <?php foreach ($mod15_sidebar as $t_post) :
                        $t_date_str = date('j F, Y', strtotime($t_post->post_date));
                        $t_excerpt  = !empty($t_post->post_excerpt) ? $t_post->post_excerpt : $t_post->post_content;
                        $t_excerpt  = wp_trim_words(wp_strip_all_tags($t_excerpt), 18, '...');
                    ?>
                        <article class="tdc-timeline-item">
                            <span class="tdc-timeline-badge" aria-hidden="true"></span>
                            <div class="tdc-timeline-panel">
                                <div class="tdc-timeline-header">
                                    <h4 class="tdc-timeline-title">
                                        <a href="<?php echo esc_url(get_permalink($t_post->ID)); ?>">
                                            <?php echo esc_html($t_post->post_title); ?>
                                        </a>
                                    </h4>
                                    <time class="tdc-timeline-date" datetime="<?php echo esc_attr(date('c', strtotime($t_post->post_date))); ?>">
                                        <?php echo esc_html($t_date_str); ?>
                                    </time>
                                </div>
                                <div class="tdc-timeline-body">
                                    <p><?php echo esc_html($t_excerpt); ?></p>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p style="padding:10px 0;font-size:13px;color:#888;">Chưa có bài viết.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ================================================
         MODULE (11): ARCHIVE / XEM NHIỀU 2 CỘT (VNEXPRESS STYLE)
    ================================================= -->
    <?php
    $mod11_posts = $wpdb->get_results("
        SELECT p.ID, p.post_title, COUNT(c.comment_ID) AS comment_count
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->comments} c ON c.comment_post_ID = p.ID AND c.comment_approved = '1'
        WHERE p.post_type = 'post' AND p.post_status = 'publish'
        GROUP BY p.ID
        ORDER BY comment_count DESC, p.post_date DESC
        LIMIT 8
    ");
    $mod11_total = count($mod11_posts);
    $mod11_half  = (int) ceil($mod11_total / 2);
    $mod11_col1  = array_slice($mod11_posts, 0, $mod11_half);
    $mod11_col2  = array_slice($mod11_posts, $mod11_half);
    ?>
    <section class="widget tdc-module-11-widget">
        <div class="tdc-vnexpress-wrap">
            <div class="tdc-vne-header">
                <h3 class="tdc-vne-title" data-i18n="most_viewed_title">Xem nhiều</h3>
            </div>
            <?php if (!empty($mod11_posts)) : ?>
                <div class="tdc-vne-grid">
                    <!-- Cột 1 -->
                    <div class="tdc-vne-col">
                        <?php foreach ($mod11_col1 as $idx => $vp) :
                            $vnum = $idx + 1;
                        ?>
                            <article class="tdc-vne-item">
                                <span class="tdc-vne-number"><?php echo esc_html($vnum); ?></span>
                                <div class="tdc-vne-content">
                                    <a href="<?php echo esc_url(get_permalink($vp->ID)); ?>" class="tdc-vne-link">
                                        <?php echo esc_html($vp->post_title); ?>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <!-- Cột 2 -->
                    <div class="tdc-vne-col">
                        <?php foreach ($mod11_col2 as $idx => $vp) :
                            $vnum = $mod11_half + $idx + 1;
                        ?>
                            <article class="tdc-vne-item">
                                <span class="tdc-vne-number"><?php echo esc_html($vnum); ?></span>
                                <div class="tdc-vne-content">
                                    <a href="<?php echo esc_url(get_permalink($vp->ID)); ?>" class="tdc-vne-link">
                                        <?php echo esc_html($vp->post_title); ?>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else : ?>
                <p style="padding:10px 0;font-size:13px;color:#888;" data-i18n="no_archive_posts">Chưa có bài viết nào.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ================================================
         MODULE (12): COMMENT LIST ĐƠN GIẢN (SIDEBAR)
    ================================================= -->
    <?php
    $mod12_comments = $wpdb->get_results("
        SELECT c.comment_ID, c.comment_author, c.comment_content, c.comment_post_ID
        FROM {$wpdb->comments} c
        WHERE c.comment_approved = '1'
        ORDER BY c.comment_date DESC
        LIMIT 3
    ");
    ?>
    <section class="widget tdc-module-comments-widget">
        <div class="tdc-sidebar-comments-box">
            <div class="tdc-sidebar-comments-header">
                <h3 class="tdc-sidebar-comments-title">
                    <i class="fa fa-comments-o"></i> <span data-i18n="recent_comments">Comments</span>
                </h3>
                <div class="tdc-sidebar-stripe"></div>
            </div>
            <?php if (!empty($mod12_comments)) : ?>
                <div class="tdc-sidebar-comments-list">
                    <?php foreach ($mod12_comments as $mc) : ?>
                        <article class="tdc-sidebar-comment-item">
                            <a href="<?php echo esc_url(get_comment_link($mc->comment_ID)); ?>" class="tdc-sidebar-comment-link">
                                <span class="comment-author-name"><strong><?php echo esc_html($mc->comment_author); ?></strong>:</span>
                                <?php echo esc_html(wp_trim_words($mc->comment_content, 10, '...')); ?>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="tdc-sidebar-comments-empty" data-i18n="no_comments">Chưa có bình luận nào.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ================================================
         MODULE (13): LỊCH THI ĐẤU & KẾT QUẢ THỂ THAO
    ================================================= -->
    <div class="custom-module-13-fixtures-widget">
        <div class="module-13-header">
            <div class="module-13-title-wrap">
                <i class="fa fa-futbol-o module-13-icon"></i>
                <h4 class="module-13-title" data-i18n="fixtures_title">Lịch Thi Đấu & Kết Quả</h4>
            </div>
            <div class="module-13-live-indicator">
                <span class="pulse-dot"></span> LIVE
            </div>
        </div>

        <!-- TABS -->
        <div class="module-13-tabs">
            <button type="button" class="module-13-tab-btn active" onclick="switchModule13Tab(event,'tab-today')" data-i18n="tab_today">Hôm nay</button>
            <button type="button" class="module-13-tab-btn" onclick="switchModule13Tab(event,'tab-fixtures')" data-i18n="tab_fixtures">Lịch thi đấu</button>
            <button type="button" class="module-13-tab-btn" onclick="switchModule13Tab(event,'tab-results')" data-i18n="tab_results">Kết quả</button>
        </div>

        <!-- TAB 1: HÔM NAY -->
        <div class="module-13-panel active" id="tab-today">
            <div class="module-13-match-row is-live-match">
                <div class="module-13-tournament-tag">
                    <span>Premier League</span>
                    <span class="match-time-tag"><i class="fa fa-clock-o"></i> 78'</span>
                </div>
                <div class="module-13-match-teams">
                    <div class="module-13-team team-home"><span>Arsenal</span><span class="team-score">2</span></div>
                    <div class="module-13-status-center"><span class="badge-live">LIVE 78'</span></div>
                    <div class="module-13-team team-away"><span class="team-score">1</span><span>Chelsea</span></div>
                </div>
            </div>
            <div class="module-13-match-row">
                <div class="module-13-tournament-tag">
                    <span>UEFA Champions League</span>
                    <span class="match-time-tag"><i class="fa fa-clock-o"></i> 21:00</span>
                </div>
                <div class="module-13-match-teams">
                    <div class="module-13-team team-home"><span>Real Madrid</span><span class="team-score">-</span></div>
                    <div class="module-13-status-center"><span class="badge-upcoming">21:00</span></div>
                    <div class="module-13-team team-away"><span class="team-score">-</span><span>Man City</span></div>
                </div>
            </div>
        </div>

        <!-- TAB 2: LỊCH THI ĐẤU -->
        <div class="module-13-panel" id="tab-fixtures">
            <div class="module-13-match-row">
                <div class="module-13-tournament-tag"><span>La Liga</span><span class="match-time-tag">T7 • 22:00</span></div>
                <div class="module-13-match-teams">
                    <div class="module-13-team team-home"><span>Barcelona</span><span class="team-score">-</span></div>
                    <div class="module-13-status-center"><span class="badge-upcoming">22:00</span></div>
                    <div class="module-13-team team-away"><span class="team-score">-</span><span>Atletico</span></div>
                </div>
            </div>
        </div>

        <!-- TAB 3: KẾT QUẢ -->
        <div class="module-13-panel" id="tab-results">
            <div class="module-13-match-row">
                <div class="module-13-tournament-tag"><span>Serie A</span><span class="match-time-tag">FT</span></div>
                <div class="module-13-match-teams">
                    <div class="module-13-team team-home"><span>Juventus</span><span class="team-score">3</span></div>
                    <div class="module-13-status-center"><span class="badge-finished">FT</span></div>
                    <div class="module-13-team team-away"><span class="team-score">1</span><span>AC Milan</span></div>
                </div>
            </div>
        </div>

        <div class="module-13-footer">
            <a href="<?php echo esc_url(home_url('/?s=the-thao')); ?>" class="module-13-all-fixtures-link" data-i18n="all_fixtures">
                <i class="fa fa-calendar"></i> Xem tất cả lịch thi đấu
            </a>
        </div>
    </div>
    <script>
    function switchModule13Tab(e, tabId) {
        var btns = document.querySelectorAll('.module-13-tab-btn');
        var panels = document.querySelectorAll('.module-13-panel');
        btns.forEach(function(b){ b.classList.remove('active'); });
        panels.forEach(function(p){ p.classList.remove('active'); });
        e.currentTarget.classList.add('active');
        var t = document.getElementById(tabId);
        if (t) t.classList.add('active');
    }
    </script>

</aside>
