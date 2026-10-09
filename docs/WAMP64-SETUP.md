# WAMP64 setup and safe CMS sync

## One-time config migration before the first `git pull`

`wp-config.php` is now a tracked loader. Your per-computer database settings and WordPress salts belong in the ignored `wp-config.local.php`. Before the first update that brings in this change, run this from the project root in PowerShell so your current configuration survives the tracked loader update:

```powershell
if (-not (Test-Path .\wp-config.local.php)) { Copy-Item -LiteralPath .\wp-config.php -Destination .\wp-config.local.php }
git restore --source=HEAD -- wp-config.php
git fetch origin
git merge origin/main
```

Do not add `wp-config.local.php` to Git. The repository `.gitignore` excludes it. If it already exists, the command leaves it untouched. The restore applies only to tracked `wp-config.php`; the local copy remains in place. Run these commands from your assigned work branch so main is merged into that branch, not edited directly.

## Setup and checks

Open PowerShell in the repository root:

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File scripts\wamp64-team.ps1 -Action Setup
powershell.exe -NoProfile -ExecutionPolicy Bypass -File scripts\wamp64-team.ps1 -Action Check
```

`Setup` preserves an existing local config. On a fresh clone it asks for local WAMP database settings and creates unique local salts. It does not create or import a database. `Check` reports the active JobScout theme, WP Job Manager plugins, Home/front-page setting, header/footer files, registered menu assignments, published jobs, and latest News posts. It is read-only and does not repair or reassign menus.

The default paths are `C:\wamp64` and the repository root. Use `-WampRoot` or `-ProjectRoot` when your local paths differ.

## Safe CMS data sync

After WordPress is already installed and WP Job Manager is active, run:

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File scripts\wamp64-team.ps1 -Action Sync
```

The script writes a full database backup to the Windows temporary directory before running the WordPress API seed. It only adds missing reference records, skips title/slug conflicts, and leaves existing records and settings unchanged. It never imports `database/cms_job_design.sql` and never deletes content. Review any reported conflicts manually.

`database/cms_job_design.sql` is the shared sanitized content snapshot for a new development database only. Do not import it into an existing personal database; use the safe sync command instead. Keep your own exports and backups outside the repository.

The sync requires PHP and `mysqldump.exe` under the selected WAMP installation. If the backup fails, the seed is not run.
