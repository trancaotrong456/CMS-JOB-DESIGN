<?php
/**
 * MODULE - PROFILE SETTINGS MODAL & I18N MULTILINGUAL ENGINE
 * Popup Cài đặt giao diện & Đa ngôn ngữ (Tiếng Việt <-> English)
 */
if (!defined('ABSPATH')) {
    exit;
}

$current_user = is_user_logged_in() ? wp_get_current_user() : null;
?>

<div class="profile-settings-overlay" id="profileSettingsOverlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="profileSettingsTitle">
    <div class="profile-settings-modal">

        <!-- HEADER -->
        <div class="profile-settings-header">
            <h3 id="profileSettingsTitle">
                <i class="fa fa-sliders"></i> <span data-i18n="settings_modal_title">Cài đặt hệ thống & Giao diện</span>
            </h3>
            <button type="button" class="profile-settings-close" id="closeProfileSettings" aria-label="Đóng">&times;</button>
        </div>

        <!-- CONTENT -->
        <div class="profile-settings-content">

            <!-- 1. APPEARANCE (CHỦ ĐỀ GIAO DIỆN) -->
            <section class="profile-settings-section">
                <h4><i class="fa fa-paint-brush"></i> <span data-i18n="appearance_heading">Giao diện (Appearance)</span></h4>
                <p class="profile-settings-description" data-i18n="appearance_desc">Tùy chỉnh chế độ hiển thị sáng / tối theo sở thích của bạn.</p>

                <div class="profile-theme-options">
                    <label class="profile-theme-option">
                        <input type="radio" name="profile_theme" value="light" id="profileThemeLight">
                        <span class="theme-box theme-light">
                            <i class="fa fa-sun-o"></i> <span data-i18n="theme_light">Sáng (Light)</span>
                        </span>
                    </label>

                    <label class="profile-theme-option">
                        <input type="radio" name="profile_theme" value="dark" id="profileThemeDark">
                        <span class="theme-box theme-dark">
                            <i class="fa fa-moon-o"></i> <span data-i18n="theme_dark">Tối (Dark)</span>
                        </span>
                    </label>

                    <label class="profile-theme-option">
                        <input type="radio" name="profile_theme" value="system" id="profileThemeSystem">
                        <span class="theme-box theme-system">
                            <i class="fa fa-desktop"></i> <span data-i18n="theme_system">Hệ thống (System)</span>
                        </span>
                    </label>
                </div>
            </section>

            <!-- 2. LANGUAGE (NGÔN NGỮ) -->
            <section class="profile-settings-section">
                <h4><i class="fa fa-globe"></i> <span data-i18n="language_heading">Ngôn ngữ (Language)</span></h4>
                <p class="profile-settings-description" data-i18n="language_desc">Chọn ngôn ngữ ưu tiên hiển thị nội dung trên website.</p>

                <select id="profileLanguage" class="profile-settings-select" aria-label="Chọn ngôn ngữ">
                    <option value="vi">Tiếng Việt (Vietnamese)</option>
                    <option value="en">English (United States)</option>
                </select>
            </section>

            <!-- 3. ACCOUNT (THÔNG TIN TÀI KHOẢN) -->
            <section class="profile-settings-section">
                <h4><i class="fa fa-user-circle-o"></i> <span data-i18n="account_heading">Tài khoản (Account)</span></h4>
                <?php if ($current_user) : ?>
                    <div class="profile-account-grid">
                        <div class="profile-form-group">
                            <label class="profile-settings-label" for="profileUsername" data-i18n="account_username">Tên đăng nhập</label>
                            <input type="text" id="profileUsername" class="profile-settings-input" value="<?php echo esc_attr($current_user->user_login); ?>" readonly>
                        </div>
                        <div class="profile-form-group">
                            <label class="profile-settings-label" for="profileEmail" data-i18n="account_email">Email tài khoản</label>
                            <input type="email" id="profileEmail" class="profile-settings-input" value="<?php echo esc_attr($current_user->user_email); ?>" readonly>
                        </div>
                    </div>
                <?php else : ?>
                    <p class="profile-guest-notice" data-i18n="guest_notice">
                        Bạn đang duyệt với tư cách Khách. Vui lòng đăng nhập để lưu cấu hình tài khoản.
                    </p>
                <?php endif; ?>
            </section>

        </div>

        <!-- FOOTER -->
        <div class="profile-settings-footer">
            <button type="button" class="profile-settings-cancel" id="cancelProfileSettings" data-i18n="btn_cancel">Hủy bỏ</button>
            <button type="button" class="profile-settings-save" id="saveProfileSettings" data-i18n="btn_save">Lưu thay đổi</button>
        </div>

    </div>
