<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
    $viewLabels = [
        'all' => 'All records',
        'technical' => 'Technical',
        'ethics' => 'Ethics',
        'publication' => 'Publication',
        'revision' => 'Needs revision',
        'published' => 'Published',
        'closed' => 'Closed',
    ];
    $activeFilters = ($selectedView ?? 'all') !== 'all' || ($selectedAccess ?? '') !== '' || ($search ?? '') !== '';
    $filteredTotal = isset($pager) ? $pager->getTotal('dashboard') : count($myDatasets ?? []);
    $currentPage = isset($pager) ? $pager->getCurrentPage('dashboard') : 1;
    $perPage = isset($pager) ? $pager->getPerPage('dashboard') : 12;
    $rangeStart = $filteredTotal > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
    $rangeEnd = min($filteredTotal, $rangeStart + count($myDatasets ?? []) - 1);
?>

<section class="contributor-library-hero">
    <div class="shell contributor-library-hero__inner">
        <div class="contributor-library-intro">
            <p class="eyebrow">Contributor workspace</p>
            <h1>My datasets</h1>
            <p>Follow each submission from technical verification to ethics clearance, publication, and long-term repository access.</p>
        </div>
        <div class="contributor-library-actions">
            <a class="button gold" href="<?= site_url('upload') ?>"><span class="material-symbols-rounded" aria-hidden="true">upload_file</span> Upload dataset</a>
            <a class="button secondary" href="<?= site_url('datasets') ?>"><span class="material-symbols-rounded" aria-hidden="true">travel_explore</span> Browse catalog</a>
        </div>
    </div>

    <div class="shell contributor-library-summary">
        <article>
            <span class="material-symbols-rounded" aria-hidden="true">folder_open</span>
            <div><strong><?= esc((string) ($statusCounts['all'] ?? 0)) ?></strong><small>Total records</small></div>
        </article>
        <article>
            <span class="material-symbols-rounded" aria-hidden="true">fact_check</span>
            <div><strong><?= esc((string) (($statusCounts['pendingTechnical'] ?? 0) + ($statusCounts['pendingEthics'] ?? 0))) ?></strong><small>Under review</small></div>
        </article>
        <article class="<?= ($statusCounts['revision'] ?? 0) > 0 ? 'is-attention' : '' ?>">
            <span class="material-symbols-rounded" aria-hidden="true">edit_note</span>
            <div><strong><?= esc((string) ($statusCounts['revision'] ?? 0)) ?></strong><small>Need your action</small></div>
        </article>
        <article>
            <span class="material-symbols-rounded" aria-hidden="true">published_with_changes</span>
            <div><strong><?= esc((string) ($statusCounts['published'] ?? 0)) ?></strong><small>Published</small></div>
        </article>
    </div>
</section>

