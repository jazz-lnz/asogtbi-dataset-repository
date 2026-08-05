<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="browse-hero">
    <div class="grid-overlay"></div>
    <div class="browse-hero-content">
        <div class="browse-hero-eyebrow">Dataset Catalog</div>
        <h1 class="browse-hero-title">Browse Datasets</h1>
        <p class="browse-hero-sub">
            Explore verified research datasets from CSPC theses, capstone projects, and ASOG TBI incubatee studies.
        </p>
    </div>
</div>

<?php
    $dataTypes = ['Text', 'Image', 'Audio', 'Video', 'Tabular'];
    $dateLabels = $dateOptions ?? [
        'today' => 'Today',
        'week' => 'This Week',
        'month' => 'This Month',
        'year' => 'This Year',
    ];

    $selectedDataTypes = $selectedDataTypes ?? [];
    $selectedCategories = $selectedCategories ?? [];
    $selectedDateUploaded = $selectedDateUploaded ?? [];

    $selectedDataTypeStr = implode(',', $selectedDataTypes);
    $selectedCategoryStr = implode(',', $selectedCategories);
    $selectedDateUploadedStr = implode(',', $selectedDateUploaded);

    $activeFilterCount = count($selectedDataTypes) + count($selectedCategories) + count($selectedDateUploaded);

    $allParams = array_filter([
        'q' => $search ?? '',
        'data_type' => $selectedDataTypeStr,
        'category' => $selectedCategoryStr,
        'date_uploaded' => $selectedDateUploadedStr,
    ], static fn($v) => $v !== '');

    $resetUrl = site_url('datasets') . '#catalog-results';
?>

