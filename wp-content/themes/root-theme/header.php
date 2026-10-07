<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<!-- MODULE (1) - MAIN HEADER (BOOTSTRAP NAVBAR) -->
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">Group C</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="<?php echo esc_url(home_url('/')); ?>">Home <span class="sr-only">(current)</span></a></li>
      </ul>
      <form class="navbar-form navbar-left" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <div class="form-group">
          <input type="text" name="s" class="form-control" placeholder="Search" value="<?php echo esc_attr(get_search_query()); ?>">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="<?php echo esc_url(home_url('/?s=the-thao')); ?>">Thể thao</a></li>
        <li><a href="<?php echo esc_url(home_url('/?s=khoa-hoc')); ?>">Khoa học</a></li>
        <li><a href="<?php echo esc_url(home_url('/?s=tin-tuc')); ?>">Tin tức</a></li>
        <li><a href="#"><i class="fa fa-ellipsis-h"></i> Menu</a></li>
        <li><a href="#"><i class="fa fa-search"></i> Search</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-user-circle"></i> Account <span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            <?php if (is_user_logged_in()) : ?>
                <?php $current_user = wp_get_current_user(); ?>
                <li><a href="#">Hello, <?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?> (<?php echo esc_html(implode(', ', $current_user->roles)); ?>)</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="<?php echo esc_url(admin_url()); ?>">Admin Panel</a></li>
                <li><a href="#" id="openSettingsModalBtn" onclick="document.getElementById('tdcSettingsModal').style.display='flex'; return false;"><i class="fa fa-cog"></i> Cài đặt</a></li>
                <li><a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Logout</a></li>
            <?php else : ?>
                <li><a href="<?php echo esc_url(wp_login_url()); ?>">Login</a></li>
                <li><a href="<?php echo esc_url(site_url('wp-login.php?action=register')); ?>">Register</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#" id="openSettingsModalBtnGuest" onclick="document.getElementById('tdcSettingsModal').style.display='flex'; return false;"><i class="fa fa-cog"></i> Cài đặt</a></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<!-- MODULE - PROFILE SETTINGS MODAL & I18N -->
<?php
if (file_exists(get_template_directory() . '/modules/profile-settings.php')) {
    include get_template_directory() . '/modules/profile-settings.php';
}
?>

<!-- MODAL CÀI ĐẶT (Giao diện / Ngôn ngữ / Tài khoản) -->
<div id="tdcSettingsModal" style="display:none;" class="tdc-settings-overlay" role="dialog" aria-modal="true" aria-labelledby="tdcSettingsTitle" onclick="if(event.target===this)this.style.display='none'">
  <div class="tdc-settings-dialog">
    <!-- Header -->
    <div class="tdc-settings-header">
      <h2 id="tdcSettingsTitle" class="tdc-settings-title">Cài đặt</h2>
      <button type="button" class="tdc-settings-close" onclick="document.getElementById('tdcSettingsModal').style.display='none'" aria-label="Đóng">&times;</button>
    </div>

    <!-- Body -->
    <div class="tdc-settings-body">

      <!-- 1. GIAO DIỆN -->
      <section class="tdc-settings-section">
        <h3 class="tdc-settings-section-title">Giao diện</h3>
        <p class="tdc-settings-section-desc">Chọn giao diện hiển thị cho website.</p>
        <div class="tdc-settings-theme-options" role="radiogroup" aria-label="Giao diện">
          <label class="tdc-settings-radio-label">
            <input type="radio" name="tdc_theme" value="light" id="tdcThemeLight" checked>
            <span class="tdc-settings-radio-text">Sáng</span>
          </label>
          <label class="tdc-settings-radio-label">
            <input type="radio" name="tdc_theme" value="dark" id="tdcThemeDark">
            <span class="tdc-settings-radio-text">Tối</span>
          </label>
          <label class="tdc-settings-radio-label">
            <input type="radio" name="tdc_theme" value="system" id="tdcThemeSystem">
            <span class="tdc-settings-radio-text">Hệ thống</span>
          </label>
        </div>
      </section>

      <!-- Divider -->
      <hr class="tdc-settings-divider">

      <!-- 2. NGÔN NGỮ -->
      <section class="tdc-settings-section">
        <h3 class="tdc-settings-section-title">Ngôn ngữ</h3>
        <p class="tdc-settings-section-desc">Chọn ngôn ngữ hiển thị.</p>
        <select id="tdcLanguageSelect" class="tdc-settings-select" aria-label="Ngôn ngữ">
          <option value="vi" selected>Tiếng Việt</option>
          <option value="en">English</option>
        </select>
      </section>

      <!-- Divider -->
      <hr class="tdc-settings-divider">

      <!-- 3. TÀI KHOẢN -->
      <section class="tdc-settings-section">
        <h3 class="tdc-settings-section-title">Tài khoản</h3>
        <div class="tdc-settings-field">
          <label class="tdc-settings-field-label" for="tdcUsernameDisplay">Tên đăng nhập</label>
          <?php if (is_user_logged_in()) : $cu = wp_get_current_user(); ?>
            <input type="text" id="tdcUsernameDisplay" class="tdc-settings-input" value="<?php echo esc_attr($cu->user_login); ?>" readonly>
          <?php else : ?>
            <input type="text" id="tdcUsernameDisplay" class="tdc-settings-input" value="Khách" readonly>
          <?php endif; ?>
        </div>
      </section>

    </div><!-- /.tdc-settings-body -->

    <!-- Footer -->
    <div class="tdc-settings-footer">
      <button type="button" class="tdc-settings-btn-cancel" onclick="document.getElementById('tdcSettingsModal').style.display='none'">Hủy</button>
      <button type="button" class="tdc-settings-btn-save" id="tdcSettingsSaveBtn">Lưu thay đổi</button>
    </div>
  </div>
