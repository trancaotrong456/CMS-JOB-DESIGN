<?php
/**
 * Purpose-built Home page sections.
 *
 * @package JobScout
 */
$jobs_page_id = absint( get_option( 'job_manager_jobs_page_id' ) );
$jobs_page    = get_page_by_path( 'jobs' );
$jobs_url     = $jobs_page_id ? get_permalink( $jobs_page_id ) : ( $jobs_page ? get_permalink( $jobs_page ) : home_url( '/jobs/' ) );
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
    <?php if ( is_active_sidebar( 'home-hero-search' ) ) : ?>
        <section class="home-hero home-hero--widget" aria-labelledby="home-hero-title">
            <div class="home-container home-hero__inner">
                <?php dynamic_sidebar( 'home-hero-search' ); ?>
            </div>
        </section>
    <?php else : ?>
        <section class="home-hero" aria-labelledby="home-hero-title">
            <div class="home-container home-hero__inner">
                <div class="home-hero__copy">
                    <h1 id="home-hero-title"><?php esc_html_e( 'FIND YOUR DREAM JOBS', 'jobscout' ); ?></h1>
                    <p><?php esc_html_e( 'The secret behind our company is simple: to always put ourselves in the other person’s shoes - employees, guests or customers. This allows us to see the world through their eyes, anticipate their needs and better understand their feelings.', 'jobscout' ); ?></p>
                    <form class="home-job-search" method="get" action="<?php echo esc_url( $jobs_url ); ?>">
                        <label class="home-search-field home-search-keywords">
                            <span class="screen-reader-text"><?php esc_html_e( 'Search jobs, companies, skills', 'jobscout' ); ?></span>
                            <span class="home-search-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f2761b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </span>
                            <input type="search" name="search_keywords" placeholder="<?php esc_attr_e( 'Search for jobs, companies, skills', 'jobscout' ); ?>">
                        </label>
                        <label class="home-search-field home-search-location">
                            <span class="screen-reader-text"><?php esc_html_e( 'Location', 'jobscout' ); ?></span>
                            <span class="home-location-icon" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f2761b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </span>
                            <select name="search_location" class="home-search-location-select" style="width: 100%; height: 100%; border: 0; outline: 0; color: #3b3837; font-size: 14px; background: transparent; cursor: pointer; -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 18px;">
                                <option value="Tokyo"><?php esc_html_e( 'Tokyo', 'jobscout' ); ?></option>
                                <option value="Kyoto"><?php esc_html_e( 'Kyoto', 'jobscout' ); ?></option>
                                <option value="Osaka"><?php esc_html_e( 'Osaka', 'jobscout' ); ?></option>
                                <option value=""><?php esc_html_e( 'All Locations', 'jobscout' ); ?></option>
                            </select>
                            <span class="home-dropdown-arrow" style="pointer-events: none; margin-left: -12px; color: #696461; font-size: 10px;">▼</span>
                        </label>
                        <button type="submit" class="home-search-submit"><?php esc_html_e( 'SEARCH JOB', 'jobscout' ); ?></button>
                    </form>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ( is_active_sidebar( 'home-top-jobs' ) ) : ?>
        <section class="home-section home-jobs home-jobs--widget" aria-labelledby="home-jobs-title">
            <div class="home-container">
                <?php dynamic_sidebar( 'home-top-jobs' ); ?>
            </div>
        </section>
    <?php else : ?>
        <section class="home-section home-jobs" aria-labelledby="home-jobs-title">
            <div class="home-container">
                <h2 id="home-jobs-title" class="home-section-title"><?php esc_html_e( 'TOP JOBS', 'jobscout' ); ?></h2>
                <?php if ( $jobs_query->have_posts() ) : ?>
                    <div class="home-job-grid pds-all-jobs-grid">
                        <?php while ( $jobs_query->have_posts() ) : $jobs_query->the_post();
                            get_template_part( 'template-parts/all-jobs-card' );
                        endwhile; wp_reset_postdata(); ?>
                    </div>
                <?php else : ?>
                    <p class="home-empty-state"><?php esc_html_e( 'New hospitality opportunities will appear here soon.', 'jobscout' ); ?></p>
                <?php endif; ?>
                <div class="home-button-row"><a class="home-outline-button" href="<?php echo esc_url( $jobs_url ); ?>"><?php esc_html_e( 'VIEW MORE JOBS', 'jobscout' ); ?></a></div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ( is_active_sidebar( 'home-career-cta' ) ) : ?>
        <section class="home-career home-career--widget" aria-labelledby="home-career-title">
            <div class="home-career__inner">
                <?php dynamic_sidebar( 'home-career-cta' ); ?>
            </div>
        </section>
    <?php else : ?>
        <section class="home-career" aria-labelledby="home-career-title">
            <div class="home-career__inner">
                <h2 id="home-career-title"><?php esc_html_e( 'CAREER WITH US', 'jobscout' ); ?></h2>
                <p><?php esc_html_e( 'Plan Do See Global is a hospitality group founded in Japan and rooted in “Omotenashi”, the Japanese principle of selfless hospitality. We strive to deliver unforgettable and bespoke experiences, to understand local cultures like natives, and to provide service that is warm but not intrusive, and to foresee our guests’ every need at all times. That is our sole mission and purpose.', 'jobscout' ); ?></p>
                <p><?php esc_html_e( 'We are experts in all stages of project development: concept, design, implementation and management. We love to find unique ways to create experiences that surprise and delight guests and customers.', 'jobscout' ); ?></p>
                <a class="home-light-button" href="<?php echo esc_url( $about_url ); ?>"><?php esc_html_e( 'MORE ABOUT US', 'jobscout' ); ?></a>
            </div>
        </section>
    <?php endif; ?>

    <?php if ( is_active_sidebar( 'home-blog-entries' ) ) : ?>
        <section class="home-section home-blog home-blog--widget" aria-labelledby="home-blog-title">
            <div class="home-container">
                <?php dynamic_sidebar( 'home-blog-entries' ); ?>
            </div>
        </section>
    <?php else : ?>
        <section class="home-section home-blog" aria-labelledby="home-blog-title">
            <div class="home-container">
                <h2 id="home-blog-title" class="home-section-title"><?php esc_html_e( 'NEWEST BLOG ENTRIES', 'jobscout' ); ?></h2>
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
    <?php endif; ?>

    <!-- NEWSLETTER BANNER -->
    <?php get_template_part( 'template-parts/pds-newsletter' ); ?>
</main>
