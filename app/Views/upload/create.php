<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<section class="hero-panel">
    <div class="shell">
        <p class="eyebrow">Dataset Lifecycle</p>
        <h1>Upload a Dataset</h1>
        <p class="lead">Submit metadata and a protected ZIP package. New submissions begin with technical verification before ethics review.</p>
    </div>
</section>

<section class="shell split-grid form-shell">
    <aside class="panel detail-sidebar">
        <p class="tag">Submission Checklist</p>
        <h2>Before Uploading</h2>
        <ul class="stack-list">
            <?php foreach (($requiredMetadata ?? []) as $metadata): ?>
                <li><?= esc($metadata) ?></li>
            <?php endforeach; ?>
            <li>Anonymization confirmation</li>
        </ul>
        <h3>What Happens Next?</h3>
        <p class="muted">Your dataset is created as Pending Review. The system distributes it to the least-loaded technical reviewer, then does the same for ethics review after technical approval. You will be notified at each stage.</p>
    </aside>

    <section class="form-card">
        <div class="panel-head">
            <div>
                <p class="tag">Submission Form</p>
                <h2>Dataset Metadata</h2>
                </div>
                <?php if (!empty($errors['anonymized'])): ?><span class="field-error" style="margin-top:8px;display:block"><?= esc($errors['anonymized']) ?></span><?php endif; ?>
            </div>

        <form method="post" action="<?= site_url('upload') ?>" enctype="multipart/form-data" id="upload-form" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="form" value="upload">

            <hr class="section-divider">

            <p class="tag">Dataset Info</p>
            <div>
                <label for="title">Dataset Title<span class="required-asterisk">*</span></label>
                <span class="help-text">A concise, descriptive name for your dataset</span>
                <input id="title" name="title" value="<?= old('title') ?>" placeholder="e.g., Startup Survey Responses" class="<?= !empty($errors['title']) ? 'field-error__input' : '' ?>">
                <?php if (!empty($errors['title'])): ?><span class="field-error"><?= esc($errors['title']) ?></span><?php endif; ?>
            </div>
            <div class="grid">
                <div>
                    <label for="category">Category<span class="required-asterisk">*</span></label>
                    <span class="help-text">The category or subject area of your dataset</span>
                    <input id="category" name="category" value="<?= old('category') ?>" placeholder="e.g., Startup Research" class="<?= !empty($errors['category']) ? 'field-error__input' : '' ?>">
                    <?php if (!empty($errors['category'])): ?><span class="field-error"><?= esc($errors['category']) ?></span><?php endif; ?>
                </div>
                <div>
                    <label for="data_type">Data Type<span class="required-asterisk">*</span></label>
                    <span class="help-text">The structural type that best describes your data</span>
                    <div class="dropdown-wrap<?= !empty($errors['data_type']) ? ' has-error' : '' ?>" data-field="data_type">
                        <button type="button" id="data_type" class="dropdown-trigger<?= !empty($errors['data_type']) ? ' field-error__input' : '' ?>" aria-haspopup="listbox">
                            <span class="dropdown-display">Select a data type</span>
                            <span class="dropdown-chevron"></span>
                        </button>
                        <div class="dropdown-menu" role="listbox">
                            <button type="button" class="dropdown-option dropdown-placeholder" role="option" disabled>Select a data type</button>
                            <?php foreach (($dataTypes ?? []) as $dataType): ?>
                                <button type="button" class="dropdown-option<?= old('data_type') === $dataType ? ' selected' : '' ?>" role="option" data-value="<?= esc($dataType) ?>"><?= esc($dataType) ?></button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="data_type" value="<?= old('data_type') ?>">
                    </div>
                    <?php if (!empty($errors['data_type'])): ?><span class="field-error"><?= esc($errors['data_type']) ?></span><?php endif; ?>
                </div>
                <div>
                    <label for="content_formats">Formats Inside ZIP<span class="required-asterisk">*</span></label>
                    <span class="help-text">Disclose the specific file types included in the protected ZIP package</span>
                    <input id="content_formats" name="content_formats" value="<?= old('content_formats') ?>" placeholder="e.g., .csv, .pdf, .png, .svg" class="<?= !empty($errors['content_formats']) ? 'field-error__input' : '' ?>">
                    <?php if (!empty($errors['content_formats'])): ?><span class="field-error"><?= esc($errors['content_formats']) ?></span><?php endif; ?>
                </div>
                <div>
                    <label for="access_type">Access Type<span class="required-asterisk">*</span></label>
                    <span class="help-text">The access level for this dataset once published</span>
                    <div class="dropdown-wrap<?= !empty($errors['access_type']) ? ' has-error' : '' ?>" data-field="access_type">
                        <button type="button" id="access_type" class="dropdown-trigger<?= !empty($errors['access_type']) ? ' field-error__input' : '' ?>" aria-haspopup="listbox">
                            <span class="dropdown-display">Select access type</span>
                            <span class="dropdown-chevron"></span>
                        </button>
                        <div class="dropdown-menu" role="listbox">
                            <button type="button" class="dropdown-option dropdown-placeholder" role="option" disabled>Select access type</button>
                            <?php foreach (($accessTypes ?? []) as $value => $label): ?>
                                <button type="button" class="dropdown-option<?= old('access_type') === $value ? ' selected' : '' ?>" role="option" data-value="<?= esc($value) ?>"><?= esc($label) ?></button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="access_type" value="<?= old('access_type') ?>">
                    </div>
                    <?php if (!empty($errors['access_type'])): ?><span class="field-error"><?= esc($errors['access_type']) ?></span><?php endif; ?>
                </div>
            </div>

            <label for="description">Description<span class="required-asterisk">*</span></label>
            <span class="help-text">Describe what the dataset contains, how it was collected, and how it may be used</span>
            <textarea id="description" name="description" rows="4" placeholder="e.g., Survey responses from incubatees covering startup needs, challenges, and resource gaps." class="<?= !empty($errors['description']) ? 'field-error__input' : '' ?>"><?= old('description') ?></textarea>
            <?php if (!empty($errors['description'])): ?><span class="field-error"><?= esc($errors['description']) ?></span><?php endif; ?>

            <label for="cover_image">Dataset Cover <span class="label-optional">(OPTIONAL)</span></label>
            <span class="help-text">Add a square or landscape JPG, PNG, or WebP image. Maximum 4 MB.</span>
            <div class="cover-upload-control<?= ! empty($errors['cover_image']) ? ' cover-upload-control--error' : '' ?>">
                <img
                    class="cover-upload-preview"
                    id="cover-preview"
                    src="<?= base_url('assets/img/placeholders/dataset-placeholder-img.png') ?>"
                    alt="Dataset cover preview"
                >
                <div class="cover-upload-copy">
                    <strong>Catalog cover image</strong>
                    <p class="muted">This image appears beside the dataset entry. The repository placeholder is used when no cover is selected.</p>
                    <input id="cover_image" type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <button class="button secondary cover-clear-button" id="cover-clear" type="button" hidden>Use placeholder</button>
                </div>
            </div>
            <?php if (! empty($errors['cover_image'])): ?>
                <span class="field-error"><?= esc($errors['cover_image']) ?></span>
            <?php endif; ?>

            <label for="tags">Tags<span class="required-asterisk">*</span></label>
            <span class="help-text">Comma-separated keywords for discoverability</span>
            <input id="tags" name="tags" value="<?= old('tags') ?>" placeholder="e.g., startup, survey, tabular" class="<?= !empty($errors['tags']) ? 'field-error__input' : '' ?>">
            <?php if (!empty($errors['tags'])): ?><span class="field-error"><?= esc($errors['tags']) ?></span><?php endif; ?>

            <hr class="section-divider">

            <p class="tag form-section-label">Research Info</p>
            <div>
                <label for="research_title">Research Title<span class="required-asterisk">*</span></label>
                <span class="help-text">The official title of the research, thesis, or capstone project</span>
                <input id="research_title" name="research_title" value="<?= old('research_title') ?>" placeholder="e.g., Startup Ecosystem Analysis" class="<?= !empty($errors['research_title']) ? 'field-error__input' : '' ?>">
                <?php if (!empty($errors['research_title'])): ?><span class="field-error"><?= esc($errors['research_title']) ?></span><?php endif; ?>
            </div>
            <div>
                <label for="project_head">Project Head or Adviser<span class="required-asterisk">*</span></label>
                <span class="help-text">Name of the project lead, supervising researcher, or faculty adviser</span>
                <input id="project_head" name="project_head" value="<?= old('project_head') ?>" placeholder="e.g., Dr. Maria Santos" class="<?= !empty($errors['project_head']) ? 'field-error__input' : '' ?>">
                <?php if (!empty($errors['project_head'])): ?><span class="field-error"><?= esc($errors['project_head']) ?></span><?php endif; ?>
            </div>

            <label for="members">Research Members or Contributors</label>
            <span class="help-text">List all student researchers, faculty collaborators, incubatees, or project contributors</span>
            <textarea id="members" name="members" rows="3" placeholder="e.g., Juan Dela Cruz, Dr. Maria Santos"><?= old('members') ?></textarea>

            <hr class="section-divider">

            <p class="tag form-section-label">Source and File</p>
            <div class="grid">
                <div style="position:relative">
                    <div class="label-row">
                        <label for="source_type">Source Type<span class="required-asterisk">*</span></label>
                        <div class="info-wrap">
                            <span class="info-trigger" id="source-type-info" tabindex="0" role="button" aria-label="Learn more about source types"><span class="info-icon">i</span></span>
                            <div class="info-popup" id="source-type-popup" hidden>
                                <div class="info-popup__header">
                                    <h4 class="info-popup__title">SOURCE TYPE CLASSIFICATION</h4>
                                    <button type="button" class="info-popup__close" aria-label="Close">âœ•</button>
                                </div>
                                <p><strong>Primary</strong> â€” Original data collected by your team. Choose this if you conducted surveys, interviews, experiments, or gathered data firsthand.</p>
                                <p><strong>Secondary</strong> â€” Data obtained from existing sources. Choose this if the data comes from published studies, government databases, or third-party repositories.</p>
                            </div>
                        </div>
                    </div>
                    <span class="help-text">Select the source type for your data</span>
                    <div class="dropdown-wrap<?= !empty($errors['source_type']) ? ' has-error' : '' ?>" data-field="source_type">
                        <button type="button" id="source_type" class="dropdown-trigger<?= !empty($errors['source_type']) ? ' field-error__input' : '' ?>" aria-haspopup="listbox">
                            <span class="dropdown-display">Select source type</span>
                            <span class="dropdown-chevron"></span>
                        </button>
                        <div class="dropdown-menu" role="listbox">
                            <button type="button" class="dropdown-option dropdown-placeholder" role="option" disabled>Select source type</button>
                            <?php foreach (($sourceTypes ?? []) as $sourceType): ?>
                                <button type="button" class="dropdown-option<?= old('source_type') === $sourceType ? ' selected' : '' ?>" role="option" data-value="<?= esc($sourceType) ?>"><?= esc($sourceType) ?></button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="source_type" value="<?= old('source_type') ?>">
                    </div>
                    <?php if (!empty($errors['source_type'])): ?><span class="field-error"><?= esc($errors['source_type']) ?></span><?php endif; ?>
                </div>
                <div>
                    <label for="source_link">Source Link <span class="label-optional">(OPTIONAL)</span></label>
                    <span class="help-text">A URL to the original data source or related publication</span>
                    <input id="source_link" name="source_link" value="<?= old('source_link') ?>" placeholder="https://">
                </div>
            </div>

            <label for="dataset_file">Dataset ZIP File<span class="required-asterisk">*</span></label>
            <span class="help-text">Upload a single ZIP file containing your dataset and any supporting documentation</span>

            <div class="file-zone<?= !empty($errors['dataset_file']) ? ' file-zone--error' : '' ?>" id="file-zone">
                <input type="file" id="dataset_file" name="dataset_file" accept=".zip" hidden>
                <div class="file-zone__placeholder" id="file-placeholder">
                    <strong>Upload protected ZIP package</strong>

                    <button type="button" class="button" id="file-trigger">Select ZIP File</button>
                </div>
                <div class="file-zone__preview" id="file-preview" hidden>
                    <span class="file-zone__icon">
                        <i class="fa-solid fa-file-zipper" style="font-size: 24px; color: var(--navy);"></i>
                    </span>
                    <span class="file-zone__name" id="file-name"></span>
                    <span class="file-zone__size muted" id="file-size"></span>
                    <button type="button" class="button secondary" id="file-clear" title="Remove selected file">Remove</button>
                </div>
            </div>
            <?php if (!empty($errors['dataset_file'])): ?>
                <span class="field-error"><?= esc($errors['dataset_file']) ?></span>
            <?php endif; ?>

            <div class="anonymization-card<?= !empty($errors['anonymized']) ? ' has-error' : '' ?>">
                <p class="tag">Ethics Requirement<span class="required-asterisk">*</span></p>
                <div class="checkbox-row">
                    <input type="checkbox" id="anonymized" name="anonymized" value="1" <?= old('anonymized') ? 'checked' : '' ?>>
                    <span class="check-box" id="check-visual"></span>
                    <label for="anonymized" class="check-label">I confirm that all sensitive or personal data has been anonymized before submission.</label>
                </div>
            </div>
            <?php if (!empty($errors['anonymized'])): ?><span class="field-error"><?= esc($errors['anonymized']) ?></span><?php endif; ?>

            <div class="actions" style="margin-top:28px">
                <button class="button" type="submit" id="upload-submit-button">Review Before Upload</button>
                <a class="button secondary" href="<?= site_url('dashboard') ?>">Cancel</a>
            </div>
        </form>
    </section>
