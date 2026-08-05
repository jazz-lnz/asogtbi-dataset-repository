<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDatasetCoverImage extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('datasets', [
            'cover_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'description',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('datasets', 'cover_image');
    }
}
