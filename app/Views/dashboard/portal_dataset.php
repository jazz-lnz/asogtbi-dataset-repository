<?= $this->extend('layouts/portal') ?>

<?= $this->section('content') ?>
<header class="portal-heading">
    <p class="eyebrow">Contributor Record</p>
    <h1><?= esc($dataset['title']) ?></h1>
    <p><?= esc($statusLabel) ?> &middot; <?= esc($accessLabel) ?> &middot; Version <?= esc($dataset['version'] ?? '1.0') ?></p>
</header>

<div class="review-layout">
    <section class="panel">
        <h2>Dataset metadata</h2>
        <dl class="detail-list">
            <div><dt>Description</dt><dd><?= esc($dataset['description']) ?></dd></div>
            <div><dt>Research title</dt><dd><?= esc($dataset['research_title'] ?: 'Not set') ?></dd></div>
            <div><dt>Project head</dt><dd><?= esc($dataset['project_head'] ?: 'Not set') ?></dd></div>
            <div><dt>Authors</dt><dd><?= esc($dataset['members'] ?: 'Not listed') ?></dd></div>
            <div><dt>Category and type</dt><dd><?= esc($dataset['category'] ?: 'Uncategorized') ?> &middot; <?= esc($dataset['data_type'] ?: 'Dataset') ?></dd></div>
            <div><dt>Formats inside ZIP</dt><dd><?= esc($dataset['content_formats'] ?: 'Not disclosed') ?></dd></div>
            <div><dt>Package</dt><dd><?= esc($dataset['file_format'] ?: 'ZIP') ?></dd></div>
            <div><dt>Source</dt><dd><?= esc($dataset['source_type'] ?: 'Not set') ?><?= ! empty($dataset['source_link']) ? ' &middot; ' . esc($dataset['source_link']) : '' ?></dd></div>
            <div><dt>Tags</dt><dd><?= esc($dataset['tags'] ?: 'No tags') ?></dd></div>
        </dl>
    </section>

    <aside class="panel">
        <h2>Portal actions</h2>
        <p class="muted">Inspection stays in the portal. Editing and public preview are website flows.</p>
        <div class="actions">
            <a class="button secondary" href="<?= site_url('portal/dashboard') ?>">Back to portal records</a>
            <a class="button portal-exit-action" href="<?= site_url('datasets/' . $dataset['id']) ?>">Return to website preview</a>
            <?php if (\App\Models\DatasetModel::isRevisionStatus($dataset['status'] ?? '') || ($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_PUBLISHED): ?>
                <a class="button portal-exit-action" href="<?= site_url('datasets/' . $dataset['id'] . '/edit') ?>"><?= ($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_PUBLISHED ? 'Return to website to update' : 'Return to website to revise' ?></a>
            <?php endif; ?>
        </div>
        <?php if ($latestFile): ?>
            <dl class="detail-list portal-detail-mini">
                <div><dt>Current file</dt><dd><?= esc($latestFile['original_name']) ?></dd></div>
                <div><dt>Size</dt><dd><?= esc(number_format((int) $latestFile['file_size'])) ?> bytes</dd></div>
            </dl>
        <?php endif; ?>
    </aside>
</div>

<section class="panel">
    <h2>Version history</h2>
    <?php if (empty($versions)): ?>
        <p class="muted">No version history recorded yet.</p>
    <?php else: ?>
        <div class="portal-stack">
            <?php foreach ($versions as $version): ?>
                <p><strong>v<?= esc($version['version']) ?></strong> &middot; <?= esc($version['change_summary'] ?: 'No summary') ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
