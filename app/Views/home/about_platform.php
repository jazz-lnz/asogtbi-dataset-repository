<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Banner -->
<header class="banner">
    <div class="grid-overlay" style="--grid-color: rgba(255,255,255,0.07);"></div>
    <div class="banner-inner">
        <div class="banner-eyebrow">About the Platform</div>
        <h1 class="banner-title">ASOG TBI Dataset Repository</h1>
        <p class="banner-lead">A collaborative platform between ASOG Technology Business Incubator and the College of Computer Studies — centralizing institutional research datasets for thesis, capstone, AI, and analytics projects.</p>
    </div>
    <div class="banner-fade"></div>
</header>

<!-- Stats -->
<div class="stats-wrap">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><span class="material-symbols-rounded" style="font-size:18px;">database</span></div>
            <div class="stat-value"><?= esc((string) ($publishedCount ?? 0)) ?></div>
            <div class="stat-label">Published&#10;Datasets</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><span class="material-symbols-rounded" style="font-size:18px;">description</span></div>
            <div class="stat-value">23</div>
            <div class="stat-label">Research&#10;Projects</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><span class="material-symbols-rounded" style="font-size:18px;">group</span></div>
            <div class="stat-value">31</div>
            <div class="stat-label">Active&#10;Contributors</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><span class="material-symbols-rounded" style="font-size:18px;">download</span></div>
            <div class="stat-value">189</div>
            <div class="stat-label">Total&#10;Downloads</div>
        </div>
    </div>
</div>

<!-- Mission -->
<section class="mission">
    <div class="mission-inner">
        <div class="eyebrow">Our Mission</div>
        <h2 class="section-title">Built for Institutional Research</h2>
        <p class="section-lead">The ASOG TBI Dataset Repository addresses the lack of accessible, well-documented datasets for students, faculty researchers, and ASOG TBI incubatees. By centralizing datasets from completed thesis, capstone, and AI projects, it promotes data reuse, proper citation, research reproducibility, and innovation in Camarines Sur.</p>
    </div>
</section>

<!-- Platform Features -->
<section class="features-section">
    <div class="grid-overlay" style="--grid-color: rgba(255,255,255,0.04);"></div>
    <div class="features-inner">
        <div class="features-head">
            <div class="eyebrow" style="color: rgba(248,175,33,.8);">Platform Features</div>
            <h2>Built for<br><span>Research</span></h2>
            <p>Every tool a researcher needs — from discovery to citation — in one secure platform.</p>
        </div>
        <div class="carousel-area" role="region" aria-roledescription="carousel" aria-label="Platform Features" tabindex="0">
            <div class="carousel-track" id="features-carousel" role="list">
                <div class="feature-card" role="listitem" aria-roledescription="slide">
                    <span class="card-num">01</span>
                    <div class="feature-icon a"><span class="material-symbols-rounded" style="font-size:17px;">search</span></div>
                    <h3>Smart Search</h3>
                    <p>Find datasets by title, tag, keyword, or category with instant results.</p>
                </div>
                <div class="feature-card" role="listitem" aria-roledescription="slide">
                    <span class="card-num">02</span>
                    <div class="feature-icon b"><span class="material-symbols-rounded" style="font-size:17px;">filter_list</span></div>
                    <h3>Type Filtering</h3>
                    <p>Filter by data type: Text, Image, Audio, Video, Tabular, and more.</p>
                </div>
                <div class="feature-card" role="listitem" aria-roledescription="slide">
                    <span class="card-num">03</span>
                    <div class="feature-icon a"><span class="material-symbols-rounded" style="font-size:17px;">format_quote</span></div>
                    <h3>Citation &amp; BibTeX</h3>
                    <p>Generate copy-ready citations and BibTeX references for any dataset.</p>
                </div>
                <div class="feature-card" role="listitem" aria-roledescription="slide">
                    <span class="card-num">04</span>
                    <div class="feature-icon b"><span class="material-symbols-rounded" style="font-size:17px;">download</span></div>
                    <h3>Secure Download</h3>
                    <p>Download approved datasets as ZIP files with role-based access control.</p>
                </div>
                <div class="feature-card" role="listitem" aria-roledescription="slide">
                    <span class="card-num">05</span>
                    <div class="feature-icon a"><span class="material-symbols-rounded" style="font-size:17px;">recommend</span></div>
                    <h3>Recommendations</h3>
                    <p>Discover related datasets via metadata-based similarity scoring.</p>
                </div>
                <div class="feature-card" role="listitem" aria-roledescription="slide">
                    <span class="card-num">06</span>
                    <div class="feature-icon b"><span class="material-symbols-rounded" style="font-size:17px;">shield</span></div>
                    <h3>Ethics Review</h3>
                    <p>Every dataset undergoes privacy and technical review before publication.</p>
                </div>
            </div>
            <button type="button" class="carousel-btn carousel-btn--prev" aria-label="Previous feature" id="features-prev">&#8249;</button>
            <button type="button" class="carousel-btn carousel-btn--next" aria-label="Next feature" id="features-next">&#8250;</button>
        </div>
    </div>
    <script>
    (function() {
        var track = document.getElementById('features-carousel');
        var prevBtn = document.getElementById('features-prev');
        var nextBtn = document.getElementById('features-next');
        var area = track ? track.closest('.carousel-area') : null;
        if (!track || !prevBtn || !nextBtn) return;

        function scrollTrack(direction) {
            var card = track.querySelector('.feature-card');
            if (!card) return;
            var cardW = card.offsetWidth + 12;
            track.scrollBy({ left: direction * cardW, behavior: 'smooth' });
        }

        function updateButtons() {
            var scrollLeft = track.scrollLeft;
            var maxScroll = track.scrollWidth - track.clientWidth;
            prevBtn.style.opacity = scrollLeft <= 5 ? '.35' : '1';
            prevBtn.style.pointerEvents = scrollLeft <= 5 ? 'none' : 'auto';
            nextBtn.style.opacity = scrollLeft >= maxScroll - 5 ? '.35' : '1';
            nextBtn.style.pointerEvents = scrollLeft >= maxScroll - 5 ? 'none' : 'auto';
        }

        prevBtn.addEventListener('click', function() { scrollTrack(-1); });
        nextBtn.addEventListener('click', function() { scrollTrack(1); });
        track.addEventListener('scroll', updateButtons, { passive: true });
        if ('onscrollend' in track) {
            track.addEventListener('scrollend', updateButtons);
        }

        if (area) {
            area.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') { scrollTrack(-1); }
                if (e.key === 'ArrowRight') { scrollTrack(1); }
            });
        }

        /* Touch/swipe support */
        var touchStartX = 0;
        var touchStartY = 0;
        var touchDeltaX = 0;
        var isSwiping = false;

        track.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            touchDeltaX = 0;
            isSwiping = false;
        }, { passive: true });

        track.addEventListener('touchmove', function(e) {
            var dx = e.touches[0].clientX - touchStartX;
            var dy = e.touches[0].clientY - touchStartY;
            if (!isSwiping && Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 10) {
                isSwiping = true;
            }
            if (isSwiping) {
                touchDeltaX = dx;
            }
        }, { passive: true });

        track.addEventListener('touchend', function() {
            if (isSwiping) {
                var threshold = 50;
                if (touchDeltaX < -threshold) {
                    scrollTrack(1);
                } else if (touchDeltaX > threshold) {
                    scrollTrack(-1);
                }
            }
            touchDeltaX = 0;
            isSwiping = false;
        }, { passive: true });

        window.addEventListener('resize', updateButtons);
        updateButtons();
    })();
    </script>
