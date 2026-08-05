<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
    $citationDataset = [
        'title' => $dataset['title'],
        'author' => $dataset['author_name'] ?? '',
        'year' => ! empty($dataset['created_at']) ? date('Y', strtotime($dataset['created_at'])) : date('Y'),
        'doi' => $dataset['doi'] ?? '',
        'url' => $dataset['source_link'] ?? '',
    ];
    $apaCitation = dataset_apa_citation($citationDataset);
    $mlaCitation = dataset_mla_citation($citationDataset);
    $acmCitation = dataset_acm_citation($citationDataset);
    $bibtexCitation = dataset_bibtex($citationDataset);

    $description = trim((string) ($dataset['description'] ?? ''));
    $descriptionLength = function_exists('mb_strlen') ? mb_strlen($description) : strlen($description);
    $hasExpandableDescription = $descriptionLength > 180;
    $publishedSource = $dataset['approved_at'] ?? $dataset['created_at'] ?? null;
    $publishedDate = ! empty($publishedSource) ? date('m/d/Y', strtotime((string) $publishedSource)) : date('m/d/Y');
    $tagItems = array_values(array_filter(array_map('trim', explode(',', (string) ($dataset['tags'] ?? '')))));

    $recommendationPreviewData = [];
    foreach ($recommendations as $recommended) {
        $recommendationPreviewData[] = [
            'id' => (int) $recommended['id'],
            'title' => (string) ($recommended['title'] ?? 'Dataset'),
            'description' => trim((string) ($recommended['description'] ?? '')),
            'category' => trim((string) ($recommended['category'] ?? 'Uncategorized')) ?: 'Uncategorized',
            'data_type' => trim((string) ($recommended['data_type'] ?? 'Dataset')) ?: 'Dataset',
            'file_format' => trim((string) ($recommended['file_format'] ?? 'ZIP')) ?: 'ZIP',
            'content_formats' => trim((string) ($recommended['content_formats'] ?? '')) ?: 'Contents not disclosed',
            'source_type' => trim((string) ($recommended['source_type'] ?? '')) ?: 'Not set',
            'author_name' => trim((string) ($recommended['author_name'] ?? 'Unknown contributor')) ?: 'Unknown contributor',
            'research_title' => trim((string) ($recommended['research_title'] ?? '')) ?: 'Not set',
            'project_head' => trim((string) ($recommended['project_head'] ?? '')) ?: 'Not listed',
            'members' => trim((string) ($recommended['members'] ?? '')) ?: 'Not listed',
            'created_at' => ! empty($recommended['created_at']) ? date('F d, Y', strtotime((string) $recommended['created_at'])) : 'Not recorded',
            'score' => (int) ($recommended['score'] ?? 0),
            'tags' => (string) ($recommended['tags'] ?? ''),
        ];
    }
?>

