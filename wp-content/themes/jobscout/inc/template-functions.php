<?php
/**
 * JobScout Template Functions which enhance the theme by hooking into WordPress
 *
 * @package JobScout
 */

if( ! function_exists( 'jobscout_doctype' ) ) :
/**
 * Doctype Declaration
*/
function jobscout_doctype(){ ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <?php
}
endif;
add_action( 'jobscout_doctype', 'jobscout_doctype' );

if( ! function_exists( 'jobscout_head' ) ) :
/**
 * Before wp_head 
*/
function jobscout_head(){ ?>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php
}
endif;
add_action( 'jobscout_before_wp_head', 'jobscout_head' );

if( ! function_exists( 'jobscout_responsive_header' ) ) :
/**
 * Responsive Header
*/
function jobscout_responsive_header(){ 
    $post_job_label  = get_theme_mod( 'post_job_label', __( 'Post Jobs', 'jobscout' ) );
    $post_job_url = jobscout_get_submit_job_url();
    ?>
    <div class="responsive-nav">
        <div class="nav-top">
            <?php jobscout_site_branding( true ); ?>
        </div>


        <nav id="mobile-site-navigation" class="main-navigation mobile-navigation">        
            <div class="primary-menu-list main-menu-modal cover-modal" data-modal-target-string=".main-menu-modal">
                <button class="close close-main-nav-toggle" data-toggle-target=".main-menu-modal" data-toggle-body-class="showing-main-menu-modal" aria-expanded="false" data-set-focus=".main-menu-modal"></button>
                <div class="mobile-menu" aria-label="<?php esc_attr_e( 'Mobile', 'jobscout' ); ?>">
                    <?php jobscout_primary_menu_fallback( 'mobile-primary-menu' ); ?>
                
                    <?php if( $post_job_label || $post_job_url ){ ?>
                        <div class="btn-wrap">
                            <a class="btn" href="<?php echo esc_url( $post_job_url ) ?>"><?php echo esc_html( $post_job_label ) ?></a>
                        </div>
                    <?php } ?>
               </div>
            </div>
        </nav><!-- #mobile-site-navigation -->
    </div> <!-- .responsive-nav -->
    <?php
}
endif;
add_action( 'jobscout_before_header', 'jobscout_responsive_header', 15 );

if( ! function_exists( 'jobscout_page_start' ) ) :
/**
 * Page Start
*/
function jobscout_page_start(){ ?>
    <div id="page" class="site">
        <a class="skip-link screen-reader-text" href="#acc-content"><?php esc_html_e( 'Skip to content (Press Enter)', 'jobscout' ); ?></a>
    <?php
}
endif;
add_action( 'jobscout_before_header', 'jobscout_page_start', 20 );

if( ! function_exists( 'jobscout_header' ) ) :
/**
 * Header Start
*/
function jobscout_header(){ 
    ?>
    <header id="masthead" class="site-header header-one" itemscope itemtype="https://schema.org/WPHeader">
        <?php if( has_nav_menu( 'secondary' ) || current_user_can( 'manage_options' ) ) jobscout_secondary_navigation(); ?>
        <div class="header-main">
            <div class="container">
                <?php 
                    jobscout_site_branding( false );
                    echo '<div class="menu-wrap">';
                    jobscout_primary_nagivation();
                    echo '</div><!-- .menu-wrap -->';
                ?>
            </div>
        </div> <!-- .header-main -->
    </header> <!-- .site-header -->
    <?php
}
endif;
add_action( 'jobscout_header', 'jobscout_header', 20 );

if( ! function_exists( 'jobscout_breadcrumbs_bar' ) ) :
    /**
     * Breadcrumbs
    */
    function jobscout_breadcrumbs_bar(){
        $ed_breadcrumbs = get_theme_mod( 'ed_breadcrumbs', false );

        if( $ed_breadcrumbs && ! is_front_page() && ! is_404() ){ ?>
            <section class="breadcrumb-wrap">
                <div class="container">
                    <?php jobscout_breadcrumbs_cb(); //Breadcrumb ?>
                </div>
            </section>   
            <?php 
        }    
    }
endif;
add_action( 'jobscout_after_header', 'jobscout_breadcrumbs_bar', 30 );