</section>

<div class="preview-modal upload-preview-modal" id="upload-preview-modal" role="dialog" aria-modal="true" aria-labelledby="upload-preview-modal-title" aria-describedby="upload-preview-modal-summary" hidden>
    <div class="preview-backdrop" data-preview-close></div>
    <article class="preview-card upload-preview-card" tabindex="-1">
        <button class="preview-close" type="button" aria-label="Close preview" data-preview-close>&times;</button>
        <div class="preview-title-row">
            <div>
                <p class="tag">Confirmation Preview</p>
                <h2 id="upload-preview-modal-title" tabindex="-1" data-preview-initial>Review your submission</h2>
                <p class="preview-kicker" id="upload-preview-modal-summary">Check the details below. Your dataset will only be uploaded after you confirm this preview.</p>
                <div class="row-badge-line preview-pill-line" aria-label="Preview state">
                    <span class="row-pill tech-type">Draft</span>
                    <span class="row-pill tech-outline">Not uploaded yet</span>
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
                        <strong id="upload-preview-modal-title-value">Not entered yet</strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Category</span>
                        <strong id="upload-preview-modal-category-value">Not entered yet</strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Access type</span>
                        <strong id="upload-preview-modal-access-value">Not selected</strong>
                    </div>
                    <div class="upload-preview-field upload-preview-field--full">
                        <span class="upload-preview-label">Description</span>
                        <strong id="upload-preview-modal-description-value">Not entered yet</strong>
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
                        <strong id="upload-preview-modal-research-title-value">Not entered yet</strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Project head</span>
                        <strong id="upload-preview-modal-project-head-value">Not entered yet</strong>
                    </div>
                    <div class="upload-preview-field upload-preview-field--full">
                        <span class="upload-preview-label">Members</span>
                        <strong id="upload-preview-modal-members-value">Not listed</strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Anonymized</span>
                        <strong id="upload-preview-modal-anonymized-value">Not confirmed</strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Data type</span>
                        <strong id="upload-preview-modal-data-type-value">Not selected</strong>
                    </div>
                </div>
            </section>

            <section class="upload-preview-section">
                <div class="upload-preview-section-head">
                    <span class="upload-preview-accent"></span>
                    <h3>Source and file</h3>
                </div>
                <div class="upload-preview-grid upload-preview-grid--two">
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Source type</span>
                        <strong id="upload-preview-modal-source-type-value">Not selected</strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Source link</span>
                        <strong id="upload-preview-modal-source-link-value">Not provided</strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Formats inside ZIP</span>
                        <strong id="upload-preview-modal-content-formats-value">Not entered yet</strong>
                    </div>
                    <div class="upload-preview-field">
                        <span class="upload-preview-label">Tags</span>
                        <strong id="upload-preview-modal-tags-value">Not entered yet</strong>
                    </div>
                    <div class="upload-preview-field upload-preview-field--full">
                        <span class="upload-preview-label">ZIP file</span>
                        <strong id="upload-preview-modal-file-value">No file selected</strong>
                    </div>
                </div>
            </section>
        </div>
        <div class="preview-actions">
            <button type="button" class="button secondary" data-preview-close>Back to edit</button>
            <button type="button" class="button" id="confirm-upload-submit">Confirm &amp; Upload</button>
        </div>
    </article>
