# ASOG TBI Dataset Repository User Manual

## Document Control

| Item | Value |
|---|---|
| Document title | ASOG TBI Dataset Repository User Manual |
| System release | Mainline milestone release, integrated CodeIgniter application |
| Manual version | 1.0 |
| Manual date | 2026-08-05 |
| Intended audience | Guests, contributors, reviewers, repository administrators, and support staff |
| Related documents | `README.md`, `SETUP.md`, `docs/CONTEXT.md`, `SOFTWARE_DOCUMENTATION.md` |
| Sensitive-data rule | This manual describes configuration names and user procedures. It does not contain credentials, `.env` values, reset tokens, or uploaded dataset contents. |

## Table of Contents

1. Introduction
2. System Overview
3. Roles and Permissions
4. Accessing the System
5. Common Interface Elements
6. Guest and Public User Procedures
7. Contributor Procedures
8. Reviewer Procedures
9. Repository Administrator Procedures
10. Workflow and Status Reference
11. Data and File Handling
12. Account, Security, and Privacy Rules
13. Notifications and Audit Trail
14. Troubleshooting
15. Operational Notes
16. Glossary
17. Quick Route Reference

## 1. Introduction

### 1.1 Purpose

The ASOG TBI Dataset Repository is a web-based institutional system for submitting, reviewing, publishing, discovering, citing, and downloading research datasets. This manual explains how users operate the system from the public catalog, contributor workspace, technical and ethics review workspaces, and administrator portal.

### 1.2 Scope

This manual covers the implemented system behavior in the current repository. It includes:

- public dataset discovery;
- Google and password sign-in;
- contributor uploads, revisions, updates, and archive actions;
- technical review;
- research ethics review;
- administrator moderation, publication, user management, and audit review;
- in-app notifications; and
- common troubleshooting.

This manual does not replace the developer setup guide, deployment checklist, or full technical specification.

### 1.3 Audience

| Audience | Use this manual to |
|---|---|
| Guest | Search, inspect, cite, and understand dataset access. |
| Contributor | Upload datasets, track moderation, revise records, update published datasets, and manage owned records. |
| Technical Reviewer | Verify ZIP packages, metadata, documentation, file suitability, and declared formats. |
| Ethics Reviewer | Verify consent, anonymization, sensitivity, source legitimacy, and access classification. |
| Repository Administrator | Assign reviewers, publish records, manage users, archive/restore records, and inspect audit activity. |
| Support Staff | Diagnose sign-in, access, workflow, upload, and notification issues. |

## 2. System Overview

### 2.1 Product Description

The repository is one integrated web application with two connected areas:

- the public and contributor-facing website; and
- the governance portal for reviewers and administrators.

Both areas use the same accounts, dataset records, protected files, review records, notifications, and audit logs.

### 2.2 Main Capabilities

- Browse and search approved datasets.
- View dataset metadata, statistics, citations, and recommendations.
- Require sign-in before downloading dataset ZIP packages.
- Submit datasets as protected ZIP packages with structured metadata.
- Route new and updated submissions through technical review before ethics review.
- Save reviewer checklist drafts before final decisions.
- Request contributor revisions, reject submissions, or approve review stages.
- Publish only after both review stages approve the current version.
- Maintain version history, review history, notification history, and audit logs.
- Manage users, sign-in methods, account status, and role assignments.

### 2.3 System Boundaries

The current release does not include automated malware scanning, automated ZIP diagnostics, public self-registration, advanced reports, formal restricted-access request approvals, or general email notifications. Password reset email delivery depends on configured SMTP.

## 3. Roles and Permissions

### 3.1 Role Summary

| Role | Main responsibility |
|---|---|
| Guest | Browse public pages and inspect public metadata. |
| User | Submit datasets, track status, revise datasets, update published datasets, download eligible published files, and archive owned records. |
| Technical Reviewer | Decide assigned technical reviews only. |
| Ethics Reviewer | Decide assigned ethics reviews only. |
| Repository Administrator | Manage moderation, publication, access classification, users, roles, archive/restore actions, review oversight, and audit logs. |

Accounts may hold multiple roles. A user with multiple roles sees the matching workspace links in the account menu or portal navigation. An administrator can submit a review decision only when the account also has the matching reviewer role and is assigned to that review.