if( ! function_exists( 'jobscout_content_start' ) ) :
/**
 * Content Start
 *  
*/
function jobscout_content_start(){       
    echo '<div id="acc-content"><!-- .site-header -->';
    $home_sections = jobscout_get_home_sections(); 
    if( ! ( is_front_page() && ! is_home() && $home_sections ) ){ //Make necessary adjust for pg template.
        echo is_404() ? '<div class="error-holder">' : '<div id="content" class="site-content">'; 

        if( is_archive() || is_search() || is_page_template( 'templates/portfolio.php' ) ) : ?>
            <header class="page-header">
                <?php
                    if( is_archive() ){ 
                        if( is_author() ) { 
                            $author_title = get_the_author(); ?>
                            <div class="author-bio">
                                <figure class="author-img"><?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?></figure>
                                <div class="author-content">
                                    <?php 
                                        echo '<span class="sub-title">' . esc_html__( 'All Posts by', 'jobscout' ) . '</span>';
                                        if( $author_title ) echo '<h1 class="author-title">' . esc_html( $author_title ) . '</h3>';
                                    ?>      
                                </div>
                            </div>
                        <?php }else{
                            the_archive_title( '<h1 class="page-title">', '</h1>' );
                            the_archive_description( '<div class="archive-description">', '</div>' );             
                        }
                    }
                    
                    if( is_search() ){ 
                        echo '<div class="container">';
                            echo '<h1 class="page-title">' . esc_html__( 'Search', 'jobscout' ) . '</h1>';
                            get_search_form();
                        echo '</div><!-- .container -->';
                    }

                    if( ! is_author() && ! is_search() ){
                        jobscout_posts_per_page_count();
                    }

                    if( is_page_template( 'templates/portfolio.php' ) ){
                        global $post;
                        echo '<div class="container">';
                            echo '<h1 class="page-title">' . esc_html( get_the_title( $post->ID ), 'jobscout' ) . '</h1>';
                            if( $post->post_content ) echo wpautop( wp_kses_post( $post->post_content ) );
                        echo '</div><!-- .container -->';
                    }
                ?>
            </header>
        <?php endif; 
            if( is_singular( 'job_listing' ) ){
                global $post;
                if ( is_active_sidebar( 'job-detail-hero' ) ) {
                    dynamic_sidebar( 'job-detail-hero' );
                } else {
                    $banner_image   = get_header_image();
                    $show_banner    = get_theme_mod( 'ed_job_banner', true );

                    if( $banner_image && $show_banner ){
                        $banner_style = 'background-image: url(' . esc_url( $banner_image ) . '); background-size: cover;';
                        echo '<header class="entry-header" style="'. esc_attr( $banner_style ) .'"></header>';
                    }
                }
            } 
        ?>
        <div class="container">
        <?php 
    }
}
endif;
add_action( 'jobscout_content', 'jobscout_content_start' );

if ( ! function_exists( 'jobscout_post_thumbnail' ) ) :
/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 */
function jobscout_post_thumbnail() {
    $image_size  = 'thumbnail';
    $ed_featured = get_theme_mod( 'ed_featured_image', true );
    $sidebar     = jobscout_sidebar_layout();
    
    if( is_home() || is_archive() || is_search() ){        
        $image_size = 'jobscout-blog';    
        if( has_post_thumbnail() ){                        
            echo '<figure class="post-thumbnail"><a href="' . esc_url( get_permalink() ) . '">';
                the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );    
            echo '</a></figure>';
        }else{
            echo '<figure class="post-thumbnail">';
                jobscout_fallback_svg_image( $image_size );  
            echo '</figure>';  
        }        
    }elseif( is_singular() ){
        $image_size = ( $sidebar ) ? 'jobscout-single' : 'jobscout-single-fullwidth';
        if( is_single() ){
            if( $ed_featured && has_post_thumbnail() ){
                echo '<figure class="post-thumbnail">';
                the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );
                echo '</figure>';
            }
        }else{
            echo '<figure class="post-thumbnail">';
            the_post_thumbnail( $image_size, array( 'itemprop' => 'image' ) );
            echo '</figure>';
        }
    }
}
endif;
add_action( 'jobscout_before_post_entry_content', 'jobscout_post_thumbnail', 15 );
add_action( 'jobscout_before_page_entry_content', 'jobscout_post_thumbnail', 15 );
add_action( 'jobscout_before_single_post_entry_content', 'jobscout_post_thumbnail', 15 );

if( ! function_exists( 'jobscout_entry_header' ) ) :
/**
 * Entry Header
*/
function jobscout_entry_header(){ ?>
    <header class="entry-header">
        <?php 
            $ed_cat_single = get_theme_mod( 'ed_category', false );
            $hide_author   = get_theme_mod( 'ed_post_author', false );
            $hide_date     = get_theme_mod( 'ed_post_date', false );

            if( is_single() ){
                if( ! $ed_cat_single ) jobscout_category();
            }else{
                if( 'post' === get_post_type() ){
                    echo '<div class="entry-meta">';
                    if( ! $hide_author ) jobscout_posted_by();
                    if( ! $hide_date ) jobscout_posted_on();
                    echo '</div>';
                }
            }

            if ( is_singular() ) :
                the_title( '<h1 class="entry-title">', '</h1>' );
            else :
                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
            endif;        
        ?>
    </header>         
    <?php    
}
endif;
add_action( 'jobscout_post_entry_content', 'jobscout_entry_header', 10 );
add_action( 'jobscout_before_page_entry_content', 'jobscout_entry_header', 10 );
add_action( 'jobscout_before_single_post_entry_content', 'jobscout_entry_header', 10 );

