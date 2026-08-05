<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceReviewWorkflow extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('reviews', [
            'draft_saved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'comments',
            ],
            'reassignment_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'draft_saved_at',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('reviews', ['draft_saved_at', 'reassignment_reason']);
    }
}