</div>

<script>
/**
 * TOÀN BỘ TỪ ĐIỂN ĐA NGÔN NGỮ (VIETNAMESE & ENGLISH)
 */
var I18N_DICTIONARY = {
    vi: {
        // Header
        home: "Trang chủ",
        search: "Tìm kiếm",
        search_submit: "Tìm kiếm",
        search_placeholder: "Tìm kiếm...",
        nav_sports: "Thể thao",
        nav_science: "Khoa học",
        nav_news: "Tin tức",
        menu: "Menu",
        menu_links: "Danh mục liên kết",
        menu_home: "🏠 Trang chủ",
        menu_sports: "⚽ Thể thao & Highlights",
        menu_science: "🔬 Khoa học - Công nghệ",
        menu_life: "📰 Tin tức đời sống",
        menu_football: "🏆 Football",
        menu_fixtures: "📅 Lịch thi đấu & Kết quả",
        account: "Tài khoản",
        hello: "Xin chào,",
        admin_panel: "Trang quản trị",
        settings_title: "Cài đặt giao diện & Hồ sơ",
        logout: "Đăng xuất",
        login: "Đăng nhập",
        register: "Đăng ký tài khoản",
        forgot_password: "Quên mật khẩu?",

        // Home
        hot_trending: "HOT TRENDING",
        featured_sports_title: "Tiêu Điểm Thể Thao & Highlights",
        featured_sports_subtitle: "Tổng hợp tin tức thể thao, clip thi đấu bóng đá, tennis và pickleball mới nhất",
        views: "lượt xem",
        video_badge: "Video",
        latest_posts: "Bài viết mới nhất",
        no_posts_title: "Chưa có bài viết nào.",
        no_posts_desc: "Hiện tại chưa có bài viết nào được đăng tải trên hệ thống.",
        prev: "« Trang trước",
        next: "Trang sau »",
        month_prefix: "THÁNG",

        // Sidebar
        categories_title: "Chuyên mục",
        no_categories: "Chưa có chuyên mục nào.",
        recent_posts_title: "Bài viết mới nhất",
        view_all_news: "XEM TẤT CẢ TIN TỨC",
        no_recent_posts: "Chưa có bài viết nào.",
        most_viewed_title: "Xem nhiều",
        no_archive_posts: "Chưa có bài viết nào.",
        fixtures_title: "Lịch Thi Đấu & Kết Quả",
        tab_today: "Hôm nay",
        tab_fixtures: "Lịch thi đấu",
        tab_results: "Kết quả",
        view_all_fixtures: "Xem toàn bộ bảng xếp hạng & lịch thi đấu",
        recent_comments: "Bình luận mới",
        no_comments: "Chưa có bình luận nào.",

        // Single Post
        reading_time_label: "Thời gian đọc: ~",
        minutes: "phút",
        source_label: "(Theo Người Lao Động)",
        prev_post: "Bài trước",
        next_post: "Bài tiếp theo",
        latest_news_title: "Dòng sự kiện mới nhất",

        // Comments Form
        make_a_post: "Make a Post",
        comment_placeholder: "Bạn đang nghĩ gì...",
        name_placeholder: "Họ và tên *",
        email_placeholder: "Email *",
        website_placeholder: "Website (tùy chọn)",
        share_btn: "Chia sẻ",
        comments_closed: "Bình luận đã được đóng cho bài viết này.",
        comments_count_label: "Bình luận",
        shared_label: "chia sẻ:",
        logged_in_as: "Đăng bình luận với tư cách",
        logout_question: "Đăng xuất?",

        // Search
        search_results_for: "Kết quả tìm kiếm cho:",
        found: "Tìm thấy",
        matching_posts: "bài viết phù hợp.",
        search_not_found_msg: "Chúng tôi không tìm thấy kết quả nào phù hợp với từ khóa của bạn. Bạn có thể thử tìm kiếm lại bằng biểu mẫu bên dưới.",
        search_banner_placeholder: "Tìm kiếm chủ đề, tin tức, từ khóa...",
        search_btn: "Tìm kiếm",

        // Footer
        footer_quick_links: "Khám phá nhanh",
        footer_categories: "Chuyên mục",
        footer_about_us: "Về chúng tôi",
        footer_general_news: "Tin tức tổng hợp",
        footer_about_editorial: "Giới thiệu tòa soạn",
        footer_admin_sys: "Quản trị hệ thống",
        footer_contact_ads: "Liên hệ & Quảng cáo",
        footer_terms: "Điều khoản & Chính sách",
        footer_rights: "Tất cả quyền được bảo lưu.",
        footer_design_by: "Thiết kế chuẩn",
        by: "bởi",

        // Settings Modal
        settings_modal_title: "Cài đặt hệ thống & Giao diện",
        appearance_heading: "Giao diện (Appearance)",
        appearance_desc: "Tùy chỉnh chế độ hiển thị sáng / tối theo sở thích của bạn.",
        theme_light: "Sáng (Light)",
        theme_dark: "Tối (Dark)",
        theme_system: "Hệ thống (System)",
        language_heading: "Ngôn ngữ (Language)",
        language_desc: "Chọn ngôn ngữ ưu tiên hiển thị nội dung trên website.",
        account_heading: "Tài khoản (Account)",
        account_username: "Tên đăng nhập",
        account_email: "Email tài khoản",
        guest_notice: "Bạn đang duyệt với tư cách Khách. Vui lòng đăng nhập để lưu cấu hình tài khoản.",
        btn_cancel: "Hủy bỏ",
        btn_save: "Lưu thay đổi",

        // Months
        m1: "THÁNG 01", m2: "THÁNG 02", m3: "THÁNG 03", m4: "THÁNG 04",
        m5: "THÁNG 05", m6: "THÁNG 06", m7: "THÁNG 07", m8: "THÁNG 08",
        m9: "THÁNG 09", m10: "THÁNG 10", m11: "THÁNG 11", m12: "THÁNG 12",
        uncategorized: "Chưa phân loại"
    },
    en: {
        // Header
        home: "Home",
        search: "Search",
        search_submit: "Search",
        search_placeholder: "Search topics...",
        nav_sports: "Sports",
        nav_science: "Science",
        nav_news: "News",
        menu: "Menu",
        menu_links: "Navigation Menu",
        menu_home: "🏠 Home",
        menu_sports: "⚽ Sports & Highlights",
        menu_science: "🔬 Science & Technology",
        menu_life: "📰 Life & Society",
        menu_football: "🏆 Football",
        menu_fixtures: "📅 Fixtures & Results",
        account: "Account",
        hello: "Hello,",
        admin_panel: "Admin Dashboard",
        settings_title: "Appearance & Profile Settings",
        logout: "Log Out",
        login: "Log In",
        register: "Register",
        forgot_password: "Forgot Password?",

        // Home
        hot_trending: "HOT TRENDING",
        featured_sports_title: "Featured Sports & Highlights",
        featured_sports_subtitle: "Comprehensive sports news, football, tennis, and pickleball clips",
        views: "views",
        video_badge: "Video",
        latest_posts: "Latest Posts",
        no_posts_title: "No posts found.",
        no_posts_desc: "There are currently no posts published on this website.",
        prev: "« Previous",
        next: "Next »",
        month_prefix: "MONTH",

        // Sidebar
        categories_title: "Categories",
        no_categories: "No categories available.",
        recent_posts_title: "Recent Posts",
        view_all_news: "VIEW ALL NEWS",
        no_recent_posts: "No recent posts.",
        most_viewed_title: "Most Viewed",
        no_archive_posts: "No popular articles.",
        fixtures_title: "Fixtures & Live Results",
        tab_today: "Today",
        tab_fixtures: "Fixtures",
        tab_results: "Results",
        view_all_fixtures: "View all standings & fixtures",
        recent_comments: "Recent Comments",
        no_comments: "No comments yet.",

        // Single Post
        reading_time_label: "Reading time: ~",
        minutes: "mins",
        source_label: "(Source: Nguoi Lao Dong)",
        prev_post: "Previous Post",
        next_post: "Next Post",
        latest_news_title: "Latest News Timeline",

        // Comments Form
        make_a_post: "Make a Post",
        comment_placeholder: "What are you thinking...",
        name_placeholder: "Full Name *",
        email_placeholder: "Email Address *",
        website_placeholder: "Website (optional)",
        share_btn: "Share",
        comments_closed: "Comments are closed for this article.",
        comments_count_label: "Comments",
        shared_label: "shared:",
        logged_in_as: "Logged in as",
        logout_question: "Log out?",

        // Search
        search_results_for: "Search results for:",
        found: "Found",
        matching_posts: "matching articles.",
        search_not_found_msg: "We could not find any results for your search. You can give it another try through the search form below.",
        search_banner_placeholder: "Search topics, news, keywords...",
        search_btn: "Search",

        // Footer
        footer_quick_links: "Quick Links",
        footer_categories: "Categories",
        footer_about_us: "About Us",
        footer_general_news: "General News",
        footer_about_editorial: "About Editorial",
        footer_admin_sys: "System Dashboard",
        footer_contact_ads: "Contact & Advertising",
        footer_terms: "Terms & Policies",
        footer_rights: "All rights reserved.",
        footer_design_by: "Designed with",
        by: "by",

        // Settings Modal
        settings_modal_title: "Appearance & Language Settings",
        appearance_heading: "Appearance Mode",
        appearance_desc: "Choose between light or dark display mode based on your preference.",
        theme_light: "Light",
        theme_dark: "Dark",
        theme_system: "System",
        language_heading: "Language",
        language_desc: "Select your preferred display language for the website.",
        account_heading: "User Account",
        account_username: "Username",
        account_email: "Account Email",
        guest_notice: "You are browsing as Guest. Log in to sync your preferences.",
        btn_cancel: "Cancel",
        btn_save: "Save Changes",

        // Months
        m1: "JAN", m2: "FEB", m3: "MAR", m4: "APR",
        m5: "MAY", m6: "JUN", m7: "JUL", m8: "AUG",
        m9: "SEP", m10: "OCT", m11: "NOV", m12: "DEC",
        uncategorized: "Uncategorized"
    }
};