### 3.2 Permission Matrix

| Action | Guest | User | Technical Reviewer | Ethics Reviewer | Administrator |
|---|---:|---:|---:|---:|---:|
| View public homepage and about pages | Yes | Yes | Yes | Yes | Yes |
| Browse public catalog metadata | Yes | Yes | Yes | Yes | Yes |
| View institutional or restricted catalog entries | No | Yes | Yes | Yes | Yes |
| Download eligible published ZIP files | No | Yes | Yes | Yes | Yes |
| Upload a dataset | No | Yes | Yes, if also user | Yes, if also user | Yes, if also user |
| Edit owned revision-requested datasets | No | Owner only | Owner only | Owner only | Owner only |
| Update owned published datasets | No | Owner only | Owner only | Owner only | Owner only |
| Review assigned technical records | No | No | Yes | No | Only if also assigned technical reviewer |
| Review assigned ethics records | No | No | No | Yes | Only if also assigned ethics reviewer |
| Assign or reassign reviewers | No | No | No | No | Yes |
| Publish datasets | No | No | No | No | Yes |
| Manage users and roles | No | No | No | No | Yes |
| View audit logs | No | No | No | No | Yes |

## 4. Accessing the System

### 4.1 Requirements

Users need a modern web browser with JavaScript enabled. Contributors and maintainers need an active repository account. Downloading any dataset package requires sign-in.

### 4.2 Sign-In Methods

The login page separates two sign-in paths:

- **Google account**: for verified `my.cspc.edu.ph` Google Workspace accounts.
- **Password account**: for administrator-issued repository credentials, usually for approved external collaborators.

### 4.3 Sign In with Google

Prerequisites:

- Google sign-in is configured by the system administrator.
- The Google account email is verified.
- The email ends with the allowed institutional domain, normally `@my.cspc.edu.ph`.

Steps:

1. Open `Login`.
2. Select `Continue with my.cspc Google Account`.
3. Choose the correct Google account.
4. Approve the Google prompt if asked.

Expected result:

- First-time Google users receive a repository user account automatically.
- Google name and email are synced from Google.
- Google-managed name, email, and password fields are read-only in repository settings.

### 4.4 Sign In with Password

Prerequisites:

- A repository administrator created the account.
- The account is active.
- The user has the email and temporary or current password.

Steps:

1. Open `Login`.
2. Enter the issued email.
3. Enter the password.
4. Select `SIGN IN`.

Expected result:

- Contributors are sent to `My datasets`.
- Technical reviewers are sent to the technical review queue.
- Ethics reviewers are sent to the ethics review queue.
- Repository administrators are sent to the governance overview.

### 4.5 Request Password Credentials

Password accounts are not self-created. To request issued credentials:

1. Open `Register` or `Request access`.
2. Select `Request Password Credentials`.
3. Send the request to `repository@cspc.edu.ph`.
4. Include the requester identity, purpose, and needed access scope.

The repository team reviews the request and creates credentials only for approved users.

### 4.6 Reset a Password

Password reset applies only to active administrator-issued local accounts outside the Google sign-in path.

Steps:

1. Open `Forgot password?`.
2. Enter the password-account email.
3. Submit the request.
4. Open the reset link if email delivery is configured.
5. Enter and confirm the new password.

Google users must recover their account through Google, not through repository password reset.

## 5. Common Interface Elements

### 5.1 Public Header

The public header provides access to the home page, browse catalog, account menu, notifications for signed-in users, and sign-in or sign-out actions.

### 5.2 Governance Portal

The portal is used by contributors, reviewers, and administrators after sign-in. Portal navigation shows only the sections allowed by the current account roles.

### 5.3 Account Menu

The account menu contains workspace links, account settings, and sign-out. Multi-role users may see several workspace links.

### 5.4 Notification Bell

The notification bell shows recent repository activity, unread counts, and a mark-read action. Notifications include upload confirmations, assignments, review outcomes, publication updates, and workflow attention items.

### 5.5 Toast Messages

Success, error, and attention messages appear as toast-style notices. Read them before continuing, especially after upload, review, publication, account, and sign-in actions.

### 5.6 Search, Filters, and Pagination

