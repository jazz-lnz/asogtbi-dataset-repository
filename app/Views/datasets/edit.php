<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="hero-panel">
    <div class="shell">
        <p class="eyebrow">Dataset Revision</p>
        <h1>Edit Dataset</h1>
        <p class="lead">Update your dataset metadata or upload a new ZIP version. Revision-requested records return to Pending Review when saved.</p>
        <span class="status-pill status-<?= esc($dataset['status'] ?? 'unknown') ?>"><?= esc($statusLabel ?? 'Unknown') ?></span>
    </div>
</section>

<section class="shell split-grid form-shell">
    <section class="form-card">
        <div class="panel-head">
            <div>
                <p class="tag">Dataset Metadata</p>
                <h2><?= esc($dataset['title'] ?? 'Dataset') ?></h2>
            </div>
        </div>

        <form method="post" action="<?= site_url('datasets/' . $datasetId . '/update') ?>" enctype="multipart/form-data" id="edit-dataset-form">
            <?= csrf_field() ?>
            <div class="grid">
                <div>
                    <label for="title">Title</label>
                    <input id="title" name="title" value="<?= old('title', $dataset['title'] ?? '') ?>">
                </div>
                <div>
                    <label for="category">Category</label>
                    <input id="category" name="category" value="<?= old('category', $dataset['category'] ?? '') ?>">
                </div>
                <div>
                    <label for="data_type">Data Type</label>
                    <select id="data_type" name="data_type">
                        <?php foreach (($dataTypes ?? []) as $dataType): ?>
                            <option value="<?= esc($dataType) ?>" <?= old('data_type', $dataset['data_type'] ?? '') === $dataType ? 'selected' : '' ?>><?= esc($dataType) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="content_formats">Formats Inside ZIP</label>
                    <input id="content_formats" name="content_formats" value="<?= old('content_formats', $dataset['content_formats'] ?? '') ?>" placeholder="e.g., .csv, .pdf, .png, .svg">
                </div>
                <div>
                    <label for="access_type">Access Type</label>
                    <select id="access_type" name="access_type">
                        <?php foreach (($accessTypes ?? []) as $value => $label): ?>
                            <option value="<?= esc($value) ?>" <?= old('access_type', $dataset['access_type'] ?? 'public') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?= old('description', $dataset['description'] ?? '') ?></textarea>

            <label for="cover_image">Dataset Cover <span class="label-optional">(OPTIONAL)</span></label>
            <span class="help-text">Upload a JPG, PNG, or WebP image up to 4 MB to replace the current catalog cover.</span>
            <div class="cover-upload-control">
                <img
                    class="cover-upload-preview"
                    id="cover-preview"
                    src="<?= esc(dataset_cover_url($dataset), 'attr') ?>"
                    alt="Current cover for <?= esc($dataset['title'] ?? 'dataset', 'attr') ?>"
                >
                <div class="cover-upload-copy">
                    <strong>Current catalog cover</strong>
                    <p class="muted">Leave this empty to keep the current image. Existing datasets without a cover use the repository placeholder.</p>
                    <input id="cover_image" type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <button class="button secondary cover-clear-button" id="cover-clear" type="button" hidden>Keep current cover</button>
                </div>
            </div>

            <label for="tags">Tags</label>
            <input id="tags" name="tags" value="<?= old('tags', $dataset['tags'] ?? '') ?>">

            <label for="research_title">Research Title</label>
            <input id="research_title" name="research_title" value="<?= old('research_title', $dataset['research_title'] ?? '') ?>">

            <label for="project_head">Project Head or Adviser</label>
            <input id="project_head" name="project_head" value="<?= old('project_head', $dataset['project_head'] ?? '') ?>">

            <label for="members">Research Members or Contributors</label>
            <textarea id="members" name="members" rows="3"><?= old('members', $dataset['members'] ?? '') ?></textarea>

            <label for="source_type">Source Type</label>
            <select id="source_type" name="source_type">
                <?php foreach (($sourceTypes ?? []) as $sourceType): ?>
                    <option value="<?= esc($sourceType) ?>" <?= old('source_type', $dataset['source_type'] ?? '') === $sourceType ? 'selected' : '' ?>><?= esc($sourceType) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="source_link">Source Link</label>
            <input id="source_link" name="source_link" value="<?= old('source_link', $dataset['source_link'] ?? '') ?>">

            <label for="change_summary">Change Summary</label>
            <textarea id="change_summary" name="change_summary" rows="3" placeholder="Describe what changed in this revision"><?= old('change_summary') ?></textarea>

            <label for="dataset_file">New ZIP version</label>
            <div class="file-zone">
                <strong>Optional file replacement</strong>
                <p class="muted">Upload a new ZIP package only when the dataset file changed.</p>
                <input id="dataset_file" type="file" name="dataset_file" accept=".zip">
            </div>

            <div class="actions">
                <button class="button" type="submit" id="edit-dataset-submit">Review and Submit</button>
                <a class="button secondary" href="<?= site_url('dashboard') ?>">Back to My Datasets</a>
            </div>
        </form>
    </section>

    <aside class="panel detail-sidebar">
        <p class="tag">Current Record</p>
        <h2>Metadata snapshot</h2>
        <ul class="record-list">
            <li><strong>Status</strong><span class="muted"><?= esc($statusLabel ?? 'Unknown') ?></span></li>
            <li><strong>Version</strong><span class="muted"><?= esc($dataset['version'] ?? '1.0') ?></span></li>
            <li><strong>Package</strong><span class="muted"><?= esc($dataset['file_format'] ?? 'ZIP') ?></span></li>
            <li><strong>Formats inside ZIP</strong><span class="muted"><?= esc($dataset['content_formats'] ?? 'Not disclosed') ?></span></li>
        </ul>
        <form method="post" action="<?= site_url('datasets/' . $datasetId . '/archive') ?>">
            <?= csrf_field() ?>
            <div class="actions">
                <button class="button warning" type="submit">Archive Dataset</button>
            </div>
        </form>
    </aside>
