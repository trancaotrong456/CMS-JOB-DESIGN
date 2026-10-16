<?php
/**
 * Template Name: News
 * Description: PDS News listing page.
 */

get_header();

$paged = max(
    1,
    (int) get_query_var('paged'),
    (int) get_query_var('page')
);

$news_query = new WP_Query([
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 8,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
]);
?>

<div class="pds-news-page">

    <!-- NEWS HERO -->
    <section class="pds-news-hero"
             aria-labelledby="pds-news-hero-title">

        <h1 id="pds-news-hero-title">
            PDS NEWS
        </h1>

    </section>

    <!-- NEWS CONTENT -->
    <main class="pds-news-main">

        <div class="pds-news-container">

            <h2 class="pds-news-heading">
                NEWEST BLOG ENTRIES
            </h2>

            <?php if ($news_query->have_posts()) : ?>

                <div class="pds-news-grid">

                    <?php while ($news_query->have_posts()) :
                        $news_query->the_post();
                    ?>

                        <article class="pds-news-card">

                            <!-- Featured Image -->
                            <a
                                class="pds-news-card-image"
                                href="<?php echo esc_url(get_permalink()); ?>"
                                aria-label="<?php echo esc_attr(get_the_title()); ?>"
                            >

                                <?php if (has_post_thumbnail()) : ?>

                                    <?php the_post_thumbnail(
                                        'medium_large',
                                        ['class' => 'pds-news-thumbnail']
                                    ); ?>

                                <?php else : ?>

                                    <img
                                        src="<?php echo esc_url(
                                            get_template_directory_uri()
                                            . '/images/banner-image.jpg'
                                        ); ?>"
                                        alt=""
                                        loading="lazy"
                                    >

                                <?php endif; ?>

                            </a>

                            <!-- Card Content -->
                            <div class="pds-news-card-content">

                                <h3 class="pds-news-card-title">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
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
                                    href="<?php echo esc_url(get_permalink()); ?>"
                                >
                                    <?php esc_html_e('Read More', 'jobscout'); ?>
                                </a>

                            </div>

                        </article>

                    <?php endwhile; ?>

                </div>


            <?php else : ?>

                <p class="pds-news-empty">
                    <?php esc_html_e(
                        'No news articles have been published yet.',
                        'jobscout'
                    ); ?>
                </p>

            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        </div>

    </main>

</div>

<?php get_footer(); ?>