Catalog, dashboard, review, moderation, user, and audit screens use search boxes, filters, tabs, and pagination. When results look incomplete, clear filters or reset the current tab before assuming a record is missing.

## 6. Guest and Public User Procedures

### 6.1 Browse the Homepage

The homepage introduces the repository, public search, institutional partners, repository features, counters, and featured or popular datasets. Use the main search area to start dataset discovery.

### 6.2 Browse Datasets

Steps:

1. Open `Browse Datasets` or `/datasets`.
2. Enter a keyword in the search field.
3. Apply filters as needed:
   - data type;
   - category; and
   - date uploaded.
4. Use result cards to preview or open dataset details.
5. Use pagination controls to move across result pages.

Guest visibility is limited to published public datasets. Signed-in users may see additional institutional or restricted entries according to repository rules.

### 6.3 Inspect a Dataset

The dataset detail page may show:

- dataset title and cover;
- publication date;
- category, data type, tags, and formats inside ZIP;
- research title, project head, members, source type, and source link;
- version;
- view and download counts;
- download action or restriction message;
- citation modal; and
- recommended datasets.

### 6.4 Cite a Dataset

Steps:

1. Open a dataset detail page.
2. Select the citation action.
3. Choose a citation format:
   - APA;
   - MLA;
   - ACM; or
   - BibTeX.
4. Copy the displayed citation for research use.

### 6.5 Download a Dataset

Downloads require sign-in. Guests see a sign-in prompt rather than a direct file download.

Steps:

1. Sign in.
2. Open the dataset detail page.
3. Select `Download`.
4. Save the ZIP package.

If the download is unavailable or restricted, the dataset may be private, archived, unpublished, missing a current file, or outside the account's allowed access.

## 7. Contributor Procedures

### 7.1 Open My Datasets

After sign-in, open `My datasets` or `/dashboard`. The contributor dashboard shows owned records, workflow counts, filters, next actions, access summaries, reviewer notes, and upload controls.

Dashboard filters include:

- all records;
- technical review;
- ethics review;
- publication;
- revision;
- published;
- closed;
- access type; and
- search by title, category, or tags.

### 7.2 Upload a New Dataset

Prerequisites:

- You are signed in.
- Your dataset package is prepared as a single ZIP file.
- Sensitive or personal data has been anonymized.

Steps:

1. Open `Upload dataset`.
2. Enter dataset information:
   - dataset title;
   - category;
   - data type;
   - formats inside ZIP;
   - requested access type;
   - description;
   - optional cover image;
   - tags.
3. Enter research information:
   - research title;
   - project head or adviser;
   - members or contributors.
4. Enter source and file information:
   - source type, either primary or secondary;
   - optional source link;
   - protected ZIP package.
5. Confirm anonymization.
6. Select `Review Before Upload`.
7. Review the preview modal.
8. Select `Confirm & Upload`.

Expected result:

- The dataset is created as version `1.0`.
- The dataset enters `Pending Technical Review`.
- The system attempts automatic technical reviewer assignment.
- You receive an in-app notification.

### 7.3 Upload Field Reference

| Field | Required | Notes |
|---|---:|---|
| Dataset Title | Yes | Use a concise descriptive title. |
| Category | Yes | Subject or research area. |
| Data Type | Yes | Text, Image, Audio, Video, Tabular, or configured option. |
| Formats Inside ZIP | Yes | Disclose actual file types, such as `.csv`, `.pdf`, `.png`. |
| Access Type | Yes | Requested publication access classification. |
| Description | Yes | Explain contents, collection method, and intended use. |
| Cover Image | No | JPG, PNG, or WebP; maximum 4 MB and 4000 by 4000 pixels. |
| Tags | Yes | Comma-separated keywords for discovery. |
| Research Title | Yes | Official research, thesis, capstone, or project title. |
| Project Head or Adviser | Yes | Supervising researcher, faculty adviser, or project lead. |
| Members | No | Student researchers, faculty collaborators, incubatees, or contributors. |
| Source Type | Yes | Primary or Secondary. |
| Source Link | No | URL to source or related publication when available. |
| Dataset ZIP File | Yes | A single ZIP file containing the dataset and supporting documentation. |
| Anonymization Confirmation | Yes | Required before submission. |

