# ASOG TBI Dataset Repository Tests

This repository uses PHPUnit 10 through CodeIgniter 4's test bootstrap. The tests cover the current milestone behavior: dataset visibility and downloads, password reset eligibility, citation formats, moderation workflow contracts, automatic review assignment, and theme isolation.

## Requirements

- PHP 8.2 or newer.
- Composer dependencies installed with `composer install`.
- A working test database configuration for feature tests that need migrations.

The local XAMPP PHP 8.0 binary is useful only for syntax checks. Do not use it to claim the application test suite passes because the application requires PHP 8.2 or newer.

## Common Commands

Run the full configured suite:

```powershell
composer test
```

Run the unit tests only:

```powershell
vendor\bin\phpunit tests\unit
```

Run a focused test class:

```powershell
vendor\bin\phpunit tests\unit\ThemeIsolationContractTest.php
```

Check available routes before browser or workflow QA:

```powershell
php spark routes
```

## Current Test Areas

| Area | Example file |
|---|---|
| Dataset catalog and download access | `tests/Feature/DatasetAccessTest.php` |
| Password reset flow | `tests/Feature/PasswordResetFlowTest.php` |
| Citation output | `tests/unit/CitationFormatTest.php` |
| Moderation workflow rules | `tests/unit/ModerationWorkflowContractTest.php` |
| Automatic review assignment | `tests/unit/AutomaticReviewAssignmentTest.php` |
| CSS/theme ownership | `tests/unit/ThemeIsolationContractTest.php` |

## Known Local Constraint

Some database-backed feature tests require the configured test database driver and extensions to be available. If the suite stops during test database setup, fix the local PHP/database extension issue before reporting feature tests as passing.