</section>

<!-- Workflow -->
<section class="workflow">
    <div class="grid-overlay grid-overlay--full" style="--grid-color: rgba(255,255,255,0.05);"></div>
    <div class="workflow-inner">
        <div class="workflow-head">
            <div class="eyebrow">Workflow</div>
            <h2 class="section-title" style="margin-bottom:0;">How the Repository Works</h2>
            <p>From submission to discovery — a structured review and approval process.</p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">01</div>
                <h3>Submit Dataset</h3>
                <p>Upload your dataset as a ZIP file with complete metadata: title, description, category, and tags.</p>
            </div>
            <div class="step-card">
                <div class="step-num">02</div>
                <h3>Ethics &amp; Technical Review</h3>
                <p>Reviewed for privacy compliance and data quality before it's made available.</p>
            </div>
            <div class="step-card">
                <div class="step-num">03</div>
                <h3>Admin Approval</h3>
                <p>Repository administrators publish approved datasets and set the appropriate access level.</p>
            </div>
            <div class="step-card">
                <div class="step-num">04</div>
                <h3>Discover &amp; Cite</h3>
                <p>Users can search, filter, download, and generate citations for any published dataset.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="grid-overlay grid-overlay--full" style="--grid-color: rgba(255,255,255,0.05);"></div>
    <div class="cta-inner">
        <div>
            <div class="cta-eyebrow">Get Involved</div>
            <h2>Start Contributing or<br>Access the Datasets</h2>
            <p>Whether you're a researcher with data to share or a student looking to explore, the repository is open for you. Create an account to submit datasets or log in to access restricted collections.</p>
        </div>
        <div class="cta-actions">
            <a href="<?= site_url('register') ?>" class="btn btn-gold"><span class="material-symbols-rounded" style="font-size:16px;">person_add</span> Sign Up</a>
            <a href="<?= site_url('login') ?>" class="btn btn-ghost"><span class="material-symbols-rounded" style="font-size:16px;">login</span> Log In</a>
        </div>
    </div>
</section>
<?= $this->endSection() ?>