</section>

<div class="preview-modal upload-preview-modal edit-preview-modal" id="edit-preview-modal" role="dialog" aria-modal="true" aria-labelledby="edit-preview-title" aria-describedby="edit-preview-summary" hidden>
    <div class="preview-backdrop" data-preview-close></div>
    <article class="preview-card upload-preview-card" tabindex="-1">
        <button class="preview-close" type="button" aria-label="Close preview" data-preview-close>&times;</button>
        <div class="preview-title-row">
            <div>
                <p class="tag">Revision Preview</p>
                <h2 id="edit-preview-title" tabindex="-1">Review your changes</h2>
                <p class="preview-kicker" id="edit-preview-summary">Check the edited metadata below before saving this revision.</p>
                <div class="row-badge-line preview-pill-line" aria-label="Preview state">
                    <span class="row-pill tech-type">Draft</span>
                    <span class="row-pill tech-outline">Not saved yet</span>
                </div>
            </div>
        </div>
        <div class="upload-preview-body">
            <section class="upload-preview-section">
                <div class="upload-preview-section-head">
                    <span class="upload-preview-accent"></span>
                    <h3>Dataset details</h3>
                </div>
                <div class="upload-preview-grid upload-preview-grid--three">
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Title</span>
                        <strong id="edit-preview-title-value"><?= esc(old('title', $dataset['title'] ?? '')) ?></strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Category</span>
                        <strong id="edit-preview-category-value"><?= esc(old('category', $dataset['category'] ?? '')) ?></strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Access type</span>
                        <strong id="edit-preview-access-value"><?= esc($accessLabel ?? 'Public') ?></strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Data type</span>
                        <strong id="edit-preview-data-type-value"><?= esc(old('data_type', $dataset['data_type'] ?? '')) ?></strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Formats inside ZIP</span>
                        <strong id="edit-preview-content-formats-value"><?= esc(old('content_formats', $dataset['content_formats'] ?? '')) ?></strong>
                    </div>
                    <div class="upload-preview-field upload-preview-field--full">
                        <span class="upload-preview-label">Description</span>
                        <strong id="edit-preview-description-value"><?= esc(old('description', $dataset['description'] ?? '')) ?></strong>
                    </div>
                </div>
            </section>

            <section class="upload-preview-section">
                <div class="upload-preview-section-head">
                    <span class="upload-preview-accent"></span>
                    <h3>Research details</h3>
                </div>
                <div class="upload-preview-grid upload-preview-grid--two">
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Research title</span>
                        <strong id="edit-preview-research-title-value"><?= esc(old('research_title', $dataset['research_title'] ?? '')) ?></strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Project head</span>
                        <strong id="edit-preview-project-head-value"><?= esc(old('project_head', $dataset['project_head'] ?? '')) ?></strong>
                    </div>
                    <div class="upload-preview-field upload-preview-field--full">
                        <span class="upload-preview-label">Members</span>
                        <strong id="edit-preview-members-value"><?= esc(old('members', $dataset['members'] ?? 'Not listed')) ?></strong>
                    </div>
                </div>
            </section>

            <section class="upload-preview-section">
                <div class="upload-preview-section-head">
                    <span class="upload-preview-accent"></span>
                    <h3>Source, cover, and revision</h3>
                </div>
                <div class="upload-preview-grid upload-preview-grid--two">
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Source type</span>
                        <strong id="edit-preview-source-type-value"><?= esc(old('source_type', $dataset['source_type'] ?? '')) ?></strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Source link</span>
                        <strong id="edit-preview-source-link-value"><?= esc(old('source_link', $dataset['source_link'] ?? 'Not provided')) ?></strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Tags</span>
                        <strong id="edit-preview-tags-value"><?= esc(old('tags', $dataset['tags'] ?? '')) ?></strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">New cover image</span>
                        <strong id="edit-preview-cover-value">Keeping current cover</strong>
                    </div>
                    <div class="upload-preview-field upload-preview-field--full">
                        <span class="upload-preview-label">New ZIP version</span>
                        <strong id="edit-preview-file-value">No new file selected</strong>
                    </div>
                    <div class="upload-preview-field upload-preview-field--full">
                        <span class="upload-preview-label">Change summary</span>
                        <strong id="edit-preview-change-summary-value"><?= esc(old('change_summary', '')) ?: 'No summary entered yet' ?></strong>
                    </div>
                </div>
            </section>
        </div>
        <div class="preview-actions">
            <button type="button" class="button secondary" data-preview-close>Back to edit</button>
            <button type="button" class="button" id="confirm-edit-submit">Confirm &amp; Save</button>
        </div>
    </article>
