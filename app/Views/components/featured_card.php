<?php
/**
 * Featured Dataset Card Component
 *
 * Renders a structured metadata card showing key dataset fields:
 * author, research/thesis origin, project head, date, and stats.
 * The entire card is clickable and navigates to the full record view.
 *
 * Usage: <?= view('components/featured_card', ['dataset' => $dataset]) ?>
 *
 * @var array $dataset Dataset row with keys: id, title, data_type, category,
 *                      tags, author_name, access_type, created_at,
 *                      research_title, project_head, members, source_type,
 *                      download_count (optional), view_count (optional)
 */
$tags = array_values(array_filter(array_map('trim', explode(',', (string) ($dataset['tags'] ?? '')))));

$accessType = $dataset['access_type'] ?? 'public';
$accessLabel = match ($accessType) {
    'public' => 'Public',
    'institutional' => 'Institutional',
    'restricted' => 'Restricted',
    default => 'Public',
};
$accessClass = match ($accessType) {
    'public' => '',
    'institutional' => ' fc-access--institutional',
    'restricted' => ' fc-access--restricted',
    default => '',
};

$dataType      = esc($dataset['data_type'] ?: 'Dataset');
$title         = esc($dataset['title']);
$contributor   = esc($dataset['author_name'] ?? 'Contributor');
$category      = esc($dataset['category'] ?: 'Uncategorized');
$researchTitle = esc(trim((string) ($dataset['research_title'] ?? '')));
$projectHead   = esc(trim((string) ($dataset['project_head'] ?? '')));
$sourceType    = esc(trim((string) ($dataset['source_type'] ?? '')));
$url           = site_url('datasets/' . $dataset['id']);
$date          = ! empty($dataset['created_at'])
    ? date('M j, Y', strtotime($dataset['created_at']))
    : '';

$datasetDescription = trim((string) ($dataset['description'] ?? ''));
$shortDescription   = strlen($datasetDescription) > 150
    ? substr($datasetDescription, 0, 147) . '…'
    : $datasetDescription;

$viewCount     = (int) ($dataset['view_count'] ?? 0);
$downloadCount = (int) ($dataset['download_count'] ?? 0);

$previewId      = 'fc-preview-' . $dataset['id'];
?>
<div class="featured-card-wrapper">
<article class="featured-card" data-type="<?= esc($dataType) ?>" data-access="<?= esc($accessType) ?>">
    <div class="fc-top-bar"></div>
    <div class="fc-content">
        <a href="<?= $url ?>" class="featured-card-link" aria-label="View <?= $title ?>">
            <div class="fc-body">
                <!-- Badges row (top) -->
                <div class="fc-badges">
                    <span class="fc-type-badge"><?= $dataType ?></span>
                    <span class="fc-cat-badge"><?= $category ?></span>
                    <span class="fc-access-badge<?= $accessClass ?>"><?= $accessLabel ?></span>
                </div>

                <!-- Title -->
                <h3 class="fc-title">
                    <span><?= $title ?></span>
                </h3>

                <!-- Metadata fields row -->
                <dl class="fc-fields">
                    <div class="fc-field">
                        <dt>Author</dt>
                        <dd><?= $contributor ?></dd>
                    </div>
                    <?php if ($researchTitle): ?>
                    <div class="fc-field">
                        <dt>Research</dt>
                        <dd><?= $researchTitle ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($projectHead): ?>
                    <div class="fc-field">
                        <dt>Advisor</dt>
                        <dd><?= $projectHead ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ($date): ?>
                    <div class="fc-field">
                        <dt>Published</dt>
                        <dd><?= $date ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>
        </a>

        <!-- Footer: stats left, actions right — outside <a> for keyboard a11y -->
        <div class="fc-footer" role="group" aria-label="Dataset stats and actions">
            <div class="fc-stats">
                <span title="Views"><span class="material-symbols-rounded" aria-hidden="true">visibility</span> <?= number_format($viewCount) ?></span>
                <span title="Downloads"><span class="material-symbols-rounded" aria-hidden="true">download</span> <?= number_format($downloadCount) ?></span>
            </div>
            <div class="fc-actions">
                <button type="button" class="fc-action-btn fc-preview-trigger" data-preview-trigger="<?= $previewId ?>" aria-controls="<?= $previewId ?>" aria-expanded="false">
                    <span class="material-symbols-rounded" aria-hidden="true">visibility</span>
                    Preview
                </button>
                <button type="button" class="fc-action-btn fc-action-btn--primary" onclick="window.location='<?= $url ?>'" aria-label="Visit record for <?= $title ?>">
                    <span class="material-symbols-rounded" aria-hidden="true">arrow_forward</span>
                    Visit Record
                </button>
            </div>
        </div>
    </div>
</article>
</div>

<!-- Preview Modal (unified component) -->
<?= view('components/dataset_preview', [
    'dataset'   => $dataset,
    'context'   => 'standard',
    'triggerId' => $previewId,
]) ?>