<section class="catalog-layout shell">
    <!-- Filter Sidebar (desktop) -->
    <aside class="filter-sidebar" id="filter-sidebar">
        <div class="filter-sidebar-header">
            <h2>Filters</h2>
            <span class="filter-count-badge"><?= $activeFilterCount ?></span>
        </div>

        <form method="get" action="<?= site_url('datasets') ?>" id="filter-form">
            <input type="hidden" name="q" value="<?= esc($search ?? '') ?>">

            <!-- Category -->
            <?php if (! empty($categories)): ?>
            <details class="filter-accordion" open>
                <summary>
                    <span>Category</span>
                    <span class="material-symbols-rounded accordion-chevron" aria-hidden="true">expand_more</span>
                </summary>
                <div class="filter-accordion-body">
                    <?php foreach ($categories as $cat): ?>
                        <button
                            type="button"
                            class="filter-chip <?= in_array($cat, $selectedCategories, true) ? 'is-active' : '' ?>"
                            data-filter-name="category"
                            data-filter-value="<?= esc($cat) ?>"
                        ><?= esc($cat) ?></button>
                    <?php endforeach; ?>
                </div>
            </details>
            <?php endif; ?>

            <!-- Date Uploaded -->
            <details class="filter-accordion"<?= ! empty($selectedDateUploaded) ? ' open' : '' ?>>
                <summary>
                    <span>Date Uploaded</span>
                    <span class="material-symbols-rounded accordion-chevron" aria-hidden="true">expand_more</span>
                </summary>
                <div class="filter-accordion-body">
                    <?php foreach ($dateLabels as $value => $label): ?>
                        <button
                            type="button"
                            class="filter-chip <?= in_array($value, $selectedDateUploaded, true) ? 'is-active' : '' ?>"
                            data-filter-name="date_uploaded"
                            data-filter-value="<?= esc($value) ?>"
                        ><?= esc($label) ?></button>
                    <?php endforeach; ?>
                </div>
            </details>

            <button type="button" class="filter-apply-btn" id="filter-apply-btn">Apply Filters</button>
        </form>
    </aside>

    <!-- Results section -->
    <section class="results-engine" id="catalog-results">

        <div class="catalog-sticky-bar">
            <!-- Search bar (inside results) -->
            <form method="get" action="<?= site_url('datasets') ?>" class="catalog-search">
                <input type="text" name="q" value="<?= esc($search ?? '') ?>" placeholder="Search by title, tag, or category…" aria-label="Search datasets">
                <button type="submit" aria-label="Search">
                    <span class="material-symbols-rounded">search</span>
                </button>
                <?php if (! empty($selectedDataTypes)): ?>
                    <input type="hidden" name="data_type" value="<?= esc(implode(',', $selectedDataTypes)) ?>">
                <?php endif; ?>
                <?php if (! empty($selectedCategories)): ?>
                    <input type="hidden" name="category" value="<?= esc(implode(',', $selectedCategories)) ?>">
                <?php endif; ?>
                <?php if (! empty($selectedDateUploaded)): ?>
                    <input type="hidden" name="date_uploaded" value="<?= esc(implode(',', $selectedDateUploaded)) ?>">
                <?php endif; ?>
            </form>

            <?php
                $summary = $paginationSummary ?? ['start' => 0, 'end' => 0, 'total' => $totalDatasets ?? 0];
                $hasResults = (int) $summary['total'] > 0;
            ?>
            <div class="catalog-results-header">
                <div class="catalog-results-heading-wrap">
                    <?php if ($hasResults): ?>
                        <h2>Showing <?= (int) $summary['start'] ?>-<?= (int) $summary['end'] ?> of <?= (int) $summary['total'] ?> Datasets</h2>
                    <?php else: ?>
                        <h2 class="catalog-results-zero">0 Datasets</h2>
                    <?php endif; ?>
                </div>
                <a class="catalog-submit-btn" href="<?= site_url('upload') ?>">
                    <span class="material-symbols-rounded" aria-hidden="true">upload</span>
                    <span class="catalog-submit-text">Submit Dataset</span>
                </a>
            </div>

            <!-- Type pills -->
            <div class="browse-pill-row">
                <button type="button" class="browse-pill <?= empty($selectedDataTypes) ? 'is-active' : '' ?>" data-pill="all">All</button>
                <?php foreach ($dataTypes as $type): ?>
                    <button type="button" class="browse-pill <?= in_array($type, $selectedDataTypes, true) ? 'is-active' : '' ?>" data-pill="<?= esc($type) ?>"><?= esc($type) ?></button>
                <?php endforeach; ?>
            </div>

            <!-- Active filters display -->
            <?php
                $activeFilters = [];
                if (! empty($search)) {
                    $activeFilters['q'] = ['label' => '&quot;' . esc($search) . '&quot;', 'key' => 'q'];
                }
                if (! empty($selectedDataTypes)) {
                    $activeFilters['data_type'] = ['label' => esc(implode(', ', $selectedDataTypes)), 'key' => 'data_type', 'prefix' => 'Type:'];
                }
                if (! empty($selectedCategories)) {
                    $activeFilters['category'] = ['label' => esc(implode(', ', $selectedCategories)), 'key' => 'category', 'prefix' => 'Category:'];
                }
                if (! empty($selectedDateUploaded)) {
                    $labels = [];
                    foreach ((array) $selectedDateUploaded as $du) {
                        $labels[] = $dateLabels[$du] ?? $du;
                    }
                    $activeFilters['date_uploaded'] = ['label' => esc(implode(', ', $labels)), 'key' => 'date_uploaded', 'prefix' => 'Uploaded:'];
                }
            ?>
            <?php if (! empty($activeFilters)): ?>
                <div class="active-filters-bar">
                    <span class="active-filters-label">Active filters</span>
                    <?php foreach ($activeFilters as $key => $filter): ?>
                        <?php
                            $remaining = $allParams;
                            unset($remaining[$key]);
                            $removeUrl = site_url('datasets') . (! empty($remaining) ? '?' . http_build_query($remaining) : '') . '#catalog-results';
                        ?>
                        <a class="active-filter-chip" href="<?= esc($removeUrl, 'attr') ?>">
                            <?php if (! empty($filter['prefix'])): ?>
                                <span class="chip-prefix"><?= esc($filter['prefix']) ?></span>
                            <?php endif; ?>
                            <?= $filter['label'] ?>
                            <span class="chip-close material-symbols-rounded" aria-hidden="true">close</span>
                        </a>
                    <?php endforeach; ?>
                    <a class="filter-clear-link" href="<?= esc($resetUrl, 'attr') ?>">Clear all</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (empty($datasets)): ?>
            <article class="catalog-empty-card">
                <div class="empty-vector">📂</div>
                <h2>No Datasets Found</h2>
                <p>We couldn't locate any items matching your active parameter selections. Try loosening your sidebar filters or modifying your query search terms.</p>
                <a class="button secondary" href="<?= site_url('datasets') ?>#catalog-results">Reset Search Filters</a>
            </article>
        <?php else: ?>
            <div class="results-grid">
                <?php foreach ($datasets as $dataset): ?>
                    <?= view('components/featured_card', ['dataset' => $dataset]) ?>
                <?php endforeach; ?>
            </div>

            <?php if (!isset($pager) || $pager->getPageCount() <= 1): ?>
                <div class="catalog-end-message">You've reached the end of the list</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
            <?= $pager->links('default', 'catalog_full') ?>
        <?php endif; ?>
    </section>
