# ASOG TBI Dataset Repository User Manual

Software Manual

Mainline Milestone Release

Manual Version 1.1

Manual Date: 2026-08-05

Prepared for: ASOG Technology Business Incubator and the CSPC research community

Prepared by: ASOG TBI Dataset Repository project team

System type: CodeIgniter 4 and MySQL web application

## Front Matter

### Document Control

| Item | Value |
|---|---|
| Document title | ASOG TBI Dataset Repository User Manual |
| Document type | Software user manual |
| System release | Mainline milestone release, integrated CodeIgniter application |
| Manual version | 1.1 |
| Manual date | 2026-08-05 |
| Primary audience | Guests, contributors, technical reviewers, research ethics reviewers, repository administrators, and support staff |
| Related documents | `README.md`, `SETUP.md`, `docs/CONTEXT.md`, `docs/DEPLOYMENT_READINESS.md`, `SOFTWARE_DOCUMENTATION.md`, `tests/README.md` |
| Template basis | Professional software-manual structure: cover/title metadata, use notice, acknowledgments, software description, installation/access information, contents, figures/tables registers, numbered chapters, appendices, and troubleshooting. |
| Sensitive-data rule | This manual describes user procedures and configuration names only. It must not contain credentials, `.env` values, reset tokens, uploaded dataset contents, or private review material. |

### Use and Support Notice

This manual describes the standard operation of the ASOG TBI Dataset Repository at the achieved mainline milestone. The system supports institutional dataset discovery, contributor submission, two-stage review, administrator-controlled publication, protected downloads, account management, notifications, and audit visibility.

Users remain responsible for submitting accurate metadata, respecting consent requirements, and ensuring that uploaded datasets are suitable for the selected access classification. Review approval does not replace institutional research ethics rules, data privacy rules, or office-level publication policies.

Operational support, account issuance, publication decisions, and production environment configuration must be handled by the assigned repository administrators or institutional IT support group.

### Citation Information

Recommended citation for this manual:

ASOG Technology Business Incubator. ASOG TBI Dataset Repository User Manual. Version 1.1. 2026-08-05.

### Acknowledgments

The repository is prepared for ASOG TBI and the CSPC research community. Its current release reflects institutional needs for dataset organization, responsible review, controlled publication, and reusable research outputs.

Institutional partner branding and public-facing content should be reviewed by the appropriate office before production publication.

### Abstract

The ASOG TBI Dataset Repository is a centralized platform for organizing, reviewing, publishing, discovering, citing, and downloading research datasets. It supports public browsing, authenticated downloads, Google and issued-password sign-in, contributor submissions, technical review, research ethics review, administrator governance, audit logging, and in-app notifications. This manual provides role-based operating procedures and reference information for the implemented mainline milestone release.

### Revision History

| Version | Date | Description |
|---|---|---|
| 1.0 | 2026-08-05 | Initial comprehensive user manual based on the current repository implementation. |
| 1.1 | 2026-08-05 | Reformatted into a professional software-manual structure following the supplied template style. |

### Software Description

#### One-Line Description

The ASOG TBI Dataset Repository is a web-based institutional platform for managing the lifecycle of research dataset submissions from upload through review, publication, discovery, citation, and controlled download.

#### Description

The repository combines a public dataset catalog, contributor workspace, reviewer queues, and administrator governance portal in one CodeIgniter 4 application. Users can browse published records, search and filter metadata, view citation information, and download eligible ZIP packages after sign-in. Contributors can submit ZIP packages with structured metadata and track moderation status. Reviewers verify technical quality and research ethics suitability. Administrators manage users, assignments, publication, archive actions, and audit records.

#### Benefits and Value

| Benefit | Value to users |
|---|---|
| Centralized dataset catalog | Gives students, faculty, incubatees, reviewers, and administrators one place to find and manage repository records. |
| Structured submission workflow | Keeps metadata, source details, anonymization confirmation, files, versions, review records, and publication actions connected. |
| Two-stage verification | Separates technical package checks from ethics and privacy checks before publication. |
| Controlled access | Allows public, institutional, restricted, and private dataset classifications with protected file delivery. |
| Account separation | Lets CSPC users sign in with Google while approved external users can receive issued password credentials. |

#### Platform Requirements

| Area | Requirement |
|---|---|
| End-user browser | A current desktop or mobile browser with cookies enabled. |
| User network access | Access to the deployed repository URL. |
| CSPC Google sign-in | A my.cspc.edu.ph Google account authorized by the Google OAuth client configuration. |
| Password sign-in | A password account issued by a repository administrator. |
| Server runtime | PHP 8.2 or newer, Composer dependencies, MySQL or MariaDB, writable application storage, clean URL routing, and configured environment values. |
| Optional services | Google OAuth for CSPC sign-in and SMTP for password reset email delivery. |

#### Keywords

ASOG TBI, CSPC, dataset repository, research data, institutional repository, CodeIgniter, MySQL, Google OAuth, contributor workflow, technical review, ethics review, protected downloads, audit logs.

### Installation and Access Information

End users do not install the repository software. They access the deployed web application through a browser.

Repository maintainers and institutional IT staff should use `README.md`, `SETUP.md`, `docs/DEPLOYMENT_READINESS.md`, and `SOFTWARE_DOCUMENTATION.md` for environment setup, deployment readiness, database migrations, web-server routing, OAuth configuration, SMTP configuration, backup expectations, and release verification.

If a user cannot access the application after deployment, confirm the following before escalating:

