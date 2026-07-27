<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\DatasetDownloadModel;
use App\Models\DatasetFileModel;
use App\Models\DatasetModel;
use App\Models\DatasetVersionModel;
use App\Models\DatasetViewModel;
use App\Models\NotificationModel;
use App\Models\ReviewModel;
use App\Services\ModerationWorkflow;

class Datasets extends BaseController
{
    public function index(): string
    {
        $datasetModel = new DatasetModel();
        $search = trim((string) ($this->request->getGet('q') ?? ''));
        $dataTypeRaw = trim((string) ($this->request->getGet('data_type') ?? ''));
        $selectedDataTypes = $dataTypeRaw !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $dataTypeRaw)), static fn(string $v): bool => $v !== ''))
            : [];

        $categoryRaw = trim((string) ($this->request->getGet('category') ?? ''));
        $selectedCategories = $categoryRaw !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $categoryRaw)), static fn(string $v): bool => $v !== ''))
            : [];

        $dateUploadedRaw = trim((string) ($this->request->getGet('date_uploaded') ?? ''));
        $selectedDateUploaded = $dateUploadedRaw !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $dateUploadedRaw)), static fn(string $v): bool => $v !== ''))
            : [];

        $query = $datasetModel
            ->select('datasets.*, users.name AS author_name, users.email AS author_email')
            ->join('users', 'users.id = datasets.contributor_id', 'left')
            ->where('datasets.status', DatasetModel::STATUS_PUBLISHED)
            ->where('datasets.archived_at', null);
        $this->applyPublishedAccessScope($query);

        if ($search !== '') {
            $query->groupStart()
                ->like('datasets.title', $search)
                ->orLike('datasets.description', $search)
                ->orLike('datasets.tags', $search)
                ->orLike('datasets.category', $search)
                ->orLike('datasets.content_formats', $search)
                ->groupEnd();
        }

        if (! empty($selectedDataTypes)) {
            $query->whereIn('datasets.data_type', $selectedDataTypes);
        }

        if (! empty($selectedCategories)) {
            $query->whereIn('datasets.category', $selectedCategories);
        }

        $dateCutoff = $this->dateCutoffFromSelected($selectedDateUploaded);
        if ($dateCutoff !== null) {
            $query->where('datasets.created_at >=', $dateCutoff);
        }

        $datasets = $query
            ->orderBy('datasets.approved_at', 'DESC')
            ->orderBy('datasets.created_at', 'DESC')
            ->paginate(10);

        $totalDatasets = $datasetModel->pager->getTotal();
        $currentPage = $datasetModel->pager->getCurrentPage();
        $perPage = $datasetModel->pager->getPerPage();
        $visibleCount = count($datasets);
        $rangeStart = $totalDatasets > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
        $rangeEnd = $totalDatasets > 0 ? min($rangeStart + $visibleCount - 1, $totalDatasets) : 0;

        $categoryQuery = model(DatasetModel::class)
            ->select('category')
            ->where('status', DatasetModel::STATUS_PUBLISHED)
            ->where('archived_at', null)
            ->where('category !=', '');
        $this->applyPublishedAccessScope($categoryQuery);
        $categories = $categoryQuery
            ->groupBy('category')
            ->orderBy('category', 'ASC')
            ->findColumn('category');

        return view('datasets/index', [
            'title' => 'Dataset Catalog',
            'datasets' => $datasets,
            'totalDatasets' => $totalDatasets,
            'paginationSummary' => [
                'start' => $rangeStart,
                'end' => $rangeEnd,
                'total' => $totalDatasets,
            ],
            'search' => $search,
            'selectedDataTypes' => $selectedDataTypes,
            'selectedCategories' => $selectedCategories,
            'selectedDateUploaded' => $selectedDateUploaded,
            'categories' => $categories ?: [],
            'dateOptions' => $this->dateFilterOptions(),
            'statusLabels' => DatasetModel::statusLabels(),
            'accessOptions' => DatasetModel::accessOptions(),
            'pager' => $datasetModel->pager,
        ]);
    }

    public function show(int $id): string
    {
        $datasetModel = new DatasetModel();
        $dataset = $datasetModel
            ->select('datasets.*, users.name AS author_name')
            ->join('users', 'users.id = datasets.contributor_id', 'left')
            ->where('datasets.id', $id)
            ->first();

        if (! is_array($dataset) || ! $this->canViewDataset($dataset)) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (($dataset['status'] ?? '') === DatasetModel::STATUS_PUBLISHED) {
            model(DatasetViewModel::class)->insert([
                'dataset_id' => $id,
                'user_id' => $this->currentUserId(),
                'viewed_at' => date('Y-m-d H:i:s'),
                'ip_address' => $this->request->getIPAddress(),
            ]);
        }

        $latestFile = model(DatasetFileModel::class)
            ->where('dataset_id', $id)
            ->orderBy('created_at', 'DESC')
            ->first();

        $recommendations = $this->findRecommendations($dataset);

        return view('datasets/show', [
            'title' => $dataset['title'],
            'datasetId' => $id,
            'dataset' => $dataset,
            'latestFile' => $latestFile,
            'recommendations' => $recommendations,
            'viewCount' => $this->countDatasetEvents(new DatasetViewModel(), $id, 'viewed_at'),
            'downloadCount' => $this->countDatasetEvents(new DatasetDownloadModel(), $id, 'downloaded_at'),
            'canEdit' => $this->isAuthenticated() && $this->canManageDataset($dataset),
            'isOwner' => $this->isAuthenticated() && $this->canManageDataset($dataset),
            'canDownload' => $this->canDownloadDataset($dataset),
            'downloadRequiresLogin' => ! $this->isAuthenticated(),
            'statusLabel' => DatasetModel::statusLabel((string) ($dataset['status'] ?? '')),
            'accessLabel' => DatasetModel::accessLabel((string) ($dataset['access_type'] ?? '')),
        ]);
    }

    public function download(int $id)
    {
        $dataset = model(DatasetModel::class)->find($id);
        if (! is_array($dataset)) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! $this->isAuthenticated()) {
            $this->recordDeniedDownload($id, 'Guest attempted to download a dataset file without an account session.');

            return redirect()
                ->to('/login?return_to=' . rawurlencode('/datasets/' . $id))
                ->with('error', 'Sign in required. Please sign in to download this dataset.');
        }

        if (! $this->canDownloadDataset($dataset)) {
            $this->recordDeniedDownload($id, 'Authenticated account failed dataset download authorization.');

            if (($dataset['access_type'] ?? '') === DatasetModel::ACCESS_PRIVATE) {
                throw PageNotFoundException::forPageNotFound();
            }

            return redirect()
                ->to('/datasets')
                ->with('error', 'That dataset is not available for download.');
        }

        $latestFile = model(DatasetFileModel::class)
            ->where('dataset_id', $id)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (! is_array($latestFile)) {
            return redirect()
                ->to('/datasets/' . $id)
                ->with('error', 'No uploaded dataset file is available for this record yet.');
        }

        $absolutePath = WRITEPATH . str_replace('/', DIRECTORY_SEPARATOR, (string) $latestFile['file_path']);
        if (! is_file($absolutePath)) {
            return redirect()
                ->to('/datasets/' . $id)
                ->with('error', 'The dataset file record exists, but the file is missing from storage.');
        }

        model(DatasetDownloadModel::class)->insert([
            'dataset_id' => $id,
            'user_id' => $this->currentUserId(),
            'downloaded_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->request->getIPAddress(),
        ]);
        $this->recordAudit('dataset_download', 'dataset', $id, 'Dataset file download served.');

        return $this->response->download($absolutePath, null)->setFileName((string) $latestFile['original_name']);
    }

    public function cover(int $id)
    {
        $dataset = model(DatasetModel::class)->find($id);
        if (! is_array($dataset) || (! $this->canViewDataset($dataset) && ! $this->hasAssignedReviewForDataset($id))) {
            throw PageNotFoundException::forPageNotFound();
        }

        $relativePath = (string) ($dataset['cover_image'] ?? '');
        if ($relativePath !== '' && ! str_starts_with($relativePath, 'uploads/dataset-covers/')) {
            throw PageNotFoundException::forPageNotFound();
        }

        $absolutePath = WRITEPATH . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if ($relativePath === '' || ! is_file($absolutePath)) {
            return redirect()->to(base_url('assets/img/placeholders/dataset-placeholder-img.png'));
        }

        $mimeType = mime_content_type($absolutePath) ?: 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setBody((string) file_get_contents($absolutePath));
    }

    public function edit(int $id): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $datasetModel = new DatasetModel();
        $dataset = $datasetModel->find($id);

        if (! is_array($dataset)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->canManageDataset($dataset)) {
            return redirect()
                ->to('/datasets/' . $id)
                ->with('error', 'You are not allowed to edit this dataset.');
        }

        if (! DatasetModel::isRevisionStatus((string) ($dataset['status'] ?? '')) && ($dataset['status'] ?? '') !== DatasetModel::STATUS_PUBLISHED) {
            return redirect()->to('/dashboard')->with('error', 'This dataset is locked while moderation is active.');
        }

        return view('datasets/edit', [
            'title' => 'Edit Dataset',
            'datasetId' => $id,
            'dataset' => $dataset,
            'dataTypes' => ['Text', 'Image', 'Audio', 'Video', 'Tabular'],
            'sourceTypes' => ['Primary', 'Secondary'],
            'accessTypes' => DatasetModel::accessOptions(),
            'statusLabel' => DatasetModel::statusLabel((string) ($dataset['status'] ?? '')),
            'accessLabel' => DatasetModel::accessLabel((string) old('access_type', $dataset['access_type'] ?? 'public')),
        ]);
    }

    public function update(int $id)
    {
        $datasetModel = new DatasetModel();
        $dataset = $datasetModel->find($id);

        if (! is_array($dataset)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->canManageDataset($dataset)) {
            return redirect()
                ->to('/datasets/' . $id)
                ->with('error', 'You are not allowed to update this dataset.');
        }

        if (! DatasetModel::isRevisionStatus((string) ($dataset['status'] ?? '')) && ($dataset['status'] ?? '') !== DatasetModel::STATUS_PUBLISHED) {
            return redirect()->to('/dashboard')->with('error', 'This dataset is locked while moderation is active.');
        }

        $rules = [
            'title' => 'required',
            'description' => 'required',
            'category' => 'required|max_length[120]',
            'tags' => 'required|max_length[255]',
            'data_type' => 'required|max_length[80]',
            'content_formats' => 'required|max_length[255]',
            'research_title' => 'required',
            'project_head' => 'required|max_length[150]',
            'source_type' => 'required|max_length[80]',
            'access_type' => 'required|in_list[public,institutional,restricted,private]',
        ];

        $uploadedFile = $this->request->getFile('dataset_file');
        if ($uploadedFile && $uploadedFile->isValid() && ! $uploadedFile->hasMoved()) {
            $rules['dataset_file'] = 'ext_in[dataset_file,zip]|max_size[dataset_file,10240]';
        }
        $coverImage = $this->request->getFile('cover_image');
        if ($coverImage && $coverImage->isValid() && ! $coverImage->hasMoved()) {
            $rules['cover_image'] = 'is_image[cover_image]|mime_in[cover_image,image/jpeg,image/png,image/webp]|max_size[cover_image,4096]|max_dims[cover_image,4000,4000]';
        }

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $latestFile = model(DatasetFileModel::class)
            ->where('dataset_id', $id)
            ->orderBy('created_at', 'DESC')
            ->first();

        $fileRecordId = is_array($latestFile) ? (int) $latestFile['id'] : null;
        if ($uploadedFile && $uploadedFile->isValid() && ! $uploadedFile->hasMoved()) {
            $fileRecordId = $this->storeDatasetFile($id, $uploadedFile, (int) $this->currentUserId());
        }

        $coverPath = (string) ($dataset['cover_image'] ?? '');
        if ($coverImage && $coverImage->isValid() && ! $coverImage->hasMoved()) {
            $coverPath = $this->replaceCoverImage($id, $coverImage, $coverPath);
        }

        $newVersion = $this->incrementVersion((string) ($dataset['version'] ?? '1.0'));
        $newStatus = match ((string) ($dataset['status'] ?? '')) {
            DatasetModel::STATUS_TECHNICAL_REVISION => DatasetModel::STATUS_PENDING_TECHNICAL,
            DatasetModel::STATUS_ETHICS_REVISION => DatasetModel::STATUS_PENDING_ETHICS,
            DatasetModel::STATUS_PUBLISHED => DatasetModel::STATUS_PENDING_TECHNICAL,
            default => (string) $dataset['status'],
        };

        $datasetModel->update($id, [
            'title' => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'cover_image' => $coverPath !== '' ? $coverPath : null,
            'category' => trim((string) $this->request->getPost('category')),
            'tags' => trim((string) $this->request->getPost('tags')),
            'data_type' => trim((string) $this->request->getPost('data_type')),
            'file_format' => 'ZIP',
            'content_formats' => trim((string) $this->request->getPost('content_formats')),
            'access_type' => trim((string) $this->request->getPost('access_type')),
            'research_title' => trim((string) $this->request->getPost('research_title')),
            'project_head' => trim((string) $this->request->getPost('project_head')),
            'members' => trim((string) $this->request->getPost('members')),
            'source_type' => trim((string) $this->request->getPost('source_type')),
            'source_link' => trim((string) $this->request->getPost('source_link')),
            'version' => $newVersion,
            'status' => $newStatus,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        model(DatasetVersionModel::class)->insert([
            'dataset_id' => $id,
            'version' => $newVersion,
            'change_summary' => trim((string) $this->request->getPost('change_summary')) ?: 'Dataset metadata updated.',
            'dataset_file_id' => $fileRecordId,
            'created_by' => (int) $this->currentUserId(),
        ]);
        $this->recordAudit('dataset_update', 'dataset', $id, 'Dataset metadata was updated.');

        if ($newStatus !== (string) ($dataset['status'] ?? '')) {
            $title = trim((string) $this->request->getPost('title'));
            $reviewStage = $newStatus === DatasetModel::STATUS_PENDING_ETHICS
                ? ReviewModel::STAGE_ETHICS
                : ReviewModel::STAGE_TECHNICAL;
            $assignment = (new ModerationWorkflow())->autoAssign(
                $id,
                $reviewStage,
                (int) $this->currentUserId(),
                $this->request->getIPAddress()
            );
            if ($assignment !== null) {
                $admins = \Config\Database::connect()->table('users')
                    ->select('users.id')
                    ->join('user_roles', 'user_roles.user_id = users.id')
                    ->join('roles', 'roles.id = user_roles.role_id')
                    ->where('roles.name', 'repository_administrator')
                    ->where('users.status', 'active')
                    ->get()
                    ->getResultArray();
                foreach ($admins as $admin) {
                    model(NotificationModel::class)->insert([
                        'user_id' => (int) $admin['id'],
                        'type' => 'workflow_attention',
                        'title' => 'Dataset updated',
                        'message' => 'Dataset "' . $title . '" was updated and automatically assigned to ' . $assignment['reviewer_name'] . ' for ' . $reviewStage . ' review.',
                        'link' => '/admin/datasets/' . $id,
                    ]);
                }
            }
        }

        return redirect()
            ->to('/datasets/' . $id . '/edit')
            ->with('info', 'Dataset changes have been saved.');
    }

    public function archive(int $id)
    {
        $dataset = model(DatasetModel::class)->find($id);
        if (! is_array($dataset)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->canManageDataset($dataset)) {
            return redirect()
                ->to('/datasets/' . $id)
                ->with('error', 'You are not allowed to archive this dataset.');
        }

        if (DatasetModel::isUnderReview((string) ($dataset['status'] ?? ''))) {
            return redirect()->to('/dashboard')->with('error', 'A dataset cannot be archived while moderation is active.');
        }

        model(DatasetModel::class)->update($id, [
            'archived_from_status' => (string) ($dataset['status'] ?? DatasetModel::STATUS_PENDING_TECHNICAL),
            'status' => DatasetModel::STATUS_ARCHIVED,
            'archived_at' => date('Y-m-d H:i:s'),
        ]);
        $this->recordAudit('dataset_archive', 'dataset', $id, 'Dataset archived from normal browsing.');

        return redirect()
            ->to('/dashboard')
            ->with('info', 'Dataset archived successfully.');
    }

    private function storeDatasetFile(int $datasetId, object $uploadedFile, int $userId): int
    {
        $targetDir = WRITEPATH . 'uploads/datasets/' . $datasetId;
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $storedName = $uploadedFile->getRandomName();
        $uploadedFile->move($targetDir, $storedName);
        $relativePath = 'uploads/datasets/' . $datasetId . '/' . $storedName;

        return (int) model(DatasetFileModel::class)->insert([
            'dataset_id' => $datasetId,
            'stored_name' => $storedName,
            'original_name' => $uploadedFile->getClientName(),
            'file_path' => $relativePath,
            'file_size' => (int) $uploadedFile->getSize(),
            'file_type' => (string) $uploadedFile->getClientMimeType(),
            'uploaded_by' => $userId,
        ], true);
    }

    private function replaceCoverImage(int $datasetId, object $uploadedFile, string $currentPath): string
    {
        $targetDir = WRITEPATH . 'uploads/dataset-covers/' . $datasetId;
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $storedName = $uploadedFile->getRandomName();
        $uploadedFile->move($targetDir, $storedName);
        $newPath = 'uploads/dataset-covers/' . $datasetId . '/' . $storedName;

        if (str_starts_with($currentPath, 'uploads/dataset-covers/')) {
            $oldAbsolutePath = WRITEPATH . str_replace('/', DIRECTORY_SEPARATOR, $currentPath);
            if (is_file($oldAbsolutePath)) {
                unlink($oldAbsolutePath);
            }
        }

        return $newPath;
    }

    private function incrementVersion(string $currentVersion): string
    {
        if (preg_match('/^(\d+)\.(\d+)$/', $currentVersion, $matches) === 1) {
            return $matches[1] . '.' . ((int) $matches[2] + 1);
        }

        return '1.1';
    }

    private function countDatasetEvents(object $model, int $datasetId, string $dateField): int
    {
        return $model->where('dataset_id', $datasetId)->countAllResults();
    }

    /**
     * @param array<string, mixed> $dataset
     * @return array<int, array<string, mixed>>
     */
    private function findRecommendations(array $dataset): array
    {
        $candidates = model(DatasetModel::class)
            ->select('datasets.*, users.name AS author_name')
            ->join('users', 'users.id = datasets.contributor_id', 'left')
            ->where('datasets.status', DatasetModel::STATUS_PUBLISHED)
            ->where('datasets.archived_at', null)
            ->where('datasets.id !=', (int) $dataset['id']);
        $this->applyPublishedAccessScope($candidates);

        $candidates = $candidates->findAll();

        $scored = [];
        foreach ($candidates as $candidate) {
            $score = metadata_similarity_score($dataset, $candidate);
            if ($score > 0) {
                $candidate['score'] = $score;
                $scored[] = $candidate;
            }
        }

        usort($scored, static fn (array $a, array $b): int => (int) $b['score'] <=> (int) $a['score']);

        return array_slice($scored, 0, 3);
    }

    /**
     * @param array<string, mixed> $dataset
     */
    private function canViewDataset(array $dataset): bool
    {
        if ($this->hasRole('repository_administrator')) {
            return true;
        }
        if ($this->isAuthenticated() && $this->canManageDataset($dataset)) {
            return true;
        }

        return (($dataset['status'] ?? '') === DatasetModel::STATUS_PUBLISHED)
            && (($dataset['archived_at'] ?? null) === null)
            && $this->canAccessPublishedDataset($dataset);
    }

    private function hasAssignedReviewForDataset(int $datasetId): bool
    {
        if (! $this->isAuthenticated()) {
            return false;
        }

        return model(\App\Models\ReviewModel::class)
            ->where([
                'dataset_id' => $datasetId,
                'reviewer_id' => (int) $this->currentUserId(),
                'status' => \App\Models\ReviewModel::STATUS_ASSIGNED,
            ])
            ->countAllResults() > 0;
    }

    /**
     * @param array<string, mixed> $dataset
     */
    private function canAccessPublishedDataset(array $dataset): bool
    {
        $accessType = (string) ($dataset['access_type'] ?? DatasetModel::ACCESS_PUBLIC);

        if ($accessType === DatasetModel::ACCESS_PUBLIC) {
            return true;
        }

        if ($accessType === DatasetModel::ACCESS_PRIVATE) {
            return false;
        }

        return $this->isAuthenticated();
    }

    /**
     * @param array<string, mixed> $dataset
     */
    private function canDownloadDataset(array $dataset): bool
    {
        if (! $this->isAuthenticated()) {
            return false;
        }

        if ($this->hasRole('repository_administrator') || $this->canManageDataset($dataset)) {
            return true;
        }

        $isPublished = (($dataset['status'] ?? '') === DatasetModel::STATUS_PUBLISHED)
            && (($dataset['archived_at'] ?? null) === null);
        if (! $isPublished) {
            return false;
        }

        return in_array((string) ($dataset['access_type'] ?? DatasetModel::ACCESS_PUBLIC), [
            DatasetModel::ACCESS_PUBLIC,
            DatasetModel::ACCESS_INSTITUTIONAL,
            DatasetModel::ACCESS_RESTRICTED,
        ], true);
    }

    private function recordDeniedDownload(int $datasetId, string $details): void
    {
        $this->recordAudit('dataset_download_denied', 'dataset', $datasetId, $details);
    }

    private function applyPublishedAccessScope(object $query): void
    {
        if (! $this->isAuthenticated()) {
            $query->where('datasets.access_type', DatasetModel::ACCESS_PUBLIC);

            return;
        }

        $query->groupStart()
            ->where('datasets.access_type', DatasetModel::ACCESS_PUBLIC)
            ->orWhere('datasets.access_type', DatasetModel::ACCESS_INSTITUTIONAL)
            ->orWhere('datasets.access_type', DatasetModel::ACCESS_RESTRICTED)
            ->groupEnd();
    }

    /**
     * @param string[] $selectedDates
     */
    private function dateCutoffFromSelected(array $selectedDates): ?string
    {
        if (empty($selectedDates)) {
            return null;
        }

        // Use the earliest (most restrictive) date cutoff when multiple are selected
        $cutoffs = [];
        foreach ($selectedDates as $date) {
            $cutoff = match ($date) {
                'today' => date('Y-m-d 00:00:00'),
                'week' => date('Y-m-d 00:00:00', strtotime('-7 days')),
                'month' => date('Y-m-d 00:00:00', strtotime('-1 month')),
                'year' => date('Y-m-d 00:00:00', strtotime('-1 year')),
                default => null,
            };
            if ($cutoff !== null) {
                $cutoffs[] = $cutoff;
            }
        }

        return empty($cutoffs) ? null : min($cutoffs);
    }

    /**
     * @return array<string, string>
     */
    private function dateFilterOptions(): array
    {
        return [
            'today' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }
}
