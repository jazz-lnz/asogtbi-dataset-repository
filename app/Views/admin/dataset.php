<?= $this->extend('layouts/portal') ?>

<?= $this->section('content') ?>
<?php
    $steps = [
        'submitted' => ['Submitted', true],
        'technical' => ['Technical', $technicalApproval !== null],
        'ethics' => ['Ethics', $ethicsApproval !== null],
        'publication' => ['Publication', in_array($dataset['status'], [\App\Models\DatasetModel::STATUS_AWAITING_PUBLICATION, \App\Models\DatasetModel::STATUS_PUBLISHED], true)],
        'published' => ['Published', $dataset['status'] === \App\Models\DatasetModel::STATUS_PUBLISHED],
    ];
?>

<header class="review-workspace-header admin-inspection-header">
    <div>
        <a class="review-back-link" href="<?= site_url('admin/datasets') ?>"><span class="material-symbols-rounded" aria-hidden="true">arrow_back</span> Back to moderation</a>
        <p class="eyebrow">Administrator inspection</p>
        <h1><?= esc($dataset['title']) ?></h1>
        <div class="review-header-meta"><span class="status-pill status-<?= esc($dataset['status']) ?>"><?= esc($statusLabel) ?></span><span><?= esc($accessLabel) ?> access</span><span>v<?= esc($dataset['version'] ?? '1.0') ?></span><span><?= esc($dataset['contributor_name'] ?? 'Unknown contributor') ?></span></div>
    </div>
    <a class="button portal-exit-action" href="<?= site_url('datasets/' . $dataset['id']) ?>"><span class="material-symbols-rounded" aria-hidden="true">open_in_new</span> Website preview</a>
</header>

<section class="workflow-stepper" aria-label="Dataset moderation progress">
    <?php foreach ($steps as [$label, $complete]): ?><div class="<?= $complete ? 'is-complete' : '' ?>"><span class="material-symbols-rounded" aria-hidden="true"><?= $complete ? 'check' : 'circle' ?></span><strong><?= esc($label) ?></strong></div><?php endforeach; ?>
</section>

<nav class="inspection-anchor-nav" aria-label="Dataset inspection sections">
    <a href="#summary">Summary</a><a href="#files">Files and versions</a><a href="#timeline">Review timeline</a><a href="#publication">Publication</a><a href="#audit">Audit activity</a>
</nav>

<section class="panel inspection-section" id="summary">
    <div class="inspection-summary-grid">
        <img src="<?= esc(dataset_cover_url($dataset), 'attr') ?>" alt="Cover for <?= esc($dataset['title'], 'attr') ?>">
        <div><p class="tag">Submission summary</p><h2>Metadata and current action</h2><p><?= esc($dataset['description']) ?></p><dl class="evidence-facts"><div><dt>Research title</dt><dd><?= esc($dataset['research_title'] ?: 'Not set') ?></dd></div><div><dt>Project head</dt><dd><?= esc($dataset['project_head'] ?: 'Not set') ?></dd></div><div><dt>Authors</dt><dd><?= esc($dataset['members'] ?: 'Not listed') ?></dd></div><div><dt>Category and type</dt><dd><?= esc($dataset['category']) ?> · <?= esc($dataset['data_type']) ?></dd></div><div><dt>Formats inside ZIP</dt><dd><?= esc($dataset['content_formats'] ?: 'Not disclosed') ?></dd></div><div><dt>Package</dt><dd><?= esc($dataset['file_format'] ?: 'ZIP') ?></dd></div></dl></div>
        <aside class="current-action-card">
            <p class="tag">Required next action</p>
            <?php if ($activeReview): ?><h3>Monitor active <?= esc($activeReview['stage']) ?> review</h3><p><?= ($activeReview['assignment_method'] ?? 'manual') === \App\Models\ReviewModel::ASSIGNMENT_AUTOMATIC ? 'Automatically assigned' : 'Manually assigned' ?> to <?= esc($activeReview['reviewer_name'] ?? 'Unknown') ?> for round <?= esc((string) $activeReview['review_round']) ?>.</p>
            <?php elseif ($dataset['status'] === \App\Models\DatasetModel::STATUS_PENDING_TECHNICAL): ?><h3>Technical reviewer unavailable</h3><p>No active technical reviewer was available for automatic distribution. Assign one manually when available.</p>
            <?php elseif ($dataset['status'] === \App\Models\DatasetModel::STATUS_PENDING_ETHICS): ?><h3>Ethics reviewer unavailable</h3><p>Technical approval is retained. Assign an ethics reviewer manually when one becomes available.</p>
            <?php elseif ($dataset['status'] === \App\Models\DatasetModel::STATUS_AWAITING_PUBLICATION): ?><h3>Confirm publication</h3><p>Both review stages approved this version.</p>
            <?php else: ?><h3><?= esc($statusLabel) ?></h3><p>No stage-changing administrator action is required here.</p><?php endif; ?>
        </aside>
    </div>
</section>

