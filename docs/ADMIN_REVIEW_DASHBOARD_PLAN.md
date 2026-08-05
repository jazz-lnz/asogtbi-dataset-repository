# Admin and Review Dashboard Improvement Plan

## Implementation Status

Implemented on `rapid-mvp` on 2026-07-16:

- Reviewer queue metrics, action/completed/all tabs, search, filtering, sorting, aging, package context, checklist progress, and pagination.
- Focused technical and ethics workspaces with evidence sections, structured three-state checklist findings, draft saving, completion progress, confirmation dialog, and immutable decision display.
- Administrator operational metrics, attention queue, stage board, filtering, pagination, reviewer workload, explicit reassignment reasons, publication confirmation, workflow stepper, version history, review timeline, and audit activity.
- Transactional draft, reassignment, decision, publication, notification, and audit behavior.

Still deferred by release scope: automated ZIP diagnostics, malware scanning, email delivery, backup/restore automation, advanced reports, and restricted-access request approval.

## Purpose

Improve the repository governance portal so technical reviewers, ethics reviewers, and repository administrators can understand their current workload, complete the correct action with confidence, and move a dataset through the technical-first moderation workflow without hunting through dense forms.

This plan keeps the existing workflow rules:

1. A contributor submits or revises a dataset.
2. An administrator assigns a technical reviewer.
3. Technical approval moves the dataset to the ethics queue.
4. An administrator assigns an ethics reviewer.
5. Ethics approval moves the dataset to awaiting publication.
6. An administrator confirms access classification and publishes.

Reviewers remain limited to assigned submissions. Decisions remain transactional, approval still requires every mandatory checklist item, and reassignment or resubmission creates history rather than overwriting it.

## Current Usability Findings

### Reviewer queues

- Assigned and completed reviews appear in one undifferentiated stack.
- Rows do not show assignment age, dataset version, requested access, or the next required action.
- There is no search, stage-specific filtering, sorting, workload summary, or empty-state guidance.
- Completed records compete visually with actionable assignments.

### Review workspace

- Submission evidence, file access, version history, checklist, decision, and complete history are compressed into two dense panels.
- The reviewer cannot see checklist completion progress at a glance.
- Checklist items are binary checkboxes with no item-level finding or correction note.
- The decision selector is visually equal to ordinary metadata controls despite being the irreversible action.
- Errors return as general flash messages instead of identifying the incomplete checklist or missing comments.
- There is no draft save, decision summary, or final confirmation step.

### Administrator moderation

- Every dataset and every possible stage control appears in one long list.
- Assignment dropdowns do not show reviewer workload or current assignment.
- Stage, assignment, aging, previous decision, and required next action are not summarized consistently.
- Review history is repeated inline, making large repositories difficult to scan.
- Reassignment is technically possible through the assignment service but is not presented as an explicit, accountable flow.
- Publication is a compact inline form without a final review gate or decision summary.

## Experience Principles

- Show the next action first and secondary history on demand.
- Separate queue triage from detailed review work.
- Use one dominant action per screen.
- Reveal controls progressively based on workflow state and role.
- Keep evidence visible while the reviewer completes the checklist.
- Preserve reviewer context when navigating files, versions, and history.
- Explain why an action is unavailable instead of merely disabling it.
- Require confirmation for decisions, reassignment, publication, archive, and restore.
- Use server-side validation as the authority; client-side behavior only improves guidance.

## Information Architecture

### Portal navigation

- `Overview`: role-aware workload and stage metrics.
- `Technical reviews`: technical queue and completed technical records.
- `Ethics reviews`: ethics queue and completed ethics records.
- `Dataset moderation`: administrator stage board.
- `Users and roles`: existing administrator access directory.
- `Audit logs`: existing searchable audit trail.
- `New activities`: existing notification drawer with links into the relevant queue or record.

Only links allowed by the account's roles render. Multi-role accounts may switch between review queues without leaving the portal shell.

## Technical Reviewer Flow

### 1. Technical queue

The queue opens on `Needs action` and includes:

