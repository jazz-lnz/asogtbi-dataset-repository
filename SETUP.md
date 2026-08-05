# ASOG TBI Dataset Repository Setup - main

## Requirements

- PHP 8.2 or newer
- Composer
- MySQL or MariaDB
- Git

Verify the PHP binary used by Composer and Spark:

```powershell
php -v
```

## Install

```powershell
git switch main
composer install
Copy-Item .env.example .env
php spark key:generate
```

Create the database:

```sql
CREATE DATABASE asog_dataset_repo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

Configure `.env`:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'
database.default.hostname = localhost
database.default.database = asog_dataset_repo
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci
database.default.port = 3306
```

For production password-reset email delivery through Google SMTP, follow [docs/GOOGLE_SMTP_EMAIL_SETUP.md](docs/GOOGLE_SMTP_EMAIL_SETUP.md). Do not commit SMTP credentials or add them to `.env.example`.

Clean URLs are enabled by default through `app.indexPage = ''`. On Apache/XAMPP, keep `mod_rewrite` enabled and serve the `public/` directory so `public/.htaccess` can route paths like `/datasets` and `/admin` without `/index.php`.

Run the schema and demo data:

```powershell
php spark migrate
php spark db:seed DemoAccountsSeeder
php spark db:seed MvpSeeder
php spark serve
```

If you only need the login accounts, run `DemoAccountsSeeder`. `MvpSeeder` also normalizes these same demo accounts and then loads the walkthrough datasets.

Optional large dummy catalog seed:

```powershell
php spark db:seed DummyPublishedUploadsSeeder
```

This uses `dummydata/dataset1.csv` as the metadata source, but seeds each record as a published public ZIP upload owned by user `1` and discloses `.csv` as the format inside the ZIP. Run `php spark migrate` first so the `2026-07-20` migrations can clear older CSV-based dummy file rows and add the `content_formats` disclosure field, then run `MvpSeeder` so the demo contributor and administrator accounts exist.

## Demo Accounts

All seeded accounts use password `change-me`.

| Role | Email |
|---|---|
| Contributor | `user@example.test` |
| Repository Administrator | `admin@example.test` |
| Research Ethics Reviewer | `ethics@example.test` |
| Technical Reviewer | `technical@example.test` |

Change demo passwords before any shared or production deployment.

## Verification Walkthrough

1. Sign in as the contributor and upload a ZIP dataset. Confirm `Pending Technical Review`.
2. Sign in as the administrator, open Dataset Moderation, select `Technical assignment`, and assign the technical reviewer using the workload information.
3. Sign in as the technical reviewer, open `Needs action`, download and manually inspect the protected ZIP, record each checklist item as Confirmed, Issue found, or Not reviewed, save a draft, then approve.
4. Sign in as the administrator, select `Ethics assignment`, and assign the ethics reviewer.
5. Sign in as the ethics reviewer, confirm the technical approval context, complete every ethics checklist item, and approve.
6. Sign in as the administrator, open `Ready to publish`, inspect both approvals, choose the final access type, check the publication confirmation, and publish.
7. Confirm the dataset appears in the allowed public or authenticated catalog scope and that every action appears in Audit Logs.

For revision testing, request revision at either stage, update the dataset as the contributor, and confirm it returns to that same stage with a new review round.

For reassignment testing, create or activate a second reviewer with the matching role, open an active stage record as administrator, choose `Reassign review`, provide a reason, and confirm the previous reviewer loses decision access immediately.

## Commands

```powershell
php spark routes
php spark migrate:status
composer test
```

## Deployment Readiness

Deployment remains conditional on server availability and successful PHP 8.2+ verification. Use [docs/DEPLOYMENT_READINESS.md](docs/DEPLOYMENT_READINESS.md) for the go/no-go checklist, production setup steps, and evidence to record after deployment.

Uploaded files belong under `writable/uploads/datasets/`. Do not commit `.env`, credentials, database dumps, `vendor/`, writable runtime data, or uploaded ZIP files.

Automated ZIP inspection, antivirus, backup/restore, reports, restricted-access requests, and email notifications are deferred.
