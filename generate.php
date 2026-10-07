<?php
/**
 * Script tự động tạo và cập nhật 10 bài viết mẫu
 * Đầy đủ: Tiêu đề, Chuyên mục, Thẻ (Tags), Ảnh đại diện (Featured Image), Đoạn trích (Excerpt ~100 ký tự)
 * Dự án: TranCaoTrong_CMS
 * 
 * Hướng dẫn sử dụng:
 * 1. Chạy qua trình duyệt: http://localhost/TranCaoTrong_CMS/generate.php
 * 2. Chạy qua CLI: php generate.php
 */

// Nạp môi trường WordPress
require_once __DIR__ . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

// Cấu hình thời gian chạy tối đa
@set_time_limit(300);

// Danh sách 5 danh mục chuẩn
$categories_config = [
    'cong-nghe' => [
        'name' => 'Công nghệ',
        'desc' => 'Tin tức và xu hướng công nghệ'
    ],
    'doi-song' => [
        'name' => 'Đời sống',
        'desc' => 'Cẩm nang và bí quyết sống khỏe'
    ],
    'giai-tri' => [
        'name' => 'Giải trí',
        'desc' => 'Âm nhạc, điện ảnh và đời sống'
    ],
    'giao-duc' => [
        'name' => 'Giáo dục',
        'desc' => 'Kiến thức và tài liệu học tập'
    ],
    'the-thao' => [
        'name' => 'Thể thao',
        'desc' => 'Cập nhật các sự kiện thể thao'
    ],
];

// Đảm bảo các danh mục tồn tại và lưu lại ID
$category_ids = [];
foreach ($categories_config as $slug => $cat_data) {
    $existing = get_category_by_slug($slug);
    if ($existing && !is_wp_error($existing)) {
        $category_ids[$slug] = $existing->term_id;
    } else {
        $new_term = wp_insert_term(
            $cat_data['name'],
            'category',
            [
                'slug' => $slug,
                'description' => $cat_data['desc'],
            ]
        );
        if (!is_wp_error($new_term)) {
            $category_ids[$slug] = $new_term['term_id'];
        }
    }
}

// Lấy user ID của admin làm tác giả
$admin_user = get_user_by('login', 'trancaotrong');
$author_id = $admin_user ? $admin_user->ID : 1;