if( ! function_exists( 'jobscout_entry_content' ) ) :
/**
 * Entry Content
*/
function jobscout_entry_content(){ 
    $ed_excerpt = get_theme_mod( 'ed_excerpt', true ); ?>
    <div class="entry-content" itemprop="text">
		<?php
			if( is_singular() || ! $ed_excerpt || ( get_post_format() != false ) ){
                the_content();    
    			wp_link_pages( array(
    				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'jobscout' ),
    				'after'  => '</div>',
    			) );
            }else{
                the_excerpt();
            }
		?>
	</div><!-- .entry-content -->
    <?php
}
endif;
add_action( 'jobscout_post_entry_content', 'jobscout_entry_content', 15 );
add_action( 'jobscout_page_entry_content', 'jobscout_entry_content', 15 );
add_action( 'jobscout_single_post_entry_content', 'jobscout_entry_content', 15 );
add_action( 'jobscout_single_post_entry_content', 'jobscout_entry_content', 15 );
add_action( 'jobscout_before_single_job_content', 'jobscout_entry_content', 15 );

if( ! function_exists( 'jobscout_entry_footer' ) ) :
/**
 * Entry Footer
*/
function jobscout_entry_footer(){ 
    $readmore = get_theme_mod( 'read_more_text', __( 'Read More', 'jobscout' ) );
    $ed_post_date   = get_theme_mod( 'ed_post_date', false ); ?>
	<footer class="entry-footer">
		<?php
			if( is_single() ){
			    jobscout_tag();
			}
            
            if( is_front_page() || is_home() || is_search() || is_archive() ){
                echo '<a href="' . esc_url( get_the_permalink() ) . '" class="readmore-link"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16.207 8.58"><defs><style>.c{fill:none;stroke:#2ace5e;}</style></defs><g transform="translate(-701.5 -958.173)"><path class="c" d="M-9326.909-9204.917l-3.937,3.937,3.937,3.937" transform="translate(-8613.846 -8238.518) rotate(180)"/><line class="c" x2="15.154" transform="translate(701.5 962.426)"/></g></svg>' . esc_html( $readmore ) . '</a>';    
            }

            if( is_single() ) echo '<div class="entry-footer-right">';
            if( 'post' === get_post_type() && is_single() ){
                if( ! $ed_post_date ) jobscout_posted_on( true );
                jobscout_comment_count();
            }
            
            if( get_edit_post_link() ){
                edit_post_link(
                    sprintf(
                        wp_kses(
                            /* translators: %s: Name of current post. Only visible to screen readers */
                            __( 'Edit <span class="screen-reader-text">%s</span>', 'jobscout' ),
                            array(
                                'span' => array(
                                    'class' => array(),
                                ),
                            )
                        ),
                        get_the_title()
                    ),
                    '<span class="edit-link">',
                    '</span>'
                );
            }
            if( is_single() ) echo '</div>';
		?>
	</footer><!-- .entry-footer -->
	<?php 
}
endif;
add_action( 'jobscout_post_entry_content', 'jobscout_entry_footer', 20 );
add_action( 'jobscout_page_entry_content', 'jobscout_entry_footer', 20 );
add_action( 'jobscout_single_post_entry_content', 'jobscout_entry_footer', 20 );

if( ! function_exists( 'jobscout_get_single_job_title' ) ) :
/**
 * Before wp_head 
*/
function jobscout_get_single_job_title(){ 
    ?>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php
            if ( get_option( 'job_manager_enable_types' ) ) { 
                echo '<div class="job-type">';
                    $types = wpjm_get_the_job_types(); 
                    if ( ! empty( $types ) ) : foreach ( $types as $type ) : ?>
                        <span class="btn <?php echo esc_attr( sanitize_title( $type->slug ) ); ?>"><?php echo esc_html( $type->name ); ?></span>
                    <?php endforeach; endif;
                echo '</div>';
            } 
        ?>
    </header>
    <?php
}
endif;
add_action( 'jobscout_before_single_job_content', 'jobscout_get_single_job_title' );

if( ! function_exists( 'jobscout_navigation' ) ) :
/**
 * Navigation
*/
function jobscout_navigation(){
    if( is_single() ){
        $previous = get_previous_post_link(
    		'<div class="nav-previous nav-holder">%link</div>',
    		'<span class="meta-nav">' . esc_html__( 'Previous Article', 'jobscout' ) . '</span><span class="post-title">%title</span>',
    		false,
    		'',
    		'category'
    	);
    
    	$next = get_next_post_link(
    		'<div class="nav-next nav-holder">%link</div>',
    		'<span class="meta-nav">' . esc_html__( 'Next Article', 'jobscout' ) . '</span><span class="post-title">%title</span>',
    		false,
    		'',
    		'category'
    	); 
        
        if( $previous || $next ){?>            
            <nav class="navigation post-navigation" role="navigation">
    			<h2 class="screen-reader-text"><?php esc_html_e( 'Post Navigation', 'jobscout' ); ?></h2>
    			<div class="nav-links">
    				<?php
                        if( $previous ) echo $previous;
                        if( $next ) echo $next;
                    ?>
    			</div>
    		</nav>        
            <?php
        }
    }else{
        the_posts_navigation();
    }
}
endif;
add_action( 'jobscout_after_post_content', 'jobscout_navigation', 10 );
add_action( 'jobscout_after_posts_content', 'jobscout_navigation' );

