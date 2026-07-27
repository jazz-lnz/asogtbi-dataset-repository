<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Banner -->
<header class="banner">
    <div class="grid-overlay" style="--grid-color: rgba(255,255,255,0.07);"></div>
    <div class="banner-inner">
        <div class="banner-eyebrow">About Us</div>
        <h1 class="banner-title">Partners &amp; Recognition</h1>
        <p class="banner-lead">The institutions and individuals behind the ASOG TBI Dataset Repository — from founding partners to the development team.</p>
    </div>
    <div class="banner-fade"></div>
</header>

<!-- Partners -->
<section class="partners-section">
    <div class="partners-inner">
        <div class="partners-head">
            <div class="eyebrow">Our Partners</div>
            <h2 class="section-title">Institutional Partners</h2>
            <p>Organizations that founded, sponsor, and sustain the repository.</p>
        </div>

        <div class="partners-grid">
            <?php $partners = [
                ['short' => 'ASOG TBI', 'name' => 'ASOG Technology Business Incubator', 'role' => 'Platform Sponsor & Research Host', 'desc' => 'An incubator supporting technology-based startups and research ventures at CSPC. ASOG TBI co-owns the repository and governs dataset ethics and access policies.', 'logo' => 'ASOG-TBI_full-colored_stacked-white.png'],
                ['short' => 'CCS · CSPC', 'name' => 'College of Computer Studies — CSPC', 'role' => 'Academic Partner', 'desc' => 'The academic unit that produced the datasets and provided the development team. CCS faculty validate dataset quality and provide research mentorship.', 'logo' => 'ccs-logo.png'],
                ['short' => 'REB', 'name' => 'Research Ethics Board', 'role' => 'Ethics Regulatory Body', 'desc' => 'Reviews and approves all datasets for ethical compliance before publication, ensuring participant privacy, data integrity, and responsible research practices.', 'icon' => 'verified_user'],
                ['short' => 'PCIEERD', 'name' => 'Philippine Council for Industry, Energy, and Emerging Technology Research and Development', 'role' => 'Research Funding Agency', 'desc' => 'DOST council supporting industry-aligned research in the Philippines. Provides research funding frameworks that align with the repository\'s goals for applied data science.', 'logo' => 'pcieerd.png'],
                ['short' => 'DOST V', 'name' => 'DOST Regional Office V — Bicol', 'role' => 'Regional Science Authority', 'desc' => 'The regional arm of DOST in the Bicol region, supporting science and technology initiatives including research data management and academic-industry collaboration.', 'logo' => 'dost-region5.png'],
            ]; ?>
            <?php foreach ($partners as $partner): ?>
            <div class="partner-card">
                <div class="partner-top">
                    <?php if (!empty($partner['logo'])): ?>
                        <div class="partner-logo"><img src="<?= base_url('assets/img/' . $partner['logo']) ?>" alt="<?= esc($partner['short']) ?> logo"></div>
                    <?php else: ?>
                        <div class="partner-icon"><span class="material-symbols-rounded" style="font-size:26px;"><?= $partner['icon'] ?></span></div>
                    <?php endif; ?>
                    <div>
                        <div class="partner-short"><?= esc($partner['short']) ?></div>
                        <div class="partner-name"><?= esc($partner['name']) ?></div>
                    </div>
                </div>
                <div class="partner-role"><?= esc($partner['role']) ?></div>
                <p class="partner-desc"><?= esc($partner['desc']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Thesis Origin -->
<section class="thesis-section">
    <div class="grid-overlay grid-overlay--full" style="--grid-color: rgba(255,255,255,0.05);"></div>
    <div class="thesis-inner">
        <div class="thesis-label-row">
            <div class="thesis-label-icon"><span class="material-symbols-rounded" style="font-size:22px;">menu_book</span></div>
            <div>
                <div class="thesis-label-eyebrow">Thesis Origin</div>
                <div class="thesis-label-sub">Camarines Sur Polytechnic Colleges — College of Computer Studies · 2025–2026</div>
            </div>
        </div>

        <h2 class="thesis-title">Institutional Dataset Repository for Academic Research</h2>
        <p class="thesis-desc">This study developed an institutional dataset repository system designed to centralize, manage, and make accessible research datasets from thesis, capstone, and AI projects at CSPC, addressing the lack of structured data resources for academic and industry use.</p>

        <div class="thesis-grid">
            <div>
                <div class="thesis-subhead">Research Members</div>
                <div class="member-row"><div class="member-avatar" style="background:hsl(210,60%,34%);">M1</div><span class="member-name">Member 1</span></div>
                <div class="member-row"><div class="member-avatar" style="background:hsl(228,60%,34%);">M2</div><span class="member-name">Member 2</span></div>
                <div class="member-row"><div class="member-avatar" style="background:hsl(246,60%,34%);">M3</div><span class="member-name">Member 3</span></div>
            </div>
            <div>
                <div style="margin-bottom:20px;">
                    <div class="thesis-subhead">Research Adviser</div>
                    <div class="adviser-name">[Research Adviser]</div>
                </div>
                <div>
                    <div class="thesis-subhead">Panel Members</div>
                    <div class="panelist">[Panelist 1]</div>
                    <div class="panelist">[Panelist 2]</div>
                    <div class="panelist">[Panelist 3]</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Developers -->
<section class="devs-section">
    <div class="devs-inner">
        <div class="devs-head">
            <h3>Meet the Developers</h3>
            <span>2026 DOST-SEI PTP Scholar-Trainees</span>
        </div>
        <div class="dev-contact-list">
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#03558b;">I1</div><div><div class="dev-name">Intern 1</div><div class="dev-role">Frontend Developer</div></div></div>
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#0470a8;">I2</div><div><div class="dev-name">Intern 2</div><div class="dev-role">UI/UX Designer</div></div></div>
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#43a7db;">I3</div><div><div class="dev-name">Intern 3</div><div class="dev-role">Backend Developer</div></div></div>
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#02447a;">I4</div><div><div class="dev-name">Intern 4</div><div class="dev-role">Database Engineer</div></div></div>
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#06314f;">I5</div><div><div class="dev-name">Intern 5</div><div class="dev-role">Systems Analyst</div></div></div>
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#03558b;">I6</div><div><div class="dev-name">Intern 6</div><div class="dev-role">QA &amp; Documentation</div></div></div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