1. The user is visiting the correct repository URL.
2. The browser allows cookies and is not blocking the session.
3. The user's account is active.
4. The user has the required role for the page being opened.
5. Google OAuth redirect URLs match the deployed host.
6. Password reset email delivery is configured if local password recovery is expected.
7. File and upload storage directories are writable by the web server.

### Contents

1. Introduction
2. System Overview
3. Accessing the System
4. Roles and Permissions
5. Interface and Navigation
6. Public Catalog Procedures
7. Contributor Procedures
8. Reviewer Procedures
9. Repository Administrator Procedures
10. Workflow, Data Handling, and Security Reference
11. Troubleshooting and Support
12. Operational Notes
13. References
Appendix A. Route Reference
Appendix B. Local Demo Accounts
Appendix C. Glossary and Acronyms
Appendix D. Known Release Limits

### List of Figures

| Figure | Title | Location |
|---|---|---|
| Figure 2-1 | Standard dataset workflow | Section 2.4 |

### List of Tables

| Table | Title | Location |
|---|---|---|
| Table FM-1 | Document Control | Front Matter |
| Table FM-2 | Revision History | Front Matter |
| Table FM-3 | Benefits and Value | Front Matter |
| Table FM-4 | Platform Requirements | Front Matter |
| Table 1-1 | Manual Audience | Section 1.3 |
| Table 2-1 | Main Capability Summary | Section 2.2 |
| Table 4-1 | Role Summary | Section 4.1 |
| Table 4-2 | Permission Matrix | Section 4.2 |
| Table 7-1 | Upload Field Reference | Section 7.3 |
| Table 8-1 | Technical Review Checklist | Section 8.3 |
| Table 8-2 | Ethics Review Checklist | Section 8.7 |
| Table 10-1 | Dataset Status Reference | Section 10.1 |
| Table 10-2 | Access Classification Reference | Section 10.2 |
| Table 10-3 | Account Identity Rules | Section 10.4 |
| Table 11-1 | Troubleshooting Matrix | Section 11.2 |
| Table A-1 | Public and Auth Routes | Appendix A |
| Table A-2 | Reviewer Routes | Appendix A |
| Table A-3 | Administrator Routes | Appendix A |
| Table B-1 | Development Demo Accounts | Appendix B |
| Table C-1 | Glossary | Appendix C |
| Table C-2 | Acronyms | Appendix C |

## 1. Introduction

### 1.1 Purpose

This manual explains how to use the ASOG TBI Dataset Repository from the perspective of each operating role. It covers public browsing, account access, contributor uploads, review decisions, administrator moderation, protected downloads, workflow states, notifications, and common support issues.

### 1.2 Scope

This manual covers behavior implemented in the mainline milestone release:

- public homepage and dataset catalog;
- dataset search, filtering, pagination, detail pages, citations, and recommendations;
- Google sign-in for authorized CSPC accounts;
- issued-password sign-in for approved non-Google access;
- request-access flow for password credential requests;
- contributor upload, revision, update, archive, and status tracking;
- technical reviewer queue and checklist workflow;
- research ethics reviewer queue and checklist workflow;
- automatic and manual reviewer assignment;
- administrator publication, archive, restore, user, role, and audit-log management;
- protected dataset file storage and download controls; and
- in-app notifications and audit records.

This manual does not replace the full technical specification, deployment guide, database migration history, production security review, or institutional research ethics policies.

### 1.3 Manual Audience

| Audience | Use this manual to |
|---|---|
| Guest | Search and inspect public dataset records before signing in. |
| Contributor | Upload datasets, track moderation, respond to revision requests, update published records, and archive owned records. |
| Technical reviewer | Verify protected ZIP packages, metadata, documentation, declared file formats, and file suitability. |
| Research ethics reviewer | Verify consent, anonymization, sensitive-data safeguards, source legitimacy, and access classification. |
| Repository administrator | Assign or reassign reviewers, publish records, manage users and roles, archive or restore records, and inspect audit activity. |
| Support staff | Diagnose access, workflow, upload, download, notification, and account issues. |

### 1.4 Manual Conventions

- Route examples use a leading slash, such as `/datasets`.
- Button and menu labels are written as visible interface text, such as `Upload Dataset`.
- Dataset statuses are written using their user-facing labels, such as `Pending Technical Review`.
- Code, configuration keys, and file paths are written in monospace.
- Procedures assume the user is signed in when the page requires authentication.

## 2. System Overview

### 2.1 Product Context

The repository is one integrated web application with two connected operating areas:

- the public and contributor-facing website; and
- the governance portal for reviewers and administrators.

Both areas use the same users, roles, dataset records, protected files, versions, reviews, notifications, and audit logs. Published catalog records are separated from pending, rejected, archived, and private records by workflow status and access classification.

### 2.2 Main Capability Summary

| Capability | Description |
|---|---|
| Homepage search | Lets users begin dataset discovery from the main public landing view. |
| Dataset catalog | Lists published datasets with search, filters, pagination, and dataset cards. |
| Dataset detail | Shows metadata, file context, citations, statistics, recommendations, and download controls. |
| Protected downloads | Requires sign-in before serving eligible ZIP packages from non-public storage. |
| Contributor workspace | Lets contributors submit and manage owned datasets. |
| Review queues | Gives technical and ethics reviewers assigned worklists with filters and aging context. |
| Review workspace | Provides protected downloads, metadata review, checklist decisions, draft saving, and final decisions. |
| Administrator portal | Provides moderation stages, assignment controls, publication actions, user management, and audit logs. |
| Notifications | Shows repository alerts through the shared notification menu. |
| Audit trail | Records important workflow, account, review, publication, and download events for administrator inspection. |

