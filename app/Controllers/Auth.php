<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\PasswordResetModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use Config\GoogleAuth;

class Auth extends BaseController
{
    private ?string $googleAccountFailure = null;

    public function login(): string
    {
        $returnTo = $this->storeIntendedDestination((string) $this->request->getGet('return_to'));

        return view('auth/login', [
            'title' => 'Sign in',
            'validation' => session()->getFlashdata('validation'),
            'returnTo' => $returnTo,
            'demoAccounts' => [
                ['role' => 'User', 'email' => 'user@example.test', 'password' => 'change-me'],
                ['role' => 'Repository Administrator', 'email' => 'admin@example.test', 'password' => 'change-me'],
                ['role' => 'Research Ethics Reviewer', 'email' => 'ethics@example.test', 'password' => 'change-me'],
                ['role' => 'Technical Reviewer', 'email' => 'technical@example.test', 'password' => 'change-me'],
            ],
            'loginChecks' => [
                'Email and password authentication for MVP-FR-01 and MVP-FR-02.',
                'Seeded demo accounts exist for local walkthroughs after migrations and seeding.',
                'Inactive accounts are blocked before a session is created.',
            ],
            'googleAuthEnabled' => config(GoogleAuth::class)->isConfigured(),
        ]);
    }

    public function attemptLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation', $this->validator->getErrors())
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $userModel = new UserModel();
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $user = $userModel->where('email', $email)->first();

        if (is_array($user) && $this->isGoogleAccount($user)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation', ['email' => 'This account uses Google sign-in. Continue with your my.cspc Google account instead.'])
                ->with('error', 'Use Google sign-in for this account.');
        }

        if (! is_array($user) || ! password_verify((string) $this->request->getPost('password'), (string) ($user['password_hash'] ?? ''))) {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation', ['email' => 'Check your email and password, then try again.'])
                ->with('error', 'The provided credentials do not match our records.');
        }

        if (($user['status'] ?? 'inactive') !== 'active') {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation', ['email' => 'This account is inactive. Contact a repository administrator for access.'])
                ->with('error', 'This account is inactive and cannot log in yet.');
        }

        $roles = $this->findUserRoleNames((int) $user['id']);

