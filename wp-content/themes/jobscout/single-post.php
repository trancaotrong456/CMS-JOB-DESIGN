<?php
/**
 * News detail for standard WordPress posts.
 * Uses the active JobScout header and footer without modifying either.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

wp_enqueue_style(
    'pds-news-detail',
    get_template_directory_uri() . '/assets/css/news-detail.css',
    array(),
    '1.0.0'
);

get_header();
?>
<div class="pds-news-detail-page">
    <div class="pds-news-detail-container">
        <?php while ( have_posts() ) : the_post();
            $current_post_id = get_the_ID();
            $categories = get_the_category();
            $category_ids = wp_list_pluck( $categories, 'term_id' );
            $news_page = get_page_by_path( 'news' );
            $news_url = $news_page ? get_permalink( $news_page ) : home_url( '/news/' );
            $share_url = get_permalink();
        ?>
        <nav class="pds-news-detail-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
            <span aria-hidden="true">/</span>
            <a href="<?php echo esc_url( $news_url ); ?>">News</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">News Detail</span>
        </nav>

        <article <?php post_class( 'pds-news-detail-article' ); ?>>
            <header class="pds-news-detail-summary">
                <div class="pds-news-detail-cover">
                    <?php if ( has_post_thumbnail() ) :
                        the_post_thumbnail( 'medium', array( 'alt' => esc_attr( get_the_title() ) ) );
                    else : ?>
                        <span class="pds-news-detail-placeholder">PDS NEWS</span>
                    <?php endif; ?>
                </div>
                <div class="pds-news-detail-summary-text">
                    <h1><?php echo esc_html( get_the_title() ); ?></h1>
                    <p class="pds-news-detail-date">Published: <?php echo esc_html( get_the_date( 'M d, Y' ) ); ?></p>
                    <div class="pds-news-detail-tags">
                        <span><?php echo esc_html( get_the_author() ); ?></span>
                        <?php foreach ( array_slice( $categories, 0, 2 ) as $category ) : ?>
                            <span><?php echo esc_html( $category->name ); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="pds-news-detail-actions">
                    <button type="button" class="pds-news-detail-share" data-share-url="<?php echo esc_url( $share_url ); ?>">SHARE</button>
                    <a class="pds-news-detail-back" href="<?php echo esc_url( $news_url ); ?>">BACK TO NEWS</a>
                </div>
            </header>

            <div class="pds-news-detail-columns">
                <div class="pds-news-detail-content">
                    <h2>Article Overview</h2>
                    <div class="pds-news-detail-entry">
                        <?php the_content();
                        wp_link_pages( array( 'before' => '<div class="pds-news-detail-page-links">Pages: ', 'after' => '</div>' ) ); ?>
                    </div>
                </div>
                <aside class="pds-news-detail-sidebar" aria-label="News sidebar">
                    <section class="pds-news-detail-side-card">
                        <h2>Article Information</h2>
                        <dl>
                            <dt>Published</dt><dd><?php echo esc_html( get_the_date( 'M d, Y' ) ); ?></dd>
                            <dt>Author</dt><dd><?php echo esc_html( get_the_author() ); ?></dd>
                            <dt>Category</dt><dd><?php echo esc_html( $categories ? implode( ', ', wp_list_pluck( $categories, 'name' ) ) : 'News' ); ?></dd>
                        </dl>
                    </section>
                    <section class="pds-news-detail-side-card">
                        <h2>Recent News</h2>
                        <?php
                        $recent_news = new WP_Query( array(
                            'post_type' => 'post', 'post_status' => 'publish',
                            'posts_per_page' => 3, 'post__not_in' => array( $current_post_id ),
                            'ignore_sticky_posts' => true, 'no_found_rows' => true,
                        ) );
                        if ( $recent_news->have_posts() ) : ?>
                            <div class="pds-news-detail-recent-list">
                                <?php while ( $recent_news->have_posts() ) : $recent_news->the_post(); ?>
                                    <a class="pds-news-detail-recent-item" href="<?php the_permalink(); ?>">
                                        <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'thumbnail' );
                                        else : ?><span class="pds-news-detail-mini-placeholder">NEWS</span><?php endif; ?>
                                        <span><?php echo esc_html( get_the_title() ); ?></span>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                        <?php else : ?><p>No recent articles.</p><?php endif;
                        wp_reset_postdata(); ?>
                    </section>
                </aside>
            </div>
        </article>

        <?php
        $related_args = array(
            'post_type' => 'post', 'post_status' => 'publish',
            'posts_per_page' => 6, 'post__not_in' => array( $current_post_id ),
            'ignore_sticky_posts' => true, 'no_found_rows' => true,
        );
        if ( $category_ids ) { $related_args['category__in'] = $category_ids; }
        $related = new WP_Query( $related_args );
        $related_ids = wp_list_pluck( $related->posts, 'ID' );
        if ( $related->post_count < 6 ) {
            $extra = new WP_Query( array(
                'post_type' => 'post', 'post_status' => 'publish',
                'posts_per_page' => 6 - $related->post_count,
                'post__not_in' => array_merge( array( $current_post_id ), $related_ids ),
                'ignore_sticky_posts' => true, 'no_found_rows' => true,
            ) );
            $related_posts = array_merge( $related->posts, $extra->posts );
        } else { $related_posts = $related->posts; }
        ?>
        <?php if ( $related_posts ) : ?>
            <section class="pds-news-detail-related" aria-labelledby="pds-news-detail-related-title">
                <h2 id="pds-news-detail-related-title">NEWEST BLOG ENTRIES</h2>
                <div class="pds-news-detail-related-grid">
                    <?php foreach ( $related_posts as $related_post ) :
                        $related_id = $related_post->ID;
                        $related_categories = get_the_category( $related_id ); ?>
                        <article class="pds-news-detail-related-card">
                            <a class="pds-news-detail-related-image" href="<?php echo esc_url( get_permalink( $related_id ) ); ?>">
                                <?php if ( has_post_thumbnail( $related_id ) ) :
                                    echo get_the_post_thumbnail( $related_id, 'thumbnail', array( 'alt' => esc_attr( get_the_title( $related_id ) ) ) );
                                else : ?><span class="pds-news-detail-placeholder">NEWS</span><?php endif; ?>
                            </a>
                            <div class="pds-news-detail-related-body">
                                <h3><a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>"><?php echo esc_html( get_the_title( $related_id ) ); ?></a></h3>
                                <p class="pds-news-detail-related-date">Published: <?php echo esc_html( get_the_date( 'M d, Y', $related_id ) ); ?></p>
                                <p class="pds-news-detail-related-meta"><?php echo esc_html( $related_categories ? $related_categories[0]->name : 'News' ); ?></p>
                                <p class="pds-news-detail-related-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $related_id ), 18, '…' ) ); ?></p>
                                <a class="pds-news-read-more"
                                    href="<?php echo esc_url(get_permalink()); ?>">
                                    Read More
                                </a>
                            </div>
                            
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif;
        wp_reset_postdata();
        endwhile; ?>
    </div>
</div>
<script>
(function () {
    var btn = document.querySelector('.pds-news-detail-share');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var url = btn.getAttribute('data-share-url');
        if (navigator.share) {
            navigator.share({ title: document.title, url: url }).catch(function () {});
        } else if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(function () {
                btn.textContent = 'COPIED';
            }).catch(function () { window.prompt('Copy article URL:', url); });
        } else {
            window.prompt('Copy article URL:', url);
        }
    });
})();
</script>
<?php get_footer(); ?>
