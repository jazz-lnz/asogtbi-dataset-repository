<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pageCount = $pager->getPageCount();
$currentPage = $pager->getCurrentPageNumber();
$pager->setSurroundCount($pageCount);

$linksByPage = [];
foreach ($pager->links() as $link) {
    $linksByPage[(int) $link['title']] = $link;
}

$edgeWindowSize = 4;
$edgeThreshold = 3;

if ($pageCount <= 7) {
    $windowStart = 1;
    $windowEnd = $pageCount;
} elseif ($currentPage <= $edgeThreshold) {
    $windowStart = 1;
    $windowEnd = $edgeWindowSize;
} elseif ($currentPage >= $pageCount - ($edgeThreshold - 1)) {
    $windowStart = $pageCount - ($edgeWindowSize - 1);
    $windowEnd = $pageCount;
} else {
    $windowStart = $currentPage - 1;
    $windowEnd = $currentPage + 1;
}

$displayItems = [];
if ($windowStart > 1) {
    $displayItems[] = 1;

    if ($windowStart === 3) {
        $displayItems[] = 2;
    } elseif ($windowStart > 3) {
        $displayItems[] = 'ellipsis-start';
    }
}

for ($page = $windowStart; $page <= $windowEnd; $page++) {
    $displayItems[] = $page;
}

if ($windowEnd < $pageCount) {
    if ($windowEnd === $pageCount - 2) {
        $displayItems[] = $pageCount - 1;
    } elseif ($windowEnd < $pageCount - 2) {
        $displayItems[] = 'ellipsis-end';
    }

    $displayItems[] = $pageCount;
}
?>

<nav class="catalog-pagination" aria-label="<?= lang('Pager.pageNavigation') ?>">
    <ul class="pagination catalog-pagination__list">
        <li class="page-item page-item--nav<?= $pager->hasPreviousPage() ? '' : ' disabled' ?>">
            <?php if ($pager->hasPreviousPage()): ?>
                <a class="page-link page-link--nav" href="<?= esc($pager->getPreviousPage(), 'attr') ?>" rel="prev">
                    <span class="material-symbols-rounded" aria-hidden="true">chevron_left</span>
                    <span>Prev</span>
                </a>
            <?php else: ?>
                <span class="page-link page-link--nav" aria-disabled="true">
                    <span class="material-symbols-rounded" aria-hidden="true">chevron_left</span>
                    <span>Prev</span>
                </span>
            <?php endif; ?>
        </li>

        <?php foreach ($displayItems as $item): ?>
            <?php if (is_string($item)): ?>
                <li class="page-item page-item--ellipsis" aria-hidden="true">
                    <span class="page-ellipsis">...</span>
                </li>
            <?php else: ?>
                <?php $link = $linksByPage[$item]; ?>
                <li class="page-item<?= $link['active'] ? ' active' : '' ?>">
                    <a class="page-link" href="<?= esc($link['uri'], 'attr') ?>"<?= $link['active'] ? ' aria-current="page"' : '' ?>>
                        <?= esc($link['title']) ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>

        <li class="page-item page-item--nav<?= $pager->hasNextPage() ? '' : ' disabled' ?>">
            <?php if ($pager->hasNextPage()): ?>
                <a class="page-link page-link--nav" href="<?= esc($pager->getNextPage(), 'attr') ?>" rel="next">
                    <span>Next</span>
                    <span class="material-symbols-rounded" aria-hidden="true">chevron_right</span>
                </a>
            <?php else: ?>
                <span class="page-link page-link--nav" aria-disabled="true">
                    <span>Next</span>
                    <span class="material-symbols-rounded" aria-hidden="true">chevron_right</span>
                </span>
            <?php endif; ?>
        </li>
    </ul>
</nav>
