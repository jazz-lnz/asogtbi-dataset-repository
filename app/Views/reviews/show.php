<?= $this->extend('layouts/portal') ?>

<?= $this->section('content') ?>
<?php
    $isAssigned = ($review['status'] ?? '') === \App\Models\ReviewModel::STATUS_ASSIGNED;
    $stageLabel = $stage === 'technical' ? 'Technical verification' : 'Research ethics review';
    $commentsValue = old('comments', $review['comments'] ?? '');
?>

<header class="review-workspace-header">
    <div>
        <a class="review-back-link" href="<?= site_url('review/' . $stage) ?>"><span class="material-symbols-rounded" aria-hidden="true">arrow_back</span> Back to queue</a>
        <p class="eyebrow"><?= esc($stageLabel) ?> · Round <?= esc((string) $review['review_round']) ?></p>
        <h1><?= esc($dataset['title']) ?></h1>
        <div class="review-header-meta">
            <span><?= esc($dataset['contributor_name']) ?></span>
            <span>v<?= esc($dataset['version'] ?? '1.0') ?></span>
            <span><?= esc($ageLabel) ?></span>
            <span class="status-pill status-<?= esc($dataset['status']) ?>"><?= esc(\App\Models\DatasetModel::statusLabel($dataset['status'])) ?></span>
        </div>
    </div>
    <?php if (! empty($review['draft_saved_at'])): ?>
        <span class="review-saved-state"><span class="material-symbols-rounded" aria-hidden="true">cloud_done</span> Draft saved <?= esc(date('M d, g:i A', strtotime($review['draft_saved_at']))) ?></span>
    <?php endif; ?>
</header>

