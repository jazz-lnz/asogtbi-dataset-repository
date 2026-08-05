<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGoogleAuthToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'google_sub' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'email'],
            'auth_provider' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'local', 'after' => 'google_sub'],
            'avatar_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'auth_provider'],
            'email_verified_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'avatar_url'],
        ]);

        $this->db->query('CREATE UNIQUE INDEX users_google_sub_unique ON ' . $this->db->prefixTable('users') . ' (google_sub)');
    }

    public function down(): void
    {
        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query('DROP INDEX users_google_sub_unique ON ' . $this->db->prefixTable('users'));
        } else {
            $this->db->query('DROP INDEX users_google_sub_unique');
        }

        $this->forge->dropColumn('users', ['google_sub', 'auth_provider', 'avatar_url', 'email_verified_at']);
    }
}
