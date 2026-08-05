# Implementation Progress

This file is append-only. Every material implementation milestone must add a dated entry so a new agent can recover the branch state without relying on chat history.

## Entry Template

```markdown
## YYYY-MM-DD - Milestone
- Branch/commit:
- Completed behavior:
- Schema changes:
- Important files:
- Verification:
- Blockers:
- Next step:
```

## 2026-07-16 - Comprehensive governance dashboard overhaul

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `6781cb5`.
- Completed behavior: Rebuilt technical and ethics queues with workload metrics, action/completed/all tabs, search, filters, sorting, aging, package context, checklist progress, and pagination. Rebuilt review workspaces with stage-specific evidence, structured Confirmed/Issue found/Not reviewed checklist responses, item-level findings, draft saving, progress guidance, explicit confirmation, and immutable completed decisions. Rebuilt administrator governance with operational metrics, oldest-record attention queue, stage tabs, queue filters, workload-aware assignment, explicit reassignment with required reasons and reviewer notifications, publication confirmation, workflow stepper, version and review timelines, lifecycle controls, and audit activity.
- Schema changes: Added nullable `reviews.draft_saved_at` and `reviews.reassignment_reason` through `2026-07-16-000016_EnhanceReviewWorkflow.php`. The migration completed successfully on local MySQL.
- Important files: `app/Services/ModerationWorkflow.php`, `app/Controllers/Reviews.php`, `app/Controllers/Admin.php`, review/admin views, `app/Config/Routes.php`, `app/Models/ReviewModel.php`, `public/assets/css/app.css`, `tests/unit/ModerationWorkflowContractTest.php`, and governance documentation.
- Verification: PHP syntax checks pass for all new workflow files; Spark routes include review draft and administrator reassignment endpoints; PHP 8.5.7 unit tests pass with 14 tests and 37 assertions; live role-based requests return 200 for administrator overview/stage board/inspection, technical completed queue/immutable decision, and ethics active queue/structured workspace. Live guards reject completed-review drafts, reassignment without a reason, and publication without confirmation. A controlled ethics draft stored structured findings and was then restored without leaving test data.
- Blockers: The full database feature suite remains blocked because the installed PHP 8.5 runtime lacks the SQLite3 extension. Screenshot-level browser QA remains unavailable in this session.
- Next step: Enable SQLite3 for PHP 8.5, run the full PHPUnit suite, then complete desktop/mobile visual QA and a real two-reviewer reassignment walkthrough.

## 2026-07-13 - Baseline and workflow foundation

- Branch/commit: `rapid-mvp` at `9989246` before local implementation changes.
- Completed behavior: Confirmed the existing branch contained contributor/public flows only; added the integrated moderation state model, multiple role support, role filter, review assignments, stage decisions, notifications, publication gating, and archive restoration services.
- Schema changes: Added `archived_from_status`, migrated legacy pending/revision values, added a unique user-role pair, and created `reviews` and `notifications` tables.
- Important files: `app/Services/ModerationWorkflow.php`, workflow migrations, models, filters, routes, and authentication session handling.
- Verification: Static repository inspection and PHP 8.0 syntax checks on the workflow and new controllers; PHP 8.2 runtime verification remains required.
- Blockers: Local XAMPP PHP is 8.0.30 while the application requires PHP 8.2 or newer.
- Next step: Complete role-specific portal interfaces and contributor workflow feedback.

## 2026-07-13 - Portal and moderation interfaces

- Branch/commit: `rapid-mvp`, local uncommitted implementation.
- Completed behavior: Added the administrator overview, dataset assignment/publication/lifecycle controls, user-role administration, audit-log viewer, ethics and technical queues, protected review download, manual checklists, review history, contributor status notifications, and active-review edit locking.
- Schema changes: No additional schema beyond the workflow foundation.
- Important files: `app/Controllers/Admin.php`, `app/Controllers/Reviews.php`, `app/Views/layouts/portal.php`, admin/review views, contributor dashboard, and portal CSS.
- Verification: New PHP controllers and service passed available syntax checks; route, migration, test, and browser verification are pending.
- Blockers: Full CodeIgniter execution cannot be claimed under PHP 8.0.30.
- Next step: Update tests, remove obsolete scope language, run complete static verification, and document residual runtime gaps.

## 2026-07-13 - Static verification and handoff

- Branch/commit: `rapid-mvp`, local uncommitted implementation.
- Completed behavior: Reconciled public and portal navigation, added seeded walkthrough accounts, updated contributor upload/status language, tightened invalid-stage and archive guards, restricted reassigned review access, and replaced obsolete split-portal documentation.
- Schema changes: Confirmed the additive migration order and updated fresh-install dataset defaults to `pending_ethics_review`.
- Important files: `README.md`, `SETUP.md`, `docs/CONTEXT.md`, `docs/DESIGN.md`, `docs/SKILL.md`, workflow tests, and the complete moderation implementation listed above.
- Verification: All 31 changed or new PHP files pass `php -l`; `git diff --check` passes; active docs and views contain no separate-Admin-Portal prohibition. Spark routes and PHPUnit were attempted but cannot run under PHP 8.0.30.
- Blockers: A PHP 8.2+ runtime and migrated MySQL test database are required for route, migration, feature, and browser walkthrough verification.
- Next step: Run `php spark migrate`, `php spark db:seed MvpSeeder`, `php spark routes`, and `composer test` under PHP 8.2+, then execute the role-by-role walkthrough in `SETUP.md`.