<div class="review-workspace-grid">
    <main class="review-evidence-stack">
        <section class="panel evidence-overview-card">
            <img src="<?= esc(dataset_cover_url($dataset), 'attr') ?>" alt="Cover for <?= esc($dataset['title'], 'attr') ?>">
            <div>
                <p class="tag">Submission summary</p>
                <h2>Evidence overview</h2>
                <p><?= esc($dataset['description'] ?: 'No description provided.') ?></p>
                <div class="evidence-chip-row">
                    <span><?= esc($dataset['data_type']) ?></span>
                    <span><?= esc($dataset['content_formats'] ?: 'Contents not disclosed') ?></span>
                    <span><?= esc($dataset['file_format'] ?: 'ZIP') ?> package</span>
                    <span><?= esc($dataset['category']) ?></span>
                    <span><?= esc(\App\Models\DatasetModel::accessLabel($dataset['access_type'])) ?> access</span>
                </div>
            </div>
        </section>

        <?php if ($stage === 'technical'): ?>
            <section class="panel evidence-section">
                <div class="evidence-section-heading">
                    <div><p class="tag">Protected package</p><h2>File inspection</h2></div>
                    <?php if ($latestFile): ?><a class="button" href="<?= site_url('review/' . $stage . '/' . $review['id'] . '/download') ?>"><span class="material-symbols-rounded" aria-hidden="true">download</span> Download ZIP</a><?php endif; ?>
                </div>
                <?php if ($latestFile): ?>
                    <dl class="evidence-facts">
                        <div><dt>File name</dt><dd><?= esc($latestFile['original_name']) ?></dd></div>
                        <div><dt>File size</dt><dd><?= esc(number_format((int) $latestFile['file_size'])) ?> bytes</dd></div>
                        <div><dt>Detected type</dt><dd><?= esc($latestFile['file_type']) ?></dd></div>
                        <div><dt>Uploaded</dt><dd><?= ! empty($latestFile['created_at']) ? esc(date('M d, Y g:i A', strtotime($latestFile['created_at']))) : 'Not recorded' ?></dd></div>
                    </dl>
                    <p class="evidence-notice"><span class="material-symbols-rounded" aria-hidden="true">info</span> Automated archive diagnostics are deferred. Download and manually verify readability, contents, documentation, and declared formats.</p>
                <?php else: ?>
                    <p class="field-error">No protected file is available for this review.</p>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <section class="panel evidence-section">
                <div class="evidence-section-heading"><div><p class="tag">Ethics context</p><h2>Clearance and access evidence</h2></div></div>
                <dl class="evidence-facts">
                    <div><dt>Anonymization declared</dt><dd><?= ! empty($dataset['anonymized']) ? 'Yes' : 'No' ?></dd></div>
                    <div><dt>Requested access</dt><dd><?= esc(\App\Models\DatasetModel::accessLabel($dataset['access_type'])) ?></dd></div>
                    <div><dt>Source type</dt><dd><?= esc($dataset['source_type'] ?: 'Not set') ?></dd></div>
                    <div><dt>Source link</dt><dd><?= ! empty($dataset['source_link']) ? esc($dataset['source_link']) : 'Not provided' ?></dd></div>
                </dl>
                <?php if ($previousTechnical): ?>
                    <aside class="prior-approval-card">
                        <span class="material-symbols-rounded" aria-hidden="true">verified</span>
                        <div><strong>Technical review approved</strong><p><?= esc($previousTechnical['reviewer_name'] ?? 'Technical reviewer') ?> approved round <?= esc((string) $previousTechnical['review_round']) ?><?= ! empty($previousTechnical['decided_at']) ? ' on ' . esc(date('M d, Y', strtotime($previousTechnical['decided_at']))) : '' ?>.</p></div>
                    </aside>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <section class="panel evidence-section">
            <div class="evidence-section-heading"><div><p class="tag">Repository metadata</p><h2>Research and source details</h2></div></div>
            <dl class="evidence-facts evidence-facts--single">
                <div><dt>Research title</dt><dd><?= esc($dataset['research_title'] ?: 'Not set') ?></dd></div>
                <div><dt>Project head</dt><dd><?= esc($dataset['project_head'] ?: 'Not set') ?></dd></div>
                <div><dt>Authors</dt><dd><?= esc($dataset['members'] ?: 'Not listed') ?></dd></div>
                <div><dt>Source</dt><dd><?= esc($dataset['source_type'] ?: 'Not set') ?><?= ! empty($dataset['source_link']) ? ' · ' . esc($dataset['source_link']) : '' ?></dd></div>
                <div><dt>Tags</dt><dd><?= esc($dataset['tags'] ?: 'No tags') ?></dd></div>
            </dl>
        </section>

        <details class="panel evidence-disclosure">
            <summary><span><small>Version history</small><strong><?= count($versions) ?> recorded version(s)</strong></span><span class="material-symbols-rounded" aria-hidden="true">expand_more</span></summary>
            <div>
                <?php foreach ($versions as $version): ?>
                    <article><strong>v<?= esc($version['version']) ?></strong><p><?= esc($version['change_summary'] ?: 'No change summary') ?></p><small><?= ! empty($version['created_at']) ? esc(date('M d, Y g:i A', strtotime($version['created_at']))) : '' ?></small></article>
                <?php endforeach; ?>
            </div>
        </details>

        <details class="panel evidence-disclosure">
            <summary><span><small>Review timeline</small><strong><?= count($history) ?> review record(s)</strong></span><span class="material-symbols-rounded" aria-hidden="true">expand_more</span></summary>
            <div class="review-history-timeline">
                <?php foreach ($history as $item): ?>
                    <article>
                        <span class="timeline-dot"></span>
                        <div><strong><?= esc(ucfirst($item['stage'])) ?> round <?= esc((string) $item['review_round']) ?> · <?= esc(str_replace('_', ' ', $item['status'])) ?></strong><p><?= esc($item['reviewer_name'] ?? 'Unknown reviewer') ?></p><?php if (! empty($item['comments'])): ?><blockquote><?= esc($item['comments']) ?></blockquote><?php endif; ?></div>
                    </article>
                <?php endforeach; ?>
            </div>
        </details>
    </main>

    <aside class="panel review-decision-panel">
        <?php if ($isAssigned): ?>
            <div class="review-panel-heading">
                <div><p class="tag">Required review</p><h2>Checklist and decision</h2></div>
                <strong data-progress-label><?= esc((string) $progress['reviewed']) ?>/<?= esc((string) $progress['total']) ?></strong>
            </div>
            <div class="review-progress-line review-progress-line--large"><span data-progress-bar style="width: <?= $progress['total'] > 0 ? esc((string) (($progress['reviewed'] / $progress['total']) * 100), 'attr') : '0' ?>%"></span></div>
            <p class="review-guidance">Confirm each requirement or record an issue with a specific finding. Approval unlocks only after every item is confirmed.</p>

            <form method="post" action="<?= site_url('review/' . $stage . '/' . $review['id'] . '/decision') ?>" data-review-form>
                <?= csrf_field() ?>
                <div class="structured-checklist">
                    <?php foreach ($checklist as $key => $label): ?>
                        <?php $answer = $answers[$key] ?? ['result' => 'not_reviewed', 'note' => '']; ?>
                        <fieldset class="structured-check-item" data-check-item>
                            <legend><?= esc($label) ?></legend>
                            <div class="check-result-options">
                                <?php foreach (['confirmed' => 'Confirmed', 'issue' => 'Issue found', 'not_reviewed' => 'Not reviewed'] as $value => $optionLabel): ?>
                                    <label class="check-result-option">
                                        <input type="radio" name="checklist[<?= esc($key) ?>][result]" value="<?= esc($value) ?>" <?= $answer['result'] === $value ? 'checked' : '' ?>>
                                        <span><?= esc($optionLabel) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <label class="check-finding" data-finding <?= $answer['result'] !== 'issue' ? 'hidden' : '' ?>>
                                <span>Finding for this item</span>
                                <textarea name="checklist[<?= esc($key) ?>][note]" rows="2" placeholder="Describe what must be corrected"><?= esc($answer['note']) ?></textarea>
                            </label>
                        </fieldset>
                    <?php endforeach; ?>
                </div>

                <label class="review-comments-field">
                    <span>Contributor-facing comments</span>
                    <small>Required for revision requests and rejection. Keep the requested correction specific and actionable.</small>
                    <textarea name="comments" rows="5" placeholder="Summarize findings and the required next step"><?= esc($commentsValue) ?></textarea>
                </label>

                <div class="decision-blockers" data-decision-blockers aria-live="polite"></div>
                <div class="review-decision-actions">
                    <button class="button secondary" type="submit" formaction="<?= site_url('review/' . $stage . '/' . $review['id'] . '/draft') ?>" formnovalidate data-save-draft>Save draft</button>
                    <button class="button warning" type="submit" name="decision" value="revision_requested" data-confirm-decision="Request revision">Request revision</button>
                    <button class="button danger" type="submit" name="decision" value="rejected" data-confirm-decision="Reject submission">Reject</button>
                    <button class="button" type="submit" name="decision" value="approved" data-approve data-confirm-decision="Approve <?= esc($stage) ?> review">Approve review</button>
                </div>
            </form>
        <?php else: ?>
            <div class="completed-decision-card">
                <span class="material-symbols-rounded" aria-hidden="true"><?= $review['status'] === 'approved' ? 'task_alt' : ($review['status'] === 'rejected' ? 'cancel' : 'rate_review') ?></span>
                <p class="tag">Immutable decision</p>
                <h2><?= esc(ucwords(str_replace('_', ' ', $review['status']))) ?></h2>
                <p><?= esc($review['comments'] ?: 'No reviewer comments were recorded.') ?></p>
                <?php if (! empty($review['decided_at'])): ?><small>Submitted <?= esc(date('M d, Y g:i A', strtotime($review['decided_at']))) ?></small><?php endif; ?>
            </div>
            <div class="completed-checklist">
                <?php foreach ($checklist as $key => $label): ?>
                    <?php $answer = $answers[$key] ?? ['result' => 'not_reviewed', 'note' => '']; ?>
                    <article class="is-<?= esc($answer['result']) ?>"><strong><?= esc($label) ?></strong><span><?= esc(ucwords(str_replace('_', ' ', $answer['result']))) ?></span><?php if ($answer['note'] !== ''): ?><p><?= esc($answer['note']) ?></p><?php endif; ?></article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </aside>
