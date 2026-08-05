<?= $this->extend('layouts/portal') ?>

<?= $this->section('content') ?>
<?php
    $stageLabel = $stage === 'technical' ? 'Technical verification' : 'Research ethics review';
    $queryBase = array_filter([
        'q' => $search ?? '',
        'age' => $selectedAge ?? '',
        'access' => $selectedAccess ?? '',
        'data_type' => $selectedDataType ?? '',
        'sort' => $selectedSort ?? '',
    ], static fn ($value): bool => $value !== '');
?>

<header class="portal-heading portal-heading--wide">
    <p class="eyebrow"><?= esc($stageLabel) ?></p>
    <h1>Your review queue</h1>
    <p>Prioritize active assignments, inspect the protected submission, save findings as you work, and submit a decision only when the evidence is complete.</p>
</header>

<section class="review-metric-grid" aria-label="Review workload summary">
    <article class="review-metric-card">
        <span class="material-symbols-rounded" aria-hidden="true">assignment</span>
        <div><strong><?= esc((string) ($metrics['assigned'] ?? 0)) ?></strong><small>Needs action</small></div>
    </article>
    <article class="review-metric-card <?= ($metrics['aging'] ?? 0) > 0 ? 'is-attention' : '' ?>">
        <span class="material-symbols-rounded" aria-hidden="true">schedule</span>
        <div><strong><?= esc((string) ($metrics['aging'] ?? 0)) ?></strong><small>Older than 3 days</small></div>
    </article>
    <article class="review-metric-card">
        <span class="material-symbols-rounded" aria-hidden="true">task_alt</span>
        <div><strong><?= esc((string) ($metrics['completed_week'] ?? 0)) ?></strong><small>Completed this week</small></div>
    </article>
    <article class="review-metric-card">
        <span class="material-symbols-rounded" aria-hidden="true">rate_review</span>
        <div><strong><?= esc((string) ($metrics['revisions'] ?? 0)) ?></strong><small>Revision decisions</small></div>
    </article>
</section>

<nav class="portal-tabs" aria-label="Review queue sections">
    <?php foreach (['action' => 'Needs action', 'completed' => 'Completed', 'all' => 'All records'] as $value => $label): ?>
        <?php $params = array_merge($queryBase, ['tab' => $value]); ?>
        <a class="<?= ($tab ?? 'action') === $value ? 'is-active' : '' ?>" href="<?= site_url('review/' . $stage) . '?' . http_build_query($params) ?>"><?= esc($label) ?></a>
    <?php endforeach; ?>
</nav>

