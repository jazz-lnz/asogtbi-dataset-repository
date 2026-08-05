<?= $this->extend('layouts/portal') ?>

<?= $this->section('content') ?>
<header class="portal-heading portal-heading--wide">
    <p class="eyebrow">Repository administrator</p>
    <h1>Governance overview</h1>
    <p>See where submissions are waiting, which assignments are aging, and what requires administrator action before opening the detailed moderation board.</p>
</header>

<section class="governance-metric-grid">
    <?php
        $metricCards = [
            ['technical_unassigned', 'Technical unassigned', 'No eligible reviewer', 'inventory_2', 'technical_assignment'],
            ['technical_active', 'Technical review', 'Active verification', 'sdk', 'technical_review'],
            ['ethics_unassigned', 'Ethics unassigned', 'No eligible reviewer', 'assignment_ind', 'ethics_assignment'],
            ['ethics_active', 'Ethics review', 'Active ethics checks', 'verified_user', 'ethics_review'],
            ['awaiting_publication', 'Ready to publish', 'Both stages approved', 'publish', 'publication'],
            ['aging', 'Aging assignments', 'Older than 3 days', 'schedule', 'technical_review'],
            ['revision', 'Revision loops', 'Contributor action', 'rate_review', 'revision'],
        ];
    ?>
    <?php foreach ($metricCards as [$key, $label, $detail, $icon, $stage]): ?>
        <a class="governance-metric-card <?= in_array($key, ['technical_unassigned', 'ethics_unassigned', 'awaiting_publication', 'aging'], true) && ($metrics[$key] ?? 0) > 0 ? 'is-attention' : '' ?>" href="<?= site_url('admin/datasets') ?>?stage=<?= esc($stage) ?>">
            <span class="material-symbols-rounded" aria-hidden="true"><?= esc($icon) ?></span>
            <div><strong><?= esc((string) ($metrics[$key] ?? 0)) ?></strong><h2><?= esc($label) ?></h2><p><?= esc($detail) ?></p></div>
        </a>
    <?php endforeach; ?>
</section>

<section class="panel governance-attention-panel">
    <div class="panel-head">
        <div><p class="tag">Requires attention</p><h2>Oldest workflow records</h2></div>
        <a class="button secondary" href="<?= site_url('admin/datasets') ?>">Open moderation board</a>
    </div>
    <?php if ($attention === []): ?>
        <div class="governance-empty-inline"><span class="material-symbols-rounded" aria-hidden="true">task_alt</span><p>No moderation records currently require administrator action.</p></div>
    <?php else: ?>
        <div class="attention-list">
            <?php foreach ($attention as $dataset): ?>
                <article>
                    <img src="<?= esc(dataset_cover_url($dataset), 'attr') ?>" alt="" loading="lazy">
                    <div><span class="status-pill status-<?= esc($dataset['status']) ?>"><?= esc(\App\Models\DatasetModel::statusLabel($dataset['status'])) ?></span><h3><?= esc($dataset['title']) ?></h3><p><?= esc($dataset['contributor_name'] ?? 'Unknown contributor') ?> · v<?= esc($dataset['version'] ?? '1.0') ?></p></div>
                    <span class="review-age-chip"><?= esc($dataset['age_label']) ?></span>
                    <a class="button secondary" href="<?= site_url('admin/datasets/' . $dataset['id']) ?>">Inspect</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
