# CMS-JOB-DESIGN — Team AI coding rules (Codex + Antigravity)

> **ALWAYS APPLY** when working in this repository. These are project-level rules; do not treat a generic user request to "finish everything" as authorization to change other people's pages, shared files, data, or Git history. The user's direct, explicit page/scope assignment may refine the task, but changes to protected/shared files still require project-lead agreement.

## 1. Project invariants

- WordPress + **JobScout**, **WP Job Manager**, WP Job Manager Extra Fields, WAMP64 (Windows). Keep WordPress APIs, template hierarchy, native post/page types, and existing CMS records.
- Repo `trancaotrong456/CMS-JOB-DESIGN`. **`main` is integration only**; do not introduce or use a `develop` workflow. Everyone works on their assigned branch.
- Do not migrate frameworks, rebuild WordPress from scratch, install a new theme, or rewrite unrelated parts. Prefer the smallest task-focused change.
- Treat local files and MySQL as user data. **NEVER** `git reset --hard`, `git clean -fd`, force-push, overwrite uncommitted work, delete/replace a local database, or import `database/cms_job_design.sql` into an existing database.
- `wp-config.local.php` is machine-specific and ignored. Never commit credentials, salts, local exports, real contact information, or transient backups. `wp-config.php` is the shared tracked loader and must not be modified for page work.
- This rule is used by BOTH Codex and Antigravity. Further details: `docs/TEAM_WORKFLOW.md` and `docs/UI_REFERENCE.md`. Read both before coding a page.

## 2. Branch ownership: do not cross boundaries

| Owner | Feature branch | Page responsibility | Allowed page-local entrypoints (examples; verify routing first) |
|---|---|---|---|
| Trần Cao Trọng (lead) | `tran-cao-trong/home` | Home + shared Header/Footer/design tokens | `front-page.php`, `template-parts/home-content.php`, `css/home.css`; shared files only via lead |
| Trần Văn Trọng | `tran-van-trong/all-jobs` | All Jobs | `page-jobs.php` / appropriate Jobs route, `assets/css/all-jobs.css`, `template-parts/all-jobs-*.php` |
| Trần Văn Trọng | `tran-van-trong/job-detail` | Job Detail | `single-job_listing.php` and page-local CSS / partials |
| Nguyễn Hữu Chúc | `nguyen-huu-chuc/news` | News listing | existing `page-news.php`, `assets/css/news.css`; use `home.php` only after verifying the posts-index route |
| Nguyễn Hữu Chúc | `nguyen-huu-chuc/news-detail` | News Detail | existing `single-post.php`, `assets/css/news-detail.css` |
| Hồ Hoàng Đức Huy | `ho-hoang-duc-huy/about` | About | `page-about.php`, `assets/css/about.css`, About-only partials |
| Nguyễn Hữu Phong | `nguyen-huu-phong/contact` | Contact | `page-contact.php`, `assets/css/contact.css`, Contact-only partials |

- **Branch ≠ person:** even if one person owns two pages, implement each page on its own listed feature branch. Before editing, verify `git branch --show-current` and `git status`.
- Only edit files needed for your assigned page. When an example path does not exist, create a new uniquely named page file; never repurpose a page owned by someone else. Scope all HTML/CSS classes with a page prefix (`pds-news-`, `pds-job-detail-`, `pds-about-`, `pds-contact-`, etc.) to avoid cross-page selectors.
- **Shared protected files:** `header.php`, `footer.php`, `functions.php`, `style.css`, `front-page.php`, `css/home.css`, `inc/custom-functions.php`, `inc/extras.php`, `inc/template-functions.php`, `wp-config.php`, `database/**`, `scripts/wamp64-team.ps1`, global CSS/JS, menu registration and theme settings. Only the lead handles shared UI changes after coordination; `front-page.php` and `css/home.css` are also Home-owned. Other agents must not touch these even just to enqueue a stylesheet; use page-scoped templates/hooks when possible. If shared routing or enqueue requires a global edit, **STOP and ask lead to allocate the integration edit**.
- A branch can never own WordPress core, plugin source, other members' templates, or the full `wp-content/uploads` directory. Images for content should use Media Library / existing seeded assets; page-specific new assets should be placed under a clearly named theme asset folder, with author/source documented.
- Do not combine multiple pages or shared changes in one commit. Don't silently modify another team's pages to 'make tests pass'.

## 3. Screenshot-first implementation, 7 reference screens

**Source of truth:** use the files in `docs/ui-reference/` (actual uploaded screenshots), and read `docs/UI_REFERENCE.md` for page mapping and acceptance criteria. Never rely on rough guesses when the images are available. If missing, report it; do not claim visual parity.