</div>

<dialog class="governance-dialog" data-decision-dialog>
    <form method="dialog">
        <span class="material-symbols-rounded" aria-hidden="true">fact_check</span>
        <h2 data-dialog-title>Confirm decision</h2>
        <p>This decision is final for the current review round and will immediately update the contributor workflow.</p>
        <dl>
            <div><dt>Dataset</dt><dd><?= esc($dataset['title']) ?></dd></div>
            <div><dt>Version</dt><dd>v<?= esc($dataset['version'] ?? '1.0') ?></dd></div>
            <div><dt>Review stage</dt><dd><?= esc(ucfirst($stage)) ?></dd></div>
        </dl>
        <div class="dialog-actions"><button class="button secondary" value="cancel">Go back</button><button class="button" value="confirm" data-dialog-confirm>Confirm decision</button></div>
    </form>
</dialog>

<script>
(() => {
    const form = document.querySelector('[data-review-form]');
    if (!form) return;
    const items = Array.from(form.querySelectorAll('[data-check-item]'));
    const progressLabel = document.querySelector('[data-progress-label]');
    const progressBar = document.querySelector('[data-progress-bar]');
    const blockers = form.querySelector('[data-decision-blockers]');
    const approve = form.querySelector('[data-approve]');
    const dialog = document.querySelector('[data-decision-dialog]');
    let pendingButton = null;

    const update = () => {
        let reviewed = 0;
        let confirmed = 0;
        let issuesWithoutNotes = 0;
        items.forEach((item) => {
            const selected = item.querySelector('input[type="radio"]:checked')?.value || 'not_reviewed';
            const finding = item.querySelector('[data-finding]');
            const note = finding?.querySelector('textarea');
            finding.hidden = selected !== 'issue';
            if (selected !== 'not_reviewed') reviewed++;
            if (selected === 'confirmed') confirmed++;
            if (selected === 'issue' && !note?.value.trim()) issuesWithoutNotes++;
        });
        progressLabel.textContent = `${reviewed}/${items.length}`;
        progressBar.style.width = `${items.length ? (reviewed / items.length) * 100 : 0}%`;
        approve.disabled = confirmed !== items.length;
        const messages = [];
        if (reviewed < items.length) messages.push(`${items.length - reviewed} checklist item(s) are not reviewed.`);
        if (issuesWithoutNotes > 0) messages.push(`${issuesWithoutNotes} issue finding(s) still need a note.`);
        blockers.textContent = messages.join(' ');
        blockers.hidden = messages.length === 0;
    };

    items.forEach((item) => {
        item.addEventListener('change', update);
        item.querySelector('textarea')?.addEventListener('input', update);
    });
    update();

    form.querySelectorAll('[data-confirm-decision]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (button.disabled) return;
            event.preventDefault();
            pendingButton = button;
            dialog.querySelector('[data-dialog-title]').textContent = button.dataset.confirmDecision;
            dialog.showModal();
        });
    });
    dialog?.addEventListener('close', () => {
        if (dialog.returnValue !== 'confirm' || !pendingButton) return;
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'decision';
        hidden.value = pendingButton.value;
        form.appendChild(hidden);
        form.querySelectorAll('button[type="submit"]').forEach((button) => button.disabled = true);
        form.requestSubmit();
    });
})();
</script>
<?= $this->endSection() ?>
