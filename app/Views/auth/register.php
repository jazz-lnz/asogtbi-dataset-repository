<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
    $flashValidation = session()->getFlashdata('validation');
    $errors = is_array($validation ?? null) ? $validation : (is_array($flashValidation) ? $flashValidation : []);
    $contactEmail = (string) ($accessContactEmail ?? 'repository@cspc.edu.ph');
    $credentialMailto = 'mailto:' . $contactEmail . '?subject=' . rawurlencode('ASOG Dataset Repository credential request');
?>

<section class="auth-stage auth-stage--register">
    <a class="auth-back-link" href="<?= site_url('datasets') ?>">
        <span class="material-symbols-rounded" aria-hidden="true">arrow_back</span>
        Browse as guest
    </a>

    <div class="auth-island auth-island--workspace">
        <aside class="auth-intro-panel">
            <div class="auth-heading">
                <h1>Request repository access</h1>
                <p>CSPC users can enter with Google. Password credentials are reserved for approved collaborators who need authenticated access without a CSPC Google account.</p>
            </div>

            <div class="auth-policy-copy">
                <p class="eyebrow">Credential Issuance</p>
                <p>Password accounts are created by repository administrators after request review. Already have access? <a href="<?= site_url('login') ?>">Sign in here.</a></p>
            </div>

            <ul class="auth-policy-list">
                <?php foreach (($registerNotes ?? []) as $note): ?>
                    <li><?= esc($note) ?></li>
                <?php endforeach; ?>
            </ul>

        </aside>

        <section class="auth-panel" aria-labelledby="register-heading">
            <h2 class="sr-only" id="register-heading">Request access</h2>

            <div class="auth-path-heading auth-path-heading--google">
                <span class="material-symbols-rounded" aria-hidden="true">verified_user</span>
                <div>
                    <p class="tag">CSPC Google account</p>
                    <p class="muted">Use your verified my.cspc.edu.ph account for regular repository access.</p>
                </div>
            </div>

            <?php if (! empty($googleAuthEnabled)): ?>
                <a class="auth-google-button" href="<?= site_url('auth/google') ?>">
                    <span class="auth-google-mark" aria-hidden="true"><img src="<?= base_url('assets/img/brand/google-g.svg') ?>" alt=""></span>
                    CONTINUE WITH MY.CSPC GOOGLE ACCOUNT
                </a>
            <?php else: ?>
                <button class="auth-google-button" type="button" disabled aria-disabled="true">
                    <span class="auth-google-mark" aria-hidden="true"><img src="<?= base_url('assets/img/brand/google-g.svg') ?>" alt=""></span>
                    CONTINUE WITH MY.CSPC GOOGLE ACCOUNT
                </button>
            <?php endif; ?>

            <div class="auth-divider"><span>or</span></div>

            <div class="auth-path-heading">
                <span class="material-symbols-rounded" aria-hidden="true">key</span>
                <div>
                    <p class="tag">Password credential</p>
                    <p class="muted">Request issued credentials for approved external download access.</p>
                </div>
            </div>

            <div class="auth-access-request">
                <p>Password accounts are not self-created. The repository team issues them after confirming the requester, purpose, and access scope.</p>
                <a class="button auth-submit" href="<?= esc($credentialMailto, 'attr') ?>">REQUEST PASSWORD CREDENTIALS</a>
                <a class="auth-inline-link" href="https://asogtbi.com/" target="_blank" rel="noopener noreferrer">Contact ASOG TBI</a>
            </div>

            <p class="auth-footnotes">Already have access? <a href="<?= site_url('login') ?>">Sign in</a></p>
        </section>
    </div>
</section>
<?= $this->endSection() ?>
