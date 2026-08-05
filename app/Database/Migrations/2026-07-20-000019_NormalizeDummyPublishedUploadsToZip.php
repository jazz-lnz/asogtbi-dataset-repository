<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeDummyPublishedUploadsToZip extends Migration
{
    private const LEGACY_CHANGE_SUMMARY = 'Imported from dummydata/dataset1.csv as a published demo upload.';

    public function up(): void
    {
        $datasetRows = $this->db->table('datasets')
            ->select('id')
            ->where('category', 'Dummy Data')
            ->like('members', 'Imported dummydata row ', 'after')
            ->get()
            ->getResultArray();

        if ($datasetRows === []) {
            return;
        }

        $datasetIds = array_map(static fn (array $row): int => (int) $row['id'], $datasetRows);
        $now = date('Y-m-d H:i:s');

        $legacyFiles = $this->db->table('dataset_files')
            ->whereIn('dataset_id', $datasetIds)
            ->groupStart()
            ->where('original_name', 'dataset1.csv')
            ->orLike('stored_name', 'dummydata-dataset1-', 'after')
            ->orWhere('file_type', 'text/csv')
            ->groupEnd()
            ->get()
            ->getResultArray();

        foreach ($legacyFiles as $legacyFile) {
            $filePath = trim((string) ($legacyFile['file_path'] ?? ''));
            if ($filePath !== '') {
                $absolutePath = WRITEPATH . str_replace('/', DIRECTORY_SEPARATOR, $filePath);
                if (is_file($absolutePath)) {
                    @unlink($absolutePath);
                }
            }

            $fileId = (int) ($legacyFile['id'] ?? 0);
            if ($fileId > 0) {
                $this->db->table('dataset_versions')->where('dataset_file_id', $fileId)->delete();
            }
        }

        if ($legacyFiles !== []) {
            $legacyFileIds = array_map(static fn (array $row): int => (int) $row['id'], $legacyFiles);
            $this->db->table('dataset_files')->whereIn('id', $legacyFileIds)->delete();
        }

        $this->db->table('dataset_versions')
            ->whereIn('dataset_id', $datasetIds)
            ->where('change_summary', self::LEGACY_CHANGE_SUMMARY)
            ->delete();

        $this->db->table('datasets')
            ->whereIn('id', $datasetIds)
            ->update([
                'file_format' => 'ZIP',
                'form' => 'upload',
                'updated_at' => $now,
            ]);
    }

    public function down(): void
    {
        // Legacy CSV demo uploads are intentionally not restored.
    }
}
