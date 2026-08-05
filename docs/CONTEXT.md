# Integrated Repository Context

## Product Boundary

This repository is one integrated CodeIgniter 4 application containing the public catalog, contributor workspace, Research Ethics review, Technical review, and Repository Administration portal. `Database-Repo-SRS.md` is the requirements source; this file records the implemented first-release boundary.

## Roles

| Role | Responsibility |
|---|---|
| Guest | Browse, inspect, cite, and download eligible public datasets |
| User | Submit datasets, track status, respond to revisions, update published datasets, and self-archive owned records |
| Ethics Reviewer | Decide only ethics reviews assigned by an administrator |
| Technical Reviewer | Decide only technical reviews assigned by an administrator |
| Repository Administrator | Manage assignments, publication, access type, archive/restore, users, roles, review oversight, and audit logs |

Accounts may hold multiple roles. An administrator may submit a review decision only when also holding the matching reviewer role and assigned that review.

## Workflow

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

- Administrators assign every review; reviewers cannot claim arbitrary submissions.
- Technical verification is the first maintainer gate so corrupted ZIPs, wrong file entries, and package/metadata mismatches are caught before ethics review.
- Checklist drafts may be saved without changing dataset state. Each item records `confirmed`, `issue`, or `not_reviewed`; issue findings require an item note.
- Approval requires every stage checklist item to be confirmed. Rejection and revision require contributor-facing comments.
- Review records are immutable history. Reassignment closes the old assignment and creates another record in the same round.
- Contributor edits are locked during active review and publication approval.
- A published update creates a new version, removes the record from the catalog, and restarts technical verification.
- Only non-archived `published` datasets appear in public browsing and recommendations.
- Protected files are streamed through authorized controllers; direct file URLs are forbidden.
- Every assignment, decision, publication, lifecycle action, role change, protected review download, login, upload, update, archive, and public download is audited.

## Review Checklists

Technical verification is manual and covers ZIP readability, metadata completeness, documentation, declared formats, and file suitability. Reviewers work from paginated assigned queues with aging indicators, protected file context, draft saving, and structured findings. Automated package inspection and malware scanning are not part of this release.

Ethics verification covers consent or clearance, anonymization, sensitive-data safeguards, source legitimacy, and access classification after the package passes technical review. The ethics workspace displays the retained technical approval before a final ethics decision.

## Data Ownership

- `datasets` stores current lifecycle state and publication metadata.
- `dataset_versions` and `dataset_files` preserve submission versions and protected packages.
- `reviews` preserves assignment, stage, round, structured checklist drafts, comments, reassignment reasons, and decision history.
- `notifications` currently carries review assignments, review outcomes, and publication results.
- `audit_logs` is the accountability record visible to repository administrators.

## Deferred Scope

- Automated ZIP diagnostics and malware scanning
- Email notifications
- Browser-triggered backup and restore
- Advanced usage and compliance reports
- Restricted dataset access-request approval
- General-purpose notification preferences
- Advanced recommendation algorithms

## Agent Continuity

Read `docs/progress.md` before changing this workflow. Append a dated entry after each material milestone with branch/commit, completed behavior, schema impact, important files, verification, blockers, and the exact next step. Never rewrite prior progress entries.
