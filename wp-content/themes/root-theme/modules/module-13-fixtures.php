<?php
/**
 * Module 13: Lịch Thi Đấu & Kết Quả Thể Thao (Tự chọn 2)
 * Vị trí: Sidebar / Shortcode [tdc_sports_fixtures]
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="custom-module-13-fixtures-widget">
    <div class="module-13-header">
        <div class="module-13-title-wrap">
            <i class="fa fa-futbol-o module-13-icon"></i>
            <h4 class="module-13-title" data-i18n="fixtures_title">Lịch Thi Đấu & Kết Quả</h4>
        </div>
        <div class="module-13-live-indicator">
            <span class="pulse-dot"></span> LIVE
        </div>
    </div>

    <!-- TABS -->
    <div class="module-13-tabs">
        <button type="button" class="module-13-tab-btn active" onclick="switchModule13Tab(event, 'tab-today')" data-i18n="tab_today">
            Hôm nay
        </button>
        <button type="button" class="module-13-tab-btn" onclick="switchModule13Tab(event, 'tab-fixtures')" data-i18n="tab_fixtures">
            Lịch thi đấu
        </button>
        <button type="button" class="module-13-tab-btn" onclick="switchModule13Tab(event, 'tab-results')" data-i18n="tab_results">
            Kết quả
        </button>
    </div>

    <!-- TAB 1: HÔM NAY (ĐANG DIỄN RA / SẮP ĐÁ) -->
    <div class="module-13-panel active" id="tab-today">
        <div class="module-13-match-row is-live-match">
            <div class="module-13-tournament-tag">
                <span>Premier League</span>
                <span class="match-time-tag"><i class="fa fa-clock-o"></i> 78'</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Arsenal</span>
                    <span class="team-score">2</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-live">LIVE 78'</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">1</span>
                    <span>Chelsea</span>
                </div>
            </div>
        </div>

        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>La Liga</span>
                <span class="match-time-tag"><i class="fa fa-clock-o"></i> 22:30</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Real Madrid</span>
                    <span class="team-score">-</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">22:30</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">-</span>
                    <span>Barcelona</span>
                </div>
            </div>
        </div>

        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>V-League 1</span>
                <span class="match-time-tag"><i class="fa fa-clock-o"></i> 19:15</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Hà Nội FC</span>
                    <span class="team-score">-</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">19:15</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">-</span>
                    <span>HAGL</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: LỊCH THI ĐẤU -->
    <div class="module-13-panel" id="tab-fixtures">
        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>Champions League</span>
                <span class="match-time-tag"><i class="fa fa-calendar"></i> 02:00</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Man City</span>
                    <span class="team-score">-</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">02:00</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">-</span>
                    <span>Bayern Munich</span>
                </div>
            </div>
        </div>

        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>Premier League</span>
                <span class="match-time-tag"><i class="fa fa-calendar"></i> 18:30</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Liverpool</span>
                    <span class="team-score">-</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">18:30</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">-</span>
                    <span>Man United</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: KẾT QUẢ -->
    <div class="module-13-panel" id="tab-results">
        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>Premier League</span>
                <span class="match-time-tag">FT</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Tottenham</span>
                    <span class="team-score">3</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">FT</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">1</span>
                    <span>Aston Villa</span>
                </div>
            </div>
        </div>

        <div class="module-13-match-row">
            <div class="module-13-tournament-tag">
                <span>Serie A</span>
                <span class="match-time-tag">FT</span>
            </div>
            <div class="module-13-match-teams">
                <div class="module-13-team team-home">
                    <span>Inter Milan</span>
                    <span class="team-score">2</span>
                </div>
                <div class="module-13-status-center">
                    <span class="badge-status">FT</span>
                </div>
                <div class="module-13-team team-away">
                    <span class="team-score">0</span>
                    <span>Juventus</span>
                </div>
            </div>
        </div>
    </div>

    <div class="module-13-footer">
        <a href="<?php echo esc_url(home_url('/?s=the-thao')); ?>" class="module-13-all-link">
            <span data-i18n="view_all_fixtures">Xem toàn bộ bảng xếp hạng & lịch thi đấu</span> <i class="fa fa-angle-right"></i>
        </a>
    </div>
</div>

<script>
function switchModule13Tab(evt, tabId) {
    var parent = evt.currentTarget.closest('.custom-module-13-fixtures-widget');
    if (!parent) return;
    var buttons = parent.querySelectorAll('.module-13-tab-btn');
    var panels = parent.querySelectorAll('.module-13-panel');
    buttons.forEach(function(btn) { btn.classList.remove('active'); });
    panels.forEach(function(p) { p.classList.remove('active'); });
    evt.currentTarget.classList.add('active');
    var target = parent.querySelector('#' + tabId);
    if (target) {
        target.classList.add('active');
    }
}
</script>