- Summary cards for assigned, aging, completed this week, and returned for revision.
- Search by dataset title, contributor, category, file name, or version.
- Filters for assignment status, age, requested access, and data type; package format is always ZIP.
- Sort by oldest assignment, newest assignment, contributor, or dataset title.
- Queue rows with cover thumbnail, dataset title, contributor, version, ZIP name and size, assignment age, access request, and checklist progress.
- Primary action `Continue review`; completed rows use `View decision`.

Recommended tabs:

- `Needs action`
- `Completed`
- `All records`

### 2. Technical review workspace

Use a focused workspace with:

- Sticky header containing dataset title, version, assignment age, current stage, contributor, and `Back to queue`.
- Left evidence column for package details, metadata summary, declared formats, source, documentation, version changes, and previous technical findings.
- Protected file card with file name, size, uploaded date, `Download ZIP`, and a clear manual-inspection notice.
- Right sticky review panel containing progress, checklist, findings, decision, and save state.

Technical checklist controls:

- Each required item uses `Confirmed`, `Issue found`, and `Not reviewed`.
- `Issue found` reveals a concise item-level finding field.
- Approval is enabled only when every item is `Confirmed`.
- Revision and rejection require an overall contributor-facing comment.
- The interface displays `3 of 5 checks completed` and lists blockers beside the decision control.

Technical decision actions:

- `Save draft` keeps the review assigned.
- `Request revision` is the emphasized warning action when issues exist.
- `Reject submission` is visually separated and requires confirmation.
- `Approve technical review` becomes available only after every check passes.
- A confirmation dialog summarizes checklist result, comments, dataset version, and resulting next state.

### 3. Technical completion

After submission:

- Return to the technical queue.
- Show a success message naming the dataset and resulting state.
- Move the record to `Completed`.
- Notify the contributor for revision or rejection.
- Notify administrators when the dataset is ready for ethics assignment.

## Ethics Reviewer Flow

### 1. Ethics queue

Use the same queue framework for consistency, but surface ethics-specific facts:

- Technical approval indicator and technical decision date.
- Requested access classification.
- Anonymization confirmation.
- Source type and source legitimacy.
- Sensitive-data warning derived from metadata, where available.
- Assignment age and review round.

The queue must never show datasets that have not passed technical review.

### 2. Ethics review workspace

The evidence column prioritizes:

- Research title, project head, authors, contributor, source, and intended use.
- Requested access classification and anonymization declaration.
- Technical approval summary with previous technical findings.
- Metadata and protected file access as secondary evidence.
- Prior ethics decisions and contributor revision summaries.

Ethics checklist controls:

- Consent or clearance evidence.
- Anonymization.
- Sensitive-data handling.
- Source legitimacy and intended use.
- Access classification.

Use the same three-state checklist and item-level findings as technical review. Approval requires every required item to be confirmed. Revision and rejection require contributor-facing comments.

### 3. Ethics completion

- Approval moves the dataset to `Awaiting publication`.
- Revision returns the contributor to the ethics revision loop without repeating technical approval.
- Rejection closes the submission.
- Administrators receive a notification when final publication action becomes available.

## Administrator Flow

### 1. Governance overview

Replace the flat status-card grid with operational metrics:

- Unassigned technical reviews.
- Active technical reviews.
- Unassigned ethics reviews.
- Active ethics reviews.
- Awaiting publication.
- Aging assignments.
- Revision requests.
- Rejected and archived totals as secondary metrics.

Add a `Requires attention` panel listing the oldest unassigned, aging, or publication-ready records.

### 2. Dataset moderation stage board

Use stage tabs:

- `Technical assignment`
- `Technical review`
- `Ethics assignment`
- `Ethics review`
- `Ready to publish`
- `Revision and rejected`
- `Published and archived`

Each row shows only controls relevant to its current stage. Shared row content includes cover, title, contributor, version, stage age, current reviewer, requested access, and latest decision summary.

Filters:

- Search.
- Stage.
- Assignment state.
- Reviewer.
- Age.
- Access classification.
- Data type.

### 3. Assignment and reassignment