</section>

<!-- Mobile filter trigger -->
<button type="button" class="mobile-filter-trigger" id="mobile-filter-trigger" aria-label="Open filters">
    <span class="material-symbols-rounded" aria-hidden="true">filter_list</span>
    Filters
    <?php if ($activeFilterCount > 0): ?>
        <span class="filter-count-dot"><?= $activeFilterCount ?></span>
    <?php endif; ?>
</button>

<!-- Mobile filter sheet -->
<div class="filter-sheet-backdrop" id="filter-sheet-backdrop"></div>
<div class="filter-sheet" id="filter-sheet" role="dialog" aria-modal="true" aria-label="Filters">
    <div class="filter-sheet-header">
        <h2>Filters</h2>
        <button type="button" class="filter-sheet-close" id="filter-sheet-close" aria-label="Close filters">
            <span class="material-symbols-rounded" aria-hidden="true">close</span>
        </button>
    </div>

    <?php if (! empty($categories)): ?>
    <details class="filter-accordion" open>
        <summary>
            <span>Category</span>
            <span class="material-symbols-rounded accordion-chevron" aria-hidden="true">expand_more</span>
        </summary>
        <div class="filter-accordion-body">
            <?php foreach ($categories as $cat): ?>
                <button type="button" class="filter-chip <?= in_array($cat, $selectedCategories ?? [], true) ? 'is-active' : '' ?>" data-filter-name="category" data-filter-value="<?= esc($cat) ?>"><?= esc($cat) ?></button>
            <?php endforeach; ?>
        </div>
    </details>
    <?php endif; ?>

    <details class="filter-accordion"<?= ! empty($selectedDateUploaded) ? ' open' : '' ?>>
        <summary>
            <span>Date Uploaded</span>
            <span class="material-symbols-rounded accordion-chevron" aria-hidden="true">expand_more</span>
        </summary>
        <div class="filter-accordion-body">
            <?php foreach ($dateLabels as $value => $label): ?>
                <button type="button" class="filter-chip <?= in_array($value, $selectedDateUploaded ?? [], true) ? 'is-active' : '' ?>" data-filter-name="date_uploaded" data-filter-value="<?= esc($value) ?>"><?= esc($label) ?></button>
            <?php endforeach; ?>
        </div>
    </details>

    <button type="button" class="filter-sheet-apply" id="filter-sheet-apply">Apply Filters</button>
</div>