### 7.4 Track Dataset Status

Open `My datasets` and review each card's workflow stage, latest reviewer note, next step, access type, and available actions.

Common next actions:

- `Locked while technical review is active`: wait for the technical review result.
- `Locked while ethics review is active`: wait for the ethics review result.
- `Revise dataset and resubmit for review`: open the edit form and address reviewer comments.
- `Approved by reviewers and waiting for publication`: wait for administrator publication.
- `Published in the catalog`: the record is available according to its access classification.

### 7.5 Revise a Dataset

Revision is available only when a technical or ethics reviewer requests changes.

Steps:

1. Open `My datasets`.
2. Find the record with `Technical Revision Requested` or `Ethics Revision Requested`.
3. Select `Revise dataset`.
4. Update metadata and, if needed, upload a new ZIP version.
5. Enter a change summary.
6. Review the preview.
7. Confirm and save.

Expected result:

- Technical revisions return to `Pending Technical Review`.
- Ethics revisions return to `Pending Ethics Review`.
- Active review assignment is recreated for the appropriate stage.

### 7.6 Update a Published Dataset

Published records may be updated by the owner.

Steps:

1. Open `My datasets`.
2. Find the published record.
3. Select `Submit update`.
4. Update metadata and upload a new ZIP only if the file changed.
5. Enter a change summary.
6. Confirm and save.

Expected result:

- The published record leaves the catalog while the update is reviewed.
- The version number increments.
- The dataset restarts at technical review.

### 7.7 Archive an Owned Dataset

Contributors can archive eligible owned records when no active review blocks the action.

Steps:

1. Open the dataset edit page from `My datasets`.
2. Select `Archive Dataset` or `Archive record`.
3. Confirm the action if prompted.

Archived datasets are retained in records but removed from normal workflow and catalog surfaces.

## 8. Reviewer Procedures

### 8.1 Reviewer Access Rules

Reviewers can only access reviews assigned to them. Reassigned records remain in history but are no longer decision-accessible to the previous reviewer. Reviewers cannot claim arbitrary records.

### 8.2 Open a Review Queue

Technical reviewers open `/review/technical`. Ethics reviewers open `/review/ethics`.

The queue includes:

- workload metrics;
- `Needs action`, `Completed`, and `All` tabs;
- search;
- assignment-age filters;
- access filters;
- data-type filters;
- sorting; and
- pagination.

### 8.3 Technical Review

Technical review is the first maintainer gate.

Checklist:

| Item | Reviewer verifies |
|---|---|
| Archive readable | ZIP package can be opened and inspected. |
| Metadata complete | Required metadata is clear and complete. |
| Documentation complete | Supporting documentation is present and usable. |
| Formats match | Declared formats inside ZIP match actual contents. |
| Files suitable | Files are appropriate for repository use and review. |

Steps:

1. Open the technical review queue.
2. Select a record.
3. Download the protected ZIP.
4. Manually inspect the archive and documentation.
5. Mark each checklist item as `Confirmed`, `Issue found`, or `Not reviewed`.
6. Add a finding note for every `Issue found`.
7. Add contributor-facing comments when requesting revision or rejecting.
8. Save a draft if the review is incomplete.
9. Submit one final decision:
   - `Approve review`;
   - `Request revision`; or
   - `Reject`.

Approval is available only when every checklist item is confirmed.

### 8.4 Ethics Review

Ethics review begins after technical approval.

Checklist:

| Item | Reviewer verifies |
|---|---|
| Consent clearance | Consent, clearance, or appropriate basis is documented. |
| Anonymization | Sensitive or personal data has been anonymized as declared. |
| Sensitive data | Safeguards are appropriate for sensitive data. |
| Source legitimacy | Dataset source and source link are legitimate and suitable. |
| Access classification | Requested access classification is appropriate. |

Steps:

1. Open the ethics review queue.
2. Select a record.
3. Review the retained technical approval context.
4. Inspect metadata, source, anonymization declaration, and access request.
5. Complete the checklist.
6. Save a draft if needed.
7. Submit a final decision.

If approved, the dataset moves to `Awaiting Publication`. If revision is requested, the contributor resubmits directly to ethics review without repeating technical approval.