### 2.3 System Boundaries

The current release does not include automated malware scanning, automated ZIP diagnostics, public self-registration, formal restricted-access request approval, advanced reports, backup/restore automation, or general email notifications. SMTP is used only where password reset delivery has been configured.

Technical file safety, documentation sufficiency, source legitimacy, and ethics suitability remain human review responsibilities in this release.

### 2.4 Standard Dataset Workflow

Figure 2-1. Standard dataset workflow

```text
Contributor uploads ZIP and metadata
        |
        v
Pending Technical Review
        |
        +-- Technical Revision Requested -> Contributor revises -> Pending Technical Review
        |
        +-- Rejected -> Workflow closed
        |
        v
Pending Ethics Review
        |
        +-- Ethics Revision Requested -> Contributor revises -> Pending Ethics Review
        |
        +-- Rejected -> Workflow closed
        |
        v
Awaiting Publication
        |
        v
Published
        |
        +-- Contributor update -> Pending Technical Review
        |
        +-- Archive -> Archived
```

### 2.5 Related Documents

- `README.md` summarizes the achieved milestone, quick start, and documentation map.
- `SETUP.md` explains local setup and environment configuration.
- `docs/CONTEXT.md` records current release boundaries.
- `docs/DEPLOYMENT_READINESS.md` lists deployment checks.
- `SOFTWARE_DOCUMENTATION.md` provides the as-built technical and operational specification.
- `tests/README.md` explains project verification commands and known test constraints.

## 3. Accessing the System

### 3.1 Access Requirements

Guests can browse public dataset records without an account. Authentication is required for dataset downloads, contributor tools, reviewer queues, administrator tools, notifications, and account settings.

Authenticated access uses one of two sign-in methods:

- Google sign-in for authorized my.cspc.edu.ph accounts; or
- password sign-in for accounts issued by a repository administrator.

### 3.2 Sign In with Google

Use Google sign-in when the account belongs to the authorized CSPC Google domain.

1. Open `/login`.
2. Select the Google sign-in option.
3. Choose the my.cspc.edu.ph Google account.
4. Complete Google consent if prompted.
5. Return to the repository after Google redirects back to the application.
6. Confirm that the correct name and email appear in the account menu.

Google account details are treated as authoritative. The repository does not allow Google-authenticated users to edit their repository name, email address, or password from account settings.

### 3.3 Sign In with Password

Use password sign-in when a repository administrator has issued credentials.

1. Open `/login`.
2. Enter the issued email address.
3. Enter the issued password.
4. Submit the form.
5. After sign-in, open the account menu or workspace to confirm the assigned role.

If sign-in fails, confirm the email, password, and account status with a repository administrator.

### 3.4 Request Password Credentials

The `/register` page is a request-access page, not open public self-registration. CSPC users should use Google sign-in. Password accounts are reserved for approved collaborators who need authenticated access without a CSPC Google account.

To request access:

1. Open `/register`.
2. Read the password-account guidance.
3. Provide the requested contact details.
4. Submit the request or follow the listed contact instructions.
5. Wait for administrator approval and credential issuance.

Do not share issued passwords through public or unsecured channels.

### 3.5 Reset a Password

Password reset applies to local password accounts only.

1. Open `/forgot-password`.
2. Enter the email address of the password account.
3. Submit the reset request.
4. Open the reset link within the allowed time.
5. Enter and confirm the new password.
6. Return to `/login` and sign in with the new password.

Google accounts recover access through Google account recovery and my.cspc.edu.ph support, not through the repository password reset flow.

### 3.6 Access Problems

If an authenticated page redirects to `/login`, the session may have expired or the account may not be active. Sign in again and retry the page. If the same page redirects or shows a permission message after sign-in, the account probably lacks the required role.

If Google sign-in fails after account selection, check the OAuth client configuration, redirect URI, allowed domain, and the local `google.caBundle` setting if the environment reports a cURL certificate error.

## 4. Roles and Permissions

### 4.1 Role Summary

| Role | Primary responsibility |
|---|---|
| Guest | Browse public records and inspect public metadata. |
| User / Contributor | Upload and manage owned datasets. |
| Technical reviewer | Verify package readability, metadata completeness, documentation, declared formats, and file suitability. |
| Research ethics reviewer | Verify consent, anonymization, sensitive-data handling, source legitimacy, and access classification. |
| Repository administrator | Manage moderation, reviewer assignments, publication, archive/restore actions, user roles, password accounts, and audit logs. |

One account can hold multiple roles. The interface exposes pages according to the roles stored for the signed-in account.

### 4.2 Permission Matrix

| Action | Guest | Contributor | Technical reviewer | Ethics reviewer | Administrator |
|---|---:|---:|---:|---:|---:|
| View public homepage | Yes | Yes | Yes | Yes | Yes |
| Browse public dataset records | Yes | Yes | Yes | Yes | Yes |
| View institutional or restricted published records | No | Yes | Yes | Yes | Yes |
| Download eligible published dataset ZIP files | No | Yes | Yes | Yes | Yes |
| Upload a dataset | No | Yes | Yes, if role includes user | Yes, if role includes user | Yes, if role includes user |
| Edit owned dataset during revision or published update | No | Yes | Yes, if owner | Yes, if owner | Yes, through admin inspection |
| Access technical review queue | No | No | Yes | No | Only if also technical reviewer |
| Access ethics review queue | No | No | No | Yes | Only if also ethics reviewer |
| Assign or reassign reviewers | No | No | No | No | Yes |
| Publish datasets | No | No | No | No | Yes |
| Archive or restore any dataset | No | Owner archive only | Owner archive only | Owner archive only | Yes |
| Manage users and roles | No | No | No | No | Yes |
| View audit logs | No | No | No | No | Yes |