        return $this->completeLogin($userModel, $user, $roles, 'login', 'User logged into the repository.');
    }

    public function google()
    {
        $config = config(GoogleAuth::class);
        if (! $config->isConfigured()) {
            return redirect()
                ->to('/login')
                ->with('error', 'Google sign-in is not configured yet.');
        }

        $state = bin2hex(random_bytes(24));
        $this->session->set('google_oauth_state', $state);

        return redirect()->to($config->authEndpoint . '?' . http_build_query([
            'client_id' => $config->clientId,
            'redirect_uri' => $this->googleRedirectUri($config),
            'response_type' => 'code',
            'scope' => implode(' ', $config->scopes),
            'state' => $state,
            'prompt' => 'select_account',
            'access_type' => 'online',
            'include_granted_scopes' => 'true',
        ], '', '&', PHP_QUERY_RFC3986));
    }

    public function googleCallback()
    {
        $config = config(GoogleAuth::class);
        if (! $config->isConfigured()) {
            return redirect()
                ->to('/login')
                ->with('error', 'Google sign-in is not configured yet.');
        }

        $error = trim((string) $this->request->getGet('error'));
        if ($error !== '') {
            return redirect()
                ->to('/login')
                ->with('error', 'Google sign-in was cancelled or denied.');
        }

        $state = (string) $this->request->getGet('state');
        $storedState = (string) $this->session->get('google_oauth_state');
        $this->session->remove('google_oauth_state');
        if ($state === '' || $storedState === '' || ! hash_equals($storedState, $state)) {
            return redirect()
                ->to('/login')
                ->with('error', 'Google sign-in could not be verified. Please try again.');
        }

        $code = trim((string) $this->request->getGet('code'));
        if ($code === '') {
            return redirect()
                ->to('/login')
                ->with('error', 'Google did not return an authorization code.');
        }

        $token = $this->exchangeGoogleCode($config, $code);
        if (! is_array($token) || empty($token['access_token'])) {
            return redirect()
                ->to('/login')
                ->with('error', 'Google sign-in could not be completed.');
        }

        $profile = $this->fetchGoogleProfile($config, (string) $token['access_token']);
        if (! is_array($profile) || ! $this->isAllowedGoogleProfile($profile, $config)) {
            return redirect()
                ->to('/login')
                ->with('error', 'Use a verified Google Workspace account ending in @' . $config->allowedDomain . '.');
        }

        $this->googleAccountFailure = null;
        $user = $this->findOrCreateGoogleUser($profile);
        if (! is_array($user)) {
            return redirect()
                ->to('/login')
                ->with('error', $this->googleAccountFailure ?? 'Google sign-in could not store your repository account.');
        }

        if (($user['status'] ?? 'inactive') !== 'active') {
            return redirect()
                ->to('/login')
                ->with('error', 'This account is inactive and cannot log in yet.');
        }

        $roles = $this->findUserRoleNames((int) $user['id']);

        return $this->completeLogin(model(UserModel::class), $user, $roles, 'login_google', 'User logged into the repository with Google.');
    }

    public function register(): string
    {
        return view('auth/register', [
            'title' => 'Request access',
            'validation' => session()->getFlashdata('validation'),
            'registerNotes' => [
                'CSPC users sign in with their verified my.cspc.edu.ph Google account.',
                'Password credentials are issued by repository administrators for approved external collaborators.',
                'Issued password accounts can be used for authenticated dataset access without a CSPC Google account.',
            ],
            'accessContactEmail' => 'repository@cspc.edu.ph',
            'googleAuthEnabled' => config(GoogleAuth::class)->isConfigured(),
        ]);
    }

    public function forgotPassword(): string
    {
        $this->clearStaleAuthenticationPrompt();

        return view('auth/forgot_password', [
            'title' => 'Forgot Password',
        ]);
    }

    public function sendResetLink()
    {
        $rules = [
            'email' => 'required|valid_email',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->to('/forgot-password')
                ->withInput()
                ->with('validation', $this->validator->getErrors())
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $user = $this->findPasswordResetEligibleUser($email);

        if (is_array($user)) {
            $accountEmail = strtolower(trim((string) $user['email']));
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $resetModel = model(PasswordResetModel::class);

            $resetId = (int) $resetModel->insert([
                'email' => $accountEmail,
                'token_hash' => $tokenHash,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
            ], true);

            if ($resetId > 0) {
                $resetUrl = site_url('reset-password?token=' . $token . '&email=' . rawurlencode($accountEmail));
                $this->sendPasswordResetEmail($accountEmail, $resetUrl);

                if (ENVIRONMENT === 'development') {
                    $this->session->setFlashdata('reset_link', $resetUrl);
                }
            }
        }

        return redirect()
            ->to('/forgot-password')
            ->with('info', 'If that active password account exists, a reset link has been prepared.');
    }

    public function resetPassword(): string
    {
        $this->clearStaleAuthenticationPrompt();

        $token = trim((string) $this->request->getGet('token'));
        $email = trim((string) $this->request->getGet('email'));

        if ($token === '' || $email === '' || ! $this->isValidPasswordResetToken($email, $token)) {
            return view('auth/reset_password', [
                'title' => 'Reset Password',
                'token' => '',
                'email' => '',
                'isValidToken' => false,
            ]);
        }

        return view('auth/reset_password', [
            'title' => 'Reset Password',
            'token' => $token,
            'email' => $email,
            'isValidToken' => true,
        ]);
    }

    public function updatePassword()
    {
        $rules = [
            'email' => 'required|valid_email',
            'token' => 'required',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $token = trim((string) $this->request->getPost('token'));
        $reset = $this->findValidPasswordReset($email, $token);

        if (! is_array($reset)) {
            return redirect()
                ->to('/forgot-password')
                ->with('error', 'That password reset link is invalid or expired.');
        }

        $user = $this->findPasswordResetEligibleUser($email);
        if (! is_array($user)) {
            return redirect()
                ->to('/forgot-password')
                ->with('error', 'That password reset link is invalid or expired.');
        }

        model(UserModel::class)->update((int) $user['id'], [
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);
        model(PasswordResetModel::class)->update((int) $reset['id'], [
            'used_at' => date('Y-m-d H:i:s'),
        ]);
        $this->recordAudit('password_reset', 'user', (int) $user['id'], 'User password was reset.');

        return redirect()
            ->to('/login')
            ->with('info', 'Password updated. You can now log in.');
    }

    public function attemptRegister()
    {
        return redirect()
            ->to('/register')
            ->with('info', 'Password accounts are issued by repository administrators. Contact repository@cspc.edu.ph to request credentials.');
    }

    public function logout()
    {
        $userId = $this->currentUserId();
        if ($userId !== null) {
            $this->recordAudit('logout', 'user', $userId, 'User logged out of the repository.');
        }

        $this->session->destroy();

        return redirect()->to('/login');
    }

    private function findUserRoleNames(int $userId): array
    {
        $roleRows = model(UserRoleModel::class)
            ->select('roles.name')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->orderBy('roles.name', 'ASC')
            ->findAll();

        $roles = array_values(array_filter(array_map(static fn (array $row): string => (string) ($row['name'] ?? ''), $roleRows)));

        return $roles !== [] ? $roles : ['user'];
    }

    /**
     * @param array<string, mixed> $user
     * @param array<int, string> $roles
     */
    private function completeLogin(UserModel $userModel, array $user, array $roles, string $auditAction, string $auditDetails)
    {
        $userId = (int) $user['id'];
        $now = date('Y-m-d H:i:s');
        $isFirstLogin = empty($user['first_login_at']);

        $this->session->set([
            'user_id' => $userId,
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'role' => $roles[0] ?? 'user',
            'roles' => $roles,
            'is_first_login' => $isFirstLogin,
        ]);

        $updates = ['last_login_at' => $now];
        if ($isFirstLogin) {
            $updates['first_login_at'] = $now;
        }

        $userModel->update($userId, $updates);
        $this->recordAudit($auditAction, 'user', $userId, $auditDetails);

        $message = $isFirstLogin
            ? 'Welcome, ' . $user['name'] . '. Your repository account is ready.'
            : 'Welcome back, ' . $user['name'] . '.';

        return redirect()
            ->to($this->postLoginDestination($roles))
            ->with('info', $message);
    }

    private function googleRedirectUri(GoogleAuth $config): string
    {
        return $config->redirectUri !== '' ? $config->redirectUri : site_url('auth/google/callback');
    }

    /**
     * @return bool|string
     */
    private function googleHttpVerifyOption(GoogleAuth $config)
    {
        return $config->caBundle !== '' ? $config->caBundle : true;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function exchangeGoogleCode(GoogleAuth $config, string $code): ?array
    {
        try {
            $response = service('curlrequest')->post($config->tokenEndpoint, [
                'form_params' => [
                    'code' => $code,
                    'client_id' => $config->clientId,
                    'client_secret' => $config->clientSecret,
                    'redirect_uri' => $this->googleRedirectUri($config),
                    'grant_type' => 'authorization_code',
                ],
                'http_errors' => false,
                'timeout' => 15,
                'verify' => $this->googleHttpVerifyOption($config),
            ]);
        } catch (\Throwable $exception) {
            log_message('error', 'Google token exchange failed: {message}', ['message' => $exception->getMessage()]);

            return null;
        }

        if ($response->getStatusCode() !== 200) {
            log_message('error', 'Google token exchange returned HTTP {status}: {body}', [
                'status' => $response->getStatusCode(),
                'body' => $response->getBody(),
            ]);

            return null;
        }

        $data = json_decode($response->getBody(), true);

        return is_array($data) ? $data : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchGoogleProfile(GoogleAuth $config, string $accessToken): ?array
    {
        try {
            $response = service('curlrequest')->get($config->userInfoEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'http_errors' => false,
                'timeout' => 15,
                'verify' => $this->googleHttpVerifyOption($config),
            ]);
        } catch (\Throwable $exception) {
            log_message('error', 'Google profile request failed: {message}', ['message' => $exception->getMessage()]);

            return null;
        }

        if ($response->getStatusCode() !== 200) {
            log_message('error', 'Google profile request returned HTTP {status}: {body}', [
                'status' => $response->getStatusCode(),
                'body' => $response->getBody(),
            ]);

            return null;
        }

        $data = json_decode($response->getBody(), true);

        return is_array($data) ? $data : null;
    }

    /**
     * @param array<string, mixed> $profile
     */
    private function isAllowedGoogleProfile(array $profile, GoogleAuth $config): bool
    {
        $email = strtolower(trim((string) ($profile['email'] ?? '')));
        $allowedDomain = strtolower($config->allowedDomain);
        $emailVerified = $profile['email_verified'] ?? false;
        $emailVerified = $emailVerified === true || $emailVerified === 1 || $emailVerified === '1' || $emailVerified === 'true';

        return $emailVerified
            && str_ends_with($email, '@' . $allowedDomain)
            && trim((string) ($profile['sub'] ?? '')) !== '';
    }

    /**
     * @param array<string, mixed> $profile
     *
     * @return array<string, mixed>|null
     */
    private function findOrCreateGoogleUser(array $profile): ?array
    {
        $userModel = new UserModel();
        $roleModel = new RoleModel();
        $userRoleModel = new UserRoleModel();
        $now = date('Y-m-d H:i:s');
        $googleSub = trim((string) $profile['sub']);
        $email = strtolower(trim((string) $profile['email']));
        $name = trim((string) ($profile['name'] ?? ''));

        if ($name === '') {
            $name = $email;
        }

        $user = $userModel->where('google_sub', $googleSub)->first();
        if (is_array($user) && ! $this->isGoogleAccount($user)) {
            $this->googleAccountFailure = 'This email is already registered as a password account. Sign in with your email and password instead.';

            return null;
        }

        if (! is_array($user)) {
            $emailUser = $userModel->where('email', $email)->first();
            if (is_array($emailUser)) {
                if (! $this->isGoogleAccount($emailUser)) {
                    $this->googleAccountFailure = 'This email is already registered as a password account. Sign in with your email and password instead.';

                    return null;
                }

                $user = $emailUser;
            }
        }

        $updates = [
            'name' => $name,
            'email' => $email,
            'google_sub' => $googleSub,
            'auth_provider' => 'google',
            'avatar_url' => trim((string) ($profile['picture'] ?? '')) ?: null,
            'email_verified_at' => $now,
        ];

        if (is_array($user)) {
            $userModel->update((int) $user['id'], $updates);
            $this->ensureDefaultUserRole((int) $user['id'], $roleModel, $userRoleModel);

            return $userModel->find((int) $user['id']);
        }

        $userId = (int) $userModel->insert($updates + [
            'password_hash' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
            'auth_provider' => 'google',
            'status' => 'active',
        ], true);

        if ($userId <= 0) {
            return null;
        }

        $this->ensureDefaultUserRole($userId, $roleModel, $userRoleModel);
        $this->recordAudit('register_google', 'user', $userId, 'New user account created through Google sign-in.');

        return $userModel->find($userId);
    }

    private function ensureDefaultUserRole(int $userId, ?RoleModel $roleModel = null, ?UserRoleModel $userRoleModel = null): void
    {
        $roleModel ??= new RoleModel();
        $userRoleModel ??= new UserRoleModel();

        $role = $roleModel->where('name', 'user')->first();
        if (! is_array($role)) {
            $roleId = (int) $roleModel->insert([
                'name' => 'user',
                'description' => 'Authorized dataset user',
            ], true);
        } else {
            $roleId = (int) $role['id'];
        }

        if ($roleId <= 0) {
            return;
        }

        $exists = $userRoleModel
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->first();

        if (! is_array($exists)) {
            $userRoleModel->insert([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);
        }
    }

    /**
     * @param array<string, mixed> $user
     */
    private function isGoogleAccount(array $user): bool
    {
        return strtolower(trim((string) ($user['auth_provider'] ?? 'local'))) === 'google';
    }

    private function clearStaleAuthenticationPrompt(): void
    {
        if ($this->session->getFlashdata('error') === 'Please log in to continue.') {
            $this->session->remove('error');
        }
    }

    /**
     * Only active, administrator-issued password accounts outside the CSPC
     * Google domain may enter the password-reset flow.
     *
     * @return array<string, mixed>|null
     */
    private function findPasswordResetEligibleUser(string $email): ?array
    {
        $email = strtolower(trim($email));
        if ($email === '') {
            return null;
        }

        $user = model(UserModel::class)->where('email', $email)->first();

        return is_array($user) && $this->isPasswordResetEligibleAccount($user) ? $user : null;
    }

    /**
     * @param array<string, mixed> $user
     */
    private function isPasswordResetEligibleAccount(array $user): bool
    {
        if (strtolower(trim((string) ($user['status'] ?? ''))) !== 'active') {
            return false;
        }

        if (strtolower(trim((string) ($user['auth_provider'] ?? ''))) !== 'local') {
            return false;
        }

        $email = strtolower(trim((string) ($user['email'] ?? '')));
        $passwordInfo = password_get_info((string) ($user['password_hash'] ?? ''));
        if ($email === '' || ($passwordInfo['algoName'] ?? 'unknown') === 'unknown') {
            return false;
        }

        $googleDomain = strtolower(trim((string) config(GoogleAuth::class)->allowedDomain));

        return $googleDomain === '' || ! str_ends_with($email, '@' . $googleDomain);
    }

    /**
     * Maintainer accounts should land in their work queue instead of the public contributor dashboard.
     *
     * @param array<int, string> $roles
     */
    private function postLoginDestination(array $roles): string
    {
        $intended = $this->consumeIntendedDestination();
        if ($intended !== null) {
            return $intended;
        }

        if (in_array('repository_administrator', $roles, true)) {
            return '/admin';
        }
        if (in_array('technical_reviewer', $roles, true)) {
            return '/review/technical';
        }
        if (in_array('ethics_reviewer', $roles, true)) {
            return '/review/ethics';
        }

        return '/dashboard';
    }

    private function storeIntendedDestination(string $returnTo): string
    {
        $safeReturnTo = $this->sanitizeInternalReturnTo($returnTo);
        if ($safeReturnTo !== null) {
            $this->session->setTempdata('auth_return_to', $safeReturnTo, 900);
        }

        $storedReturnTo = $this->session->getTempdata('auth_return_to');

        return is_string($storedReturnTo) ? $storedReturnTo : '';
    }

    private function consumeIntendedDestination(): ?string
    {
        $returnTo = $this->session->getTempdata('auth_return_to');
        $this->session->removeTempdata('auth_return_to');

        return is_string($returnTo) ? $this->sanitizeInternalReturnTo($returnTo) : null;
    }

    private function sanitizeInternalReturnTo(string $returnTo): ?string
    {
        $returnTo = trim($returnTo);

        if ($returnTo === '' || ! str_starts_with($returnTo, '/') || str_starts_with($returnTo, '//')) {
            return null;
        }

        if (str_contains($returnTo, '://') || str_contains($returnTo, "\0")) {
            return null;
        }

        return $returnTo;
    }

    private function isValidPasswordResetToken(string $email, string $token): bool
    {
        return is_array($this->findValidPasswordReset($email, $token));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findValidPasswordReset(string $email, string $token): ?array
    {
        $email = strtolower(trim($email));
        if (! is_array($this->findPasswordResetEligibleUser($email))) {
            return null;
        }

        $tokenHash = hash('sha256', $token);
        $reset = model(PasswordResetModel::class)
            ->where('email', $email)
            ->where('token_hash', $tokenHash)
            ->where('used_at', null)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->orderBy('created_at', 'DESC')
            ->first();

        return is_array($reset) ? $reset : null;
    }

    private function sendPasswordResetEmail(string $recipient, string $resetUrl): bool
    {
        $emailConfig = config('Email');
        if (trim((string) $emailConfig->fromEmail) === '') {
            log_message('error', 'Password reset email was not sent because email.fromEmail is not configured.');
            return false;
        }

        $mailer = service('email');
        $mailer->setFrom($emailConfig->fromEmail, $emailConfig->fromName ?: 'ASOG TBI Dataset Repository');
        $mailer->setTo($recipient);
        $mailer->setSubject('Reset your ASOG TBI Dataset Repository password');
        $mailer->setMailType('html');
        $mailer->setMessage(view('emails/password_reset', [
            'resetUrl' => $resetUrl,
            'expiresInMinutes' => 30,
        ]));
        $mailer->setAltMessage("Reset your ASOG TBI Dataset Repository password:\n\n{$resetUrl}\n\nThis link expires in 30 minutes and can only be used once.");

        if (! $mailer->send()) {
            log_message('error', 'Password reset email could not be sent to the requested account.');
            return false;
        }

        return true;
    }
}