### 8.5 Drafts and Final Decisions

Draft saving preserves checklist and comment work without changing dataset status. Final decisions are immutable for the current review record and immediately update contributor workflow.

## 9. Repository Administrator Procedures

### 9.1 Open Governance Overview

Open `/admin`. The governance overview shows operational metrics and records requiring attention, including unassigned stages, active reviews, publication-ready records, aging assignments, and revision records.

### 9.2 Moderate Datasets

Open `/admin/datasets`. Use stage tabs:

- Technical unassigned;
- Technical review;
- Ethics unassigned;
- Ethics review;
- Ready to publish;
- Revision and rejected;
- Published and archived.

Use filters for search, reviewer, age, access, and data type.

### 9.3 Inspect a Dataset

From moderation, select `Inspect record`.

The inspection page includes:

- workflow stepper;
- submission summary;
- current required action;
- protected file and version history;
- review timeline;
- approval summary;
- publication controls when eligible;
- website preview link; and
- recent audit activity.

### 9.4 Assign a Reviewer Manually

Manual assignment is used when automatic distribution did not create an active review.

Steps:

1. Open `Dataset moderation`.
2. Select the relevant unassigned technical or ethics stage.
3. Open `Assign manually`.
4. Select an eligible active reviewer.
5. Confirm assignment.

The reviewer receives access to the assigned record and an in-app notification.

### 9.5 Reassign an Active Review

Steps:

1. Open the active technical or ethics stage.
2. Open `Reassign review`.
3. Choose a different eligible reviewer.
4. Enter the reassignment reason.
5. Confirm reassignment.

Expected result:

- The old assignment is closed as reassigned.
- The old reviewer immediately loses decision access.
- A new assignment is created in the same review round.
- The reassignment reason is retained in the review timeline.

### 9.6 Publish a Dataset

Publication controls unlock only when the dataset is `Awaiting Publication`.

Steps:

1. Open the `Ready to publish` stage or the dataset inspection page.
2. Confirm technical approval.
3. Confirm ethics approval.
4. Select the final access classification.
5. Check the publication confirmation.
6. Select `Publish dataset` or `Publish current version`.

Expected result:

- The dataset becomes `Published`.
- The current version becomes visible according to its access classification.
- The contributor is notified.
- The action is audited.

### 9.7 Archive or Restore a Dataset

Archive:

1. Open a moderation card or inspection page.
2. Open `More actions`.
3. Enter an archive reason.
4. Confirm archive.

Restore:

1. Open the `Published and archived` stage.
2. Select the archived record.
3. Choose `Restore dataset`.

Active assigned reviews block archive actions. Restored pending records may return to the appropriate review assignment flow.

### 9.8 Manage Users and Roles

Open `/admin/users`.

Administrators can:

- create password accounts;
- filter accounts by search, sign-in method, status, and role;
- activate or deactivate accounts;
- assign or remove roles; and
- review whether accounts use Google or password sign-in.

Important safeguards:

- The final active administrator cannot be removed or deactivated.
- Administrators cannot remove their own administrator access through the user editor.
- Every account must retain at least one role.
- A reviewer with active assigned reviews cannot be deactivated or lose the required reviewer role.
- Creating or activating reviewers can trigger assignment of pending review records.

### 9.9 Create a Password Account

Steps:

1. Open `Users and roles`.
2. Expand `Create password account`.
3. Enter name, email, temporary password, status, and roles.
4. Select `Create credentials`.
5. Share the temporary credentials through a secure channel.

Password accounts are intended for approved collaborators who cannot use the institutional Google sign-in path.

### 9.10 View Audit Logs

Open `/admin/audit-logs`.

Audit logs include authentication, upload, update, download, review, assignment, reassignment, publication, archive, restore, role, and user-management activity. Use search to find actions, users, or details.

## 10. Workflow and Status Reference

### 10.1 Standard Dataset Workflow

```text
Pending Technical Review
  -> Technical Revision Requested -> contributor resubmits -> Pending Technical Review
  -> Rejected
  -> Pending Ethics Review
       -> Ethics Revision Requested -> contributor resubmits -> Pending Ethics Review
       -> Rejected
       -> Awaiting Publication
            -> Published
```