## 5. Interface and Navigation

### 5.1 Public Header

The public header provides access to the homepage, dataset browsing, account entry points, and authenticated account controls. When signed in, users can reach account settings, notifications, and relevant workspaces from the header or account menu.

### 5.2 Homepage Search

The homepage search is the primary first-screen discovery tool. Search terms entered here lead users into the dataset catalog, where filters and pagination can refine the result set.

### 5.3 Dataset Catalog Controls

The dataset catalog supports:

- keyword search across title, description, tags, category, and formats;
- data type filters;
- category filters;
- date-uploaded filters;
- result summaries; and
- numbered pagination with ellipses for large page counts.

Use the visible page numbers to jump directly to known pages. Use previous and next controls for adjacent navigation.

### 5.4 Dataset Detail Pages

Dataset detail pages display:

- title and status;
- author or contributor context;
- description;
- category, tags, data type, format, and content formats;
- research title, project head, members, source type, and source link where available;
- version information;
- view and download counts;
- citation and BibTeX output;
- recommendations based on metadata similarity; and
- download or sign-in prompts based on access rules.

### 5.5 Governance Portal

The governance portal is used by contributors, reviewers, and administrators. It contains role-specific dashboards, review queues, moderation stages, user management, and audit logs.

Access to portal pages is role-filtered. If a user reaches a page without the required role, the system redirects or shows a permission message.

### 5.6 Account Menu

The account menu shows the signed-in user's identity and provides access to account settings and logout. Password users can update local profile details and password. Google users can view their verified profile details, but the repository treats Google as the source of truth and blocks local identity edits.

### 5.7 Notifications

The shared notification menu shows unread and recent repository activity. Notifications can include submission confirmation, reviewer assignment, review decision, revision request, publication, archive, and workflow attention messages.

Use the mark-read control to clear unread notifications after reviewing them.

### 5.8 Toast Messages and Validation

The system uses visible messages for successful actions, errors, validation issues, and access warnings. If a form returns a validation message, correct the highlighted fields and submit again.

## 6. Public Catalog Procedures

### 6.1 Browse from the Homepage

1. Open `/`.
2. Enter a keyword in the main search bar.
3. Submit the search.
4. Review the matching dataset catalog results.
5. Open a dataset card to inspect the full record.

### 6.2 Browse Dataset Catalog

1. Open `/datasets`.
2. Review the result summary and visible dataset cards.
3. Use keyword search for title, description, tag, category, or format matches.
4. Apply filters for data type, category, or date uploaded.
5. Use pagination to move through result pages.
6. Clear or revise filters when no results appear.

Guests see public records only. Signed-in users can also see institutional and restricted published records. Private records remain hidden from general catalog browsing.

### 6.3 Inspect a Dataset

1. Select a dataset card from the catalog.
2. Confirm the title, description, data type, category, tags, and access label.
3. Review research metadata such as research title, project head, members, and source type.
4. Check the current version and file context.
5. Review citation output if the dataset will be referenced.
6. Use recommendations to find similar published datasets.

### 6.4 Cite a Dataset

1. Open the dataset detail page.
2. Locate the citation or BibTeX section.
3. Copy the citation format required by the target paper, thesis, capstone, or report.
4. Confirm contributor, title, and year details before final use.

Citation text is generated from repository metadata. If metadata appears incomplete or incorrect, contact the contributor or repository administrator before relying on it in formal work.

### 6.5 Download a Dataset

1. Open the dataset detail page.
2. Review the access label and file context.
3. If prompted, sign in.
4. Select the download control.
5. Save the ZIP package.
6. Follow the dataset's source, ethics, and access expectations when using the contents.

Guests cannot download dataset ZIP packages. Public, institutional, and restricted published records are downloadable by eligible signed-in users in the current release. Private records are limited to the owner and administrators.

## 7. Contributor Procedures

### 7.1 Open the Contributor Workspace

1. Sign in.
2. Open `/dashboard` or the contributor workspace link.
3. Review owned dataset cards and status summaries.
4. Open a record to inspect details or required actions.

The contributor workspace separates active review items, revision requests, published records, rejected records, and archived records.

### 7.2 Prepare a Dataset Package

Before upload, prepare:

- one ZIP package containing the dataset files and supporting documentation;
- a clear title and description;
- tags and category;
- data type and formats inside the ZIP;
- research title;
- project head or adviser;
- member list where applicable;
- source type and optional source URL;
- access classification;
- anonymization confirmation; and
- optional cover image in JPG, PNG, or WebP format.

Do not upload files that violate consent, privacy, confidentiality, ownership, or institutional policy.

### 7.3 Upload a New Dataset

1. Sign in with an account that has contributor access.
2. Open `/upload`.
3. Complete every required metadata field.
4. Select the access classification.
5. Confirm anonymization.
6. Attach the ZIP package.
7. Attach an optional cover image if available.
8. Submit the form.
9. Review the confirmation message and the new dataset record.

Initial uploads enter `Pending Technical Review`. The system attempts automatic technical reviewer assignment when eligible reviewers are available.

Table 7-1. Upload Field Reference

