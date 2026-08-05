<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
    $flashValidation = session()->getFlashdata('validation');
    $errors = is_array($validation ?? null) ? $validation : (is_array($flashValidation) ? $flashValidation : []);
    $roleLabels = [
        'user' => 'Contributor',
        'ethics_reviewer' => 'Ethics Reviewer',
        'technical_reviewer' => 'Technical Reviewer',
        'repository_administrator' => 'Repository Administrator',
    ];
    $isGoogleAccount = strtolower(trim((string) ($user['auth_provider'] ?? 'local'))) === 'google';
    $authProviderLabel = $isGoogleAccount ? 'Google sign-in' : 'Password sign-in';
?>

<section class="hero-panel profile-hero">
    <div class="shell">
        <p class="eyebrow">Account Settings</p>
        <h1>Profile and sign-in details</h1>
        <p class="lead">Review your repository identity and manage the sign-in method assigned to your account.</p>
    </div>
</section>

<section class="shell content-section profile-settings-shell">
    <aside class="panel profile-summary-card">
        <div class="profile-avatar" aria-hidden="true"><?= esc(strtoupper(substr((string) ($user['name'] ?? 'U'), 0, 1))) ?></div>
        <div>
            <p class="tag">Signed in as</p>
            <h2><?= esc($user['name'] ?? 'Repository user') ?></h2>
            <p class="muted"><?= esc($user['email'] ?? '') ?></p>
        </div>

        <dl class="profile-meta-list">
            <div>
                <dt>Status</dt>
                <dd><span class="status-chip status-<?= esc((string) ($user['status'] ?? 'inactive')) ?>"><?= esc(ucfirst((string) ($user['status'] ?? 'inactive'))) ?></span></dd>
            </div>
            <div>
                <dt>Roles</dt>
                <dd class="profile-role-list">
                    <?php foreach ($roles as $role): ?>
                        <span><?= esc($roleLabels[$role] ?? ucwords(str_replace('_', ' ', $role))) ?></span>
                    <?php endforeach; ?>
                </dd>
            </div>
            <div>
                <dt>Sign-in method</dt>
                <dd><span class="auth-provider-chip <?= $isGoogleAccount ? 'is-google' : 'is-local' ?>"><span class="material-symbols-rounded" aria-hidden="true"><?= $isGoogleAccount ? 'verified_user' : 'password' ?></span><?= esc($authProviderLabel) ?></span></dd>
            </div>
            <div>
                <dt>Last login</dt>
                <dd><?= ! empty($user['last_login_at']) ? esc(date('M j, Y g:i A', strtotime((string) $user['last_login_at']))) : 'Not recorded' ?></dd>
            </div>
            <div>
                <dt>Account created</dt>
                <dd><?= ! empty($user['created_at']) ? esc(date('M j, Y', strtotime((string) $user['created_at']))) : 'Not recorded' ?></dd>
            </div>
        </dl>
    </aside>

    <section class="form-card profile-settings-card">
        <div class="panel-head">
            <div>
                <p class="tag"><?= $isGoogleAccount ? 'Google-managed identity' : 'Editable Details' ?></p>
                <h2><?= $isGoogleAccount ? 'Verified account details' : 'Update your profile' ?></h2>
            </div>
        </div>

        <form class="profile-settings-form" method="post" action="<?= site_url('account/settings') ?>" novalidate>
            <?= csrf_field() ?>

            <label for="name">Full name</label>
            <input
                id="name"
                name="name"
                value="<?= $isGoogleAccount ? esc((string) ($user['name'] ?? ''), 'attr') : old('name', (string) ($user['name'] ?? '')) ?>"
                autocomplete="name"
                class="<?= isset($errors['name']) ? 'field-error__input' : '' ?>"
                aria-describedby="name-help <?= isset($errors['name']) ? 'name-error' : '' ?>"
                <?= $isGoogleAccount ? 'readonly' : '' ?>
                <?= isset($errors['name']) ? 'aria-invalid="true"' : '' ?>
            >
            <p class="field-help" id="name-help"><?= $isGoogleAccount ? 'This name is supplied by your verified Google account and refreshes after Google sign-in.' : 'This name appears on your submissions, reviews, and account menus.' ?></p>
            <?php if (isset($errors['name'])): ?><p class="field-error" id="name-error"><?= esc($errors['name']) ?></p><?php endif; ?>

            <label for="email">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="<?= $isGoogleAccount ? esc((string) ($user['email'] ?? ''), 'attr') : old('email', (string) ($user['email'] ?? '')) ?>"
                autocomplete="email"
                inputmode="email"
                class="<?= isset($errors['email']) ? 'field-error__input' : '' ?>"
                aria-describedby="email-help <?= isset($errors['email']) ? 'email-error' : '' ?>"
                <?= $isGoogleAccount ? 'readonly' : '' ?>
                <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>
            >
            <p class="field-help" id="email-help"><?= $isGoogleAccount ? 'This verified Google email is managed by your my.cspc.edu.ph sign-in.' : 'Use the email assigned to your repository password credentials.' ?></p>
            <?php if (isset($errors['email'])): ?><p class="field-error" id="email-error"><?= esc($errors['email']) ?></p><?php endif; ?>

            <?php if ($isGoogleAccount): ?>
                <div class="profile-password-panel profile-password-panel--google">
                    <p class="tag">Google-managed sign-in</p>
                    <p class="muted">This account does not use a repository password. Password changes and recovery are handled through your my.cspc.edu.ph Google account.</p>
                </div>
            <?php else: ?>
                <div class="profile-password-panel">
                    <p class="tag">Password sign-in</p>
                    <p class="muted">Leave these fields blank to keep your current password.</p>

                    <label for="current_password">Current password</label>
                    <input
                        id="current_password"
                        type="password"
                        name="current_password"
                        autocomplete="current-password"
                        class="<?= isset($errors['current_password']) ? 'field-error__input' : '' ?>"
                        aria-describedby="<?= isset($errors['current_password']) ? 'current-password-error' : '' ?>"
                        <?= isset($errors['current_password']) ? 'aria-invalid="true"' : '' ?>
                    >
                    <?php if (isset($errors['current_password'])): ?><p class="field-error" id="current-password-error"><?= esc($errors['current_password']) ?></p><?php endif; ?>

                    <label for="new_password">New password</label>
                    <input
                        id="new_password"
                        type="password"
                        name="new_password"
                        autocomplete="new-password"
                        class="<?= isset($errors['new_password']) ? 'field-error__input' : '' ?>"
                        aria-describedby="new-password-help <?= isset($errors['new_password']) ? 'new-password-error' : '' ?>"
                        <?= isset($errors['new_password']) ? 'aria-invalid="true"' : '' ?>
                    >
                    <p class="field-help" id="new-password-help">Use at least 8 characters.</p>
                    <?php if (isset($errors['new_password'])): ?><p class="field-error" id="new-password-error"><?= esc($errors['new_password']) ?></p><?php endif; ?>

                    <label for="new_password_confirm">Confirm new password</label>
                    <input
                        id="new_password_confirm"
                        type="password"
                        name="new_password_confirm"
                        autocomplete="new-password"
                        class="<?= isset($errors['new_password_confirm']) ? 'field-error__input' : '' ?>"
                        aria-describedby="<?= isset($errors['new_password_confirm']) ? 'new-password-confirm-error' : '' ?>"
                        <?= isset($errors['new_password_confirm']) ? 'aria-invalid="true"' : '' ?>
                    >
                    <?php if (isset($errors['new_password_confirm'])): ?><p class="field-error" id="new-password-confirm-error"><?= esc($errors['new_password_confirm']) ?></p><?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="profile-settings-actions">
                <?php if ($isGoogleAccount): ?>
                    <a class="button" href="<?= site_url('dashboard') ?>">Back to dashboard</a>
                <?php else: ?>
                    <a class="button secondary" href="<?= site_url('dashboard') ?>">Cancel</a>
                    <button class="button" type="submit">Save settings</button>
                <?php endif; ?>
            </div>
        </form>
    </section>
</section>
<?= $this->endSection() ?>