### 10.2 Status Table

| Status | Meaning | User action |
|---|---|---|
| Pending Technical Review | ZIP package, metadata, documentation, and formats are being verified. | Wait for decision. |
| Technical Revision Requested | Technical reviewer found corrections. | Revise and resubmit. |
| Pending Ethics Review | Technical review passed; ethics review is active or waiting assignment. | Wait for decision. |
| Ethics Revision Requested | Ethics reviewer found corrections. | Revise and resubmit. |
| Awaiting Publication | Both reviews approved the current version. | Wait for administrator publication. |
| Published | Dataset is released according to final access classification. | Use catalog; owner may submit update. |
| Rejected | Review ended with rejection. | Inspect comments; no normal resubmission action. |
| Archived | Record is retained but removed from active workflow and catalog surfaces. | Owner or administrator may inspect; administrator may restore. |

### 10.3 Access Classification

| Access type | Catalog visibility | Download behavior |
|---|---|---|
| Public | Visible in public catalog. | Signed-in users can download. |
| Institutional | Visible to signed-in repository users. | Signed-in users can download when authorized by repository rules. |
| Restricted | Limited catalog visibility for signed-in users. | Downloads require repository authorization. |
| Private | Hidden from normal catalog users. | Limited to owner and authorized maintainers. |

## 11. Data and File Handling

### 11.1 Protected ZIP Packages

Dataset packages are stored outside the public web directory and streamed through authorized controller routes. Do not share direct server file paths. Download actions are audited.

### 11.2 Dataset Versions

Initial submissions create version `1.0`. Revisions and published updates create additional version records when metadata or files change. Version history remains available to contributors, reviewers, and administrators.

### 11.3 Cover Images

Cover images appear in catalog cards, detail pages, dashboards, and review/admin inspection pages. If no cover image is uploaded, the repository placeholder is used.

### 11.4 Source and Anonymization

Contributors must classify each dataset as primary or secondary and must confirm anonymization before submission. Ethics reviewers verify the declaration and may request revision or reject when evidence is insufficient.

## 12. Account, Security, and Privacy Rules

### 12.1 Google Accounts

Google credentials are the identity source of truth. For Google accounts:

- name and email are synced from Google;
- name and email cannot be edited locally;
- repository password change is not available;
- password recovery must happen through Google; and
- duplicate local password accounts using the same email are rejected.

### 12.2 Password Accounts

Password accounts are created by administrators. Local account users can update their repository profile and password from account settings, subject to validation and current-password checks.

### 12.3 Inactive Accounts

Inactive accounts cannot sign in. Administrators control active or inactive status from `Users and roles`.

### 12.4 Privacy Expectations

Contributors must not upload raw personal or sensitive data unless it has been properly anonymized and cleared for the selected access classification. Reviewers and administrators must treat protected packages as controlled research materials.

## 13. Notifications and Audit Trail

### 13.1 Notifications

Notifications are shown in the header or portal notification menu. They may be generated when:

- a dataset is submitted;
- a review is assigned;
- a reviewer requests revision;
- a reviewer rejects or approves;
- a dataset becomes ready for publication;
- a dataset is published;
- a dataset requires administrator attention.

### 13.2 Audit Trail

The system records audit events for accountability. Administrators can inspect audit logs to review sign-in activity, upload activity, moderation actions, download activity, lifecycle actions, and role changes.

## 14. Troubleshooting