/**
 * HÀM THỰC HIỆN CHUYỂN ĐỔI TOÀN BỘ NGÔN NGỮ (CLIENT-SIDE ENGINE)
 */
function applyLanguage(lang) {
    if (!lang || !I18N_DICTIONARY[lang]) {
        lang = 'vi';
    }
    document.documentElement.lang = (lang === 'en') ? 'en-US' : 'vi-VN';

    var dict = I18N_DICTIONARY[lang];

    // 1. Dịch các phần tử có data-i18n
    var elements = document.querySelectorAll('[data-i18n]');
    elements.forEach(function (el) {
        var key = el.getAttribute('data-i18n');
        if (dict[key]) {
            el.textContent = dict[key];
        }
    });

    // 2. Dịch placeholder các input/textarea
    var placeholders = document.querySelectorAll('[data-i18n-placeholder]');
    placeholders.forEach(function (el) {
        var key = el.getAttribute('data-i18n-placeholder');
        if (dict[key]) {
            el.setAttribute('placeholder', dict[key]);
        }
    });

    // 3. Dịch tháng trên thẻ bài viết (.date-month)
    var dateMonths = document.querySelectorAll('.date-month, .tdc-search-month');
    dateMonths.forEach(function (el) {
        var text = el.textContent.trim().toUpperCase();
        var match = text.match(/(\d{1,2})/);
        if (match) {
            var mNum = parseInt(match[1], 10);
            if (mNum >= 1 && mNum <= 12) {
                el.textContent = (lang === 'en') ? dict['m' + mNum] : ('THÁNG ' + (mNum < 10 ? '0' + mNum : mNum));
            }
        }
    });

    // 4. Dịch nhãn chuyên mục "Chưa phân loại" -> "Uncategorized"
    var catTags = document.querySelectorAll('.module-12-cat-tag, .tdc-fit-cat-link');
    catTags.forEach(function (el) {
        var txt = el.textContent.trim();
        if (txt === 'Chưa phân loại' && lang === 'en') {
            el.textContent = 'Uncategorized';
        } else if (txt === 'Uncategorized' && lang === 'vi') {
            el.textContent = 'Chưa phân loại';
        }
    });

    // 5. Cập nhật giá trị trong dropdown select
    var langSelect = document.getElementById('profileLanguage');
    if (langSelect && langSelect.value !== lang) {
        langSelect.value = lang;
    }
}

