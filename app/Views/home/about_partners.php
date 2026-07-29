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

        <h2 class="thesis-title">CSPC Online Dataset Repository with an Integrated Recommender System using Content-Based Filtering Algorithm</h2>
        <p class="thesis-desc">This study developed an institutional dataset repository system designed to centralize, manage, and make accessible research datasets from thesis, capstone, and AI projects at CSPC, addressing the lack of structured data resources for academic and industry use.</p>

        <div class="thesis-grid">
            <div>
                <div class="thesis-subhead">Research Members - 3Js</div>
                <div class="member-row"><div class="member-avatar" style="background:hsl(210,60%,34%);">J1</div><span class="member-name">Joshua P. Estallo</span></div>
                <div class="member-row"><div class="member-avatar" style="background:hsl(228,60%,34%);">J2</div><span class="member-name"> Janben Aldrich H. Quiapo</span></div>
                <div class="member-row"><div class="member-avatar" style="background:hsl(246,60%,34%);">J3</div><span class="member-name">Jan Dale M. Parañal</span></div>
            </div>
            <div>
                <div style="margin-bottom:20px;">
                    <div class="thesis-subhead">Research Adviser</div>
                    <div class="adviser-name">Rosel O. Onesa</div>
                </div>
                <div>
                    <div class="thesis-subhead">Panel Chair</div>
                    <div class="panelist">Kaela Marie N. Fortuno</div>
                    <br>
                    <div class="thesis-subhead">Panel Members</div>
                    <div class="panelist">Tiffany Lyn O. Pandes</div>
                    <div class="panelist">Allan O. Ibo Jr.</div>
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
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#03558b;">F</div><div><div class="dev-name">Fernanne Hannah A. Enimedez</div></div></div>
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#0470a8;">J</div><div><div class="dev-name">Jessica Mae T. Lanuzo</div></div></div>
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#43a7db;">J</div><div><div class="dev-name">John Carlo E. Nas</div></div></div>
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#02447a;">H</div><div><div class="dev-name">Harvey Lloyd V. Palacios</div></div></div>
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#06314f;">M</div><div><div class="dev-name">Marc Justin N. Prestado</div></div></div>
            <div class="dev-contact-item"><div class="dev-avatar" style="background:#03558b;">J</div><div><div class="dev-name">John Patrick Y. Salcedo</div></div></div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