<section class="shell dataset-detail-shell">
    <nav class="dataset-back-nav" aria-label="Breadcrumb">
        <a class="dataset-back-link" href="<?= site_url('datasets') ?>">
            <span class="material-symbols-rounded" aria-hidden="true">arrow_back</span>
            Back to Catalog
        </a>
    </nav>
    <div class="dataset-detail-main">
        <article class="panel dataset-hero-card <?= $hasExpandableDescription ? 'is-collapsed' : 'is-static' ?>">
            <div class="dataset-hero-cover">
                <div class="dataset-hero-cover-top">
                    <div class="dataset-hero-badges">
                        <span class="status-pill status-<?= esc($dataset['status'] ?? 'unknown') ?>"><?= esc($statusLabel ?? 'Unknown') ?></span>
                        <span class="access-pill access-<?= esc($dataset['access_type'] ?? 'public') ?>"><?= esc($accessLabel ?? 'Public') ?></span>
                    </div>
                    <span class="dataset-hero-version">v<?= esc($dataset['version'] ?? '1.0') ?></span>
                </div>

                <div class="dataset-hero-content">
                    <img
                        class="dataset-hero-image"
                        src="<?= esc(dataset_cover_url($dataset), 'attr') ?>"
                        alt="Cover for <?= esc($dataset['title'], 'attr') ?>"
                    >
                    <div class="dataset-hero-copy">
                        <h1><?= esc($dataset['title']) ?></h1>
                        <p class="dataset-hero-date">Published on: <?= esc($publishedDate) ?></p>
                    </div>
                </div>
            </div>

            <div class="dataset-hero-body">
                <div class="dataset-hero-summary">
                    <p class="dataset-hero-description"><?= esc($description ?: 'No description provided yet.') ?></p>
                </div>

                <?php if ($hasExpandableDescription): ?>
                    <button
                        class="dataset-hero-toggle"
                        type="button"
                        data-description-toggle
                        aria-expanded="false"
                    >
                        <span data-description-label>Read more</span>
                        <span class="material-symbols-rounded" aria-hidden="true">expand_more</span>
                    </button>
                <?php endif; ?>
            </div>
        </article>

        <details class="panel dataset-accordion" open>
            <summary class="dataset-accordion-summary">
                <span>
                    <small>Dataset Information</small>
                    <strong>Metadata, source, and tag chips</strong>
                </span>
                <span class="dataset-accordion-icon material-symbols-rounded" aria-hidden="true">expand_more</span>
            </summary>
            <div class="dataset-accordion-body">
                <dl class="dataset-info-grid" aria-label="Dataset metadata">
                    <div>
                        <dt>Research Title</dt>
                        <dd><?= esc($dataset['research_title'] ?: 'Not set') ?></dd>
                    </div>
                    <div>
                        <dt>Project Head</dt>
                        <dd><?= esc($dataset['project_head'] ?: 'Not set') ?></dd>
                    </div>
                    <div>
                        <dt>Authors</dt>
                        <dd><?= esc($dataset['members'] ?: 'Not listed') ?></dd>
                    </div>
                    <div>
                        <dt>Source</dt>
                        <dd>
                            <?= esc($dataset['source_type'] ?: 'Not set') ?>
                            <?php if (! empty($dataset['source_link'])): ?>
                                <span class="dataset-inline-separator">&middot;</span>
                                <a href="<?= esc((string) $dataset['source_link'], 'attr') ?>" target="_blank" rel="noopener noreferrer"><?= esc($dataset['source_link']) ?></a>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div>
                        <dt>Category</dt>
                        <dd><?= esc($dataset['category'] ?: 'Uncategorized') ?></dd>
                    </div>
                    <div>
                        <dt>Data Type</dt>
                        <dd><?= esc($dataset['data_type'] ?: 'Dataset') ?></dd>
                    </div>
                    <div>
                        <dt>Formats Inside ZIP</dt>
                        <dd><?= esc($dataset['content_formats'] ?: 'Not disclosed') ?></dd>
                    </div>
                    <div>
                        <dt>Package</dt>
                        <dd><?= esc($dataset['file_format'] ?: 'ZIP') ?></dd>
                    </div>
                    <div>
                        <dt>Access</dt>
                        <dd><?= esc($accessLabel ?? 'Public') ?></dd>
                    </div>
                    <div>
                        <dt>Status</dt>
                        <dd><?= esc($statusLabel ?? 'Unknown') ?></dd>
                    </div>
                </dl>

                <div class="dataset-chip-block">
                    <p class="dataset-chip-label">Tags</p>
                    <?php if (empty($tagItems)): ?>
                        <p class="muted">No tags</p>
                    <?php else: ?>
                        <div class="dataset-chip-list" aria-label="Dataset tags">
                            <?php foreach ($tagItems as $tag): ?>
                                <span class="dataset-chip"><?= esc($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </details>

        <details class="panel dataset-accordion" open>
            <summary class="dataset-accordion-summary">
                <span>
                    <small>Dataset Files</small>
                    <strong>Latest upload and download access</strong>
                </span>
                <span class="dataset-accordion-icon material-symbols-rounded" aria-hidden="true">expand_more</span>
            </summary>
            <div class="dataset-accordion-body">
                <div class="dataset-files-head" aria-hidden="true">
                    <span>Files</span>
                    <span>Size</span>
                </div>

                <?php if (! empty($latestFile)): ?>
                    <article class="dataset-file-row">
                        <div class="dataset-file-copy">
                            <span class="dataset-file-name"><?= esc($latestFile['original_name']) ?></span>
                        </div>
                        <div class="dataset-file-size"><?= esc(number_format((int) $latestFile['file_size'])) ?> bytes</div>
                    </article>
                <?php else: ?>
                    <p class="muted">No uploaded dataset file is available for this record yet.</p>
                <?php endif; ?>
            </div>
        </details>

    </div>

    <div class="dataset-sidebar">
        <aside class="panel">
        <p class="tag">Dataset File</p>
        <h2>Download and Cite</h2>

        <dl class="dataset-stats-grid" aria-label="Dataset summary statistics">
            <div>
                <dt>Version</dt>
                <dd><?= esc($dataset['version'] ?? '1.0') ?></dd>
            </div>
            <div>
                <dt>Views</dt>
                <dd><?= esc((string) ($viewCount ?? 0)) ?></dd>
            </div>
            <div>
                <dt>Downloads</dt>
                <dd><?= esc((string) ($downloadCount ?? 0)) ?></dd>
            </div>
            <div>
                <dt>Contributor</dt>
                <dd><?= esc($dataset['author_name'] ?? 'Unknown contributor') ?></dd>
            </div>
        </dl>

        <div class="actions dataset-sidebar-actions">
            <?php if (empty($latestFile)): ?>
                <span class="button is-disabled" aria-disabled="true">Download unavailable</span>
            <?php elseif (! empty($downloadRequiresLogin)): ?>
                <a class="button" href="<?= site_url('datasets/' . $datasetId . '/download') ?>">
                    <span class="material-symbols-rounded" aria-hidden="true">lock</span>
                    Sign in to Download
                </a>
            <?php elseif (! empty($canDownload)): ?>
                <a class="button" href="<?= site_url('datasets/' . $datasetId . '/download') ?>">Download</a>
            <?php else: ?>
                <span class="button secondary is-disabled" aria-disabled="true">Download restricted</span>
            <?php endif; ?>
            <button
                class="button gold citation-trigger"
                type="button"
                data-citation-target="dataset-citation-<?= esc((string) $datasetId) ?>"
                aria-controls="dataset-citation-<?= esc((string) $datasetId) ?>"
                aria-expanded="false"
            >
                Cite
            </button>
            <?php if (! empty($canEdit)): ?>
                <a class="button secondary dataset-sidebar-edit" href="<?= site_url('datasets/' . $datasetId . '/edit') ?>">Edit dataset</a>
            <?php endif; ?>
        </div>
        </aside>

        <section class="panel dataset-accordion dataset-recommendations-accordion">
            <div class="dataset-accordion-summary">
                <span>
                    <small>Recommended Datasets</small>
                    <strong>Similar datasets and preview shortcuts</strong>
                </span>
            </div>
            <div class="dataset-accordion-body">
                <?php if (empty($recommendationPreviewData)): ?>
                    <p class="muted">No metadata-based recommendations are available yet.</p>
                <?php else: ?>
                    <div class="dataset-rec-list">
                        <?php foreach ($recommendationPreviewData as $recommended): ?>
                            <article class="dataset-rec-row">
                                <div class="dataset-rec-copy">
                                    <h3><a href="<?= site_url('datasets/' . $recommended['id']) ?>"><?= esc($recommended['title']) ?></a></h3>
                                    <div class="dataset-rec-meta">
                                        <span class="dataset-chip dataset-chip--accent"><?= esc($recommended['category']) ?></span>
                                        <span class="dataset-rec-score">score <?= esc((string) $recommended['score']) ?></span>
                                    </div>
                                </div>
                                <button class="button secondary dataset-preview-trigger" type="button" data-preview-target="dataset-preview-<?= esc((string) $recommended['id']) ?>" aria-controls="dataset-preview-<?= esc((string) $recommended['id']) ?>" aria-expanded="false">Preview</button>
                            </article>

                            <?= view('components/dataset_preview', [
                                'dataset'   => $recommended,
                                'context'   => 'recommended',
                                'triggerId' => 'dataset-preview-' . (string) $recommended['id'],
                            ]) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</section>

<div class="preview-modal citation-modal" id="dataset-citation-<?= esc((string) $datasetId) ?>" role="dialog" aria-modal="true" aria-labelledby="citation-modal-title-<?= esc((string) $datasetId) ?>" hidden>
    <div class="preview-backdrop" data-citation-close></div>
    <article class="preview-card citation-card" tabindex="-1">
        <button class="preview-close" type="button" data-citation-close aria-label="Close citations">&times;</button>
        <div class="citation-header">
            <div>
                <p class="tag">Citation</p>
                <h2 id="citation-modal-title-<?= esc((string) $datasetId) ?>">Cite this dataset</h2>
            </div>
            <button class="button citation-copy-btn" type="button" data-citation-copy-active data-format="apa">
                <span class="material-symbols-rounded" aria-hidden="true">content_copy</span>
                <span class="citation-copy-label">Copy</span>
            </button>
        </div>

        <nav class="citation-tabs" role="tablist" aria-label="Citation format">
            <button class="citation-tab is-active" role="tab" id="citation-tab-apa-<?= esc((string) $datasetId) ?>" aria-selected="true" aria-controls="citation-panel-apa-<?= esc((string) $datasetId) ?>" data-citation-tab="apa">
                APA
            </button>
            <button class="citation-tab" role="tab" id="citation-tab-mla-<?= esc((string) $datasetId) ?>" aria-selected="false" aria-controls="citation-panel-mla-<?= esc((string) $datasetId) ?>" data-citation-tab="mla">
                MLA
            </button>
            <button class="citation-tab" role="tab" id="citation-tab-acm-<?= esc((string) $datasetId) ?>" aria-selected="false" aria-controls="citation-panel-acm-<?= esc((string) $datasetId) ?>" data-citation-tab="acm">
                ACM
            </button>
            <button class="citation-tab" role="tab" id="citation-tab-bibtex-<?= esc((string) $datasetId) ?>" aria-selected="false" aria-controls="citation-panel-bibtex-<?= esc((string) $datasetId) ?>" data-citation-tab="bibtex">
                BibTeX
            </button>
        </nav>

        <div class="citation-panels">
            <div class="citation-panel is-active" role="tabpanel" id="citation-panel-apa-<?= esc((string) $datasetId) ?>" aria-labelledby="citation-tab-apa-<?= esc((string) $datasetId) ?>" data-citation-panel="apa">
                <pre class="citation-output citation-output--apa"><?= esc($apaCitation) ?></pre>
            </div>
            <div class="citation-panel" role="tabpanel" id="citation-panel-mla-<?= esc((string) $datasetId) ?>" aria-labelledby="citation-tab-mla-<?= esc((string) $datasetId) ?>" data-citation-panel="mla" hidden>
                <pre class="citation-output citation-output--mla"><?= esc($mlaCitation) ?></pre>
            </div>
            <div class="citation-panel" role="tabpanel" id="citation-panel-acm-<?= esc((string) $datasetId) ?>" aria-labelledby="citation-tab-acm-<?= esc((string) $datasetId) ?>" data-citation-panel="acm" hidden>
                <pre class="citation-output citation-output--acm"><?= esc($acmCitation) ?></pre>
            </div>
            <div class="citation-panel" role="tabpanel" id="citation-panel-bibtex-<?= esc((string) $datasetId) ?>" aria-labelledby="citation-tab-bibtex-<?= esc((string) $datasetId) ?>" data-citation-panel="bibtex" hidden>
                <pre class="citation-output citation-output--bibtex"><?= esc($bibtexCitation) ?></pre>
            </div>
        </div>
    </article>
</div>

<script>
    const descriptionToggle = document.querySelector('[data-description-toggle]');
    const descriptionCard = document.querySelector('.dataset-hero-card');
    const descriptionLabel = document.querySelector('[data-description-label]');

    descriptionToggle?.addEventListener('click', () => {
        const isExpanded = descriptionToggle.getAttribute('aria-expanded') === 'true';
        const nextExpanded = !isExpanded;

        descriptionToggle.setAttribute('aria-expanded', nextExpanded ? 'true' : 'false');
        descriptionCard?.classList.toggle('is-expanded', nextExpanded);
        descriptionCard?.classList.toggle('is-collapsed', !nextExpanded);

        if (descriptionLabel) {
            descriptionLabel.textContent = nextExpanded ? 'Show less' : 'Read more';
        }
    });

    const focusablePreviewSelector = 'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])';

    document.querySelectorAll('.dataset-preview-trigger').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modal = document.getElementById(trigger.dataset.previewTarget);
            if (!modal) return;

            modal.hidden = false;
            document.body.classList.add('preview-open');
            trigger.setAttribute('aria-expanded', 'true');
            (modal.querySelector('[data-preview-initial]') || modal.querySelector('[data-preview-primary]') || modal.querySelector('.preview-card'))?.focus();
        });
    });

    const closePreview = (control) => {
        const modalId = control.dataset.previewClose;
        const modal = modalId ? document.getElementById(modalId) : control.closest('.preview-modal, .dataset-preview-modal');
        if (!modal) return;
        const trigger = document.querySelector(`[data-preview-target="${modal.id}"]`);
        modal.hidden = true;
        document.body.classList.remove('preview-open');
        trigger?.setAttribute('aria-expanded', 'false');
        trigger?.focus();
    };

    document.querySelectorAll('[data-preview-close]').forEach((control) => {
        control.addEventListener('click', () => closePreview(control));
    });

    document.querySelectorAll('.citation-trigger').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modal = document.getElementById(trigger.dataset.citationTarget);
            if (!modal) return;

            modal.hidden = false;
            document.body.classList.add('preview-open');
            trigger.setAttribute('aria-expanded', 'true');
            modal.querySelector('.citation-card')?.focus();
        });
    });

    const closeCitation = (modal) => {
        if (!modal) return;

        const trigger = document.querySelector(`[data-citation-target="${modal.id}"]`);
        modal.hidden = true;
        document.body.classList.remove('preview-open');
        trigger?.setAttribute('aria-expanded', 'false');
        trigger?.focus();
    };

    document.querySelectorAll('[data-citation-close]').forEach((control) => {
        control.addEventListener('click', () => closeCitation(control.closest('.citation-modal')));
    });

    // Citation tab switching
    document.querySelectorAll('.citation-tab').forEach((tab) => {
        tab.addEventListener('click', () => {
            const modal = tab.closest('.citation-modal');
            if (!modal) return;

            const format = tab.dataset.citationTab;

            // Update tabs
            modal.querySelectorAll('.citation-tab').forEach((t) => {
                t.classList.remove('is-active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('is-active');
            tab.setAttribute('aria-selected', 'true');

            // Update panels
            modal.querySelectorAll('.citation-panel').forEach((p) => {
                p.classList.remove('is-active');
                p.hidden = true;
            });
            const activePanel = modal.querySelector(`[data-citation-panel="${format}"]`);
            if (activePanel) {
                activePanel.classList.add('is-active');
                activePanel.hidden = false;
            }

            // Update copy button format
            const copyBtn = modal.querySelector('[data-citation-copy-active]');
            if (copyBtn) {
                copyBtn.dataset.format = format;
            }
        });
    });

    // Single copy button for active tab
    document.querySelectorAll('[data-citation-copy-active]').forEach((button) => {
        button.addEventListener('click', async () => {
            const modal = button.closest('.citation-modal');
            if (!modal) return;

            const activePanel = modal.querySelector('.citation-panel.is-active');
            const citation = activePanel?.querySelector('.citation-output')?.textContent;
            if (!citation) return;

            const label = button.querySelector('.citation-copy-label');
            const originalLabel = label?.textContent ?? 'Copy';
            try {
                await navigator.clipboard.writeText(citation.trim());
                if (label) label.textContent = 'Copied!';
            } catch (error) {
                if (label) label.textContent = 'Failed';
            }

            window.setTimeout(() => { if (label) label.textContent = originalLabel; }, 1600);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            const openPreviewModal = document.querySelector('.preview-modal:not(.citation-modal):not([hidden])');
            if (openPreviewModal) {
                closePreview(openPreviewModal);
                return;
            }

            const openCitationModal = document.querySelector('.citation-modal:not([hidden])');
            if (openCitationModal) {
                closeCitation(openCitationModal);
            }

            return;
        }

        if (event.key !== 'Tab') return;

        const openModal = document.querySelector('.preview-modal:not(.citation-modal):not([hidden])');
        if (!openModal) return;

        const focusable = Array.from(openModal.querySelectorAll(focusablePreviewSelector))
            .filter((element) => element.offsetParent !== null || element === document.activeElement);
        if (focusable.length === 0) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });
</script>
<?= $this->endSection() ?>
