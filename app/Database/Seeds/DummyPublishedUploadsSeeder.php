<?php

namespace App\Database\Seeds;

use App\Models\DatasetModel;
use CodeIgniter\Database\Seeder;
use ZipArchive;

class DummyPublishedUploadsSeeder extends Seeder
{
    private const CONTRIBUTOR_ID = 1;
    private const SOURCE_FILE = 'dummydata/dataset1.csv';
    private const LEGACY_CHANGE_SUMMARY = 'Imported from dummydata/dataset1.csv as a published demo upload.';
    private const ZIP_CHANGE_SUMMARY = 'Imported from dummydata/dataset1.csv as a ZIP demo upload.';

    public function run(): void
    {
        $source = ROOTPATH . str_replace('/', DIRECTORY_SEPARATOR, self::SOURCE_FILE);
        if (! is_file($source)) {
            throw new \RuntimeException('Missing dummy data source: ' . self::SOURCE_FILE);
        }

        $user = $this->db->table('users')->where('id', self::CONTRIBUTOR_ID)->get()->getRowArray();
        if (! is_array($user)) {
            throw new \RuntimeException('User 1 must exist before seeding dummy published uploads. Run MvpSeeder first.');
        }

        $now = date('Y-m-d H:i:s');
        $adminId = $this->resolveApproverId();
        $sourceSize = filesize($source) ?: 0;
        $handle = fopen($source, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Unable to open dummy data source: ' . self::SOURCE_FILE);
        }

        $this->db->transStart();

        while (($row = fgetcsv($handle)) !== false) {
            $sourceId = trim((string) ($row[0] ?? ''));
            $title = trim((string) ($row[1] ?? ''));
            $description = trim((string) ($row[2] ?? ''));

            if ($title === '' || $description === '') {
                continue;
            }

            $datasetId = $this->upsertDataset($title, $description, $sourceId, (string) $user['name'], $adminId, $now);
            $this->removeLegacyCsvArtifacts($datasetId);
            $fileId = $this->upsertFile($datasetId, $title, $sourceId, $description, $now);
            $this->upsertVersion($datasetId, $fileId, $now);
        }

        fclose($handle);
        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            throw new \RuntimeException('Dummy published upload seeding failed.');
        }
    }

    private function resolveApproverId(): int
    {
        $admin = $this->db->table('users')->where('email', 'admin@example.test')->get()->getRowArray();

        return is_array($admin) ? (int) $admin['id'] : self::CONTRIBUTOR_ID;
    }

    private function upsertDataset(string $title, string $description, string $sourceId, string $contributorName, int $adminId, string $now): int
    {
        $record = [
            'title' => $title,
            'description' => $description,
            'category' => 'Dummy Data',
            'tags' => $this->deriveTags($title),
            'data_type' => 'Tabular',
            'file_format' => 'ZIP',
            'content_formats' => '.csv',
            'source_type' => 'Secondary',
            'source_link' => null,
            'form' => 'upload',
            'research_title' => 'Dummydata Import: ' . substr($title, 0, 230),
            'project_head' => $contributorName,
            'members' => 'Imported dummydata row ' . ($sourceId !== '' ? $sourceId : 'n/a'),
            'contributor_id' => self::CONTRIBUTOR_ID,
            'status' => DatasetModel::STATUS_PUBLISHED,
            'access_type' => DatasetModel::ACCESS_PUBLIC,
            'version' => '1.0',
            'approved_by' => $adminId,
            'approved_at' => $now,
            'archived_at' => null,
            'archived_from_status' => null,
            'updated_at' => $now,
        ];

        $existing = $this->db->table('datasets')
            ->where('title', $title)
            ->where('contributor_id', self::CONTRIBUTOR_ID)
            ->get()
            ->getRowArray();

        if (is_array($existing)) {
            $datasetId = (int) $existing['id'];
            $this->db->table('datasets')->where('id', $datasetId)->update($record);

            return $datasetId;
        }

        $record['created_at'] = $now;
        $this->db->table('datasets')->insert($record);

        return (int) $this->db->insertID();
    }

    private function removeLegacyCsvArtifacts(int $datasetId): void
    {
        $legacyFiles = $this->db->table('dataset_files')
            ->where('dataset_id', $datasetId)
            ->groupStart()
            ->where('original_name', 'dataset1.csv')
            ->orLike('stored_name', 'dummydata-dataset1-', 'after')
            ->orWhere('file_type', 'text/csv')
            ->groupEnd()
            ->get()
            ->getResultArray();

        foreach ($legacyFiles as $legacyFile) {
            $fileId = (int) ($legacyFile['id'] ?? 0);
            if ($fileId > 0) {
                $this->db->table('dataset_versions')->where('dataset_file_id', $fileId)->delete();
            }

            $filePath = trim((string) ($legacyFile['file_path'] ?? ''));
            if ($filePath !== '') {
                $absolutePath = WRITEPATH . str_replace('/', DIRECTORY_SEPARATOR, $filePath);
                if (is_file($absolutePath)) {
                    @unlink($absolutePath);
                }
            }
        }

        if ($legacyFiles !== []) {
            $this->db->table('dataset_files')->where('dataset_id', $datasetId)
                ->groupStart()
                ->where('original_name', 'dataset1.csv')
                ->orLike('stored_name', 'dummydata-dataset1-', 'after')
                ->orWhere('file_type', 'text/csv')
                ->groupEnd()
                ->delete();
        }

        $this->db->table('dataset_versions')
            ->where('dataset_id', $datasetId)
            ->where('change_summary', self::LEGACY_CHANGE_SUMMARY)
            ->delete();
    }

    private function upsertFile(int $datasetId, string $title, string $sourceId, string $description, string $now): int
    {
        $datasetDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'datasets' . DIRECTORY_SEPARATOR . $datasetId;
        if (! is_dir($datasetDir)) {
            mkdir($datasetDir, 0777, true);
        }

        $storedName = $this->buildArchiveName($title, $datasetId);
        $absolutePath = $datasetDir . DIRECTORY_SEPARATOR . $storedName;
        $this->writeZipPayload($absolutePath, $sourceId, $title, $description);

        $record = [
            'dataset_id' => $datasetId,
            'stored_name' => $storedName,
            'original_name' => $storedName,
            'file_path' => 'uploads/datasets/' . $datasetId . '/' . $storedName,
            'file_size' => is_file($absolutePath) ? ((int) filesize($absolutePath) ?: 0) : 0,
            'file_type' => 'application/zip',
            'uploaded_by' => self::CONTRIBUTOR_ID,
            'updated_at' => $now,
        ];

        $existing = $this->db->table('dataset_files')
            ->where('dataset_id', $datasetId)
            ->get()
            ->getRowArray();

        if (is_array($existing)) {
            $fileId = (int) $existing['id'];
            $this->db->table('dataset_files')->where('id', $fileId)->update($record);

            return $fileId;
        }

        $record['created_at'] = $now;
        $this->db->table('dataset_files')->insert($record);

        return (int) $this->db->insertID();
    }

    private function upsertVersion(int $datasetId, int $fileId, string $now): void
    {
        $record = [
            'dataset_id' => $datasetId,
            'version' => '1.0',
            'change_summary' => self::ZIP_CHANGE_SUMMARY,
            'dataset_file_id' => $fileId,
            'created_by' => self::CONTRIBUTOR_ID,
            'updated_at' => $now,
        ];

        $existing = $this->db->table('dataset_versions')
            ->where('dataset_id', $datasetId)
            ->where('version', '1.0')
            ->get()
            ->getRowArray();

        if (is_array($existing)) {
            $this->db->table('dataset_versions')->where('id', (int) $existing['id'])->update($record);

            return;
        }

        $record['created_at'] = $now;
        $this->db->table('dataset_versions')->insert($record);
    }

    private function deriveTags(string $title): string
    {
        $tags = strtolower((string) preg_replace('/[^a-z0-9]+/i', ',', $title));

        return trim((string) preg_replace('/,+/', ',', $tags), ',');
    }

    private function buildArchiveName(string $title, int $datasetId): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        if ($slug === '') {
            $slug = 'dummy-published-upload-' . $datasetId;
        }

        return $slug . '.zip';
    }

    private function writeZipPayload(string $absolutePath, string $sourceId, string $title, string $description): void
    {
        if (class_exists(ZipArchive::class)) {
            $archive = new ZipArchive();
            $result = $archive->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            if ($result !== true) {
                throw new \RuntimeException('Unable to create dummy ZIP archive: ' . $absolutePath);
            }

            $archive->addFromString('dataset.csv', $this->buildCsvPayload($sourceId, $title, $description));
            $archive->addFromString(
                'README.txt',
                "Seeded demo upload\n"
                . 'Source row: ' . ($sourceId !== '' ? $sourceId : 'n/a') . "\n"
                . 'Title: ' . $title . "\n"
            );
            $archive->close();

            return;
        }

        $emptyZip = base64_decode('UEsFBgAAAAAAAAAAAAAAAAAAAAAAAA==');
        if (! is_string($emptyZip) || file_put_contents($absolutePath, $emptyZip) === false) {
            throw new \RuntimeException('Unable to write fallback dummy ZIP archive: ' . $absolutePath);
        }
    }

    private function buildCsvPayload(string $sourceId, string $title, string $description): string
    {
        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            throw new \RuntimeException('Unable to build dummy CSV payload.');
        }

        fputcsv($handle, ['source_id', 'title', 'description']);
        fputcsv($handle, [$sourceId, $title, $description]);
        rewind($handle);
        $payload = stream_get_contents($handle);
        fclose($handle);

        return is_string($payload) ? $payload : '';
    }
}
