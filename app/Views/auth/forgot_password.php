<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
    $resetLink = session()->getFlashdata('reset_link');
    $flashValidation = session()->getFlashdata('validation');
    $errors = is_array($validation ?? null) ? $validation : (is_array($flashValidation) ? $flashValidation : []);
?>

<section class="auth-stage auth-stage--recovery">
    <a class="auth-back-link" href="<?= site_url('login') ?>">
        <span class="material-symbols-rounded" aria-hidden="true">arrow_back</span>
        Back to sign in
    </a>

    <div class="auth-island auth-island--recovery">
        <aside class="auth-intro-panel">
            <div class="auth-heading">
                <p class="eyebrow">Account Recovery</p>
                <h1>Find your way back in.</h1>
                <p>Request a short-lived link to create a new password for an approved repository account.</p>
            </div>

            <div class="auth-policy-copy">
                <p class="eyebrow">Before you begin</p>
                <p>Enter the email address issued by the repository administrator. Google accounts recover access through my.cspc.edu.ph.</p>
            </div>
        </aside>

        <section class="auth-panel auth-recovery-panel" aria-labelledby="recovery-heading">
            <div class="auth-path-heading">
                <span class="material-symbols-rounded" aria-hidden="true">lock_reset</span>
                <div>
                    <p class="tag">Password Reset</p>
                    <h2 id="recovery-heading">Request a reset link</h2>
                    <p class="muted">The link expires after 30 minutes and can only be used once.</p>
                </div>
            </div>

            <form class="auth-form" method="post" action="<?= site_url('forgot-password') ?>" novalidate>
                <?= csrf_field() ?>
                <div class="auth-field">
                    <label for="email">Account email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="<?= old('email') ?>"
                        autocomplete="email"
                        inputmode="email"
                        placeholder="name@example.edu"
                        aria-describedby="email-help <?= isset($errors['email']) ? 'email-error' : '' ?>"
                        <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>
                    >
                    <p class="field-help" id="email-help">Use the email assigned to your repository password account.</p>
                    <?php if (isset($errors['email'])): ?><p class="field-error" id="email-error"><?= esc($errors['email']) ?></p><?php endif; ?>
                </div>

                <button class="button auth-submit" type="submit">PREPARE RESET LINK</button>
            </form>

            <?php if ($resetLink): ?>
                <div class="auth-recovery-note" role="status">
                    <span class="material-symbols-rounded" aria-hidden="true">developer_mode</span>
                    <div>
                        <strong>Development reset link ready</strong>
                        <a href="<?= esc($resetLink) ?>"><?= esc($resetLink) ?></a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="auth-footnotes">
                <p>Remembered your password? <a href="<?= site_url('login') ?>">Sign in</a></p>
            </div>
        </section>
    </div>
</section>
<?= $this->endSection() ?>
