<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserRoleModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Account extends BaseController
{
    public function settings(): string
    {
        $user = $this->currentUser();

        return view('account/settings', [
            'title' => 'Profile Settings',
            'user' => $user,
            'roles' => $this->roleNames((int) $user['id']),
            'validation' => session()->getFlashdata('validation'),
        ]);
    }

    public function updateSettings()
    {
        $user = $this->currentUser();
        $userId = (int) $user['id'];
        $isGoogleAccount = $this->isGoogleAccount($user);

        if ($isGoogleAccount) {
            if ($this->hasPasswordPostFields()) {
                return redirect()
                    ->back()
                    ->with('error', 'Google sign-in accounts do not use repository passwords.');
            }

            $this->session->set([
                'user_name' => (string) ($user['name'] ?? ''),
                'user_email' => (string) ($user['email'] ?? ''),
            ]);

            return redirect()
                ->to('/account/settings')
                ->with('info', 'Google account details are managed by your verified my.cspc.edu.ph Google profile.');
        }

        $newPassword = (string) $this->request->getPost('new_password');

        $rules = [
            'name' => [
                'label' => 'Full name',
                'rules' => 'required|min_length[3]|max_length[150]',
            ],
            'email' => [
                'label' => 'Email address',
                'rules' => 'required|valid_email|max_length[190]|is_unique[users.email,id,' . $userId . ']',
                'errors' => [
                    'is_unique' => 'That email address is already used by another account.',
                ],
            ],
        ];

        $rules += [
            'current_password' => [
                'label' => 'Current password',
                'rules' => 'permit_empty',
            ],
            'new_password' => [
                'label' => 'New password',
                'rules' => 'permit_empty|min_length[8]',
            ],
            'new_password_confirm' => [
                'label' => 'Confirm new password',
                'rules' => 'permit_empty|matches[new_password]',
            ],
        ];

        if ($newPassword !== '') {
            $rules['current_password']['rules'] = 'required';
            $rules['new_password_confirm']['rules'] = 'required|matches[new_password]';
        }

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation', $this->validator->getErrors())
                ->with('error', 'Profile settings need a quick fix before saving.');
        }

        if ($newPassword !== '' && ! password_verify((string) $this->request->getPost('current_password'), (string) ($user['password_hash'] ?? ''))) {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation', ['current_password' => 'Enter your current password before changing to a new one.'])
                ->with('error', 'Current password did not match.');
        }

        $updates = [
            'name' => trim((string) $this->request->getPost('name')),
            'email' => trim((string) $this->request->getPost('email')),
        ];

        if ($newPassword !== '') {
            $updates['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        model(UserModel::class)->update($userId, $updates);

        $this->session->set([
            'user_name' => $updates['name'],
            'user_email' => $updates['email'],
        ]);

        $this->recordAudit('profile_updated', 'user', $userId, $newPassword !== '' ? 'User updated profile details and password.' : 'User updated profile details.');

        return redirect()
            ->to('/account/settings')
            ->with('info', 'Profile settings updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function currentUser(): array
    {
        $userId = $this->currentUserId();
        $user = $userId !== null ? model(UserModel::class)->find($userId) : null;

        if (! is_array($user)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $user;
    }

    /**
     * @param array<string, mixed> $user
     */
    private function isGoogleAccount(array $user): bool
    {
        return strtolower(trim((string) ($user['auth_provider'] ?? 'local'))) === 'google';
    }

    private function hasPasswordPostFields(): bool
    {
        return trim((string) $this->request->getPost('current_password')) !== ''
            || trim((string) $this->request->getPost('new_password')) !== ''
            || trim((string) $this->request->getPost('new_password_confirm')) !== '';
    }

    /**
     * @return array<int, string>
     */
    private function roleNames(int $userId): array
    {
        $rows = model(UserRoleModel::class)
            ->select('roles.name')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->orderBy('roles.name', 'ASC')
            ->findAll();

        return array_values(array_filter(array_map(static fn (array $row): string => (string) ($row['name'] ?? ''), $rows)));
    }
}
