<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
    $flashValidation = session()->getFlashdata('validation');
    $errors = is_array($validation ?? null) ? $validation : (is_array($flashValidation) ? $flashValidation : []);
?>

<section class="auth-stage auth-stage--login">
    <a class="auth-back-link" href="<?= site_url('datasets') ?>">
        <span class="material-symbols-rounded" aria-hidden="true">arrow_back</span>
        Browse as guest
    </a>

    <div class="auth-island auth-island--workspace">
        <aside class="auth-intro-panel">
            <div class="auth-heading">
                <h1>Sign in to your workspace</h1>
                <p>Access dataset uploads, reviewer queues, citations, and protected dataset tools through your account.</p>
            </div>

            <div class="auth-policy-copy">
                <p class="eyebrow">Access Options</p>
                <p>Use your <strong>my.cspc.edu.ph</strong> Google account, or use issued password credentials for approved external access. Need credentials? <a href="<?= site_url('register') ?>">Request access.</a></p>
            </div>

        </aside>

        <section class="auth-panel" aria-labelledby="login-heading">
            <h2 class="sr-only" id="login-heading">SIGN IN</h2>

            <div class="auth-path-heading">
                <span class="material-symbols-rounded" aria-hidden="true">password</span>
                <div>
                    <p class="tag">Password account</p>
                    <p class="muted">Use this for repository accounts created with an email and password.</p>
                </div>
            </div>

            <form class="auth-form" method="post" action="<?= site_url('login') ?>" novalidate>
                <?= csrf_field() ?>
                <?php if (! empty($returnTo)): ?>
                    <input type="hidden" name="return_to" value="<?= esc($returnTo, 'attr') ?>">
                <?php endif; ?>

                <div class="auth-field">
                    <label for="email">Email</label>
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
                    <p class="field-help" id="email-help">Use the repository account assigned to your contributor or maintainer role.</p>
                    <?php if (isset($errors['email'])): ?><p class="field-error" id="email-error"><?= esc($errors['email']) ?></p><?php endif; ?>
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        aria-describedby="password-help <?= isset($errors['password']) ? 'password-error' : '' ?>"
                        <?= isset($errors['password']) ? 'aria-invalid="true"' : '' ?>
                    >
                    <p class="field-help" id="password-help">Passwords are case-sensitive.</p>
                    <?php if (isset($errors['password'])): ?><p class="field-error" id="password-error"><?= esc($errors['password']) ?></p><?php endif; ?>
                </div>

                <button class="button auth-submit" type="submit">SIGN IN</button>
            </form>

            <div class="auth-divider"><span>or</span></div>

            <div class="auth-path-heading auth-path-heading--google">
                <span class="material-symbols-rounded" aria-hidden="true">verified_user</span>
                <div>
                    <p class="tag">Google account</p>
                    <p class="muted">Use this for verified my.cspc.edu.ph Google sign-in.</p>
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

            <div class="auth-footnotes">
                <p><a href="<?= site_url('forgot-password') ?>">Forgot password?</a></p>
            </div>

            <details class="auth-demo-note">
                <summary>Local demo accounts</summary>
                <p>For development walkthroughs after migrations and `MvpSeeder`; not part of the production sign-in experience.</p>
                <ul class="auth-demo-list">
                    <?php foreach (($demoAccounts ?? []) as $account): ?>
                        <li>
                            <strong><?= esc($account['role']) ?></strong>
                            <span><?= esc($account['email']) ?></span>
                            <code><?= esc($account['password']) ?></code>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </details>
        </section>
    </div>
</section>
<?= $this->endSection() ?>