## 2026-07-14 - Marc task pass

- Branch/commit: `rapid-mvp`, local uncommitted implementation.
- Completed behavior: Implemented Marc's refactored tracker items by polishing dataset preview metadata, access context, focus return, keyboard trapping, and mobile-safe preview layout; expanded demo seed data across published access labels and moderation lifecycle states; added a conditional deployment readiness checklist.
- Schema changes: None.
- Important files: `app/Views/datasets/index.php`, `public/assets/css/app.css`, `app/Database/Seeds/MvpSeeder.php`, `SETUP.md`, `docs/DEPLOYMENT_READINESS.md`, and `docs/refactored-audit-task-tracker.csv`.
- Verification: `C:\xampp\php\php.exe -l` passes for touched PHP files; `git diff --check` passes with only Windows line-ending warnings; local WinGet PHP 8.5.7 runs `php spark routes`; PHPUnit passes with 10 tests and 22 assertions.
- Blockers: Database write verification for `php spark db:seed MvpSeeder` was not run in this pass to avoid mutating the local database without a dedicated seed check.
- Next step: Run `php spark migrate:status` and `php spark db:seed MvpSeeder` against the intended local/staging database, then browser-check the dataset preview modal on desktop and mobile.

## 2026-07-14 - Portal flow separation

- Branch/commit: `rapid-mvp`, local work after `71c04e3`.
- Completed behavior: Separated the governance portal from the public website by replacing ambiguous contributor/public catalog links with portal-native contributor records, portal dataset inspection, administrator metadata inspection, and explicit `Return to website` exits.
- Schema changes: None.
- Important files: `app/Config/Routes.php`, `app/Controllers/Dashboard.php`, `app/Controllers/Admin.php`, `app/Views/layouts/portal.php`, `app/Views/dashboard/portal.php`, `app/Views/dashboard/portal_dataset.php`, `app/Views/admin/dataset.php`, `app/Views/admin/datasets.php`, and `public/assets/css/app.css`.
- Verification: PHP 8.5 syntax checks passed for updated routes, controllers, portal layout, and new portal/admin views; `php spark routes` shows the new portal/dashboard, portal/datasets, portal notifications, and admin dataset inspection routes; PHPUnit passes with 10 tests and 22 assertions.
- Blockers: Browser-level confirmation of the portal navigation distinction is still needed.
- Next step: Start the app under PHP 8.5+ and click through admin, ethics, technical, and portal contributor records to confirm only explicit website-exit actions leave the portal shell.

## 2026-07-14 - Sleeker shared UI scale

- Branch/commit: `rapid-mvp`, local work after `71c04e3`.
- Completed behavior: Tightened the shared visual scale across public and portal shells: smaller base type, slimmer buttons, reduced hero/card/form/modal spacing, denser portal sidebar/table layouts, Material Symbols icon font loading, icon-led public account menu, portal account tab, and clearer icon treatment for review/admin navigation and website exits.
- Schema changes: None.
- Important files: `app/Views/layouts/main.php`, `app/Views/layouts/portal.php`, and `public/assets/css/app.css`.
- Verification: PHP 8.5 syntax checks passed for updated public and portal layouts; `php spark routes` passes; PHPUnit passes with 10 tests and 22 assertions; `git diff --check` passes with only Windows line-ending warnings; local PHP server returns `200` at `http://127.0.0.1:8082/`.
- Blockers: Manual visual QA is still needed across public home/catalog and portal/admin screens to approve the final density.
- Next step: Inspect `http://127.0.0.1:8082/`, login with seeded accounts, and confirm the account icon menu, portal account tab, and smaller shared scale feel right before staging.

## 2026-07-14 - Dashboard links under profile

- Branch/commit: `rapid-mvp`, local work after `71c04e3`.
- Completed behavior: Moved contributor, reviewer, and administrator dashboard entry points out of the public main navigation and into the profile/account tab so normal users and reviewers share the same general website header. Added matching role shortcuts to the portal account tab and compact mobile account panel.
- Schema changes: None.
- Important files: `app/Views/layouts/main.php`, `app/Views/layouts/portal.php`, and `public/assets/css/app.css`.
- Verification: PHP 8.5 syntax checks passed for updated public and portal layouts; `php spark routes` passes; PHPUnit passes with 10 tests and 22 assertions; `git diff --check` passes with only Windows line-ending warnings.
- Blockers: Manual visual QA should confirm the account dropdown remains readable for multi-role users.
- Next step: Login as contributor, reviewer, and administrator, then confirm all dashboard/workspace links are reachable from the profile tab without cluttering the main nav.

