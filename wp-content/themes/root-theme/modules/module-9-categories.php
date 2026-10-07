<?php
/**
 * Module 9: Categories phong cách FIT TDC
 * Vị trí: Sidebar / Shortcode [tdc_categories]
 * Tích hợp SQL trực tiếp qua $wpdb
 */
if (!defined('ABSPATH')) {
    exit;
}

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
