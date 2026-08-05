<?php
/**
 * Compact Dataset Card Component
 *
 * A simpler, less detailed card for the home page featured section.
 *
 * Variants:
 *   'recent'  — shows the created date
 *   'popular' — shows view count and download count
 *
 * Usage: <?= view('components/compact_card', ['dataset' => $dataset, 'variant' => 'recent']) ?>
 */
$dataType  = esc($dataset['data_type'] ?: 'Dataset');
$title     = esc($dataset['title']);
$contributor = esc($dataset['author_name'] ?? 'Contributor');
$category  = esc($dataset['category'] ?: 'Uncategorized');
$url       = site_url('datasets/' . $dataset['id']);
$variant   = $variant ?? 'recent'; // 'recent' | 'popular'

// Format date
$createdDate = ! empty($dataset['created_at'])
    ? date('M d, Y', strtotime($dataset['created_at']))
    : '';

// Stats
$viewCount     = (int) ($dataset['view_count'] ?? 0);
$downloadCount = (int) ($dataset['download_count'] ?? 0);
?>
<a class="compact-card" href="<?= $url ?>" data-type="<?= $dataType ?>" data-variant="<?= $variant ?>">
    <span class="cc-title"><?= $title ?></span>
    <div class="cc-badges">
        <span class="cc-type"><?= $dataType ?></span>
        <span class="cc-cat"><?= $category ?></span>
    </div>

    <?php if ($variant === 'recent' && $createdDate): ?>
        <span class="cc-date">
            <span class="material-symbols-rounded" aria-hidden="true">calendar_today</span>
            <?= $createdDate ?>
        </span>
    <?php elseif ($variant === 'popular'): ?>
        <span class="cc-stats">
            <span class="cc-stat" title="Views">
                <span class="material-symbols-rounded" aria-hidden="true">visibility</span>
                <?= number_format($viewCount) ?>
            </span>
            <span class="cc-stat" title="Downloads">
                <span class="material-symbols-rounded" aria-hidden="true">download</span>
                <?= number_format($downloadCount) ?>
            </span>
        </span>
    <?php endif; ?>

    <span class="cc-author">
        <span class="material-symbols-rounded" aria-hidden="true">person</span>
        <?= $contributor ?>
    </span>
</a>