## 2026-07-15 - Browse preview mockup alignment

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `86f6b08`.
- Completed behavior: Reworked the browse dataset preview modal to match the provided mockup: updated compact row pills inside the preview header, italic dataset title, gold divider, larger short description, bordered metadata fact sheet, contributor email display when available, compact tag text, single gold Explore action, restored `aria-expanded`, heading-first focus, Escape close, backdrop close, and Tab focus trapping.
- Schema changes: None.
- Important files: `app/Controllers/Datasets.php`, `app/Views/datasets/index.php`, and `public/assets/css/app.css`.
- Verification: PHP 8.5 syntax checks passed for `app/Controllers/Datasets.php` and `app/Views/datasets/index.php`; `php spark routes` passes; PHPUnit passes with 10 tests and 22 assertions; `git diff --check` passes with only Windows line-ending warnings.
- Blockers: Browser-level visual QA is still needed to confirm the modal matches the attached composition across desktop and mobile.
- Next step: Run PHP 8.5 verification, then open the browse page and compare the preview modal against the mockup.

## 2026-07-15 - Technical-first maintainer workflow

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `925ddb8`.
- Completed behavior: Reordered moderation so new and updated submissions enter technical verification before ethics review, moved maintainer login destinations into admin/technical/ethics work queues, demoted contributor records for maintainer portal navigation, and added live portal activities with unread yellow badge, toast, and browser-unlocked beep.
- Schema changes: Added migration `2026-07-15-000014_TechnicalFirstReviewFlow` to change the dataset status default to `pending_technical_review` and move unassigned legacy pending ethics rows into the technical queue.
- Important files: `app/Services/ModerationWorkflow.php`, `app/Models/DatasetModel.php`, `app/Controllers/Auth.php`, `app/Controllers/Dashboard.php`, `app/Views/layouts/portal.php`, admin/dashboard/upload views, routes, docs, tests, and the new migration.
- Verification: PHP 8.5 syntax checks passed for changed controllers, model, service, portal layout, and new migration; `php spark routes` passes and shows `portal/notifications/poll`; `php spark migrate:status` recognizes `TechnicalFirstReviewFlow` as pending; PHPUnit passes with 11 tests and 23 assertions; `git diff --check` passes with only Windows line-ending warnings.
- Blockers: Browser sound playback still depends on a user interaction because browsers block autoplay audio.
- Next step: Run full PHP 8.5 verification, then manually walk through upload -> technical assignment -> technical approval -> ethics assignment -> ethics approval -> publication.

## 2026-07-16 - Authenticated download gate

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `be12739`.
- Completed behavior: Changed dataset ZIP download flow so guests can browse/view public metadata but cannot download any dataset file. All direct `/datasets/{id}/download` requests now require an authenticated account; public, institutional, and restricted published files are downloadable by logged-in users, while private files require owner or administrator authorization. Denied download attempts are written to audit logs, and public dataset detail pages show "Sign in to download" for guests instead of a direct ZIP action.
- Schema changes: None.
- Important files: `app/Controllers/Datasets.php`, `app/Views/datasets/show.php`, `public/assets/css/app.css`, `tests/Feature/DatasetAccessTest.php`, and `docs/progress.md`.
- Verification: PHP 8.0.30 syntax checks passed for `Datasets.php`, dataset detail view, and `DatasetAccessTest.php`; `git diff --check` passes with only Windows line-ending warnings; live local app redirects guest direct download to login, public detail page shows "Sign in to download" instead of "Download ZIP", logged-in seeded user receives a 200 attachment response, and MySQL audit log contains `dataset_download_denied` for the guest attempt. PHPUnit feature execution is blocked locally because the XAMPP CLI PHP is 8.0.30 while this PHPUnit requires PHP 8.1+.
- Blockers: Manual browser QA should confirm the login redirect toast and audit-log visibility in the administrator portal.
- Next step: Login as a regular user and verify public/restricted downloads work, then logout and confirm the same direct download URLs redirect to login and create denied audit log entries.

## 2026-07-16 - Stylized flash feedback

- Branch/commit: `rapid-mvp`, local uncommitted implementation after profile settings work.
- Completed behavior: Replaced plain session flash notices in public and portal layouts with dismissible toast-style popups for success and error feedback, including icons, clear headings, auto-dismiss behavior, mobile positioning, and preserved in-page `.notice` styling for non-flash content such as development reset links.
- Schema changes: None.
- Important files: `app/Views/layouts/main.php`, `app/Views/layouts/portal.php`, `public/assets/css/app.css`, and `docs/progress.md`.
- Verification: PHP 8.0.30 syntax checks passed for public and portal layouts; `git diff --check` passes with only Windows line-ending warnings.
- Blockers: Manual browser QA should confirm animation timing and positioning on public, portal, and mobile layouts.
- Next step: Trigger a successful profile save and a validation error to confirm the popup copy and dismissal behavior feel right.

