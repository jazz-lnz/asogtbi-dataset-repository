<?= $this->extend('layouts/portal') ?>

<?= $this->section('content') ?>
<?php
    $stageLabels = [
        'technical_assignment' => 'Technical unassigned',
        'technical_review' => 'Technical review',
        'ethics_assignment' => 'Ethics unassigned',
        'ethics_review' => 'Ethics review',
        'publication' => 'Ready to publish',
        'revision' => 'Revision and rejected',
        'published' => 'Published and archived',
    ];
?>

<header class="portal-heading portal-heading--wide">
    <p class="eyebrow">Administrator queue</p>
    <h1>Dataset moderation</h1>
    <p>Move through one workflow stage at a time. Assignment, reassignment, publication, and lifecycle controls appear only when they are valid for the selected record.</p>
</header>

<nav class="portal-tabs portal-tabs--scroll" aria-label="Moderation stages">
    <?php foreach ($stageLabels as $value => $label): ?>
        <a class="<?= ($selectedStage ?? '') === $value ? 'is-active' : '' ?>" href="<?= site_url('admin/datasets') ?>?stage=<?= esc($value) ?>">
            <?= esc($label) ?><span><?= esc((string) ($stageCounts[$value] ?? 0)) ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<section class="panel review-queue-tools">
    <form class="admin-moderation-filters" method="get" action="<?= site_url('admin/datasets') ?>">
        <input type="hidden" name="stage" value="<?= esc($selectedStage) ?>">
        <label class="review-filter-search"><span class="sr-only">Search moderation records</span><span class="material-symbols-rounded" aria-hidden="true">search</span><input name="q" value="<?= esc($search ?? '') ?>" placeholder="Search title, contributor, category, or tags"></label>
        <label><span>Reviewer</span><select name="reviewer_id"><option value="">All reviewers</option><?php foreach ($allReviewers as $reviewer): ?><option value="<?= esc((string) $reviewer['id']) ?>" <?= (int) ($selectedReviewerId ?? 0) === (int) $reviewer['id'] ? 'selected' : '' ?>><?= esc($reviewer['name']) ?></option><?php endforeach; ?></select></label>
        <label><span>Age</span><select name="age"><option value="">Any age</option><?php foreach (['3' => '3+ days', '7' => '7+ days', '14' => '14+ days'] as $value => $label): ?><option value="<?= esc($value) ?>" <?= ($selectedAge ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?></select></label>
        <label><span>Access</span><select name="access"><option value="">All access</option><?php foreach ($accessOptions as $value => $label): ?><option value="<?= esc($value) ?>" <?= ($selectedAccess ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?></select></label>
        <label><span>Data type</span><select name="data_type"><option value="">All types</option><?php foreach (['Text', 'Image', 'Audio', 'Video', 'Tabular'] as $value): ?><option value="<?= esc($value) ?>" <?= ($selectedDataType ?? '') === $value ? 'selected' : '' ?>><?= esc($value) ?></option><?php endforeach; ?></select></label>
        <div class="review-filter-actions"><button class="button" type="submit">Apply</button><a class="button secondary" href="<?= site_url('admin/datasets') ?>?stage=<?= esc($selectedStage) ?>">Reset</a></div>
    </form>
</section>

<?php if ($datasets === []): ?>
    <article class="panel governance-empty-state"><span class="material-symbols-rounded" aria-hidden="true">playlist_add_check</span><h2>No records in <?= esc(strtolower($stageLabels[$selectedStage])) ?></h2><p>This stage is clear or the active filters exclude its records.</p></article>
<?php else: ?>
    <div class="moderation-board-list">
        <?php foreach ($datasets as $dataset): ?>
            <?php
                $isTechnical = ($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_PENDING_TECHNICAL;
                $isEthics = ($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_PENDING_ETHICS;
                $assignmentStage = $isTechnical ? 'technical' : 'ethics';
                $reviewerOptions = $isTechnical ? $technicalReviewers : $ethicsReviewers;
                $activeReviewId = (int) ($dataset['active_review_id'] ?? 0);
                $latestReview = is_array($dataset['latest_review'] ?? null) ? $dataset['latest_review'] : null;
            ?>
            <article class="panel moderation-board-card">
                <img class="moderation-cover" src="<?= esc(dataset_cover_url($dataset), 'attr') ?>" alt="" loading="lazy">
                <div class="moderation-main">
                    <div class="moderation-status-line"><span class="status-pill status-<?= esc($dataset['status']) ?>"><?= esc($statusLabels[$dataset['status']] ?? $dataset['status']) ?></span><span class="review-age-chip"><?= esc($dataset['stage_age']) ?> in stage</span></div>
                    <h2><?= esc($dataset['title']) ?></h2>
                    <p><?= esc($dataset['description'] ?: 'No description provided.') ?></p>
                    <div class="moderation-meta">
                        <span><strong>Contributor</strong><?= esc($dataset['contributor_name'] ?? 'Unknown') ?></span>
                        <span><strong>Version</strong>v<?= esc($dataset['version'] ?? '1.0') ?></span>
                        <span><strong>Access</strong><?= esc(\App\Models\DatasetModel::accessLabel($dataset['access_type'])) ?></span>
                        <span><strong>Reviewer</strong><?= esc($dataset['active_reviewer_name'] ?? 'Unassigned') ?></span>
                    </div>
                    <?php if ($latestReview && ! empty($latestReview['comments'])): ?><p class="moderation-latest-note"><strong>Latest finding:</strong> <?= esc($latestReview['comments']) ?></p><?php endif; ?>
                </div>
                <div class="moderation-actions">
                    <a class="button secondary" href="<?= site_url('admin/datasets/' . $dataset['id']) ?>">Inspect record</a>
                    <?php if (($isTechnical || $isEthics) && $activeReviewId === 0): ?>
                        <details class="governance-action-menu">
                            <summary class="button">Assign manually</summary>
                            <form method="post" action="<?= site_url('admin/datasets/' . $dataset['id'] . '/assign') ?>">
                                <?= csrf_field() ?><input type="hidden" name="stage" value="<?= esc($assignmentStage) ?>">
                                <h3>Assign <?= esc($assignmentStage) ?> review</h3>
                                <p>Automatic distribution could not complete. Select an active reviewer as a fallback.</p>
                                <label>Reviewer<select name="reviewer_id" required><option value="">Select reviewer</option><?php foreach ($reviewerOptions as $reviewer): ?><option value="<?= esc((string) $reviewer['id']) ?>"><?= esc($reviewer['name']) ?> · <?= esc((string) $reviewer['active_count']) ?> active · <?= esc($reviewer['oldest_assignment']) ?></option><?php endforeach; ?></select></label>
                                <button class="button" type="submit">Confirm assignment</button>
                            </form>
                        </details>
                    <?php elseif (($isTechnical || $isEthics) && $activeReviewId > 0): ?>
                        <details class="governance-action-menu">
                            <summary class="button warning">Reassign review</summary>
                            <form method="post" action="<?= site_url('admin/datasets/' . $dataset['id'] . '/reassign') ?>">
                                <?= csrf_field() ?><input type="hidden" name="stage" value="<?= esc($assignmentStage) ?>">
                                <h3>Reassign <?= esc($assignmentStage) ?> review</h3>
                                <p><?= esc($dataset['active_reviewer_name']) ?> immediately loses decision access after confirmation.</p>
                                <label>New reviewer<select name="reviewer_id" required><option value="">Select a different reviewer</option><?php foreach ($reviewerOptions as $reviewer): ?><?php if ((int) $reviewer['id'] !== (int) $dataset['active_reviewer_id']): ?><option value="<?= esc((string) $reviewer['id']) ?>"><?= esc($reviewer['name']) ?> · <?= esc((string) $reviewer['active_count']) ?> active</option><?php endif; ?><?php endforeach; ?></select></label>
                                <label>Reason<textarea name="reason" rows="3" required placeholder="Why is this reassignment necessary?"><?= old('reason') ?></textarea></label>
                                <button class="button warning" type="submit">Confirm reassignment</button>
                            </form>
                        </details>
                    <?php elseif (($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_AWAITING_PUBLICATION): ?>
                        <details class="governance-action-menu">
                            <summary class="button">Open publication gate</summary>
                            <form method="post" action="<?= site_url('admin/datasets/' . $dataset['id'] . '/publish') ?>">
                                <?= csrf_field() ?>
                                <h3>Publish current version</h3>
                                <p>Both review stages are approved. Confirm the final access classification before making this version available.</p>
                                <label>Final access<select name="access_type" required><?php foreach ($accessOptions as $value => $label): ?><option value="<?= esc($value) ?>" <?= $dataset['access_type'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?></select></label>
                                <label class="publication-confirm"><input type="checkbox" name="publication_confirmed" value="1" required><span>I checked both approvals and the selected access classification.</span></label>
                                <button class="button" type="submit">Publish dataset</button>
                            </form>
                        </details>
                    <?php endif; ?>
                    <details class="governance-more-actions">
                        <summary>More actions</summary>
                        <?php if (($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_ARCHIVED): ?>
                            <form method="post" action="<?= site_url('admin/datasets/' . $dataset['id'] . '/restore') ?>"><?= csrf_field() ?><button class="text-button" type="submit">Restore dataset</button></form>
                        <?php else: ?>
                            <form class="archive-reason-form" method="post" action="<?= site_url('admin/datasets/' . $dataset['id'] . '/archive') ?>" onsubmit="return confirm('Archive this dataset and remove it from its current workflow surface?')"><?= csrf_field() ?><label>Archive reason<textarea name="reason" rows="2" required placeholder="State why this record is being archived"></textarea></label><button class="text-button text-button--danger" type="submit">Archive dataset</button></form>
                        <?php endif; ?>
                    </details>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (isset($pager) && $pager->getPageCount() > 1): ?><nav class="portal-pagination" aria-label="Moderation pagination"><?= $pager->links() ?></nav><?php endif; ?>
<?= $this->endSection() ?>
