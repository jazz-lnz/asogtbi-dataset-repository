<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoAccountsSeeder extends Seeder
{
    private const DEMO_PASSWORD = 'change-me';

    /**
     * @var list<array{name: string, email: string, role: string, description: string}>
     */
    private array $accounts = [
        [
            'name' => 'Demo User',
            'email' => 'user@example.test',
            'role' => 'user',
            'description' => 'Authorized dataset user',
        ],
        [
            'name' => 'Repository Administrator',
            'email' => 'admin@example.test',
            'role' => 'repository_administrator',
            'description' => 'Repository governance and publication',
        ],
        [
            'name' => 'Research Ethics Reviewer',
            'email' => 'ethics@example.test',
            'role' => 'ethics_reviewer',
            'description' => 'Ethics and privacy verification',
        ],
        [
            'name' => 'Technical Reviewer',
            'email' => 'technical@example.test',
            'role' => 'technical_reviewer',
            'description' => 'Technical dataset verification',
        ],
    ];

    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        foreach ($this->accounts as $account) {
            $roleId = $this->ensureRole($account['role'], $account['description'], $now);
            $userId = $this->ensureUser($account['name'], $account['email'], $now);
            $this->ensureUserRole($userId, $roleId);
        }
    }

    private function ensureRole(string $name, string $description, string $now): int
    {
        $role = $this->db->table('roles')->where('name', $name)->get()->getRowArray();

        if (is_array($role)) {
            $roleId = (int) $role['id'];
            $this->db->table('roles')->where('id', $roleId)->update([
                'description' => $description,
                'updated_at' => $now,
            ]);

            return $roleId;
        }

        $this->db->table('roles')->insert([
            'name' => $name,
            'description' => $description,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (int) $this->db->insertID();
    }

    private function ensureUser(string $name, string $email, string $now): int
    {
        $user = $this->db->table('users')->where('email', $email)->get()->getRowArray();

        $record = [
            'name' => $name,
            'password_hash' => password_hash(self::DEMO_PASSWORD, PASSWORD_DEFAULT),
            'status' => 'active',
            'updated_at' => $now,
        ];

        if (is_array($user)) {
            $userId = (int) $user['id'];
            $this->db->table('users')->where('id', $userId)->update($record);

            return $userId;
        }

        $record['email'] = $email;
        $record['created_at'] = $now;
        $this->db->table('users')->insert($record);

        return (int) $this->db->insertID();
    }

    private function ensureUserRole(int $userId, int $roleId): void
    {
        $exists = $this->db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->get()
            ->getRowArray();

        if (is_array($exists)) {
            return;
        }

        $this->db->table('user_roles')->insert([
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);
    }
}