</div>

<script>
(() => {
    const form = document.getElementById('edit-dataset-form');
    const submitButton = document.getElementById('edit-dataset-submit');
    const previewModal = document.getElementById('edit-preview-modal');
    const confirmButton = document.getElementById('confirm-edit-submit');
    const fieldMap = [
        ['title', 'edit-preview-title-value'],
        ['category', 'edit-preview-category-value'],
        ['description', 'edit-preview-description-value'],
        ['data_type', 'edit-preview-data-type-value', true],
        ['content_formats', 'edit-preview-content-formats-value'],
        ['access_type', 'edit-preview-access-value', true],
        ['research_title', 'edit-preview-research-title-value'],
        ['project_head', 'edit-preview-project-head-value'],
        ['members', 'edit-preview-members-value'],
        ['source_type', 'edit-preview-source-type-value', true],
        ['source_link', 'edit-preview-source-link-value'],
        ['tags', 'edit-preview-tags-value'],
        ['change_summary', 'edit-preview-change-summary-value']
    ];
    let previewConfirmed = false;

    const textOrFallback = (value, fallback) => {
        const trimmed = (value ?? '').toString().trim();
        return trimmed ? trimmed : fallback;
    };

    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };

    const getSelectedLabel = (select) => {
        if (!select) return '';
        const option = select.options[select.selectedIndex];
        return option ? option.textContent.trim() : '';
    };

    const renderPreview = () => {
        fieldMap.forEach(([fieldId, previewId, isSelect]) => {
            const input = document.getElementById(fieldId);
            if (!input) return;
            const value = isSelect ? getSelectedLabel(input) : input.value;
            const fallback = fieldId === 'change_summary'
                ? 'No summary entered yet'
                : fieldId === 'members'
                    ? 'Not listed'
                    : fieldId === 'source_link'
                        ? 'Not provided'
                        : 'Not entered yet';
            setText(previewId, textOrFallback(value, fallback));
        });

        const coverInput = document.getElementById('cover_image');
        const coverValue = coverInput && coverInput.files && coverInput.files.length > 0
            ? coverInput.files[0].name
            : 'Keeping current cover';
        setText('edit-preview-cover-value', coverValue);

        const fileInput = document.getElementById('dataset_file');
        const fileValue = fileInput && fileInput.files && fileInput.files.length > 0
            ? fileInput.files[0].name
            : 'No new file selected';
        setText('edit-preview-file-value', fileValue);
    };

    const openPreview = () => {
        if (!previewModal) return;
        renderPreview();
        previewModal.hidden = false;
        document.documentElement.classList.add('preview-open');
        previewModal.querySelector('.preview-card')?.focus();
    };

    const closePreview = () => {
        if (!previewModal) return;
        previewModal.hidden = true;
        document.documentElement.classList.remove('preview-open');
        previewConfirmed = false;
    };

    if (form) {
        form.addEventListener('submit', (e) => {
            if (previewConfirmed) return;
            e.preventDefault();
            openPreview();
        });
    }

    if (submitButton) {
        submitButton.addEventListener('click', (e) => {
            e.preventDefault();
            openPreview();
        });
    }

    document.querySelectorAll('#edit-dataset-form input, #edit-dataset-form textarea, #edit-dataset-form select').forEach((el) => {
        el.addEventListener('input', renderPreview);
        el.addEventListener('change', renderPreview);
    });

    if (confirmButton) {
        confirmButton.addEventListener('click', (e) => {
            e.preventDefault();
            previewConfirmed = true;
            if (form && typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else if (form) {
                form.submit();
            }
        });
    }

    if (previewModal) {
        previewModal.querySelectorAll('[data-preview-close]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                closePreview();
            });
        });

        previewModal.addEventListener('click', (e) => {
            if (e.target === previewModal) {
                closePreview();
            }
        });
    }

    const input = document.getElementById('cover_image');
    const preview = document.getElementById('cover-preview');
    const clear = document.getElementById('cover-clear');
    if (!input || !preview || !clear) {
        renderPreview();
        return;
    }

    const currentCover = preview.src;
    let objectUrl = null;

    input.addEventListener('change', () => {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        if (input.files.length === 0) {
            preview.src = currentCover;
            clear.hidden = true;
            renderPreview();
            return;
        }

        objectUrl = URL.createObjectURL(input.files[0]);
        preview.src = objectUrl;
        clear.hidden = false;
        renderPreview();
    });

    clear.addEventListener('click', () => {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = null;
        input.value = '';
        preview.src = currentCover;
        clear.hidden = true;
        renderPreview();
    });

    renderPreview();
})();
</script>
<?= $this->endSection() ?>