<main class="shell contributor-library">
    <?php if (($totalDatasets ?? 0) === 0): ?>
        <section class="contributor-first-upload">
            <div class="contributor-first-upload__copy">
                <p class="eyebrow">Your repository starts here</p>
                <h2>No datasets uploaded yet</h2>
                <p>Prepare one ZIP package and a clear metadata record. This workspace will then show exactly where the submission is, who needs to act, and how it will be published.</p>
                <div class="contributor-first-upload__actions">
                    <a class="button gold" href="<?= site_url('upload') ?>"><span class="material-symbols-rounded" aria-hidden="true">add_circle</span> Upload your first dataset</a>
                    <a class="button secondary" href="<?= site_url('datasets') ?>">See published examples</a>
                </div>
            </div>
            <ol class="contributor-onboarding-steps">
                <li><span>1</span><div><strong>Upload package</strong><p>Add metadata, documentation, and the protected ZIP.</p></div></li>
                <li><span>2</span><div><strong>Technical review</strong><p>Reviewers verify archive readability, files, formats, and documentation.</p></div></li>
                <li><span>3</span><div><strong>Ethics review</strong><p>Consent, anonymization, clearance, and data handling are checked.</p></div></li>
                <li><span>4</span><div><strong>Publication</strong><p>An administrator confirms access and publishes the approved version.</p></div></li>
            </ol>
        </section>
    <?php else: ?>
        <section class="contributor-library-controls" aria-labelledby="dataset-library-heading">
            <div class="contributor-library-controls__heading">
                <div>
                    <p class="eyebrow">Submission library</p>
                    <h2 id="dataset-library-heading">Dataset records</h2>
                    <p><?= $filteredTotal > 0 ? 'Showing ' . esc((string) $rangeStart) . '-' . esc((string) $rangeEnd) . ' of ' . esc((string) $filteredTotal) . ' matching records.' : 'No records match the current view.' ?></p>
                </div>
                <form class="contributor-library-search" method="get" action="<?= site_url('dashboard') ?>">
                    <input type="hidden" name="view" value="<?= esc($selectedView ?? 'all') ?>">
                    <label><span class="sr-only">Search your datasets</span><span class="material-symbols-rounded" aria-hidden="true">search</span><input type="search" name="q" value="<?= esc($search ?? '') ?>" placeholder="Search title, category, or tags"></label>
                    <button class="button" type="submit">Filter</button>
                    <a class="button secondary contributor-clear-filters" href="<?= site_url('dashboard') ?>">Clear all</a>
                </form>
            </div>

            <nav class="contributor-status-tabs" aria-label="Dataset workflow filters">
                <?php foreach ($viewLabels as $value => $label): ?>
                    <a class="<?= ($selectedView ?? 'all') === $value ? 'is-active' : '' ?>" href="<?= site_url('dashboard') ?>?view=<?= esc($value) ?><?= ($search ?? '') !== '' ? '&q=' . urlencode($search) : '' ?><?= ($selectedAccess ?? '') !== '' ? '&access=' . urlencode($selectedAccess) : '' ?>">
                        <?= esc($label) ?><span><?= esc((string) ($statusCounts[$value] ?? 0)) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <?php if (($statusCounts['published'] ?? 0) > 0 && in_array(($selectedView ?? 'all'), ['all', 'published'], true)): ?>
                <div class="contributor-access-summary" aria-label="Published access breakdown">
                    <span class="contributor-access-summary__label">Published access</span>
                    <?php foreach (($accessOptions ?? []) as $value => $label): ?>
                        <a class="<?= ($selectedAccess ?? '') === $value ? 'is-active' : '' ?>" href="<?= site_url('dashboard') ?>?view=published&amp;access=<?= esc($value) ?>">
                            <span class="material-symbols-rounded" aria-hidden="true"><?= esc(match ($value) {
                                \App\Models\DatasetModel::ACCESS_PUBLIC => 'public',
                                \App\Models\DatasetModel::ACCESS_INSTITUTIONAL => 'school',
                                \App\Models\DatasetModel::ACCESS_RESTRICTED => 'key',
                                default => 'lock',
                            }) ?></span>
                            <?= esc($label) ?><strong><?= esc((string) ($publishedAccessCounts[$value] ?? 0)) ?></strong>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <?php if (empty($myDatasets)): ?>
            <section class="contributor-no-results">
                <span class="material-symbols-rounded" aria-hidden="true">filter_alt_off</span>
                <h2>No matching datasets</h2>
                <p>Your uploads are still here. Clear the search or switch workflow views to see the rest of your records.</p>
                <a class="button secondary" href="<?= site_url('dashboard') ?>">Clear filters</a>
            </section>
        <?php else: ?>
            <div class="contributor-record-list">
                <?php foreach ($myDatasets as $dataset): ?>
                    <?php
                        $latestReview = is_array($dataset['latestReview'] ?? null) ? $dataset['latestReview'] : null;
                        $workflow = is_array($dataset['workflow'] ?? null) ? $dataset['workflow'] : ['stage' => 'Workflow status unavailable', 'detail' => '', 'icon' => 'info', 'tone' => 'neutral', 'step' => 0];
                        $nextAction = is_array($dataset['nextAction'] ?? null) ? $dataset['nextAction'] : ['label' => 'Open the record for details.', 'url' => null, 'tone' => 'neutral'];
                        $step = (int) ($workflow['step'] ?? 0);
                    ?>
                    <article class="contributor-record contributor-record--<?= esc($workflow['tone'] ?? 'neutral') ?>">
                        <a class="contributor-record__cover" href="<?= site_url('datasets/' . $dataset['id']) ?>" aria-label="Open <?= esc($dataset['title'], 'attr') ?>">
                            <img src="<?= esc(dataset_cover_url($dataset), 'attr') ?>" alt="" loading="lazy">
                        </a>

                        <div class="contributor-record__main">
                            <div class="contributor-record__eyebrow">
                                <span><?= esc($dataset['category'] ?: 'Uncategorized') ?></span>
                                <span>Version <?= esc($dataset['version'] ?? '1.0') ?></span>
                                <span>Updated <?= ! empty($dataset['updated_at']) ? esc(date('M d, Y', strtotime($dataset['updated_at']))) : 'date unavailable' ?></span>
                            </div>
                            <h3><a href="<?= site_url('datasets/' . $dataset['id']) ?>"><?= esc($dataset['title']) ?></a></h3>
                            <p class="contributor-record__description"><?= esc($dataset['description'] ?: 'No description provided.') ?></p>

                            <div class="contributor-workflow-state">
                                <span class="contributor-workflow-state__icon"><span class="material-symbols-rounded" aria-hidden="true"><?= esc($workflow['icon']) ?></span></span>
                                <div><strong><?= esc($workflow['stage']) ?></strong><p><?= esc($workflow['detail']) ?></p></div>
                            </div>

                            <?php if ($step > 0): ?>
                                <ol class="contributor-progress" aria-label="Submission workflow progress">
                                    <?php foreach ([1 => 'Technical', 2 => 'Ethics', 3 => 'Publication', 4 => 'Published'] as $progressStep => $label): ?>
                                        <li class="<?= $progressStep < $step ? 'is-complete' : ($progressStep === $step ? 'is-current' : '') ?>"><span><?= $progressStep < $step ? 'check' : $progressStep ?></span><small><?= esc($label) ?></small></li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php endif; ?>

                            <?php if ($latestReview && trim((string) ($latestReview['comments'] ?? '')) !== ''): ?>
                                <aside class="contributor-review-note">
                                    <span class="material-symbols-rounded" aria-hidden="true">rate_review</span>
                                    <div><strong><?= esc(ucfirst((string) ($latestReview['stage'] ?? 'Review'))) ?> reviewer note</strong><p><?= esc($latestReview['comments']) ?></p></div>
                                </aside>
                            <?php endif; ?>
                        </div>

                        <aside class="contributor-record__aside">
                            <div class="contributor-access-card contributor-access-card--<?= esc($dataset['access_type'] ?? 'public') ?>">
                                <span class="material-symbols-rounded" aria-hidden="true"><?= esc(($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_PUBLISHED ? $workflow['icon'] : 'visibility') ?></span>
                                <div><small><?= ($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_PUBLISHED ? 'Published access' : 'Requested access' ?></small><strong><?= esc($dataset['accessLabel'] ?? 'Public') ?></strong></div>
                            </div>

                            <div class="contributor-next-action contributor-next-action--<?= esc($nextAction['tone'] ?? 'neutral') ?>">
                                <small>Next step</small>
                                <p><?= esc($nextAction['label'] ?? 'Open the record for details.') ?></p>
                            </div>

                            <div class="contributor-record__actions">
                                <?php if (! empty($dataset['canEdit'])): ?>
                                    <a class="button" href="<?= site_url('datasets/' . $dataset['id'] . '/edit') ?>"><?= ($dataset['status'] ?? '') === \App\Models\DatasetModel::STATUS_PUBLISHED ? 'Submit update' : 'Revise dataset' ?></a>
                                <?php endif; ?>
                                <a class="button secondary" href="<?= site_url('datasets/' . $dataset['id']) ?>">View record</a>
                                <?php if (! empty($dataset['canArchive'])): ?>
                                    <form method="post" action="<?= site_url('datasets/' . $dataset['id'] . '/archive') ?>"><?= csrf_field() ?><button class="text-button" type="submit">Archive record</button></form>
                                <?php endif; ?>
                            </div>
                        </aside>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (isset($pager) && $pager->getPageCount('dashboard') > 1): ?>
                <nav class="contributor-pagination" aria-label="My datasets pagination"><?= $pager->links('dashboard', 'dashboard_full') ?></nav>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?= $this->endSection() ?>
