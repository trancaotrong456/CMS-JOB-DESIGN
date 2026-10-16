# CMS-JOB-DESIGN — Quy tắc AI chung (Antigravity + Codex)

> Luôn áp dụng khi agent làm việc trong repository `trancaotrong456/CMS-JOB-DESIGN`.
> **Mục tiêu:** 5 thành viên cùng viết code WordPress đúng 7 ảnh mẫu, giữ được công việc của nhau, chia sẻ bối cảnh qua Git mà không phải giải thích lại trong từng cuộc chat.
> Rule lưu tri thức dự án và hướng dẫn agent; **không phải huấn luyện/training lại trọng số mô hình AI**.

## A. Nguồn sự thật và ưu tiên

1. **Dữ liệu và quyền sở hữu:** bảo toàn source, thay đổi chưa commit, MySQL, thông tin riêng từng máy. Không phá hủy để hoàn tất tác vụ.
2. **Yêu cầu cụ thể hiện tại:** chỉ triển khai tính năng/trang người dùng giao, không tự thêm tính năng hay đại tu.
3. **Code đang có + WordPress runtime + Git hiện tại:** là nguồn sự thật về cái gì đã triển khai; nhật ký có thể cũ.
4. **7 ảnh tham chiếu:** là chuẩn trình bày UI; tham chiếu `docs/ai-team/SCREEN_GUIDE.md` và `docs/ui-reference/`.
5. **Quy tắc trong file này và các rule chi tiết:** xem `.agents/rules/`; kiểm tra `docs/ai-team/PROJECT_CONTEXT.md` trước khi làm task lớn.
6. Nếu nguồn mâu thuẫn: báo chỗ mâu thuẫn, chọn phương án không phá code/dữ liệu; nếu đòi sửa file của người khác thì xin phê duyệt rõ ràng của trưởng nhóm.

**Không áp dụng rule Laravel Travel Planner cho repo này:** không bắt MVC Service Interfaces, Apache Solr, Docker CI, Google Drive sync, hay nhánh `master`/`develop`.

## B. Tech stack và cách làm

- WordPress + theme JobScout + WP Job Manager + WP Job Manager Extra Fields; PHP/HTML/CSS/JS thuần trong theme hiện có; WAMP64 Windows cho local.
- Ưu tiên **WordPress template hierarchy, WP_Query, WordPress APIs, esc_html/esc_attr/esc_url, wp_kses_post, nonce, sanitize input** phù hợp ngữ cảnh.
- Template trang dùng `get_header()` / `get_footer()` thay vì sao chép markup Header/Footer.
- CSS/JS chỉ áp dụng trong một trang: prefix class theo feature, ví dụ `.pds-news-`, `.pds-job-detail-`, `.pds-contact-`.
- Tránh sửa global selectors (`body`, `.container`, `h1`, `.site-header`, `.site-footer`), `functions.php`, hook/menu toàn site chỉ để sửa một trang.
- Giữ code đơn giản, rõ ràng, tên hàm/biến mô tả được mục đích; comment kỹ thuật ngắn bằng tiếng Việt khi cần. Không refactor ngoài phạm vi.
- **Không mock nghiệp vụ:** jobs/news đọc từ CMS thật, không tạo hàng loạt dữ liệu trùng để lấp đầy ảnh. Không dựng nút/link/form trông hoạt động nhưng không có hành vi thực.

## C. Phân công cố định — kiểm tra branch trước khi sửa