</div>

<script>
(function () {
  'use strict';

  // Đọc localStorage khi trang tải
  var savedTheme = localStorage.getItem('tdc_theme') || 'light';
  var savedLang  = localStorage.getItem('tdc_lang')  || 'vi';

  // Áp dụng theme
  function applyTheme(theme) {
    if (theme === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    } else if (theme === 'system') {
      var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      document.documentElement.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
    } else {
      document.documentElement.setAttribute('data-theme', 'light');
    }
  }
  applyTheme(savedTheme);

  document.addEventListener('DOMContentLoaded', function () {
    // Set radio
    var radioEl = document.querySelector('input[name="tdc_theme"][value="' + savedTheme + '"]');
    if (radioEl) radioEl.checked = true;

    // Set language
    var langEl = document.getElementById('tdcLanguageSelect');
    if (langEl) langEl.value = savedLang;

    // Save button
    var saveBtn = document.getElementById('tdcSettingsSaveBtn');
    if (saveBtn) {
      saveBtn.addEventListener('click', function () {
        var theme = document.querySelector('input[name="tdc_theme"]:checked');
        var lang  = document.getElementById('tdcLanguageSelect');

        if (theme) {
          localStorage.setItem('tdc_theme', theme.value);
          applyTheme(theme.value);
        }
        if (lang) {
          localStorage.setItem('tdc_lang', lang.value);
        }

        // Feedback
        saveBtn.textContent = '✓ Đã lưu!';
        saveBtn.style.background = '#16a34a';
        setTimeout(function () {
          saveBtn.textContent = 'Lưu thay đổi';
          saveBtn.style.background = '';
          document.getElementById('tdcSettingsModal').style.display = 'none';
        }, 1200);
      });
    }
  });
}());
</script>

<!-- JAVASCRIPT - MODULE 1 (HEADER INTERACTIONS) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var searchIconBtn = document.getElementById('headerSearchIconBtn');
    var searchInput   = document.getElementById('header-search-input');
    var searchForm    = document.getElementById('headerSearchForm');

    if (searchIconBtn && searchForm) {
        searchIconBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (searchInput && searchInput.value.trim() !== '') {
                searchForm.submit();
            } else if (searchInput) {
                searchInput.focus();
                searchInput.classList.add('highlight-search');
                setTimeout(function () {
                    searchInput.classList.remove('highlight-search');
                }, 1500);
            }
        });
    }

    var menuBtn         = document.getElementById('headerMenuBtn');
    var menuDropdown    = document.getElementById('headerMenuDropdown');
    var accountDropdown = document.getElementById('headerAccountDropdown');
    var dropdownBtn     = document.getElementById('accountDropdownBtn');

    if (menuBtn && menuDropdown) {
        menuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            menuDropdown.classList.toggle('show');
            menuBtn.setAttribute('aria-expanded', menuDropdown.classList.contains('show'));
            if (accountDropdown) {
                accountDropdown.classList.remove('show');
                if (dropdownBtn) dropdownBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    if (accountDropdown && dropdownBtn) {
        dropdownBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            accountDropdown.classList.toggle('show');
            dropdownBtn.setAttribute('aria-expanded', accountDropdown.classList.contains('show'));
            if (menuDropdown) {
                menuDropdown.classList.remove('show');
                if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.addEventListener('click', function (e) {
        if (menuDropdown && menuBtn && !menuBtn.contains(e.target)) {
            menuDropdown.classList.remove('show');
            menuBtn.setAttribute('aria-expanded', 'false');
        }
        if (accountDropdown && !accountDropdown.contains(e.target)) {
            accountDropdown.classList.remove('show');
            if (dropdownBtn) dropdownBtn.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>