Open assignment in a side panel or modal instead of an inline row form.

Reviewer options show:

- Name and email.
- Required role.
- Active assignment count.
- Oldest active assignment.
- Whether the reviewer is already assigned to this dataset round.

Assignment confirmation states the dataset, stage, version, and selected reviewer.

Reassignment is a distinct action:

- Display the current reviewer and assignment age.
- Require a reassignment reason.
- Warn that the previous reviewer immediately loses decision access.
- Preserve the existing review as `reassigned`.
- Notify both the previous and new reviewer.
- Record the reason in review history and audit logs.

### 4. Administrator dataset inspection

Organize the record into tabs or anchored sections:

- `Summary`
- `Files and versions`
- `Review timeline`
- `Publication`
- `Audit activity`

The summary includes a horizontal workflow stepper:

`Submitted -> Technical -> Ethics -> Publication -> Published`

Each completed step shows reviewer, decision, round, and date. The active step shows its required next action.

### 5. Final publication gate

The publication panel appears only for `awaiting_publication` datasets and includes:

- Technical approval summary.
- Ethics approval summary.
- Current version and file.
- Requested access type.
- Final access-type dropdown with plain-language descriptions.
- Public visibility impact.
- Confirmation checkbox stating that both review stages and access classification were checked.
- Primary `Publish dataset` action.

The confirmation dialog states that publication makes the current version available according to the selected access type.

### 6. Lifecycle controls

Archive and restore belong in a secondary `More actions` menu, not beside the primary workflow action.

- Archive requires a reason and confirmation.
- Restore shows the status that will be restored.
- Destructive or exceptional controls never share the same visual weight as assignment or publication.

## Shared Control Strategy

### Buttons

- One primary action per view.
- Secondary buttons for navigation, download, and draft saving.
- Warning buttons for revision requests.
- Danger buttons for rejection and archive.
- Text buttons only for low-risk disclosure actions.
- Disabled actions include visible explanatory copy.

### Dropdowns

- Use dropdowns for filtering, reviewer selection, access classification, and sorting.
- Do not use a dropdown as the only presentation of a high-risk final decision.
- Reviewer and access options include supporting descriptions.

### Checklists

- Use large clickable checklist rows, not small isolated checkboxes.
- Display completion count and unresolved items.
- Reveal item-level notes only when an issue is found.
- Restore draft answers after validation failures.
- Render completed checklist responses read-only in review history.

### Dialogs and side panels

- Assignment and reassignment use a side panel to preserve queue context.
- Decision, publication, archive, and restore use confirmation dialogs.
- Focus is trapped inside open dialogs, Escape closes safe dialogs, and focus returns to the trigger.

### Feedback

- Inline errors appear beside the affected checklist, comment, reviewer, or access control.
- Success feedback names the dataset and new workflow state.
- Live notifications link directly to the relevant record.
- Prevent duplicate submissions by disabling the action after the first valid submit.

## Backend and Data Changes

### Controller/query changes

- Add queue query services or repository methods instead of loading all records and filtering in views.
- Return counts, active assignment age, current reviewer, latest decision, and latest version in queue queries.
- Paginate administrator and reviewer queues.
- Preserve query parameters across pagination.
- Add dedicated draft, assignment, reassignment, and publication endpoints.

Recommended routes:

- `GET /review/{stage}?tab=&q=&age=&sort=`
- `POST /review/{stage}/{review}/draft`
- `POST /review/{stage}/{review}/decision`
- `POST /admin/datasets/{dataset}/assign`
- `POST /admin/datasets/{dataset}/reassign`
- `POST /admin/datasets/{dataset}/publish`

### Review draft format

Continue using the existing `reviews.checklist` JSON column, but store structured responses:

```json
{
  "archive_readable": {
    "result": "confirmed",
    "note": ""
  },
  "metadata_complete": {
    "result": "issue",
    "note": "Missing data dictionary."
  }
}
```

Draft saves update `checklist`, `comments`, and `updated_at` while retaining `status = assigned`. Final decisions remain immutable after submission.

### Recommended migration additions

