# Shared CMS content

This directory contains an add-only WordPress API seed and a sanitized content snapshot for the shared reference data.

## Recommended setup and sync

For WAMP64, follow [the team setup guide](../docs/WAMP64-SETUP.md). The PowerShell sync creates a database backup outside the repository before invoking the seed. It does not import the SQL snapshot.

The seed creates missing reference jobs, News posts, About/Contact/News pages, categories, settings that are absent, and the seven shared images. A post with the same seed key is left untouched on later runs. An unrelated post with a matching title or slug is reported as a conflict and preserved. Existing site options are not changed.

With WP-CLI, the seed can also be run from the WordPress root:

```sh
wp eval-file database/seed-cms-content.php
```

Back up the database first when running it manually. WP Job Manager and its required taxonomies must be active.

## Sanitized SQL snapshot

`cms_job_design.sql` contains selected content tables and safe settings only. It excludes user accounts, user metadata, comments, and credentials. Use it only with a newly created local WordPress database whose content tables are still empty and whose `wp_` prefix matches. Create a separate local administrator before importing, so imported author IDs resolve to that local admin. Copy the shared image directory from `wp-content/uploads/seed-cms-content/` into the same path in the new site.

Do not import this snapshot into an existing personal database. SQL import can collide with content IDs and is not the safe sync mechanism. The export is not a full WordPress backup or installation.

## Reference data limitations

- The seven included photographs were generated for this project because no licensed reference assets were available. They are illustrative and are not original company photography.
- No matching company logos were present in project media, so the six listings have no company logo.
- The Job Detail reference shows a company logo that conflicts with the Home/All Jobs reference for the COO listing; the Home/All Jobs company association was used.
- Application contact details, the obscured phone numbers, and social profile URLs are not legible in the supplied references and were not invented.
- The Home template currently supplies the Career With Us copy and Newsletter UI; Newsletter submissions are not persisted by the CMS.
- The About reference provides company facts and two image subjects; the generated images are illustrative.
