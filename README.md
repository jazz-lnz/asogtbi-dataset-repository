# ASOG TBI Dataset Repository

`main` is the achieved milestone branch for the ASOG TBI Dataset Repository: a unified CodeIgniter 4 and MySQL application for public dataset discovery, contributor submissions, two-stage institutional verification, and administrator-controlled publication.

---

## Project Overview

Academic and institutional research requires a centralized, trustworthy platform to share datasets while enforcing technical quality and research ethics compliance. The **ASOG TBI Dataset Repository** solves this by providing a role-governed repository where datasets undergo strict multi-stage institutional review before becoming publicly discoverable and citable.

### Milestone Status
The mainline release milestone is achieved. The repository includes the public catalog, contributor workspace, reviewer workspaces, administrator governance portal, Google/password account separation, protected dataset downloads, current as-built documentation, and a comprehensive user manual.

---

## Methodology

The platform was engineered using a structured, governance-driven web development methodology:

1. **Role-Based Workspace Architecture:** Designed four isolated operational workspaces (**Public/Guest**, **Contributor**, **Technical & Research Ethics Reviewers**, and **Repository Administrator**) within a unified CodeIgniter 4 MVC architecture.
2. **Two-Stage Verification State Machine:** Enforced a strict sequential governance workflow (`Pending Technical` -> `Pending Ethics` -> `Awaiting Publication` -> `Published`). Submissions requiring revisions return to the exact originating review stage, while updates to published datasets automatically re-trigger technical verification.
3. **Protected Asset Storage & Security:** Isolated uploaded dataset archives under `writable/uploads/` outside the web server's `public/` directory to prevent unauthorized direct downloads, pairing access controls with Google OAuth and password account separation.
4. **Reproducible Database & Test Engineering:** Built schema migrations (`php spark migrate`), role and walkthrough seeders (`DemoAccountsSeeder`, `MvpSeeder`), append-only implementation tracking, and automated feature/unit tests via **PHPUnit**.

---

## Results & Key Outcomes

- **End-to-End Dataset Governance Pipeline:** Delivered a complete lifecycle supporting dataset uploads, version updates, structured technical and ethics compliance reviews, draft saving, and final administrative publication gating.
- **Public Discovery & Citation Engine:** Launched a searchable, filterable public catalog displaying only non-archived `published` datasets, complete with automated citation generation, dataset recommendations, and protected ZIP downloads.
- **Auditability & Operational Oversight:** Implemented workload-aware reviewer assignment, accountable reassignment, review timelines, immutable ethics decisions, and system-wide administrative audit logs.
- **Comprehensive As-Built Documentation:** Produced complete technical, operational, and user documentation (`SOFTWARE_DOCUMENTATION.md`, `USER_MANUAL.md`, `SETUP.md`, and `Database-Repo-SRS.md`) alongside a seeded walkthrough environment.

---
## Included Workspaces

- Public catalog, search, filters, dataset details, citation, recommendations, and protected downloads
- Contributor registration, upload, version updates, revision responses, status notifications, and self-archive
- Technical review with protected ZIP access, searchable queues, assignment aging, structured findings, draft saving, and required manual verification
- Research Ethics review with technical-approval context, structured compliance findings, draft saving, and immutable decisions
- Repository administration with operational metrics, stage queues, workload-aware assignment, accountable reassignment, final publication gating, users, roles, archive/restore, review timelines, and audit logs

Only non-archived `published` datasets appear publicly. New and updated submissions move through:

```text
Pending Technical -> Pending Ethics -> Awaiting Publication -> Published
       |                  |
       +-> Revision       +-> Revision
       +-> Rejected       +-> Rejected
```

Revision resubmissions return to the same stage. Updates to published datasets restart technical verification so package changes are checked before ethics review.

## Documentation

- [SETUP.md](SETUP.md) contains installation, migrations, demo accounts, and the review walkthrough.
- [docs/USER_MANUAL.md](docs/USER_MANUAL.md) is the professional software user manual for guests, contributors, reviewers, administrators, and support staff.
- [SOFTWARE_DOCUMENTATION.md](SOFTWARE_DOCUMENTATION.md) is the as-built technical and operational specification for the achieved milestone.
- [docs/CONTEXT.md](docs/CONTEXT.md) defines roles, workflow rules, and deferred scope.
- [docs/DESIGN.md](docs/DESIGN.md) defines the shared public and governance visual language.
- [docs/SKILL.md](docs/SKILL.md) gives agents implementation guardrails.
- [docs/progress.md](docs/progress.md) is the append-only implementation and verification ledger.
- [docs/ADMIN_REVIEW_DASHBOARD_PLAN.md](docs/ADMIN_REVIEW_DASHBOARD_PLAN.md) records the governance dashboard design and implemented milestones.
- [Database-Repo-SRS.md](Database-Repo-SRS.md) remains the full requirements source.
- [handover.md](handover.md) is the current mainline handover and release wrap-up note.

## Quick Start

```powershell
composer install
Copy-Item .env.example .env
php spark key:generate
php spark migrate
php spark db:seed DemoAccountsSeeder
php spark db:seed MvpSeeder
php spark serve
```

Run `DemoAccountsSeeder` when you only need the contributor, admin, ethics reviewer, and technical reviewer demo accounts. Run `MvpSeeder` when you also need the full walkthrough dataset set.

The application requires PHP 8.2 or newer. Uploaded ZIP files stay under `writable/uploads/` and must never be served directly from `public/`.

## Testing

This repository uses PHPUnit for feature and unit testing.

### Running Tests
To run the full suite:
```bash
./vendor/bin/phpunit
```

## Deferred

Automated ZIP diagnostics, malware scanning, email delivery, browser-driven backup/restore, advanced reports, restricted-access request approval, and generalized notifications remain outside this release.
