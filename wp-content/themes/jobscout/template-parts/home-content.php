<?php
/**
 * Purpose-built Home page sections.
 *
 * @package JobScout
 */
$jobs_page_id = absint( get_option( 'job_manager_jobs_page_id' ) );
$jobs_url     = $jobs_page_id ? get_permalink( $jobs_page_id ) : home_url( '/' );
$about_page   = get_page_by_path( 'about' );
$about_url    = $about_page ? get_permalink( $about_page ) : home_url( '/about/' );
$posts_query  = new WP_Query( array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 4,
    'ignore_sticky_posts' => true,
) );
$jobs_query   = new WP_Query( array(
    'post_type'           => 'job_listing',
    'post_status'         => 'publish',
    'posts_per_page'      => 6,
    'ignore_sticky_posts' => true,
) );
?>
<main id="home-content" class="site-content home-page">
    <section class="home-hero" aria-labelledby="home-hero-title">
        <div class="home-container home-hero__inner">
            <div class="home-hero__copy">
                <p class="home-eyebrow"><?php esc_html_e( 'Hospitality careers in Japan', 'jobscout' ); ?></p>
                <h1 id="home-hero-title"><?php esc_html_e( 'Find your dream jobs', 'jobscout' ); ?></h1>
                <p><?php esc_html_e( 'The secret behind our company is simple: to always put ourselves in the other person’s shoes. Discover hospitality roles where thoughtful service makes a difference.', 'jobscout' ); ?></p>
                <?php if ( function_exists( 'jobscout_is_wp_job_manager_activated' ) && jobscout_is_wp_job_manager_activated() ) : ?>
                    <form class="home-job-search" method="get" action="<?php echo esc_url( $jobs_url ); ?>">
                        <label class="home-search-field home-search-keywords">
                            <span class="screen-reader-text"><?php esc_html_e( 'Search jobs, companies, skills', 'jobscout' ); ?></span>
                            <span class="home-search-icon" aria-hidden="true">⌕</span>
                            <input type="search" name="search_keywords" placeholder="<?php esc_attr_e( 'Search for jobs, companies, skills', 'jobscout' ); ?>">
                        </label>
                        <label class="home-search-field home-search-location">
                            <span class="screen-reader-text"><?php esc_html_e( 'Location', 'jobscout' ); ?></span>
                            <span class="home-location-icon" aria-hidden="true">⌖</span>
                            <input type="text" name="search_location" placeholder="<?php esc_attr_e( 'Tokyo', 'jobscout' ); ?>">
                        </label>
                        <button type="submit" class="home-search-submit"><?php esc_html_e( 'Search Job', 'jobscout' ); ?></button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="home-section home-jobs" aria-labelledby="home-jobs-title">
        <div class="home-container">
            <h2 id="home-jobs-title" class="home-section-title"><?php esc_html_e( 'Top Jobs', 'jobscout' ); ?></h2>
            <?php if ( $jobs_query->have_posts() ) : ?>
                <div class="home-job-grid">
                    <?php while ( $jobs_query->have_posts() ) : $jobs_query->the_post();
                        $job_id    = get_the_ID();
                        $job_types = wp_get_post_terms( $job_id, 'job_listing_type', array( 'fields' => 'names' ) );
                        $job_cats  = wp_get_post_terms( $job_id, 'job_listing_category', array( 'fields' => 'names' ) );
                        $location  = get_post_meta( $job_id, '_job_location', true );
                        $logo      = function_exists( 'get_the_company_logo' ) ? get_the_company_logo( $job_id, 'thumbnail' ) : '';
                        $description = get_the_excerpt();
                        ?>
                        <article class="home-job-card">
                            <div class="home-job-card__main">
                                <a class="home-job-card__logo" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'jobscout' ), get_the_title() ) ); ?>">
                                    <?php if ( $logo ) : ?><img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_post_meta( $job_id, '_company_name', true ) ?: get_the_title() ); ?>">
                                    <?php else : ?><span aria-hidden="true"><?php echo esc_html( strtoupper( substr( get_the_title(), 0, 1 ) ) ); ?></span><?php endif; ?>
                                </a>
                                <div class="home-job-card__details">
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <p class="home-job-card__date"><?php echo esc_html( sprintf( __( 'Created: %s', 'jobscout' ), get_the_date( 'M j, Y' ) ) ); ?></p>
                                    <ul class="home-job-meta">
                                        <?php if ( ! is_wp_error( $job_types ) && $job_types ) : ?><li><?php echo esc_html( $job_types[0] ); ?></li><?php endif; ?>
                                        <?php if ( ! is_wp_error( $job_cats ) && $job_cats ) : ?><li><?php echo esc_html( $job_cats[0] ); ?></li><?php endif; ?>
                                        <?php if ( $location ) : ?><li><?php echo esc_html( $location ); ?></li><?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php if ( $description ) : ?><div class="home-job-card__excerpt"><?php echo wp_kses_post( wpautop( $description ) ); ?></div><?php endif; ?>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <p class="home-empty-state"><?php esc_html_e( 'New hospitality opportunities will appear here soon.', 'jobscout' ); ?></p>
            <?php endif; ?>
            <div class="home-button-row"><a class="home-outline-button" href="<?php echo esc_url( $jobs_url ); ?>"><?php esc_html_e( 'View More Jobs', 'jobscout' ); ?></a></div>
        </div>
    </section>

    <section class="home-career" aria-labelledby="home-career-title">
        <div class="home-career__inner">
            <h2 id="home-career-title"><?php esc_html_e( 'Career With Us', 'jobscout' ); ?></h2>
            <p><?php esc_html_e( 'Plan Do See Global is a hospitality group founded in Japan and rooted in “Omotenashi”, the Japanese principle of selfless hospitality. We strive to deliver unforgettable and bespoke experiences, to understand local cultures like natives, and to provide service that is warm but not intrusive, and to foresee our guests’ every need at all times. That is our sole mission and purpose.', 'jobscout' ); ?></p>
            <p><?php esc_html_e( 'We are experts in all stages of project development: concept, design, implementation and management. We love to find unique ways to create experiences that surprise and delight guests and customers.', 'jobscout' ); ?></p>
            <a class="home-light-button" href="<?php echo esc_url( $about_url ); ?>"><?php esc_html_e( 'More About Us', 'jobscout' ); ?></a>
        </div>
    </section>

    <section class="home-section home-blog" aria-labelledby="home-blog-title">
        <div class="home-container">
            <h2 id="home-blog-title" class="home-section-title"><?php esc_html_e( 'Newest Blog Entries', 'jobscout' ); ?></h2>
            <?php if ( $posts_query->have_posts() ) : ?>
                <div class="home-blog-grid">
                    <?php while ( $posts_query->have_posts() ) : $posts_query->the_post(); ?>
                        <article class="home-blog-card">
                            <a class="home-blog-card__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                                <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); } else { ?><span class="home-blog-card__placeholder" aria-hidden="true"></span><?php } ?>
                            </a>
                            <div class="home-blog-card__copy">
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 19, '…' ) ); ?></p>
                                <a class="home-read-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'jobscout' ); ?></a>
                            </div>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <p class="home-empty-state"><?php esc_html_e( 'The latest stories will appear here soon.', 'jobscout' ); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="home-newsletter" aria-labelledby="home-newsletter-title">
        <div class="home-container home-newsletter__inner">
            <h2 id="home-newsletter-title"><?php esc_html_e( 'Subscribe To', 'jobscout' ); ?><br><?php esc_html_e( 'Our Newsletter', 'jobscout' ); ?></h2>
            <form class="home-newsletter-form">
                <label class="screen-reader-text" for="home-newsletter-email"><?php esc_html_e( 'Your email address', 'jobscout' ); ?></label>
                <input id="home-newsletter-email" type="email" name="newsletter_email" placeholder="<?php esc_attr_e( 'Input your email address', 'jobscout' ); ?>" required>
                <button type="button"><?php esc_html_e( 'Subscribe', 'jobscout' ); ?></button>
            </form>
        </div>
    </section>
</main>