/**
 * HÀM CHUYỂN ĐỔI THEME (LIGHT / DARK / SYSTEM)
 */
function applyTheme(theme) {
    document.body.classList.remove('profile-theme-light', 'profile-theme-dark');
    var resolvedTheme = theme;
    if (theme === 'system') {
        resolvedTheme = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : 'light';
    }
    if (resolvedTheme === 'dark') {
        document.body.classList.add('profile-theme-dark');
    } else {
        document.body.classList.add('profile-theme-light');
    }
}

// Áp dụng ngay khi script tải
(function() {
    var savedTheme = localStorage.getItem('profile_theme') || 'system';
    applyTheme(savedTheme);

    var savedLang = localStorage.getItem('profile_language') || 'vi';
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            applyLanguage(savedLang);
        });
    } else {
        applyLanguage(savedLang);
    }
})();

// Khởi tạo các sự kiện modal khi DOM sẵn sàng
document.addEventListener('DOMContentLoaded', function () {
    var openBtn       = document.getElementById('openProfileSettings');
    var overlay       = document.getElementById('profileSettingsOverlay');
    var closeBtn      = document.getElementById('closeProfileSettings');
    var cancelBtn     = document.getElementById('cancelProfileSettings');
    var saveBtn       = document.getElementById('saveProfileSettings');
    var langSelect    = document.getElementById('profileLanguage');
    var lightRadio    = document.getElementById('profileThemeLight');
    var darkRadio     = document.getElementById('profileThemeDark');
    var systemRadio   = document.getElementById('profileThemeSystem');

    function loadSettings() {
        var curTheme = localStorage.getItem('profile_theme') || 'system';
        var curLang  = localStorage.getItem('profile_language') || 'vi';

        if (curTheme === 'light' && lightRadio) lightRadio.checked = true;
        else if (curTheme === 'dark' && darkRadio) darkRadio.checked = true;
        else if (systemRadio) systemRadio.checked = true;

        if (langSelect) langSelect.value = curLang;
    }

    function openModal() {
        if (!overlay) return;
        loadSettings();
        overlay.classList.add('show');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('settings-open');
    }

    function closeModal() {
        if (!overlay) return;
        overlay.classList.remove('show');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('settings-open');
    }

    if (openBtn) {
        openBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var accountDropdown = document.getElementById('headerAccountDropdown');
            if (accountDropdown) accountDropdown.classList.remove('show');
            openModal();
        });
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeModal();
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay && overlay.classList.contains('show')) {
            closeModal();
        }
    });

    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            var checkedTheme = document.querySelector('input[name="profile_theme"]:checked');
            var theme = checkedTheme ? checkedTheme.value : 'system';
            var lang  = langSelect ? langSelect.value : 'vi';

            // Lưu cấu hình
            localStorage.setItem('profile_theme', theme);
            localStorage.setItem('profile_language', lang);
            document.cookie = "profile_language=" + lang + ";path=/;max-age=31536000";

            // Áp dụng ngay lập tức toàn bộ
            applyTheme(theme);
            applyLanguage(lang);

            closeModal();
        });
    }
});
</script>
