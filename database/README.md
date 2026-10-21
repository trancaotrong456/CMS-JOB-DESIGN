# Shared CMS content

This directory contains an idempotent WordPress seed and a sanitized SQL content export for local development.

## Seed the content

Install WordPress with the JobScout theme, WP Job Manager, and WP Job Manager Extra Fields enabled. Put the seven files in `wp-content/uploads/seed-cms-content/` on the target site, then run from the WordPress root:

```sh
wp eval-file database/seed-cms-content.php
```

The seed uses WordPress APIs, and can be run repeatedly. It creates or updates the six reference job listings, four News posts, one News Detail post, About and Contact pages, and the seven generated reference-style images in the media library. The WordPress site front page must be the existing Home page; the script configures the Jobs and News archive settings.

## Import the SQL content export

`cms_job_design.sql` contains WordPress content and selected non-sensitive options only. It excludes user accounts, user metadata, comments, and credentials. Install a local WordPress instance with the same `wp_` table prefix and required theme/plugins, create your own local administrator during setup, and import the SQL into that site's database. The administrator should have ID 1 so imported post authors resolve. Copy the media directory above to the same uploads path. If the local URL differs, update the `home` and `siteurl` options for that local instance.

The export is intended as a development content transfer into a matching WordPress database, not as a complete WordPress installation or backup.

## Reference data limitations

- The seven included photographs were generated for this project because no licensed reference assets were available. They are illustrative and are not original company photography.
- No matching company logos were present in project media, so the six listings have no company logo.
- The Job Detail reference shows a company logo that conflicts with the Home/All Jobs reference for the COO listing; the Home/All Jobs company association was used.
- Application contact details, the obscured phone numbers, and social profile URLs are not legible in the supplied references and were not invented.
- The Home template currently supplies the Career With Us copy and Newsletter UI; Newsletter submissions are not persisted by the CMS.
- The About reference provides company facts and two image subjects; the generated images are illustrative.