<section class="panel inspection-section" id="files">
    <div class="panel-head"><div><p class="tag">Protected package</p><h2>Files and versions</h2></div></div>
    <?php if ($latestFile): ?><dl class="evidence-facts"><div><dt>Current file</dt><dd><?= esc($latestFile['original_name']) ?></dd></div><div><dt>Size</dt><dd><?= esc(number_format((int) $latestFile['file_size'])) ?> bytes</dd></div><div><dt>Type</dt><dd><?= esc($latestFile['file_type']) ?></dd></div><div><dt>Uploaded</dt><dd><?= esc($latestFile['created_at'] ?? 'Not recorded') ?></dd></div></dl><?php endif; ?>
    <div class="version-card-list"><?php foreach ($versions as $version): ?><article><strong>v<?= esc($version['version']) ?></strong><p><?= esc($version['change_summary'] ?: 'No summary') ?></p><small><?= esc($version['created_at'] ?? '') ?></small></article><?php endforeach; ?></div>
</section>

<section class="panel inspection-section" id="timeline">
    <div class="panel-head"><div><p class="tag">Immutable history</p><h2>Review timeline</h2></div></div>
    <?php if ($reviews === []): ?><p class="muted">No review assignments have been created.</p><?php else: ?><div class="review-history-timeline"><?php foreach ($reviews as $item): ?><article><span class="timeline-dot"></span><div><strong><?= esc(ucfirst($item['stage'])) ?> round <?= esc((string) $item['review_round']) ?> · <?= esc(str_replace('_', ' ', $item['status'])) ?></strong><p><?= esc($item['reviewer_name'] ?? 'Unknown') ?> · <?= ($item['assignment_method'] ?? 'manual') === \App\Models\ReviewModel::ASSIGNMENT_AUTOMATIC ? 'automatic distribution' : 'assigned by ' . esc($item['assigner_name'] ?? 'Unknown') ?></p><?php if (! empty($item['reassignment_reason'])): ?><blockquote>Reassignment: <?= esc($item['reassignment_reason']) ?></blockquote><?php elseif (! empty($item['comments'])): ?><blockquote><?= esc($item['comments']) ?></blockquote><?php endif; ?></div></article><?php endforeach; ?></div><?php endif; ?>
</section>

<section class="panel inspection-section" id="publication">
    <div class="panel-head"><div><p class="tag">Final governance gate</p><h2>Publication</h2></div></div>
    <div class="approval-summary-grid">
        <article class="<?= $technicalApproval ? 'is-approved' : '' ?>"><span class="material-symbols-rounded" aria-hidden="true"><?= $technicalApproval ? 'verified' : 'pending' ?></span><div><strong>Technical approval</strong><p><?= $technicalApproval ? esc($technicalApproval['reviewer_name'] ?? 'Approved') . ' · round ' . esc((string) $technicalApproval['review_round']) : 'Not yet approved' ?></p></div></article>
        <article class="<?= $ethicsApproval ? 'is-approved' : '' ?>"><span class="material-symbols-rounded" aria-hidden="true"><?= $ethicsApproval ? 'verified_user' : 'pending' ?></span><div><strong>Ethics approval</strong><p><?= $ethicsApproval ? esc($ethicsApproval['reviewer_name'] ?? 'Approved') . ' · round ' . esc((string) $ethicsApproval['review_round']) : 'Not yet approved' ?></p></div></article>
    </div>
    <?php if ($dataset['status'] === \App\Models\DatasetModel::STATUS_AWAITING_PUBLICATION): ?>
        <form class="publication-gate-form" method="post" action="<?= site_url('admin/datasets/' . $dataset['id'] . '/publish') ?>" onsubmit="return confirm('Publish the current dataset version using the selected access classification?')">
            <?= csrf_field() ?>
            <label>Final access classification<select name="access_type" required><?php foreach ($accessOptions as $value => $label): ?><option value="<?= esc($value) ?>" <?= $dataset['access_type'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?></select></label>
            <p class="evidence-notice"><span class="material-symbols-rounded" aria-hidden="true">visibility</span> Publication makes the current version available according to this access classification.</p>
            <label class="publication-confirm"><input type="checkbox" name="publication_confirmed" value="1" required><span>I verified the technical approval, ethics approval, current version, and final access classification.</span></label>
            <button class="button" type="submit">Publish current version</button>
        </form>
    <?php else: ?><p class="muted">Publication controls unlock only when both review stages approve the current submission.</p><?php endif; ?>
</section>

<section class="panel inspection-section" id="audit">
    <div class="panel-head"><div><p class="tag">Accountability</p><h2>Recent audit activity</h2></div></div>
    <?php if ($auditLogs === []): ?><p class="muted">No dataset-level audit records are available.</p><?php else: ?><div class="audit-activity-list"><?php foreach ($auditLogs as $log): ?><article><strong><?= esc(str_replace('_', ' ', $log['action'])) ?></strong><p><?= esc($log['details'] ?: 'No details') ?></p><small><?= esc($log['user_name'] ?? 'System') ?> · <?= esc($log['created_at']) ?></small></article><?php endforeach; ?></div><?php endif; ?>
</section>
<?= $this->endSection() ?>