## 2026-07-16 - Cover radius refinement and governance UX plan

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `6781cb5`.
- Completed behavior: Reduced dataset cover, detail hero, contributor card, and upload-preview corner rounding from the oversized 42-50px treatment to a restrained 20px radius across desktop and mobile. Audited the current administrator moderation, technical review, ethics review, portal navigation, workflow service, review schema, notifications, and tests, then documented a staged dashboard improvement plan centered on queue triage, focused evidence review, structured checklists, safe decisions, workload-aware assignment, explicit reassignment, and final publication gating.
- Schema changes: None for this planning milestone. The plan recommends optional review draft and reassignment fields during implementation.
- Important files: `public/assets/css/app.css`, `docs/ADMIN_REVIEW_DASHBOARD_PLAN.md`, and `docs/progress.md`.
- Verification: Confirmed all cover-related radius declarations now use `20px`; reviewed the active routes, controllers, views, `ModerationWorkflow`, `reviews` and `notifications` migrations, and existing moderation contract tests before defining the plan.
- Blockers: This milestone plans the dashboard overhaul but does not implement the new reviewer or administrator interfaces yet.
- Next step: Begin Milestone 1 in `docs/ADMIN_REVIEW_DASHBOARD_PLAN.md` by building paginated, filterable technical and ethics queues with workload and aging summaries.

## 2026-07-16 - Dataset cover images and legacy placeholders

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `6781cb5`.
- Completed behavior: Added optional dataset cover uploads for new submissions and dataset revisions, with live client-side previews and JPG/PNG/WebP validation. Covers are stored under protected writable storage and streamed through an authorization-aware dataset route. Browse rows, dataset details, and contributor dataset cards now display rounded cover images with approximately 50px corners; all existing datasets automatically use the shared repository placeholder without requiring data updates.
- Schema changes: Added nullable `datasets.cover_image` through migration `2026-07-16-000015_AddDatasetCoverImage.php`. The migration completed successfully against the local MySQL database and existing rows remain null.
- Important files: `app/Database/Migrations/2026-07-16-000015_AddDatasetCoverImage.php`, `app/Helpers/dataset_helper.php`, `app/Controllers/DatasetUpload.php`, `app/Controllers/Datasets.php`, `app/Models/DatasetModel.php`, dataset upload/edit/catalog/detail/dashboard views, `public/assets/css/app.css`, `public/assets/img/placeholders/dataset-placeholder-img.png`, and `tests/unit/RapidMvpContractTest.php`.
- Verification: PHP 8.0.30 syntax checks passed for every touched PHP file; PHP 8.5.7 migration completed; unit tests pass with 12 tests and 32 assertions; live MySQL-backed requests confirm 10 catalog entries render placeholder cover references, authenticated upload renders the cover control and placeholder preview, dataset detail renders its cover element, the legacy cover route redirects to the placeholder, and the placeholder asset returns `200 image/png`.
- Blockers: The full PHPUnit run reaches 27 tests but its 14 database feature tests cannot start because the installed PHP 8.5 runtime does not have the SQLite3 extension enabled. In-app browser screenshot-level visual QA was unavailable in this session.
- Next step: Enable SQLite3 for PHP 8.5 and rerun the full PHPUnit suite, then upload one custom cover through the browser and visually confirm desktop/mobile cropping and replacement behavior.

## 2026-07-16 - Protected profile settings

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `ff68bbd`.
- Completed behavior: Added protected `/account/settings` page for logged-in users to review account status, roles, timestamps, and update full name, email, and optional password with current-password confirmation. Linked profile settings from public and portal account menus, refreshed session name/email after saves, recorded profile update audit logs, and strengthened the auth filter so inactive or missing accounts are signed out before accessing protected routes.
- Schema changes: None.
- Important files: `app/Controllers/Account.php`, `app/Views/account/settings.php`, `app/Config/Routes.php`, `app/Filters/AuthFilter.php`, `app/Views/layouts/main.php`, `app/Views/layouts/portal.php`, `public/assets/css/app.css`, and `docs/progress.md`.
- Verification: PHP 8.0.30 syntax checks passed for `Account.php`, settings view, `AuthFilter.php`, routes, and both layouts; `git diff --check` passes with only Windows line-ending warnings; anonymous `/account/settings` redirects to `/login`; logged-in seeded user reaches the settings page; unchanged profile save shows success feedback; password change with a bad current password is rejected inline. `php spark routes` is blocked locally because XAMPP CLI PHP is 8.0.30 while the app requires PHP 8.2+.
- Blockers: Manual QA should still confirm inactive-account forced logout after deactivating an already logged-in user from the admin interface.
- Next step: Log in as a seeded account, open Profile settings from the account menu, save profile changes, test a bad current password, then deactivate the account from admin and confirm protected routes force logout.