if( ! function_exists( 'jobscout_author' ) ) :
/**
 * Author Section
*/
function jobscout_author(){ 
    $ed_author    = get_theme_mod( 'ed_author', false );
    $author_title = get_theme_mod( 'author_title', __( 'About Author', 'jobscout' ) );
    if( ! $ed_author && get_the_author_meta( 'description' ) ){ ?>
    <div class="author-bio">
        <?php if( $author_title ) echo '<h3 class="title">' . esc_html( $author_title ) . '</h3>'; ?>
        <div class="author-bio-inner">
            <figure class="author-img"><?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?></figure>
            <div class="author-content">
                <?php echo '<div class="author-info">' . wpautop( wp_kses_post( get_the_author_meta( 'description' ) ) ) . '</div>';
                ?>		
            </div>
		</div>
	</div>
    <?php
    }
}
endif;
add_action( 'jobscout_after_post_content', 'jobscout_author', 20 );

if( ! function_exists( 'jobscout_comment' ) ) :
/**
 * Comments Template 
*/
function jobscout_comment(){
    // If comments are open or we have at least one comment, load up the comment template.
	if( get_theme_mod( 'ed_comments', true ) && ( comments_open() || get_comments_number() ) ) :
		comments_template();
	endif;
}
endif;
add_action( 'jobscout_after_post_content', 'jobscout_comment', 30 );
add_action( 'jobscout_after_page_content', 'jobscout_comment' );

if( ! function_exists( 'jobscout_content_end' ) ) :
/**
 * Content End
*/
function jobscout_content_end(){ 
    $home_sections = jobscout_get_home_sections(); 
    if( ! ( is_front_page() && ! is_home() && $home_sections ) ){ ?>            
        </div><!-- .container/ -->        
    </div><!-- .error-holder/site-content -->
    <?php
    }
}
endif;
add_action( 'jobscout_before_footer', 'jobscout_content_end', 20 );

if( ! function_exists( 'jobscout_footer_start' ) ) :
/**
 * Footer Start
*/
function jobscout_footer_start(){
    ?>
    <footer id="colophon" class="site-footer" itemscope itemtype="https://schema.org/WPFooter">
    <?php
}
endif;
add_action( 'jobscout_footer', 'jobscout_footer_start', 20 );

if( ! function_exists( 'jobscout_footer_top' ) ) :
/**
 * Footer Top
*/
function jobscout_footer_top(){    
    $footer_sidebars = array( 'footer-one', 'footer-two', 'footer-three', 'footer-four' );
    $active_sidebars = array();
    $sidebar_count   = 0;
    
    foreach ( $footer_sidebars as $sidebar ) {
        if( is_active_sidebar( $sidebar ) ){
            array_push( $active_sidebars, $sidebar );
            $sidebar_count++ ;
        }
    }
                 
    if( $active_sidebars ){ ?>
        <div class="footer-t">
    		<div class="container">
    			<div class="grid column-<?php echo esc_attr( $sidebar_count ); ?>">
                <?php foreach( $active_sidebars as $active ){ ?>
    				<div class="col">
    				   <?php dynamic_sidebar( $active ); ?>	
    				</div>
                <?php } ?>
                </div>
    		</div>
    	</div>
        <?php 
    }
}
endif;
add_action( 'jobscout_footer', 'jobscout_footer_top', 30 );