<script>
(function() {
    // ─── Filter chip toggling ────────────────────────────────────────────────
    const filterForm = document.getElementById('filter-form');
    if (!filterForm) return;

    // Active filters tracking — all three filters use arrays for multi-select
    var initialDataTypes = <?= json_encode($selectedDataTypes ?? []) ?>;
    var initialCategories = <?= json_encode($selectedCategories ?? []) ?>;
    var initialDateUploaded = <?= json_encode($selectedDateUploaded ?? []) ?>;

    // Ensure arrays (legacy-safety)
    if (!Array.isArray(initialDataTypes)) initialDataTypes = [];
    if (!Array.isArray(initialCategories)) initialCategories = [];
    if (!Array.isArray(initialDateUploaded)) initialDateUploaded = [];

    var currentSearch = <?= json_encode($search ?? '') ?>;
    if (typeof currentSearch !== 'string') currentSearch = '';

    // These track what the server already applied
    const committedFilters = {
        data_type: initialDataTypes.slice(),
        category: initialCategories.slice(),
        date_uploaded: initialDateUploaded.slice(),
    };

    // These track what the user is currently selecting (pending state)
    const pendingFilters = {
        data_type: initialDataTypes.slice(),
        category: initialCategories.slice(),
        date_uploaded: initialDateUploaded.slice(),
    };

    // ─── Helper: toggle value in array ────────────────────────────────────────
    function toggleArray(arr, val) {
        var idx = arr.indexOf(val);
        if (idx === -1) {
            arr.push(val);
        } else {
            arr.splice(idx, 1);
        }
        return arr;
    }

    // ─── Toggle chips (multi-select for all filter groups) — PENDING ONLY ────
    document.querySelectorAll('.filter-chip[data-filter-name]').forEach(chip => {
        chip.addEventListener('click', function() {
            var name = this.dataset.filterName;
            var value = this.dataset.filterValue;
            var arr = pendingFilters[name];
            if (!Array.isArray(arr)) arr = [];
            arr = toggleArray(arr, value);
            pendingFilters[name] = arr;
            this.classList.toggle('is-active');
        });
    });

    // ─── Type pills (multi-select) — PENDING ONLY ────────────────────────────
    document.querySelectorAll('.browse-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            var type = this.dataset.pill;
            if (type === 'all') {
                pendingFilters.data_type = [];
                document.querySelectorAll('.browse-pill').forEach(function(p) { p.classList.remove('is-active'); });
                document.querySelector('.browse-pill[data-pill="all"]').classList.add('is-active');
            } else {
                // Remove 'all' active state when a specific type is selected
                document.querySelector('.browse-pill[data-pill="all"]').classList.remove('is-active');
                var arr = pendingFilters.data_type;
                if (!Array.isArray(arr)) arr = [];
                arr = toggleArray(arr, type);
                pendingFilters.data_type = arr;
                this.classList.toggle('is-active');
                // If nothing selected, make 'all' active again
                if (arr.length === 0) {
                    document.querySelector('.browse-pill[data-pill="all"]').classList.add('is-active');
                }
            }
            // Immediately apply the pill filter
            submitFilters(pendingFilters);
        });
    });

    // ─── Submit filters (full page reload) ──────────────────────────────────
    function submitFilters(filtersToApply) {
        var params = new URLSearchParams();

        if (currentSearch) params.set('q', currentSearch);
        if (filtersToApply.data_type && filtersToApply.data_type.length > 0) {
            params.set('data_type', filtersToApply.data_type.join(','));
        }
        if (filtersToApply.category && filtersToApply.category.length > 0) {
            params.set('category', filtersToApply.category.join(','));
        }
        if (filtersToApply.date_uploaded && filtersToApply.date_uploaded.length > 0) {
            params.set('date_uploaded', filtersToApply.date_uploaded.join(','));
        }

        var qs = params.toString();
        var base = '<?= site_url('datasets') ?>';
        window.location.href = base + (qs ? '?' + qs : '') + '#catalog-results';
    }

    // ─── Desktop sidebar Apply button ─────────────────────────────────────────
    var desktopApplyBtn = document.getElementById('filter-apply-btn');
    if (desktopApplyBtn) {
        desktopApplyBtn.addEventListener('click', function() {
            // Copy pending → committed and submit
            committedFilters.data_type = pendingFilters.data_type.slice();
            committedFilters.category = pendingFilters.category.slice();
            committedFilters.date_uploaded = pendingFilters.date_uploaded.slice();
            submitFilters(pendingFilters);
        });
    }

    // ─── Mobile filter sheet ─────────────────────────────────────────────────
    const trigger = document.getElementById('mobile-filter-trigger');
    const sheet = document.getElementById('filter-sheet');
    const backdrop = document.getElementById('filter-sheet-backdrop');
    const closeBtn = document.getElementById('filter-sheet-close');
    const applyBtn = document.getElementById('filter-sheet-apply');

    const openSheet = () => {
        sheet?.classList.add('is-open');
        backdrop?.classList.add('is-open');
        document.body.classList.add('overflow-hidden');
    };

    const closeSheet = () => {
        sheet?.classList.remove('is-open');
        backdrop?.classList.remove('is-open');
        document.body.classList.remove('overflow-hidden');
    };

    trigger?.addEventListener('click', openSheet);
    closeBtn?.addEventListener('click', closeSheet);
    backdrop?.addEventListener('click', closeSheet);

    applyBtn?.addEventListener('click', function() {
        // Rebuild pendingFilters arrays from mobile sheet chip state
        var newDataTypes = [];
        var newCategories = [];
        var newDates = [];

        document.querySelectorAll('#filter-sheet .filter-chip.is-active[data-filter-name]').forEach(function(chip) {
            var name = chip.dataset.filterName;
            var value = chip.dataset.filterValue;
            if (name === 'data_type') newDataTypes.push(value);
            else if (name === 'category') newCategories.push(value);
            else if (name === 'date_uploaded') newDates.push(value);
        });

        pendingFilters.data_type = newDataTypes;
        pendingFilters.category = newCategories;
        pendingFilters.date_uploaded = newDates;

        // Update desktop sidebar chips to match
        document.querySelectorAll('#filter-sidebar .filter-chip[data-filter-name]').forEach(function(chip) {
            var name = chip.dataset.filterName;
            var value = chip.dataset.filterValue;
            var arr = pendingFilters[name];
            var isActive = Array.isArray(arr) ? arr.indexOf(value) !== -1 : false;
            chip.classList.toggle('is-active', isActive);
        });

        // Sync type pills
        document.querySelectorAll('.browse-pill').forEach(function(pill) {
            var type = pill.dataset.pill;
            if (type === 'all') {
                pill.classList.toggle('is-active', pendingFilters.data_type.length === 0);
            } else {
                pill.classList.toggle('is-active', pendingFilters.data_type.indexOf(type) !== -1);
            }
        });

        closeSheet();
        // Only submit if there are actual changes
        var hasChanges = ['data_type', 'category', 'date_uploaded'].some(function(key) {
            return JSON.stringify((pendingFilters[key] || []).slice().sort()) !==
                   JSON.stringify((committedFilters[key] || []).slice().sort());
        });
        if (hasChanges) submitFilters(pendingFilters);
    });

    // Sync active-filter chips in mobile sheet when opening
    trigger?.addEventListener('click', function() {
        document.querySelectorAll('#filter-sheet .filter-chip[data-filter-name]').forEach(function(chip) {
            var name = chip.dataset.filterName;
            var value = chip.dataset.filterValue;
            var arr = pendingFilters[name];
            var isActive = Array.isArray(arr) ? arr.indexOf(value) !== -1 : false;
            chip.classList.toggle('is-active', isActive);
        });
    });

    // ─── Detect sticky bar stuck state ─
    function checkStickyBar() {
        var bar = document.querySelector('.catalog-sticky-bar');
        if (!bar) return;
        var topPx = parseFloat(window.getComputedStyle(bar).top) || 0;
        bar.classList.toggle('is-stuck', bar.getBoundingClientRect().top <= topPx + 1);
    }

    // Suppress transitions during initial mount so the collapse animation
    // doesn't play on page load when the user is already scrolled down.
    var stickyBar = document.querySelector('.catalog-sticky-bar');
    if (stickyBar) stickyBar.classList.add('no-transition');
    checkStickyBar();
    requestAnimationFrame(function() {
        requestAnimationFrame(function() {
            if (stickyBar) stickyBar.classList.remove('no-transition');
        });
    });

    window.addEventListener('scroll', checkStickyBar, { passive: true });
})();
</script>