## 2026-07-16 - Mockup-aligned auth workspace

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `3bfb0c2`.
- Completed behavior: Reworked login/register into a mockup-aligned workspace entry screen with a top "Browse as guest" escape, large editorial left-side copy, compact right-side credential card, disabled CSPC Google account button, quieter local demo-account disclosure, and partner logos placed from organized public brand assets.
- Schema changes: None.
- Important files: `app/Views/auth/login.php`, `app/Views/auth/register.php`, `public/assets/css/app.css`, `public/assets/img/brand/asogtbi-logo.webp`, `public/assets/img/brand/ccs-logo.png`, and `docs/progress.md`.
- Verification: PHP 8.0.30 syntax checks passed for login/register views; `git diff --check` passes with only Windows line-ending warnings; local `/login` and `/register` return 200; organized brand asset URLs return 200; rendered `/login` includes the guest link, workspace heading, disabled CSPC Google button, and brand asset references.
- Blockers: In-app browser was unavailable in this session, so manual visual QA is still needed on desktop and mobile to confirm logo sizing and the mockup spacing with real browser rendering.
- Next step: Open `/login` and `/register`, check desktop/mobile layout, then decide whether forgot/reset password should adopt the same workspace treatment.

## 2026-07-15 - Admin users and roles polish

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `925ddb8`.
- Completed behavior: Reworked the administrator Users and Roles screen from dense inline rows into a scalable access directory with account metrics, client-side search/status/role filters, compact account rows, active/inactive pills, current-account marker, role preview chips, expandable role editors, and clearer save actions while preserving existing server-side role safeguards. Added slimmer search/filter controls, tighter chips, a cleaner edit popover for large account lists, fixed desktop portal scrolling so long edit panels scroll inside the content area without dragging the sidebar or exposing a bottom gap, and corrected filtered account rows so search/status/role filters visibly update the list.
- Schema changes: None.
- Important files: `app/Views/admin/users.php`, `public/assets/css/app.css`, and `docs/progress.md`.
- Verification: PHP 8.5 syntax check passed for `app/Views/admin/users.php`; `git diff --check` passes with only Windows line-ending warnings; local `/admin/users` returns 200 after the compact UI, portal scroll, and filter visibility fixes.
- Blockers: Manual browser QA should confirm filtering, expandable editors, and role saves with a larger seeded account list.
- Next step: Login as administrator, open Users and Roles, filter by role/status/search, expand several rows, and verify active/inactive changes plus multi-role saves still feel clear and safe.

## 2026-07-15 - Seedable dummy published uploads

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `c4ea40a`.
- Completed behavior: Converted the one-off `dummydata/dataset1.csv` import into an idempotent CodeIgniter seeder that publishes each CSV row as a public dataset owned by user `1`, stores protected CSV copies, and creates matching dataset file/version records.
- Schema changes: None; this is data seeding only and requires the existing dataset, file, and version migrations.
- Important files: `app/Database/Seeds/DummyPublishedUploadsSeeder.php`, `dummydata/dataset1.csv`, `SETUP.md`, and `docs/progress.md`.
- Verification: PHP 8.5 syntax check passed for `DummyPublishedUploadsSeeder`; `php spark db:seed DummyPublishedUploadsSeeder` runs successfully; local MySQL count remains idempotent at 120 published dummy datasets and 120 matching CSV file records; `git diff --check` passes with only Windows line-ending warnings.
- Blockers: The source CSV must be present at `dummydata/dataset1.csv`; run `MvpSeeder` first if user `1` does not exist.
- Next step: Run `php spark db:seed DummyPublishedUploadsSeeder` after migrations and `MvpSeeder` when a large published dummy catalog is needed.

## 2026-07-20 - ZIP-normalized dummy published uploads

- Branch/commit: `rapid-mvp`, local uncommitted implementation after the Google-auth/account-lock pass.
- Completed behavior: Updated the metadata flow so every uploaded package is treated as a protected ZIP while contributors separately disclose the formats inside the archive through `content_formats`. The upload/edit forms, catalog cards, detail pages, portal views, admin/review evidence, search text, and demo seeders now use that split instead of exposing a package-format dropdown.
- Schema changes: Added migration `2026-07-20-000019_NormalizeDummyPublishedUploadsToZip` to delete previously seeded CSV dummy file/version records, remove old stored CSV files, and normalize dummy package metadata before reseeding. Added migration `2026-07-20-000020_AddContentFormatsToDatasets` to add `datasets.content_formats`, migrate non-ZIP legacy `file_format` values into it, and normalize `datasets.file_format` to `ZIP`.
- Important files: `app/Database/Seeds/DummyPublishedUploadsSeeder.php`, `app/Database/Seeds/MvpSeeder.php`, `app/Database/Migrations/2026-07-20-000019_NormalizeDummyPublishedUploadsToZip.php`, `app/Database/Migrations/2026-07-20-000020_AddContentFormatsToDatasets.php`, upload/catalog/detail/review/admin views, `SETUP.md`, and `docs/progress.md`.
- Verification: Not run against the database in this shell yet. Recommended checks are PHP syntax checks on changed PHP files, then `php spark migrate`, `php spark db:seed MvpSeeder`, and `php spark db:seed DummyPublishedUploadsSeeder` to confirm old CSV file rows are gone, package metadata is `ZIP`, and `content_formats` is populated.
- Blockers: `dummydata/dataset1.csv` remains the bulk metadata source, and ZIP creation prefers the PHP `ZipArchive` extension; without it, the seeder falls back to a minimal empty ZIP container.
- Next step: Run migrations, reseed demo data, and spot-check the catalog sidebar and several dataset cards to confirm the old file-format filter is gone and cards show disclosed formats inside ZIP.