if( ! function_exists( 'jobscout_footer_bottom' ) ) :
/**
 * Footer Bottom
*/
function jobscout_footer_bottom(){ ?>
    <div class="home-footer-main">
        <div class="container">
            <?php if ( is_active_sidebar( 'footer-bottom' ) ) : ?>
                <div class="footer-bottom-sidebar">
                    <?php dynamic_sidebar( 'footer-bottom' ); ?>
                </div>
            <?php else : ?>
                <div class="home-footer-brand"><?php jobscout_site_branding( true ); ?></div>
                <nav class="home-footer-navigation" aria-label="<?php esc_attr_e( 'Footer navigation', 'jobscout' ); ?>">
                    <?php wp_nav_menu( array( 'theme_location' => 'footer', 'menu_id' => 'footer-menu', 'menu_class' => 'home-footer-menu', 'container' => false, 'fallback_cb' => 'jobscout_footer_menu_fallback' ) ); ?>
                </nav>
                <div class="home-footer-social" aria-label="<?php esc_attr_e( 'Social links', 'jobscout' ); ?>">
                    <?php
                    $home_social_links = array( 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn' );
                    $has_home_social_link = false;
                    foreach ( $home_social_links as $key => $label ) :
                        $social_url = get_theme_mod( $key . '_link', '' );
                        if ( $social_url ) : $has_home_social_link = true; ?>
                            <a class="home-social--<?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( $social_url ); ?>" rel="noopener noreferrer" target="_blank" aria-label="<?php echo esc_attr( $label ); ?>"><?php echo esc_html( 'facebook' === $key ? 'f' : strtoupper( substr( $label, 0, 1 ) ) ); ?></a>
                        <?php endif;
                    endforeach;
                    if ( ! $has_home_social_link ) : ?>
                        <span class="home-social--facebook" aria-hidden="true">f</span>
                        <span class="home-social--google" aria-hidden="true">G</span>
                        <span class="home-social--line" aria-hidden="true">LINE</span>
                        <span class="home-social--twitter" aria-hidden="true">t</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer-b">
		<div class="container">
            <?php 
                if ( function_exists( 'the_privacy_policy_link' )  )  the_privacy_policy_link( '<div class="privacy-block">', '</div>' );
                if ( is_active_sidebar( 'footer-copyright' ) ) :
                    dynamic_sidebar( 'footer-copyright' );
                else :
            ?>
			<div class="copyright">            
            <?php
                echo '&copy; ' . esc_html( gmdate( 'Y' ) ) . ' Plan Do See Global. All Rights Reserved.';
            ?>               
            </div>
            <?php endif; ?>
		</div>
	</div>
    <?php
}
endif;
add_action( 'jobscout_footer', 'jobscout_footer_bottom', 40 );

if( ! function_exists( 'jobscout_footer_end' ) ) :
/**
 * Footer End 
*/
function jobscout_footer_end(){ ?>
    </footer><!-- #colophon -->
    <?php
}
endif;
add_action( 'jobscout_footer', 'jobscout_footer_end', 50 );

if( ! function_exists( 'jobscout_page_end' ) ) :
/**
 * Page End
*/
function jobscout_page_end(){ ?>
    </div><!-- #acc-content -->
    </div><!-- #page -->
    <?php
}
endif;
add_action( 'jobscout_after_footer', 'jobscout_page_end', 20 );

/**
 * =============================================================================
 * HỆ THỐNG TRUY VẤN VÀ TÌM KIẾM THÔNG MINH VIỆC LÀM (SMART JOB SEARCH ENGINE)
 * =============================================================================
 *
 * @package           JobScout
 * @subpackage        PDS / Core Backend Query & Search
 * @author            Trần Cao Trọng (Lead) & Trần Văn Trọng (Jobs)
 * @git_branch        tran-cao-trong/home & tran-van-trong/all-jobs
 *
 * -----------------------------------------------------------------------------
 * 1. THUẬT TOÁN TÌM KIẾM THÔNG MINH & TRỌNG SỐ LIÊN QUAN (RELEVANCE ALGORITHM):
 * -----------------------------------------------------------------------------
 * - Mở rộng phạm vi tìm kiếm sang:
 *   + Tiêu đề công việc (post_title), nội dung (post_content), tóm tắt (post_excerpt).
 *   + Tên công ty tuyển dụng qua postmeta (_company_name).
 *   + Taxonomies việc làm: job_listing_type (Fulltime, Part-time), job_listing_category, job_listing_tag.
 * - Xử lý thông minh biến thể & từ khóa viết cách:
 *   + Tự động đồng bộ các dạng: "full time" ⇄ "fulltime" ⇄ "full-time", "part time" ⇄ "parttime".
 *   + Ánh xạ từ đồng nghĩa tiếng Việt sang tiếng Anh chuẩn chuyên ngành:
 *     "khách sạn" / "khach san" ➔ "hotel"
 *     "quản lý" / "quan ly" ➔ "manager"
 *     "đầu bếp" / "dau bep" ➔ "chef", "cook"
 * - Thuật toán xếp hạng ưu tiên (Relevance Ordering SQL):
 *   + Mức 0 (Cao nhất): Khớp trong post_title hoặc _company_name.
 *   + Mức 1 (Ưu tiên tiếp theo): Khớp trong Taxonomy job_listing_type hoặc job_listing_category.
 *   + Mức 2: Khớp trong mô tả nội dung post_content hoặc post_excerpt.
 * =============================================================================
 */
if ( ! function_exists( 'pds_filter_job_listing_search' ) ) :
function pds_filter_job_listing_search( $clauses, $query ) {
    $keywords  = $query->get( 'pds_job_keywords' );
    $post_type = $query->get( 'post_type' );
    $is_job    = ( 'job_listing' === $post_type || ( is_array( $post_type ) && in_array( 'job_listing', $post_type, true ) ) );

    if ( ! empty( $keywords ) && $is_job ) {
        global $wpdb;

        // Sinh danh sách các biến thể từ khóa thông minh
        $variants = array( $keywords );
        $lower_kw = mb_strtolower( trim( $keywords ) );

        // Xử lý biến thể Full time / Fulltime / Full-time
        if ( false !== strpos( $lower_kw, 'full time' ) || false !== strpos( $lower_kw, 'full-time' ) || false !== strpos( $lower_kw, 'fulltime' ) ) {
            $variants[] = 'full time';
            $variants[] = 'fulltime';
            $variants[] = 'full-time';
        }
        // Xử lý biến thể Part time / Parttime / Part-time
        if ( false !== strpos( $lower_kw, 'part time' ) || false !== strpos( $lower_kw, 'part-time' ) || false !== strpos( $lower_kw, 'parttime' ) ) {
            $variants[] = 'part time';
            $variants[] = 'parttime';
            $variants[] = 'part-time';
        }
        // Ánh xạ đồng nghĩa thông dụng ngành hospitality
        if ( 'khach san' === $lower_kw || 'khách sạn' === $lower_kw ) {
            $variants[] = 'hotel';
        }
        if ( 'quan ly' === $lower_kw || 'quản lý' === $lower_kw ) {
            $variants[] = 'manager';
        }
        if ( 'dau bep' === $lower_kw || 'đầu bếp' === $lower_kw ) {
            $variants[] = 'chef';
            $variants[] = 'cook';
        }
        $variants = array_values( array_unique( $variants ) );

        // Nối bảng postmeta để lấy tên công ty và địa điểm
        $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS pds_comp ON ({$wpdb->posts}.ID = pds_comp.post_id AND pds_comp.meta_key = '_company_name') ";
        $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS pds_loc ON ({$wpdb->posts}.ID = pds_loc.post_id AND pds_loc.meta_key = '_job_location') ";

        // Nối các bảng taxonomy (job_listing_type, job_listing_category, job_listing_tag)
        $clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} AS pds_tr ON ({$wpdb->posts}.ID = pds_tr.object_id) ";
        $clauses['join'] .= " LEFT JOIN {$wpdb->term_taxonomy} AS pds_tt ON (pds_tr.term_taxonomy_id = pds_tt.term_taxonomy_id AND pds_tt.taxonomy IN ('job_listing_type', 'job_listing_category', 'job_listing_tag')) ";
        $clauses['join'] .= " LEFT JOIN {$wpdb->terms} AS pds_terms ON (pds_tt.term_id = pds_terms.term_id) ";

        // Xây dựng điều kiện WHERE cho tất cả các biến thể
        $where_conditions = array();
        foreach ( $variants as $v ) {
            $kw = '%' . $wpdb->esc_like( $v ) . '%';
            $where_conditions[] = $wpdb->prepare(
                "({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_content LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR pds_comp.meta_value LIKE %s OR pds_loc.meta_value LIKE %s OR pds_terms.name LIKE %s OR pds_terms.slug LIKE %s)",
                $kw, $kw, $kw, $kw, $kw, $kw, $kw
            );
        }
        $clauses['where'] .= ' AND (' . implode( ' OR ', $where_conditions ) . ') ';

        // Relevance ordering:
        // Mức 0: Khớp trong post_title hoặc _company_name (ưu tiên cao nhất)
        // Mức 1: Khớp trong địa điểm _job_location
        // Mức 2: Khớp trong taxonomy (job type, category)
        // Mức 3: Khớp trong post_content hoặc post_excerpt
        $title_conditions = array();
        $loc_conditions   = array();
        $term_conditions  = array();
        foreach ( $variants as $v ) {
            $kw_v = '%' . $wpdb->esc_like( $v ) . '%';
            $title_conditions[] = $wpdb->prepare( "({$wpdb->posts}.post_title LIKE %s OR pds_comp.meta_value LIKE %s)", $kw_v, $kw_v );
            $loc_conditions[]   = $wpdb->prepare( "pds_loc.meta_value LIKE %s", $kw_v );
            $term_conditions[]  = $wpdb->prepare( "(pds_terms.name LIKE %s OR pds_terms.slug LIKE %s)", $kw_v, $kw_v );
        }
        $title_sql = implode( ' OR ', $title_conditions );
        $loc_sql   = implode( ' OR ', $loc_conditions );
        $term_sql  = implode( ' OR ', $term_conditions );

        $relevance_order = " (CASE WHEN ({$title_sql}) THEN 0 WHEN ({$loc_sql}) THEN 1 WHEN ({$term_sql}) THEN 2 ELSE 3 END) ASC ";

        if ( ! empty( trim( $clauses['orderby'] ?? '' ) ) ) {
            $clauses['orderby'] = $relevance_order . ', ' . $clauses['orderby'];
        } else {
            $clauses['orderby'] = $relevance_order;
        }

        $clauses['distinct'] = 'DISTINCT';
    }
    return $clauses;
}
add_filter( 'posts_clauses', 'pds_filter_job_listing_search', 10, 2 );
endif;