| Field | Required | Notes |
|---|---:|---|
| Title | Yes | Use a concise, descriptive dataset title. |
| Description | Yes | Explain what the dataset contains and how it may be useful. |
| Category | Yes | Use the most appropriate research or project category. |
| Tags | Yes | Enter searchable tags, usually comma-separated. |
| Data type | Yes | Select Text, Image, Audio, Video, or Tabular. |
| Formats inside ZIP | Yes | Disclose actual formats such as CSV, XLSX, TXT, JPG, PNG, PDF, or JSON. |
| Source type | Yes | Select Primary or Secondary. |
| Source link | No | Must be a valid `https://` URL when provided. |
| Research title | Yes | Enter the related thesis, capstone, analytics, startup, or research title. |
| Project head or adviser | Yes | Enter the responsible project head, adviser, or supervising person. |
| Members | No | List team members or contributors where applicable. |
| Access type | Yes | Choose Public, Institutional, Restricted, or Private. |
| Anonymized confirmation | Yes | Required before submission. |
| Dataset ZIP | Yes | Only ZIP files are accepted. |
| Cover image | No | JPG, PNG, or WebP, up to 4 MB and 4000 by 4000 pixels. |

### 7.4 Track Dataset Status

1. Open `/dashboard`.
2. Locate the dataset card.
3. Read the workflow stage and reviewer notes if present.
4. Follow the available action, such as revise, view, or update.

Datasets under active moderation are locked from contributor editing until a revision is requested or the record is published.

### 7.5 Revise a Dataset

Revision is available only when the status is `Technical Revision Requested` or `Ethics Revision Requested`.

1. Open `/dashboard`.
2. Select the dataset requiring revision.
3. Open the revise/edit action.
4. Review reviewer comments and checklist issues.
5. Update metadata, package file, cover image, or source details as needed.
6. Submit the revision.
7. Confirm that the record returns to the correct review stage.

Technical revision returns to technical review. Ethics revision returns to ethics review without repeating a completed technical approval for the same workflow path.

### 7.6 Update a Published Dataset

1. Open `/dashboard`.
2. Locate the published dataset.
3. Open the update or edit action.
4. Make the required changes.
5. Upload a replacement ZIP when the dataset contents changed.
6. Submit the update.

Published updates create a new version and return to technical review. The updated version must complete review before publication.

### 7.7 Archive an Owned Dataset

1. Open `/dashboard`.
2. Locate the dataset.
3. Confirm that it is not under active moderation.
4. Use the archive action.
5. Confirm the result.

Contributors cannot archive datasets while moderation is active. Archived records are removed from normal active browsing but retained in repository records.

### 7.8 Contributor Responsibilities

Contributors are responsible for:

- submitting accurate metadata;
- providing usable ZIP contents;
- documenting source and file formats;
- confirming anonymization only when it has actually been performed;
- choosing an appropriate access classification;
- responding to reviewer comments completely; and
- avoiding uploads that contain unauthorized confidential or personal data.

## 8. Reviewer Procedures

### 8.1 Reviewer Access Rules

Reviewers see only reviews assigned to their account. A technical reviewer cannot open ethics-only work unless the account also has the ethics reviewer role. An ethics reviewer cannot open technical-only work unless the account also has the technical reviewer role.

Reassigned reviews are retained in history, but the previous reviewer loses active decision access.

### 8.2 Open the Technical Review Queue

1. Sign in with a technical reviewer role.
2. Open `/review/technical`.
3. Use tabs to view action items, completed items, or all items.
4. Filter by search term, age, access type, or data type.
5. Sort by oldest, newest, title, or contributor.
6. Open an assigned review.

### 8.3 Complete Technical Review

1. Open an assigned technical review.
2. Review metadata and repository context.
3. Download the protected ZIP package.
4. Manually inspect the archive and supporting files.
5. Complete each checklist item.
6. Enter item-level notes where needed.
7. Save a draft or submit a final decision.

Table 8-1. Technical Review Checklist

| Checklist item | Reviewer confirms |
|---|---|
| Archive readable | The protected ZIP can be downloaded and opened. |
| Metadata complete | Required metadata is complete and internally consistent. |
| Documentation complete | Documentation is sufficient for repository users. |
| Formats match | Declared formats match the submitted package contents. |
| Files suitable | Files are usable and suitable for publication. |

Technical decisions:

- `Approve` advances the dataset to ethics review.
- `Request revision` returns the dataset to the contributor for technical corrections.
- `Reject` closes the submission.

### 8.4 Save a Technical Review Draft

Use draft saving when review work is incomplete.

1. Mark completed checklist items.
2. Add notes for open questions or issues.
3. Select the draft-save action.
4. Confirm that the review remains assigned to you.

Drafts do not advance the workflow.

### 8.5 Open the Ethics Review Queue

1. Sign in with a research ethics reviewer role.
2. Open `/review/ethics`.
3. Use tabs to view action items, completed items, or all items.
4. Filter by search term, age, access type, or data type.
5. Open an assigned review.

Ethics review normally follows technical approval. The review workspace displays prior technical approval context where relevant.

### 8.6 Complete Ethics Review

1. Open an assigned ethics review.
2. Review metadata, source, access classification, and prior technical approval.
3. Download the protected ZIP package if needed.
4. Verify anonymization, consent, sensitive-data handling, source legitimacy, and access classification.
5. Complete the checklist.
6. Add comments that explain approval, revision, or rejection.
7. Submit the decision.

Table 8-2. Ethics Review Checklist

| Checklist item | Reviewer confirms |
|---|---|
| Consent and clearance | Consent and ethics clearance are documented or not applicable. |
| Anonymization | Personal or sensitive data is appropriately anonymized. |
| Sensitive-data safeguards | Sensitive-data handling and safeguards are adequate. |
| Source legitimacy | The source and proposed use are legitimate and documented. |
| Access classification | The requested access classification is appropriate. |