- `reviews.draft_saved_at` nullable datetime.
- `reviews.reassignment_reason` nullable text.
- Optional `reviews.last_opened_at` for operational visibility, only if the team agrees this activity tracking is useful.

No migration is required for queue aging because `assigned_at` already exists.

### Workflow service changes

- Add `saveDraft()`.
- Extend `assign()` or add `reassign()` with a required reason and previous-reviewer notification.
- Accept structured checklist responses while preserving all-required approval validation.
- Notify administrators when technical approval creates an ethics assignment need and when ethics approval creates a publication need.
- Keep every mutation transactional and audit logged.

## Accessibility and Responsive Behavior

- Queue rows collapse into cards without hiding stage, age, reviewer, or primary action.
- Review evidence stacks above the decision panel on narrow screens.
- Sticky panels become normal document flow on mobile.
- All controls have explicit labels and helper text.
- Checklist state is conveyed by text and icon, not color alone.
- Keyboard users can complete checklists and confirmation dialogs.
- Focus moves to the first invalid control after failed submission.
- Status and live feedback use appropriate `aria-live` regions.

## Implementation Milestones

### Milestone 1: Queue foundation

- Add paginated queue queries, metrics, tabs, filters, sorting, and aging labels.
- Rebuild technical and ethics queue views using a shared component structure.
- Add empty and no-result states.

Acceptance:

- Reviewers can find an assigned dataset without scanning completed records.
- Unassigned or reassigned reviewers cannot see actionable review controls.
- Filters and pagination preserve role and stage boundaries.

### Milestone 2: Review workspace

- Rebuild the review detail page with sticky context, evidence sections, checklist progress, item findings, and decision cards.
- Add draft saving and restore validation input.
- Add decision confirmation and duplicate-submit protection.

Acceptance:

- Approval cannot be submitted with incomplete or issue-marked checks.
- Revision and rejection cannot be submitted without comments.
- Draft saving does not change dataset state.
- Completed reviews render an immutable decision summary.

### Milestone 3: Administrator stage board

- Add operational metrics, stage tabs, filters, queue pagination, assignment panel, workload indicators, and explicit reassignment.
- Update notifications and audit details for reassignment.

Acceptance:

- Administrators can identify every unassigned and aging record.
- Reassignment removes the previous reviewer's decision access immediately.
- The new reviewer receives a direct notification link.

### Milestone 4: Publication and inspection

- Add workflow stepper, structured review timeline, file/version sections, and final publication gate.
- Move archive/restore into confirmed secondary actions.

Acceptance:

- Publication remains impossible before both stages approve.
- The administrator sees the exact version and access type being published.
- Review rounds and prior assignments remain visible.

### Milestone 5: Verification and polish

- Add feature tests for queue visibility, filters, drafts, reassignment, decision validation, publication confirmation, and inactive accounts.
- Run role-by-role desktop, tablet, mobile, and keyboard walkthroughs.
- Update `README.md`, `SETUP.md`, `docs/CONTEXT.md`, and `docs/DESIGN.md` after implementation.

## Test Matrix

- Anonymous users cannot access portal routes.
- Contributors cannot access reviewer or administrator routes.
- Technical reviewers see only active or historical technical assignments belonging to them.
- Ethics reviewers see only ethics assignments that reached the ethics stage.
- Multi-role administrators can enter a review only when assigned and holding the matching reviewer role.
- Reassigned reviewers can view historical decisions only when policy permits, but cannot submit.
- Draft saves retain checklist answers without changing dataset status.
- Approval requires all mandatory items confirmed.
- Revision and rejection require comments.
- Direct POST attempts cannot skip technical, ethics, or publication stages.
- Pagination and filtering never expose unauthorized records.
- Inactive accounts lose portal access.
- Notifications and audit logs are created for assignment, reassignment, decisions, and publication.

## Recommended Delivery Order

Implement the reviewer queue and workspace before redesigning the administrator stage board. The administrator experience depends on the same queue summaries, checklist response structure, assignment state, and review timeline. Building those shared contracts first reduces duplicate UI and query work.