<section class="panel review-queue-tools">
    <form class="review-filter-form" method="get" action="<?= site_url('review/' . $stage) ?>">
        <input type="hidden" name="tab" value="<?= esc($tab ?? 'action') ?>">
        <label class="review-filter-search">
            <span class="sr-only">Search assigned reviews</span>
            <span class="material-symbols-rounded" aria-hidden="true">search</span>
            <input name="q" value="<?= esc($search ?? '') ?>" placeholder="Search title, contributor, category, or format">
        </label>
        <label>
            <span>Assignment age</span>
            <select name="age">
                <option value="">Any age</option>
                <option value="3" <?= ($selectedAge ?? '') === '3' ? 'selected' : '' ?>>3+ days</option>
                <option value="7" <?= ($selectedAge ?? '') === '7' ? 'selected' : '' ?>>7+ days</option>
                <option value="14" <?= ($selectedAge ?? '') === '14' ? 'selected' : '' ?>>14+ days</option>
            </select>
        </label>
        <label>
            <span>Access request</span>
            <select name="access">
                <option value="">All access types</option>
                <?php foreach (($accessOptions ?? []) as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= ($selectedAccess ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Data type</span>
            <select name="data_type">
                <option value="">All data types</option>
                <?php foreach (['Text', 'Image', 'Audio', 'Video', 'Tabular'] as $dataType): ?>
                    <option value="<?= esc($dataType) ?>" <?= ($selectedDataType ?? '') === $dataType ? 'selected' : '' ?>><?= esc($dataType) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Sort</span>
            <select name="sort">
                <option value="oldest" <?= ($selectedSort ?? 'oldest') === 'oldest' ? 'selected' : '' ?>>Oldest assignment</option>
                <option value="newest" <?= ($selectedSort ?? '') === 'newest' ? 'selected' : '' ?>>Newest assignment</option>
                <option value="title" <?= ($selectedSort ?? '') === 'title' ? 'selected' : '' ?>>Dataset title</option>
                <option value="contributor" <?= ($selectedSort ?? '') === 'contributor' ? 'selected' : '' ?>>Contributor</option>
            </select>
        </label>
        <div class="review-filter-actions">
            <button class="button" type="submit">Apply</button>
            <a class="button secondary" href="<?= site_url('review/' . $stage) ?>?tab=<?= esc($tab ?? 'action') ?>">Reset</a>
        </div>
    </form>
</section>

<?php if ($reviews === []): ?>
    <article class="panel governance-empty-state">
        <span class="material-symbols-rounded" aria-hidden="true"><?= ($tab ?? 'action') === 'action' ? 'inbox' : 'search_off' ?></span>
        <h2><?= ($tab ?? 'action') === 'action' ? 'No reviews need action' : 'No matching review records' ?></h2>
        <p><?= ($tab ?? 'action') === 'action' ? 'Your active queue is clear. Automatically distributed reviews and administrator reassignments will appear here and trigger an activity notification.' : 'Try removing a filter or searching with a broader term.' ?></p>
    </article>
<?php else: ?>
    <div class="review-queue-list">
        <?php foreach ($reviews as $item): ?>
            <?php $file = is_array($item['latest_file'] ?? null) ? $item['latest_file'] : null; ?>
            <?php $progress = $item['progress'] ?? ['reviewed' => 0, 'total' => 5, 'issues' => 0]; ?>
            <article class="panel review-queue-card <?= ($item['age_days'] ?? 0) >= 3 && $item['status'] === 'assigned' ? 'is-aging' : '' ?>">
                <img class="review-queue-cover" src="<?= esc(dataset_cover_url($item), 'attr') ?>" alt="" loading="lazy">
                <div class="review-queue-copy">
                    <div class="review-queue-status">
                        <span class="status-pill status-<?= esc($item['dataset_status']) ?>"><?= esc(str_replace('_', ' ', $item['status'])) ?></span>
                        <span class="review-age-chip"><?= esc($item['age_label']) ?></span>
                        <?php if (! empty($item['draft_saved_at'])): ?><span class="review-draft-chip">Draft saved</span><?php endif; ?>
                    </div>
                    <h2><?= esc($item['title']) ?></h2>
                    <p><?= esc($item['description'] ?: 'No dataset description provided.') ?></p>
                    <div class="review-queue-meta">
                        <span><strong>Contributor</strong><?= esc($item['contributor_name'] ?? 'Unknown') ?></span>
                        <span><strong>Version</strong>v<?= esc($item['dataset_version'] ?? '1.0') ?></span>
                        <span><strong>Access</strong><?= esc(ucfirst((string) $item['access_type'])) ?></span>
                        <span><strong>Package</strong><?= $file ? esc($file['original_name']) : 'Unavailable' ?></span>
                    </div>
                    <div class="review-progress-line" aria-label="<?= esc((string) $progress['reviewed']) ?> of <?= esc((string) $progress['total']) ?> checks reviewed">
                        <span style="width: <?= $progress['total'] > 0 ? esc((string) (($progress['reviewed'] / $progress['total']) * 100), 'attr') : '0' ?>%"></span>
                    </div>
                    <small><?= esc((string) $progress['reviewed']) ?> of <?= esc((string) $progress['total']) ?> checks reviewed<?= $progress['issues'] > 0 ? ' · ' . esc((string) $progress['issues']) . ' issue(s)' : '' ?></small>
                </div>
                <div class="review-queue-action">
                    <span>Round <?= esc((string) $item['review_round']) ?></span>
                    <a class="button <?= $item['status'] === 'assigned' ? '' : 'secondary' ?>" href="<?= site_url('review/' . $stage . '/' . $item['id']) ?>">
                        <?= $item['status'] === 'assigned' ? 'Continue review' : 'View decision' ?>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (isset($pager) && $pager->getPageCount() > 1): ?>
    <nav class="portal-pagination" aria-label="Review queue pagination"><?= $pager->links() ?></nav>
<?php endif; ?>
<?= $this->endSection() ?>