| Branch | Người phụ trách | Phần được sửa (kiểm tra template hiện có trước) |
|---|---|---|
| `tran-cao-trong/home` | Trưởng nhóm Trần Cao Trọng | Home, Header/Footer/chrome chung, tích hợp chung |
| `tran-van-trong/all-jobs` | Trần Văn Trọng | All Jobs, CSS/JS/partials riêng trang |
| `tran-van-trong/job-detail` | Trần Văn Trọng | Job Detail, CSS/JS/partials riêng trang |
| `nguyen-huu-chuc/news` | Nguyễn Hữu Chúc | News, CSS/JS/partials riêng trang |
| `nguyen-huu-chuc/news-detail` | Nguyễn Hữu Chúc | News Detail, CSS/JS/partials riêng trang |
| `ho-hoang-duc-huy/about` | Hồ Hoàng Đức Huy | About, CSS/JS/partials riêng trang |
| `nguyen-huu-phong/contact` | Nguyễn Hữu Phong | Contact, CSS/JS/partials riêng trang |

**Phân quyền theo FEATURE, không chỉ tên người.** Người phụ trách News không tự sửa News Detail khi đứng ở branch News.

- Chỉ sửa file theo trang đang phụ trách. Trước khi tạo file mới, tìm xem trang đã được triển khai trong template, shortcode, CMS Page hoặc branch hiện tại chưa.
- Các file **chung được bảo vệ**: `wp-content/themes/jobscout/header.php`, `footer.php`, `functions.php`, `style.css`, `front-page.php`, `css/home.css`, `inc/custom-functions.php`, `inc/extras.php`, `inc/template-functions.php`, menu/URL chung, các global CSS/JS, `wp-config.php`, `database/**`, `scripts/wamp64-team.ps1`.
- Thành viên khác chỉ READ/ANALYZE/REFERENCE file chung và file người khác. Nếu thực sự cần sửa file chung để đăng ký template/enqueue/routing, **DỪNG ở file đó** và gửi trưởng nhóm yêu cầu sửa tối thiểu; đừng lách bằng copy Header/Footer sang template của mình.
- Mọi người có thể đọc toàn repo và lịch sử Git để hiểu tích hợp. **Không suy ra quyền sửa từ quyền đọc.**

## D. Quy trình một tác vụ

**TRƯỚC KHI CODE:**
1. Xác nhận đúng repo và branch (`git rev-parse --show-toplevel`, `git branch --show-current`, `git status --short --branch`). Nếu đứng ở `main` hoặc sai branch thì không viết tính năng.
2. Kiểm tra `origin/main`, branch của mình và các thay đổi chưa commit. `git fetch origin` chỉ khi có network; không giả định fetch thành công.
3. Đọc `docs/ai-team/PROJECT_CONTEXT.md`, `docs/ai-team/SCREEN_GUIDE.md`, file ảnh đúng trang, log liên quan, template PHP/CSS hiện có.
4. Xác định phạm vi file dự định sửa và xác nhận không chồng chéo. Nếu người dùng gửi lại ảnh cũ, **kiểm tra code trước**, phân loại NEW/EXISTING/PARTIAL/BROKEN; không viết lại mặc định.

**KHI CODE:**
5. Thực hiện thay đổi nhỏ nhất để đáp ứng yêu cầu. Không sửa chức năng khác hoặc tự tạo API, DB table, menu, theme options, plugin mới.
6. Code theo ảnh (section, spacing, typography, color, position, responsiveness), kết nối dữ liệu thực; xử lý trạng thái không có dữ liệu và lỗi hợp lý.
7. Nếu task gián đoạn, ghi tiến độ và bước tiếp theo vào nhật ký riêng của feature **khi còn khả năng lưu**; không tuyên bố hoàn tất.

**SAU KHI CODE:**
8. PHP lint các file PHP có sửa, `git diff --check`, đọc diff và test đường dẫn/CTA/CMS thực tế.
9. Nếu thay đổi UI: chụp/so sánh với ảnh đúng trang ở desktop và mobile khi có khả năng; không có trình duyệt thì nói chưa xác minh trực quan.
10. Chỉ stage file được phép, không `git add .`/`git add -A` khi repo có file chưa commit của người khác. Nếu có `scripts/check-team-scope.ps1` thì chạy trước commit.
11. Chỉ commit mốc đã kiểm tra; **không auto push/merge vào main** khi chưa được giao rõ ràng. Push chỉ branch được phân công, không force push.
12. Sau thay đổi đáng kể, ghi 1 log mới theo mẫu trong `docs/ai-team/history/README.md`. Không tự động sửa cả project chỉ vì có rule mới.
13. Báo cáo bằng tiếng Việt: branch, files changed, kiểm tra thực sự đã chạy, sai khác UI, lỗi, việc cần trưởng nhóm hỗ trợ.

