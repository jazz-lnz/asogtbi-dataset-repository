<?php

namespace App\Database\Seeds;

use App\Models\DatasetModel;
use CodeIgniter\Database\Seeder;

class MvpSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $roleId = $this->ensureRole($now);
        $userId = $this->ensureDemoUser($roleId, $now);
        $portalAccounts = $this->ensurePortalAccounts($now);
        $adminId = (int) ($portalAccounts['admin@example.test'] ?? $userId);

        $datasets = [
            [
                'title' => 'Startup Survey Responses',
                'description' => 'Sample tabular dataset for incubatee startup needs assessment.',
                'category' => 'Startup Research',
                'tags' => 'startup,survey,incubatee',
                'data_type' => 'Tabular',
                'file_format' => 'ZIP',
                'content_formats' => '.csv, .xlsx',
                'source_type' => 'Primary',
                'research_title' => 'ASOG TBI Startup Needs Assessment',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_PUBLISHED,
                'access_type' => DatasetModel::ACCESS_PUBLIC,
                'version' => '1.0',
                'file_name' => 'startup-survey-responses.zip',
                'members' => 'ASOG TBI Research Team',
            ],
            [
                'title' => 'Innovation Program Interviews',
                'description' => 'Sample text dataset containing coded interview excerpts.',
                'category' => 'Innovation',
                'tags' => 'innovation,interview,text',
                'data_type' => 'Text',
                'file_format' => 'ZIP',
                'content_formats' => '.txt, .pdf',
                'source_type' => 'Primary',
                'research_title' => 'Innovation Program Review',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_PUBLISHED,
                'access_type' => DatasetModel::ACCESS_INSTITUTIONAL,
                'version' => '1.0',
                'file_name' => 'innovation-program-interviews.zip',
                'members' => 'Innovation Program Office',
            ],
            [
                'title' => 'Restricted Incubatee Finance Extract',
                'description' => 'Sample restricted dataset for testing authenticated catalogue visibility and protected download messaging.',
                'category' => 'Incubation Finance',
                'tags' => 'incubatee,finance,restricted',
                'data_type' => 'Tabular',
                'file_format' => 'ZIP',
                'content_formats' => '.csv',
                'source_type' => 'Primary',
                'research_title' => 'Incubatee Finance Readiness Review',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_PUBLISHED,
                'access_type' => DatasetModel::ACCESS_RESTRICTED,
                'version' => '1.0',
                'file_name' => 'restricted-incubatee-finance-extract.zip',
                'members' => 'ASOG TBI Finance Review Team',
            ],
            [
                'title' => 'Private Founder Notes',
                'description' => 'Sample private published dataset for owner and administrator visibility checks.',
                'category' => 'Founder Support',
                'tags' => 'founder,private,notes',
                'data_type' => 'Text',
                'file_format' => 'ZIP',
                'content_formats' => '.txt, .docx',
                'source_type' => 'Primary',
                'research_title' => 'Founder Support Case Notes',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_PUBLISHED,
                'access_type' => DatasetModel::ACCESS_PRIVATE,
                'version' => '1.0',
                'file_name' => 'private-founder-notes.zip',
                'members' => 'Founder Support Desk',
            ],
            [
                'title' => 'Pending Prototype Dataset',
                'description' => 'Sample pending dataset ready for technical reviewer assignment before ethics review.',
                'category' => 'Prototype',
                'tags' => 'prototype,pending',
                'data_type' => 'Tabular',
                'file_format' => 'ZIP',
                'content_formats' => '.csv, .json',
                'source_type' => 'Secondary',
                'research_title' => 'Prototype Dataset Submission',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_PENDING,
                'access_type' => DatasetModel::ACCESS_PUBLIC,
                'version' => '1.0',
                'file_name' => 'pending-prototype-dataset.zip',
                'members' => 'Prototype Team',
            ],
            [
                'title' => 'Ethics Revision Sample',
                'description' => 'Sample submission returned to the contributor after ethics review requested revisions.',
                'category' => 'Human Subjects',
                'tags' => 'ethics,revision,consent',
                'data_type' => 'Text',
                'file_format' => 'ZIP',
                'content_formats' => '.pdf, .txt',
                'source_type' => 'Primary',
                'research_title' => 'Consent Handling Revision Sample',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_ETHICS_REVISION,
                'access_type' => DatasetModel::ACCESS_RESTRICTED,
                'version' => '1.1',
                'file_name' => 'ethics-revision-sample.zip',
                'members' => 'Ethics Demo Team',
            ],
            [
                'title' => 'Technical Queue Sample',
                'description' => 'Sample submission waiting for first-pass technical reviewer assignment.',
                'category' => 'Data Engineering',
                'tags' => 'technical,queue,metadata',
                'data_type' => 'Tabular',
                'file_format' => 'ZIP',
                'content_formats' => '.csv',
                'source_type' => 'Secondary',
                'research_title' => 'Technical Queue Dataset',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_PENDING_TECHNICAL,
                'access_type' => DatasetModel::ACCESS_INSTITUTIONAL,
                'version' => '1.0',
                'file_name' => 'technical-queue-sample.zip',
                'members' => 'Data Engineering Team',
            ],
            [
                'title' => 'Technical Revision Sample',
                'description' => 'Sample submission that retained ethics approval but needs technical packaging fixes.',
                'category' => 'Archive Quality',
                'tags' => 'technical,revision,archive',
                'data_type' => 'Image',
                'file_format' => 'ZIP',
                'content_formats' => '.png, .jpg',
                'source_type' => 'Primary',
                'research_title' => 'Archive Quality Revision Sample',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_TECHNICAL_REVISION,
                'access_type' => DatasetModel::ACCESS_RESTRICTED,
                'version' => '1.1',
                'file_name' => 'technical-revision-sample.zip',
                'members' => 'Archive Quality Team',
            ],
            [
                'title' => 'Publication Gate Sample',
                'description' => 'Sample technically and ethically approved dataset waiting for repository administrator publication.',
                'category' => 'Governance',
                'tags' => 'publication,approval,governance',
                'data_type' => 'Tabular',
                'file_format' => 'ZIP',
                'content_formats' => '.csv, .xlsx',
                'source_type' => 'Secondary',
                'research_title' => 'Publication Gate Dataset',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_AWAITING_PUBLICATION,
                'access_type' => DatasetModel::ACCESS_PUBLIC,
                'version' => '1.0',
                'file_name' => 'publication-gate-sample.zip',
                'members' => 'Repository Governance Team',
            ],
            [
                'title' => 'Rejected Submission Sample',
                'description' => 'Sample terminal rejected submission for contributor status and admin oversight testing.',
                'category' => 'Rejected Research',
                'tags' => 'rejected,terminal,workflow',
                'data_type' => 'Audio',
                'file_format' => 'ZIP',
                'content_formats' => '.mp3, .wav',
                'source_type' => 'Primary',
                'research_title' => 'Rejected Submission Dataset',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_REJECTED,
                'access_type' => DatasetModel::ACCESS_PRIVATE,
                'version' => '1.0',
                'file_name' => 'rejected-submission-sample.zip',
                'members' => 'Demo Research Team',
            ],
            [
                'title' => 'Archived Public Sample',
                'description' => 'Sample archived dataset for restore controls and public catalogue exclusion checks.',
                'category' => 'Archived Research',
                'tags' => 'archived,restore,catalogue',
                'data_type' => 'Video',
                'file_format' => 'ZIP',
                'content_formats' => '.mp4',
                'source_type' => 'Secondary',
                'research_title' => 'Archived Dataset Sample',
                'project_head' => 'Demo Adviser',
                'status' => DatasetModel::STATUS_ARCHIVED,
                'access_type' => DatasetModel::ACCESS_PUBLIC,
                'version' => '1.0',
                'file_name' => 'archived-public-sample.zip',
                'members' => 'Demo Archive Team',
                'archived_from_status' => DatasetModel::STATUS_PUBLISHED,
            ],
        ];

        foreach ($datasets as $dataset) {
            $datasetId = $this->ensureDataset($dataset, $userId, $adminId, $now);
            $fileId = $this->ensureDatasetFile($datasetId, $userId, (string) $dataset['file_name'], $now);
            $this->ensureDatasetVersion($datasetId, $userId, $fileId, (string) $dataset['version'], $now);
        }
    }

    private function ensureRole(string $now): int
    {
        $role = $this->db->table('roles')->where('name', 'user')->get()->getRowArray();
        if (is_array($role)) {
            $this->db->table('roles')->where('id', (int) $role['id'])->update([
                'description' => 'Authorized dataset user',
                'updated_at' => $now,
            ]);

            return (int) $role['id'];
        }

        $this->db->table('roles')->insert([
            'name' => 'user',
            'description' => 'Authorized dataset user',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (int) $this->db->insertID();
    }

    private function ensureDemoUser(int $roleId, string $now): int
    {
        $user = $this->db->table('users')->where('email', 'user@example.test')->get()->getRowArray();

        if (is_array($user)) {
            $userId = (int) $user['id'];
            $this->db->table('users')->where('id', $userId)->update([
                'name' => 'Demo User',
                'password_hash' => password_hash('change-me', PASSWORD_DEFAULT),
                'status' => 'active',
                'updated_at' => $now,
            ]);
        } else {
            $this->db->table('users')->insert([
                'name' => 'Demo User',
                'email' => 'user@example.test',
                'password_hash' => password_hash('change-me', PASSWORD_DEFAULT),
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $userId = (int) $this->db->insertID();
        }

        $userRole = $this->db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->get()
            ->getRowArray();

        if (! is_array($userRole)) {
            $this->db->table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);
        }

        return $userId;
    }

    /**
     * @return array<string, int>
     */
    private function ensurePortalAccounts(string $now): array
    {
        $accounts = [
            ['name' => 'Repository Administrator', 'email' => 'admin@example.test', 'role' => 'repository_administrator', 'description' => 'Repository governance and publication'],
            ['name' => 'Research Ethics Reviewer', 'email' => 'ethics@example.test', 'role' => 'ethics_reviewer', 'description' => 'Ethics and privacy verification'],
            ['name' => 'Technical Reviewer', 'email' => 'technical@example.test', 'role' => 'technical_reviewer', 'description' => 'Technical dataset verification'],
        ];
        $accountIds = [];
        foreach ($accounts as $account) {
            $role = $this->db->table('roles')->where('name', $account['role'])->get()->getRowArray();
            if (! is_array($role)) {
                $this->db->table('roles')->insert(['name' => $account['role'], 'description' => $account['description'], 'created_at' => $now, 'updated_at' => $now]);
                $roleId = (int) $this->db->insertID();
            } else {
                $roleId = (int) $role['id'];
            }
            $user = $this->db->table('users')->where('email', $account['email'])->get()->getRowArray();
            if (! is_array($user)) {
                $this->db->table('users')->insert(['name' => $account['name'], 'email' => $account['email'], 'password_hash' => password_hash('change-me', PASSWORD_DEFAULT), 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);
                $userId = (int) $this->db->insertID();
            } else {
                $userId = (int) $user['id'];
                $this->db->table('users')->where('id', $userId)->update([
                    'name' => $account['name'],
                    'password_hash' => password_hash('change-me', PASSWORD_DEFAULT),
                    'status' => 'active',
                    'updated_at' => $now,
                ]);
            }
            $exists = $this->db->table('user_roles')->where(['user_id' => $userId, 'role_id' => $roleId])->get()->getRowArray();
            if (! is_array($exists)) {
                $this->db->table('user_roles')->insert(['user_id' => $userId, 'role_id' => $roleId]);
            }
            $accountIds[$account['email']] = $userId;
        }

        return $accountIds;
    }

    /**
     * @param array<string, string> $dataset
     */
    private function ensureDataset(array $dataset, int $userId, int $adminId, string $now): int
    {
        $existing = $this->db->table('datasets')
            ->where('title', $dataset['title'])
            ->where('contributor_id', $userId)
            ->get()
            ->getRowArray();

        $record = [
            'title' => $dataset['title'],
            'description' => $dataset['description'],
            'category' => $dataset['category'],
            'tags' => $dataset['tags'],
            'data_type' => $dataset['data_type'],
            'file_format' => $dataset['file_format'],
            'content_formats' => $dataset['content_formats'] ?? null,
            'source_type' => $dataset['source_type'],
            'research_title' => $dataset['research_title'],
            'project_head' => $dataset['project_head'],
            'members' => $dataset['members'] ?? null,
            'contributor_id' => $userId,
            'status' => $dataset['status'],
            'access_type' => $dataset['access_type'],
            'version' => $dataset['version'],
            'approved_by' => in_array($dataset['status'], [DatasetModel::STATUS_PUBLISHED, DatasetModel::STATUS_ARCHIVED], true) ? $adminId : null,
            'approved_at' => in_array($dataset['status'], [DatasetModel::STATUS_PUBLISHED, DatasetModel::STATUS_ARCHIVED], true) ? $now : null,
            'archived_at' => $dataset['status'] === DatasetModel::STATUS_ARCHIVED ? $now : null,
            'archived_from_status' => $dataset['archived_from_status'] ?? null,
            'updated_at' => $now,
        ];

        if (is_array($existing)) {
            $datasetId = (int) $existing['id'];
            $this->db->table('datasets')->where('id', $datasetId)->update($record);

            return $datasetId;
        }

        $record['created_at'] = $now;
        $this->db->table('datasets')->insert($record);

        return (int) $this->db->insertID();
    }

    private function ensureDatasetFile(int $datasetId, int $userId, string $fileName, string $now): int
    {
        $baseDir = WRITEPATH . 'uploads/datasets';
        $datasetDir = $baseDir . DIRECTORY_SEPARATOR . $datasetId;
        if (! is_dir($datasetDir)) {
            mkdir($datasetDir, 0777, true);
        }

        $emptyZip = base64_decode('UEsFBgAAAAAAAAAAAAAAAAAAAAAAAA==');
        $absolutePath = $datasetDir . DIRECTORY_SEPARATOR . $fileName;
        if (! is_file($absolutePath)) {
            file_put_contents($absolutePath, $emptyZip);
        }

        $relativePath = 'uploads/datasets/' . $datasetId . '/' . $fileName;
        $existing = $this->db->table('dataset_files')
            ->where('dataset_id', $datasetId)
            ->where('original_name', $fileName)
            ->get()
            ->getRowArray();

        $record = [
            'dataset_id' => $datasetId,
            'stored_name' => $fileName,
            'original_name' => $fileName,
            'file_path' => $relativePath,
            'file_size' => is_string($emptyZip) ? strlen($emptyZip) : 0,
            'file_type' => 'application/zip',
            'uploaded_by' => $userId,
            'updated_at' => $now,
        ];

        if (is_array($existing)) {
            $fileId = (int) $existing['id'];
            $this->db->table('dataset_files')->where('id', $fileId)->update($record);

            return $fileId;
        }

        $record['created_at'] = $now;
        $this->db->table('dataset_files')->insert($record);

        return (int) $this->db->insertID();
    }

    private function ensureDatasetVersion(int $datasetId, int $userId, int $fileId, string $version, string $now): void
    {
        $existing = $this->db->table('dataset_versions')
            ->where('dataset_id', $datasetId)
            ->where('version', $version)
            ->get()
            ->getRowArray();

        $record = [
            'dataset_id' => $datasetId,
            'version' => $version,
            'change_summary' => 'Initial seeded dataset.',
            'dataset_file_id' => $fileId,
            'created_by' => $userId,
            'updated_at' => $now,
        ];

        if (is_array($existing)) {
            $this->db->table('dataset_versions')->where('id', (int) $existing['id'])->update($record);

            return;
        }

        $record['created_at'] = $now;
        $this->db->table('dataset_versions')->insert($record);
    }
}
