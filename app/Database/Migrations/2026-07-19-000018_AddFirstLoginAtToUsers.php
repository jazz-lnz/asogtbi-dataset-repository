<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFirstLoginAtToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'first_login_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'last_login_at'],
        ]);

        $this->db->query(
            'UPDATE ' . $this->db->prefixTable('users')
            . ' SET first_login_at = COALESCE(last_login_at, created_at, updated_at)'
            . ' WHERE first_login_at IS NULL'
        );
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'first_login_at');
    }
}