</div>

<script>
(function() {
    var fieldMessages = {
        'title':          'Please enter the dataset title.',
        'category':       'Please enter a category.',
        'content_formats': 'Please disclose the file formats inside the ZIP package.',
        'description':    'Please describe what the dataset contains.',
        'tags':           'Please enter at least one tag.',
        'research_title': 'Please enter the research title.',
        'project_head':   'Please enter the project head or adviser.'
    };
    var dropdownMessages = {
        'data_type':    'Please select a data type.',
        'access_type':  'Please select an access type.',
        'source_type':  'Please select a source type.'
    };
    var previewConfirmed = false;

    document.querySelectorAll('label[for]').forEach(function(lbl) {
        lbl.addEventListener('click', function(e) { e.preventDefault(); });
    });

    function getInputValue(id, fallback) {
        var el = document.getElementById(id);
        if (!el) return fallback;
        var value = el.value ? el.value.trim() : '';
        return value || fallback;
    }

    function getDropdownValue(field, fallback) {
        var wrap = document.querySelector('.dropdown-wrap[data-field="' + field + '"]');
        if (!wrap) return fallback;
        var display = wrap.querySelector('.dropdown-display');
        var value = display && display.textContent ? display.textContent.trim() : '';
        return value && !/^select /i.test(value) ? value : fallback;
    }

    function getFileSummary() {
        var input = document.getElementById('dataset_file');
        if (!input || !input.files || input.files.length === 0) {
            return {
                name: 'No file selected',
                size: ''
            };
        }

        var file = input.files[0];
        return {
            name: file.name || 'Selected file',
            size: file.size ? '(' + (file.size / 1024 / 1024).toFixed(1) + ' MB)' : ''
        };
    }

    function setPreviewText(id, value) {
        var el = document.getElementById(id);
        if (el) {
            el.textContent = value;
        }
    }

    function renderUploadPreview() {
        var title = getInputValue('title', 'Not entered yet');
        var category = getInputValue('category', 'Not entered yet');
        var description = getInputValue('description', 'Not entered yet');
        var dataType = getDropdownValue('data_type', 'Not selected');
        var accessType = getDropdownValue('access_type', 'Not selected');
        var researchTitle = getInputValue('research_title', 'Not entered yet');
        var projectHead = getInputValue('project_head', 'Not entered yet');
        var members = getInputValue('members', 'Not listed');
        var sourceType = getDropdownValue('source_type', 'Not selected');
        var sourceLink = getInputValue('source_link', 'Not provided');
        var contentFormats = getInputValue('content_formats', 'Not entered yet');
        var tags = getInputValue('tags', 'Not entered yet');
        var anonymized = document.getElementById('anonymized');
        var file = getFileSummary();

        setPreviewText('upload-preview-title-value', title);
        setPreviewText('upload-preview-category-value', category);
        setPreviewText('upload-preview-data-type-value', dataType);
        setPreviewText('upload-preview-access-value', accessType);
        setPreviewText('upload-preview-research-title-value', researchTitle);
        setPreviewText('upload-preview-project-head-value', projectHead);
        setPreviewText('upload-preview-source-type-value', sourceType);
        setPreviewText('upload-preview-tags-value', tags);
        setPreviewText('upload-preview-file-value', file.name + (file.size ? ' ' + file.size : ''));

        setPreviewText('upload-preview-modal-title-value', title);
        setPreviewText('upload-preview-modal-category-value', category);
        setPreviewText('upload-preview-modal-description-value', description);
        setPreviewText('upload-preview-modal-data-type-value', dataType);
        setPreviewText('upload-preview-modal-access-value', accessType);
        setPreviewText('upload-preview-modal-anonymized-value', anonymized && anonymized.checked ? 'Confirmed' : 'Not confirmed');
        setPreviewText('upload-preview-modal-research-title-value', researchTitle);
        setPreviewText('upload-preview-modal-project-head-value', projectHead);
        setPreviewText('upload-preview-modal-members-value', members);
        setPreviewText('upload-preview-modal-source-type-value', sourceType);
        setPreviewText('upload-preview-modal-source-link-value', sourceLink);
        setPreviewText('upload-preview-modal-content-formats-value', contentFormats);
        setPreviewText('upload-preview-modal-tags-value', tags);
        setPreviewText('upload-preview-modal-file-value', file.name + (file.size ? ' ' + file.size : ''));
    }

    function openUploadPreview() {
        var modal = document.getElementById('upload-preview-modal');
        var card = modal ? modal.querySelector('.preview-card') : null;
        if (!modal || !card) return;

        renderUploadPreview();
        modal.hidden = false;
        document.documentElement.classList.add('preview-open');
        card.focus();
    }

    function closeUploadPreview() {
        var modal = document.getElementById('upload-preview-modal');
        if (!modal) return;

        modal.hidden = true;
        document.documentElement.classList.remove('preview-open');
        previewConfirmed = false;
    }

    function ensureErrorSpan(el) {
        var sib = el.nextElementSibling;
        while (sib && !sib.classList.contains('field-error')) sib = sib.nextElementSibling;
        if (sib) return sib;
        sib = document.createElement('span');
        sib.className = 'field-error';
        sib.textContent = fieldMessages[el.id] || 'This field is required.';
        el.parentNode.insertBefore(sib, el.nextSibling);
        return sib;
    }

    function markInvalid(el) {
        el.classList.add('field-error__input');
        ensureErrorSpan(el).hidden = false;
    }

    function clearInvalid(el) {
        el.classList.remove('field-error__input');
        var sib = el.nextElementSibling;
        while (sib && !sib.classList.contains('field-error')) sib = sib.nextElementSibling;
        if (sib) sib.hidden = true;
    }

    function markDropdownInvalid(wrap) {
        wrap.classList.add('has-error');
        var t = wrap.querySelector('.dropdown-trigger');
        if (t) t.classList.add('field-error__input');
        var sib = wrap.nextElementSibling;
        while (sib && !sib.classList.contains('field-error')) sib = sib.nextElementSibling;
        if (!sib) {
            sib = document.createElement('span');
            sib.className = 'field-error';
            sib.textContent = dropdownMessages[wrap.getAttribute('data-field')] || 'This field is required.';
            wrap.parentNode.insertBefore(sib, wrap.nextSibling);
        }
        sib.hidden = false;
    }

    function clearDropdownInvalid(wrap) {
        wrap.classList.remove('has-error');
        var t = wrap.querySelector('.dropdown-trigger');
        if (t) t.classList.remove('field-error__input');
        var sib = wrap.nextElementSibling;
        while (sib && !sib.classList.contains('field-error')) sib = sib.nextElementSibling;
        if (sib) sib.hidden = true;
    }

    function markEthicsInvalid() {
        var card = document.querySelector('.anonymization-card');
        if (!card) return;
        card.classList.add('has-error');
        var sib = card.nextElementSibling;
        while (sib && !sib.classList.contains('field-error')) sib = sib.nextElementSibling;
        if (!sib) {
            sib = document.createElement('span');
            sib.className = 'field-error';
            sib.textContent = 'You must confirm anonymization before submitting.';
            card.parentNode.insertBefore(sib, card.nextSibling);
        }
        sib.hidden = false;
    }

    function clearEthicsInvalid() {
        var card = document.querySelector('.anonymization-card');
        if (!card) return;
        card.classList.remove('has-error');
        var sib = card.nextElementSibling;
        while (sib && !sib.classList.contains('field-error')) sib = sib.nextElementSibling;
        if (sib) sib.hidden = true;
    }

    function markFileInvalid() {
        var fz = document.getElementById('file-zone');
        if (!fz) return;
        fz.classList.add('file-zone--error');
        var sib = fz.nextElementSibling;
        if (sib && sib.classList.contains('field-error')) {
            sib.hidden = false;
            return;
        }
        sib = document.createElement('span');
        sib.className = 'field-error';
        sib.textContent = 'Please select a ZIP file to upload.';
        fz.parentNode.insertBefore(sib, fz.nextSibling);
        sib.hidden = false;
    }

    function clearFileInvalid() {
        var fz = document.getElementById('file-zone');
        if (!fz) return;
        fz.classList.remove('file-zone--error');
        var sib = fz.nextElementSibling;
        if (sib && sib.classList.contains('field-error')) sib.hidden = true;
    }

    var formEl = document.getElementById('upload-form');
    if (formEl) {
        var requiredFields = ['title','category','content_formats','description','tags','research_title','project_head'];

        formEl.addEventListener('submit', function(e) {
            var firstInvalid = null;
            var ok = true;

            requiredFields.forEach(function(id) {
                var el = document.getElementById(id);
                if (el && !el.value.trim()) {
                    markInvalid(el);
                    if (!firstInvalid) firstInvalid = el;
                    ok = false;
                } else if (el) {
                    clearInvalid(el);
                }
            });

            var anonymized = document.getElementById('anonymized');
            if (anonymized && !anonymized.checked) {
                markEthicsInvalid();
                if (!firstInvalid) firstInvalid = document.getElementById('check-visual');
                ok = false;
            } else if (anonymized) {
                clearEthicsInvalid();
            }

            document.querySelectorAll('.dropdown-wrap[data-field]').forEach(function(wrap) {
                var h = wrap.querySelector('input[type="hidden"]');
                if (h && !h.value) {
                    markDropdownInvalid(wrap);
                    if (!firstInvalid) firstInvalid = wrap;
                    ok = false;
                } else {
                    clearDropdownInvalid(wrap);
                }
            });

            var fInput = document.getElementById('dataset_file');
            if (fInput && fInput.files.length === 0) {
                markFileInvalid();
                if (!firstInvalid) firstInvalid = fInput;
                ok = false;
            } else if (fInput) {
                clearFileInvalid();
            }

            if (!ok) {
                e.preventDefault();
                previewConfirmed = false;
                if (firstInvalid) {
                    if (typeof firstInvalid.focus === 'function') setTimeout(function() { firstInvalid.focus(); }, 50);
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            if (!previewConfirmed) {
                e.preventDefault();
                openUploadPreview();
            }
        });

        requiredFields.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() { clearInvalid(el); });
                el.addEventListener('change', function() { clearInvalid(el); });
            }
        });

        document.querySelectorAll('.dropdown-wrap[data-field] .dropdown-option').forEach(function(opt) {
            opt.addEventListener('click', function() { clearDropdownInvalid(opt.closest('.dropdown-wrap')); });
        });

        var fInput = document.getElementById('dataset_file');
        if (fInput) {
            fInput.addEventListener('change', function() {
                if (fInput.files.length > 0) clearFileInvalid();
                renderUploadPreview();
            });
        }

        var sourceLink = document.getElementById('source_link');
        if (sourceLink) {
            sourceLink.addEventListener('blur', function() {
                var val = this.value.trim();
                if (val && !/^https:\/\/.+\..+/i.test(val)) {
                    this.classList.add('field-error__input');
                    var sib = this.nextElementSibling;
                    while (sib && !sib.classList.contains('field-error')) sib = sib.nextElementSibling;
                    if (!sib) {
                        sib = document.createElement('span');
                        sib.className = 'field-error';
                        sib.textContent = 'Please enter a valid URL starting with https://';
                        this.parentNode.insertBefore(sib, this.nextSibling);
                    }
                    sib.hidden = false;
                } else {
                    this.classList.remove('field-error__input');
                    var sib = this.nextElementSibling;
                    while (sib && !sib.classList.contains('field-error')) sib = sib.nextElementSibling;
                    if (sib) sib.hidden = true;
                }
            });
        }

        document.querySelectorAll('input, textarea').forEach(function(el) {
            el.addEventListener('input', renderUploadPreview);
            el.addEventListener('change', renderUploadPreview);
        });

    }

    var checkVisual = document.getElementById('check-visual');
    var anonymized = document.getElementById('anonymized');
    if (checkVisual && anonymized) {
        checkVisual.addEventListener('click', function() {
            anonymized.checked = !anonymized.checked;
            if (anonymized.checked) clearEthicsInvalid();
        });
    }

    var fileInput = document.getElementById('dataset_file');
    var fileTrigger = document.getElementById('file-trigger');
    var fileZone = document.getElementById('file-zone');
    var filePreview = document.getElementById('file-preview');
    var filePlaceholder = document.getElementById('file-placeholder');
    var fileName = document.getElementById('file-name');
    var fileSize = document.getElementById('file-size');
    var fileClear = document.getElementById('file-clear');
    var coverInput = document.getElementById('cover_image');
    var coverPreview = document.getElementById('cover-preview');
    var coverClear = document.getElementById('cover-clear');
    var coverPlaceholder = '<?= esc(base_url('assets/img/placeholders/dataset-placeholder-img.png'), 'js') ?>';
    var coverObjectUrl = null;

    if (coverInput && coverPreview) {
        coverInput.addEventListener('change', function() {
            if (coverObjectUrl) URL.revokeObjectURL(coverObjectUrl);
            if (coverInput.files.length === 0) {
                coverPreview.src = coverPlaceholder;
                if (coverClear) coverClear.hidden = true;
                return;
            }

            coverObjectUrl = URL.createObjectURL(coverInput.files[0]);
            coverPreview.src = coverObjectUrl;
            if (coverClear) coverClear.hidden = false;
        });
    }

    if (coverClear && coverInput && coverPreview) {
        coverClear.addEventListener('click', function() {
            if (coverObjectUrl) URL.revokeObjectURL(coverObjectUrl);
            coverObjectUrl = null;
            coverInput.value = '';
            coverPreview.src = coverPlaceholder;
            coverClear.hidden = true;
        });
    }

    if (!fileInput || !fileTrigger) return;

    fileTrigger.addEventListener('click', function(e) {
        e.preventDefault();
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        if (fileInput.files.length === 0) return;
        var file = fileInput.files[0];
        clearFileInvalid();
        fileName.textContent = file.name;
        fileSize.textContent = '(' + (file.size / 1024 / 1024).toFixed(1) + ' MB)';
        filePlaceholder.hidden = true;
        filePreview.hidden = false;
        fileZone.classList.add('file-zone--has-file');
        renderUploadPreview();
    });

    if (fileClear) {
        fileClear.addEventListener('click', function(e) {
            e.preventDefault();
            fileInput.value = '';
            filePlaceholder.hidden = false;
            filePreview.hidden = true;
            fileZone.classList.remove('file-zone--has-file');
            clearFileInvalid();
            renderUploadPreview();
        });
    }

    fileZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileZone.classList.add('file-zone--dragover');
    });

    fileZone.addEventListener('dragleave', function() {
        fileZone.classList.remove('file-zone--dragover');
    });

    fileZone.addEventListener('drop', function(e) {
        e.preventDefault();
        fileZone.classList.remove('file-zone--dragover');
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            if (fileInput.files[0]) {
                var event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        }
    });

    document.querySelectorAll('input, textarea, select').forEach(function(el) {
        el.addEventListener('mouseenter', function() { this.classList.add('input-hovered'); });
        el.addEventListener('mouseleave', function() { this.classList.remove('input-hovered'); });
    });

    document.querySelectorAll('.dropdown-wrap').forEach(function(wrap) {
        wrap.addEventListener('mouseenter', function() { this.classList.add('input-hovered'); });
        wrap.addEventListener('mouseleave', function() { this.classList.remove('input-hovered'); });
    });

    function closeAllDropdowns(except) {
        document.querySelectorAll('.dropdown-wrap.is-open').forEach(function(w) {
            if (w !== except) closeDropdown(w);
        });
    }

    function openDropdown(wrap) {
        wrap.classList.add('is-open');
        wrap.querySelector('.dropdown-menu').classList.add('is-open');
    }

    function closeDropdown(wrap) {
        wrap.querySelector('.dropdown-menu').classList.remove('is-open');
        wrap.classList.remove('is-open');
    }

    document.addEventListener('click', function(e) {
        var trigger = e.target.closest('.dropdown-trigger');
        if (trigger) {
            var wrap = trigger.closest('.dropdown-wrap');
            if (wrap.classList.contains('is-open')) {
                closeDropdown(wrap);
            } else {
                closeAllDropdowns(wrap);
                openDropdown(wrap);
            }
            return;
        }

        var option = e.target.closest('.dropdown-option:not([disabled])');
        if (option) {
            var wrap = option.closest('.dropdown-wrap');
            var input = wrap.querySelector('input[type="hidden"]');
            var display = wrap.querySelector('.dropdown-display');
            input.value = option.getAttribute('data-value');
            display.textContent = option.textContent;
            wrap.querySelectorAll('.dropdown-option').forEach(function(o) {
                o.classList.remove('selected');
            });
            option.classList.add('selected');
            closeDropdown(wrap);
            renderUploadPreview();
            return;
        }

        if (!e.target.closest('.dropdown-wrap')) {
            closeAllDropdowns();
        }
    });

    document.querySelectorAll('.dropdown-wrap').forEach(function(wrap) {
        var input = wrap.querySelector('input[type="hidden"]');
        var display = wrap.querySelector('.dropdown-display');
        if (input.value) {
            wrap.querySelectorAll('.dropdown-option').forEach(function(opt) {
                if (opt.getAttribute('data-value') === input.value) {
                    opt.classList.add('selected');
                    display.textContent = opt.textContent;
                }
            });
        }
    });

    var openUploadPreviewButton = document.getElementById('open-upload-preview');
    var confirmUploadButton = document.getElementById('confirm-upload-submit');
    var uploadPreviewModal = document.getElementById('upload-preview-modal');

    if (openUploadPreviewButton) {
        openUploadPreviewButton.addEventListener('click', function(e) {
            e.preventDefault();
            openUploadPreview();
        });
    }

    if (confirmUploadButton) {
        confirmUploadButton.addEventListener('click', function(e) {
            e.preventDefault();
            previewConfirmed = true;
            if (formEl && typeof formEl.requestSubmit === 'function') {
                formEl.requestSubmit();
            } else if (formEl) {
                formEl.submit();
            }
        });
    }

    if (uploadPreviewModal) {
        uploadPreviewModal.querySelectorAll('[data-preview-close]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                closeUploadPreview();
            });
        });

        uploadPreviewModal.addEventListener('click', function(e) {
            if (e.target === uploadPreviewModal) {
                closeUploadPreview();
            }
        });
    }

    var infoTrigger = document.getElementById('source-type-info');
    var infoPopup = document.getElementById('source-type-popup');
    if (infoTrigger && infoPopup) {
        infoTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            var hidden = infoPopup.hasAttribute('hidden');
            document.querySelectorAll('.info-popup').forEach(function(p) { p.hidden = true; });
            document.querySelectorAll('.info-trigger--active').forEach(function(t) { t.classList.remove('info-trigger--active'); });
            infoPopup.hidden = !hidden;
            if (!hidden) {
                infoTrigger.classList.remove('info-trigger--active');
            } else {
                infoTrigger.classList.add('info-trigger--active');
            }
        });
        infoPopup.querySelector('.info-popup__close').addEventListener('click', function() {
            infoPopup.hidden = true;
            infoTrigger.classList.remove('info-trigger--active');
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.info-trigger') && !e.target.closest('.info-popup')) {
                infoPopup.hidden = true;
                infoTrigger.classList.remove('info-trigger--active');
            }
        });
    }

    renderUploadPreview();
})();
</script>
<?= $this->endSection() ?>