/**
 * Handle AJAX Live Search Suggestions for Home page
 */
add_action( 'template_redirect', 'pds_handle_live_search_suggest' );
function pds_handle_live_search_suggest() {
    if ( ! isset( $_GET['pds_ajax_search_suggest'] ) ) {
        return;
    }

    $keyword  = isset( $_GET['keyword'] ) ? sanitize_text_field( wp_unslash( $_GET['keyword'] ) ) : '';
    $location = isset( $_GET['location'] ) ? sanitize_text_field( wp_unslash( $_GET['location'] ) ) : '';

    if ( mb_strlen( trim( $keyword ) ) < 2 ) {
        wp_send_json_success( array( 'jobs' => array(), 'total' => 0 ) );
    }

    $args = pds_get_jobs_query_args( array(
        'posts_per_page'  => 5,
        'search_keywords' => $keyword,
        'search_location' => $location,
        'sort'            => 'latest',
    ) );

    $query = new WP_Query( $args );
    $jobs  = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $job_id = get_the_ID();

            $types = wp_get_post_terms( $job_id, 'job_listing_type' );
            $job_type = ( ! empty( $types ) && ! is_wp_error( $types ) ) ? $types[0]->name : 'Fulltime';

            $company = get_post_meta( $job_id, '_company_name', true );
            $loc     = get_post_meta( $job_id, '_job_location', true );

            $jobs[] = array(
                'id'       => $job_id,
                'title'    => get_the_title(),
                'company'  => $company ? $company : 'Plan Do See',
                'location' => $loc ? $loc : 'Ho Chi Minh City',
                'type'     => $job_type,
                'url'      => get_permalink(),
            );
        }
    }
    wp_reset_postdata();

    // URL chuyển sang trang Jobs khi người dùng chọn xem toàn bộ kết quả
    $jobs_page_url = home_url( '/jobs/?search_keywords=' . urlencode( $keyword ) );
    if ( ! empty( $location ) ) {
        $jobs_page_url .= '&search_location=' . urlencode( $location );
    }

    wp_send_json_success( array(
        'jobs'          => $jobs,
        'total'         => $query->found_posts,
        'jobs_page_url' => $jobs_page_url,
        'keyword'       => $keyword,
    ) );
}