Ethics decisions:

- `Approve` advances the dataset to awaiting publication.
- `Request revision` returns the dataset to the contributor for ethics corrections.
- `Reject` closes the submission.

### 8.7 Protected Review Downloads

Reviewer downloads are served from protected storage and recorded in the audit trail. Reviewers should keep downloaded files within the approved review environment and should not redistribute submitted packages outside the review process.

## 9. Repository Administrator Procedures

### 9.1 Open Governance Overview

1. Sign in with the repository administrator role.
2. Open `/admin`.
3. Review metrics for unassigned technical reviews, active technical reviews, unassigned ethics reviews, active ethics reviews, awaiting publication records, aging reviews, and revision records.
4. Open attention items that need assignment, reassignment, publication, or status review.

### 9.2 Moderate Datasets

1. Open `/admin/datasets`.
2. Select a moderation stage tab:
   - technical assignment;
   - technical review;
   - ethics assignment;
   - ethics review;
   - publication;
   - revision; or
   - published.
3. Use search and filters to narrow the list.
4. Open a dataset for inspection or action.

### 9.3 Inspect a Dataset

The administrator dataset page shows metadata, contributor details, latest file information, version history, review history, technical and ethics approvals, active assignment state, publication controls, and recent audit records.

Use this page before publishing, archiving, restoring, assigning, or reassigning review work.

### 9.4 Assign a Reviewer Manually

Manual assignment is used when automatic distribution did not create an active review or when an administrator needs direct control.

1. Open the dataset in `/admin/datasets/{id}`.
2. Confirm the dataset is in the expected stage.
3. Select the review stage.
4. Choose an eligible active reviewer.
5. Submit the assignment.

The system prevents creating a duplicate active review for the same dataset.

### 9.5 Reassign an Active Review

1. Open the dataset inspection page.
2. Locate the active review.
3. Select a different eligible reviewer.
4. Enter the required reassignment reason.
5. Submit the reassignment.

The previous review is marked as reassigned and the replacement review keeps the same review round. The previous reviewer no longer has decision access.

### 9.6 Publish a Dataset

Publication is available only after both review stages approve the current version and the dataset is `Awaiting Publication`.

1. Open the dataset inspection page.
2. Confirm the technical approval.
3. Confirm the ethics approval.
4. Review and confirm the final access classification.
5. Confirm publication.
6. Submit the publish action.

Published datasets appear according to their access classification.

### 9.7 Archive or Restore a Dataset

Administrators can archive or restore eligible datasets from the dataset inspection page.

Archive removes the dataset from normal active browsing while retaining its record. Restore returns the dataset to its prior repository state where supported by workflow rules.

Use clear reasons for archive or restore actions when the interface requests them.

### 9.8 Manage Users and Roles

1. Open `/admin/users`.
2. Review user status, authentication provider, and roles.
3. Activate or deactivate accounts as needed.
4. Assign or remove roles only when the account still keeps at least one role.
5. Save changes.

Important safeguards:

- the final active administrator cannot be removed;
- an administrator cannot deactivate their own account or remove their own administrator role;
- reviewers with active assignments cannot be deactivated or lose the required reviewer role until their work is reassigned; and
- activating a reviewer can trigger automatic distribution of waiting reviews.

### 9.9 Create a Password Account

1. Open `/admin/users`.
2. Use the password-account creation form.
3. Enter the name, email, temporary password, status, and roles.
4. Submit the form.
5. Share the issued email and temporary password securely.
6. Tell the user to change the temporary password after first sign-in.

Password accounts are intended for approved collaborators who cannot use a CSPC Google account.

### 9.10 View Audit Logs

1. Open `/admin/audit-logs`.
2. Review the latest records.
3. Search by action, details, or user name when investigating a workflow event.

The audit log records important actions such as dataset upload, review assignment, review decision, publication, archive, download, denied download, user-role updates, and password-account creation.

## 10. Workflow, Data Handling, and Security Reference

### 10.1 Dataset Status Reference

Table 10-1. Dataset Status Reference

| Status | Meaning | Typical next action |
|---|---|---|
| Pending Technical Review | The submitted ZIP, metadata, and documentation are waiting for technical verification. | Technical reviewer reviews or administrator assigns reviewer if unassigned. |
| Technical Revision Requested | Technical review found issues requiring contributor correction. | Contributor revises and resubmits. |
| Pending Ethics Review | Technical review passed and ethics review is active or awaiting assignment. | Ethics reviewer reviews or administrator assigns reviewer if unassigned. |
| Ethics Revision Requested | Ethics review found issues requiring contributor correction. | Contributor revises and resubmits. |
| Awaiting Publication | Both review stages approved the current version. | Administrator confirms access classification and publishes. |
| Published | Dataset is released according to final access classification. | Users browse and download if eligible; owner may submit an update. |
| Rejected | Review ended with rejection. | Contributor reviews final comments; workflow is closed. |
| Archived | Dataset is retained but removed from active browsing or workflow surfaces. | Administrator may restore where appropriate. |

### 10.2 Access Classification Reference

Table 10-2. Access Classification Reference

| Access type | Catalog visibility | Download behavior in current release |
|---|---|---|
| Public | Visible to guests and signed-in users. | Signed-in users can download. Guests are prompted to sign in. |
| Institutional | Visible to signed-in users. | Signed-in users can download. |
| Restricted | Visible to signed-in users. | Signed-in users can download in the current release; formal request approval is not implemented. |
| Private | Hidden from general catalog browsing. | Owner and administrators retain access where permitted. |