## 2026-07-15 - Polished auth entry flow

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `474055d`.
- Completed behavior: Reworked sign-in and sign-up pages into a centered auth island with clear background, disabled "Continue with Google" placeholder, policy/help copy, autocomplete attributes, inline field errors, mobile stacking, consistent Sign in/Sign up wording, and server-side `@cspc.edu.ph` validation for self-registration. Demoted local demo credentials into a collapsible development note on login so they do not read like a product feature.
- Schema changes: None.
- Important files: `app/Controllers/Auth.php`, `app/Views/auth/login.php`, `app/Views/auth/register.php`, `app/Views/auth/forgot_password.php`, `app/Views/layouts/main.php`, `public/assets/css/app.css`, and `docs/progress.md`.
- Verification: PHP 8.5 syntax checks passed for `Auth.php`, login/register views, forgot-password view, and main layout; local `/login` and `/register` return 200; invalid non-CSPC registration is blocked and renders the inline `@cspc.edu.ph` error when following the local session redirect; `php spark routes` passes; PHPUnit passes with 11 tests and 23 assertions; `git diff --check` passes with only Windows line-ending warnings.
- Blockers: Google OAuth remains intentionally disabled and visual-only until a provider flow is implemented.
- Next step: Browser-check login/register on desktop and mobile, then decide whether to extend the same auth island treatment to forgot/reset password screens.

## 2026-07-15 - Dedicated demo account seeder

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `b0bd5c5`.
- Completed behavior: Added an account-only seeder that creates or repairs the contributor, repository administrator, ethics reviewer, and technical reviewer demo accounts, ensures their roles, activates them, and resets all demo passwords to `change-me`. Updated `MvpSeeder` so rerunning the full demo seed also repairs existing demo account passwords.
- Schema changes: None; existing `users`, `roles`, and `user_roles` migrations already support the accounts.
- Important files: `app/Database/Seeds/DemoAccountsSeeder.php`, `app/Database/Seeds/MvpSeeder.php`, `README.md`, `SETUP.md`, and `docs/progress.md`.
- Verification: PHP 8.5 syntax checks passed for `DemoAccountsSeeder` and `MvpSeeder`; `php spark db:seed DemoAccountsSeeder` runs successfully; local login POSTs with `change-me` redirect `admin@example.test` to `/admin`, `ethics@example.test` to `/review/ethics`, and `technical@example.test` to `/review/technical`; `git diff --check` passes with only Windows line-ending warnings.
- Blockers: Teammates must still run `php spark migrate` before either seeder on a fresh database.
- Next step: Run `php spark db:seed DemoAccountsSeeder`, then sign in as `admin@example.test`, `ethics@example.test`, and `technical@example.test` with `change-me`.

## 2026-07-15 - Clean public URLs

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `0aa31a6`.
- Completed behavior: Removed `index.php` from CodeIgniter-generated links by setting `app.indexPage` to an empty string and documenting the Apache/XAMPP rewrite requirement.
- Schema changes: None.
- Important files: `app/Config/App.php`, `.env.example`, `SETUP.md`, and `docs/progress.md`.
- Verification: PHP 8.5 syntax check passed for `App.php`; local `/login` HTML contains no `index.php` links; admin login redirects to `/admin` instead of `/index.php/admin`; `/datasets` returns 200 and protected `/admin` redirects cleanly.
- Blockers: Clean URLs require the server to route requests through `public/index.php`; Apache deployments must keep `mod_rewrite` and `public/.htaccess` active.
- Next step: Open `/login`, `/datasets`, and a protected dashboard redirect and confirm generated links and redirects no longer include `/index.php`.

## 2026-07-14 - Contributor dashboard submission cards

- Branch/commit: `rapid-mvp`, local work after `71c04e3`.
- Completed behavior: Enriched My Datasets cards with owner, workflow, access type, next action, latest reviewer comments, revision/update actions, archive availability, and a clearer Upload Datasets empty state.
- Schema changes: None.
- Important files: `app/Controllers/Dashboard.php`, `app/Models/DatasetModel.php`, `app/Views/dashboard/index.php`, `public/assets/css/app.css`, and `tests/unit/ModerationWorkflowContractTest.php`.
- Verification: PHP syntax checks pass for touched PHP files; `php spark routes` passes; `composer test` passes with 11 tests and 30 assertions.
- Blockers: Browser connector was unavailable in this session, so visual QA on `/index.php/dashboard` still needs an in-browser pass with seeded contributor records.
- Next step: Login as a seeded contributor and inspect `/index.php/dashboard` across pending, revision, awaiting publication, published, rejected, archived, and empty states.