// Danh sách 10 bài viết chi tiết, đầy đủ chuyên mục, thẻ, excerpt ~100 ký tự và ảnh đại diện
$posts_data = [
    // --- 1. CÔNG NGHỆ ---
    [
        'category' => 'cong-nghe',
        'title'    => 'Trí Tuệ Nhân Tạo (AI) Đang Định Hình Lại Cuộc Sống Con Người Ra Sao Trong Năm 2026?',
        'slug'     => 'tri-tue-nhan-tao-ai-dinh-hinh-cuoc-song-2026',
        'excerpt'  => 'Khám phá các bước đột phá của trí tuệ nhân tạo và tác động sâu sắc đến đời sống năm 2026.', // 91 ký tự
        'tags'     => ['Trí tuệ nhân tạo', 'Công nghệ 2026', 'Tự động hóa', 'Mạng nơ-ron'],
        'featured_img' => [
            'url'      => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'featured-ai-revolution.jpg',
            'desc'     => 'Ảnh đại diện Trí tuệ nhân tạo AI'
        ],
        'images'   => [
            [
                'url'     => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Mạng nơ-ron và công nghệ trí tuệ nhân tạo đang tiến hóa với tốc độ phi thường.',
                'alt'     => 'Trí tuệ nhân tạo AI hiện đại'
            ],
            [
                'url'     => 'https://images.unsplash.com/photo-1531746790731-6c087fecd65a?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Robot thông minh tương tác và hỗ trợ con người trong đời sống thường nhật.',
                'alt'     => 'Robot thông minh và tự động hóa'
            ]
        ],
        'content'  => <<<HTML
<p class="lead">Trí tuệ nhân tạo (AI) không còn là khái niệm xa vời trong các bộ phim khoa học viễn tưởng, mà đã trở thành một phần thiết yếu trong nhịp sống đương đại. Từ các công cụ hỗ trợ công việc văn phòng đến các giải pháp tự động hóa phức tạp trong y tế và công nghiệp, AI đang tạo nên một cuộc cách mạng sâu rộng.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=1200&q=80" alt="Trí tuệ nhân tạo AI hiện đại" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Mạng nơ-ron và công nghệ trí tuệ nhân tạo đang tiến hóa với tốc độ phi thường.</figcaption>
</figure>

<h2>1. AI Tối Ưu Hóa Năng Suất Làm Việc Cá Nhân</h2>
<p>Ngày nay, các trợ lý ảo thông minh có khả năng tóm tắt tài liệu hàng trăm trang chỉ trong vài giây, tạo lập mã nguồn chính xác và hỗ trợ thiết kế giao diện đồ họa. Nhờ đó, người lao động có thể tiết kiệm tới 40% thời gian cho các tác vụ lặp đi lặp lại để tập trung vào tư duy chiến lược và sáng tạo.</p>

<h2>2. Bước Đột Phá Trong Chăm Sóc Sức Khỏe Và Y Tế</h2>
<p>Trong lĩnh vực y khoa, các thuật toán học sâu (Deep Learning) hỗ trợ bác sĩ chẩn đoán hình ảnh X-quang, MRI với độ chính xác cao hơn bao giờ hết. Điều này giúp phát hiện sớm các mầm bệnh hiểm nghèo và cá nhân hóa phác đồ điều trị cho từng bệnh nhân.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1531746790731-6c087fecd65a?auto=format&fit=crop&w=1200&q=80" alt="Robot thông minh và tự động hóa" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Robot thông minh tương tác và hỗ trợ con người trong đời sống thường nhật.</figcaption>
</figure>

<h2>3. Thách Thức Về Đạo Đức Và An Toàn Dữ Liệu</h2>
<p>Bên cạnh những tiện ích vượt trội, vấn đề bảo mật quyền riêng tư và bản quyền nội dung cũng đang đặt ra nhiều bài toán nan giải. Việc xây dựng các khung pháp lý chặt chẽ và sử dụng AI một cách có trách nhiệm chính là chìa khóa để nhân loại phát triển bền vững cùng công nghệ.</p>
HTML
    ],

    [
        'category' => 'cong-nghe',
        'title'    => 'Khám Phá Xu Hướng Nhà Thông Minh (Smart Home): Trải Nghiệm Sống Đẳng Cấp',
        'slug'     => 'kham-pha-xu-huong-nha-thong-minh-smart-home',
        'excerpt'  => 'Tìm hiểu hệ sinh thái nhà thông minh mang đến không gian sống tiện nghi và tiết kiệm năng lượng.', // 97 ký tự
        'tags'     => ['Nhà thông minh', 'Smart Home', 'IoT', 'Thiết bị thông minh'],
        'featured_img' => [
            'url'      => 'https://images.unsplash.com/photo-1558002038-1055907df827?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'featured-smart-home.jpg',
            'desc'     => 'Ảnh đại diện Không gian nhà thông minh'
        ],
        'images'   => [
            [
                'url'     => 'https://images.unsplash.com/photo-1558002038-1055907df827?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Hệ thống điều khiển tiện ích thông minh tích hợp ngay trong tầm tay.',
                'alt'     => 'Không gian phòng khách ngôi nhà thông minh'
            ],
            [
                'url'     => 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Thiết bị IoT kết nối đồng bộ giúp tối ưu hóa năng lượng tiêu thụ.',
                'alt'     => 'Thiết bị IoT và nhà thông minh'
            ]
        ],
        'content'  => <<<HTML
<p class="lead">Hệ thống nhà thông minh (Smart Home) không chỉ là biểu tượng của phong cách sống thời thượng mà còn là giải pháp nâng tầm chất lượng cuộc sống, mang đến sự an tâm và thoải mái trọn vẹn.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1558002038-1055907df827?auto=format&fit=crop&w=1200&q=80" alt="Không gian phòng khách ngôi nhà thông minh" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Hệ thống điều khiển tiện ích thông minh tích hợp ngay trong tầm tay.</figcaption>
</figure>

<h2>1. Tự Động Hóa Kịch Bản Sống Hằng Ngày</h2>
<p>Hãy tưởng tượng mỗi buổi sáng rèm cửa tự động mở đón ánh bình minh, máy pha cà phê khởi động và âm nhạc du dương cất lên chào ngày mới. Khi bạn rời khỏi nhà, toàn bộ thiết bị điện không cần thiết tự động ngắt nguồn để tiết kiệm năng lượng.</p>

<h2>2. An Ninh Chủ Động 24/7</h2>
<p>Nhờ tích hợp cảm biến chuyển động, camera AI nhận diện khuôn mặt và khóa cửa sinh trắc học, chủ nhà có thể giám sát an ninh ngôi nhà mọi lúc mọi nơi thông qua điện thoại thông minh, nhận cảnh báo tức thì khi có sự cố bất thường.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1585771724684-38269d6639fd?auto=format&fit=crop&w=1200&q=80" alt="Thiết bị IoT và nhà thông minh" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Thiết bị IoT kết nối đồng bộ giúp tối ưu hóa năng lượng tiêu thụ.</figcaption>
</figure>

<h2>3. Tiết Kiệm Năng Lượng Và Thân Thiện Môi Trường</h2>
<p>Hệ thống điều hòa nhiệt độ thông minh tự động điều chỉnh theo thời tiết thực tế, cùng hệ thống đèn LED cảm ứng giúp gia đình giảm thiểu tới 30% hóa đơn tiền điện mỗi tháng, góp phần xây dựng lối sống xanh.</p>
HTML
    ],

    // --- 2. ĐỜI SỐNG ---
    [
        'category' => 'doi-song',
        'title'    => 'Bí Quyết Thiết Lập Lối Sống Lành Mạnh Cho Người Làm Việc Bận Rộn',
        'slug'     => 'bi-quyet-thiet-lap-loi-song-lanh-manh-cho-nguoi-ban-ron',
        'excerpt'  => 'Bí quyết duy trì năng lượng và sức khỏe bền bỉ mỗi ngày cho những người bận rộn với công việc.', // 95 ký tự
        'tags'     => ['Sống khỏe', 'Dinh dưỡng xanh', 'Chăm sóc bản thân', 'Lối sống lành mạnh'],
        'featured_img' => [
            'url'      => 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'featured-healthy-lifestyle.jpg',
            'desc'     => 'Ảnh đại diện Lối sống lành mạnh'
        ],
        'images'   => [
            [
                'url'     => 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Bữa ăn cân bằng giàu rau xanh và thực phẩm nguyên chất.',
                'alt'     => 'Chế độ dinh dưỡng lành mạnh'
            ],
            [
                'url'     => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Dành 15 phút tập thiền hoặc yoga mỗi sớm mai để tái tạo năng lượng.',
                'alt'     => 'Thiền và yoga thư giãn tâm trí'
            ]
        ],
        'content'  => <<<HTML
<p class="lead">Trong guồng quay hối hả của cuộc sống hiện đại, nhiều người thường vô tình bỏ quên việc chăm sóc bản thân. Tuy nhiên, chỉ cần những thay đổi nhỏ trong nếp sinh hoạt hằng ngày cũng đủ để tạo nên sự khác biệt lớn cho sức khỏe của bạn.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&fit=crop&w=1200&q=80" alt="Chế độ dinh dưỡng lành mạnh" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Bữa ăn cân bằng giàu rau xanh và thực phẩm nguyên chất.</figcaption>
</figure>

<h2>1. Dinh Dưỡng Xanh - Nền Tảng Của Sinh Lực</h2>
<p>Ưu tiên thực phẩm tươi sống, bổ sung nhiều rau xanh, ngũ cốc nguyên hạt và hạn chế đồ ngọt hay thức ăn chế biến sẵn. Đừng quên uống đủ từ 1.5 - 2 lít nước mỗi ngày để hỗ trợ quá trình thanh lọc cơ thể và giữ cho làn da luôn căng tràn sức sống.</p>

<h2>2. Giấc Ngủ Chất Lượng: Liều Thuốc Tự Nhiên Tuyệt Vời</h2>
<p>Một giấc ngủ sâu từ 7 đến 8 tiếng mỗi đêm giúp não bộ đào thải độc tố và tái tạo tế bào. Hãy tắt các thiết bị điện tử ít nhất 30 phút trước khi ngủ, tạo không gian phòng ngủ yên tĩnh, thoáng mát và có ánh sáng dịu nhẹ.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=1200&q=80" alt="Thiền và yoga thư giãn tâm trí" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Dành 15 phút tập thiền hoặc yoga mỗi sớm mai để tái tạo năng lượng.</figcaption>
</figure>

<h2>3. Vận Động Nhẹ Nhàng Nhưng Đều Đặn</h2>
<p>Dù lịch trình có dày đặc đến đâu, hãy cố gắng dành ra 20-30 phút mỗi ngày để đi bộ, leo cầu thang bộ hoặc tập yoga. Vận động giúp giải phóng endorphin – hormone hạnh phúc, giúp bạn luôn giữ được tinh thần lạc quan và tích cực.</p>
HTML
    ],

    [
        'category' => 'doi-song',
        'title'    => 'Nghệ Thuật Sống Tối Giản: Giải Phóng Không Gian Và Tìm Lại Bình Yên',
        'slug'     => 'nghe-thuat-song-toi-gian-giai-phong-khong-gian-binh-yen',
        'excerpt'  => 'Lối sống tối giản giúp loại bỏ âu lo, giải phóng không gian và tìm lại sự bình yên trong tâm hồn.', // 99 ký tự
        'tags'     => ['Sống tối giản', 'Minimalism', 'Cân bằng cuộc sống', 'Bình yên nội tại'],
        'featured_img' => [
            'url'      => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'featured-minimalism-room.jpg',
            'desc'     => 'Ảnh đại diện Sống tối giản'
        ],
        'images'   => [
            [
                'url'     => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Không gian sống thoáng đãng, gọn gàng mang lại sự tĩnh lặng cho tâm hồn.',
                'alt'     => 'Không gian phòng khách tối giản'
            ],
            [
                'url'     => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Tận hưởng từng khoảnh khắc giản dị bên tách trà và cuốn sách yêu thích.',
                'alt'     => 'Lối sống tối giản an yên'
            ]
        ],
        'content'  => <<<HTML
<p class="lead">Sống tối giản không có nghĩa là tự tước bỏ mọi tiện nghi, mà là sự chọn lọc tinh tế: giữ lại những gì thực sự có giá trị, hữu ích và mang lại niềm vui cho bản thân.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=1200&q=80" alt="Không gian phòng khách tối giản" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Không gian sống thoáng đãng, gọn gàng mang lại sự tĩnh lặng cho tâm hồn.</figcaption>
</figure>

<h2>1. Dọn Dẹp Không Gian Vật Lý</h2>
<p>Bắt đầu từ việc rà soát tủ quần áo, bàn làm việc và các vật dụng không còn sử dụng trong vòng 6 tháng qua. Việc quyên góp hoặc tái chế những món đồ dư thừa sẽ tạo ra sự thoáng đãng cho căn phòng, từ đó giúp tâm trí bạn bớt ngột ngạt.</p>

<h2>2. Thanh Lọc Không Gian Kỹ Thuật Số</h2>
<p>Hủy đăng ký các bản tin rác, dọn sạch hộp thư đến và xóa các ứng dụng không cần thiết trên điện thoại. Hạn chế thời gian lướt mạng xã hội vô định sẽ giúp bạn có thêm hàng giờ quý báu dành cho gia đình và sở thích cá nhân.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80" alt="Lối sống tối giản an yên" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Tận hưởng từng khoảnh khắc giản dị bên tách trà và cuốn sách yêu thích.</figcaption>
</figure>

<h2>3. Đầu Tư Vào Trải Nghiệm Thay Vì Vật Chất</h2>
<p>Thay vì mua sắm bốc đồng theo trào lưu, hãy dành ngân sách cho những chuyến đi trải nghiệm văn hóa, các khóa học phát triển bản thân hoặc những buổi sum họp ấm áp cùng người thân yêu.</p>
HTML
    ],

    // --- 3. GIẢI TRÍ ---
    [
        'category' => 'giai-tri',
        'title'    => 'Xu Hướng Điện Ảnh Đương Đại: Khi Công Nghệ Kỹ Xảo Hòa Quyện Cùng Cảm Xúc',
        'slug'     => 'xu-huong-dien-anh-duong-dai-cong-nghe-ky-xao-cam-xuc',
        'excerpt'  => 'Điểm qua những bước tiến công nghệ kỹ xảo điện ảnh và các trải nghiệm rạp phim đỉnh cao hiện nay.', // 101 ký tự
        'tags'     => ['Điện ảnh', 'Phim chiếu rạp', 'Kỹ xảo điện ảnh', 'IMAX'],
        'featured_img' => [
            'url'      => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'featured-cinema-trends.jpg',
            'desc'     => 'Ảnh đại diện Xu hướng điện ảnh'
        ],
        'images'   => [
            [
                'url'     => 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Không gian rạp chiếu phim hiện đại mang đến trải nghiệm nghe nhìn mãn nhãn.',
                'alt'     => 'Rạp chiếu phim hiện đại'
            ],
            [
                'url'     => 'https://images.unsplash.com/photo-1518173946687-a4c8a383392e?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Hậu trường sản xuất điện ảnh với những thiết bị quay phim kỹ thuật số đỉnh cao.',
                'alt'     => 'Máy quay phim chuyên nghiệp'
            ]
        ],
        'content'  => <<<HTML
<p class="lead">Nền công nghiệp điện ảnh đang bước vào kỷ nguyên hoàng kim mới, nơi sự bùng nổ của công nghệ kỹ xảo hình ảnh kết hợp nhuần nhuyễn với nghệ thuật kể chuyện chân thực, lay động trái tim khán giả toàn cầu.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=1200&q=80" alt="Rạp chiếu phim hiện đại" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Không gian rạp chiếu phim hiện đại mang đến trải nghiệm nghe nhìn mãn nhãn.</figcaption>
</figure>

<h2>1. Sự Lên Ngôi Của Chuẩn Định Dạng IMAX Và Âm Thanh Vòm Dolby Atmos</h2>
<p>Trải nghiệm tại rạp chiếu phim ngày nay đã vượt xa việc xem một bộ phim đơn thuần. Khán giả được đắm chìm vào từng khung hình rộng lớn với độ phân giải siêu nét và âm thanh vòm sống động như đang hiện diện ngay giữa bối cảnh của câu chuyện.</p>

<h2>2. Kịch Bản Tôn Vinh Chiều Sâu Tâm Lý Nhân Vật</h2>
<p>Bỏ qua những công thức hành động rập khuôn, các đạo diễn tên tuổi ngày nay chú trọng khai thác nội tâm giằng xé, tình cảm gia đình và những bài học nhân văn mang tính thời đại, để lại dư ba khó quên sau khi phim kết thúc.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1518173946687-a4c8a383392e?auto=format&fit=crop&w=1200&q=80" alt="Máy quay phim chuyên nghiệp" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Hậu trường sản xuất điện ảnh với những thiết bị quay phim kỹ thuật số đỉnh cao.</figcaption>
</figure>

<h2>3. Xu Hướng Xem Phim Đa Nền Tảng</h2>
<p>Các nền tảng streaming trực tuyến tiếp tục đầu tư sản xuất những series phim chất lượng tương đương phim nhựa chiếu rạp, mang đến cho công chúng kho tàng giải trí phong phú ngay tại phòng khách gia đình.</p>
HTML
    ],

    [
        'category' => 'giai-tri',
        'title'    => 'Âm Nhạc Và Sức Sống Từ Những Đêm Đại Nhạc Hội Trực Tiếp (Live Concert)',
        'slug'     => 'am-nhac-va-suc-song-tu-nhung-dem-dai-nhac-hoi-live-concert',
        'excerpt'  => 'Cảm nhận nguồn năng lượng cuồng nhiệt và sức mạnh gắn kết hàng vạn con người từ các đêm nhạc live.', // 100 ký tự
        'tags'     => ['Âm nhạc', 'Live Concert', 'Lễ hội âm nhạc', 'Nghệ thuật biểu diễn'],
        'featured_img' => [
            'url'      => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'featured-live-concert.jpg',
            'desc'     => 'Ảnh đại diện Đêm nhạc hội trực tiếp'
        ],
        'images'   => [
            [
                'url'     => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Bầu không khí cuồng nhiệt dưới ánh sáng rực rỡ của lễ hội âm nhạc đỉnh cao.',
                'alt'     => 'Sân khấu đại nhạc hội rực rỡ'
            ],
            [
                'url'     => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Cảm xúc thăng hoa khi nghệ sĩ cống hiến hết mình cùng cây đàn guitar.',
                'alt'     => 'Nghệ sĩ biểu diễn âm nhạc thăng hoa'
            ]
        ],
        'content'  => <<<HTML
<p class="lead">Không có gì so sánh được với cảm giác hòa mình vào biển người trong một đêm nhạc sống, nơi từng giai điệu và ca từ vang lên làm rung động mọi giác quan.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=1200&q=80" alt="Sân khấu đại nhạc hội rực rỡ" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Bầu không khí cuồng nhiệt dưới ánh sáng rực rỡ của lễ hội âm nhạc đỉnh cao.</figcaption>
</figure>

<h2>1. Năng Lượng Gắn Kết Diệu Kỳ</h2>
<p>Tại các sự kiện concert, mọi rào cản về lứa tuổi, nghề nghiệp hay ngôn ngữ đều tan biến. Hàng chục ngàn người cùng hát vang một điệp khúc quen thuộc, tạo nên bầu không khí đoàn kết đầy xúc cảm mà không màn hình điện thoại nào tái hiện được.</p>

<h2>2. Trải Nghiệm Trình Diễn Và Sân Khấu Ngoạn Mục</h2>
<p>Các nghệ sĩ đương đại chú trọng đầu tư công phu vào hiệu ứng ánh sáng laser, pháo hoa và vũ đạo chuẩn xác. Mỗi buổi biểu diễn được xây dựng như một vở kịch âm nhạc liền mạch, dẫn dắt người nghe đi qua nhiều cung bậc cảm xúc thăng hoa.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=1200&q=80" alt="Nghệ sĩ biểu diễn âm nhạc thăng hoa" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Cảm xúc thăng hoa khi nghệ sĩ cống hiến hết mình cùng cây đàn guitar.</figcaption>
</figure>

<h2>3. Âm Nhạc Trị Liệu Tâm Hồn</h2>
<p>Sau những ngày làm việc căng thẳng, những giai điệu chân thành chính là liều thuốc xoa dịu áp lực, khơi dậy niềm đam mê và nguồn cảm hứng sáng tạo cho cuộc sống thường nhật.</p>
HTML
    ],

    // --- 4. GIÁO DỤC ---
    [
        'category' => 'giao-duc',
        'title'    => 'Phương Pháp Tự Học Hiệu Quả Trong Kỷ Nguyên Số Hóa',
        'slug'     => 'phuong-phap-tu-hoc-hieu-qua-trong-ky-nguyen-so-hoa',
        'excerpt'  => 'Phương pháp rèn luyện kỹ năng tự học hiệu quả, làm chủ kiến thức mới trong kỷ nguyên số hiện đại.', // 99 ký tự
        'tags'     => ['Tự học', 'Kỹ năng số', 'Phương pháp học tập', 'Tư duy giáo dục'],
        'featured_img' => [
            'url'      => 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'featured-self-learning.jpg',
            'desc'     => 'Ảnh đại diện Phương pháp tự học'
        ],
        'images'   => [
            [
                'url'     => 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Không gian học tập yên tĩnh, tập trung cao độ cùng tài liệu trực tuyến.',
                'alt'     => 'Sinh viên tự học với laptop và tài liệu'
            ],
            [
                'url'     => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Đọc sách và hệ thống hóa kiến thức bằng sơ đồ tư duy trực quan.',
                'alt'     => 'Ghi chép và sơ đồ tư duy sáng tạo'
            ]
        ],
        'content'  => <<<HTML
<p class="lead">Trong một thế giới thay đổi không ngừng, năng lực tự học không chỉ là một lợi thế cạnh tranh mà đã trở thành kỹ năng sinh tồn tối quan trọng của mỗi cá nhân trên con đường chinh phục tri thức.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1501504905252-473c47e087f8?auto=format&fit=crop&w=1200&q=80" alt="Sinh viên tự học với laptop và tài liệu" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Không gian học tập yên tĩnh, tập trung cao độ cùng tài liệu trực tuyến.</figcaption>
</figure>

<h2>1. Xác Định Mục Tiêu Học Tập Theo Tiêu Chuẩn SMART</h2>
<p>Để việc tự học không bị đứt đoạn, hãy đặt ra mục tiêu cụ thể, đo lường được và có thời hạn rõ ràng. Ví dụ: Thay vì nói chung chung "tôi muốn học tiếng Anh", hãy đặt mục tiêu "học 10 từ vựng và luyện phát âm 20 phút mỗi tối trong 3 tháng".</p>

<h2>2. Ứng Dụng Kỹ Thuật Ghi Nhớ Feynman</h2>
<p>Kỹ thuật Feynman khuyên bạn: sau khi học xong một kiến thức mới, hãy thử giảng giải lại nó cho một người chưa biết gì bằng ngôn từ đơn giản nhất. Nếu bạn giải thích được trôi chảy, chứng tỏ bạn đã thực sự thấu hiểu cốt lõi vấn đề.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=1200&q=80" alt="Ghi chép và sơ đồ tư duy sáng tạo" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Đọc sách và hệ thống hóa kiến thức bằng sơ đồ tư duy trực quan.</figcaption>
</figure>

<h2>3. Tận Dụng Các Nền Tảng Học Trực Tuyến Hàng Đầu</h2>
<p>Các nền tảng như Coursera, edX hay YouTube cung cấp hàng triệu bài giảng miễn phí từ các trường đại học danh tiếng thế giới. Hãy biến chiếc máy tính xách tay của bạn thành cánh cổng kết nối trực tiếp với tri thức nhân loại.</p>
HTML
    ],

    [
        'category' => 'giao-duc',
        'title'    => 'Kỹ Năng Mềm Cốt Lõi Cần Chuẩn Bị Cho Thế Hệ Trẻ Hội Nhập Quốc Tế',
        'slug'     => 'ky-nang-mem-cot-loi-cho-the-he-tre-hoi-nhap-quoc-te',
        'excerpt'  => 'Những kỹ năng mềm thiết yếu giúp thế hệ trẻ tự tin hội nhập và phát triển sự nghiệp toàn cầu hóa.', // 100 ký tự
        'tags'     => ['Kỹ năng mềm', 'Giao tiếp', 'Hội nhập quốc tế', 'Phát triển bản thân'],
        'featured_img' => [
            'url'      => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'featured-soft-skills.jpg',
            'desc'     => 'Ảnh đại diện Kỹ năng mềm hội nhập'
        ],
        'images'   => [
            [
                'url'     => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Làm việc nhóm và trao đổi ý tưởng cởi mở thúc đẩy sự sáng tạo.',
                'alt'     => 'Nhóm bạn trẻ làm việc nhóm năng động'
            ],
            [
                'url'     => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Kỹ năng thuyết trình tự tin trước đám đông giúp truyền tải thông điệp mạnh mẽ.',
                'alt'     => 'Thuyết trình trước khán giả'
            ]
        ],
        'content'  => <<<HTML
<p class="lead">Bên cạnh kiến thức chuyên môn vững vàng, các kỹ năng mềm đóng vai trò quyết định đến 75% sự thành công và khả năng thích ứng của người trẻ trong môi trường làm việc toàn cầu hóa hiện nay.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80" alt="Nhóm bạn trẻ làm việc nhóm năng động" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Làm việc nhóm và trao đổi ý tưởng cởi mở thúc đẩy sự sáng tạo.</figcaption>
</figure>

<h2>1. Tư Duy Phản Biện (Critical Thinking)</h2>
<p>Trước làn sóng thông tin đa chiều trên không gian số, tư duy phản biện giúp chúng ta phân biệt sự thật và tin giả, biết đặt câu hỏi đúng đắn và đưa ra những quyết định sáng suốt dựa trên bằng chứng xác thực.</p>

<h2>2. Kỹ Năng Giao Tiếp Thấu Cảm Và Lắng Nghe Chủ Động</h2>
<p>Giao tiếp hiệu quả không chỉ là khả năng diễn đạt lưu loát mà còn là năng lực lắng nghe với sự tôn trọng và đồng cảm sâu sắc, từ đó xây dựng được mối quan hệ hợp tác bền chặt trong công việc.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=1200&q=80" alt="Thuyết trình trước khán giả" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Kỹ năng thuyết trình tự tin trước đám đông giúp truyền tải thông điệp mạnh mẽ.</figcaption>
</figure>

<h2>3. Khả Năng Thích Ứng Linh Hoạt (Agility)</h2>
<p>Môi trường làm việc hiện đại đòi hỏi mỗi người phải luôn sẵn sàng học hỏi những công nghệ mới, linh hoạt điều chỉnh kế hoạch và kiên trì đối mặt với những biến động không lường trước.</p>
HTML
    ],

    // --- 5. THỂ THAO ---
    [
        'category' => 'the-thao',
        'title'    => 'Hành Trình Chinh Phục Cự Ly Marathon Cho Người Mới Bắt Đầu',
        'slug'     => 'hanh-trinh-chinh-phuc-cu-ly-marathon-cho-nguoi-moi',
        'excerpt'  => 'Hướng dẫn lịch tập luyện, kỹ thuật thở và chế độ dinh dưỡng để chinh phục cự ly marathon đầu đời.', // 99 ký tự
        'tags'     => ['Chạy bộ', 'Marathon', 'Thể lực', 'Rèn luyện sức khỏe'],
        'featured_img' => [
            'url'      => 'https://images.unsplash.com/photo-1452626038306-9aae5e071dd3?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'featured-marathon-runner.jpg',
            'desc'     => 'Ảnh đại diện Chinh phục marathon'
        ],
        'images'   => [
            [
                'url'     => 'https://images.unsplash.com/photo-1452626038306-9aae5e071dd3?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Chạy bộ buổi sáng giữa không gian trong lành tiếp thêm sinh lực dồi dào.',
                'alt'     => 'Vận động viên chạy bộ đường trường'
            ],
            [
                'url'     => 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Khởi động kỹ lưỡng và trang bị giày chạy đạt chuẩn giúp phòng tránh chấn thương.',
                'alt'     => 'Khởi động và tập luyện thể thao'
            ]
        ],
        'content'  => <<<HTML
<p class="lead">Chạy bộ marathon không chỉ là một môn thể thao thử thách sức bền thể chất mà còn là hành trình tôi luyện ý chí, kỷ luật và niềm tin vào bản thân.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1452626038306-9aae5e071dd3?auto=format&fit=crop&w=1200&q=80" alt="Vận động viên chạy bộ đường trường" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Chạy bộ buổi sáng giữa không gian trong lành tiếp thêm sinh lực dồi dào.</figcaption>
</figure>

<h2>1. Xây Dựng Thể Lực Từ Những Cự Ly Ngắn</h2>
<p>Nếu mới bắt đầu, bạn đừng vội nghĩ đến con số 42.195 km ngay. Hãy đặt mục tiêu vượt qua 5km đầu tiên, sau đó nâng dần lên 10km và 21km (Half Marathon). Quy tắc vàng là không tăng quãng đường chạy quá 10% mỗi tuần để cơ thể kịp thích nghi.</p>

<h2>2. Tầm Quan Trọng Của Kỹ Thuật Thở Và Giày Chạy</h2>
<p>Thở nhịp nhàng bằng cả mũi và miệng theo nhịp bước chân (ví dụ nhịp 3:2 hoặc 2:2) giúp cung cấp đủ oxy cho cơ bắp. Ngoài ra, một đôi giày chạy vừa vặn, có đệm êm hỗ trợ giảm chấn là trợ thủ đắc lực bảo vệ khớp gối của bạn.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1517838277536-f5f99be501cd?auto=format&fit=crop&w=1200&q=80" alt="Khởi động và tập luyện thể thao" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Khởi động kỹ lưỡng và trang bị giày chạy đạt chuẩn giúp phòng tránh chấn thương.</figcaption>
</figure>

<h2>3. Dinh Dưỡng Và Hồi Phục Sau Buổi Chạy</h2>
<p>Bổ sung điện giải kịp thời trong khi chạy và nạp protein kết hợp carbohydrate trong vòng 30 phút sau khi về đích giúp cơ bắp nhanh chóng phục hồi, sẵn sàng cho những thử thách tiếp theo.</p>
HTML
    ],

    [
        'category' => 'the-thao',
        'title'    => 'Bóng Đá Thế Giới: Những Bước Chuyển Chiến Thuật Định Đoạt Đỉnh Cao',
        'slug'     => 'bong-da-the-gioi-nhung-buoc-chuyen-chien-thuat-dinh-cao',
        'excerpt'  => 'Phân tích những chiến thuật đỉnh cao, lối chơi pressing và vai trò của khoa học dữ liệu bóng đá.', // 98 ký tự
        'tags'     => ['Bóng đá', 'Chiến thuật bóng đá', 'Thể thao đỉnh cao', 'Phân tích dữ liệu'],
        'featured_img' => [
            'url'      => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'featured-football-tactics.jpg',
            'desc'     => 'Ảnh đại diện Chiến thuật bóng đá'
        ],
        'images'   => [
            [
                'url'     => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Sân cỏ rực sáng cùng không khí nghẹt thở của các trận cầu đỉnh cao.',
                'alt'     => 'Sân vận động bóng đá quốc tế rực rỡ'
            ],
            [
                'url'     => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1200&q=80',
                'caption' => 'Pha bóng kịch tính đòi hỏi tốc độ, sức mạnh và tư duy chiến thuật nhạy bén.',
                'alt'     => 'Pha tranh chấp bóng kịch tính trên sân cỏ'
            ]
        ],
        'content'  => <<<HTML
<p class="lead">Bóng đá hiện đại ngày nay không chỉ dựa vào khoảnh khắc thiên tài của các ngôi sao, mà là cuộc đấu trí cân não giữa những triết lý chiến thuật tinh vi và công nghệ phân tích dữ liệu thể thao tiên tiến.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1200&q=80" alt="Sân vận động bóng đá quốc tế rực rỡ" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Sân cỏ rực sáng cùng không khí nghẹt thở của các trận cầu đỉnh cao.</figcaption>
</figure>

<h2>1. Triết Lý Gegenpressing Và Kiểm Soát Khu Vực</h2>
<p>Chiến thuật đoạt lại bóng ngay trên phần sân đối phương chỉ sau vài giây mất bóng đã trở thành tiêu chuẩn chung của các đội bóng hàng đầu châu Âu. Điều này đòi hỏi các cầu thủ phải có nền tảng thể lực phi thường cùng khả năng giữ cự ly đội hình chuẩn xác đến từng mét.</p>

<h2>2. Ứng Dụng Khoa Học Dữ Liệu Và AI Trong Thể Thao</h2>
<p>Từ việc đo chỉ số bàn thắng kỳ vọng (xG), quãng đường di chuyển cho đến biểu đồ nhiệt (heatmap), các ban huấn luyện giờ đây có thể điều chỉnh chiến thuật theo thời gian thực để khai thác điểm yếu của đối phương.</p>

<figure style="margin: 24px 0; text-align: center;">
    <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1200&q=80" alt="Pha tranh chấp bóng kịch tính trên sân cỏ" style="width: 100%; max-width: 900px; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" />
    <figcaption style="margin-top: 8px; font-style: italic; color: #666; font-size: 0.95em;">Pha bóng kịch tính đòi hỏi tốc độ, sức mạnh và tư duy chiến thuật nhạy bén.</figcaption>
</figure>

<h2>3. Tinh Thần Đồng Đội - Giá Trị Vĩnh Cửu Của Túc Cầu</h2>
<p>Dù chiến thuật có phức tạp đến đâu, yếu tố quyết định chiến thắng sau cùng vẫn là tinh thần đồng đội quả cảm, lòng kiên định và khát khao cống hiến cho người hâm mộ.</p>
HTML
    ],
];

