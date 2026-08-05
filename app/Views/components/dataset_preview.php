<?php
/**
 * Unified Dataset Preview Modal Component
 *
 * Renders a consistent preview modal for any dataset, whether it's from
 * the browse page (featured card) or the recommendations sidebar.
 *
 * Usage:
 *   <?= view('components/dataset_preview', [
 *       'dataset'     => $dataset,           // Required: dataset array
 *       'context'     => 'standard',         // Optional: 'standard' or 'recommended'
 *       'triggerId'   => 'preview-123',      // Optional: custom trigger/modal ID
 *   ]) ?>
 *
 * Required dataset keys: id, title
 * Optional keys: description, category, data_type, file_format, content_formats,
 *                author_name, research_title, project_head, members, source_type,
 *                tags, created_at, access_type, view_count, download_count, score
 */

$context   = $context ?? 'standard';
$datasetId = (int) ($dataset['id'] ?? 0);
$baseId    = $triggerId ?? 'dataset-preview-' . $datasetId;

// Prepare data with defaults
$title         = esc($dataset['title'] ?? 'Untitled Dataset');
$description   = trim((string) ($dataset['description'] ?? ''));
$hasDescription = strlen($description) > 0;
$category      = esc($dataset['category'] ?: 'Uncategorized');
$dataType      = esc($dataset['data_type'] ?: 'Dataset');
$fileFormat    = esc($dataset['file_format'] ?: 'ZIP');
$contentFormats = esc($dataset['content_formats'] ?: '');
$authorName    = esc($dataset['author_name'] ?: 'Unknown contributor');
$researchTitle = esc($dataset['research_title'] ?? '');
$projectHead   = esc($dataset['project_head'] ?? '');
$members       = esc($dataset['members'] ?? '');
$sourceType    = esc($dataset['source_type'] ?? '');
$accessType    = $dataset['access_type'] ?? 'public';
$score         = (int) ($dataset['score'] ?? 0);

// Access label
$accessLabel = match ($accessType) {
    'public'       => 'Public',
    'institutional'=> 'Institutional',
    'restricted'   => 'Restricted',
    default        => 'Public',
};
$accessClass = match ($accessType) {
    'institutional'=> ' fc-access--institutional',
    'restricted'   => ' fc-access--restricted',
    default        => '',
};

// Date
$publishedDate = '';
if (! empty($dataset['created_at'])) {
    $publishedDate = date('M j, Y', strtotime((string) $dataset['created_at']));
}

// Tags
$tags = array_values(array_filter(array_map('trim', explode(',', (string) ($dataset['tags'] ?? '')))));
$hasTags = count($tags) > 0;

// Stats
$viewCount     = (int) ($dataset['view_count'] ?? 0);
$downloadCount = (int) ($dataset['download_count'] ?? 0);
$hasStats = $viewCount > 0 || $downloadCount > 0;

// URL
$url = site_url('datasets/' . $datasetId);

// Context-specific labels
$previewLabel = $context === 'recommended' ? 'Recommendation Preview' : 'Preview';
$exploreLabel = $context === 'recommended' ? 'Explore' : 'Explore Full Record';
?>

<div class="dataset-preview-modal" id="<?= $baseId ?>" role="dialog" aria-modal="true" aria-labelledby="<?= $baseId ?>-title" hidden>
    <div class="dataset-preview-backdrop" data-preview-close="<?= $baseId ?>"></div>
    <div class="dataset-preview-card" tabindex="-1">
        <button type="button" class="dataset-preview-close" data-preview-close="<?= $baseId ?>" aria-label="Close preview">
            <span class="material-symbols-rounded" aria-hidden="true">close</span>
        </button>

        <div class="dataset-preview-head">
            <p class="dataset-preview-kicker"><?= $previewLabel ?></p>
            <h2 id="<?= $baseId ?>-title"><?= $title ?></h2>
            <div class="dataset-preview-badges">
                <span class="fc-type-badge"><?= $dataType ?></span>
                <span class="fc-cat-badge"><?= $category ?></span>
                <?php if ($contentFormats): ?>
                    <span class="fc-access-badge"><?= $contentFormats ?></span>
                <?php endif; ?>
                <span class="fc-access-badge<?= $accessClass ?>"><?= $accessLabel ?></span>
            </div>
        </div>

        <!-- Metadata table -->
        <table class="dataset-preview-table">
            <tbody>
                <tr><th>Author</th><td><?= $authorName ?></td></tr>
                <?php if ($researchTitle): ?>
                    <tr><th>Research Title</th><td><?= $researchTitle ?></td></tr>
                <?php endif; ?>
                <?php if ($projectHead): ?>
                    <tr><th>Project Head / Advisor</th><td><?= $projectHead ?></td></tr>
                <?php endif; ?>
                <?php if ($members): ?>
                    <tr><th>Members</th><td><?= $members ?></td></tr>
                <?php endif; ?>
                <?php if ($sourceType): ?>
                    <tr><th>Source Type</th><td><?= $sourceType ?></td></tr>
                <?php endif; ?>
                <tr><th>Category</th><td><?= $category ?></td></tr>
                <tr><th>Data Type</th><td><?= $dataType ?></td></tr>
                <tr><th>Format</th><td><?= $fileFormat ?></td></tr>
                <tr><th>Access</th><td><?= $accessLabel ?></td></tr>
                <?php if ($publishedDate): ?>
                    <tr><th>Published</th><td><?= $publishedDate ?></td></tr>
                <?php endif; ?>
                <?php if ($hasStats): ?>
                    <tr>
                        <th>Activity</th>
                        <td>
                            <?php if ($viewCount > 0): ?>
                                <span class="pm-stat"><span class="material-symbols-rounded" aria-hidden="true">visibility</span> <?= number_format($viewCount) ?> views</span>
                            <?php endif; ?>
                            <?php if ($downloadCount > 0): ?>
                                <span class="pm-stat"><span class="material-symbols-rounded" aria-hidden="true">download</span> <?= number_format($downloadCount) ?> downloads</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if ($score > 0): ?>
                    <tr><th>Match Score</th><td><?= $score ?>%</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($hasDescription): ?>
        <div class="dataset-preview-section">
            <h3 class="dataset-preview-section-title">Description</h3>
            <p class="dataset-preview-desc"><?= esc($description) ?></p>
        </div>
        <?php endif; ?>

        <?php if ($hasTags): ?>
        <div class="dataset-preview-section">
            <h3 class="dataset-preview-section-title">Tags</h3>
            <div class="dataset-preview-tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="fc-tag">#<?= esc($tag) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="dataset-preview-actions">
            <a class="button" href="<?= $url ?>"><span class="material-symbols-rounded" aria-hidden="true">open_in_new</span> <?= $exploreLabel ?></a>
        </div>
    </div>
</div>