## 2026-07-16 - Governance dashboard final verification

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `6781cb5`.
- Completed behavior: Completed the implementation and safety sweep for the technical-first reviewer queues, structured review workspace, administrator stage board, reviewer reassignment, publication confirmation, lifecycle guards, protected cover access, and responsive governance UI.
- Schema changes: Confirmed local MySQL is migrated through `2026-07-16-000016_EnhanceReviewWorkflow.php`; `reviews.draft_saved_at` and `reviews.reassignment_reason` are present and nullable.
- Important files: `app/Services/ModerationWorkflow.php`, `app/Controllers/Reviews.php`, `app/Controllers/Admin.php`, review/admin views, `public/assets/css/app.css`, workflow migrations, unit contracts, and `docs/ADMIN_REVIEW_DASHBOARD_PLAN.md`.
- Verification: PHP 8.0.30 syntax checks pass for all 24 changed or new PHP files. PHP 8.5.7 unit tests pass with 14 tests and 37 assertions. All seven administrator moderation stages and reviewer queue search/filter combinations return HTTP 200 against the live MySQL-backed app. Protected cover requests return 404 for guests and 200 for the assigned reviewer and administrator. Temporary authorization-test data and files were removed. `git diff --check` reports no whitespace errors or conflict markers.
- Blockers: The complete PHPUnit run reaches 29 tests, but all 14 database feature tests stop during setup because PHP 8.5 does not have the SQLite3 extension installed. In-app screenshot automation was not exposed in this session, so final visual approval remains a manual browser pass.
- Next step: Enable the PHP 8.5 SQLite3 extension and rerun `vendor/bin/phpunit`, then perform desktop and mobile visual QA of the technical queue, ethics queue, review workspace, administrator stage board, and dataset inspection page.

## 2026-07-16 - Contributor dataset library overhaul

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `6781cb5`.
- Completed behavior: Rebuilt My Datasets as a paginated contributor library with technical, ethics, publication, revision, published, and closed workflow views; search and access filters; per-record workflow progress; reviewer notes; explicit published access states; a published-access summary; compact actions; collapsible repository updates; a guided first-upload experience; and a separate filtered no-results state. The visual direction now shares the login workspace's soft blue-and-cream canvas, editorial headings, white islands, and thin gold dividers.
- Schema changes: None.
- Important files: `app/Controllers/Dashboard.php`, `app/Models/DatasetModel.php`, `app/Views/dashboard/index.php`, `app/Views/dashboard/portal.php`, `public/assets/css/app.css`, and `tests/unit/ModerationWorkflowContractTest.php`.
- Verification: PHP 8.0.30 syntax checks pass for the controller, model, public dashboard, portal dashboard, and updated unit test. PHP 8.5.7 unit tests pass with 15 tests and 44 assertions. Live MySQL-backed requests confirm the 123-record contributor receives 12 records per page, second-page navigation, workflow filters, published access filtering, and the access breakdown; an account with zero uploads receives the guided empty state; and a search with no matches receives the filtered no-results state. Public and portal dashboard routes return HTTP 200.
- Blockers: Final visual approval still requires a manual desktop/mobile browser pass. The complete database feature suite remains blocked by the missing SQLite3 extension in PHP 8.5.
- Next step: Visually inspect `/dashboard` with the seeded contributor and an empty contributor account at desktop and mobile widths, then enable SQLite3 and rerun the complete PHPUnit suite.

## 2026-07-16 - Unified header notifications

- Branch/commit: `rapid-mvp`, local uncommitted implementation after `6781cb5`.
- Completed behavior: Consolidated persistent repository notifications into one reusable bell menu in the public header and governance portal top bar. Removed duplicate notification panels from My Datasets and portal contributor records, removed the sidebar activity disclosure, retained the live unread badge, bounded activity history, mark-all-read control, polling, transient toast, and opt-in beep, and added a mobile header variant.
- Schema changes: None.
- Important files: `app/Views/components/notification_menu.php`, `app/Views/components/notification_script.php`, `app/Views/layouts/main.php`, `app/Views/layouts/portal.php`, `app/Views/dashboard/index.php`, `app/Views/dashboard/portal.php`, `app/Controllers/Dashboard.php`, `app/Config/Routes.php`, and `public/assets/css/app.css`.
- Verification: PHP 8.0.30 syntax checks pass for routes, controller, layouts, notification components, and both dashboard views. PHP 8.5.7 routes include protected `/notifications/read` and `/notifications/poll`; unit tests pass with 15 tests and 44 assertions. Live contributor, ethics reviewer, technical reviewer, and administrator pages return HTTP 200, render the header notification component, return successful polling JSON, hide zero-count badges, and contain no body-level or sidebar notification panels.
- Blockers: Final interaction and responsive visual approval still requires a manual browser pass.
- Next step: Open the public header and portal top bar notification menus at desktop and mobile widths, confirm popover positioning and mark-all-read behavior, then enable SQLite3 and rerun the complete feature suite.