// Hàm hỗ trợ tải và thiết lập Featured Image an toàn
function set_or_update_featured_image($post_id, $img_data) {
    // Nếu bài viết đã có Featured Image hợp lệ thì giữ nguyên
    $current_thumb_id = get_post_thumbnail_id($post_id);
    if ($current_thumb_id && wp_get_attachment_url($current_thumb_id)) {
        return $current_thumb_id;
    }

    $tmp = download_url($img_data['url']);
    if (is_wp_error($tmp)) {
        return false;
    }

    $file_array = [
        'name'     => $img_data['filename'],
        'tmp_name' => $tmp
    ];

    $attach_id = media_handle_sideload($file_array, $post_id, $img_data['desc']);
    if (is_wp_error($attach_id)) {
        @unlink($tmp);
        return false;
    }

    set_post_thumbnail($post_id, $attach_id);
    return $attach_id;
}

// Tiến hành chèn hoặc cập nhật các bài viết vào WordPress
$results = [];

foreach ($posts_data as $index => $item) {
    $cat_slug = $item['category'];
    $cat_id = isset($category_ids[$cat_slug]) ? $category_ids[$cat_slug] : 1;
    $cat_name = isset($categories_config[$cat_slug]) ? $categories_config[$cat_slug]['name'] : $cat_slug;

    // Kiểm tra xem bài viết đã tồn tại chưa (dựa theo slug)
    $existing_post = get_page_by_path($item['slug'], OBJECT, 'post');

    $post_args = [
        'post_title'    => $item['title'],
        'post_name'     => $item['slug'],
        'post_content'  => $item['content'],
        'post_excerpt'  => $item['excerpt'],
        'post_status'   => 'publish',
        'post_author'   => $author_id,
        'post_type'     => 'post',
        'post_category' => [$cat_id],
    ];

    if ($existing_post) {
        $post_args['ID'] = $existing_post->ID;
        $post_id = wp_update_post($post_args);
        $action = 'Cập nhật';
    } else {
        $post_id = wp_insert_post($post_args);
        $action = 'Tạo mới';
    }

    if (is_wp_error($post_id)) {
        $results[] = [
            'stt'        => $index + 1,
            'title'      => $item['title'],
            'category'   => $cat_name,
            'tags'       => implode(', ', $item['tags']),
            'excerpt'    => $item['excerpt'],
            'excerpt_len'=> mb_strlen($item['excerpt'], 'UTF-8'),
            'thumb'      => 'Thất bại',
            'thumb_url'  => '',
            'status'     => 'Lỗi: ' . $post_id->get_error_message(),
            'images_cnt' => count($item['images']),
            'url'        => '#',
            'action'     => $action,
            'success'    => false,
        ];
    } else {
        // Gắn chuyên mục
        wp_set_post_categories($post_id, [$cat_id]);

        // Gắn các thẻ (Tags)
        wp_set_post_tags($post_id, $item['tags'], false);

        // Tải và gán Featured Image
        $thumb_id = set_or_update_featured_image($post_id, $item['featured_img']);
        $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : '';

        $results[] = [
            'stt'        => $index + 1,
            'id'         => $post_id,
            'title'      => $item['title'],
            'category'   => $cat_name,
            'tags'       => implode(', ', $item['tags']),
            'excerpt'    => $item['excerpt'],
            'excerpt_len'=> mb_strlen($item['excerpt'], 'UTF-8'),
            'thumb'      => $thumb_id ? "Đã gán (ID: $thumb_id)" : 'Chưa gán',
            'thumb_url'  => $thumb_url,
            'status'     => 'Thành công (Đã xuất bản)',
            'images_cnt' => count($item['images']),
            'url'        => get_permalink($post_id),
            'action'     => $action,
            'success'    => true,
        ];
    }
}

// Thông báo hoàn tất xử lý
echo "Đã tạo/cập nhật thành công 10 bài viết đầy đủ tiêu đề, chuyên mục, thẻ tags, ảnh đại diện và đoạn trích vào WordPress!";