### 10.3 File Handling Rules

- Dataset packages are uploaded as ZIP files.
- Dataset packages are stored under writable application storage, not directly in `public/`.
- Downloads are served through application routes after access checks.
- Cover images are validated as images and served through controlled routes.
- Missing protected files produce an error instead of exposing storage paths.
- Reviewer and dataset downloads are audit-recorded.

### 10.4 Account Identity Rules

Table 10-3. Account Identity Rules

| Account provider | Identity source | Editable in repository | Password in repository |
|---|---|---|---|
| Google | Verified Google profile for authorized my.cspc.edu.ph sign-in. | No. Name and email are managed by Google. | No. Recovery is handled through Google. |
| Local password | Repository administrator-issued account. | Yes, subject to validation and uniqueness rules. | Yes, password can be changed or reset. |

### 10.5 Privacy and Security Expectations

Users should:

- keep accounts private;
- avoid shared credentials;
- sign out on shared devices;
- avoid uploading unauthorized personal or confidential data;
- respect access classifications;
- keep downloaded datasets within approved academic or institutional use; and
- report missing files, suspicious content, incorrect metadata, or access mistakes to administrators.

Administrators should:

- keep `.env` values private;
- use HTTPS in production;
- protect upload storage;
- run migrations before production use;
- remove demo credentials from production;
- confirm OAuth and SMTP settings;
- back up the database and protected uploads together; and
- review audit logs when investigating workflow or access issues.

## 11. Troubleshooting and Support

### 11.1 General Support Process

When troubleshooting, collect:

- user email;
- authentication provider;
- role;
- page route;
- dataset ID if relevant;
- exact message displayed;
- browser and device;
- time of the event; and
- screenshot where appropriate.

Do not collect passwords, reset tokens, OAuth secrets, `.env` contents, or uploaded dataset files through ordinary support messages.

### 11.2 Troubleshooting Matrix

Table 11-1. Troubleshooting Matrix

| Problem | Likely cause | Resolution |
|---|---|---|
| Google sign-in could not be completed | OAuth client, redirect URI, domain authorization, or CA bundle issue. | Confirm OAuth settings, authorized redirect URI, Google test/published state, and machine-specific CA bundle. |
| User sees `Welcome back` on first Google login | Account may already exist or first-login flag was already set. | Check the `users.first_login_at` value and account creation history. |
| Password login fails | Incorrect credentials, inactive account, or account uses Google provider. | Confirm provider, account status, email spelling, and password reset path. |
| User cannot open reviewer queue | Missing reviewer role. | Administrator assigns the required technical or ethics reviewer role. |
| User cannot access admin portal | Missing repository administrator role. | Administrator verifies role assignment. |
| Dataset is locked from editing | Dataset is under active moderation. | Wait for review decision or revision request. |
| Revision action is not visible | Dataset is not in a revision status and is not a published update path. | Confirm current status in dashboard or admin inspection. |
| Download prompts for sign-in | Guest users cannot download files. | Sign in and retry. |
| Download says file is missing | File record exists but protected file is absent from storage. | Administrator checks upload storage and backup state. |
| Published dataset is not visible to guest | Access type is institutional, restricted, or private. | Sign in or confirm intended access classification. |
| Restricted dataset lacks formal request workflow | Request-approval workflow is outside current release scope. | Use administrator policy until formal restricted-access approvals are implemented. |
| Password reset email does not arrive | SMTP is not configured or delivery failed. | Administrator checks mail configuration and logs. |
| Local Google OAuth fails with cURL certificate error | Machine-specific CA bundle path is missing or invalid. | Set `google.caBundle` to the correct local certificate bundle path or leave it blank when not needed. |

### 11.3 Escalation Guidelines

Escalate to a repository administrator when the issue involves roles, account status, password-account issuance, publication, archive/restore actions, review assignment, audit inspection, missing protected files, or incorrect access classification.

Escalate to institutional IT or deployment maintainers when the issue involves server errors, HTTPS, OAuth client configuration, SMTP delivery, database connectivity, upload permissions, or missing PHP extensions.

## 12. Operational Notes

### 12.1 Production Readiness

Before production operation, confirm:

- PHP 8.2 or newer is active for CLI and web runtime;
- Composer dependencies are installed;
- database migrations are applied;
- `public/` is the web root;
- clean URL rewriting is enabled;
- `.env` contains production-safe values;
- Google OAuth is configured if Google sign-in is required;
- SMTP is configured if password reset email delivery is required;
- writable upload directories are protected and backed up;
- demo accounts are removed or changed;
- protected uploads are not served directly from `public/`; and
- a manual browser smoke test has passed for public, contributor, reviewer, and administrator workflows.

### 12.2 Machine-Specific Google CA Bundle

The `google.caBundle` setting is machine-specific. Leave it blank unless the environment raises a cURL certificate error. If a CA bundle is required on another device, set it to that device's actual certificate bundle path, such as the local XAMPP `curl-ca-bundle.crt` path.

Do not commit a local CA bundle path into shared repository history.

### 12.3 Data Backup Expectation

The database and protected upload storage must be backed up together. A database backup without uploaded files can produce dataset records whose ZIP packages cannot be downloaded. Uploaded files without the matching database can lose ownership, version, review, and access context.

### 12.4 Audit and Accountability

Audit logs provide operational accountability. They do not replace institutional records management, incident response, or formal approval records.