| Issue | Likely cause | What to do |
|---|---|---|
| Google button is disabled | Google OAuth is not configured. | Ask an administrator to configure Google client ID, secret, redirect URI, and allowed domain. |
| Google sign-in says it could not be completed | OAuth, redirect URI, account domain, or local certificate configuration may be wrong. | Confirm the selected account is verified and ends in the allowed domain. Administrators should check OAuth settings and logs. |
| Login says to use Google sign-in | The email belongs to a Google-managed account. | Return to login and use the Google button. |
| Password reset email does not arrive | The account is Google-managed, inactive, not a local password account, or SMTP is not configured. | Use Google recovery for Google accounts or contact support for issued credentials. |
| Dataset does not appear in the catalog | It is unpublished, archived, private, or outside the current user's access scope. | Check `My datasets` or administrator moderation status. |
| Download redirects to login | Downloads require authentication. | Sign in, then retry the download. |
| Download is restricted after sign-in | Dataset access type or ownership does not allow the current account. | Contact the repository team if access is needed. |
| Upload is rejected | Required metadata is missing, the package is not ZIP, the cover is invalid, or the source link is invalid. | Fix highlighted fields and resubmit. |
| Contributor cannot edit a dataset | It is under active review, awaiting publication, rejected, archived, or not owned by the current user. | Wait for a reviewer decision or contact an administrator. |
| Reviewer cannot open a record | The review is not assigned to the account or has been reassigned. | Check the review queue or ask an administrator. |
| Approval button is disabled | Not every checklist item is confirmed. | Complete all checklist items; approval requires all confirmed. |
| Publication controls are hidden | The dataset is not awaiting publication. | Wait for both review stages to approve the current version. |
| Admin cannot deactivate a reviewer | The reviewer has active assigned reviews. | Reassign or complete the active reviews first. |

## 15. Operational Notes

### 15.1 Local Demo Accounts

Development seeders may create these local walkthrough accounts with password `change-me`:

| Role | Email |
|---|---|
| Contributor | `user@example.test` |
| Repository Administrator | `admin@example.test` |
| Research Ethics Reviewer | `ethics@example.test` |
| Technical Reviewer | `technical@example.test` |

These are for local testing only and must not be used as production credentials.

### 15.2 Environment-Specific Google CA Bundle

The `google.caBundle` setting is machine-specific. Leave it blank unless the environment raises a cURL certificate error. If a CA bundle is required on another device, set it to that device's actual certificate bundle path, such as the local XAMPP `curl-ca-bundle.crt` path.

### 15.3 Production Responsibilities

Before production operation, administrators should confirm:

- PHP 8.2 or newer is used;
- database migrations are applied;
- `public/` is the web root;
- clean URL rewriting is enabled;
- `.env` contains production-safe values;
- Google OAuth and SMTP are configured if needed;
- writable upload storage is backed up;
- demo credentials are removed or changed; and
- uploaded files are not served directly from `public/`.

## 16. Glossary

| Term | Definition |
|---|---|
| Access classification | Publication scope: Public, Institutional, Restricted, or Private. |
| Active review | A review assignment with decision access still open. |
| Audit log | Administrator-visible accountability record of important system actions. |
| Contributor | User who owns and submits a dataset. |
| Dataset | Metadata record plus protected file package and version history. |
| Ethics review | Review stage for consent, anonymization, sensitivity, source, and access suitability. |
| Protected package | Dataset ZIP stored outside the public web directory. |
| Repository administrator | User with governance control over moderation, publication, users, and audit logs. |
| Technical review | Review stage for package readability, metadata, documentation, declared formats, and file suitability. |
| Version | A recorded dataset revision or publication update. |

## 17. Quick Route Reference

| Area | Route |
|---|---|
| Home | `/` |
| About platform | `/about/platform` |
| Partners | `/about/partners` |
| Browse datasets | `/datasets` |
| Dataset detail | `/datasets/{id}` |
| Dataset cover | `/datasets/{id}/cover` |
| Dataset download | `/datasets/{id}/download` |
| Login | `/login` |
| Google sign-in | `/auth/google` |
| Google callback | `/auth/google/callback` |
| Request access | `/register` |
| Forgot password | `/forgot-password` |
| Reset password | `/reset-password` |
| Account settings | `/account/settings` |
| My datasets | `/dashboard` |
| Portal contributor dashboard | `/portal/dashboard` |
| Upload dataset | `/upload` |
| Edit owned dataset | `/datasets/{id}/edit` |
| Technical review queue | `/review/technical` |
| Ethics review queue | `/review/ethics` |
| Governance overview | `/admin` |
| Dataset moderation | `/admin/datasets` |
| Admin dataset inspection | `/admin/datasets/{id}` |
| Users and roles | `/admin/users` |
| Audit logs | `/admin/audit-logs` |

## Revision History

| Version | Date | Description |
|---|---|---|
| 1.0 | 2026-08-05 | Initial comprehensive user manual based on the current repository implementation. |
