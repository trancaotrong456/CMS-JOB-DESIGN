# WAMP64 Team Workflow Implementation Plan

> **For agentic workers:** Implement directly in the isolated `chore/wamp64-team-workflow` worktree. Keep all local database credentials out of tracked files.

**Goal:** Let five teammates update the shared WordPress repository under WAMP64 without losing local config or overwriting local CMS data.

**Architecture:** Keep a tracked `wp-config.php` loader and move machine values to ignored `wp-config.local.php`. Provide a Windows PowerShell setup/sync command that creates a backup and uses WordPress APIs for add-only seed data; never auto-import the SQL snapshot.

**Tech Stack:** WordPress/PHP, PowerShell 5.1+, WAMP64 PHP and MySQL clients, Git.

**Spec:** User request (2026-10-09).

## Global Constraints

- Preserve active local configuration and all user content.
- Do not reset Git, delete data, or modify other WordPress projects.
- Keep one safe shared CMS dataset and never import its SQL over an existing site.
- Do not commit passwords, salts, personal dumps, or local state.
- Verify Home, JobScout menus, plugins, and front-page settings before changes.
- Run checks before commit and create a PR to `main`.

## Review Focus

- Existing tracked config migration: local config must be copied before first update; verify the current config copy matches exactly.
- Existing local seeded posts: repeat sync must not update their content or metadata.
- Existing DB safety: sync makes a backup before any seed operation and never imports the SQL snapshot.
- WAMP path variation: script accepts an explicit project root and detects PHP/MySQL tools.
- Git safety: personal SQL/config remain excluded while the shared content export stays tracked.

## Tasks

### Task 1: Repository and live WordPress audit
- Inspect branch state, tracked config/database files, active theme/plugins, front page, menus, and endpoints without printing secrets.
- Preserve the current config into the ignored local config path.

### Task 2: Configuration and repository hygiene
- Add a local config loader/example, safe ignores, and deterministic text/binary attributes.
- Keep the shared sanitized content export tracked while ignoring personal dumps/backups.

### Task 3: Safe CMS sync
- Make the shared seed insert-only for existing CMS records/settings.
- Add a WAMP64 PowerShell setup/sync script; backup before seed; do not import SQL.

### Task 4: Verification and PR
- Lint PHP and PowerShell, verify ignore/attributes and no secret patterns, check current live site settings, and validate sync guards.
- Commit on the feature branch, push, and create a PR to `main`.
