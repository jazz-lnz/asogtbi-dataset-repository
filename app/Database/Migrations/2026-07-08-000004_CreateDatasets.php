<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDatasets extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT'],
            'category' => ['type' => 'VARCHAR', 'constraint' => 120],
            'tags' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'data_type' => ['type' => 'VARCHAR', 'constraint' => 80],
            'file_format' => ['type' => 'VARCHAR', 'constraint' => 30],
            'source_type' => ['type' => 'VARCHAR', 'constraint' => 80],
            'source_link' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'form' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'research_title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'project_head' => ['type' => 'VARCHAR', 'constraint' => 150],
            'members' => ['type' => 'TEXT', 'null' => true],
            'contributor_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'pending_ethics_review'],
            'access_type' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'public'],
            'version' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => '1.0'],
            'approved_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approved_at' => ['type' => 'DATETIME', 'null' => true],
            'archived_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['status', 'data_type']);
        $this->forge->addForeignKey('contributor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('datasets');
    }

    public function down(): void
    {
        $this->forge->dropTable('datasets');
    }
}
