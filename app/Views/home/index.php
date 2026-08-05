<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="home-hero-section">
    <div class="home-hero-grid" aria-hidden="true"></div>

    <div class="home-hero-content">
        <img class="home-hero-logo" src="<?= base_url('assets/img/asog-data-repo-logo.png') ?>" alt="ASOG TBI Dataset Repository logo">
        <div class="home-hero-kicker">Institutional</div>
        <h1>Dataset Repository</h1>
        <p class="home-hero-lead">A centralized platform for discovering, citing, and downloading institutional research datasets from CSPC and ASOG TBI.</p>

        <form class="home-hero-search" method="get" action="<?= site_url('datasets') ?>">
            <label class="sr-only" for="home-search">Search datasets</label>
            <input id="home-search" type="search" name="q" placeholder="Search datasets by title, tag, or category…">
            <button type="submit" aria-label="Search datasets"><span class="material-symbols-rounded" aria-hidden="true">search</span></button>
        </form>

        <div class="home-hero-features">
            <div><span class="material-symbols-rounded" aria-hidden="true">school</span><strong>Academic Origins</strong><small>Sourced from research, theses, and capstone projects.</small></div>
            <div><span class="material-symbols-rounded" aria-hidden="true">fact_check</span><strong>Fully Documented</strong><small>Complete metadata for clear, reusable research.</small></div>
            <div><span class="material-symbols-rounded" aria-hidden="true">verified_user</span><strong>Ethically Reviewed</strong><small>Reviewed for quality, privacy, and responsible access.</small></div>
        </div>

        <a class="home-hero-scroll" href="#repo-snapshot">Browse Datasets <span class="material-symbols-rounded" aria-hidden="true">keyboard_arrow_down</span></a>
    </div>
</section>

<section id="browse-preview" class="home-featured">
    <div class="shell">
        <!-- Platform Snapshot with heading + dividers -->
        <div id="repo-snapshot" class="home-snapshot-section">
            <div class="home-snapshot-head">
                <p class="home-snapshot-kicker">Platform Overview</p>
                <h2>Repository at a Glance</h2>
            </div>
            <div class="snapshot-stats">
                <div class="snapshot-stat">
                    <span class="snapshot-number"><?= esc((string) ($publishedCount ?? 0)) ?></span>
                    <span class="snapshot-label">Public datasets</span>
                </div>
                <div class="snapshot-divider" aria-hidden="true"></div>
                <div class="snapshot-stat">
                    <span class="snapshot-number">5</span>
                    <span class="snapshot-label">Canonical data types</span>
                </div>
                <div class="snapshot-divider" aria-hidden="true"></div>
                <div class="snapshot-stat">
                    <span class="snapshot-number">ZIP</span>
                    <span class="snapshot-label">Protected dataset uploads</span>
                </div>
            </div>
        </div>

        <!-- Sparkle dots (12 total) -->
        <i class="home-sparkle" aria-hidden="true" style="top:14%;left:6%;--dur:2.2s;--delay:0s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:34%;left:14%;--dur:1.8s;--delay:0.6s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:62%;left:4%;--dur:2.6s;--delay:1.1s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:80%;left:18%;--dur:2.0s;--delay:0.3s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:8%;left:82%;--dur:1.9s;--delay:0.9s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:28%;left:91%;--dur:2.3s;--delay:0.15s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:55%;left:88%;--dur:2.7s;--delay:0.7s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:76%;left:78%;--dur:2.1s;--delay:1.3s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:20%;left:48%;--dur:3.0s;--delay:0.5s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:70%;left:55%;--dur:1.7s;--delay:1.0s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:44%;left:30%;--dur:2.4s;--delay:0.2s"></i>
        <i class="home-sparkle" aria-hidden="true" style="top:50%;left:70%;--dur:2.0s;--delay:1.5s"></i>

        <div class="home-dataset-head">
            <p style="color:var(--gold-dark);font-size:10px;font-weight:900;letter-spacing:.18em;text-transform:uppercase;margin:0 0 6px">Repository</p>
            <h2>Featured Datasets</h2>
            <p>Recent releases and view-ranked records share one clean space, keeping the home screen focused on catalog discovery.</p>
        </div>

        <!-- Side-by-side columns: Recent | Popular -->
        <?php if (empty($featuredDatasets) && empty($popularDatasets)): ?>
            <div class="home-empty-line">
                <strong>No published datasets yet</strong>
                <p>Run migrations and seeders, or submit the first public dataset for review.</p>
            </div>
        <?php else: ?>
            <div class="featured-columns">
                <!-- Recent Uploads column -->
                <div class="fc-col">
                    <div class="fc-col-head">
                        <p class="fc-col-kicker">Recent Uploads</p>
                        <h3 class="fc-col-title">Latest additions</h3>
                    </div>
                    <?php if (! empty($featuredDatasets)): ?>
                        <div class="fc-col-list">
                            <?php foreach ($featuredDatasets as $dataset): ?>
                                <?= view('components/compact_card', ['dataset' => $dataset, 'variant' => 'recent']) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="home-empty-line">
                            <strong>No recent datasets</strong>
                            <p>Check back soon for new uploads.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Most Viewed column -->
                <div class="fc-col">
                    <div class="fc-col-head">
                        <p class="fc-col-kicker">Most Viewed</p>
                        <h3 class="fc-col-title">Popular datasets</h3>
                    </div>
                    <?php if (! empty($popularDatasets)): ?>
                        <div class="fc-col-list">
                            <?php foreach ($popularDatasets as $dataset): ?>
                                <?= view('components/compact_card', ['dataset' => $dataset, 'variant' => 'popular']) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color:var(--public-muted);font-size:13px;margin:0;">No view data available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="home-featured-cta">
            <a class="button" href="<?= site_url('datasets') ?>" aria-label="Explore the full dataset catalog"><span class="material-symbols-rounded" aria-hidden="true">grid_view</span> Explore the full catalog</a>
        </div>
    </div>
</section>

<section class="home-cta-section">
    <div class="home-hero-grid" aria-hidden="true"></div>
    <div>
        <div class="home-kicker">Contribute</div>
        <h2>Have a dataset to contribute?</h2>
        <p>Share your thesis, capstone, or research data with the CSPC and ASOG TBI research community.</p>
        <div class="actions">
            <a class="button" href="<?= site_url(session()->get('user_id') ? 'upload' : 'login') ?>" aria-label="Submit a new dataset for review"><span class="material-symbols-rounded" aria-hidden="true">upload</span> Submit a Dataset</a>
            <a class="button secondary" href="<?= site_url('about/platform') ?>" aria-label="Learn how the repository works"><span class="material-symbols-rounded" aria-hidden="true">info</span> Learn How It Works</a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>