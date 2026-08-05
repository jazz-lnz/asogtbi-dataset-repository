<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="auth-stage auth-stage--recovery">
    <a class="auth-back-link" href="<?= site_url('forgot-password') ?>">
        <span class="material-symbols-rounded" aria-hidden="true">arrow_back</span>
        Request another link
    </a>

    <div class="auth-island auth-island--recovery">
        <aside class="auth-intro-panel">
            <div class="auth-heading">
                <p class="eyebrow">Account Recovery</p>
                <h1><?= ! empty($isValidToken) ? 'Set a fresh password.' : 'This link has reached its limit.' ?></h1>
                <p><?= ! empty($isValidToken) ? 'Choose a new password to restore access to your repository account.' : 'Reset links are deliberately short-lived to help protect repository access.' ?></p>
            </div>

            <div class="auth-policy-copy">
                <p class="eyebrow">Recovery protection</p>
                <p>Each reset link expires after 30 minutes and can only be used once. Google accounts continue recovery through my.cspc.edu.ph.</p>
            </div>
        </aside>

        <section class="auth-panel auth-recovery-panel" aria-labelledby="reset-heading">
            <?php if (empty($isValidToken)): ?>
                <div class="auth-path-heading">
                    <span class="material-symbols-rounded" aria-hidden="true">link_off</span>
                    <div>
                        <p class="tag">Reset Link Unavailable</p>
                        <h2 id="reset-heading">Request a new link</h2>
                        <p class="muted">This link is invalid, already used, or expired.</p>
                    </div>
                </div>

                <div class="auth-invalid-state">
                    <p>The recovery request cannot continue with this link. Start again with the email assigned to your password account.</p>
                    <a class="button auth-submit" href="<?= site_url('forgot-password') ?>">REQUEST NEW LINK</a>
                </div>
            <?php else: ?>
                <div class="auth-path-heading">
                    <span class="material-symbols-rounded" aria-hidden="true">lock_reset</span>
                    <div>
                        <p class="tag">New Password</p>
                        <h2 id="reset-heading">Create a new password</h2>
                        <p class="muted">Use at least 8 characters, then confirm the same password.</p>
                    </div>
                </div>

                <form class="auth-form" method="post" action="<?= site_url('reset-password') ?>" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="email" value="<?= esc($email ?? '') ?>">
                    <input type="hidden" name="token" value="<?= esc($token ?? '') ?>">

                    <div class="auth-field">
                        <label for="password">New password</label>
                        <input id="password" type="password" name="password" autocomplete="new-password" placeholder="At least 8 characters">
                    </div>

                    <div class="auth-field">
                        <label for="password_confirm">Confirm new password</label>
                        <input id="password_confirm" type="password" name="password_confirm" autocomplete="new-password" placeholder="Repeat your password">
                    </div>

                    <button class="button auth-submit" type="submit">UPDATE PASSWORD</button>
                </form>
            <?php endif; ?>

            <div class="auth-footnotes">
                <p><a href="<?= site_url('login') ?>">Return to sign in</a></p>
            </div>
        </section>
    </div>
</section>
<?= $this->endSection() ?>
