<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContentFormatsToDatasets extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('content_formats', 'datasets')) {
            $this->forge->addColumn('datasets', [
                'content_formats' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'file_format',
                ],
            ]);
        }

        $this->db->table('datasets')
            ->set(
                'content_formats',
                "CASE
                    WHEN content_formats IS NULL OR content_formats = '' THEN
                        CASE
                            WHEN file_format IS NULL OR file_format = '' OR UPPER(file_format) = 'ZIP' THEN NULL
                            ELSE file_format
                        END
                    ELSE content_formats
                END",
                false
            )
            ->set('file_format', 'ZIP')
            ->update();
    }

    public function down(): void
    {
        if ($this->db->fieldExists('content_formats', 'datasets')) {
            $this->forge->dropColumn('datasets', 'content_formats');
        }
    }
}