<script>
(function() {
    // ─── Featured card preview modal ───────────────────────────────────────────
    document.querySelectorAll('[data-preview-trigger]').forEach(function(trigger) {
        trigger.addEventListener('click', function() {
            var targetId = this.getAttribute('data-preview-trigger');
            var modal = document.getElementById(targetId);
            if (!modal) return;

            // Close all other open previews first
            document.querySelectorAll('.fc-preview-modal:not([hidden])').forEach(function(m) {
                if (m.id !== targetId) m.setAttribute('hidden', '');
            });

            modal.removeAttribute('hidden');
            document.body.classList.add('overflow-hidden');

            // Focus the card inside
            var card = modal.querySelector('.fc-preview-card');
            if (card) card.focus({ preventScroll: true });
        });
    });

    // Close via [data-preview-close]
    document.querySelectorAll('[data-preview-close]').forEach(function(el) {
        el.addEventListener('click', function() {
            var targetId = this.getAttribute('data-preview-close');
            var modal = document.getElementById(targetId);
            if (modal) {
                modal.setAttribute('hidden', '');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var openModal = document.querySelector('.fc-preview-modal:not([hidden])');
            if (openModal) {
                openModal.setAttribute('hidden', '');
                document.body.classList.remove('overflow-hidden');
            }
        }
    });
})();
</script>
<?= $this->endSection() ?>
