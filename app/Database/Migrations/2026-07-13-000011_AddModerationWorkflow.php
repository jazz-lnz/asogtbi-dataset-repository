<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddModerationWorkflow extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('datasets', [
            'archived_from_status' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => true,
                'after' => 'archived_at',
            ],
        ]);

        $this->db->table('datasets')
            ->where('status', 'pending_review')
            ->update(['status' => 'pending_ethics_review']);
        $this->db->table('datasets')
            ->where('status', 'revision_requested')
            ->update(['status' => 'ethics_revision_requested']);

        $this->forge->addUniqueKey(['user_id', 'role_id'], 'user_roles_user_role_unique');
        $this->forge->processIndexes('user_roles');
    }

    public function down(): void
    {
        try {
            $this->forge->dropKey('user_roles', 'user_roles_user_role_unique');
        } catch (\Throwable $e) {
        }

        $this->db->table('datasets')
            ->where('status', 'pending_ethics_review')
            ->update(['status' => 'pending_review']);
        $this->db->table('datasets')
            ->whereIn('status', ['ethics_revision_requested', 'technical_revision_requested'])
            ->update(['status' => 'revision_requested']);

        $this->forge->dropColumn('datasets', 'archived_from_status');
    }
}
