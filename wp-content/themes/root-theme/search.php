<?php
/**
 * Template for Search Page (Module 4: Search Form & Module 5: Search Results)
 * THIẾT KẾ THEO SƠ ĐỒ ẢNH 3 (TRANG TÌM KIẾM - 3 CỘT + MODULE 15 BÊN DƯỚI):
 * Trên: Search (4) - Banner tìm kiếm lớn
 * Cột trái: Module 13 (Lịch thi đấu / thể thao - rớt dòng 1 bài 1 dòng)
 * Cột giữa: Search result (5) (Kết quả tìm kiếm FIT TDC)
 * Cột phải: Module 14 (Comments)
 * Dưới 3 cột: Module 15 (Latest News timeline xrKXW nằm ngang)
 * Chân trang: Footer
 */
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main class="search-page tdc-search-container" role="main">
    <div class="search-layout-wrapper">

        <!-- =========================================================
             MODULE (4): SEARCH FORM BANNER (NẰM NGANG PHÍA TRÊN)
        ========================================================== -->
        <section class="tdc-search-top-banner">
            <div class="tdc-search-form-banner">
                <form class="tdc-large-search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>" role="search">
                    <div class="tdc-search-input-wrap">
                        <span class="tdc-search-lens-icon"><i class="fa fa-search"></i></span>
                        <input
                            type="search"
                            name="s"
                            class="tdc-search-banner-input"
                            placeholder="Tìm kiếm chủ đề, tin tức, từ khóa..."
                            data-i18n-placeholder="search_banner_placeholder"
                            value="<?php echo esc_attr(get_search_query()); ?>"
                            required
                        >
                    </div>
                    <button type="submit" class="tdc-search-banner-btn" data-i18n="search_btn">
                        Tìm kiếm
                    </button>
                </form>
            </div>
        </section>

        <!-- =========================================================
             HÀNG 3 CỘT (THEO ĐÚNG SƠ ĐỒ THIẾT KẾ ẢNH 3):
             Cột trái: Module 13 (rớt dòng 1 bài/dòng)
             Cột giữa: Search result (5)
             Cột phải: Module 14 (Comments)
        ========================================================== -->
        <div class="tdc-3cols-layout search-3cols-row">

            <!-- 1. CỘT TRÁI: MODULE 13 (LỊCH THI ĐẤU / THỂ THAO - 1 BÀI 1 DÒNG) -->
            <aside class="tdc-col-left search-col-mod13" role="complementary" aria-label="Tiện ích thể thao">
                <?php root_theme_render_module_13_fixtures(true); ?>
                <?php root_theme_render_module_9_categories(); ?>
            </aside>

            <!-- 2. CỘT GIỮA: SEARCH RESULT (MODULE 5) -->
            <section class="tdc-col-center search-col-results">
                <?php if (have_posts()) : ?>

                    <div class="search-result-header tdc-has-results-header">
                        <h1 class="tdc-search-title">
                            <span data-i18n="search_results_for">Kết quả tìm kiếm cho:</span> <span>"<?php echo esc_html(get_search_query()); ?>"</span>
                        </h1>
                        <p class="tdc-search-count">
                            <span data-i18n="found">Tìm thấy</span> <?php global $wp_query; echo esc_html($wp_query->found_posts); ?> <span data-i18n="matching_posts">bài viết phù hợp.</span>
                        </p>
                    </div>

                    <div class="search-results tdc-search-results-list">
                        <?php while (have_posts()) : the_post(); ?>
                            <?php
                            $post_date  = get_the_date('d', get_the_ID());
                            $post_month = get_the_date('m', get_the_ID());
                            ?>
                            <article class="search-result-card tdc-search-item-card">

                                <!-- 1. HÌNH ẢNH BÊN TRÁI -->
                                <div class="result-image tdc-search-thumb-box">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium', array('class' => 'tdc-search-thumbnail')); ?>
                                        </a>
                                    <?php else : ?>
                                        <a href="<?php the_permalink(); ?>" class="tdc-search-no-thumb">
                                            <div class="tdc-search-thumb-placeholder">
                                                <i class="fa fa-newspaper-o"></i>
                                                <span>FIT TDC</span>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- 2. Ô NGÀY THÁNG Ở GIỮA -->
                                <div class="result-date tdc-search-date-box">
                                    <div class="result-day tdc-search-day">
                                        <?php echo esc_html($post_date); ?>
                                    </div>
                                    <div class="result-month tdc-search-month">
                                        THÁNG <?php echo esc_html($post_month); ?>
                                    </div>
                                </div>

                                <!-- 3. TIÊU ĐỀ & NỘI DUNG BÊN PHẢI -->
                                <div class="result-content tdc-search-content-box">
                                    <h2 class="tdc-search-item-title">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>

                                    <p class="tdc-search-item-excerpt">
                                        <?php
                                        echo esc_html(wp_trim_words(
                                            get_the_excerpt(),
                                            25,
                                            '[...]'
                                        ));
                                        ?>
                                    </p>
                                </div>

                            </article>
                        <?php endwhile; ?>

                        <!-- Phân trang tìm kiếm -->
                        <div class="home-pagination tdc-pagination">
                            <?php
                            the_posts_pagination(array(
                                'mid_size'  => 2,
                                'prev_text' => '<i class="fa fa-angle-left"></i> Previous',
                                'next_text' => 'Next <i class="fa fa-angle-right"></i>',
                            ));
                            ?>
                        </div>

                    </div>

                <?php else : ?>

                    <div class="tdc-search-not-found-wrapper">
                        <div class="tdc-search-not-found-header">
                            <h1 class="tdc-search-keyword-highlight">
                                Search: <span>"<?php echo esc_html(get_search_query()); ?>"</span>
                            </h1>
                            <p class="tdc-search-not-found-msg" data-i18n="search_not_found_msg">
                                Chúng tôi không tìm thấy kết quả nào phù hợp với từ khóa của bạn. Hãy thử tìm với từ khóa khác!
                            </p>
                        </div>
                    </div>

                <?php endif; ?>
            </section>

            <!-- 3. CỘT PHẢI: MODULE 14 (COMMENTS GẦN ĐÂY) -->
            <aside class="tdc-col-right search-col-mod14" role="complementary" aria-label="Bình luận và xem nhiều">
                <?php root_theme_render_module_12_comments(5); ?>
                <?php root_theme_render_module_10_recent_posts(4); ?>
            </aside>

        </div><!-- /.search-3cols-row -->

        <!-- =========================================================
             MODULE (15): LATEST NEWS TIMELINE (NẰM NGANG DƯỚI 3 CỘT)
        ========================================================== -->
        <div class="search-bottom-module15">
            <?php root_theme_render_module_15_latest_news(4); ?>
        </div>

    </div><!-- /.search-layout-wrapper -->
</main>

<?php get_footer(); ?>