## E. Git, database và môi trường — giới hạn an toàn

- **Không chạy:** `git reset --hard`, `git clean`, force push, xóa worktree/branch, overwrite uncommitted work, tự động chọn `ours/theirs` khi conflict.
- Pull chỉ branch cá nhân **không đồng nghĩa** đã lấy code Home từ `main`. Cần tích hợp `origin/main` an toàn khi worktree sạch/đã được bảo vệ. Branch diverged: kiểm tra lịch sử và giải quyết có kiểm soát, không reset/rebase tùy tiện.
- Không sửa/commit `wp-config.local.php` (được ignore), secrets, salts, `.env`, SQL backup; `wp-config.php` là bộ nạp cấu hình tracked, không ghi thông tin local vào đó.
- Không import `database/cms_job_design.sql` lên DB đang dùng, không drop/update WordPress tables hay chạy seed/Sync để "sửa giao diện". Nếu yêu cầu đồng bộ dữ liệu, dùng `scripts/wamp64-team.ps1 -Action Check`, và `Sync` **chỉ sau backup thành công và đồng ý tác vụ**, giữ nguyên bản ghi hiện có.
- Không tự đổi `home`, `siteurl`, VirtualHost, user WP Admin, đường dẫn máy hoặc port của người khác.
- Không chỉnh WordPress core (`wp-admin`, `wp-includes`) hoặc source plugin khi làm giao diện.
- Không push media phát sinh ngẫu nhiên trong `wp-content/uploads/`, không đụng vùng dữ liệu của người khác.

## F. Chất lượng và nghiệm thu

- **Image first:** đối chiếu `docs/ui-reference/` theo `docs/ai-team/SCREEN_GUIDE.md`; ô bôi đen trong ảnh là che thông tin, **không dựng thành phần bôi đen**.
- Kiểm tra các kích thước hợp lý 1440 / 1024 / 768 / 390 px, không làm tràn ngang, không phá Header/Footer chung.
- Các trang cần hoạt động trên dữ liệu WP thật: Home, All Jobs, Job Detail, News, News Detail, About, Contact; CTA Submit Job có hành vi plugin đúng; không báo 200 là bằng chứng pixel-perfect.
- Với công việc nhỏ: kiểm thử theo phạm vi. Trước merge tích hợp: kiểm tra tất cả route quan trọng. Không bắt Docker CI/Laravel Tests giả nếu repo không có.
- Khi không thể kiểm tra một mục, đánh dấu **CHƯA XÁC MINH**; đừng viết "PASS" khi chỉ mới đoán.

## G. Ghi nhớ chung, không cần Google Drive

- `docs/ai-team/PROJECT_CONTEXT.md`: đặc tả bối cảnh do trưởng nhóm quản lý, đọc trước task lớn.
- `docs/ai-team/history/`: nhật ký theo branch/phiên dưới dạng file **mới**, tránh nhiều người cùng sửa một file trạng thái chung.
- Luôn đọc log liên quan mới nhất, nhưng nếu khác code và `git log` thì ưu tiên code hiện tại và nói rõ lịch sử đã cũ.
- Lưu quyết định, file sửa, test, chặn xung đột, next steps. Không lưu mật khẩu, file SQL toàn phần, thông tin cá nhân hoặc chat raw.
- Nhật ký chia sẻ qua Git **sau khi được commit/merge**, không phải AI đọc được lịch sử chat riêng của người khác theo thời gian thực. Không hứa đồng bộ liên tục.
