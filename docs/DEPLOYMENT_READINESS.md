# Deployment Readiness

This checklist supports Marc's conditional deployment tasks. Use it only when a server is available and the current `main` build has passed the PHP 8.2+ verification commands in `SETUP.md`.

## Go / No-Go Conditions

- PHP 8.2 or newer is installed on the target server.
- Composer dependencies install without platform overrides.
- MySQL or MariaDB database credentials are available.
- `.env` is configured for the target domain, database, and production environment.
- `php spark migrate` completes on the target database.
- `php spark db:seed MvpSeeder` is run only for demo/staging environments, not for production data unless explicitly approved.
- `php spark routes` and `composer test` pass under PHP 8.2+.
- Upload storage under `writable/uploads/datasets/` is writable by the web server and not publicly browsable.
- Direct public access to `writable/`, `.env`, `vendor/`, and database dumps is blocked.
- Demo account passwords are changed or demo accounts are removed before shared production access.

## Deployment Steps

1. Pull the approved branch or release archive.
2. Run `composer install --no-dev --optimize-autoloader` for production, or `composer install` for staging.
3. Copy and configure `.env`.
4. Set `CI_ENVIRONMENT = production` for production.
5. Set `app.baseURL` to the final HTTPS URL.
6. Configure the database connection.
7. Run `php spark migrate`.
8. Confirm `writable/cache`, `writable/logs`, `writable/session`, and `writable/uploads` are writable.
9. Point the web server document root to `public/`.
10. Run the role walkthrough from `SETUP.md`.

## Post-Deployment Evidence

Record the following in the final deployment notes:

- Branch or commit deployed.
- PHP version and Composer install result.
- Migration status.
- Whether demo seed data was used.
- Public catalog smoke test.
- Contributor upload smoke test.
- Ethics assignment and decision smoke test.
- Technical assignment and decision smoke test.
- Administrator publication smoke test.
- Protected download smoke test.
- Known blockers or deferred production risks.

## Deferred Production Scope

Automated ZIP diagnostics, malware scanning, backup/restore automation, advanced reports, restricted-access request approval, email delivery, and generalized notifications remain deferred unless separately added to the task tracker.
