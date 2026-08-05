<?php

namespace App\Controllers;

use App\Models\DatasetFileModel;
use App\Models\DatasetModel;
use App\Models\DatasetVersionModel;
use App\Models\NotificationModel;
use App\Models\ReviewModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Dashboard extends BaseController
{
    public function index(): string
    {
        return view('dashboard/index', $this->dashboardData('My Datasets'));
    }

    public function portal(): string
    {
        return view('dashboard/portal', $this->dashboardData('Portal Contributor Records'));
    }

    public function portalDataset(int $datasetId): string
    {
        $dataset = model(DatasetModel::class)
            ->where('id', $datasetId)
            ->where('contributor_id', (int) $this->currentUserId())
            ->first();

        if (! is_array($dataset)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('dashboard/portal_dataset', [
            'title' => $dataset['title'],
            'dataset' => $dataset,
            'statusLabel' => DatasetModel::statusLabel((string) ($dataset['status'] ?? '')),
            'accessLabel' => DatasetModel::accessLabel((string) ($dataset['access_type'] ?? '')),
            'latestFile' => model(DatasetFileModel::class)->where('dataset_id', $datasetId)->orderBy('created_at', 'DESC')->first(),
            'versions' => model(DatasetVersionModel::class)->where('dataset_id', $datasetId)->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function readNotifications()
    {
        model(NotificationModel::class)->where('user_id', (int) $this->currentUserId())->where('read_at', null)->set(['read_at' => date('Y-m-d H:i:s')])->update();
        $returnTo = trim((string) $this->request->getPost('return_to'));
        if (! str_starts_with($returnTo, '/') || str_starts_with($returnTo, '//')) {
            $returnTo = '/dashboard';
        }

        return redirect()->to($returnTo)->with('info', 'Notifications marked as read.');
    }

    public function readPortalNotifications()
    {
        model(NotificationModel::class)->where('user_id', (int) $this->currentUserId())->where('read_at', null)->set(['read_at' => date('Y-m-d H:i:s')])->update();

        return redirect()->back()->with('info', 'Notifications marked as read.');
    }

    public function pollNotifications()
    {
        $userId = (int) $this->currentUserId();
        $notificationModel = model(NotificationModel::class);
        $latest = $notificationModel
            ->where('user_id', $userId)
            ->where('read_at', null)
            ->orderBy('id', 'DESC')
            ->first();

        return $this->response->setJSON([
            'unreadCount' => $notificationModel->where('user_id', $userId)->where('read_at', null)->countAllResults(),
            'latest' => is_array($latest) ? [
                'id' => (int) $latest['id'],
                'title' => (string) ($latest['title'] ?? 'New activity'),
                'message' => (string) ($latest['message'] ?? ''),
                'link' => ! empty($latest['link']) ? site_url(ltrim((string) $latest['link'], '/')) : null,
            ] : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function dashboardData(string $title): array
    {
        $datasetModel = new DatasetModel();
        $userId = (int) $this->currentUserId();
        $selectedView = trim((string) ($this->request->getGet('view') ?? 'all'));
        $selectedAccess = trim((string) ($this->request->getGet('access') ?? ''));
        $search = trim((string) ($this->request->getGet('q') ?? ''));
        $validViews = ['all', 'technical', 'ethics', 'publication', 'revision', 'published', 'closed'];

        if (! in_array($selectedView, $validViews, true)) {
            $selectedView = 'all';
        }

        if (! array_key_exists($selectedAccess, DatasetModel::accessOptions())) {
            $selectedAccess = '';
        }

        $query = $datasetModel->where('contributor_id', $userId);
        $this->applyDashboardView($query, $selectedView);

        if ($selectedAccess !== '') {
            $query->where('access_type', $selectedAccess);
        }

        if ($search !== '') {
            $query->groupStart()
                ->like('title', $search)
                ->orLike('description', $search)
                ->orLike('category', $search)
                ->orLike('tags', $search)
                ->groupEnd();
        }

        $datasets = $query
            ->orderBy('updated_at', 'DESC')
            ->paginate(12, 'dashboard');
        $latestReviews = $this->latestReviewsByDataset($datasets);
        $statusCounts = $this->dashboardCounts($userId);

        return [
            'title' => $title,
            'myDatasets' => $this->decorateDatasets($datasets, $latestReviews),
            'statusLabels' => DatasetModel::statusLabels(),
            'accessOptions' => DatasetModel::accessOptions(),
            'roles' => $this->currentRoles(),
            'statusCounts' => $statusCounts,
            'publishedAccessCounts' => $this->publishedAccessCounts($userId),
            'selectedView' => $selectedView,
            'selectedAccess' => $selectedAccess,
            'search' => $search,
            'totalDatasets' => $statusCounts['all'],
            'pager' => $datasetModel->pager,
        ];
    }

    private function applyDashboardView(DatasetModel $query, string $view): void
    {
        match ($view) {
            'technical' => $query->whereIn('status', [DatasetModel::STATUS_PENDING_TECHNICAL, DatasetModel::STATUS_TECHNICAL_REVISION]),
            'ethics' => $query->whereIn('status', [DatasetModel::STATUS_PENDING_ETHICS, DatasetModel::STATUS_ETHICS_REVISION]),
            'publication' => $query->where('status', DatasetModel::STATUS_AWAITING_PUBLICATION),
            'revision' => $query->whereIn('status', [DatasetModel::STATUS_TECHNICAL_REVISION, DatasetModel::STATUS_ETHICS_REVISION]),
            'published' => $query->where('status', DatasetModel::STATUS_PUBLISHED)->where('archived_at', null),
            'closed' => $query->whereIn('status', [DatasetModel::STATUS_REJECTED, DatasetModel::STATUS_ARCHIVED]),
            default => null,
        };
    }

    /**
     * @return array<string, int>
     */
    private function dashboardCounts(int $userId): array
    {
        $count = static function (array $statuses) use ($userId): int {
            return model(DatasetModel::class)
                ->where('contributor_id', $userId)
                ->whereIn('status', $statuses)
                ->countAllResults();
        };

        $pendingTechnical = $count([DatasetModel::STATUS_PENDING_TECHNICAL]);
        $pendingEthics = $count([DatasetModel::STATUS_PENDING_ETHICS]);
        $technicalRevisions = $count([DatasetModel::STATUS_TECHNICAL_REVISION]);
        $ethicsRevisions = $count([DatasetModel::STATUS_ETHICS_REVISION]);
        $publication = $count([DatasetModel::STATUS_AWAITING_PUBLICATION]);
        $revisions = $technicalRevisions + $ethicsRevisions;
        $published = model(DatasetModel::class)
            ->where('contributor_id', $userId)
            ->where('status', DatasetModel::STATUS_PUBLISHED)
            ->where('archived_at', null)
            ->countAllResults();
        $closed = $count([DatasetModel::STATUS_REJECTED, DatasetModel::STATUS_ARCHIVED]);

        return [
            'all' => model(DatasetModel::class)->where('contributor_id', $userId)->countAllResults(),
            'technical' => $pendingTechnical + $technicalRevisions,
            'ethics' => $pendingEthics + $ethicsRevisions,
            'pendingTechnical' => $pendingTechnical,
            'pendingEthics' => $pendingEthics,
            'publication' => $publication,
            'revision' => $revisions,
            'published' => $published,
            'closed' => $closed,
            // Preserve the existing portal summary contract.
            'pending' => $pendingTechnical + $pendingEthics + $publication,
            'needsRevision' => $revisions,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function publishedAccessCounts(int $userId): array
    {
        $counts = array_fill_keys(array_keys(DatasetModel::accessOptions()), 0);
        $rows = model(DatasetModel::class)
            ->select('access_type, COUNT(*) AS total')
            ->where('contributor_id', $userId)
            ->where('status', DatasetModel::STATUS_PUBLISHED)
            ->where('archived_at', null)
            ->groupBy('access_type')
            ->findAll();

        foreach ($rows as $row) {
            $accessType = (string) ($row['access_type'] ?? '');
            if (array_key_exists($accessType, $counts)) {
                $counts[$accessType] = (int) ($row['total'] ?? 0);
            }
        }

        return $counts;
    }

    /**
     * @param list<array<string, mixed>> $datasets
     * @return array<int, array<string, mixed>>
     */
    private function latestReviewsByDataset(array $datasets): array
    {
        $datasetIds = array_values(array_filter(array_map(static fn (array $dataset): int => (int) ($dataset['id'] ?? 0), $datasets)));
        if ($datasetIds === []) {
            return [];
        }

        $reviews = model(ReviewModel::class)
            ->whereIn('dataset_id', $datasetIds)
            ->where('comments !=', '')
            ->orderBy('id', 'DESC')
            ->findAll();
        $latestReviews = [];

        foreach ($reviews as $review) {
            $datasetId = (int) ($review['dataset_id'] ?? 0);
            if ($datasetId > 0 && ! isset($latestReviews[$datasetId])) {
                $latestReviews[$datasetId] = $review;
            }
        }

        return $latestReviews;
    }

    /**
     * @param list<array<string, mixed>> $datasets
     * @param array<int, array<string, mixed>> $latestReviews
     * @return list<array<string, mixed>>
     */
    private function decorateDatasets(array $datasets, array $latestReviews): array
    {
        $ownerName = (string) ($this->session->get('user_name') ?: 'You');

        foreach ($datasets as &$dataset) {
            $datasetId = (int) ($dataset['id'] ?? 0);
            $status = (string) ($dataset['status'] ?? '');
            $accessType = (string) ($dataset['access_type'] ?? DatasetModel::ACCESS_PUBLIC);

            $dataset['statusLabel'] = DatasetModel::statusLabel($status);
            $dataset['accessLabel'] = DatasetModel::accessLabel($accessType);
            $dataset['ownershipLabel'] = $ownerName . ' owns this submission';
            $dataset['latestReview'] = $latestReviews[$datasetId] ?? null;
            $dataset['nextAction'] = DatasetModel::dashboardActionForStatus($status, $datasetId);
            $dataset['workflow'] = DatasetModel::dashboardWorkflowForStatus($status, $accessType);
            $dataset['canEdit'] = DatasetModel::isRevisionStatus($status) || $status === DatasetModel::STATUS_PUBLISHED;
            $dataset['canArchive'] = ! DatasetModel::isUnderReview($status)
                && ! in_array($status, [DatasetModel::STATUS_ARCHIVED, DatasetModel::STATUS_REJECTED], true);
        }
        unset($dataset);

        return $datasets;
    }
}
