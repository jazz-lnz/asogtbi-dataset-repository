<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReviewAssignmentMethod extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('reviews', [
            'assignment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'manual',
                'after' => 'assigned_by',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('reviews', 'assignment_method');
    }
}