1. `01-home.png` → Home (`tran-cao-trong/home`): Kyoto dusk hero/search, 6 Top Jobs, Career With Us, 4 newest posts, orange newsletter, shared footer.
2. `02-about.png` → About (`ho-hoang-duc-huy/about`): Japanese street hero, "OMOTENASHI", two-column vision/mission/value, facts + photos.
3. `03-news.png` → News (`nguyen-huu-chuc/news`): Tokyo skyline hero, 2-column editorial cards.
4. `04-all-jobs.png` → All Jobs (`tran-van-trong/all-jobs`): bamboo hero, sort selector, 2-column job cards, Load More.
5. `05-job-detail.png` → Job Detail (`tran-van-trong/job-detail`): breadcrumb/summary, share/apply, content + right sidebar, related jobs.
6. `06-news-detail.png` → News Detail (`nguyen-huu-chuc/news-detail`): breadcrumb/summary/share, article/media, latest posts.
7. `07-contact.png` → Contact (`nguyen-huu-phong/contact`): Japanese umbrella hero, centered address, employer/jobseeker columns.

**Shared visual contract:** white header; logo left; Home / Jobs / News / About / Contact right; orange SUBMIT JOB CTA; orange primary accents; light-gray page bands, white content cards, dark text; orange newsletter stripe and consistent footer. The black rectangles in reference screenshots redact identities/content: **DO NOT recreate the black redaction bars.** Use existing brand/logo content if available or a neutral text fallback; do not fabricate masked phone numbers.

- Match **layout, typography hierarchy, spacing, aspect ratios, cards, colors, alignment, active menu, and content density** before adding effects/animations. Respect desktop reference and responsive widths 1440, 1024, 768, 390 px (adapt rather than stretch the full-page screenshots).
- Validate with a locally rendered **screenshot comparison** against the matching reference; explain differences and fix high-impact ones. If you cannot capture screenshots, explicitly say visual validation is incomplete.
- Do NOT use hard-coded duplicate jobs/news rows to imitate repeated example cards. Query live WordPress content; paginate/load more actual records. Do not modify seed/MySQL to inflate demo content.
- Use existing CMS titles/images/links and WordPress escaping (`esc_html`, `esc_url`, `esc_attr`, `wp_kses_post`); no static fake URLs, bogus phone numbers, misleading working newsletter/subscriptions or fake "submit success".

## 4. WordPress / CSS integration constraints

- Check actual routing before implementation: a page selected as `page_for_posts` normally uses `home.php` (not automatically `page-news.php`); Jobs shortcode page vs `job_listing` archive can use different templates. Confirm with rendered URL and WordPress template hierarchy. Don't break Home `front-page.php`.
- Templates use `get_header()` and `get_footer()` for shared chrome; don't copy shared header/footer markup into each page.
- Enqueue page CSS only for its page; prefer unique handles; do not override `.site-header`, `.site-footer`, `body`, `.container`, `h1` globally to fix one page. Use a root page wrapper class.
- Keep **Submit Job**, Job Detail, News Detail, navigation, sort/search, image links, Apply/Share, and Load More functional where required; if an action needs backend work, preserve WordPress/plugin behavior and document the missing capability instead of faking it.
- Preserve existing local menus, settings, content, uploads. Do not run Setup/Sync during pure visual changes unless local environment is broken and the owner requests it. If Sync is necessary, use approved script with backup and add-only semantics.

## 5. Safe Git / delivery protocol (every task)

1. **Before**: `git status --short --branch`, `git branch --show-current`, `git fetch origin`; verify you're on the assigned feature branch, not `main` / `develop`; review the target page and references.
2. Integrate `origin/main` safely only after protecting local changes. Fast-forward when possible; otherwise normal merge (never reset/force). **If conflicts, stop rather than choosing ours/theirs**. Recent News / News Detail / Contact branches may already diverge from `main`.
3. State **exact intended files** before edits; if a protected/shared file is required, stop and ask lead. Make small commits with page-specific titles, e.g. `feat(news): match reference layout`.
4. **After**: run PHP lint on changed PHP; run `git diff --check`; run `scripts/check-team-scope.ps1` for staged files (if available); inspect `git diff --name-only`; test page links, CMS queries, empty state, desktop/mobile screenshots; check `git status` and preserve SQL/config.
5. Push **only your own branch** if the user explicitly requests push; never directly push `main` from a page agent. Integration into `main` is the project lead's task after review and tests.
6. Final report (short): branch/commit, owned files changed, screenshots checked, functionality tested, unresolved visual differences/404s/conflicts, any pending shared-file request. Do not claim tests not actually run.

**Success definition:** assigned page matches its screenshot closely and works with real WordPress content, with zero unrelated edits, no overwritten teammate changes, and a clean reviewable Git diff.
