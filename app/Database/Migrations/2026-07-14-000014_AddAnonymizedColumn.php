<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAnonymizedColumn extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('datasets', [
            'anonymized' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => true,
                'default' => 0,
                'after' => 'access_type',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('datasets', 'anonymized');
    }
}
