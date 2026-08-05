# Mainline Handover

Updated: 2026-08-05
Release status: mainline milestone achieved
Source branch: `rapid-mvp`
Target branch: `main`

## Start Here

1. Run `git status --short --branch`.
2. Read `README.md` for the achieved milestone summary.
3. Read `docs/USER_MANUAL.md` for role-based user procedures.
4. Read `SOFTWARE_DOCUMENTATION.md` for the as-built technical and operational specification.
5. Use `docs/CONTEXT.md` for workflow and role invariants.
6. Use `docs/progress.md` only as the append-only implementation and verification ledger.

## Achieved Milestone

The repository now contains one integrated CodeIgniter 4 and MySQL application with:

- public homepage, partner/about pages, dataset catalog, search, filters, pagination, dataset detail, citations, recommendations, and authenticated downloads;
- contributor workspace with upload, protected ZIP storage, optional cover images, version updates, revision responses, workflow tracking, notifications, and self-archive;
- Google and administrator-issued password sign-in paths with Google-managed identity locked as read-only;
- technical-first review queues, protected ZIP inspection, structured checklist findings, draft saving, decisions, and immutable history;
- ethics review after technical approval, with retained technical context and structured compliance findings;
- administrator governance overview, stage moderation board, reviewer assignment/reassignment, publication gate, archive/restore, users and roles, and audit logs;
- unified navy/gold/off-white visual language across public, contributor, reviewer, and administrator workspaces;
- as-built documentation and a comprehensive user manual.

## Current Documentation Map

| File | Purpose |
|---|---|
| `README.md` | Main milestone summary, documentation links, quick start, deferred scope. |
| `SETUP.md` | Local setup, migrations, seeders, demo accounts, walkthrough. |
| `docs/USER_MANUAL.md` | Standard user manual for guests, contributors, reviewers, administrators, and support staff. |
| `SOFTWARE_DOCUMENTATION.md` | Full as-built technical and operational specification. |
| `docs/CONTEXT.md` | Product boundary, roles, workflow rules, data ownership, deferred scope. |
| `docs/DESIGN.md` | Canonical UI design system. |
| `docs/SKILL.md` | Short visual implementation companion to the design system. |
| `docs/DEPLOYMENT_READINESS.md` | Deployment checklist and evidence to collect. |
| `docs/progress.md` | Append-only milestone ledger. |
| `Database-Repo-SRS.md` | Historical requirements source. |
| `handover-2026-07-19.md` | Historical Google/auth/homepage handover. |

The old root alpha design export now points to `docs/DESIGN.md`; do not maintain duplicate design specifications.

## Workflow Invariants

```text
pending_technical_review
  -> technical_revision_requested -> contributor resubmits -> pending_technical_review
  -> rejected
  -> pending_ethics_review
       -> ethics_revision_requested -> contributor resubmits -> pending_ethics_review
       -> rejected
       -> awaiting_publication
            -> published
```

- Technical review always happens before ethics review.
- Reviewers act only on assigned review records.
- Reassignment closes the old assignment and preserves history.
- Approval requires every checklist item to be confirmed.
- Revision and rejection require contributor-facing comments.
- Contributor edits are blocked during active review and publication approval.
- Published updates create a new version, leave the catalog, and restart technical review.
- Only administrators publish and set final access classification.
- Only non-archived published datasets appear in public catalog results.
- Protected ZIPs and covers are streamed through authorized controllers.
- Workflow transitions must remain transactional and server-validated.

## Release Validation Before Push

Minimum checks:

```powershell
git diff --check
vendor\bin\phpunit tests\unit
git status --short --branch
```

Use PHP 8.2 or newer for PHPUnit and Spark checks. If only XAMPP PHP 8.0 is available, report that limitation and do not claim runtime test coverage.

## Deferred Scope

Do not implement these without a new scope decision:

- automated ZIP diagnostics;
- malware scanning;
- backup and restore automation;
- advanced reports;
- restricted-access request approval;
- general notification preferences.

SMTP password-reset email and Google OAuth are supported when environment configuration is present.

## Git Guidance

- Keep `.env`, credentials, database dumps, writable runtime data, and uploaded ZIPs out of Git.
- Review untracked files before broad staging.
- Preserve user changes that are not part of the current task.
- Keep commits behavior-focused.
- Merge the release branch into `main` only after validation.