## 2026-07-22 - Automatic balanced review distribution

- Branch/commit: `rapid-mvp`, automatic review distribution implemented after CSS refactor commit `680f454`.
- Completed behavior: Technical and ethics reviews are now assigned immediately to eligible active reviewers using least-active-load distribution, with longest-idle and account-ID tie-breakers for fair deterministic rotation. Automatic assignment runs for initial uploads, contributor resubmissions, technical-to-ethics approval, restored pending records, and reviewer account activation/creation. Manual assignment remains available when no eligible reviewer exists, manual reassignment still preserves history, and accounts with active reviews cannot be deactivated or lose the required reviewer role. Assignment creation now refuses to create a second active review of any stage for the same dataset.
- Schema changes: Added and applied `2026-07-22-000021_AddReviewAssignmentMethod`, which adds `reviews.assignment_method` with a `manual` default for existing history and records new system assignments as `automatic`. Local MySQL migration batch 8 completed successfully.
- Important files: `app/Services/ModerationWorkflow.php`, `app/Controllers/DatasetUpload.php`, `app/Controllers/Datasets.php`, `app/Controllers/Admin.php`, `app/Models/ReviewModel.php`, review/admin/upload views, `app/Database/Migrations/2026-07-22-000021_AddReviewAssignmentMethod.php`, `tests/unit/AutomaticReviewAssignmentTest.php`, and `tests/unit/ModerationWorkflowContractTest.php`.
- Verification: PHP 8.5.7 syntax checks pass for all touched PHP files. Unit and in-memory SQLite workflow tests pass with 23 tests and 72 assertions, covering balanced rotation, inactive reviewer exclusion, duplicate prevention, no-reviewer fallback, pending backlog distribution, automatic assignment provenance, notifications, audit records, and the same-transaction technical-to-ethics handoff. `git diff --check` passes. Live MySQL confirms the migration and existing review rows defaulted to `manual`.
- Blockers: The 14 existing `DatasetAccessTest` cases still stop during test migration setup because `2026-07-20-000020_AddContentFormatsToDatasets.php` runs before the SQLite test database has a `datasets` table. The local demo database also contains an older inconsistent record whose active ethics review does not match its pending-technical dataset status; the new allocator safely refuses to create a second active review for it.
- Next step: Resolve the old demo record through the administrator workflow, then test a new upload with multiple technical and ethics reviewer accounts and confirm queue counts remain balanced as reviews complete.

## 2026-08-05 - Comprehensive user manual

- Branch/commit: `rapid-mvp`, local documentation update.
- Completed behavior: Added a standard-format user manual covering system overview, roles, sign-in, public catalog use, contributor procedures, reviewer procedures, administrator procedures, workflow/status reference, access classifications, data/file handling, security rules, notifications, troubleshooting, operational notes, glossary, and route quick reference. Linked the manual from the README documentation list.
- Schema changes: None.
- Important files: `docs/USER_MANUAL.md`, `README.md`, and `docs/progress.md`.
- Verification: Markdown documentation created from the current routes, controllers, models, services, views, and existing repository documentation. `git diff --check -- docs/USER_MANUAL.md README.md docs/progress.md` passed for tracked edits with only existing LF-to-CRLF warnings; `docs/USER_MANUAL.md` has no trailing whitespace and is ASCII-only.
- Blockers: No browser or application runtime verification was required for this documentation-only milestone.
- Next step: Review the manual for institution-specific wording, support ownership, and any production-only operating policies before release.

## 2026-08-05 - Mainline milestone wrap-up

- Branch/commit: `rapid-mvp`, prepared for merge into `main`.
- Completed behavior: Clarified the repository documentation around the achieved mainline milestone, replaced stale branch-oriented setup wording, linked the as-built specification and user manual from the README, refreshed the live handover, replaced boilerplate test documentation with a project-specific test guide, and reduced the old root alpha design export to a pointer to the canonical `docs/DESIGN.md`.
- Schema changes: None.
- Important files: `README.md`, `SETUP.md`, `SOFTWARE_DOCUMENTATION.md`, `docs/USER_MANUAL.md`, `docs/DEPLOYMENT_READINESS.md`, `docs/DESIGN.md`, `docs/SKILL.md`, `docs/progress.md`, `handover.md`, `tests/README.md`, and `asog-technology-business-incubator-(asog-tbi)-DESIGN.md`.
- Verification: `git diff --cached --check` passed for the release commit and the final main merge. `C:\xampp\php\php.exe -l` passed for all 88 staged PHP files after the main merge. No actual Git conflict markers remain. The local SQL dump, `temp/`, and standalone Figma/Vite export were removed from the main merge and ignored. Focused PHPUnit execution was attempted, but the only accessible PHP binary was XAMPP PHP 8.0.30 and PHPUnit requires PHP 8.1+ while the app requires PHP 8.2+.
- Blockers: Runtime PHPUnit verification requires an accessible PHP 8.2+ CLI binary.
- Next step: Commit, merge to `main`, and push the achieved milestone.