/**
 * Build unified WP_Query args for job listings with search keywords, location, category, type and sorting.
 *
 * @param array $params Custom overrides (posts_per_page, paged, sort, search_keywords, search_location, search_category, search_type)
 * @return array Sanitized WP_Query arguments
 */
if ( ! function_exists( 'pds_get_jobs_query_args' ) ) :
function pds_get_jobs_query_args( $params = array() ) {
    $paged     = isset( $params['paged'] ) ? max( 1, (int) $params['paged'] ) : 1;
    $count     = isset( $params['posts_per_page'] ) ? (int) $params['posts_per_page'] : 6;
    $sort      = isset( $params['sort'] ) ? sanitize_text_field( $params['sort'] ) : 'latest';
    $keywords  = isset( $params['search_keywords'] ) ? trim( sanitize_text_field( $params['search_keywords'] ) ) : '';
    $location  = isset( $params['search_location'] ) ? trim( sanitize_text_field( $params['search_location'] ) ) : '';
    $category  = isset( $params['search_category'] ) ? trim( sanitize_text_field( $params['search_category'] ) ) : '';
    $type      = isset( $params['search_type'] ) ? trim( sanitize_text_field( $params['search_type'] ) ) : '';

    $query_args = array(
        'post_type'      => 'job_listing',
        'post_status'    => 'publish',
        'posts_per_page' => $count,
        'paged'          => $paged,
    );

    // Sorting
    switch ( $sort ) {
        case 'oldest':
            $query_args['orderby'] = 'date';
            $query_args['order']   = 'ASC';
            break;
        case 'title_asc':
            $query_args['orderby'] = 'title';
            $query_args['order']   = 'ASC';
            break;
        case 'title_desc':
            $query_args['orderby'] = 'title';
            $query_args['order']   = 'DESC';
            break;
        case 'latest':
        default:
            $query_args['orderby'] = 'date';
            $query_args['order']   = 'DESC';
            break;
    }

    // Search keywords (matches post_title, post_content, post_excerpt, _company_name, _job_location, taxonomy)
    if ( ! empty( $keywords ) ) {
        $query_args['pds_job_keywords'] = $keywords;
    }

    // Search location (matches _job_location meta)
    if ( ! empty( $location ) && strtolower( $location ) !== 'all locations' ) {
        $query_args['meta_query'] = array(
            array(
                'key'     => '_job_location',
                'value'   => $location,
                'compare' => 'LIKE',
            ),
        );
    }

    // Tax Query for Category & Type Filters
    $tax_query = array();

    if ( ! empty( $category ) && 'all' !== strtolower( $category ) && 'all categories' !== strtolower( $category ) ) {
        $tax_query[] = array(
            'taxonomy' => 'job_listing_category',
            'field'    => is_numeric( $category ) ? 'term_id' : 'slug',
            'terms'    => $category,
        );
    }

    if ( ! empty( $type ) && 'all' !== strtolower( $type ) ) {
        $tax_query[] = array(
            'taxonomy' => 'job_listing_type',
            'field'    => is_numeric( $type ) ? 'term_id' : 'slug',
            'terms'    => $type,
        );
    }

    if ( ! empty( $tax_query ) ) {
        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'AND';
        }
        $query_args['tax_query'] = $tax_query;
    }

    return $query_args;
}
endif;

/**
 * Retrieve real company photos gallery for a job listing
 *
 * @param int $job_id
 * @return array Array of image data: [ ['full' => url, 'thumb' => url, 'title' => title], ... ]
 */
