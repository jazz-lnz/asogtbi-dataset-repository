<?= $this->extend('layouts/portal') ?>

<?= $this->section('content') ?>
<header class="portal-heading">
    <p class="eyebrow">Contributor Records</p>
    <h1>My datasets inside the portal</h1>
    <p>This view mirrors your contributor work without leaving the review and administration workspace.</p>
</header>

<section class="portal-stat-grid">
    <article class="panel stat-card">
        <p class="tag">Published</p>
        <h2 class="stat-value"><?= esc((string) (($statusCounts['published'] ?? 0))) ?></h2>
        <p class="muted">Visible records that passed publication.</p>
    </article>
    <article class="panel stat-card">
        <p class="tag">Queued</p>
        <h2 class="stat-value"><?= esc((string) (($statusCounts['pending'] ?? 0))) ?></h2>
        <p class="muted">Records in technical verification, ethics review, or publication approval.</p>
    </article>
    <article class="panel stat-card">
        <p class="tag">Needs Revision</p>
        <h2 class="stat-value"><?= esc((string) (($statusCounts['needsRevision'] ?? 0))) ?></h2>
        <p class="muted">Records awaiting contributor changes.</p>
    </article>
</section>

<section class="panel portal-section">
    <div class="panel-head">
        <div>
            <p class="tag">Submissions</p>
            <h2>Your dataset records</h2>
        </div>
        <a class="button portal-exit-action" href="<?= site_url('upload') ?>">Return to website to upload</a>
    </div>

    <?php if (empty($myDatasets)): ?>
        <p class="muted">No submissions yet. Use the website upload flow when you are ready to add a dataset.</p>
    <?php else: ?>
        <div class="portal-stack">
            <?php foreach ($myDatasets as $dataset): ?>
                <article class="review-queue-row portal-record-row">
                    <div>
                        <span class="status-pill status-<?= esc($dataset['status'] ?? 'unknown') ?>">
                            <?= esc(($statusLabels[$dataset['status'] ?? ''] ?? 'Unknown')) ?>
                        </span>
                        <h2><?= esc($dataset['title']) ?></h2>
                        <p class="muted"><?= esc($dataset['category'] ?: 'Uncategorized') ?> &middot; Version <?= esc($dataset['version'] ?? '1.0') ?></p>
                    </div>
                    <div class="portal-row-actions">
                        <a class="button secondary" href="<?= site_url('portal/datasets/' . $dataset['id']) ?>">View in portal</a>
                        <?php if (\App\Models\DatasetModel::isRevisionStatus($dataset['status'] ?? '') || ($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_PUBLISHED): ?>
                            <a class="button portal-exit-action" href="<?= site_url('datasets/' . $dataset['id'] . '/edit') ?>"><?= ($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_PUBLISHED ? 'Return to website to update' : 'Return to website to revise' ?></a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if (isset($pager) && $pager->getPageCount('dashboard') > 1): ?>
            <nav class="portal-pagination" aria-label="Contributor record pagination"><?= $pager->links('dashboard', 'dashboard_full') ?></nav>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
