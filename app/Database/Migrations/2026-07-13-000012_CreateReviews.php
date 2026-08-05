<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReviews extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'dataset_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'dataset_version_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'stage' => ['type' => 'VARCHAR', 'constraint' => 20],
            'review_round' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 1],
            'reviewer_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'assigned_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'assigned'],
            'checklist' => ['type' => 'TEXT', 'null' => true],
            'comments' => ['type' => 'TEXT', 'null' => true],
            'assigned_at' => ['type' => 'DATETIME'],
            'decided_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['dataset_id', 'stage', 'status']);
        $this->forge->addKey(['reviewer_id', 'status']);
        $this->forge->addForeignKey('dataset_id', 'datasets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('dataset_version_id', 'dataset_versions', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('reviewer_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('assigned_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('reviews');
    }

    public function down(): void
    {
        $this->forge->dropTable('reviews');
    }
}
