
<?php
/**
 * Template Name: News
 * Description: PDS News page with widget areas.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$news_query = new WP_Query(array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 8,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
));
?>

<div class="pds-news-page">

    <!-- HERO -->
    <section class="pds-news-hero" aria-label="News Hero Banner">

        <?php if (is_active_sidebar('pds-news-hero')) : ?>

            <?php dynamic_sidebar('pds-news-hero'); ?>

        <?php else : ?>

            <div class="pds-news-hero-content">
                <h1>PDS NEWS</h1>
            </div>

        <?php endif; ?>

    </section>

    <!-- NEWS CONTENT -->
    <main class="pds-news-main">

        <div class="pds-news-container">

            <?php if (is_active_sidebar('pds-news-before-grid')) : ?>

                <div class="pds-news-before-grid">
                    <?php dynamic_sidebar('pds-news-before-grid'); ?>
                </div>

            <?php else : ?>

                <h2 class="pds-news-heading">
                    NEWEST BLOG ENTRIES
                </h2>

            <?php endif; ?>

            <?php if ($news_query->have_posts()) : ?>

                <div class="pds-news-grid">

                    <?php while ($news_query->have_posts()) :
                        $news_query->the_post();
                    ?>

                        <article class="pds-news-card">

                            <a
                                class="pds-news-card-image"
                                href="<?php the_permalink(); ?>"
                            >
                                <?php if (has_post_thumbnail()) : ?>

                                    <?php the_post_thumbnail(
                                        'medium_large',
                                        array(
                                            'class' => 'pds-news-thumbnail'
                                        )
                                    ); ?>

                                <?php else : ?>

                                    <span class="pds-news-image-placeholder">
                                        NEWS
                                    </span>

                                <?php endif; ?>
                            </a>

                            <div class="pds-news-card-content">

                                <h3 class="pds-news-card-title">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo esc_html(get_the_title()); ?>
                                    </a>
                                </h3>

                                <p class="pds-news-card-excerpt">
                                    <?php
                                    echo esc_html(
                                        wp_trim_words(
                                            get_the_excerpt(),
                                            24,
                                            '...'
                                        )
                                    );
                                    ?>
                                </p>

                                <a
                                    class="pds-news-read-more"
                                    href="<?php the_permalink(); ?>"
                                >
                                    Read More
                                </a>

                            </div>

                        </article>

                    <?php endwhile; ?>

                </div>

            <?php else : ?>

                <p class="pds-news-empty">
                    No news articles have been published yet.
                </p>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

            <!-- AFTER GRID WIDGET -->
            <?php if (is_active_sidebar('pds-news-after-grid')) : ?>

                <div class="pds-news-after-grid">
                    <?php dynamic_sidebar('pds-news-after-grid'); ?>
                </div>

            <?php endif; ?>

        </div>

    </main>

    <!-- NEWSLETTER -->
    <?php if (is_active_sidebar('pds-news-newsletter')) : ?>

        <section class="pds-news-newsletter">
            <?php dynamic_sidebar('pds-news-newsletter'); ?>
        </section>

    <?php else : ?>

        <?php get_template_part('template-parts/pds-newsletter'); ?>

    <?php endif; ?>

</div>

<?php get_footer(); ?>