if ( ! function_exists( 'pds_get_job_company_photos' ) ) :
function pds_get_job_company_photos( $job_id ) {
    $photos = array();
    $company_name = get_post_meta( $job_id, '_company_name', true );
    if ( empty( $company_name ) ) {
        $company_name = get_the_title( $job_id );
    }

    // 1. Check custom meta _company_photos (attachment IDs or URLs)
    $meta_photos = get_post_meta( $job_id, '_company_photos', true );
    if ( ! empty( $meta_photos ) && is_array( $meta_photos ) ) {
        foreach ( $meta_photos as $item ) {
            if ( is_numeric( $item ) ) {
                $full  = wp_get_attachment_image_url( $item, 'full' );
                $thumb = wp_get_attachment_image_url( $item, 'medium' );
                if ( $full ) {
                    $photos[] = array( 'full' => $full, 'thumb' => $thumb ?: $full, 'title' => $company_name );
                }
            } elseif ( is_string( $item ) && filter_var( $item, FILTER_VALIDATE_URL ) ) {
                $photos[] = array( 'full' => $item, 'thumb' => $item, 'title' => $company_name );
            }
        }
    }

    // 2. Check attached media in WordPress
    if ( empty( $photos ) ) {
        $attachments = get_attached_media( 'image', $job_id );
        foreach ( $attachments as $att ) {
            $full  = wp_get_attachment_image_url( $att->ID, 'full' );
            $thumb = wp_get_attachment_image_url( $att->ID, 'medium' );
            if ( $full ) {
                $photos[] = array(
                    'full'  => $full,
                    'thumb' => $thumb ?: $full,
                    'title' => get_the_title( $att->ID ) ?: $company_name,
                );
            }
        }
    }

    // 3. Fallback to real seed gallery photos from uploads/seed-cms-content
    if ( empty( $photos ) ) {
        $seed_files = array(
            array(
                'file'  => 'news-restaurant-hotel-operations.png',
                'title' => __( 'Operations & Main Venue', 'jobscout' ),
            ),
            array(
                'file'  => 'news-venue-interior.png',
                'title' => __( 'Interior Design & Dining Hall', 'jobscout' ),
            ),
            array(
                'file'  => 'news-hospitality-consulting.png',
                'title' => __( 'Hospitality & Guest Reception', 'jobscout' ),
            ),
            array(
                'file'  => 'news-project-development.png',
                'title' => __( 'Project Development & Facilities', 'jobscout' ),
            ),
            array(
                'file'  => 'contact-japanese-street.png',
                'title' => __( 'Exterior & Neighborhood', 'jobscout' ),
            ),
            array(
                'file'  => 'about-tokyo-tower.png',
                'title' => __( 'Tokyo Tower View & Headquarters', 'jobscout' ),
            ),
        );

        $upload_dir = wp_upload_dir();
        $seed_base_url = content_url( '/uploads/seed-cms-content/' );
        $seed_base_dir = trailingslashit( $upload_dir['basedir'] ) . 'seed-cms-content/';

        foreach ( $seed_files as $seed ) {
            $full_url = $seed_base_url . $seed['file'];
            $ext = pathinfo( $seed['file'], PATHINFO_EXTENSION );
            $base = pathinfo( $seed['file'], PATHINFO_FILENAME );
            $thumb_file = $base . '-600x450.' . $ext;
            $thumb_url = file_exists( $seed_base_dir . $thumb_file ) ? ( $seed_base_url . $thumb_file ) : $full_url;

            $photos[] = array(
                'full'  => $full_url,
                'thumb' => $thumb_url,
                'title' => sprintf( '%s - %s', $company_name, $seed['title'] ),
            );
        }
    }

    return $photos;
}
endif;

/**
 * Handle AJAX requests for All Jobs page (Load More, Sorting, Filtering)
 */
add_action( 'template_redirect', 'pds_handle_all_jobs_ajax', 1 );
add_action( 'wp_ajax_pds_ajax_jobs', 'pds_handle_all_jobs_ajax' );
add_action( 'wp_ajax_nopriv_pds_ajax_jobs', 'pds_handle_all_jobs_ajax' );

function pds_handle_all_jobs_ajax() {
    if ( ! isset( $_REQUEST['pds_ajax_jobs'] ) && ( ! isset( $_REQUEST['action'] ) || 'pds_ajax_jobs' !== $_REQUEST['action'] ) ) {
        return;
    }

    $paged    = isset( $_REQUEST['page'] ) ? max( 1, (int) $_REQUEST['page'] ) : 1;
    $sort     = isset( $_REQUEST['sort'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['sort'] ) ) : 'latest';
    $keywords = isset( $_REQUEST['search_keywords'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search_keywords'] ) ) : '';
    $location = isset( $_REQUEST['search_location'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search_location'] ) ) : '';
    $category = isset( $_REQUEST['search_category'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search_category'] ) ) : '';
    $type     = isset( $_REQUEST['search_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search_type'] ) ) : '';

    $args = pds_get_jobs_query_args( array(
        'posts_per_page'  => 6,
        'paged'           => $paged,
        'sort'            => $sort,
        'search_keywords' => $keywords,
        'search_location' => $location,
        'search_category' => $category,
        'search_type'     => $type,
    ) );

    $ajax_query = new WP_Query( $args );

    ob_start();
    if ( $ajax_query->have_posts() ) {
        while ( $ajax_query->have_posts() ) {
            $ajax_query->the_post();
            get_template_part( 'template-parts/all-jobs-card' );
        }
    }
    $rendered_html = ob_get_clean();
    $max_num_pages = $ajax_query->max_num_pages;
    wp_reset_postdata();

    wp_send_json_success( array(
        'html'         => $rendered_html,
        'has_more'     => ( $paged < $max_num_pages ),
        'max_pages'    => $max_num_pages,
        'current_page' => $paged,
        'total'        => $ajax_query->found_posts,
    ) );
    exit;
}

/**
 * Prevent canonical redirect from intercepting AJAX requests with page=1
 */
add_filter( 'redirect_canonical', function( $redirect_url ) {
    if ( isset( $_REQUEST['pds_ajax_jobs'] ) ) {
        return false;
    }
    return $redirect_url;
} );