Administrators should review audit records when investigating:

- unexpected publication;
- denied downloads;
- missing file reports;
- role changes;
- review reassignment;
- archive/restore actions; and
- account issuance.

## 13. References

The following repository documents support this manual:

- `README.md`
- `SETUP.md`
- `docs/CONTEXT.md`
- `docs/DEPLOYMENT_READINESS.md`
- `docs/DESIGN.md`
- `SOFTWARE_DOCUMENTATION.md`
- `tests/README.md`

The following implementation areas were used to keep this manual aligned with current behavior:

- `app/Config/Routes.php`
- `app/Controllers/Auth.php`
- `app/Controllers/Account.php`
- `app/Controllers/Datasets.php`
- `app/Controllers/DatasetUpload.php`
- `app/Controllers/Dashboard.php`
- `app/Controllers/Reviews.php`
- `app/Controllers/Admin.php`
- `app/Models/DatasetModel.php`
- `app/Models/ReviewModel.php`
- `app/Models/UserModel.php`
- `app/Services/ModerationWorkflow.php`

## Appendix A. Route Reference

Table A-1. Public and Auth Routes

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
| Logout | `/logout` |
| Account settings | `/account/settings` |
| Notifications read | `/notifications/read` |
| Notifications poll | `/notifications/poll` |
| Contributor dashboard | `/dashboard` |
| Portal dashboard | `/portal/dashboard` |
| Portal dataset detail | `/portal/datasets/{id}` |
| Upload dataset | `/upload` |
| Edit owned dataset | `/datasets/{id}/edit` |
| Update owned dataset | `/datasets/{id}/update` |
| Archive owned dataset | `/datasets/{id}/archive` |

Table A-2. Reviewer Routes

| Area | Route |
|---|---|
| Technical review queue | `/review/technical` |
| Technical review workspace | `/review/technical/{reviewId}` |
| Save technical review draft | `/review/technical/{reviewId}/draft` |
| Submit technical decision | `/review/technical/{reviewId}/decision` |
| Download technical review file | `/review/technical/{reviewId}/download` |
| Ethics review queue | `/review/ethics` |
| Ethics review workspace | `/review/ethics/{reviewId}` |
| Save ethics review draft | `/review/ethics/{reviewId}/draft` |
| Submit ethics decision | `/review/ethics/{reviewId}/decision` |
| Download ethics review file | `/review/ethics/{reviewId}/download` |

Table A-3. Administrator Routes

| Area | Route |
|---|---|
| Governance overview | `/admin` |
| Dataset moderation | `/admin/datasets` |
| Admin dataset inspection | `/admin/datasets/{id}` |
| Assign reviewer | `/admin/datasets/{id}/assign` |
| Reassign reviewer | `/admin/datasets/{id}/reassign` |
| Publish dataset | `/admin/datasets/{id}/publish` |
| Archive dataset | `/admin/datasets/{id}/archive` |
| Restore dataset | `/admin/datasets/{id}/restore` |
| Users and roles | `/admin/users` |
| Create password account | `/admin/users` |
| Update user access | `/admin/users/{id}` |
| Audit logs | `/admin/audit-logs` |

## Appendix B. Local Demo Accounts

Development seeders may create local walkthrough accounts with password `change-me`.

Table B-1. Development Demo Accounts

| Role | Email |
|---|---|
| Contributor | `user@example.test` |
| Repository administrator | `admin@example.test` |
| Research ethics reviewer | `ethics@example.test` |
| Technical reviewer | `technical@example.test` |

These accounts are for local testing only. They must not be used as production credentials.

## Appendix C. Glossary and Acronyms

Table C-1. Glossary

| Term | Definition |
|---|---|
| Access classification | Publication scope: Public, Institutional, Restricted, or Private. |
| Active review | A review assignment that still accepts draft or final decisions from the assigned reviewer. |
| Archive | Action that removes a dataset from normal active browsing while retaining repository records. |
| Audit log | Administrator-visible accountability record of important system actions. |
| Contributor | User who owns and submits a dataset. |
| Dataset | Metadata record plus protected file package and version history. |
| Ethics review | Review stage for consent, anonymization, sensitive-data handling, source legitimacy, and access suitability. |
| Google account | Account authenticated through authorized my.cspc.edu.ph Google sign-in. |
| Local account | Repository-issued password account. |
| Protected package | Dataset ZIP stored outside the public web directory and served through access-controlled routes. |
| Repository administrator | User with governance control over moderation, publication, users, roles, and audit logs. |
| Technical review | Review stage for package readability, metadata completeness, documentation, declared formats, and file suitability. |
| Version | A recorded dataset revision or publication update. |

Table C-2. Acronyms

| Acronym | Meaning |
|---|---|
| ASOG TBI | ASOG Technology Business Incubator |
| CSPC | Camarines Sur Polytechnic Colleges |
| CSV | Comma-Separated Values |
| IT | Information Technology |
| OAuth | Open Authorization |
| SMTP | Simple Mail Transfer Protocol |
| URL | Uniform Resource Locator |
| ZIP | Compressed archive package |

## Appendix D. Known Release Limits

The following items are outside the current mainline milestone release or require production policy decisions:

- automated malware scanning;
- automated ZIP diagnostics;
- formal restricted-access request approval workflow;
- public self-registration;
- advanced reporting dashboards;
- generalized email notification delivery;
- browser-driven backup/restore automation;
- final institution-approved public partner/personnel wording; and
- complete production security and device-matrix certification.

These limits should be tracked in future planning before expanding the production operating scope.
