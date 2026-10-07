<?php
/**
 * Tự động tạo:
 * 1. Ba chuyên mục (Categories): Tennis, Pic, Football
 * 2. Bài viết Thể thao chuyên mục Football có ảnh và nhúng Video YouTube
 */

require_once __DIR__ . '/wp-load.php';

// 1. Tạo 3 chuyên mục
$categories = [
    'Tennis'   => 'tennis',
    'Pic'      => 'pic',
    'Football' => 'football',
];

$cat_ids = [];

echo "<h3>1. Khởi tạo chuyên mục:</h3><ul>";
foreach ($categories as $cat_name => $slug) {
    $existing = get_category_by_slug($slug);
    if (!$existing) {
        $term = wp_insert_term($cat_name, 'category', ['slug' => $slug]);
        if (!is_wp_error($term)) {
            $cat_ids[$slug] = $term['term_id'];
            echo "<li>Đã tạo chuyên mục: <strong>$cat_name</strong> (ID: {$term['term_id']})</li>";
        } else {
            echo "<li>Lỗi tạo $cat_name: " . $term->get_error_message() . "</li>";
        }
    } else {
        $cat_ids[$slug] = $existing->term_id;
        echo "<li>Chuyên mục <strong>$cat_name</strong> đã tồn tại (ID: {$existing->term_id})</li>";
    }
}
echo "</ul>";

// 2. Tạo bài viết Thể thao có hình ảnh và video YouTube
$post_title = "Những Khoảnh Khắc Đỉnh Cao Của Bóng Đá Thế Giới";

// Tìm xem bài viết này đã có chưa
$existing_post = get_page_by_title($post_title, OBJECT, 'post');

$post_content = <<<HTML
<!-- wp:paragraph -->
<p>Bóng đá (Football) không chỉ là môn thể thao vua mà còn là niềm đam mê mãnh liệt của hàng triệu người hâm mộ trên khắp hành tinh. Dưới đây là những hình ảnh và video tổng hợp những khoảnh khắc đẹp mắt nhất.</p>
<!-- /wp:paragraph -->

<!-- wp:image {"align":"center"} -->
<figure class="wp-block-image aligncenter"><img src="/TranCaoTrong_CMS/wp-content/uploads/2026/09/featured-football-tactics.jpg" alt="Bóng đá thể thao"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Cùng theo dõi video tổng hợp những bàn thắng và pha bóng kỹ thuật đỉnh cao dưới đây:</p>
<!-- /wp:paragraph -->

<!-- wp:core-embed/youtube {"url":"https://www.youtube.com/watch?v=W8xYLKXd-aA","type":"video","providerNameSlug":"youtube","responsive":true} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
https://www.youtube.com/watch?v=W8xYLKXd-aA
</div></figure>
<!-- /wp:core-embed/youtube -->

<!-- wp:paragraph -->
<p>Chúc các bạn có những giây phút thư giãn tuyệt vời với niềm đam mê bóng đá!</p>
<!-- /wp:paragraph -->
HTML;

$football_cat_id = $cat_ids['football'] ?? get_cat_ID('Football');

$post_data = [
    'post_title'    => $post_title,
    'post_content'  => $post_content,
    'post_status'   => 'publish',
    'post_author'   => 1,
    'post_category' => [$football_cat_id]
];

echo "<h3>2. Khởi tạo bài viết thể thao có Ảnh & Video YouTube:</h3>";

if ($existing_post) {
    $post_data['ID'] = $existing_post->ID;
    wp_update_post($post_data);
    echo "<p>Đã cập nhật bài viết: <strong><a href='" . get_permalink($existing_post->ID) . "' target='_blank'>$post_title</a></strong> (ID: {$existing_post->ID})</p>";
} else {
    $new_post_id = wp_insert_post($post_data);
    if (!is_wp_error($new_post_id)) {
        echo "<p>Đã tạo mới bài viết thành công: <strong><a href='" . get_permalink($new_post_id) . "' target='_blank'>$post_title</a></strong> (ID: $new_post_id)</p>";
    } else {
        echo "<p>Lỗi tạo bài viết: " . $new_post_id->get_error_message() . "</p>";
    }
}
